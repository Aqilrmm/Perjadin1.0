<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\User\UserModel;
use App\Models\Bidang\BidangModel;

class UserController extends BaseController
{
    protected $userModel;
    protected $bidangModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->bidangModel = new BidangModel();
    }

    /**
     * Display user management page
     */
    public function index()
    {
        $data = [
            'title' => 'Kelola User',
            'bidang_list' => $this->bidangModel->getActiveOptions(),
        ];

        return view('superadmin/users/index', $data);
    }

    /**
     * Get users data for DataTables (AJAX)
     */
    public function datatable()
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $request = $this->request->getPost();
        $data = $this->userModel->getDatatablesData($request);

        // Format data for display
        foreach ($data['data'] as $key => $row) {
            $data['data'][$key]->nama_lengkap = get_nama_lengkap($row);
            $data['data'][$key]->role_badge = get_role_badge($row->role);
            $data['data'][$key]->status_badge = get_user_status_badge($row->is_active, $row->is_blocked);
            $data['data'][$key]->action = $this->getActionButtons($row->id, $row->is_blocked);
        }

        return $this->respond($data);
    }

    /**
     * Get single user data (AJAX)
     */
    public function get($id)
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->respondError('User tidak ditemukan', null, 404);
        }

        // Remove password from response
        unset($user['password']);

        return $this->respondSuccess('User found', $user);
    }

    /**
     * Create new user
     */
    public function create()
    {
        $rules = config('Validation')->rules['user_create'];

        // Validate bidang_id if not superadmin, kepaladinas, or keuangan
        $role = $this->request->getPost('role');
        if (!in_array($role, ['superadmin', 'kepaladinas', 'keuangan'])) {
            $rules['bidang_id'] = 'required|numeric';
        }

        $valid = $this->validate($rules);
        if ($valid !== true) {
            return $this->respondError('Validasi gagal', $this->getValidationErrors(), 422);
        }

        $data = [
            'uuid' => $this->userModel->generateUUIDD(),
            'nip_nik' => $this->request->getPost('nip_nik'),
            'gelar_depan' => $this->request->getPost('gelar_depan'),
            'nama' => $this->request->getPost('nama'),
            'gelar_belakang' => $this->request->getPost('gelar_belakang'),
            'jenis_pegawai' => $this->request->getPost('jenis_pegawai'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'jabatan' => $this->request->getPost('jabatan'),
            'bidang_id' => $this->request->getPost('bidang_id'),
            'role' => $role,
            'is_active' => 1,
        ];

        // Handle foto upload
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid()) {
            $newName = 'profile_' . time() . '.' . $foto->getExtension();
            $foto->move(FCPATH . 'uploads/foto_profile', $newName);
            $data['foto'] = $newName;
        }

        if ($this->userModel->insert($data)) {
            $this->logActivity('CREATE_USER', "Created user: {$data['nama']}");
            return $this->respondSuccess('User berhasil ditambahkan', ['id' => $this->userModel->getInsertID()]);
        }

        return $this->respondError('Gagal menambahkan user', null, 500);
    }

    /**
     * Update user
     */
    public function update($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->respondError('User tidak ditemukan', null, 404);
        }

        $rules = config('Validation')->rules['user_update'];

        // Replace placeholder {id} in rules
        foreach ($rules as $key => $rule) {
            $rules[$key] = str_replace('{id}', $id, $rule);
        }

        // If the user is keeping the same nip_nik or email, remove is_unique check for those fields
        $postNip = $this->request->getPost('nip_nik');
        if ($postNip !== null && $postNip == $user['nip_nik'] && isset($rules['nip_nik'])) {
            $rules['nip_nik'] = str_replace('is_unique[users.nip_nik,id,' . $id . ']', '', $rules['nip_nik']);
            $rules['nip_nik'] = trim($rules['nip_nik'], '|');
        }

        $postEmail = $this->request->getPost('email');
        if ($postEmail !== null && $postEmail == $user['email'] && isset($rules['email'])) {
            $rules['email'] = str_replace('is_unique[users.email,id,' . $id . ']', '', $rules['email']);
            $rules['email'] = trim($rules['email'], '|');
        }

        // Password is optional on update; only validate/set if provided

        $valid = $this->validate($rules);
        if ($valid !== true) {
            return $this->respondError('Validasi gagal', $this->getValidationErrors(), 422);
        }

        $data = [
            'nip_nik' => $this->request->getPost('nip_nik'),
            'gelar_depan' => $this->request->getPost('gelar_depan'),
            'nama' => $this->request->getPost('nama'),
            'gelar_belakang' => $this->request->getPost('gelar_belakang'),
            'jenis_pegawai' => $this->request->getPost('jenis_pegawai'),
            'email' => $this->request->getPost('email'),
            'jabatan' => $this->request->getPost('jabatan'),
            'bidang_id' => $this->request->getPost('bidang_id'),
            'role' => $this->request->getPost('role'),
        ];

        // Update password only if provided
        if ($this->request->getPost('password')) {
            $data['password'] = $this->request->getPost('password');
        }

        // Handle foto upload
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid()) {
            // Delete old foto
            if ($user['foto'] && file_exists(FCPATH . 'uploads/foto_profile/' . $user['foto'])) {
                unlink(FCPATH . 'uploads/foto_profile/' . $user['foto']);
            }

            $newName = 'profile_' . time() . '.' . $foto->getExtension();
            $foto->move(FCPATH . 'uploads/foto_profile', $newName);
            $data['foto'] = $newName;
        }

        // We've already validated input via controller rules, skip model validation to avoid
        // duplicate/incorrect checks (model has its own validationRules with {id} placeholder).
        $this->userModel->skipValidation(true);

        $updated = $this->userModel->update($id, $data);

        if ($updated) {
            $this->logActivity('UPDATE_USER', "Updated user: {$data['nama']}");
            return $this->respondSuccess('User berhasil diupdate');
        }

        // Try to return model validation errors if available
        $modelErrors = $this->userModel->errors();
        if (!empty($modelErrors)) {
            return $this->respondError('Validasi gagal', $modelErrors, 422);
        }

        // Fallback: return generic error
        return $this->respondError('Gagal mengupdate user', null, 500);
    }

    /**
     * Delete user
     */
    public function delete($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->respondError('User tidak ditemukan', null, 404);
        }

        // Prevent deleting self
        if ($id == user_id()) {
            return $this->respondError('Tidak dapat menghapus akun sendiri', null, 400);
        }

        if ($this->userModel->delete($id)) {
            $this->logActivity('DELETE_USER', "Deleted user: {$user['nama']}");
            return $this->respondSuccess('User berhasil dihapus');
        }

        return $this->respondError('Gagal menghapus user', null, 500);
    }

    /**
     * Block user
     */
    public function block($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->respondError('User tidak ditemukan', null, 404);
        }

        // Prevent blocking self
        if ($id == user_id()) {
            return $this->respondError('Tidak dapat memblokir akun sendiri', null, 400);
        }

        $reason = $this->request->getPost('reason') ?: 'Blocked by administrator';

        if ($this->userModel->blockUser($id, $reason, user_id())) {
            $this->logActivity('BLOCK_USER', "Blocked user: {$user['nama']}. Reason: {$reason}");
            return $this->respondSuccess('User berhasil diblokir');
        }

        return $this->respondError('Gagal memblokir user', null, 500);
    }

    /**
     * Unblock user
     */
    public function unblock($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->respondError('User tidak ditemukan', null, 404);
        }

        if ($this->userModel->unblockUser($id)) {
            $this->logActivity('UNBLOCK_USER', "Unblocked user: {$user['nama']}");
            return $this->respondSuccess('User berhasil di-unblock');
        }

        return $this->respondError('Gagal meng-unblock user', null, 500);
    }

    /**
     * Get action buttons for DataTable
     */
    private function getActionButtons($userId, $isBlocked)
    {
        $buttons = '<div class="flex gap-2">';
        $buttons .= '<button class="btn-edit text-blue-600 hover:text-blue-800" data-id="' . $userId . '" title="Edit">
                        <i class="fas fa-pencil-alt"></i>
                    </button>';
        $buttons .= '<button class="btn-delete text-red-600 hover:text-red-800" data-id="' . $userId . '" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>';

        if ($isBlocked) {
            $buttons .= '<button class="btn-unblock text-green-600 hover:text-green-800" data-id="' . $userId . '" title="Unblock">
                            <i class="fas fa-unlock"></i>
                        </button>';
        } else {
            $buttons .= '<button class="btn-block text-orange-600 hover:text-orange-800" data-id="' . $userId . '" title="Block">
                            <i class="fas fa-lock"></i>
                        </button>';
        }

        $buttons .= '</div>';

        return $buttons;
    }
}
