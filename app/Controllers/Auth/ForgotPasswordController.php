<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\User\UserModel;
use App\Models\Log\SecurityLogModel;

class ForgotPasswordController extends BaseController
{
    protected $userModel;
    protected $logModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->logModel = new SecurityLogModel();
    }

    public function index()
    {
        return view('auth/forgot_password');
    }

    public function sendResetLink()
    {
        $rules = [
            'email' => 'required|valid_email',
        ];

        $valid = $this->validate($rules);
        if ($valid !== true) {
            return $this->respondError('Email tidak valid', $this->getValidationErrors(), 422);
        }

        $email = $this->request->getPost('email');
        $user = $this->userModel->where('email', $email)
            ->where('deleted_at', null)
            ->first();

        if (!$user) {
            $this->logModel->logActivity(
                null,
                'RESET_PASSWORD_ATTEMPT',
                "Reset password attempted for non-existent email: {$email}",
                $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            );

            return $this->respondSuccess('Jika email terdaftar, link reset password telah dikirim');
        }

        if ($user['is_blocked']) {
            $this->logModel->logActivity(
                $user['id'],
                'RESET_PASSWORD_BLOCKED',
                "Blocked user attempted password reset: {$email}",
                $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            );

            return $this->respondSuccess('Jika email terdaftar, link reset password telah dikirim');
        }

        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $db = \Config\Database::connect();
        $db->table('password_resets')->insert([
            'user_id' => $user['id'],
            'email' => $email,
            'token' => hash('sha256', $token),
            'expires_at' => $expiry,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $resetLink = base_url("auth/reset-password/{$token}");
        $this->sendResetEmail($email, $user['nama'] ?? $user['email'], $resetLink);

        $this->logModel->logActivity(
            $user['id'],
            'RESET_PASSWORD_REQUESTED',
            "Password reset requested for: {$email}",
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        );

        return $this->respondSuccess('Jika email terdaftar, link reset password telah dikirim');
    }

    public function resetPassword($token)
    {
        $hashedToken = hash('sha256', $token);
        $db = \Config\Database::connect();
        $reset = $db->table('password_resets')
            ->where('token', $hashedToken)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->where('used_at', null)
            ->get()
            ->getRow();

        if (!$reset) {
            return redirect()->to('/auth/forgot-password')
                ->with('error', 'Link reset password tidak valid atau sudah kadaluarsa');
        }

        return view('auth/reset_password', ['title' => 'Reset Password', 'token' => $token]);
    }

    public function processResetPassword()
    {
        $rules = [
            'token' => 'required',
            'password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[password]',
        ];

        $valid = $this->validate($rules);
        if ($valid !== true) {
            return $this->respondError('Validasi gagal', $this->getValidationErrors(), 422);
        }

        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');
        $hashedToken = hash('sha256', $token);

        $db = \Config\Database::connect();
        $reset = $db->table('password_resets')
            ->where('token', $hashedToken)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->where('used_at', null)
            ->get()
            ->getRow();

        if (!$reset) {
            return $this->respondError('Link reset password tidak valid atau sudah kadaluarsa', null, 400);
        }

        $this->userModel->update($reset->user_id, [
            'password' => $password,
            'login_attempts' => 0
        ]);

        $db->table('password_resets')
            ->where('token', $hashedToken)
            ->update(['used_at' => date('Y-m-d H:i:s')]);

        if ($this->userModel->find($reset->user_id)['is_blocked']) {
            $this->userModel->unblockUser($reset->user_id);
        }

        $this->logModel->logActivity(
            $reset->user_id,
            'PASSWORD_RESET',
            "Password reset completed for user ID: {$reset->user_id}",
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        );

        return $this->respondSuccess('Password berhasil direset. Silakan login dengan password baru');
    }

    protected function sendResetEmail($email, $name, $resetLink)
    {
        $emailService = \Config\Services::email();

        $emailService->setFrom('noreply@perjadin.com', 'Aplikasi Perjadin');
        $emailService->setTo($email);
        $emailService->setSubject('Reset Password - Aplikasi Perjadin');

        $message = view('emails/reset_password', [
            'name' => $name,
            'reset_link' => $resetLink,
            'expiry' => '1 jam'
        ]);

        $emailService->setMessage($message);

        return $emailService->send();
    }

    public function cleanExpiredTokens()
    {
        $db = \Config\Database::connect();

        $deleted = $db->table('password_resets')
            ->where('expires_at <', date('Y-m-d H:i:s'))
            ->orWhere('created_at <', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->delete();

        return $this->respondSuccess("Cleaned {$deleted} expired tokens");
    }
}
