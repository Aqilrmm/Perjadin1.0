<?php

namespace App\Controllers\KepalaBidang;

use App\Controllers\BaseController;
use App\Models\SPPD\SPPDModel;
use App\Models\SPPD\SPPDPegawaiModel;
use App\Models\Program\ProgramModel;
use App\Models\Program\KegiatanModel;
use App\Models\Program\SubKegiatanModel;
use App\Models\User\UserModel;

class SPPDController extends BaseController
{
    protected $sppdModel;
    protected $sppdPegawaiModel;
    protected $programModel;
    protected $kegiatanModel;
    protected $subKegiatanModel;
    protected $userModel;

    public function __construct()
    {
        $this->sppdModel = new SPPDModel();
        $this->sppdPegawaiModel = new SPPDPegawaiModel();
        $this->programModel = new ProgramModel();
        $this->kegiatanModel = new KegiatanModel();
        $this->subKegiatanModel = new SubKegiatanModel();
        $this->userModel = new UserModel();
    }

    /**
     * Display SPPD list
     */
    public function index()
    {
        $data = [
            'title' => 'Kelola SPPD',
        ];

        return view('kepalabidang/sppd/index', $data);
    }

    /**
     * Display create SPPD wizard
     */
    public function create()
    {
        $data = [
            'title' => 'Buat SPPD Baru',
            'approved_programs' => $this->programModel->getApprovedOptions(user_bidang_id()),
        ];

        return view('kepalabidang/sppd/create', $data);
    }

    /**
     * Get SPPD data for DataTables (AJAX)
     */
    public function datatable()
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $request = $this->request->getPost();

        // Filter by current user's bidang and created_by
        $request['filters']['bidang_id'] = user_bidang_id();
        $request['filters']['created_by'] = user_id();

        $data = $this->sppdModel->getDatatablesData($request);

        // Format data for display
        foreach ($data['data'] as $key => $row) {
            $data['data'][$key]->status_badge = get_sppd_status_badge($row->status);
            $data['data'][$key]->tipe_badge = get_tipe_perjalanan_badge($row->tipe_perjalanan);
            $data['data'][$key]->estimasi_biaya_formatted = format_rupiah($row->estimasi_biaya);
            $data['data'][$key]->tanggal_formatted = format_tanggal($row->tanggal_berangkat, false);
            $data['data'][$key]->action = $this->getActionButtons($row->id, $row->status);
        }

