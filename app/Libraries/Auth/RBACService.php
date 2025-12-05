<?php

namespace App\Libraries\Auth;

/**
 * Role-Based Access Control (RBAC) Service
 * 
 * Manages permissions and access control
 */
class RBACService
{
    protected $permissions = [];
    protected $roleHierarchy = [];

    public function __construct()
    {
        $this->initializePermissions();
        $this->initializeRoleHierarchy();
    }

    /**
     * Initialize permissions matrix
     */
    protected function initializePermissions()
    {
        $this->permissions = [
            'superadmin' => ['*'], // All permissions
            
            'kepaladinas' => [
                'view_all_dashboard',
                'view_all_programs',
                'view_all_kegiatan',
                'view_all_subkegiatan',
                'view_all_sppd',
                'approve_program',
                'reject_program',
                'approve_kegiatan',
                'reject_kegiatan',
                'approve_subkegiatan',
                'reject_subkegiatan',
                'approve_sppd',
                'reject_sppd',
                'view_all_analytics',
                'view_all_reports',
            ],
            
            'kepalabidang' => [
                'view_bidang_dashboard',
                'create_program',
                'update_program',
                'delete_program',
                'submit_program',
                'view_own_programs',
                'create_kegiatan',
                'update_kegiatan',
                'delete_kegiatan',
                'submit_kegiatan',
                'view_own_kegiatan',
                'create_subkegiatan',
                'update_subkegiatan',
                'delete_subkegiatan',
                'submit_subkegiatan',
                'view_own_subkegiatan',
                'create_sppd',
                'update_sppd',
                'delete_sppd',
                'submit_sppd',
                'view_own_sppd',
                'view_bidang_analytics',
                'view_bidang_reports',
            ],
            
            'pegawai' => [
                'view_pegawai_dashboard',
                'view_own_sppd',
                'view_nota_dinas',
                'create_lppd',
                'update_lppd',
                'submit_lppd',
                'create_kwitansi',
                'update_kwitansi',
                'submit_kwitansi',
                'upload_dokumentasi',
                'upload_bukti',
            ],
            
            'keuangan' => [
                'view_keuangan_dashboard',
                'view_submitted_sppd',
                'verify_sppd',
                'reject_sppd',
                'view_lppd',
                'view_kwitansi',
                'view_all_bukti',
                'create_laporan',
                'export_laporan',
                'view_keuangan_analytics',
            ],
        ];
    }

    /**
     * Initialize role hierarchy
     */
    protected function initializeRoleHierarchy()
    {
        $this->roleHierarchy = [
            'superadmin' => 5,
            'kepaladinas' => 4,
            'kepalabidang' => 3,
            'keuangan' => 2,
            'pegawai' => 1,
        ];
    }

    /**
     * Check if user has permission
     *
     * @param string $role
     * @param string $permission
     * @return bool
     */
    public function hasPermission($role, $permission)
    {
        if (!isset($this->permissions[$role])) {
            return false;
        }

        // Super admin has all permissions
        if (in_array('*', $this->permissions[$role])) {
            return true;
        }

        return in_array($permission, $this->permissions[$role]);
    }

