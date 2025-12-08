<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Libraries\Auth\AuthService;
use App\Libraries\Logger\ActivityLogger;

/**
 * Auth Controller (Updated)
 * 
 * Now uses AuthService and ActivityLogger for better separation of concerns
 */
class AuthController extends BaseController
{
    protected $authService;
    protected $activityLogger;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->activityLogger = new ActivityLogger();
    }

    /**
     * Display login page
     */
    public function login()
    {
        // Redirect if already logged in
        if ($this->authService->check()) {
            return redirect_by_role();
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

        $valid = $this->validate($rules);
        if ($valid !== true) {
            return $this->respondError('Validasi gagal', $this->getValidationErrors(), 422);
        }

        $nipNik = $this->request->getPost('nip_nik');
        $password = $this->request->getPost('password');
        $rememberMe = $this->request->getPost('remember_me') == 'true';

        // Use AuthService for authentication
        $result = $this->authService->attempt($nipNik, $password, $rememberMe);

        if (!$result['success']) {
            // Log failed attempt
            $this->activityLogger->logLogin(null, false, $nipNik, $result['code']);

            return $this->respondError($result['message'], null, 401);
        }

        // Log successful login (already done in AuthService, but we can add extra context)
        $this->activityLogger->log(
            'LOGIN_SUCCESS',
            "User logged in successfully via web interface",
            $result['user']['id'],
            ['interface' => 'web', 'remember_me' => $rememberMe]
        );

        return $this->respondSuccess($result['message'], [
            'user' => $result['user'],
            'redirect' => $result['redirect']
        ]);
    }

    /**
     * Process logout
     */
    public function logout()
    {
        $user = $this->authService->getCurrentUser();

        if ($user) {
            $this->activityLogger->logLogout($user['id']);
        }

        $this->authService->logout();

        return redirect()->to('/login')->with('success', 'Logout berhasil');
    }

    /**
     * Check session
     */
    public function checkSession()
    {
        if ($this->authService->check()) {
            // Validate session timeout (60 minutes)
            if (!$this->authService->validateSessionTimeout(60)) {
                $this->activityLogger->log(
                    'SESSION_TIMEOUT',
                    'Session expired due to inactivity',
                    null
                );

                return $this->respondError('Session expired', null, 401);
            }

            return $this->respondSuccess('Authenticated', [
                'authenticated' => true,
                'user' => $this->authService->getCurrentUser()
            ]);
        }

        return $this->respondError('Not authenticated', null, 401);
    }

    /**
     * Refresh session data
     */
    public function refreshSession()
    {
        $userId = user_id();

        if (!$userId) {
            return $this->respondError('Not authenticated', null, 401);
        }

        if ($this->authService->refreshSession($userId)) {
            $this->activityLogger->log(
                'SESSION_REFRESH',
                'User session data refreshed',
                $userId
            );

            return $this->respondSuccess('Session refreshed', [
                'user' => $this->authService->getCurrentUser()
            ]);
        }

        return $this->respondError('Failed to refresh session', null, 500);
    }
}
