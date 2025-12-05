<?php

namespace App\Controllers\KepalaBidang;

use App\Controllers\BaseController;
use App\Models\SPPD\SPPDModel;
use App\Models\Program\ProgramModel;
use App\Models\Program\KegiatanModel;
use App\Models\Program\SubKegiatanModel;

class AnalyticsController extends BaseController
{
    protected $sppdModel;
    protected $programModel;
    protected $kegiatanModel;
    protected $subKegiatanModel;

    public function __construct()
    {
        $this->sppdModel = new SPPDModel();
        $this->programModel = new ProgramModel();
        $this->kegiatanModel = new KegiatanModel();
        $this->subKegiatanModel = new SubKegiatanModel();
    }

    /**
     * Display analytics dashboard for Kepala Bidang
     */
    public function index()
    {
        $data = [
            'title' => 'Analytics Bidang',
            'programs' => $this->programModel->getApprovedOptions(user_bidang_id()),
        ];

        return view('kepalabidang/analytics/index', $data);
    }

    /**
     * Get dashboard statistics for bidang
     */
    public function getDashboardStats()
    {
        $tahun = $this->request->getGet('tahun') ?: date('Y');
        $bidangId = user_bidang_id();

        // Program Statistics
        $programStats = $this->getProgramStats($tahun, $bidangId);

        // SPPD Statistics
        $sppdStats = $this->getSppdStats($tahun, $bidangId);

        // Budget Statistics
        $budgetStats = $this->getBudgetStats($tahun, $bidangId);

        // Trend Data
        $trendData = $this->getTrendData($tahun, $bidangId);

        // Performance Metrics
        $performanceMetrics = $this->getPerformanceMetrics($tahun, $bidangId);

        return $this->respondSuccess('Dashboard statistics', [
            'program_stats' => $programStats,
            'sppd_stats' => $sppdStats,
            'budget_stats' => $budgetStats,
            'trend_data' => $trendData,
            'performance_metrics' => $performanceMetrics,
        ]);
    }

