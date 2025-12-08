<?php

namespace App\Libraries\SPPD;

use App\Models\SPPD\SPPDModel;
use App\Models\Bidang\BidangModel;

/**
 * SPPD Number Generator
 * 
 * Handles automatic generation and validation of SPPD numbers
 */
class SPPDGenerator
{
    protected $sppdModel;
    protected $bidangModel;

    /**
     * SPPD number format: SPPD/KODE_BIDANG/BULAN_ROMAWI/TAHUN/URUT
     * Example: SPPD/KOMINFO/I/2024/001
     */
    public function __construct()
    {
        $this->sppdModel = new SPPDModel();
        $this->bidangModel = new BidangModel();
    }

    /**
     * Generate automatic SPPD number
     * 
     * @param int $bidangId
     * @param string|null $date If null, uses current date
     * @return string
     */
    public function generateNoSPPD(int $bidangId, ?string $date = null): string
    {
        $bidang = $this->bidangModel->find($bidangId);
        
        if (!$bidang) {
            throw new \Exception('Bidang tidak ditemukan');
        }

        $date = $date ?: date('Y-m-d');
        $bulan = bulan_romawi(date('n', strtotime($date)));
        $tahun = date('Y', strtotime($date));
        $kodeBidang = $bidang['kode_bidang'];

        // Get last urut number for this bidang, month, and year
        $lastUrut = $this->getLastUrut($bidangId, $tahun, date('n', strtotime($date)));
        $nextUrut = $lastUrut + 1;

        return $this->formatNoSPPD($kodeBidang, $bulan, $tahun, $nextUrut);
    }

    /**
     * Suggest next SPPD number
     * 
     * @param int $bidangId
     * @param string|null $date
     * @return string
     */
    public function suggestNoSPPD(int $bidangId, ?string $date = null): string
    {
        return $this->generateNoSPPD($bidangId, $date);
    }

    /**
     * Validate SPPD number format
     * 
     * @param string $noSppd
     * @return array ['valid' => bool, 'message' => string, 'parts' => array]
     */
    public function validateNoSPPD(string $noSppd): array
    {
        // Expected format: SPPD/KODE_BIDANG/BULAN_ROMAWI/TAHUN/URUT
        $pattern = '/^SPPD\/([A-Z0-9]+)\/([IVX]+)\/(\d{4})\/(\d{3})$/';
        
        if (!preg_match($pattern, $noSppd, $matches)) {
            return [
                'valid' => false,
                'message' => 'Format nomor SPPD tidak valid. Format: SPPD/KODE_BIDANG/BULAN/TAHUN/URUT',
                'parts' => []
            ];
        }

        $parts = [
            'prefix' => 'SPPD',
            'kode_bidang' => $matches[1],
            'bulan_romawi' => $matches[2],
            'tahun' => $matches[3],
            'urut' => $matches[4]
        ];

        // Validate bidang code exists
        $bidang = $this->bidangModel->where('kode_bidang', $parts['kode_bidang'])->first();
        if (!$bidang) {
            return [
                'valid' => false,
                'message' => 'Kode bidang tidak ditemukan',
                'parts' => $parts
            ];
        }

        // Validate roman numeral (I-XII)
        $validRoman = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        if (!in_array($parts['bulan_romawi'], $validRoman)) {
            return [
                'valid' => false,
                'message' => 'Bulan romawi tidak valid',
                'parts' => $parts
            ];
        }

        // Validate year format (4 digits)
        if (strlen($parts['tahun']) !== 4 || !is_numeric($parts['tahun'])) {
            return [
                'valid' => false,
                'message' => 'Tahun tidak valid',
                'parts' => $parts
            ];
        }

        // Validate urut format (3 digits)
        if (strlen($parts['urut']) !== 3 || !is_numeric($parts['urut'])) {
            return [
                'valid' => false,
                'message' => 'Nomor urut tidak valid',
                'parts' => $parts
            ];
        }

        // Check if number already exists
        $exists = $this->sppdModel->where('no_sppd', $noSppd)
                                  ->where('deleted_at', null)
                                  ->first();

        if ($exists) {
            return [
                'valid' => false,
                'message' => 'Nomor SPPD sudah digunakan',
                'parts' => $parts
            ];
        }

        return [
            'valid' => true,
            'message' => 'Nomor SPPD valid',
            'parts' => $parts
        ];
    }

    /**
     * Format SPPD number
     * 
     * @param string $bidangKode
     * @param string $bulan Roman numeral (I-XII)
     * @param string $tahun 4-digit year
     * @param int $urut Sequential number
     * @return string
     */
    public function formatNoSPPD(string $bidangKode, string $bulan, string $tahun, int $urut): string
    {
        $urutFormatted = str_pad($urut, 3, '0', STR_PAD_LEFT);
        return "SPPD/{$bidangKode}/{$bulan}/{$tahun}/{$urutFormatted}";
    }

    /**
     * Get last urut number for specific bidang, year, and month
     * 
     * @param int $bidangId
     * @param string $tahun
     * @param int $bulan 1-12
     * @return int
     */
    protected function getLastUrut(int $bidangId, string $tahun, int $bulan): int
    {
        $bidang = $this->bidangModel->find($bidangId);
        $kodeBidang = $bidang['kode_bidang'];
        $bulanRomawi = bulan_romawi($bulan);

        // Get all SPPD with matching pattern
        $pattern = "SPPD/{$kodeBidang}/{$bulanRomawi}/{$tahun}/%";
        
        $lastSppd = $this->sppdModel->like('no_sppd', $pattern)
                                    ->where('deleted_at', null)
                                    ->orderBy('no_sppd', 'DESC')
                                    ->first();

        if (!$lastSppd || !$lastSppd['no_sppd']) {
            return 0;
        }

        // Extract urut number from no_sppd
        $parts = explode('/', $lastSppd['no_sppd']);
        if (count($parts) === 5) {
            return (int) $parts[4];
        }

        return 0;
    }

