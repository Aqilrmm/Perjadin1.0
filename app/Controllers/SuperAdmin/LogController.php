<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\Log\SecurityLogModel;
use App\Models\User\UserModel;

class LogController extends BaseController
{
    protected $logModel;
    protected $userModel;

    public function __construct()
    {
        $this->logModel = new SecurityLogModel();
        $this->userModel = new UserModel();
    }

    /**
     * Display security logs page
     */
    public function index()
    {
        $data = [
            'title' => 'Security Logs',
            'users' => $this->userModel->select('id, nama, nip_nik')->findAll(),
            'action_types' => $this->getActionTypes(),
        ];

        return view('superadmin/logs/index', $data);
    }

    /**
     * Get logs data for DataTables (AJAX)
     */
    public function datatable()
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $request = $this->request->getPost();
        $data = $this->logModel->getDatatablesData($request);

        // Format data for display
        foreach ($data['data'] as $key => $row) {
            $data['data'][$key]->created_at_formatted = format_tanggal_waktu($row->created_at);
            $data['data'][$key]->user_display = $row->nama ? 
                get_nama_lengkap($row) . ' (' . $row->nip_nik . ')' : 
                'System';
            $data['data'][$key]->action_badge = $this->getActionBadge($row->action);
        }

        return $this->respond($data);
    }

    /**
     * Get log detail
     */
    public function detail($id)
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $log = $this->logModel->select('security_logs.*, users.nama, users.nip_nik')
                              ->join('users', 'users.id = security_logs.user_id', 'left')
                              ->where('security_logs.id', $id)
                              ->first();

        if (!$log) {
            return $this->respondError('Log tidak ditemukan', null, 404);
        }

        return $this->respondSuccess('Log detail', $log);
    }

    /**
     * Get statistics
     */
    public function statistics()
    {
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-d', strtotime('-30 days'));
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-d');

        $stats = $this->logModel->getStatistics($startDate, $endDate);

        // Get login activity trend (last 7 days)
        $loginTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            
            $successCount = $this->logModel->where('action', 'LOGIN_SUCCESS')
                                           ->where('DATE(created_at)', $date)
                                           ->countAllResults();
            
            $failedCount = $this->logModel->where('action', 'LOGIN_FAILED')
                                          ->where('DATE(created_at)', $date)
                                          ->countAllResults();
            
            $loginTrend[] = [
                'date' => date('d M', strtotime($date)),
                'success' => $successCount,
                'failed' => $failedCount,
            ];
        }

        // Get top users by activity
        $topUsers = $this->logModel->select('user_id, users.nama, COUNT(*) as total_activity')
                                   ->join('users', 'users.id = security_logs.user_id', 'left')
                                   ->where('security_logs.created_at >=', $startDate)
                                   ->where('security_logs.created_at <=', $endDate . ' 23:59:59')
                                   ->groupBy('user_id')
                                   ->orderBy('total_activity', 'DESC')
                                   ->limit(10)
                                   ->get()
                                   ->getResult();

        // Get action distribution
        $actionDistribution = $this->logModel->select('action, COUNT(*) as count')
                                             ->where('created_at >=', $startDate)
                                             ->where('created_at <=', $endDate . ' 23:59:59')
                                             ->groupBy('action')
                                             ->orderBy('count', 'DESC')
                                             ->limit(10)
                                             ->get()
                                             ->getResult();

        return $this->respondSuccess('Statistics', [
            'summary' => $stats,
            'login_trend' => $loginTrend,
            'top_users' => $topUsers,
            'action_distribution' => $actionDistribution,
        ]);
    }

    /**
     * Export logs
     */
    public function export()
    {
        $format = $this->request->getGet('format') ?: 'csv';
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $userId = $this->request->getGet('user_id');
        $action = $this->request->getGet('action');

        $builder = $this->logModel->select('security_logs.*, users.nama, users.nip_nik')
                                  ->join('users', 'users.id = security_logs.user_id', 'left');

        if ($startDate) {
            $builder->where('security_logs.created_at >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('security_logs.created_at <=', $endDate . ' 23:59:59');
        }
        
        if ($userId) {
            $builder->where('security_logs.user_id', $userId);
        }
        
        if ($action) {
            $builder->where('security_logs.action', $action);
        }

        $logs = $builder->orderBy('security_logs.created_at', 'DESC')
                       ->limit(10000) // Limit to prevent memory issues
                       ->get()
                       ->getResult();

        if ($format === 'csv') {
            return $this->exportCSV($logs);
        } elseif ($format === 'excel') {
            return $this->exportExcel($logs);
        }

        return $this->respondError('Invalid format', null, 400);
    }

    /**
     * Export to CSV
     */
    protected function exportCSV($logs)
    {
        $filename = 'security_logs_' . date('Y-m-d_His') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV Header
        fputcsv($output, ['ID', 'User', 'NIP/NIK', 'Action', 'Description', 'IP Address', 'User Agent', 'Timestamp']);
        
        // CSV Data
        foreach ($logs as $log) {
            fputcsv($output, [
                $log->id,
                $log->nama ?: 'System',
                $log->nip_nik ?: '-',
                $log->action,
                $log->description,
                $log->ip_address,
                $log->user_agent,
                $log->created_at,
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Export to Excel (placeholder - requires PhpSpreadsheet)
     */
    protected function exportExcel($logs)
    {
        // TODO: Implement Excel export using PhpSpreadsheet
        return $this->respondError('Excel export not implemented yet', null, 501);
    }

    /**
     * Clean old logs
     */
    public function cleanOldLogs()
    {
        $days = $this->request->getPost('days') ?: 365;

        $deleted = $this->logModel->cleanOldLogs($days);

        $this->logActivity('CLEAN_LOGS', "Cleaned logs older than {$days} days. Deleted: {$deleted} records");

        return $this->respondSuccess("Berhasil menghapus {$deleted} log lama");
    }

    /**
     * Get action types
     */
    protected function getActionTypes()
    {
        return [
            'LOGIN_SUCCESS',
            'LOGIN_FAILED',
            'LOGIN_BLOCKED',
            'LOGOUT',
            'AUTO_BLOCKED',
            'CREATE_USER',
            'UPDATE_USER',
            'DELETE_USER',
            'BLOCK_USER',
            'UNBLOCK_USER',
            'CREATE_BIDANG',
            'UPDATE_BIDANG',
            'DELETE_BIDANG',
            'CREATE_PROGRAM',
            'UPDATE_PROGRAM',
            'DELETE_PROGRAM',
            'SUBMIT_PROGRAM',
            'APPROVE_PROGRAM',
            'REJECT_PROGRAM',
            'CREATE_KEGIATAN',
            'UPDATE_KEGIATAN',
            'DELETE_KEGIATAN',
            'CREATE_SUBKEGIATAN',
            'UPDATE_SUBKEGIATAN',
            'DELETE_SUBKEGIATAN',
            'CREATE_SPPD',
            'UPDATE_SPPD',
            'DELETE_SPPD',
            'SUBMIT_SPPD',
            'APPROVE_SPPD',
            'REJECT_SPPD',
            'VERIFY_SPPD',
            'SUBMIT_LPPD',
            'SUBMIT_KWITANSI',
            'UPDATE_PROFILE',
            'CHANGE_PASSWORD',
        ];
    }

    /**
     * Get action badge
     */
    protected function getActionBadge($action)
    {
        $badges = [
            'LOGIN_SUCCESS' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Login Success</span>',
            'LOGIN_FAILED' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Login Failed</span>',
            'LOGOUT' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Logout</span>',
            'AUTO_BLOCKED' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Auto Blocked</span>',
        ];

        if (strpos($action, 'CREATE') !== false) {
            return '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">' . str_replace('_', ' ', $action) . '</span>';
        } elseif (strpos($action, 'UPDATE') !== false) {
            return '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">' . str_replace('_', ' ', $action) . '</span>';
        } elseif (strpos($action, 'DELETE') !== false) {
            return '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">' . str_replace('_', ' ', $action) . '</span>';
        } elseif (strpos($action, 'APPROVE') !== false) {
            return '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">' . str_replace('_', ' ', $action) . '</span>';
        }

        return $badges[$action] ?? '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">' . str_replace('_', ' ', $action) . '</span>';
    }
}