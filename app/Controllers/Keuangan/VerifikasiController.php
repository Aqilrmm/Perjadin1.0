<?php

namespace App\Controllers\Keuangan;

use App\Controllers\BaseController;
use App\Models\SPPD\SPPDModel;
use App\Models\SPPD\LPPDModel;
use App\Models\SPPD\KwitansiModel;

class VerifikasiController extends BaseController
{
    protected $sppdModel;
    protected $lppdModel;
    protected $kwitansiModel;

    public function __construct()
    {
        $this->sppdModel = new SPPDModel();
        $this->lppdModel = new LPPDModel();
        $this->kwitansiModel = new KwitansiModel();
    }

    /**
     * Display verifikasi list
     */
    public function index()
    {
        $data = [
            'title' => 'Verifikasi SPPD',
        ];

        return view('keuangan/verifikasi/index', $data);
    }

    /**
     * Get SPPD data for verification (DataTables)
     */
    public function datatable()
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $request = $this->request->getPost();

        // Filter by submitted status
        if (!isset($request['filters']['status'])) {
            $request['filters']['status'] = 'submitted';
        }

        $data = $this->sppdModel->getDatatablesData($request);

        // Format data for display
        foreach ($data['data'] as $key => $row) {
            $data['data'][$key]->status_badge = get_sppd_status_badge($row->status);
            $data['data'][$key]->estimasi_biaya_formatted = format_rupiah($row->estimasi_biaya);
            $data['data'][$key]->submitted_at_formatted = format_tanggal_waktu($row->submitted_at);
            $data['data'][$key]->action = $this->getActionButtons($row->id, $row->status);
        }