    /**
     * Parse SPPD number into components
     * 
     * @param string $noSppd
     * @return array|null
     */
    public function parseNoSPPD(string $noSppd): ?array
    {
        $validation = $this->validateNoSPPD($noSppd);
        
        if (!$validation['valid']) {
            return null;
        }

        return $validation['parts'];
    }

    /**
     * Get SPPD count for bidang in specific period
     * 
     * @param int $bidangId
     * @param string $tahun
     * @param int|null $bulan If null, counts whole year
     * @return int
     */
    public function getSppdCount(int $bidangId, string $tahun, ?int $bulan = null): int
    {
        $bidang = $this->bidangModel->find($bidangId);
        $kodeBidang = $bidang['kode_bidang'];

        if ($bulan) {
            $bulanRomawi = bulan_romawi($bulan);
            $pattern = "SPPD/{$kodeBidang}/{$bulanRomawi}/{$tahun}/%";
        } else {
            $pattern = "SPPD/{$kodeBidang}/%/{$tahun}/%";
        }

        return $this->sppdModel->like('no_sppd', $pattern)
                              ->where('deleted_at', null)
                              ->countAllResults();
    }

    /**
     * Get SPPD statistics by bidang
     * 
     * @param int $bidangId
     * @param string $tahun
     * @return array
     */
    public function getStatistics(int $bidangId, string $tahun): array
    {
        $stats = [
            'total_year' => 0,
            'by_month' => [],
            'by_status' => []
        ];

        // Count by month
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $count = $this->getSppdCount($bidangId, $tahun, $bulan);
            $stats['by_month'][$bulan] = [
                'month' => $bulan,
                'month_name' => date('F', mktime(0, 0, 0, $bulan, 1)),
                'count' => $count
            ];
            $stats['total_year'] += $count;
        }

        // Count by status
        $statuses = ['draft', 'pending', 'approved', 'rejected', 'submitted', 'verified', 'closed'];
        foreach ($statuses as $status) {
            $count = $this->sppdModel->where('bidang_id', $bidangId)
                                     ->where('YEAR(tanggal_berangkat)', $tahun)
                                     ->where('status', $status)
                                     ->where('deleted_at', null)
                                     ->countAllResults();
            
            $stats['by_status'][$status] = $count;
        }

        return $stats;
    }

    /**
     * Regenerate SPPD number (for correction purposes)
     * 
     * @param int $sppdId
     * @param bool $force Force regeneration even if already has number
     * @return string
     */
    public function regenerateNoSPPD(int $sppdId, bool $force = false): string
    {
        $sppd = $this->sppdModel->find($sppdId);
        
        if (!$sppd) {
            throw new \Exception('SPPD tidak ditemukan');
        }

        // Check if already has number and force is not set
        if ($sppd['no_sppd'] && !$force) {
            return $sppd['no_sppd'];
        }

        // Generate new number
        $newNo = $this->generateNoSPPD($sppd['bidang_id'], $sppd['tanggal_berangkat']);

        // Update SPPD
        $this->sppdModel->update($sppdId, ['no_sppd' => $newNo]);

        return $newNo;
    }

    /**
     * Check if SPPD number is available
     * 
     * @param string $noSppd
     * @return bool
     */
    public function isAvailable(string $noSppd): bool
    {
        $validation = $this->validateNoSPPD($noSppd);
        return $validation['valid'];
    }

    /**
     * Get next available number for bidang
     * 
     * @param int $bidangId
     * @param string|null $date
     * @return string
     */
    public function getNextAvailable(int $bidangId, ?string $date = null): string
    {
        return $this->generateNoSPPD($bidangId, $date);
    }

    /**
     * Bulk generate SPPD numbers for draft SPPDs
     * 
     * @param int|null $bidangId If null, processes all bidang
     * @return array ['success' => int, 'failed' => int, 'details' => array]
     */
    public function bulkGenerate(?int $bidangId = null): array
    {
        $builder = $this->sppdModel->builder();
        $builder->where('no_sppd IS NULL OR no_sppd = ""', null, false)
               ->where('deleted_at', null);

        if ($bidangId) {
            $builder->where('bidang_id', $bidangId);
        }

        $draftSppds = $builder->findAll();

        $success = 0;
        $failed = 0;
        $details = [];

        foreach ($draftSppds as $sppd) {
            try {
                $newNo = $this->generateNoSPPD($sppd['bidang_id'], $sppd['tanggal_berangkat']);
                $this->sppdModel->update($sppd['id'], ['no_sppd' => $newNo]);
                
                $success++;
                $details[] = [
                    'id' => $sppd['id'],
                    'status' => 'success',
                    'no_sppd' => $newNo
                ];
            } catch (\Exception $e) {
                $failed++;
                $details[] = [
                    'id' => $sppd['id'],
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
            }
        }

        return [
            'success' => $success,
            'failed' => $failed,
            'details' => $details
        ];
    }

    /**
     * Create instance with fluent interface
     * 
     * @return self
     */
    public static function create(): self
    {
        return new self();
    }
}