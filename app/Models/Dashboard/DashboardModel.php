<?php

namespace App\Models\Dashboard;

use App\Models\BaseModel;

class DashboardModel extends BaseModel
{
    // This model doesn't map to a single table; it's a helper aggregator for dashboard data

    /**
     * Get overall aggregated statistics for dashboard
     */
    public function getStatistics()
    {
        $db = \Config\Database::connect();

        $userModel = new \App\Models\User\UserModel();
        $bidangModel = new \App\Models\Bidang\BidangModel();
        $programModel = new \App\Models\Program\ProgramModel();
        $sppdModel = new \App\Models\SPPD\SPPDModel();

        $stats = $bidangModel->getStatistics();

        // Merge other counts
        $stats['total_users'] = $userModel->where('deleted_at', null)->countAllResults();
        $stats['active_users'] = $userModel->where('is_active', 1)
            ->where('is_blocked', 0)
            ->where('deleted_at', null)
            ->countAllResults();
        $stats['blocked_users'] = $userModel->where('is_blocked', 1)->countAllResults();
        $stats['total_sppd'] = $sppdModel->where('deleted_at', null)->countAllResults();
        $stats['pending_programs'] = $programModel->where('status', 'pending')->countAllResults();
        $stats['pending_sppd'] = $sppdModel->where('status', 'pending')->countAllResults();

        return $stats;
    }

    /**
     * Recent users
     */
    public function getRecentUsers($limit = 10)
    {
        $userModel = new \App\Models\User\UserModel();
        return $userModel->select('users.*, bidang.nama_bidang')
            ->join('bidang', 'bidang.id = users.bidang_id', 'left')
            ->where('users.deleted_at', null)
            ->orderBy('users.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Recent activities from security_logs
     */
    public function getRecentActivities($limit = 15)
    {
        $db = \Config\Database::connect();

        return $db->table('security_logs')
            ->select('security_logs.*, users.nama, users.nip_nik')
            ->join('users', 'users.id = security_logs.user_id', 'left')
            ->orderBy('security_logs.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    /**
     * SPPD count grouped by bidang for given year
     */
    public function getSppdByBidang($year = null)
    {
        $db = \Config\Database::connect();
        $year = $year ?? date('Y');

        return $db->table('sppd')
            ->select('bidang.nama_bidang, COUNT(sppd.id) as total')
            ->join('bidang', 'bidang.id = sppd.bidang_id')
            ->where('sppd.deleted_at', null)
            ->where('YEAR(sppd.tanggal_berangkat)', $year)
            ->groupBy('sppd.bidang_id')
            ->get()
            ->getResult();
    }

    /**
     * SPPD trend for last N months
     */
    public function getSppdTrend($months = 6)
    {
        $db = \Config\Database::connect();
        $data = [];

        for ($i = $months - 1; $i >= 0; $i--) {
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
     * SPPD counts by status
     */
    public function getSppdByStatus()
    {
        $sppdModel = new \App\Models\SPPD\SPPDModel();
        $statuses = ['draft', 'pending', 'approved', 'submitted', 'verified', 'rejected'];
        $data = [];

        foreach ($statuses as $status) {
            $count = $sppdModel->where('status', $status)
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
