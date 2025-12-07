<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Role-Based Access Control Filter
 * 
 * Checks if user has required role to access route
 */
class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = \Config\Services::session();

        // Check if user is logged in
        if (!$session->has('user_data')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // If no specific role required, allow access
        if (!$arguments) {
            return null;
        }

        $user = $session->get('user_data');
        $userRole = $user['role'] ?? null;
        
        // Check if user has required role
        $requiredRoles = is_array($arguments) ? $arguments : [$arguments];
        
        if (!in_array($userRole, $requiredRoles)) {
            // Log unauthorized access attempt
            $this->logUnauthorizedAccess($user, $request->getUri()->getPath());
            
            return redirect()->to('/')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }

    /**
     * Log unauthorized access attempt
     */
    protected function logUnauthorizedAccess($user, $path)
    {
        $logModel = new \App\Models\Log\SecurityLogModel();
        $logModel->logActivity(
            $user['id'] ?? null,
            'UNAUTHORIZED_ACCESS',
            "Attempted to access: {$path}",
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        );
    }
}