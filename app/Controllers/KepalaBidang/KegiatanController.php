<?php

namespace App\Controllers\KepalaBidang;

use App\Controllers\BaseController;

// ========================================
// KEGIATAN CONTROLLER
// ========================================

class KegiatanController extends BaseController
{
    protected $kegiatanModel;
    protected $programModel;

    public function __construct()
    {
        $this->kegiatanModel = new \App\Models\Program\KegiatanModel();
        $this->programModel = new \App\Models\Program\ProgramModel();
    }

    public function index()
    {
        return view('kepalabidang/kegiatan/index', [
            'title' => 'Kelola Kegiatan',
            'approved_programs' => $this->programModel->getApprovedOptions(user_bidang_id()),
        ]);
    }

    public function datatable()
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $request = $this->request->getPost();
        $request['filters']['bidang_id'] = user_bidang_id();

        // Use optimized datatable that joins programs and computes sisa in SQL
        $data = $this->kegiatanModel->getDatatablesWithBudget($request);

        foreach ($data['data'] as $key => $row) {
            $data['data'][$key]->status_badge = get_status_badge($row->status);
            $data['data'][$key]->anggaran_formatted = format_rupiah($row->anggaran_kegiatan ?? 0);
            $data['data'][$key]->sisa_formatted = format_rupiah($row->sisa_anggaran ?? 0);
            $data['data'][$key]->action = $this->getActionButtons($row->id, $row->status);
        }

        return $this->respond($data);
    }

    public function get($id)
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $kegiatan = $this->kegiatanModel->find($id);
        if (!$kegiatan) {
            return $this->respondError('Kegiatan tidak ditemukan', null, 404);
        }

        return $this->respondSuccess('Kegiatan found', $kegiatan);
    }

    public function create()
    {
        $rules = config('Validation')->rules['kegiatan'];
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

        // Validate anggaran
        $programId = $this->request->getPost('program_id');
        $anggaranKegiatan = $this->request->getPost('anggaran_kegiatan');

        if (!$this->kegiatanModel->validateAnggaran($programId, $anggaranKegiatan)) {
            return $this->respondError('Anggaran kegiatan melebihi sisa anggaran program', null, 422);
        }

        $saveAsDraft = $this->request->getPost('save_as_draft') == 'true';

        $data = [
            'bidang_id' => user_bidang_id(),
            'program_id' => $programId,
            'kode_kegiatan' => strtoupper($this->request->getPost('kode_kegiatan')),
            'nama_kegiatan' => $this->request->getPost('nama_kegiatan'),
            'anggaran_kegiatan' => $anggaranKegiatan,
            'deskripsi' => $this->request->getPost('deskripsi'),
            'status' => $saveAsDraft ? 'draft' : 'pending',
            'submitted_at' => $saveAsDraft ? null : date('Y-m-d H:i:s'),
            'created_by' => user_id(),
        ];

        if ($this->kegiatanModel->insert($data)) {
            $this->logActivity('CREATE_KEGIATAN', "Created kegiatan: {$data['nama_kegiatan']}");
            return $this->respondSuccess($saveAsDraft ? 'Kegiatan disimpan sebagai draft' : 'Kegiatan berhasil diajukan');
        }

        return $this->respondError('Gagal menyimpan kegiatan', null, 500);
    }

    public function update($id)
    {
        $kegiatan = $this->kegiatanModel->find($id);
        if (!$kegiatan || $kegiatan['status'] != 'draft') {
            return $this->respondError('Hanya kegiatan draft yang dapat diedit', null, 400);
        }

        $rules = config('Validation')->rules['kegiatan'];
        // Replace placeholder {id}
        foreach ($rules as $key => $rule) {
            $rules[$key] = str_replace('{id}', $id, $rule);
        }

        $valid = $this->validate($rules);
        if ($valid !== true) {
            return $this->respondError('Validasi gagal', $this->getValidationErrors(), 422);
        }

        $anggaranKegiatan = $this->request->getPost('anggaran_kegiatan');
        if (!$this->kegiatanModel->validateAnggaran($kegiatan['program_id'], $anggaranKegiatan, $id)) {
            return $this->respondError('Anggaran melebihi sisa anggaran program', null, 422);
        }

        $data = [
            'kode_kegiatan' => strtoupper($this->request->getPost('kode_kegiatan')),
            'nama_kegiatan' => $this->request->getPost('nama_kegiatan'),
            'anggaran_kegiatan' => $anggaranKegiatan,
            'deskripsi' => $this->request->getPost('deskripsi'),
        ];

        if ($this->kegiatanModel->update($id, $data)) {
            $this->logActivity('UPDATE_KEGIATAN', "Updated kegiatan: {$data['nama_kegiatan']}");
            return $this->respondSuccess('Kegiatan berhasil diupdate');
        }

        return $this->respondError('Gagal mengupdate kegiatan', null, 500);
    }

    public function submit($id)
    {
        $kegiatan = $this->kegiatanModel->find($id);
        if (!$kegiatan || $kegiatan['status'] != 'draft') {
            return $this->respondError('Hanya kegiatan draft yang dapat diajukan', null, 400);
        }

        if ($this->kegiatanModel->submitKegiatan($id)) {
            $this->logActivity('SUBMIT_KEGIATAN', "Submitted kegiatan: {$kegiatan['nama_kegiatan']}");
            return $this->respondSuccess('Kegiatan berhasil diajukan');
        }

        return $this->respondError('Gagal mengajukan kegiatan', null, 500);
    }

    public function delete($id)
    {
        $kegiatan = $this->kegiatanModel->find($id);
        if (!$kegiatan || !in_array($kegiatan['status'], ['draft', 'rejected'])) {
            return $this->respondError('Hanya kegiatan draft/rejected yang dapat dihapus', null, 400);
        }

        if ($this->kegiatanModel->delete($id)) {
            $this->logActivity('DELETE_KEGIATAN', "Deleted kegiatan: {$kegiatan['nama_kegiatan']}");
            return $this->respondSuccess('Kegiatan berhasil dihapus');
        }

        return $this->respondError('Gagal menghapus kegiatan', null, 500);
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
