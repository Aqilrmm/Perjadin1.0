<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\Bidang\BidangModel;

class BidangController extends BaseController
{
    protected $bidangModel;

    public function __construct()
    {
        $this->bidangModel = new BidangModel();
    }

    /**
     * Display bidang management page
     */
    public function index()
    {
        $data = [
            'title' => 'Kelola Bidang',
        ];

        return view('superadmin/bidang/index', $data);
    }

    /**
     * Get bidang data for DataTables (AJAX)
     */
    public function datatable()
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $request = $this->request->getPost();
        $data = $this->bidangModel->getDatatablesData($request);

        // Format data for display
        foreach ($data['data'] as $key => $row) {
            $data['data'][$key]->status_badge = $row->is_active ? 
                '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>' :
                '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactive</span>';
            $data['data'][$key]->action = $this->getActionButtons($row->id, $row->is_active);
        }

        return $this->respond($data);
    }

    /**
     * Get single bidang data (AJAX)
     */
    public function get($id)
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $bidang = $this->bidangModel->find($id);

        if (!$bidang) {
            return $this->respondError('Bidang tidak ditemukan', null, 404);
        }

        return $this->respondSuccess('Bidang found', $bidang);
    }

    /**
     * Create new bidang
     */
    public function create()
    {
        $rules = [
            'kode_bidang' => 'required|min_length[2]|max_length[10]|is_unique[bidang.kode_bidang]',
            'nama_bidang' => 'required|min_length[3]|max_length[255]|is_unique[bidang.nama_bidang]',
        ];

        $errors = $this->validate($rules);
        if ($errors !== true) {
            return $this->respondError('Validasi gagal', $errors, 422);
        }

        $data = [
            'kode_bidang' => strtoupper($this->request->getPost('kode_bidang')),
            'nama_bidang' => $this->request->getPost('nama_bidang'),
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => 1,
        ];

        if ($this->bidangModel->insert($data)) {
            $this->logActivity('CREATE_BIDANG', "Created bidang: {$data['nama_bidang']}");
            return $this->respondSuccess('Bidang berhasil ditambahkan', ['id' => $this->bidangModel->getInsertID()]);
        }

        return $this->respondError('Gagal menambahkan bidang', null, 500);
    }

    /**
     * Update bidang
     */
    public function update($id)
    {
        $bidang = $this->bidangModel->find($id);
        if (!$bidang) {
            return $this->respondError('Bidang tidak ditemukan', null, 404);
        }

        $rules = [
            'kode_bidang' => "required|min_length[2]|max_length[10]|is_unique[bidang.kode_bidang,id,{$id}]",
            'nama_bidang' => "required|min_length[3]|max_length[255]|is_unique[bidang.nama_bidang,id,{$id}]",
        ];

        $errors = $this->validate($rules);
        if ($errors !== true) {
            return $this->respondError('Validasi gagal', $errors, 422);
        }

        $data = [
            'kode_bidang' => strtoupper($this->request->getPost('kode_bidang')),
            'nama_bidang' => $this->request->getPost('nama_bidang'),
            'keterangan' => $this->request->getPost('keterangan'),
        ];

        if ($this->bidangModel->update($id, $data)) {
            $this->logActivity('UPDATE_BIDANG', "Updated bidang: {$data['nama_bidang']}");
            return $this->respondSuccess('Bidang berhasil diupdate');
        }

        return $this->respondError('Gagal mengupdate bidang', null, 500);
    }

    /**
     * Delete bidang
     */
    public function delete($id)
    {
        $bidang = $this->bidangModel->find($id);
        if (!$bidang) {
            return $this->respondError('Bidang tidak ditemukan', null, 404);
        }

        // Check if bidang has users
        if ($this->bidangModel->hasUsers($id)) {
            return $this->respondError('Tidak dapat menghapus bidang yang masih memiliki pegawai', null, 400);
        }

        // Check if bidang has programs
        if ($this->bidangModel->hasPrograms($id)) {
            return $this->respondError('Tidak dapat menghapus bidang yang masih memiliki program', null, 400);
        }

        if ($this->bidangModel->delete($id)) {
            $this->logActivity('DELETE_BIDANG', "Deleted bidang: {$bidang['nama_bidang']}");
            return $this->respondSuccess('Bidang berhasil dihapus');
        }

        return $this->respondError('Gagal menghapus bidang', null, 500);
    }

    /**
     * Activate bidang
     */
    public function activate($id)
    {
        $bidang = $this->bidangModel->find($id);
        if (!$bidang) {
            return $this->respondError('Bidang tidak ditemukan', null, 404);
        }

        if ($this->bidangModel->activate($id)) {
            $this->logActivity('ACTIVATE_BIDANG', "Activated bidang: {$bidang['nama_bidang']}");
            return $this->respondSuccess('Bidang berhasil diaktifkan');
        }

        return $this->respondError('Gagal mengaktifkan bidang', null, 500);
    }

    /**
     * Deactivate bidang
     */
    public function deactivate($id)
    {
        $bidang = $this->bidangModel->find($id);
        if (!$bidang) {
            return $this->respondError('Bidang tidak ditemukan', null, 404);
        }

        if ($this->bidangModel->deactivate($id)) {
            $this->logActivity('DEACTIVATE_BIDANG', "Deactivated bidang: {$bidang['nama_bidang']}");
            return $this->respondSuccess('Bidang berhasil dinonaktifkan');
        }

        return $this->respondError('Gagal menonaktifkan bidang', null, 500);
    }

    /**
     * Get action buttons for DataTable
     */
    private function getActionButtons($bidangId, $isActive)
    {
        $buttons = '<div class="flex gap-2">';
        $buttons .= '<button class="btn-edit text-blue-600 hover:text-blue-800" data-id="'.$bidangId.'" title="Edit">
                        <i class="fas fa-pencil-alt"></i>
                    </button>';
        $buttons .= '<button class="btn-delete text-red-600 hover:text-red-800" data-id="'.$bidangId.'" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>';
        
        if ($isActive) {
            $buttons .= '<button class="btn-deactivate text-orange-600 hover:text-orange-800" data-id="'.$bidangId.'" title="Deactivate">
                            <i class="fas fa-ban"></i>
                        </button>';
        } else {
            $buttons .= '<button class="btn-activate text-green-600 hover:text-green-800" data-id="'.$bidangId.'" title="Activate">
                            <i class="fas fa-check"></i>
                        </button>';
        }
        
        $buttons .= '</div>';
        
        return $buttons;
    }
}