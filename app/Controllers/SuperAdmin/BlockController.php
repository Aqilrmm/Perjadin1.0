<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\User\UserModel;

class BlockController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Display blocked users page
     */
    public function index()
    {
        $data = [
            'title' => 'User yang Diblokir',
        ];

        return view('superadmin/blocked/index', $data);
    }

    /**
     * Get blocked users data for DataTables (AJAX)
     */
    public function datatable()
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $request = $this->request->getPost();
        
        $draw = $request['draw'];
        $start = $request['start'];
        $length = $request['length'];
        $searchValue = $request['search']['value'] ?? '';

        // Build query for blocked users
        $builder = $this->userModel->select('users.*, 
                                              bidang.nama_bidang, 
                                              blocker.nama as blocked_by_name')
                                   ->join('bidang', 'bidang.id = users.bidang_id', 'left')
                                   ->join('users as blocker', 'blocker.id = users.blocked_by', 'left')
                                   ->where('users.is_blocked', 1)
                                   ->where('users.deleted_at', null);

        // Apply search
        if ($searchValue) {
            $builder->groupStart()
                    ->like('users.nip_nik', $searchValue)
                    ->orLike('users.nama', $searchValue)
                    ->orLike('users.email', $searchValue)
                    ->groupEnd();
        }

        // Count filtered records
        $totalFiltered = $builder->countAllResults(false);
        $totalRecords = $this->userModel->where('is_blocked', 1)->countAllResults();

        // Get data
        $data = $builder->orderBy('users.blocked_at', 'DESC')
                        ->limit($length, $start)
                        ->get()
                        ->getResult();

        // Format data for display
        foreach ($data as $key => $row) {
            $data[$key]->nama_lengkap = get_nama_lengkap($row);
            $data[$key]->role_badge = get_role_badge($row->role);
            $data[$key]->blocked_at_formatted = format_tanggal_waktu($row->blocked_at);
            $data[$key]->action = $this->getActionButtons($row->id);
        }

        return $this->respond([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }

    /**
     * Get blocked user detail
     */
    public function detail($id)
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $user = $this->userModel->select('users.*, 
                                          bidang.nama_bidang, 
                                          blocker.nama as blocked_by_name,
                                          blocker.nip_nik as blocked_by_nip')
                                ->join('bidang', 'bidang.id = users.bidang_id', 'left')
                                ->join('users as blocker', 'blocker.id = users.blocked_by', 'left')
                                ->where('users.id', $id)
                                ->first();

        if (!$user) {
            return $this->respondError('User tidak ditemukan', null, 404);
        }

        // Get user's recent activities
        $logModel = new \App\Models\Log\SecurityLogModel();
        $recentActivities = $logModel->getByUser($id, 20);

        return $this->respondSuccess('User detail', [
            'user' => $user,
            'recent_activities' => $recentActivities,
        ]);
    }

    /**
     * Block user
     */
    public function block()
    {
        $userId = $this->request->getPost('user_id');
        $reason = $this->request->getPost('reason');

        if (!$userId) {
            return $this->respondError('User ID required', null, 400);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->respondError('User tidak ditemukan', null, 404);
        }

        // Prevent blocking self
        if ($userId == user_id()) {
            return $this->respondError('Tidak dapat memblokir akun sendiri', null, 400);
        }

        // Validate reason
        if (!$reason || strlen($reason) < 10) {
            return $this->respondError('Alasan blokir wajib diisi minimal 10 karakter', null, 422);
        }

        if ($this->userModel->blockUser($userId, $reason, user_id())) {
            $this->logActivity('BLOCK_USER', "Blocked user: {$user['nama']} (ID: {$userId}). Reason: {$reason}");

            // Send notification to blocked user
            send_notification(
                $userId,
                'warning',
                'Akun Anda Diblokir',
                "Akun Anda telah diblokir oleh administrator. Alasan: {$reason}. Hubungi administrator untuk informasi lebih lanjut.",
                null
            );

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

        if (!$user['is_blocked']) {
            return $this->respondError('User tidak dalam status blocked', null, 400);
        }

        if ($this->userModel->unblockUser($id)) {
            $this->logActivity('UNBLOCK_USER', "Unblocked user: {$user['nama']} (ID: {$id})");

            // Send notification to user
            send_notification(
                $id,
                'info',
                'Akun Anda Telah Dibuka',
                'Akun Anda telah dibuka kembali oleh administrator. Anda dapat login kembali ke sistem.',
                '/login'
            );

            return $this->respondSuccess('User berhasil di-unblock');
        }

        return $this->respondError('Gagal meng-unblock user', null, 500);
    }

    /**
     * Bulk unblock users
     */
    public function bulkUnblock()
    {
        $userIds = $this->request->getPost('user_ids');

        if (!$userIds || !is_array($userIds)) {
            return $this->respondError('User IDs required', null, 400);
        }

        $successCount = 0;
        $failedCount = 0;

        foreach ($userIds as $userId) {
            $user = $this->userModel->find($userId);
            
            if (!$user || !$user['is_blocked']) {
                $failedCount++;
                continue;
            }

            if ($this->userModel->unblockUser($userId)) {
                $successCount++;
                
                // Send notification
                send_notification(
                    $userId,
                    'info',
                    'Akun Anda Telah Dibuka',
                    'Akun Anda telah dibuka kembali oleh administrator.',
                    '/login'
                );
            } else {
                $failedCount++;
            }
        }

        $this->logActivity('BULK_UNBLOCK', "Bulk unblocked {$successCount} users. Failed: {$failedCount}");

        return $this->respondSuccess("Berhasil unblock {$successCount} user. Gagal: {$failedCount}");
    }

    /**
     * Get block history for user
     */
    public function history($userId)
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $logModel = new \App\Models\Log\SecurityLogModel();
        
        $history = $logModel->select('security_logs.*, blocker.nama as blocker_name')
                            ->join('users as blocker', 'blocker.id = security_logs.user_id', 'left')
                            ->where('security_logs.user_id', $userId)
                            ->whereIn('security_logs.action', ['BLOCK_USER', 'UNBLOCK_USER', 'AUTO_BLOCKED'])
                            ->orWhere('security_logs.description LIKE', "%user_id:{$userId}%")
                            ->orderBy('security_logs.created_at', 'DESC')
                            ->limit(50)
                            ->get()
                            ->getResult();

        return $this->respondSuccess('Block history', ['history' => $history]);
    }

    /**
     * Get statistics
     */
    public function statistics()
    {
        $totalBlocked = $this->userModel->where('is_blocked', 1)->countAllResults();
        $autoBlocked = $this->userModel->where('is_blocked', 1)
                                       ->like('blocked_reason', 'Auto-blocked')
                                       ->countAllResults();
        $manualBlocked = $totalBlocked - $autoBlocked;

        // Blocked by bidang
        $blockedByBidang = $this->userModel->select('bidang.nama_bidang, COUNT(users.id) as total')
                                           ->join('bidang', 'bidang.id = users.bidang_id', 'left')
                                           ->where('users.is_blocked', 1)
                                           ->groupBy('users.bidang_id')
                                           ->get()
                                           ->getResult();

        // Blocked by role
        $blockedByRole = $this->userModel->select('role, COUNT(*) as total')
                                         ->where('is_blocked', 1)
                                         ->groupBy('role')
                                         ->get()
                                         ->getResult();

        // Recent blocks (last 30 days)
        $recentBlocks = $this->userModel->where('is_blocked', 1)
                                        ->where('blocked_at >=', date('Y-m-d', strtotime('-30 days')))
                                        ->countAllResults();

        return $this->respondSuccess('Statistics', [
            'total_blocked' => $totalBlocked,
            'auto_blocked' => $autoBlocked,
            'manual_blocked' => $manualBlocked,
            'blocked_by_bidang' => $blockedByBidang,
            'blocked_by_role' => $blockedByRole,
            'recent_blocks' => $recentBlocks,
        ]);
    }

    /**
     * Get action buttons for DataTable
     */
    protected function getActionButtons($userId)
    {
        $buttons = '<div class="flex gap-2">';
        
        $buttons .= '<button class="btn-detail text-blue-600 hover:text-blue-800" data-id="'.$userId.'" title="Detail">
                        <i class="fas fa-eye"></i>
                    </button>';
        
        $buttons .= '<button class="btn-unblock text-green-600 hover:text-green-800" data-id="'.$userId.'" title="Unblock">
                        <i class="fas fa-unlock"></i>
                    </button>';
        
        $buttons .= '<button class="btn-history text-purple-600 hover:text-purple-800" data-id="'.$userId.'" title="History">
                        <i class="fas fa-history"></i>
                    </button>';
        
        $buttons .= '</div>';
        
        return $buttons;
    }
}