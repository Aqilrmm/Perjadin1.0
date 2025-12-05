<?php

namespace App\Controllers\KepalaDinas;

use App\Controllers\BaseController;

// ========================================
// APPROVAL SPPD CONTROLLER
// ========================================

class ApprovalSPPDController extends BaseController
{
    protected $sppdModel;

    public function __construct()
    {
        $this->sppdModel = new \App\Models\SPPD\SPPDModel();
    }

    public function index()
    {
        return view('kepaladinas/approval/sppd', ['title' => 'Persetujuan SPPD']);
    }

    public function datatable()
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $request = $this->request->getPost();
        $data = $this->sppdModel->getDatatablesData($request);

        foreach ($data['data'] as $key => $row) {
            $data['data'][$key]->status_badge = get_sppd_status_badge($row->status);
            $data['data'][$key]->estimasi_formatted = format_rupiah($row->estimasi_biaya);
            $data['data'][$key]->action = $this->getActionButtons($row->id, $row->status);
        }

        return $this->respond($data);
    }

    public function detail($id)
    {
        $sppd = $this->sppdModel->getWithRelations($id);
        if (!$sppd) {
            return redirect()->to('/kepaladinas/sppd/approval')->with('error', 'SPPD tidak ditemukan');
        }

        $sppdPegawaiModel = new \App\Models\SPPD\SPPDPegawaiModel();
        $pegawaiList = $sppdPegawaiModel->getPegawaiBySppdId($id);

        return view('kepaladinas/approval/sppd_detail', [
            'title' => 'Detail SPPD',
            'sppd' => $sppd,
            'pegawai_list' => $pegawaiList,
        ]);
    }

    public function preview($id)
    {
        // Generate PDF preview
        $sppd = $this->sppdModel->getWithRelations($id);
        if (!$sppd) {
            return $this->respondError('SPPD tidak ditemukan', null, 404);
        }

        // TODO: Generate PDF using mPDF
        return $this->respondSuccess('Preview generated', ['url' => '/preview/sppd/'.$id]);
    }

    public function approve($id)
    {
        $sppd = $this->sppdModel->find($id);
        if (!$sppd || $sppd['status'] != 'pending') {
            return $this->respondError('Hanya SPPD pending yang dapat disetujui', null, 400);
        }

        // Generate No SPPD if not exists
        $noSppd = $sppd['no_sppd'];
        if (!$noSppd) {
            // Auto-generate nomor SPPD
            $bidangModel = new \App\Models\Bidang\BidangModel();
            $bidang = $bidangModel->find($sppd['bidang_id']);
            $bulan = bulan_romawi(date('n'));
            $tahun = date('Y');
            
            // Get last number
            $lastSppd = $this->sppdModel->where('bidang_id', $sppd['bidang_id'])
                                        ->where('YEAR(created_at)', $tahun)
                                        ->orderBy('id', 'DESC')
                                        ->first();
            
            $urut = 1;
            if ($lastSppd && $lastSppd['no_sppd']) {
                $parts = explode('/', $lastSppd['no_sppd']);
                $urut = intval(end($parts)) + 1;
            }
            
            $noSppd = sprintf('SPPD/%s/%s/%s/%03d', $bidang['kode_bidang'], $bulan, $tahun, $urut);
        }

        $catatan = $this->request->getPost('catatan');

        if ($this->sppdModel->approveSPPD($id, user_id(), $noSppd, $catatan)) {
            $this->logActivity('APPROVE_SPPD', "Approved SPPD: {$noSppd}");
            
            // TODO: Generate Nota Dinas PDF
            
            // Send notification
            notify_sppd_approved($id);
            
            return $this->respondSuccess('SPPD berhasil disetujui', ['no_sppd' => $noSppd]);
        }

        return $this->respondError('Gagal menyetujui SPPD', null, 500);
    }

    public function reject($id)
    {
        $sppd = $this->sppdModel->find($id);
        if (!$sppd || $sppd['status'] != 'pending') {
            return $this->respondError('Hanya SPPD pending yang dapat ditolak', null, 400);
        }

        $rules = ['catatan' => 'required|min_length[10]'];
        $errors = $this->validate($rules);
        if ($errors !== true) {
            return $this->respondError('Catatan penolakan wajib diisi minimal 10 karakter', $errors, 422);
        }

        $catatan = $this->request->getPost('catatan');
        if ($this->sppdModel->rejectSPPD($id, $catatan)) {
            $this->logActivity('REJECT_SPPD', "Rejected SPPD ID: {$id}");
            return $this->respondSuccess('SPPD berhasil ditolak');
        }

        return $this->respondError('Gagal menolak SPPD', null, 500);
    }

    private function getActionButtons($id, $status)
    {
        $buttons = '<div class="flex gap-2">';
        $buttons .= '<button class="btn-detail text-blue-600" data-id="'.$id.'"><i class="fas fa-eye"></i></button>';
        $buttons .= '<button class="btn-preview text-purple-600" data-id="'.$id.'"><i class="fas fa-file-pdf"></i></button>';
        if ($status == 'pending') {
            $buttons .= '<button class="btn-approve text-green-600" data-id="'.$id.'"><i class="fas fa-check-circle"></i></button>';
            $buttons .= '<button class="btn-reject text-red-600" data-id="'.$id.'"><i class="fas fa-times-circle"></i></button>';
        }
        $buttons .= '</div>';
        return $buttons;
    }
}