        return $this->respond($data);
    }

    /**
     * Display detail verification page
     */
    public function detail($id)
    {
        $sppd = $this->sppdModel->getWithRelations($id);

        if (!$sppd) {
            return redirect()->to('/keuangan/verifikasi')->with('error', 'SPPD tidak ditemukan');
        }

        // Get LPPD
        $lppd = $this->lppdModel->getBySppd($id);

        // Get Kwitansi
        $kwitansi = $this->kwitansiModel->getBySppd($id);

        // Get pegawai list
        $sppdPegawaiModel = new \App\Models\SPPD\SPPDPegawaiModel();
        $pegawaiList = $sppdPegawaiModel->getPegawaiBySppdId($id);

        $data = [
            'title' => 'Verifikasi SPPD',
            'sppd' => $sppd,
            'lppd' => $lppd,
            'kwitansi' => $kwitansi,
            'pegawai_list' => $pegawaiList,
        ];

        return view('keuangan/verifikasi/detail', $data);
    }

    /**
     * Approve SPPD (final verification)
     */
    public function approve($id)
    {
        $sppd = $this->sppdModel->find($id);

        if (!$sppd) {
            return $this->respondError('SPPD tidak ditemukan', null, 404);
        }

        // Only submitted SPPD can be verified
        if ($sppd['status'] != 'submitted') {
            return $this->respondError('Hanya SPPD dengan status submitted yang dapat diverifikasi', null, 400);
        }

        // Validate checklist
        $checklist = $this->request->getPost('checklist');

        if (!$checklist) {
            return $this->respondError('Checklist verifikasi harus diisi', null, 422);
        }

        // Check if all required items are checked
        $requiredChecks = [
            'lppd_lengkap',
            'kwitansi_lengkap',
            'bukti_valid',
            'jumlah_sesuai',
        ];

        foreach ($requiredChecks as $check) {
            if (!isset($checklist[$check]) || !$checklist[$check]) {
                return $this->respondError("Item '{$check}' harus dicentang untuk verifikasi", null, 422);
            }
        }

        $catatan = $this->request->getPost('catatan_verifikasi');

        if ($this->sppdModel->verifySPPD($id, user_id(), $catatan)) {
            $this->logActivity('VERIFY_SPPD', "Verified SPPD: {$sppd['no_sppd']}");

            // Send notification to pegawai
            $sppdPegawaiModel = new \App\Models\SPPD\SPPDPegawaiModel();
            $pegawaiIds = $sppdPegawaiModel->getPegawaiIds($id);

            foreach ($pegawaiIds as $pegawaiId) {
                notify_sppd_verified($id, $pegawaiId);
            }

            return $this->respondSuccess('SPPD berhasil diverifikasi');
        }

        return $this->respondError('Gagal memverifikasi SPPD', null, 500);
    }

    /**
     * Reject/Return SPPD for revision
     */
    public function reject($id)
    {
        $sppd = $this->sppdModel->find($id);

        if (!$sppd) {
            return $this->respondError('SPPD tidak ditemukan', null, 404);
        }

        if ($sppd['status'] != 'submitted') {
            return $this->respondError('Hanya SPPD dengan status submitted yang dapat ditolak', null, 400);
        }

        // Use central catatan rule but enforce min_length[20] for verification rejections
        $group = config('Validation')->rules['catatan'] ?? ['catatan' => 'required|min_length[10]'];
        $rule = $group['catatan'];
        // ensure min_length[20]
        if (preg_match('/min_length\[\d+\]/', $rule)) {
            $rule = preg_replace('/min_length\[\d+\]/', 'min_length[20]', $rule);
        } else {
            $rule .= '|min_length[20]';
        }

        $rules = ['catatan_penolakan' => $rule];

        $valid = $this->validate($rules);
        if ($valid !== true) {
            return $this->respondError('Catatan penolakan wajib diisi minimal 20 karakter', $this->getValidationErrors(), 422);
        }

        $catatan = $this->request->getPost('catatan_penolakan');

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Return SPPD for revision
            $this->sppdModel->returnForRevision($id, $catatan);

            // Reset LPPD & Kwitansi submitted status
            $lppd = $this->lppdModel->getBySppd($id);
            if ($lppd) {
                $this->lppdModel->update($lppd['id'], ['is_submitted' => 0]);
            }

            $kwitansi = $this->kwitansiModel->getBySppd($id);
            if ($kwitansi) {
                $this->kwitansiModel->update($kwitansi['id'], ['is_submitted' => 0]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->respondError('Gagal menolak SPPD', null, 500);
            }

            $this->logActivity('REJECT_SPPD', "Returned SPPD for revision: {$sppd['no_sppd']}");

            // Send notification to pegawai
            $sppdPegawaiModel = new \App\Models\SPPD\SPPDPegawaiModel();
            $pegawaiIds = $sppdPegawaiModel->getPegawaiIds($id);

            foreach ($pegawaiIds as $pegawaiId) {
                notify_sppd_need_revision($id, $pegawaiId, $catatan);
            }

            return $this->respondSuccess('SPPD dikembalikan untuk revisi');
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->respondError($e->getMessage(), null, 500);
        }
    }

    /**
     * Get pending verification count
     */
    public function getPendingCount()
    {
        $count = $this->sppdModel->where('status', 'submitted')
            ->where('deleted_at', null)
            ->countAllResults();

        return $this->respondSuccess('Pending count', ['count' => $count]);
    }

    /**
     * Get action buttons for DataTable
     */
    private function getActionButtons($sppdId, $status)
    {
        $buttons = '<div class="flex gap-2">';

        if ($status == 'submitted') {
            $buttons .= '<button class="btn-verify text-blue-600 hover:text-blue-800" data-id="' . $sppdId . '" title="Verifikasi">
                            <i class="fas fa-check-circle"></i>
                        </button>';
            $buttons .= '<button class="btn-reject text-red-600 hover:text-red-800" data-id="' . $sppdId . '" title="Return">
                            <i class="fas fa-undo"></i>
                        </button>';
        }

        $buttons .= '<button class="btn-detail text-purple-600 hover:text-purple-800" data-id="' . $sppdId . '" title="Detail">
                        <i class="fas fa-eye"></i>
                    </button>';

        $buttons .= '</div>';

        return $buttons;
    }
}