    /**
     * Get program statistics for bidang
     */
    protected function getProgramStats($tahun, $bidangId)
    {
        $total = $this->programModel->where('bidang_id', $bidangId)
                                    ->where('tahun_anggaran', $tahun)
                                    ->countAllResults(false);

        $approved = $this->programModel->where('bidang_id', $bidangId)
                                       ->where('tahun_anggaran', $tahun)
                                       ->where('status', 'approved')
                                       ->countAllResults();

        $pending = $this->programModel->where('bidang_id', $bidangId)
                                      ->where('tahun_anggaran', $tahun)
                                      ->where('status', 'pending')
                                      ->countAllResults();

        $rejected = $this->programModel->where('bidang_id', $bidangId)
                                       ->where('tahun_anggaran', $tahun)
                                       ->where('status', 'rejected')
                                       ->countAllResults();

        $draft = $this->programModel->where('bidang_id', $bidangId)
                                    ->where('tahun_anggaran', $tahun)
                                    ->where('status', 'draft')
                                    ->countAllResults();

        return [
            'total' => $total,
            'approved' => $approved,
            'pending' => $pending,
            'rejected' => $rejected,
            'draft' => $draft,
            'approval_rate' => $total > 0 ? round(($approved / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get SPPD statistics for bidang
     */
    protected function getSppdStats($tahun, $bidangId)
    {
        $total = $this->sppdModel->where('bidang_id', $bidangId)
                                 ->where('YEAR(tanggal_berangkat)', $tahun)
                                 ->where('created_by', user_id())
                                 ->countAllResults(false);

        $approved = $this->sppdModel->where('bidang_id', $bidangId)
                                    ->where('YEAR(tanggal_berangkat)', $tahun)
                                    ->where('created_by', user_id())
                                    ->where('status', 'approved')
                                    ->countAllResults();

        $verified = $this->sppdModel->where('bidang_id', $bidangId)
                                    ->where('YEAR(tanggal_berangkat)', $tahun)
                                    ->where('created_by', user_id())
                                    ->where('status', 'verified')
                                    ->countAllResults();

        $pending = $this->sppdModel->where('bidang_id', $bidangId)
                                   ->where('YEAR(tanggal_berangkat)', $tahun)
                                   ->where('created_by', user_id())
                                   ->where('status', 'pending')
                                   ->countAllResults();

        $draft = $this->sppdModel->where('bidang_id', $bidangId)
                                 ->where('YEAR(tanggal_berangkat)', $tahun)
                                 ->where('created_by', user_id())
                                 ->where('status', 'draft')
                                 ->countAllResults();

        return [
            'total' => $total,
            'approved' => $approved,
            'verified' => $verified,
            'pending' => $pending,
            'draft' => $draft,
            'completion_rate' => $approved > 0 ? round(($verified / $approved) * 100, 2) : 0,
        ];
    }

    /**
     * Get budget statistics for bidang
     */
    protected function getBudgetStats($tahun, $bidangId)
    {
        // Total allocated budget
        $totalBudget = $this->programModel->where('bidang_id', $bidangId)
                                          ->where('tahun_anggaran', $tahun)
                                          ->where('status', 'approved')
                                          ->selectSum('jumlah_anggaran')
                                          ->get()
                                          ->getRow()
                                          ->jumlah_anggaran ?? 0;

        // Total estimated (from SPPD)
        $totalEstimasi = $this->sppdModel->where('bidang_id', $bidangId)
                                         ->where('YEAR(tanggal_berangkat)', $tahun)
                                         ->where('created_by', user_id())
                                         ->whereNotIn('status', ['rejected', 'draft'])
                                         ->selectSum('estimasi_biaya')
                                         ->get()
                                         ->getRow()
                                         ->estimasi_biaya ?? 0;

        // Total realisasi (from verified SPPD)
        $totalRealisasi = $this->sppdModel->where('bidang_id', $bidangId)
                                          ->where('YEAR(tanggal_berangkat)', $tahun)
                                          ->where('created_by', user_id())
                                          ->where('status', 'verified')
                                          ->selectSum('realisasi_biaya')
                                          ->get()
                                          ->getRow()
                                          ->realisasi_biaya ?? 0;

        $sisaBudget = $totalBudget - $totalEstimasi;
        $utilizationRate = $totalBudget > 0 ? round(($totalEstimasi / $totalBudget) * 100, 2) : 0;
        $realisasiRate = $totalEstimasi > 0 ? round(($totalRealisasi / $totalEstimasi) * 100, 2) : 0;

        return [
            'total_budget' => $totalBudget,
            'total_estimasi' => $totalEstimasi,
            'total_realisasi' => $totalRealisasi,
            'sisa_budget' => $sisaBudget,
            'utilization_rate' => $utilizationRate,
            'realisasi_rate' => $realisasiRate,
        ];
    }

    /**
     * Get monthly trend data
     */
    protected function getTrendData($tahun, $bidangId)
    {
        $months = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $sppdCount = $this->sppdModel->where('bidang_id', $bidangId)
                                         ->where('YEAR(tanggal_berangkat)', $tahun)
                                         ->where('MONTH(tanggal_berangkat)', $month)
                                         ->where('created_by', user_id())
                                         ->countAllResults();

            $budget = $this->sppdModel->where('bidang_id', $bidangId)
                                      ->where('YEAR(tanggal_berangkat)', $tahun)
                                      ->where('MONTH(tanggal_berangkat)', $month)
                                      ->where('created_by', user_id())
                                      ->where('status', 'verified')
                                      ->selectSum('realisasi_biaya')
                                      ->get()
                                      ->getRow()
                                      ->realisasi_biaya ?? 0;

            $months[] = [
                'month' => date('M', mktime(0, 0, 0, $month, 1)),
                'month_num' => $month,
                'sppd_count' => $sppdCount,
                'budget' => $budget,
            ];
        }

        return $months;
    }

    /**
     * Get performance metrics
     */
    protected function getPerformanceMetrics($tahun, $bidangId)
    {
        // Average approval time for programs
        $programApprovalTime = $this->programModel->select('AVG(DATEDIFF(approved_at, submitted_at)) as avg_days')
                                                  ->where('bidang_id', $bidangId)
                                                  ->where('tahun_anggaran', $tahun)
                                                  ->where('status', 'approved')
                                                  ->whereNotNull('approved_at')
                                                  ->whereNotNull('submitted_at')
                                                  ->get()
                                                  ->getRow()
                                                  ->avg_days ?? 0;

        // Average approval time for SPPD
        $sppdApprovalTime = $this->sppdModel->select('AVG(DATEDIFF(approved_at_kepaladinas, submitted_at)) as avg_days')
                                            ->where('bidang_id', $bidangId)
                                            ->where('YEAR(tanggal_berangkat)', $tahun)
                                            ->where('created_by', user_id())
                                            ->where('status', 'approved')
                                            ->whereNotNull('approved_at_kepaladinas')
                                            ->whereNotNull('submitted_at')
                                            ->get()
                                            ->getRow()
                                            ->avg_days ?? 0;

        // Average SPPD per program
        $programCount = $this->programModel->where('bidang_id', $bidangId)
                                           ->where('tahun_anggaran', $tahun)
                                           ->where('status', 'approved')
                                           ->countAllResults();

        $sppdCount = $this->sppdModel->where('bidang_id', $bidangId)
                                     ->where('YEAR(tanggal_berangkat)', $tahun)
                                     ->where('created_by', user_id())
                                     ->whereNotIn('status', ['draft', 'rejected'])
                                     ->countAllResults();

        $avgSppdPerProgram = $programCount > 0 ? round($sppdCount / $programCount, 2) : 0;

        return [
            'program_approval_time' => round($programApprovalTime, 2),
            'sppd_approval_time' => round($sppdApprovalTime, 2),
            'avg_sppd_per_program' => $avgSppdPerProgram,
        ];
    }

    /**
     * Get SPPD by tipe perjalanan
     */
    public function getSppdByTipe()
    {
        $tahun = $this->request->getGet('tahun') ?: date('Y');
        $bidangId = user_bidang_id();

        $data = $this->sppdModel->select('tipe_perjalanan, COUNT(*) as total')
                                ->where('bidang_id', $bidangId)
                                ->where('YEAR(tanggal_berangkat)', $tahun)
                                ->where('created_by', user_id())
                                ->groupBy('tipe_perjalanan')
                                ->orderBy('total', 'DESC')
                                ->get()
                                ->getResult();

        return $this->respondSuccess('SPPD by tipe', $data);
    }

    /**
     * Get SPPD by status
     */
    public function getSppdByStatus()
    {
        $tahun = $this->request->getGet('tahun') ?: date('Y');
        $bidangId = user_bidang_id();

        $data = $this->sppdModel->select('status, COUNT(*) as total')
                                ->where('bidang_id', $bidangId)
                                ->where('YEAR(tanggal_berangkat)', $tahun)
                                ->where('created_by', user_id())
                                ->groupBy('status')
                                ->orderBy('total', 'DESC')
                                ->get()
                                ->getResult();

        return $this->respondSuccess('SPPD by status', $data);
    }

    /**
     * Get budget realization by program
     */
    public function getBudgetByProgram()
    {
        $tahun = $this->request->getGet('tahun') ?: date('Y');
        $bidangId = user_bidang_id();

        $programs = $this->programModel->where('bidang_id', $bidangId)
                                       ->where('tahun_anggaran', $tahun)
                                       ->where('status', 'approved')
                                       ->findAll();

        $data = [];

        foreach ($programs as $program) {
            // Get total estimated from SPPD
            $totalEstimasi = $this->sppdModel->select('SUM(estimasi_biaya) as total')
                                             ->join('sub_kegiatan', 'sub_kegiatan.id = sppd.sub_kegiatan_id')
                                             ->join('kegiatan', 'kegiatan.id = sub_kegiatan.kegiatan_id')
                                             ->where('kegiatan.program_id', $program['id'])
                                             ->where('YEAR(sppd.tanggal_berangkat)', $tahun)
                                             ->whereNotIn('sppd.status', ['draft', 'rejected'])
                                             ->get()
                                             ->getRow()
                                             ->total ?? 0;

            $data[] = [
                'program' => $program['nama_program'],
                'budget' => $program['jumlah_anggaran'],
                'used' => $totalEstimasi,
                'percentage' => $program['jumlah_anggaran'] > 0 ? 
                    round(($totalEstimasi / $program['jumlah_anggaran']) * 100, 2) : 0,
            ];
        }

        return $this->respondSuccess('Budget by program', $data);
    }

    /**
     * Get top destinations for bidang
     */
    public function getTopDestinations()
    {
        $tahun = $this->request->getGet('tahun') ?: date('Y');
        $limit = $this->request->getGet('limit') ?: 10;
        $bidangId = user_bidang_id();

        $data = $this->sppdModel->select('tempat_tujuan, COUNT(*) as total, SUM(estimasi_biaya) as total_biaya')
                                ->where('bidang_id', $bidangId)
                                ->where('YEAR(tanggal_berangkat)', $tahun)
                                ->where('created_by', user_id())
                                ->whereNotIn('status', ['draft', 'rejected'])
                                ->groupBy('tempat_tujuan')
                                ->orderBy('total', 'DESC')
                                ->limit($limit)
                                ->get()
                                ->getResult();

        return $this->respondSuccess('Top destinations', $data);
    }

    /**
     * Get pegawai participation
     */
    public function getPegawaiParticipation()
    {
        $tahun = $this->request->getGet('tahun') ?: date('Y');
        $bidangId = user_bidang_id();

        $data = $this->sppdModel->select('users.nama, users.nip_nik, COUNT(sppd_pegawai.id) as total_sppd')
                                ->join('sppd_pegawai', 'sppd_pegawai.sppd_id = sppd.id')
                                ->join('users', 'users.id = sppd_pegawai.pegawai_id')
                                ->where('sppd.bidang_id', $bidangId)
                                ->where('YEAR(sppd.tanggal_berangkat)', $tahun)
                                ->where('sppd.created_by', user_id())
                                ->whereNotIn('sppd.status', ['draft', 'rejected'])
                                ->groupBy('sppd_pegawai.pegawai_id')
                                ->orderBy('total_sppd', 'DESC')
                                ->limit(15)
                                ->get()
                                ->getResult();

        return $this->respondSuccess('Pegawai participation', $data);
    }

    /**
     * Get quarterly comparison
     */
    public function getQuarterlyComparison()
    {
        $tahun = $this->request->getGet('tahun') ?: date('Y');
        $bidangId = user_bidang_id();

        $quarters = [
            'Q1' => [1, 2, 3],
            'Q2' => [4, 5, 6],
            'Q3' => [7, 8, 9],
            'Q4' => [10, 11, 12],
        ];

        $data = [];

        foreach ($quarters as $quarter => $months) {
            $sppdCount = $this->sppdModel->where('bidang_id', $bidangId)
                                         ->where('YEAR(tanggal_berangkat)', $tahun)
                                         ->whereIn('MONTH(tanggal_berangkat)', $months)
                                         ->where('created_by', user_id())
                                         ->whereNotIn('status', ['draft', 'rejected'])
                                         ->countAllResults();

            $budget = $this->sppdModel->where('bidang_id', $bidangId)
                                      ->where('YEAR(tanggal_berangkat)', $tahun)
                                      ->whereIn('MONTH(tanggal_berangkat)', $months)
                                      ->where('created_by', user_id())
                                      ->where('status', 'verified')
                                      ->selectSum('realisasi_biaya')
                                      ->get()
                                      ->getRow()
                                      ->realisasi_biaya ?? 0;

            $data[] = [
                'quarter' => $quarter,
                'sppd_count' => $sppdCount,
                'budget' => $budget,
            ];
        }

        return $this->respondSuccess('Quarterly comparison', $data);
    }

    /**
     * Export bidang report
     */
    public function exportReport()
    {
        $format = $this->request->getGet('format') ?: 'pdf';
        $tahun = $this->request->getGet('tahun') ?: date('Y');
        $bidangId = user_bidang_id();

        $bidangModel = new \App\Models\Bidang\BidangModel();
        $bidang = $bidangModel->find($bidangId);

        // Gather all data
        $reportData = [
            'tahun' => $tahun,
            'bidang' => $bidang,
            'program_stats' => $this->getProgramStats($tahun, $bidangId),
            'sppd_stats' => $this->getSppdStats($tahun, $bidangId),
            'budget_stats' => $this->getBudgetStats($tahun, $bidangId),
            'performance_metrics' => $this->getPerformanceMetrics($tahun, $bidangId),
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