    /**
     * Check if user has any of the permissions
     *
     * @param string $role
     * @param array $permissions
     * @return bool
     */
    public function hasAnyPermission($role, array $permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($role, $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all permissions
     *
     * @param string $role
     * @param array $permissions
     * @return bool
     */
    public function hasAllPermissions($role, array $permissions)
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($role, $permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if role can access route
     *
     * @param string $role
     * @param string $route
     * @return bool
     */
    public function canAccessRoute($role, $route)
    {
        $routePermissions = $this->getRoutePermissions($route);

        if (empty($routePermissions)) {
            return true; // Public route
        }

        return $this->hasAnyPermission($role, $routePermissions);
    }

    /**
     * Get route permissions
     *
     * @param string $route
     * @return array
     */
    protected function getRoutePermissions($route)
    {
        $routeMap = [
            '/superadmin/*' => ['*'],
            '/kepaladinas/programs/*' => ['view_all_programs', 'approve_program', 'reject_program'],
            '/kepaladinas/sppd/*' => ['view_all_sppd', 'approve_sppd', 'reject_sppd'],
            '/kepalabidang/programs/*' => ['create_program', 'update_program', 'view_own_programs'],
            '/kepalabidang/sppd/*' => ['create_sppd', 'update_sppd', 'view_own_sppd'],
            '/pegawai/lppd/*' => ['create_lppd', 'update_lppd', 'submit_lppd'],
            '/keuangan/verifikasi/*' => ['verify_sppd', 'reject_sppd'],
        ];

        foreach ($routeMap as $pattern => $permissions) {
            if ($this->matchRoute($route, $pattern)) {
                return $permissions;
            }
        }

        return [];
    }

    /**
     * Match route pattern
     *
     * @param string $route
     * @param string $pattern
     * @return bool
     */
    protected function matchRoute($route, $pattern)
    {
        $pattern = str_replace('*', '.*', $pattern);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $route);
    }

    /**
     * Check if role is higher than another role
     *
     * @param string $role1
     * @param string $role2
     * @return bool
     */
    public function isRoleHigherThan($role1, $role2)
    {
        $level1 = $this->roleHierarchy[$role1] ?? 0;
        $level2 = $this->roleHierarchy[$role2] ?? 0;

        return $level1 > $level2;
    }

    /**
     * Get role level
     *
     * @param string $role
     * @return int
     */
    public function getRoleLevel($role)
    {
        return $this->roleHierarchy[$role] ?? 0;
    }

    /**
     * Get all permissions for role
     *
     * @param string $role
     * @return array
     */
    public function getPermissions($role)
    {
        return $this->permissions[$role] ?? [];
    }

    /**
     * Check if user can manage another user
     *
     * @param string $managerRole
     * @param string $targetRole
     * @return bool
     */
    public function canManageUser($managerRole, $targetRole)
    {
        // Super admin can manage everyone
        if ($managerRole === 'superadmin') {
            return true;
        }

        // Higher roles can manage lower roles
        return $this->isRoleHigherThan($managerRole, $targetRole);
    }

    /**
     * Check if user can approve for bidang
     *
     * @param string $role
     * @param int|null $userBidangId
     * @param int|null $targetBidangId
     * @return bool
     */
    public function canApproveForBidang($role, $userBidangId, $targetBidangId)
    {
        // Kepala Dinas can approve for all bidang
        if ($role === 'kepaladinas') {
            return true;
        }

        // Kepala Bidang can only approve for their own bidang
        if ($role === 'kepalabidang') {
            return $userBidangId === $targetBidangId;
        }

        return false;
    }

    /**
     * Get allowed roles for user creation
     *
     * @param string $creatorRole
     * @return array
     */
    public function getAllowedRolesForCreation($creatorRole)
    {
        if ($creatorRole === 'superadmin') {
            return ['superadmin', 'kepaladinas', 'kepalabidang', 'pegawai', 'keuangan'];
        }

        return [];
    }

    /**
     * Check if can view SPPD
     *
     * @param string $role
     * @param int|null $userBidangId
     * @param int|null $sppdBidangId
     * @param int|null $userId
     * @param array $sppdPegawaiIds
     * @return bool
     */
    public function canViewSppd($role, $userBidangId, $sppdBidangId, $userId, $sppdPegawaiIds)
    {
        // Super admin can view all
        if ($role === 'superadmin') {
            return true;
        }

        // Kepala Dinas can view all
        if ($role === 'kepaladinas') {
            return true;
        }

        // Keuangan can view all submitted SPPD
        if ($role === 'keuangan') {
            return true;
        }

        // Kepala Bidang can view their bidang's SPPD
        if ($role === 'kepalabidang') {
            return $userBidangId === $sppdBidangId;
        }

        // Pegawai can view if they are in the SPPD
        if ($role === 'pegawai') {
            return in_array($userId, $sppdPegawaiIds);
        }

        return false;
    }

    /**
     * Check if can edit SPPD
     *
     * @param string $role
     * @param int $userId
     * @param int $sppdCreatorId
     * @param string $sppdStatus
     * @return bool
     */
    public function canEditSppd($role, $userId, $sppdCreatorId, $sppdStatus)
    {
        // Only draft can be edited
        if ($sppdStatus !== 'draft') {
            return false;
        }

        // Super admin can edit all
        if ($role === 'superadmin') {
            return true;
        }

        // Kepala Bidang can edit their own draft SPPD
        if ($role === 'kepalabidang') {
            return $userId === $sppdCreatorId;
        }

        return false;
    }

    /**
     * Get menu permissions
     *
     * @param string $role
     * @return array
     */
    public function getMenuPermissions($role)
    {
        $menus = [
            'superadmin' => [
                'dashboard', 'users', 'bidang', 'logs', 'blocked_users'
            ],
            'kepaladinas' => [
                'dashboard', 'approval_programs', 'approval_kegiatan', 
                'approval_subkegiatan', 'approval_sppd', 'analytics'
            ],
            'kepalabidang' => [
                'dashboard', 'programs', 'kegiatan', 'subkegiatan', 
                'sppd', 'analytics'
            ],
            'pegawai' => [
                'dashboard', 'my_sppd', 'lppd', 'kwitansi'
            ],
            'keuangan' => [
                'dashboard', 'verifikasi', 'laporan', 'analytics'
            ],
        ];

        return $menus[$role] ?? [];
    }
}