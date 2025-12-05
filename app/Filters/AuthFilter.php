<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = \Config\Services::session();

        // Check if user is logged in
        if (!$session->has('user_data')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Check role if specified
        if ($arguments) {
            $user = $session->get('user_data');
            $requiredRole = $arguments[0];
            
            if ($user['role'] !== $requiredRole) {
                return redirect()->to('/')->with('error', 'Anda tidak memiliki akses ke halaman ini');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}