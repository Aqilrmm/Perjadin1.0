<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\User\UserModel;

class ProfileController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Display user profile page
     */
    public function index()
    {
        $user = $this->userModel->getWithRelations(user_id());

        if (!$user) {
            return redirect()->to('/')->with('error', 'User tidak ditemukan');
        }

        $data = [
            'title' => 'Profil Saya',
            'user' => $user,
        ];

        return view('auth/profile', $data);
    }

    /**
     * Update profile
     */
    public function update()
    {
        $userId = user_id();
        $user = $this->userModel->find($userId);

        if (!$user) {
            return $this->respondError('User tidak ditemukan', null, 404);
        }

        $rules = [
            'gelar_depan' => 'permit_empty|max_length[50]',
            'nama' => 'required|min_length[3]|max_length[255]',
            'gelar_belakang' => 'permit_empty|max_length[50]',
            'email' => "required|valid_email|is_unique[users.email,id,{$userId}]",
        ];

        $valid = $this->validate($rules);
        if ($valid !== true) {
            return $this->respondError('Validasi gagal', $this->getValidationErrors(), 422);
        }

        $data = [
            'gelar_depan' => $this->request->getPost('gelar_depan'),
            'nama' => $this->request->getPost('nama'),
            'gelar_belakang' => $this->request->getPost('gelar_belakang'),
            'email' => $this->request->getPost('email'),
        ];

        if ($this->userModel->update($userId, $data)) {
            // Update session data
            $updatedUser = $this->userModel->find($userId);
            $sessionData = $this->session->get('user_data');
            $sessionData['nama'] = $updatedUser['nama'];
            $sessionData['email'] = $updatedUser['email'];
            $this->session->set('user_data', $sessionData);

            $this->logActivity('UPDATE_PROFILE', "Updated profile");
            return $this->respondSuccess('Profil berhasil diupdate');
        }

        return $this->respondError('Gagal mengupdate profil', null, 500);
    }

    /**
     * Change password
     */
    public function changePassword()
    {
        $rules = [
            'current_password' => 'required|min_length[8]',
            'new_password' => 'required|min_length[8]|differs[current_password]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        $valid = $this->validate($rules);
        if ($valid !== true) {
            return $this->respondError('Validasi gagal', $this->getValidationErrors(), 422);
        }

        $userId = user_id();
        $user = $this->userModel->find($userId);

        // Verify current password
        if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
            return $this->respondError('Password lama tidak sesuai', null, 422);
        }

        $data = [
            'password' => $this->request->getPost('new_password'),
        ];

        if ($this->userModel->update($userId, $data)) {
            $this->logActivity('CHANGE_PASSWORD', "Changed password");
            return $this->respondSuccess('Password berhasil diubah');
        }

        return $this->respondError('Gagal mengubah password', null, 500);
    }

    /**
     * Upload profile photo
     */
    public function uploadPhoto()
    {
        $rules = [
            'foto' => 'uploaded[foto]|max_size[foto,2048]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]',
        ];

        $valid = $this->validate($rules);
        if ($valid !== true) {
            return $this->respondError('Validasi gagal', $this->getValidationErrors(), 422);
        }

        $userId = user_id();
        $user = $this->userModel->find($userId);

        $foto = $this->request->getFile('foto');

        if ($foto && $foto->isValid()) {
            // Delete old photo
            if ($user['foto'] && file_exists(FCPATH . 'uploads/foto_profile/' . $user['foto'])) {
                unlink(FCPATH . 'uploads/foto_profile/' . $user['foto']);
            }

            $newName = 'profile_' . $userId . '_' . time() . '.' . $foto->getExtension();

            // Resize image to 300x300
            $image = \Config\Services::image()
                ->withFile($foto->getTempName())
                ->resize(300, 300, true, 'height')
                ->save(FCPATH . 'uploads/foto_profile/' . $newName);

            $data = ['foto' => $newName];

            if ($this->userModel->update($userId, $data)) {
                // Update session
                $sessionData = $this->session->get('user_data');
                $sessionData['foto'] = $newName;
                $this->session->set('user_data', $sessionData);

                $this->logActivity('UPDATE_PHOTO', "Updated profile photo");

                return $this->respondSuccess('Foto profil berhasil diupdate', [
                    'foto_url' => base_url('uploads/foto_profile/' . $newName)
                ]);
            }
        }

        return $this->respondError('Gagal mengupload foto', null, 500);
    }

    /**
     * Delete profile photo
     */
    public function deletePhoto()
    {
        $userId = user_id();
        $user = $this->userModel->find($userId);

        if ($user['foto'] && file_exists(FCPATH . 'uploads/foto_profile/' . $user['foto'])) {
            unlink(FCPATH . 'uploads/foto_profile/' . $user['foto']);
        }

        if ($this->userModel->update($userId, ['foto' => null])) {
            // Update session
            $sessionData = $this->session->get('user_data');
            $sessionData['foto'] = null;
            $this->session->set('user_data', $sessionData);

            $this->logActivity('DELETE_PHOTO', "Deleted profile photo");
            return $this->respondSuccess('Foto profil berhasil dihapus');
        }

        return $this->respondError('Gagal menghapus foto', null, 500);
    }

    /**
     * Get activity history
     */
    public function activityHistory()
    {
        $logModel = new \App\Models\Log\SecurityLogModel();

        $limit = $this->request->getGet('limit') ?: 20;
        $page = $this->request->getGet('page') ?: 1;

        $logs = $logModel->getByUser(user_id(), $limit);

        $data = [
            'title' => 'Riwayat Aktivitas',
            'logs' => $logs,
        ];

        if ($this->request->isAJAX()) {
            return $this->respondSuccess('Activity history', ['logs' => $logs]);
        }

        return view('auth/activity_history', $data);
    }
}
