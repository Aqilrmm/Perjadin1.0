<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\Dashboard\DashboardModel;

class DashboardController extends BaseController
{
    protected $dashboardModel;

    public function __construct()
    {
        $this->dashboardModel = new DashboardModel();
    }

    /**
     * Display super admin dashboard
     */
    public function index()
    {
        $data = [
            'title' => 'Dashboard Super Admin',
            'statistics' => $this->dashboardModel->getStatistics(),
            'recent_users' => $this->dashboardModel->getRecentUsers(),
            'recent_activities' => $this->dashboardModel->getRecentActivities(),
            'sppd_by_bidang' => $this->dashboardModel->getSppdByBidang(),
            'sppd_trend' => $this->dashboardModel->getSppdTrend(),
        ];
        
        return view('superadmin/dashboard', $data);
    }

    /**
     * Get chart data (AJAX)
     */
    public function getChartData()
    {
        $type = $this->request->getGet('type');
        switch ($type) {
            case 'sppd_trend':
                $data = $this->dashboardModel->getSppdTrend();
                break;

            case 'sppd_by_bidang':
                $data = $this->dashboardModel->getSppdByBidang();
                break;

            case 'sppd_by_status':
                $data = $this->dashboardModel->getSppdByStatus();
                break;

            default:
                $data = [];
        }

        return $this->respondSuccess('Data retrieved', $data);
    }
}
