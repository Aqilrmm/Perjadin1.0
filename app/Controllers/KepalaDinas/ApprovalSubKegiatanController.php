<?php

namespace App\Controllers\KepalaDinas;

use App\Controllers\BaseController;

// ========================================
// APPROVAL SUB KEGIATAN CONTROLLER
// ========================================

class ApprovalSubKegiatanController extends BaseController
{
    protected $subKegiatanModel;

    public function __construct()
    {
        $this->subKegiatanModel = new \App\Models\Program\SubKegiatanModel();
    }

    public function index()
    {
        return view('kepaladinas/approval/subkegiatan', ['title' => 'Persetujuan Sub Kegiatan']);
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
            $data['data'][$key]->action = $this->getActionButtons($row->id, $row->status);
        }

        return $this->respond($data);
    }

    public function detail($id)
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $subKegiatan = $this->subKegiatanModel->getWithRelations($id);
        if (!$subKegiatan) {
            return $this->respondError('Sub kegiatan tidak ditemukan', null, 404);
        }

        $subKegiatan['sisa_anggaran'] = $this->subKegiatanModel->getSisaAnggaran($id);
        return $this->respondSuccess('Sub kegiatan detail', $subKegiatan);
    }

    public function approve($id)
    {
        $subKegiatan = $this->subKegiatanModel->find($id);
        if (!$subKegiatan || $subKegiatan['status'] != 'pending') {
            return $this->respondError('Hanya sub kegiatan pending yang dapat disetujui', null, 400);
        }

        $catatan = $this->request->getPost('catatan');
        if ($this->subKegiatanModel->approveSubKegiatan($id, user_id(), $catatan)) {
            $this->logActivity('APPROVE_SUBKEGIATAN', "Approved sub kegiatan: {$subKegiatan['nama_sub_kegiatan']}");
            return $this->respondSuccess('Sub kegiatan berhasil disetujui');
        }

        return $this->respondError('Gagal menyetujui sub kegiatan', null, 500);
    }

    public function reject($id)
    {
        $subKegiatan = $this->subKegiatanModel->find($id);
        if (!$subKegiatan || $subKegiatan['status'] != 'pending') {
            return $this->respondError('Hanya sub kegiatan pending yang dapat ditolak', null, 400);
        }

        $rules = ['catatan' => 'required|min_length[10]'];
        $errors = $this->validate($rules);
        if ($errors !== true) {
            return $this->respondError('Catatan penolakan wajib diisi minimal 10 karakter', $errors, 422);
        }

        $catatan = $this->request->getPost('catatan');
        if ($this->subKegiatanModel->rejectSubKegiatan($id, $catatan)) {
            $this->logActivity('REJECT_SUBKEGIATAN', "Rejected sub kegiatan: {$subKegiatan['nama_sub_kegiatan']}");
            return $this->respondSuccess('Sub kegiatan berhasil ditolak');
        }

        return $this->respondError('Gagal menolak sub kegiatan', null, 500);
    }

    private function getActionButtons($id, $status)
    {
        $buttons = '<div class="flex gap-2">';
        $buttons .= '<button class="btn-detail text-blue-600" data-id="'.$id.'"><i class="fas fa-eye"></i></button>';
        if ($status == 'pending') {
            $buttons .= '<button class="btn-approve text-green-600" data-id="'.$id.'"><i class="fas fa-check-circle"></i></button>';
            $buttons .= '<button class="btn-reject text-red-600" data-id="'.$id.'"><i class="fas fa-times-circle"></i></button>';
        }
        $buttons .= '</div>';
        return $buttons;
    }
}