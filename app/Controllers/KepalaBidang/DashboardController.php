<?php

namespace App\Controllers\KepalaBidang;

use App\Controllers\BaseController;
// ========================================
// DASHBOARD KEPALA BIDANG
// ========================================

class DashboardController extends BaseController
{
    public function index()
    {
        $programModel = new \App\Models\Program\ProgramModel();
        $sppdModel = new \App\Models\SPPD\SPPDModel();
        $bidangId = user_bidang_id();

        $data = [
            'title' => 'Dashboard Kepala Bidang',
            'statistics' => [
                'total_programs' => $programModel->where('bidang_id', $bidangId)->countAllResults(),
                'active_programs' => $programModel->where('bidang_id', $bidangId)->where('status', 'approved')->countAllResults(),
                'total_sppd' => $sppdModel->where('bidang_id', $bidangId)->countAllResults(),
                'sppd_bulan_ini' => $sppdModel->where('bidang_id', $bidangId)
                                              ->where('MONTH(tanggal_berangkat)', date('m'))
                                              ->countAllResults(),
                'anggaran_tahun_ini' => $programModel->getAllAnggaranTahunIniByBidang($bidangId, date('Y')),
            ],
        ];

        return view('kepalabidang/dashboard', $data);
    }
}