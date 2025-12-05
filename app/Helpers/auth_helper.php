<?php

/**
 * Auth Helper
 * 
 * Helper functions for authentication and authorization
 */

if (!function_exists('is_logged_in')) {
    /**
     * Check if user is logged in
     * 
     * @return bool
     */
    function is_logged_in()
    {
        $session = \Config\Services::session();
        return $session->has('user_data');
    }
}

if (!function_exists('current_user')) {
    /**
     * Get current logged in user data
     * 
     * @return array|null
     */
    function current_user()
    {
        $session = \Config\Services::session();
        return $session->get('user_data');
    }
}

if (!function_exists('user_id')) {
    /**
     * Get current user ID
     * 
     * @return int|null
     */
    function user_id()
    {
        $user = current_user();
        return $user['id'] ?? null;
    }
}

if (!function_exists('user_role')) {
    /**
     * Get current user role
     * 
     * @return string|null
     */
    function user_role()
    {
        $user = current_user();
        return $user['role'] ?? null;
    }
}

if (!function_exists('user_name')) {
    /**
     * Get current user name
     * 
     * @return string|null
     */
    function user_name()
    {
        $user = current_user();
        return $user['nama'] ?? null;
    }
}

if (!function_exists('user_bidang_id')) {
    /**
     * Get current user bidang ID
     * 
     * @return int|null
     */
    function user_bidang_id()
    {
        $user = current_user();
        return $user['bidang_id'] ?? null;
    }
}

if (!function_exists('has_role')) {
    /**
     * Check if user has specific role(s)
     * 
     * @param string|array $roles
     * @return bool
     */
    function has_role($roles)
    {
        $userRole = user_role();
        
        if (!$userRole) {
            return false;
        }
        
        if (is_array($roles)) {
            return in_array($userRole, $roles);
        }
        
        return $userRole === $roles;
    }
}

if (!function_exists('is_superadmin')) {
    /**
     * Check if user is super admin
     * 
     * @return bool
     */
    function is_superadmin()
    {
        return has_role('superadmin');
    }
}

if (!function_exists('is_kepaladinas')) {
    /**
     * Check if user is kepala dinas
     * 
     * @return bool
     */
    function is_kepaladinas()
    {
        return has_role('kepaladinas');
    }
}

if (!function_exists('is_kepalabidang')) {
    /**
     * Check if user is kepala bidang
     * 
     * @return bool
     */
    function is_kepalabidang()
    {
        return has_role('kepalabidang');
    }
}

if (!function_exists('is_pegawai')) {
    /**
     * Check if user is pegawai
     * 
     * @return bool
     */
    function is_pegawai()
    {
        return has_role('pegawai');
    }
}

if (!function_exists('is_keuangan')) {
    /**
     * Check if user is keuangan
     * 
     * @return bool
     */
    function is_keuangan()
    {
        return has_role('keuangan');
    }
}

if (!function_exists('can_access')) {
    /**
     * Check if user can access specific roles
     * 
     * @param string|array $allowedRoles
     * @return bool
     */
    function can_access($allowedRoles)
    {
        return has_role($allowedRoles);
    }
}

if (!function_exists('redirect_by_role')) {
    /**
     * Redirect user based on their role
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    function redirect_by_role()
    {
        $role = user_role();
        
        $redirects = [
            'superadmin' => '/superadmin/dashboard',
            'kepaladinas' => '/kepaladinas/dashboard',
            'kepalabidang' => '/kepalabidang/dashboard',
            'pegawai' => '/pegawai/dashboard',
            'keuangan' => '/keuangan/dashboard'
        ];
        
        $url = $redirects[$role] ?? '/';
        
        return redirect()->to($url);
    }
}

if (!function_exists('get_user_avatar')) {
    /**
     * Get user avatar URL
     * 
     * @param string|null $foto
     * @return string
     */
    function get_user_avatar($foto = null)
    {
        if (!$foto) {
            $user = current_user();
            $foto = $user['foto'] ?? null;
        }
        
        if ($foto && file_exists(FCPATH . 'uploads/foto_profile/' . $foto)) {
            return base_url('uploads/foto_profile/' . $foto);
        }
        
        return base_url('assets/images/default-avatar.png');
    }
}

if (!function_exists('get_nama_lengkap')) {
    /**
     * Get full name with title
     * 
     * @param object|array $user
     * @return string
     */
    function get_nama_lengkap($user)
    {
        if (is_object($user)) {
            $gelarDepan = $user->gelar_depan ?? '';
            $nama = $user->nama ?? '';
            $gelarBelakang = $user->gelar_belakang ?? '';
        } else {
            $gelarDepan = $user['gelar_depan'] ?? '';
            $nama = $user['nama'] ?? '';
            $gelarBelakang = $user['gelar_belakang'] ?? '';
        }
        
        $fullName = trim($gelarDepan . ' ' . $nama . ' ' . $gelarBelakang);
        
        return $fullName;
    }
}

if (!function_exists('check_permission')) {
    /**
     * Check if user has permission for specific action
     * 
     * @param string $permission
     * @return bool
     */
    function check_permission($permission)
    {
        $role = user_role();
        
        // Define permissions per role
        $permissions = [
            'superadmin' => ['*'], // All permissions
            'kepaladinas' => [
                'approve_program',
                'approve_kegiatan',
                'approve_subkegiatan',
                'approve_sppd',
                'view_all_analytics'
            ],
            'kepalabidang' => [
                'create_program',
                'create_kegiatan',
                'create_subkegiatan',
                'create_sppd',
                'view_bidang_analytics'
            ],
            'pegawai' => [
                'view_own_sppd',
                'create_lppd',
                'create_kwitansi'
            ],
            'keuangan' => [
                'verify_sppd',
                'create_laporan',
                'view_keuangan_analytics'
            ]
        ];
        
        if (!isset($permissions[$role])) {
            return false;
        }
        
        // Super admin has all permissions
        if (in_array('*', $permissions[$role])) {
            return true;
        }
        
        return in_array($permission, $permissions[$role]);
    }
}

if (!function_exists('require_permission')) {
    /**
     * Require permission or redirect with error
     * 
     * @param string $permission
     * @return void
     */
    function require_permission($permission)
    {
        if (!check_permission($permission)) {
            session()->setFlashdata('error', 'Anda tidak memiliki izin untuk aksi ini');
            header('Location: ' . previous_url());
            exit;
        }
    }
}

if (!function_exists('get_role_name')) {
    /**
     * Get role display name
     * 
     * @param string $role
     * @return string
     */
    function get_role_name($role)
    {
        $roleNames = [
            'superadmin' => 'Super Admin',
            'kepaladinas' => 'Kepala Dinas',
            'kepalabidang' => 'Kepala Bidang',
            'pegawai' => 'Pegawai',
            'keuangan' => 'Keuangan'
        ];
        
        return $roleNames[$role] ?? ucfirst($role);
    }
}

if (!function_exists('get_role_badge')) {
    /**
     * Get role badge HTML
     * 
     * @param string $role
     * @return string
     */
    function get_role_badge($role)
    {
        $badges = [
            'superadmin' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Super Admin</span>',
            'kepaladinas' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Kepala Dinas</span>',
            'kepalabidang' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Kepala Bidang</span>',
            'pegawai' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Pegawai</span>',
            'keuangan' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Keuangan</span>'
        ];
        
        return $badges[$role] ?? '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">' . ucfirst($role) . '</span>';
    }
}