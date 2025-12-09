<?php

namespace App\Controllers\KepalaDinas;

use App\Controllers\BaseController;

// ========================================
// APPROVAL KEGIATAN CONTROLLER
// ========================================

class ApprovalKegiatanController extends BaseController
{
    protected $kegiatanModel;

    public function __construct()
    {
        $this->kegiatanModel = new \App\Models\Program\KegiatanModel();
    }

    public function index()
    {
        return view('kepaladinas/approval/kegiatan', ['title' => 'Persetujuan Kegiatan']);
    }

    public function datatable()
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $request = $this->request->getPost();
        // Use the datatable helper that joins programs and aggregates budgets
        if (method_exists($this->kegiatanModel, 'getDatatablesWithBudget')) {
            $data = $this->kegiatanModel->getDatatablesWithBudget($request);
        } else {
            $data = $this->kegiatanModel->getDatatablesData($request);
        }

        foreach ($data['data'] as $key => $row) {
            $data['data'][$key]->status_badge = get_status_badge($row->status);
            $data['data'][$key]->anggaran_kegiatan_formatted = format_rupiah($row->anggaran_kegiatan);
            $data['data'][$key]->action = $this->getActionButtons($row->id, $row->status);
        }

        return $this->respond($data);
    }

    public function detail($id)
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $kegiatan = $this->kegiatanModel->getWithRelations($id);
        if (!$kegiatan) {
            return $this->respondError('Kegiatan tidak ditemukan', null, 404);
        }

        $kegiatan['sisa_anggaran'] = $this->kegiatanModel->getSisaAnggaran($id);
        return $this->respondSuccess('Kegiatan detail', $kegiatan);
    }

    public function approve($id)
    {
        $kegiatan = $this->kegiatanModel->find($id);
        if (!$kegiatan || $kegiatan['status'] != 'pending') {
            return $this->respondError('Hanya kegiatan pending yang dapat disetujui', null, 400);
        }

        $catatan = $this->request->getPost('catatan');
        if ($this->kegiatanModel->approveKegiatan($id, user_id(), $catatan)) {
            $this->logActivity('APPROVE_KEGIATAN', "Approved kegiatan: {$kegiatan['nama_kegiatan']}");
            return $this->respondSuccess('Kegiatan berhasil disetujui');
        }

        return $this->respondError('Gagal menyetujui kegiatan', null, 500);
    }

    public function reject($id)
    {
        $kegiatan = $this->kegiatanModel->find($id);
        if (!$kegiatan || $kegiatan['status'] != 'pending') {
            return $this->respondError('Hanya kegiatan pending yang dapat ditolak', null, 400);
        }

        $rules = config('Validation')->rules['catatan'];
        $valid = $this->validate($rules);
        if ($valid !== true) {
            return $this->respondError('Catatan penolakan wajib diisi minimal 10 karakter', $this->getValidationErrors(), 422);
        }

        $catatan = $this->request->getPost('catatan');
        if ($this->kegiatanModel->rejectKegiatan($id, $catatan)) {
            $this->logActivity('REJECT_KEGIATAN', "Rejected kegiatan: {$kegiatan['nama_kegiatan']}");
            return $this->respondSuccess('Kegiatan berhasil ditolak');
        }

        return $this->respondError('Gagal menolak kegiatan', null, 500);
    }

    private function getActionButtons($id, $status)
    {
        $buttons = '<div class="flex gap-2">';
        $buttons .= '<button class="btn-detail text-blue-600" data-id="' . $id . '"><i class="fas fa-eye"></i></button>';
        if ($status == 'pending') {
            $buttons .= '<button class="btn-approve text-green-600" data-id="' . $id . '"><i class="fas fa-check-circle"></i></button>';
            $buttons .= '<button class="btn-reject text-red-600" data-id="' . $id . '"><i class="fas fa-times-circle"></i></button>';
        }
        $buttons .= '</div>';
        return $buttons;
    }
}
