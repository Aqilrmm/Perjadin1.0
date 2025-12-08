<?php

namespace App\Controllers\KepalaBidang;

use App\Controllers\BaseController;

// ========================================
// SUB KEGIATAN CONTROLLER
// ========================================

class SubKegiatanController extends BaseController
{
    protected $subKegiatanModel;
    protected $kegiatanModel;

    public function __construct()
    {
        $this->subKegiatanModel = new \App\Models\Program\SubKegiatanModel();
        $this->kegiatanModel = new \App\Models\Program\KegiatanModel();
    }

    public function index()
    {
        return view('kepalabidang/subkegiatan/index', [
            'title' => 'Kelola Sub Kegiatan',
            'approved_kegiatan' => $this->kegiatanModel->getApprovedOptions(),
        ]);
    }

    public function datatable()
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $request = $this->request->getPost();
        $data = $this->subKegiatanModel->getDatatablesData($request);

        foreach ($data['data'] as $key => $row) {
            $data['data'][$key]->status_badge = get_status_badge($row->status);
            $data['data'][$key]->anggaran_formatted = format_rupiah($row->anggaran_sub_kegiatan);
            $data['data'][$key]->sisa_formatted = format_rupiah($row->sisa_anggaran);
            $data['data'][$key]->action = $this->getActionButtons($row->id, $row->status);
        }

        return $this->respond($data);
    }

    public function create()
    {
        $rules = config('Validation')->rules['sub_kegiatan'];
        // For create remove any {id} placeholders present in unique rules
        foreach ($rules as $k => $r) {
            if (strpos($r, '{id}') !== false) {
                $rules[$k] = str_replace(',id,{id}', '', $r);
            }
        }

        $valid = $this->validate($rules);
        if ($valid !== true) {
            return $this->respondError('Validasi gagal', $this->getValidationErrors(), 422);
        }

        $kegiatanId = $this->request->getPost('kegiatan_id');
        $anggaran = $this->request->getPost('anggaran_sub_kegiatan');

        if (!$this->subKegiatanModel->validateAnggaran($kegiatanId, $anggaran)) {
            return $this->respondError('Anggaran melebihi sisa anggaran kegiatan', null, 422);
        }

        $saveAsDraft = $this->request->getPost('save_as_draft') == 'true';

        $data = [
            'kegiatan_id' => $kegiatanId,
            'kode_sub_kegiatan' => strtoupper($this->request->getPost('kode_sub_kegiatan')),
            'nama_sub_kegiatan' => $this->request->getPost('nama_sub_kegiatan'),
            'anggaran_sub_kegiatan' => $anggaran,
            'deskripsi' => $this->request->getPost('deskripsi'),
            'status' => $saveAsDraft ? 'draft' : 'pending',
            'submitted_at' => $saveAsDraft ? null : date('Y-m-d H:i:s'),
            'created_by' => user_id(),
        ];

        if ($this->subKegiatanModel->insert($data)) {
            $this->logActivity('CREATE_SUBKEGIATAN', "Created sub kegiatan: {$data['nama_sub_kegiatan']}");
            return $this->respondSuccess($saveAsDraft ? 'Sub kegiatan disimpan sebagai draft' : 'Sub kegiatan berhasil diajukan');
        }

        return $this->respondError('Gagal menyimpan sub kegiatan', null, 500);
    }

    public function submit($id)
    {
        $subKegiatan = $this->subKegiatanModel->find($id);
        if (!$subKegiatan || $subKegiatan['status'] != 'draft') {
            return $this->respondError('Hanya sub kegiatan draft yang dapat diajukan', null, 400);
        }

        if ($this->subKegiatanModel->submitSubKegiatan($id)) {
            $this->logActivity('SUBMIT_SUBKEGIATAN', "Submitted sub kegiatan ID: {$id}");
            return $this->respondSuccess('Sub kegiatan berhasil diajukan');
        }

        return $this->respondError('Gagal mengajukan sub kegiatan', null, 500);
    }

    public function delete($id)
    {
        $subKegiatan = $this->subKegiatanModel->find($id);
        if (!$subKegiatan || !in_array($subKegiatan['status'], ['draft', 'rejected'])) {
            return $this->respondError('Hanya sub kegiatan draft/rejected yang dapat dihapus', null, 400);
        }

        if ($this->subKegiatanModel->delete($id)) {
            $this->logActivity('DELETE_SUBKEGIATAN', "Deleted sub kegiatan ID: {$id}");
            return $this->respondSuccess('Sub kegiatan berhasil dihapus');
        }

        return $this->respondError('Gagal menghapus sub kegiatan', null, 500);
    }

    private function getActionButtons($id, $status)
    {
        $buttons = '<div class="flex gap-2">';
        $buttons .= '<button class="btn-view text-blue-600" data-id="' . $id . '"><i class="fas fa-eye"></i></button>';
        if ($status == 'draft') {
            $buttons .= '<button class="btn-edit text-green-600" data-id="' . $id . '"><i class="fas fa-pencil-alt"></i></button>';
            $buttons .= '<button class="btn-submit text-purple-600" data-id="' . $id . '"><i class="fas fa-paper-plane"></i></button>';
        }
        if (in_array($status, ['draft', 'rejected'])) {
            $buttons .= '<button class="btn-delete text-red-600" data-id="' . $id . '"><i class="fas fa-trash"></i></button>';
        }
        $buttons .= '</div>';
        return $buttons;
    }
}