        return $this->respond($data);
    }

    /**
     * Validate Step 1: Select Program/Kegiatan/Sub Kegiatan
     */
    public function validateStep1()
    {
        $group = config('Validation')->rules['sppd_basic'];
        $rules = array_intersect_key($group, array_flip(['program_id', 'kegiatan_id', 'sub_kegiatan_id']));

        $valid = $this->validate($rules);
        if ($valid !== true) {
            return $this->respondError('Validasi gagal', $this->getValidationErrors(), 422);
        }

        $subKegiatanId = $this->request->getPost('sub_kegiatan_id');
        $subKegiatan = $this->subKegiatanModel->getWithRelations($subKegiatanId);

        if (!$subKegiatan) {
            return $this->respondError('Sub kegiatan tidak ditemukan', null, 404);
        }

        // Check if approved
        if ($subKegiatan['status'] != 'approved') {
            return $this->respondError('Sub kegiatan belum disetujui', null, 400);
        }

        // Get sisa anggaran
        $sisaAnggaran = $this->subKegiatanModel->getSisaAnggaran($subKegiatanId);

        return $this->respondSuccess('Validation passed', [
            'sub_kegiatan' => $subKegiatan,
            'sisa_anggaran' => $sisaAnggaran,
        ]);
    }

    /**
     * Validate Step 2: Detail Perjalanan
     */
    public function validateStep2()
    {
        $group = config('Validation')->rules['sppd_basic'];
        $rules = array_intersect_key($group, array_flip([
            'tipe_perjalanan',
            'maksud_perjalanan',
            'dasar_surat',
            'alat_angkut',
            'tempat_berangkat',
            'tempat_tujuan',
            'tanggal_berangkat',
            'lama_perjalanan'
        ]));

        $valid = $this->validate($rules);
        if ($valid !== true) {
            return $this->respondError('Validasi gagal', $this->getValidationErrors(), 422);
        }

        // Validate tanggal berangkat (min H+1)
        $tanggalBerangkat = $this->request->getPost('tanggal_berangkat');
        if (strtotime($tanggalBerangkat) <= strtotime(date('Y-m-d'))) {
            return $this->respondError('Tanggal berangkat minimal H+1 dari hari ini', null, 422);
        }

        // Calculate tanggal kembali
        $lamaPerjalanan = $this->request->getPost('lama_perjalanan');
        $tanggalKembali = date('Y-m-d', strtotime($tanggalBerangkat . ' +' . ($lamaPerjalanan - 1) . ' days'));

        return $this->respondSuccess('Validation passed', [
            'tanggal_kembali' => $tanggalKembali,
        ]);
    }

    /**
     * Validate Step 3: Select Pegawai
     */
    public function validateStep3()
    {
        $group = config('Validation')->rules['sppd_basic'];
        $rules = array_intersect_key($group, array_flip(['pegawai_ids']));

        $valid = $this->validate($rules);
        if ($valid !== true) {
            return $this->respondError('Minimal pilih 1 pegawai', $this->getValidationErrors(), 422);
        }

        $pegawaiIds = $this->request->getPost('pegawai_ids');
        $tanggalBerangkat = $this->request->getPost('tanggal_berangkat');
        $tanggalKembali = $this->request->getPost('tanggal_kembali');

        // Check for overlapping SPPD
        $warnings = [];
        foreach ($pegawaiIds as $pegawaiId) {
            $overlaps = $this->sppdModel->checkPegawaiOverlap($pegawaiId, $tanggalBerangkat, $tanggalKembali);

            if (!empty($overlaps)) {
                $pegawai = $this->userModel->find($pegawaiId);
                $warnings[] = [
                    'pegawai_id' => $pegawaiId,
                    'pegawai_nama' => $pegawai['nama'],
                    'message' => 'Pegawai ini memiliki SPPD yang overlap pada tanggal tersebut',
                    'overlaps' => $overlaps,
                ];
            }
        }

        return $this->respondSuccess('Validation passed', [
            'warnings' => $warnings,
        ]);
    }

    /**
     * Validate Step 4: Estimasi Biaya
     */
    public function validateStep4()
    {
        $group = config('Validation')->rules['sppd_basic'];
        $rules = array_intersect_key($group, array_flip(['estimasi_biaya', 'sub_kegiatan_id']));

        $valid = $this->validate($rules);
        if ($valid !== true) {
            return $this->respondError('Validasi gagal', $this->getValidationErrors(), 422);
        }

        $subKegiatanId = $this->request->getPost('sub_kegiatan_id');
        $estimasiBiaya = $this->request->getPost('estimasi_biaya');

        $sisaAnggaran = $this->subKegiatanModel->getSisaAnggaran($subKegiatanId);

        if ($estimasiBiaya > $sisaAnggaran) {
            return $this->respondError('Estimasi biaya melebihi sisa anggaran sub kegiatan', null, 422);
        }

        $percentage = ($estimasiBiaya / $sisaAnggaran) * 100;
        $warning = null;

        if ($percentage > 80) {
            $warning = 'Estimasi biaya > 80% dari sisa anggaran';
        }

        return $this->respondSuccess('Validation passed', [
            'sisa_anggaran' => $sisaAnggaran,
            'percentage' => $percentage,
            'warning' => $warning,
        ]);
    }

    /**
     * Submit SPPD
     */
    public function submit()
    {
        $group = config('Validation')->rules['sppd_basic'];
        $rules = $group;
        // augment with fields not present in the group
        $rules['no_sppd'] = 'required|is_unique[sppd.no_sppd]';
        $rules['tanggal_kembali'] = 'required|valid_date';

        $valid = $this->validate($rules);
        if ($valid !== true) {
            return $this->respondError('Validasi gagal', $this->getValidationErrors(), 422);
        }

        // Validate estimasi biaya tidak melebihi sisa anggaran
        $subKegiatanId = $this->request->getPost('sub_kegiatan_id');
        $estimasiBiaya = $this->request->getPost('estimasi_biaya');
        $sisaAnggaran = $this->subKegiatanModel->getSisaAnggaran($subKegiatanId);

        if ($estimasiBiaya > $sisaAnggaran) {
            return $this->respondError('Estimasi biaya melebihi sisa anggaran', null, 422);
        }

        $saveAsDraft = $this->request->getPost('save_as_draft') == 'true';

        $sppdData = [
            'no_sppd' => strtoupper($this->request->getPost('no_sppd')),
            'sub_kegiatan_id' => $subKegiatanId,
            'bidang_id' => user_bidang_id(),
            'tipe_perjalanan' => $this->request->getPost('tipe_perjalanan'),
            'maksud_perjalanan' => $this->request->getPost('maksud_perjalanan'),
            'dasar_surat' => $this->request->getPost('dasar_surat'),
            'alat_angkut' => $this->request->getPost('alat_angkut'),
            'tempat_berangkat' => $this->request->getPost('tempat_berangkat'),
            'tempat_tujuan' => $this->request->getPost('tempat_tujuan'),
            'tanggal_berangkat' => $this->request->getPost('tanggal_berangkat'),
            'tanggal_kembali' => $this->request->getPost('tanggal_kembali'),
            'lama_perjalanan' => $this->request->getPost('lama_perjalanan'),
            'penanggung_jawab' => $this->request->getPost('penanggung_jawab'),
            'estimasi_biaya' => $estimasiBiaya,
            'status' => $saveAsDraft ? 'draft' : 'pending',
            'submitted_at' => $saveAsDraft ? null : date('Y-m-d H:i:s'),
            'created_by' => user_id(),
        ];

        // Handle file upload (surat tugas)
        $file = $this->request->getFile('file_surat_tugas');
        if ($file && $file->isValid()) {
            $newName = 'surat_tugas_' . time() . '.' . $file->getExtension();
            $file->move(FCPATH . 'uploads/surat_tugas', $newName);
            $sppdData['file_surat_tugas'] = $newName;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insert SPPD
            $sppdId = $this->sppdModel->insert($sppdData);

            if (!$sppdId) {
                throw new \Exception('Gagal menyimpan SPPD');
            }

            // Insert SPPD Pegawai
            $pegawaiIds = $this->request->getPost('pegawai_ids');
            $this->sppdPegawaiModel->addPegawai($sppdId, $pegawaiIds);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->respondError('Gagal menyimpan SPPD', null, 500);
            }

            $this->logActivity('CREATE_SPPD', "Created SPPD: {$sppdData['no_sppd']}");

            $message = $saveAsDraft ? 'SPPD berhasil disimpan sebagai draft' : 'SPPD berhasil diajukan';

            return $this->respondSuccess($message, ['id' => $sppdId]);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->respondError($e->getMessage(), null, 500);
        }
    }

    /**
     * Get SPPD detail
     */
    public function detail($id)
    {
        $sppd = $this->sppdModel->getWithRelations($id);

        if (!$sppd) {
            return redirect()->to('/kepalabidang/sppd')->with('error', 'SPPD tidak ditemukan');
        }

        // Check authorization
        if ($sppd['bidang_id'] != user_bidang_id()) {
            return redirect()->to('/kepalabidang/sppd')->with('error', 'Unauthorized');
        }

        // Get pegawai list
        $pegawaiList = $this->sppdPegawaiModel->getPegawaiBySppdId($id);

        $data = [
            'title' => 'Detail SPPD',
            'sppd' => $sppd,
            'pegawai_list' => $pegawaiList,
        ];

        return view('kepalabidang/sppd/detail', $data);
    }

    /**
     * Get action buttons for DataTable
     */
    private function getActionButtons($sppdId, $status)
    {
        $buttons = '<div class="flex gap-2">';

        $buttons .= '<button class="btn-detail text-blue-600 hover:text-blue-800" data-id="' . $sppdId . '" title="Detail">
                        <i class="fas fa-eye"></i>
                    </button>';

        if ($status == 'draft') {
            $buttons .= '<button class="btn-edit text-green-600 hover:text-green-800" data-id="' . $sppdId . '" title="Edit">
                            <i class="fas fa-pencil-alt"></i>
                        </button>';
            $buttons .= '<button class="btn-submit text-purple-600 hover:text-purple-800" data-id="' . $sppdId . '" title="Submit">
                            <i class="fas fa-paper-plane"></i>
                        </button>';
            $buttons .= '<button class="btn-delete text-red-600 hover:text-red-800" data-id="' . $sppdId . '" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>';
        }

        if ($status == 'approved') {
            $buttons .= '<button class="btn-nota text-purple-600 hover:text-purple-800" data-id="' . $sppdId . '" title="Nota Dinas">
                            <i class="fas fa-file-pdf"></i>
                        </button>';
        }

        $buttons .= '</div>';

        return $buttons;
    }
}
