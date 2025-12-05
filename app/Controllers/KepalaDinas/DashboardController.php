<?php

namespace App\Controllers\KepalaDinas;

use App\Controllers\BaseController;// ========================================
// DASHBOARD KEPALA DINAS
// ========================================

class DashboardController extends BaseController
{
    public function index()
    {
        $programModel = new \App\Models\Program\ProgramModel();
        $sppdModel = new \App\Models\SPPD\SPPDModel();

        $data = [
            'title' => 'Dashboard Kepala Dinas',
            'statistics' => [
                'pending_programs' => $programModel->where('status', 'pending')->countAllResults(),
                'pending_kegiatan' => (new \App\Models\Program\KegiatanModel())->where('status', 'pending')->countAllResults(),
                'pending_subkegiatan' => (new \App\Models\Program\SubKegiatanModel())->where('status', 'pending')->countAllResults(),
                'pending_sppd' => $sppdModel->where('status', 'pending')->countAllResults(),
            ],
        ];

        return view('kepaladinas/dashboard', $data);
    }
}