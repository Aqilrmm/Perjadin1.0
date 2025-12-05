<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\User\UserModel;
use App\Models\Bidang\BidangModel;
use App\Models\Program\ProgramModel;
use App\Models\SPPD\SPPDModel;

class DashboardController extends BaseController
{
    protected $userModel;
    protected $bidangModel;
    protected $programModel;
    protected $sppdModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->bidangModel = new BidangModel();
        $this->programModel = new ProgramModel();
        $this->sppdModel = new SPPDModel();
    }

    /**
     * Display super admin dashboard
     */
    public function index()
    {
        $data = [
            'title' => 'Dashboard Super Admin',
            'statistics' => $this->getStatistics(),
            'recent_users' => $this->getRecentUsers(),
            'recent_activities' => $this->getRecentActivities(),
            'sppd_by_bidang' => $this->getSppdByBidang(),
            'sppd_trend' => $this->getSppdTrend(),
        ];

        return view('superadmin/dashboard', $data);
    }

    /**
     * Get overall statistics
     */
    private function getStatistics()
    {
        $db = \Config\Database::connect();

        return [
            'total_users' => $this->userModel->where('deleted_at', null)->countAllResults(),
            'total_bidang' => $this->bidangModel->where('deleted_at', null)->countAllResults(),
            'total_programs' => $this->programModel->where('deleted_at', null)->countAllResults(),
            'total_sppd' => $this->sppdModel->where('deleted_at', null)->countAllResults(),
            'active_users' => $this->userModel->where('is_active', 1)
                                               ->where('is_blocked', 0)
                                               ->where('deleted_at', null)
                                               ->countAllResults(),
            'blocked_users' => $this->userModel->where('is_blocked', 1)->countAllResults(),
            'pending_programs' => $this->programModel->where('status', 'pending')->countAllResults(),
            'pending_sppd' => $this->sppdModel->where('status', 'pending')->countAllResults(),
        ];
    }

    /**
     * Get recent users (last 10)
     */
    private function getRecentUsers()
    {
        return $this->userModel->select('users.*, bidang.nama_bidang')
                               ->join('bidang', 'bidang.id = users.bidang_id', 'left')
                               ->where('users.deleted_at', null)
                               ->orderBy('users.created_at', 'DESC')
                               ->limit(10)
                               ->findAll();
    }

    /**
     * Get recent activities from security logs
     */
    private function getRecentActivities()
    {
        $db = \Config\Database::connect();
        
        return $db->table('security_logs')
                  ->select('security_logs.*, users.nama, users.nip_nik')
                  ->join('users', 'users.id = security_logs.user_id', 'left')
                  ->orderBy('security_logs.created_at', 'DESC')
                  ->limit(15)
                  ->get()
                  ->getResult();
    }

    /**
     * Get SPPD count by bidang
     */
    private function getSppdByBidang()
    {
        $db = \Config\Database::connect();
        
        return $db->table('sppd')
                  ->select('bidang.nama_bidang, COUNT(sppd.id) as total')
                  ->join('bidang', 'bidang.id = sppd.bidang_id')
                  ->where('sppd.deleted_at', null)
                  ->where('YEAR(sppd.tanggal_berangkat)', date('Y'))
                  ->groupBy('sppd.bidang_id')
                  ->get()
                  ->getResult();
    }

    /**
     * Get SPPD trend (last 6 months)
     */
    private function getSppdTrend()
    {
        $db = \Config\Database::connect();
        
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            
            $count = $db->table('sppd')
                        ->where('deleted_at', null)
                        ->where("DATE_FORMAT(tanggal_berangkat, '%Y-%m')", $month)
                        ->countAllResults();
            
            $data[] = [
                'month' => date('M Y', strtotime($month . '-01')),
                'count' => $count
            ];
        }
        
        return $data;
    }

    /**
     * Get chart data (AJAX)
     */
    public function getChartData()
    {
        $type = $this->request->getGet('type');
        
        switch ($type) {
            case 'sppd_trend':
                $data = $this->getSppdTrend();
                break;
            
            case 'sppd_by_bidang':
                $data = $this->getSppdByBidang();
                break;
            
            case 'sppd_by_status':
                $data = $this->getSppdByStatus();
                break;
            
            default:
                $data = [];
        }
        
        return $this->respondSuccess('Data retrieved', $data);
    }

    /**
     * Get SPPD by status
     */
    private function getSppdByStatus()
    {
        $statuses = ['draft', 'pending', 'approved', 'submitted', 'verified', 'rejected'];
        $data = [];
        
        foreach ($statuses as $status) {
            $count = $this->sppdModel->where('status', $status)
                                     ->where('deleted_at', null)
                                     ->countAllResults();
            
            $data[] = [
                'status' => ucfirst($status),
                'count' => $count
            ];
        }
        
        return $data;
    }
}