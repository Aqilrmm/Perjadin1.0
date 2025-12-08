<?php

// ========================================
// LAPORAN CONTROLLER
// ========================================

namespace App\Controllers\Keuangan;

use App\Controllers\BaseController;

class LaporanController extends BaseController
{
    protected $sppdModel;

    public function __construct()
    {
        $this->sppdModel = new \App\Models\SPPD\SPPDModel();
    }

    public function index()
    {
        $bidangModel = new \App\Models\Bidang\BidangModel();

        $data = [
            'title' => 'Laporan Keuangan',
            'bidang_list' => $bidangModel->getActiveOptions(),
        ];

        return view('keuangan/laporan/index', $data);
    }

    public function generate()
    {
        $rules = config('Validation')->rules['laporan'];

        $valid = $this->validate($rules);
        if ($valid !== true) {
            return $this->respondError('Validasi gagal', $this->getValidationErrors(), 422);
        }

        $periodeStart = $this->request->getPost('periode_start');
        $periodeEnd = $this->request->getPost('periode_end');
        $format = $this->request->getPost('format');
        $bidangIds = $this->request->getPost('bidang_ids');
        $status = $this->request->getPost('status') ?: 'verified';

        // Build query
        $builder = $this->sppdModel->select('sppd.*, bidang.nama_bidang, programs.nama_program')
            ->join('bidang', 'bidang.id = sppd.bidang_id')
            ->join('sub_kegiatan', 'sub_kegiatan.id = sppd.sub_kegiatan_id')
            ->join('kegiatan', 'kegiatan.id = sub_kegiatan.kegiatan_id')
            ->join('programs', 'programs.id = kegiatan.program_id')
            ->where('sppd.tanggal_berangkat >=', $periodeStart)
            ->where('sppd.tanggal_berangkat <=', $periodeEnd)
            ->where('sppd.status', $status)
            ->where('sppd.deleted_at', null);

        if ($bidangIds) {
            $builder->whereIn('sppd.bidang_id', $bidangIds);
        }

        $sppdList = $builder->findAll();

        if ($format == 'excel') {
            return $this->generateExcel($sppdList, $periodeStart, $periodeEnd);
        } else {
            return $this->generatePDF($sppdList, $periodeStart, $periodeEnd);
        }
    }

    private function generateExcel($data, $start, $end)
    {
        // TODO: Implement Excel generation using PhpSpreadsheet
        return $this->respondSuccess('Excel generated', ['url' => '/downloads/report.xlsx']);
    }

    private function generatePDF($data, $start, $end)
    {
        // TODO: Implement PDF generation using mPDF
        return $this->respondSuccess('PDF generated', ['url' => '/downloads/report.pdf']);
    }
}
