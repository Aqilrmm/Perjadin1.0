<?php

namespace App\Controllers\KepalaBidang;

use App\Controllers\BaseController;
use App\Models\Program\ProgramModel;

class ProgramController extends BaseController
{
    protected $programModel;

    public function __construct()
    {
        $this->programModel = new ProgramModel();
    }

    /**
     * Display program list
     */
    public function index()
    {
        $data = [
            'title' => 'Kelola Program',
        ];

        return view('kepalabidang/programs/index', $data);
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
        
        // Filter by current user's bidang
        $request['filters']['bidang_id'] = user_bidang_id();
        
        $data = $this->programModel->getDatatablesData($request);

        // Format data for display
        foreach ($data['data'] as $key => $row) {
            $data['data'][$key]->status_badge = get_status_badge($row->status);
            $data['data'][$key]->jumlah_anggaran_formatted = format_rupiah($row->jumlah_anggaran);
            $data['data'][$key]->sisa_anggaran_formatted = format_rupiah($row->sisa_anggaran);
            $data['data'][$key]->action = $this->getActionButtons($row->id, $row->status);
        }

        return $this->respond($data);
    }

    /**
     * Get single program data (AJAX)
     */
    public function get($id)
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $program = $this->programModel->find($id);

        if (!$program) {
            return $this->respondError('Program tidak ditemukan', null, 404);
        }

        // Check if belongs to user's bidang
        if ($program['bidang_id'] != user_bidang_id()) {
            return $this->respondError('Unauthorized', null, 403);
        }

