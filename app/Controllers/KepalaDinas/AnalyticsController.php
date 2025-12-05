<?php

namespace App\Controllers\KepalaDinas;

use App\Controllers\BaseController;
use App\Models\SPPD\SPPDModel;
use App\Models\Program\ProgramModel;
use App\Models\Program\KegiatanModel;
use App\Models\Program\SubKegiatanModel;
use App\Models\Bidang\BidangModel;

class AnalyticsController extends BaseController
{
    protected $sppdModel;
    protected $programModel;
    protected $kegiatanModel;
    protected $subKegiatanModel;
    protected $bidangModel;

    public function __construct()
    {
        $this->sppdModel = new SPPDModel();
        $this->programModel = new ProgramModel();
        $this->kegiatanModel = new KegiatanModel();
        $this->subKegiatanModel = new SubKegiatanModel();
        $this->bidangModel = new BidangModel();
    }

    /**
     * Display analytics dashboard
     */
    public function index()
    {
        $data = [
            'title' => 'Analytics & Reports',
            'bidang_list' => $this->bidangModel->getActiveOptions(),
        ];

        return view('kepaladinas/analytics/index', $data);
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        $tahun = $this->request->getGet('tahun') ?: date('Y');
        $bidangId = $this->request->getGet('bidang_id');

        // Program Statistics
        $programStats = $this->getProgramStats($tahun, $bidangId);

        // SPPD Statistics
        $sppdStats = $this->getSppdStats($tahun, $bidangId);

        // Budget Statistics
        $budgetStats = $this->getBudgetStats($tahun, $bidangId);

        // Trend Data
        $trendData = $this->getTrendData($tahun, $bidangId);

        return $this->respondSuccess('Dashboard statistics', [
            'program_stats' => $programStats,
            'sppd_stats' => $sppdStats,
            'budget_stats' => $budgetStats,
            'trend_data' => $trendData,
        ]);
    }

