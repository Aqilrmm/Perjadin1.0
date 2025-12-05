<?php

namespace App\Libraries\Auth;

use App\Models\User\UserModel;
use App\Models\Log\SecurityLogModel;

/**
 * Authentication Service
 * 
 * Handles all authentication related operations
 */
class AuthService
{
    protected $userModel;
    protected $logModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->logModel = new SecurityLogModel();
        $this->session = \Config\Services::session();
    }

    /**
     * Attempt login
     *
     * @param string $nipNik
     * @param string $password
     * @param bool $rememberMe
     * @return array
     */
    public function attempt($nipNik, $password, $rememberMe = false)
    {
        // Get user by NIP/NIK
        $user = $this->userModel->where('nip_nik', $nipNik)
                                ->where('deleted_at', null)
                                ->first();

        if (!$user) {
            $this->logFailedAttempt($nipNik, null);
            return [
                'success' => false,
                'message' => 'NIP/NIK tidak ditemukan',
                'code' => 'USER_NOT_FOUND'
            ];
        }

        // Check if blocked
        if ($user['is_blocked']) {
            $this->logFailedAttempt($nipNik, $user['id'], 'BLOCKED_USER_ATTEMPT');
            return [
                'success' => false,
                'message' => 'Akun Anda diblokir. Hubungi administrator.',
                'code' => 'USER_BLOCKED'
            ];
        }

        // Check if active
        if (!$user['is_active']) {
            $this->logFailedAttempt($nipNik, $user['id'], 'INACTIVE_USER_ATTEMPT');
            return [
                'success' => false,
                'message' => 'Akun Anda tidak aktif. Hubungi administrator.',
                'code' => 'USER_INACTIVE'
            ];
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            $this->incrementLoginAttempts($user['id']);
            $this->logFailedAttempt($nipNik, $user['id'], 'WRONG_PASSWORD');
            return [
                'success' => false,
                'message' => 'Password salah',
                'code' => 'WRONG_PASSWORD'
            ];
        }

        // Reset login attempts
        $this->userModel->update($user['id'], ['login_attempts' => 0]);

        // Set session
        $this->setUserSession($user, $rememberMe);

        // Update last login
        $this->userModel->update($user['id'], [
            'last_login' => date('Y-m-d H:i:s')
        ]);

        // Log successful login
        $this->logSuccessfulLogin($user['id'], $user['nama']);

        return [
            'success' => true,
            'message' => 'Login berhasil',
            'user' => $this->getUserData($user),
            'redirect' => $this->getRedirectByRole($user['role'])
        ];
    }

    /**
     * Logout user
     *
     * @return array
     */
    public function logout()
    {
        $user = $this->getCurrentUser();
        
        if ($user) {
            $this->logModel->logActivity(
                $user['id'],
                'LOGOUT',
                "User logged out: {$user['nama']}",
                $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            );
        }

        $this->session->destroy();

        return [
            'success' => true,
            'message' => 'Logout berhasil'
        ];
    }

    /**
     * Check if user is authenticated
     *
     * @return bool
     */
    public function check()
    {
        return $this->session->has('user_data');
    }

    /**
     * Get current authenticated user
     *
     * @return array|null
     */
    public function getCurrentUser()
    {
        return $this->session->get('user_data');
    }

    /**
     * Get user by ID
     *
     * @param int $userId
     * @return array|null
     */
    public function getUserById($userId)
    {
        return $this->userModel->find($userId);
    }

    /**
     * Refresh user session data
     *
     * @param int $userId
     * @return bool
     */
    public function refreshSession($userId)
    {
        $user = $this->userModel->find($userId);
        
        if (!$user) {
            return false;
        }

        $this->setUserSession($user, false);
        return true;
    }

    /**
     * Set user session
     *
     * @param array $user
     * @param bool $rememberMe
     * @return void
     */
    protected function setUserSession($user, $rememberMe)
    {
        $sessionData = [
            'user_data' => [
                'id' => $user['id'],
                'uuid' => $user['uuid'],
                'nip_nik' => $user['nip_nik'],
                'nama' => $user['nama'],
                'email' => $user['email'],
                'role' => $user['role'],
                'bidang_id' => $user['bidang_id'],
                'jabatan' => $user['jabatan'],
                'foto' => $user['foto'],
                'is_logged_in' => true,
                'login_time' => time()
            ]
        ];

        $this->session->set($sessionData);

        if ($rememberMe) {
            $this->setRememberMeCookie($user['id']);
        }
    }

    /**
     * Set remember me cookie
     *
     * @param int $userId
     * @return void
     */
    protected function setRememberMeCookie($userId)
    {
        $token = bin2hex(random_bytes(32));
        $expiry = time() + (30 * 24 * 60 * 60); // 30 days

        // Store token in database (you need to create remember_tokens table)
        // For now, just set cookie
        setcookie('remember_token', $token, $expiry, '/', '', true, true);
    }

    /**
     * Increment login attempts and auto-block if needed
     *
     * @param int $userId
     * @return void
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
            
            $this->logModel->logActivity(
                $userId,
                'AUTO_BLOCKED',
                "User auto-blocked after 3 failed attempts: {$user['nip_nik']}",
                $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            );
        }

        $this->userModel->update($userId, $updateData);
    }

    /**
     * Log failed login attempt
     *
     * @param string $nipNik
     * @param int|null $userId
     * @param string $reason
     * @return void
     */
    protected function logFailedAttempt($nipNik, $userId = null, $reason = 'UNKNOWN')
    {
        $this->logModel->logActivity(
            $userId,
            'LOGIN_FAILED',
            "Failed login attempt for NIP/NIK: {$nipNik}. Reason: {$reason}",
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        );
    }

    /**
     * Log successful login
     *
     * @param int $userId
     * @param string $userName
     * @return void
     */
    protected function logSuccessfulLogin($userId, $userName)
    {
        $this->logModel->logActivity(
            $userId,
            'LOGIN_SUCCESS',
            "User logged in: {$userName}",
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        );
    }

    /**
     * Get redirect URL by role
     *
     * @param string $role
     * @return string
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

    /**
     * Get formatted user data
     *
     * @param array $user
     * @return array
     */
    protected function getUserData($user)
    {
        return [
            'id' => $user['id'],
            'uuid' => $user['uuid'],
            'nip_nik' => $user['nip_nik'],
            'nama' => $user['nama'],
            'email' => $user['email'],
            'role' => $user['role'],
            'bidang_id' => $user['bidang_id'],
            'jabatan' => $user['jabatan'],
            'foto' => $user['foto']
        ];
    }

    /**
     * Validate session timeout
     *
     * @param int $timeout Minutes
     * @return bool
     */
    public function validateSessionTimeout($timeout = 60)
    {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            return false;
        }

        $loginTime = $user['login_time'] ?? 0;
        $currentTime = time();
        $elapsed = ($currentTime - $loginTime) / 60; // Convert to minutes

        if ($elapsed > $timeout) {
            $this->logout();
            return false;
        }

        // Update login time
        $user['login_time'] = $currentTime;
        $this->session->set('user_data', $user);

        return true;
    }

    /**
     * Change password
     *
     * @param int $userId
     * @param string $currentPassword
     * @param string $newPassword
     * @return array
     */
    public function changePassword($userId, $currentPassword, $newPassword)
    {
        $user = $this->userModel->find($userId);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User tidak ditemukan'
            ];
        }

        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            return [
                'success' => false,
                'message' => 'Password lama tidak sesuai'
            ];
        }

        // Update password
        if ($this->userModel->update($userId, ['password' => $newPassword])) {
            $this->logModel->logActivity(
                $userId,
                'CHANGE_PASSWORD',
                "User changed password",
                $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            );

            return [
                'success' => true,
                'message' => 'Password berhasil diubah'
            ];
        }

        return [
            'success' => false,
            'message' => 'Gagal mengubah password'
        ];
    }

    /**
     * Reset password
     *
     * @param int $userId
     * @param string $newPassword
     * @return array
     */
    public function resetPassword($userId, $newPassword)
    {
        if ($this->userModel->update($userId, ['password' => $newPassword])) {
            $this->logModel->logActivity(
                $userId,
                'RESET_PASSWORD',
                "Password was reset by administrator",
                $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            );

            return [
                'success' => true,
                'message' => 'Password berhasil direset'
            ];
        }

        return [
            'success' => false,
            'message' => 'Gagal mereset password'
        ];
    }
}