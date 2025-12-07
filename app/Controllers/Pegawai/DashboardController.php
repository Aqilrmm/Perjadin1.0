<?php

namespace App\Controllers\Pegawai;

use App\Controllers\BaseController;
// ========================================
// DASHBOARD PEGAWAI
// ========================================

class DashboardController extends BaseController
{
    protected $sppdModel;
    protected $sppdPegawaiModel;

    public function __construct()
    {
        $this->sppdModel = new \App\Models\SPPD\SPPDModel();
        $this->sppdPegawaiModel = new \App\Models\SPPD\SPPDPegawaiModel();
    }

    public function index()
    {
        $pegawaiId = user_id();

        // Get statistics
        $statistics = [
            'total_sppd' => $this->sppdPegawaiModel->where('pegawai_id', $pegawaiId)->countAllResults(),
            'sppd_berjalan' => $this->getSppdBerjalan($pegawaiId),
            'need_action' => $this->getNeedAction($pegawaiId),
        ];

        // Get upcoming trips (next 30 days)
        $upcomingTrips = $this->getUpcomingTrips($pegawaiId);

        // Get action required SPPD
        $actionRequired = $this->getActionRequired($pegawaiId);

        $data = [
            'title' => 'Dashboard Pegawai',
            'statistics' => $statistics,
            'upcoming_trips' => $upcomingTrips,
            'action_required' => $actionRequired,
        ];

        return view('pegawai/dashboard', $data);
    }

    private function getSppdBerjalan($pegawaiId)
    {
        return $this->sppdModel->select('sppd.*')
                               ->join('sppd_pegawai', 'sppd_pegawai.sppd_id = sppd.id')
                               ->where('sppd_pegawai.pegawai_id', $pegawaiId)
                               ->whereIn('sppd.status', ['approved', 'submitted'])
                               ->where('sppd.tanggal_berangkat <=', date('Y-m-d'))
                               ->where('sppd.tanggal_kembali >=', date('Y-m-d'))
                               ->countAllResults();
    }

    private function getNeedAction($pegawaiId)
    {
        $lppdModel = new \App\Models\SPPD\LPPDModel();
        $kwitansiModel = new \App\Models\SPPD\KwitansiModel();

        // Count SPPD that need LPPD/Kwitansi
        $sppdList = $this->sppdModel->select('sppd.*')
                                    ->join('sppd_pegawai', 'sppd_pegawai.sppd_id = sppd.id')
                                    ->where('sppd_pegawai.pegawai_id', $pegawaiId)
                                    ->where('sppd.status', 'approved')
                                    ->where('sppd.tanggal_kembali <', date('Y-m-d'))
                                    ->findAll();

        $count = 0;
        foreach ($sppdList as $sppd) {
            $lppd = $lppdModel->getBySppd($sppd['id']);
            if (!$lppd || !$lppd['is_submitted']) {
                $count++;
            }
        }

        return $count;
    }

    private function getUpcomingTrips($pegawaiId)
    {
        return $this->sppdModel->select('sppd.*')
                               ->join('sppd_pegawai', 'sppd_pegawai.sppd_id = sppd.id')
                               ->where('sppd_pegawai.pegawai_id', $pegawaiId)
                               ->where('sppd.status', 'approved')
                               ->where('sppd.tanggal_berangkat >', date('Y-m-d'))
                               ->where('sppd.tanggal_berangkat <=', date('Y-m-d', strtotime('+30 days')))
                               ->orderBy('sppd.tanggal_berangkat', 'ASC')
                               ->limit(5)
                               ->findAll();
    }

    private function getActionRequired($pegawaiId)
    {
        $lppdModel = new \App\Models\SPPD\LPPDModel();

        $sppdList = $this->sppdModel->select('sppd.*')
                                    ->join('sppd_pegawai', 'sppd_pegawai.sppd_id = sppd.id')
                                    ->where('sppd_pegawai.pegawai_id', $pegawaiId)
                                    ->where('sppd.status', 'approved')
                                    ->where('sppd.tanggal_kembali <', date('Y-m-d'))
                                    ->orderBy('sppd.tanggal_kembali', 'DESC')
                                    ->findAll();

        $needAction = [];
        foreach ($sppdList as $sppd) {
            $lppd = $lppdModel->getBySppd($sppd['id']);
            if (!$lppd || !$lppd['is_submitted']) {
                $needAction[] = $sppd;
            }
        }

        return $needAction;
    }
}