    /**
     * Get program statistics
     */
    protected function getProgramStats($tahun, $bidangId = null)
    {
        $builder = $this->programModel->where('tahun_anggaran', $tahun);
        
        if ($bidangId) {
            $builder->where('bidang_id', $bidangId);
        }

        $total = $builder->countAllResults(false);
        $approved = $builder->where('status', 'approved')->countAllResults(false);
        $pending = $this->programModel->where('tahun_anggaran', $tahun)
                                      ->where('status', 'pending')
                                      ->countAllResults();
        $rejected = $this->programModel->where('tahun_anggaran', $tahun)
                                       ->where('status', 'rejected')
                                       ->countAllResults();

        return [
            'total' => $total,
            'approved' => $approved,
            'pending' => $pending,
            'rejected' => $rejected,
            'approval_rate' => $total > 0 ? round(($approved / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get SPPD statistics
     */
    protected function getSppdStats($tahun, $bidangId = null)
    {
        $builder = $this->sppdModel->where('YEAR(tanggal_berangkat)', $tahun);
        
        if ($bidangId) {
            $builder->where('bidang_id', $bidangId);
        }

        $total = $builder->countAllResults(false);
        $approved = $builder->where('status', 'approved')->countAllResults(false);
        $verified = $this->sppdModel->where('YEAR(tanggal_berangkat)', $tahun)
                                    ->where('status', 'verified')
                                    ->countAllResults();
        $pending = $this->sppdModel->where('YEAR(tanggal_berangkat)', $tahun)
                                   ->where('status', 'pending')
                                   ->countAllResults();

        return [
            'total' => $total,
            'approved' => $approved,
            'verified' => $verified,
            'pending' => $pending,
            'completion_rate' => $approved > 0 ? round(($verified / $approved) * 100, 2) : 0,
        ];
    }

    /**
     * Get budget statistics
     */
    protected function getBudgetStats($tahun, $bidangId = null)
    {
        $builder = $this->programModel->where('tahun_anggaran', $tahun)
                                      ->where('status', 'approved');
        
        if ($bidangId) {
            $builder->where('bidang_id', $bidangId);
        }

        $totalBudget = $builder->selectSum('jumlah_anggaran')->get()->getRow()->jumlah_anggaran ?? 0;

        // Get total realisasi from verified SPPD
        $sppdBuilder = $this->sppdModel->where('YEAR(tanggal_berangkat)', $tahun)
                                       ->where('status', 'verified');
        
        if ($bidangId) {
            $sppdBuilder->where('bidang_id', $bidangId);
        }

        $totalRealisasi = $sppdBuilder->selectSum('realisasi_biaya')->get()->getRow()->realisasi_biaya ?? 0;

        $sisaBudget = $totalBudget - $totalRealisasi;
        $realisasiPercentage = $totalBudget > 0 ? round(($totalRealisasi / $totalBudget) * 100, 2) : 0;

        return [
            'total_budget' => $totalBudget,
            'total_realisasi' => $totalRealisasi,
            'sisa_budget' => $sisaBudget,
            'realisasi_percentage' => $realisasiPercentage,
        ];
    }

    /**
     * Get trend data (monthly)
     */
    protected function getTrendData($tahun, $bidangId = null)
    {
        $months = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $builder = $this->sppdModel->where('YEAR(tanggal_berangkat)', $tahun)
                                       ->where('MONTH(tanggal_berangkat)', $month);
            
            if ($bidangId) {
                $builder->where('bidang_id', $bidangId);
            }

            $count = $builder->countAllResults(false);
            
            $budgetBuilder = clone $builder;
            $budget = $budgetBuilder->where('status', 'verified')
                                   ->selectSum('realisasi_biaya')
                                   ->get()
                                   ->getRow()
                                   ->realisasi_biaya ?? 0;

            $months[] = [
                'month' => date('M', mktime(0, 0, 0, $month, 1)),
                'month_num' => $month,
                'sppd_count' => $count,
                'budget' => $budget,
            ];
        }

        return $months;
    }

    /**
     * Get SPPD by bidang chart data
     */
    public function getSppdByBidang()
    {
        $tahun = $this->request->getGet('tahun') ?: date('Y');

        $data = $this->bidangModel->select('bidang.nama_bidang, COUNT(sppd.id) as total')
                                  ->join('sppd', 'sppd.bidang_id = bidang.id AND YEAR(sppd.tanggal_berangkat) = ' . $tahun, 'left')
                                  ->where('bidang.deleted_at', null)
                                  ->groupBy('bidang.id')
                                  ->orderBy('total', 'DESC')
                                  ->get()
                                  ->getResult();

        return $this->respondSuccess('SPPD by bidang', $data);
    }

    /**
     * Get SPPD by tipe perjalanan
     */
    public function getSppdByTipe()
    {
        $tahun = $this->request->getGet('tahun') ?: date('Y');
        $bidangId = $this->request->getGet('bidang_id');

        $builder = $this->sppdModel->select('tipe_perjalanan, COUNT(*) as total')
                                   ->where('YEAR(tanggal_berangkat)', $tahun);
        
        if ($bidangId) {
            $builder->where('bidang_id', $bidangId);
        }

        $data = $builder->groupBy('tipe_perjalanan')
                       ->orderBy('total', 'DESC')
                       ->get()
                       ->getResult();

        return $this->respondSuccess('SPPD by tipe', $data);
    }

    /**
     * Get budget realization by bidang
     */
    public function getBudgetByBidang()
    {
        $tahun = $this->request->getGet('tahun') ?: date('Y');

        $data = [];
        $bidangList = $this->bidangModel->where('is_active', 1)->findAll();

        foreach ($bidangList as $bidang) {
            // Total budget
            $totalBudget = $this->programModel->where('bidang_id', $bidang['id'])
                                              ->where('tahun_anggaran', $tahun)
                                              ->where('status', 'approved')
                                              ->selectSum('jumlah_anggaran')
                                              ->get()
                                              ->getRow()
                                              ->jumlah_anggaran ?? 0;

            // Total realisasi
            $totalRealisasi = $this->sppdModel->where('bidang_id', $bidang['id'])
                                              ->where('YEAR(tanggal_berangkat)', $tahun)
                                              ->where('status', 'verified')
                                              ->selectSum('realisasi_biaya')
                                              ->get()
                                              ->getRow()
                                              ->realisasi_biaya ?? 0;

            $data[] = [
                'bidang' => $bidang['nama_bidang'],
                'budget' => $totalBudget,
                'realisasi' => $totalRealisasi,
                'percentage' => $totalBudget > 0 ? round(($totalRealisasi / $totalBudget) * 100, 2) : 0,
            ];
        }

        return $this->respondSuccess('Budget by bidang', $data);
    }

    /**
     * Get top destinations
     */
    public function getTopDestinations()
    {
        $tahun = $this->request->getGet('tahun') ?: date('Y');
        $limit = $this->request->getGet('limit') ?: 10;
        $bidangId = $this->request->getGet('bidang_id');

        $builder = $this->sppdModel->select('tempat_tujuan, COUNT(*) as total')
                                   ->where('YEAR(tanggal_berangkat)', $tahun);
        
        if ($bidangId) {
            $builder->where('bidang_id', $bidangId);
        }

        $data = $builder->groupBy('tempat_tujuan')
                       ->orderBy('total', 'DESC')
                       ->limit($limit)
                       ->get()
                       ->getResult();

        return $this->respondSuccess('Top destinations', $data);
    }

    /**
     * Get approval timeline (average days)
     */
    public function getApprovalTimeline()
    {
        $tahun = $this->request->getGet('tahun') ?: date('Y');
        $bidangId = $this->request->getGet('bidang_id');

        $builder = $this->sppdModel->select('DATEDIFF(approved_at_kepaladinas, submitted_at) as days')
                                   ->where('YEAR(tanggal_berangkat)', $tahun)
                                   ->where('status !=', 'draft')
                                   ->whereNotNull('approved_at_kepaladinas')
                                   ->whereNotNull('submitted_at');
        
        if ($bidangId) {
            $builder->where('bidang_id', $bidangId);
        }

        $result = $builder->get()->getResult();

        $totalDays = 0;
        $count = 0;

        foreach ($result as $row) {
            $totalDays += $row->days;
            $count++;
        }

        $averageDays = $count > 0 ? round($totalDays / $count, 2) : 0;

        return $this->respondSuccess('Approval timeline', [
            'average_days' => $averageDays,
            'total_approved' => $count,
        ]);
    }

    /**
     * Export report
     */
    public function exportReport()
    {
        $format = $this->request->getGet('format') ?: 'pdf';
        $tahun = $this->request->getGet('tahun') ?: date('Y');
        $bidangId = $this->request->getGet('bidang_id');

        // Gather all data
        $reportData = [
            'tahun' => $tahun,
            'bidang' => $bidangId ? $this->bidangModel->find($bidangId) : null,
            'program_stats' => $this->getProgramStats($tahun, $bidangId),
            'sppd_stats' => $this->getSppdStats($tahun, $bidangId),
            'budget_stats' => $this->getBudgetStats($tahun, $bidangId),
            'generated_at' => date('Y-m-d H:i:s'),
            'generated_by' => user_name(),
        ];

        if ($format === 'pdf') {
            return $this->exportPDF($reportData);
        } elseif ($format === 'excel') {
            return $this->exportExcel($reportData);
        }

        return $this->respondError('Invalid format', null, 400);
    }

    /**
     * Export to PDF (placeholder)
     */
    protected function exportPDF($data)
    {
        // TODO: Implement PDF generation using mPDF
        return $this->respondError('PDF export not implemented yet', null, 501);
    }

    /**
     * Export to Excel (placeholder)
     */
    protected function exportExcel($data)
    {
        // TODO: Implement Excel generation using PhpSpreadsheet
        return $this->respondError('Excel export not implemented yet', null, 501);
    }
}