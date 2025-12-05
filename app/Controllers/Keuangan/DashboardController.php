<?php
// ========================================
// DASHBOARD KEUANGAN
// ========================================

namespace App\Controllers\Keuangan;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    protected $sppdModel;

    public function __construct()
    {
        $this->sppdModel = new \App\Models\SPPD\SPPDModel();
    }

    public function index()
    {
        $statistics = [
            'pending_verification' => $this->sppdModel->where('status', 'submitted')->countAllResults(),
            'verified_this_month' => $this->sppdModel->where('status', 'verified')
                                                     ->where('MONTH(verified_at_keuangan)', date('m'))
                                                     ->countAllResults(),
            'total_pencairan_month' => $this->getTotalPencairanBulanIni(),
            'need_revision' => $this->sppdModel->where('status', 'need_revision')->countAllResults(),
        ];

        // Get urgent SPPD (submitted > 3 days)
        $urgentSppd = $this->getUrgentSppd();

        $data = [
            'title' => 'Dashboard Keuangan',
            'statistics' => $statistics,
            'urgent_sppd' => $urgentSppd,
        ];

        return view('keuangan/dashboard', $data);
    }

    private function getTotalPencairanBulanIni()
    {
        $result = $this->sppdModel->selectSum('realisasi_biaya')
                                  ->where('status', 'verified')
                                  ->where('MONTH(verified_at_keuangan)', date('m'))
                                  ->where('YEAR(verified_at_keuangan)', date('Y'))
                                  ->get()
                                  ->getRow();

        return $result->realisasi_biaya ?? 0;
    }

    private function getUrgentSppd()
    {
        $threeDaysAgo = date('Y-m-d H:i:s', strtotime('-3 days'));

        return $this->sppdModel->select('sppd.*, bidang.nama_bidang')
                               ->join('bidang', 'bidang.id = sppd.bidang_id')
                               ->where('sppd.status', 'submitted')
                               ->where('sppd.submitted_at <', $threeDaysAgo)
                               ->orderBy('sppd.submitted_at', 'ASC')
                               ->limit(10)
                               ->findAll();
    }
}