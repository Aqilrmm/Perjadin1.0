<?php

namespace App\Controllers\KepalaDinas;

use App\Controllers\BaseController;
use App\Models\Program\ProgramModel;

class ApprovalProgramController extends BaseController
{
    protected $programModel;

    public function __construct()
    {
        $this->programModel = new ProgramModel();
    }

    /**
     * Display program approval page
     */
    public function index()
    {
        $data = [
            'title' => 'Persetujuan Program',
        ];

        return view('kepaladinas/approval/program', $data);
    }

    /**
     * Get programs data for DataTables (AJAX)
     */
    public function datatable()
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $request = $this->request->getPost();
        $data = $this->programModel->getDatatablesData($request);

        // Format data for display
        foreach ($data['data'] as $key => $row) {
            $data['data'][$key]->status_badge = get_status_badge($row->status);
            $data['data'][$key]->jumlah_anggaran_formatted = format_rupiah($row->jumlah_anggaran);
            $data['data'][$key]->submitted_at_formatted = format_tanggal_waktu($row->submitted_at);
            $data['data'][$key]->action = $this->getActionButtons($row->id, $row->status);
        }

        return $this->respond($data);
    }

    /**
     * Get program detail (AJAX)
     */
    public function detail($id)
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $program = $this->programModel->getWithRelations($id);

        if (!$program) {
            return $this->respondError('Program tidak ditemukan', null, 404);
        }

        // Get sisa anggaran
        $program['sisa_anggaran'] = $this->programModel->getSisaAnggaran($id);

        return $this->respondSuccess('Program detail', $program);
    }

    /**
     * Approve program
     */
    public function approve($id)
    {
        $program = $this->programModel->find($id);
        
        if (!$program) {
            return $this->respondError('Program tidak ditemukan', null, 404);
        }

        // Only pending programs can be approved
        if ($program['status'] != 'pending') {
            return $this->respondError('Hanya program pending yang dapat disetujui', null, 400);
        }

        $catatan = $this->request->getPost('catatan');

        if ($this->programModel->approveProgram($id, user_id(), $catatan)) {
            $this->logActivity('APPROVE_PROGRAM', "Approved program: {$program['nama_program']}");
            
            // Send notification to Kepala Bidang
            notify_program_approved($id, $program['created_by']);
            
            return $this->respondSuccess('Program berhasil disetujui');
        }

        return $this->respondError('Gagal menyetujui program', null, 500);
    }

    /**
     * Reject program
     */
    public function reject($id)
    {
        $program = $this->programModel->find($id);
        
        if (!$program) {
            return $this->respondError('Program tidak ditemukan', null, 404);
        }

        // Only pending programs can be rejected
        if ($program['status'] != 'pending') {
            return $this->respondError('Hanya program pending yang dapat ditolak', null, 400);
        }

        $rules = [
            'catatan' => 'required|min_length[10]',
        ];

        $errors = $this->validate($rules);
        if ($errors !== true) {
            return $this->respondError('Catatan penolakan wajib diisi minimal 10 karakter', $errors, 422);
        }

        $catatan = $this->request->getPost('catatan');

        if ($this->programModel->rejectProgram($id, $catatan)) {
            $this->logActivity('REJECT_PROGRAM', "Rejected program: {$program['nama_program']}");
            
            // Send notification to Kepala Bidang
            notify_program_rejected($id, $program['created_by'], $catatan);
            
            return $this->respondSuccess('Program berhasil ditolak');
        }

        return $this->respondError('Gagal menolak program', null, 500);
    }

    /**
     * Get action buttons for DataTable
     */
    private function getActionButtons($programId, $status)
    {
        $buttons = '<div class="flex gap-2">';
        
        // Detail button
        $buttons .= '<button class="btn-detail text-blue-600 hover:text-blue-800" data-id="'.$programId.'" title="Detail">
                        <i class="fas fa-eye"></i>
                    </button>';
        
        // Approve & Reject buttons (only for pending)
        if ($status == 'pending') {
            $buttons .= '<button class="btn-approve text-green-600 hover:text-green-800" data-id="'.$programId.'" title="Approve">
                            <i class="fas fa-check-circle"></i>
                        </button>';
            $buttons .= '<button class="btn-reject text-red-600 hover:text-red-800" data-id="'.$programId.'" title="Reject">
                            <i class="fas fa-times-circle"></i>
                        </button>';
        }
        
        $buttons .= '</div>';
        
        return $buttons;
    }
}