        return $this->respondSuccess('Program found', $program);
    }

    /**
     * Create new program
     */
    public function create()
    {
        $rules = [
            'kode_program' => 'required|min_length[5]|max_length[50]|is_unique[programs.kode_program]',
            'nama_program' => 'required|min_length[10]|max_length[255]',
            'tahun_anggaran' => 'required|numeric',
            'jumlah_anggaran' => 'required|numeric|greater_than[1000000]',
        ];

        $errors = $this->validate($rules);
        if ($errors !== true) {
            return $this->respondError('Validasi gagal', $errors, 422);
        }

        $saveAsDraft = $this->request->getPost('save_as_draft') == 'true';

        $data = [
            'kode_program' => strtoupper($this->request->getPost('kode_program')),
            'nama_program' => $this->request->getPost('nama_program'),
            'bidang_id' => user_bidang_id(),
            'tahun_anggaran' => $this->request->getPost('tahun_anggaran'),
            'jumlah_anggaran' => $this->request->getPost('jumlah_anggaran'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'status' => $saveAsDraft ? 'draft' : 'pending',
            'submitted_at' => $saveAsDraft ? null : date('Y-m-d H:i:s'),
            'created_by' => user_id(),
        ];

        if ($this->programModel->insert($data)) {
            $this->logActivity('CREATE_PROGRAM', "Created program: {$data['nama_program']}");
            
            $message = $saveAsDraft ? 'Program berhasil disimpan sebagai draft' : 'Program berhasil diajukan';
            
            return $this->respondSuccess($message, ['id' => $this->programModel->getInsertID()]);
        }

        return $this->respondError('Gagal menyimpan program', null, 500);
    }

    /**
     * Update program
     */
    public function update($id)
    {
        $program = $this->programModel->find($id);
        if (!$program) {
            return $this->respondError('Program tidak ditemukan', null, 404);
        }

        // Check if belongs to user's bidang
        if ($program['bidang_id'] != user_bidang_id()) {
            return $this->respondError('Unauthorized', null, 403);
        }

        // Only draft programs can be edited
        if ($program['status'] != 'draft') {
            return $this->respondError('Hanya program draft yang dapat diedit', null, 400);
        }

        $rules = [
            'kode_program' => "required|is_unique[programs.kode_program,id,{$id}]",
            'nama_program' => 'required|min_length[10]',
            'jumlah_anggaran' => 'required|numeric|greater_than[1000000]',
        ];

        $errors = $this->validate($rules);
        if ($errors !== true) {
            return $this->respondError('Validasi gagal', $errors, 422);
        }

        $data = [
            'kode_program' => strtoupper($this->request->getPost('kode_program')),
            'nama_program' => $this->request->getPost('nama_program'),
            'tahun_anggaran' => $this->request->getPost('tahun_anggaran'),
            'jumlah_anggaran' => $this->request->getPost('jumlah_anggaran'),
            'deskripsi' => $this->request->getPost('deskripsi'),
        ];

        if ($this->programModel->update($id, $data)) {
            $this->logActivity('UPDATE_PROGRAM', "Updated program: {$data['nama_program']}");
            return $this->respondSuccess('Program berhasil diupdate');
        }

        return $this->respondError('Gagal mengupdate program', null, 500);
    }

    /**
     * Submit program for approval
     */
    public function submit($id)
    {
        $program = $this->programModel->find($id);
        if (!$program) {
            return $this->respondError('Program tidak ditemukan', null, 404);
        }

        // Check if belongs to user's bidang
        if ($program['bidang_id'] != user_bidang_id()) {
            return $this->respondError('Unauthorized', null, 403);
        }

        // Only draft programs can be submitted
        if ($program['status'] != 'draft') {
            return $this->respondError('Hanya program draft yang dapat diajukan', null, 400);
        }

        if ($this->programModel->submitProgram($id)) {
            $this->logActivity('SUBMIT_PROGRAM', "Submitted program for approval: {$program['nama_program']}");
            
            // Send notification to Kepala Dinas
            // notify_program_submitted($id);
            
            return $this->respondSuccess('Program berhasil diajukan untuk persetujuan');
        }

        return $this->respondError('Gagal mengajukan program', null, 500);
    }

    /**
     * Delete program
     */
    public function delete($id)
    {
        $program = $this->programModel->find($id);
        if (!$program) {
            return $this->respondError('Program tidak ditemukan', null, 404);
        }

        // Check if belongs to user's bidang
        if ($program['bidang_id'] != user_bidang_id()) {
            return $this->respondError('Unauthorized', null, 403);
        }

        // Only draft or rejected programs can be deleted
        if (!in_array($program['status'], ['draft', 'rejected'])) {
            return $this->respondError('Hanya program draft atau rejected yang dapat dihapus', null, 400);
        }

        if ($this->programModel->delete($id)) {
            $this->logActivity('DELETE_PROGRAM', "Deleted program: {$program['nama_program']}");
            return $this->respondSuccess('Program berhasil dihapus');
        }

        return $this->respondError('Gagal menghapus program', null, 500);
    }

    /**
     * Get action buttons for DataTable
     */
    private function getActionButtons($programId, $status)
    {
        $buttons = '<div class="flex gap-2">';
        
        // View button
        $buttons .= '<button class="btn-view text-blue-600 hover:text-blue-800" data-id="'.$programId.'" title="View">
                        <i class="fas fa-eye"></i>
                    </button>';
        
        // Edit button (only for draft)
        if ($status == 'draft') {
            $buttons .= '<button class="btn-edit text-green-600 hover:text-green-800" data-id="'.$programId.'" title="Edit">
                            <i class="fas fa-pencil-alt"></i>
                        </button>';
            $buttons .= '<button class="btn-submit text-purple-600 hover:text-purple-800" data-id="'.$programId.'" title="Submit">
                            <i class="fas fa-paper-plane"></i>
                        </button>';
        }
        
        // Delete button (only for draft or rejected)
        if (in_array($status, ['draft', 'rejected'])) {
            $buttons .= '<button class="btn-delete text-red-600 hover:text-red-800" data-id="'.$programId.'" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>';
        }
        
        $buttons .= '</div>';
        
        return $buttons;
    }
}