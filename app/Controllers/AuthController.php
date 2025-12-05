<?php
namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\User\UserModel;

class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Display login page
     */
    public function login()
    {
        // Redirect if already logged in
        if ($this->session->has('user_data')) {
            return redirect()->to($this->getRedirectByRole($this->session->get('user_data')['role']));
        }

        return view('auth/login');
    }

    /**
     * Process login
     */
    public function processLogin()
    {
        // Validate input
        $rules = [
            'nip_nik' => 'required|min_length[16]|max_length[18]',
            'password' => 'required|min_length[8]'
        ];

        $errors = $this->validate($rules);
        if ($errors !== true) {
            return $this->respondError('Validasi gagal', $errors, 422);
        }

        $nipNik = $this->request->getPost('nip_nik');
        $password = $this->request->getPost('password');
        $rememberMe = $this->request->getPost('remember_me') == 'true';

        // Get user
        $user = $this->userModel->where('nip_nik', $nipNik)->first();

        if (!$user) {
            $this->logActivity('LOGIN_FAILED', "Failed login attempt for NIP/NIK: {$nipNik}");
            return $this->respondError('NIP/NIK tidak ditemukan', null, 401);
        }

        // Check if blocked
        if ($user['is_blocked']) {
            $this->logActivity('LOGIN_BLOCKED', "Blocked user tried to login: {$nipNik}");
            return $this->respondError('Akun Anda diblokir. Hubungi administrator.', null, 403);
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            // Increment login attempts
            $this->incrementLoginAttempts($user['id']);
            $this->logActivity('LOGIN_FAILED', "Wrong password for NIP/NIK: {$nipNik}");
            return $this->respondError('Password salah', null, 401);
        }

        // Reset login attempts
        $this->userModel->update($user['id'], ['login_attempts' => 0]);

        // Set session
        $this->setUserSession($user, $rememberMe);

        // Log successful login
        $this->logActivity('LOGIN_SUCCESS', "User logged in: {$user['nama']}");

        return $this->respondSuccess('Login berhasil', [
            'user' => [
                'id' => $user['id'],
                'nip_nik' => $user['nip_nik'],
                'nama' => $user['nama'],
                'role' => $user['role'],
                'bidang_id' => $user['bidang_id'],
                'foto' => $user['foto']
            ],
            'redirect' => $this->getRedirectByRole($user['role'])
        ]);
    }

    /**
     * Process logout
     */
    public function logout()
    {
        $user = $this->getCurrentUser();
        
        if ($user) {
            $this->logActivity('LOGOUT', "User logged out: {$user['nama']}");
        }

        $this->session->destroy();
        
        return redirect()->to('/login')->with('success', 'Logout berhasil');
    }

    /**
     * Check session
     */
    public function checkSession()
    {
        if ($this->session->has('user_data')) {
            return $this->respondSuccess('Authenticated', [
                'authenticated' => true,
                'user' => $this->getCurrentUser()
            ]);
        }

        return $this->respondError('Not authenticated', null, 401);
    }

    /**
     * Set user session
     */
    protected function setUserSession($user, $rememberMe)
    {
        $sessionData = [
            'user_data' => [
                'id' => $user['id'],
                'nip_nik' => $user['nip_nik'],
                'nama' => $user['nama'],
                'email' => $user['email'],
                'role' => $user['role'],
                'bidang_id' => $user['bidang_id'],
                'foto' => $user['foto'],
                'is_logged_in' => true
            ]
        ];

        $this->session->set($sessionData);

        // Update last login
        $this->userModel->update($user['id'], [
            'last_login' => date('Y-m-d H:i:s')
        ]);

        // Set remember me cookie if requested
        if ($rememberMe) {
            // Token-based remember me implementation here
            // Store token in database and set cookie
        }
    }

    /**
     * Increment login attempts and auto-block if needed
     */
    protected function incrementLoginAttempts($userId)
    {
        $user = $this->userModel->find($userId);
        $attempts = $user['login_attempts'] + 1;

        $updateData = ['login_attempts' => $attempts];

        // Auto-block after 3 attempts
        if ($attempts >= 3) {
            $updateData['is_blocked'] = 1;
            $updateData['blocked_reason'] = 'Auto-blocked: 3x failed login attempts';
            $updateData['blocked_at'] = date('Y-m-d H:i:s');
            
            $this->logActivity('AUTO_BLOCKED', "User auto-blocked after 3 failed attempts: {$user['nip_nik']}");
        }

        $this->userModel->update($userId, $updateData);
    }

    /**
     * Get redirect URL by role
     */
    protected function getRedirectByRole($role)
    {
        $redirects = [
            'superadmin' => '/superadmin/dashboard',
            'kepaladinas' => '/kepaladinas/dashboard',
            'kepalabidang' => '/kepalabidang/dashboard',
            'pegawai' => '/pegawai/dashboard',
            'keuangan' => '/keuangan/dashboard'
        ];

        return $redirects[$role] ?? '/';
    }
}