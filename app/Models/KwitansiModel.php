<?php

// ========================================
// KWITANSI MODEL
// ========================================

namespace App\Models\SPPD;

use App\Models\BaseModel;

class KwitansiModel extends BaseModel
{
    protected $table = 'kwitansi';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'uuid', 'sppd_id', 'pegawai_id', 'biaya_perjalanan', 'bukti_perjalanan',
        'keterangan_perjalanan', 'biaya_lumsum', 'keterangan_lumsum', 'biaya_penginapan',
        'bukti_penginapan', 'keterangan_penginapan', 'biaya_taxi', 'bukti_taxi',
        'keterangan_taxi', 'biaya_tiket', 'bukti_tiket', 'keterangan_tiket',
        'total_biaya', 'is_submitted', 'submitted_at'
    ];

    protected $useTimestamps = true;
    protected $useSoftDeletes = false;

    protected $validationRules = [
        'sppd_id' => 'required|numeric',
        'pegawai_id' => 'required|numeric',
        'total_biaya' => 'required|numeric|greater_than[0]',
    ];

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        if (!isset($data['data']['uuid']) || empty($data['data']['uuid'])) {
            $data['data']['uuid'] = $this->generateUUID();
        }
        return $data;
    }

    protected function generateUUID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Get kwitansi by SPPD ID
     */
    public function getBySppd($sppdId)
    {
        return $this->where('sppd_id', $sppdId)->first();
    }

    /**
     * Submit kwitansi
     */
    public function submitKwitansi($id)
    {
        return $this->update($id, [
            'is_submitted' => 1,
            'submitted_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Check if kwitansi exists for SPPD
     */
    public function existsForSppd($sppdId)
    {
        return $this->where('sppd_id', $sppdId)->countAllResults() > 0;
    }

    /**
     * Calculate total biaya
     */
    public function calculateTotal($data)
    {
        $total = 0;
        
        if (isset($data['biaya_perjalanan'])) {
            $total += (float) $data['biaya_perjalanan'];
        }
        
        if (isset($data['biaya_lumsum'])) {
            $total += (float) $data['biaya_lumsum'];
        }
        
        if (isset($data['biaya_penginapan'])) {
            $total += (float) $data['biaya_penginapan'];
        }
        
        if (isset($data['biaya_taxi'])) {
            $total += (float) $data['biaya_taxi'];
        }
        
        if (isset($data['biaya_tiket'])) {
            $total += (float) $data['biaya_tiket'];
        }
        
        return $total;
    }

    /**
     * Validate biaya tidak melebihi estimasi
     */
    public function validateBiaya($sppdId, $totalBiaya)
    {
        $sppdModel = new SPPDModel();
        $sppd = $sppdModel->find($sppdId);
        
        if (!$sppd) {
            return false;
        }
        
        return $totalBiaya <= $sppd['estimasi_biaya'];
    }

    /**
     * Get biaya breakdown
     */
    public function getBiayaBreakdown($kwitansiId)
    {
        $kwitansi = $this->find($kwitansiId);
        
        if (!$kwitansi) {
            return [];
        }
        
        return [
            'biaya_perjalanan' => $kwitansi['biaya_perjalanan'] ?? 0,
            'biaya_lumsum' => $kwitansi['biaya_lumsum'] ?? 0,
            'biaya_penginapan' => $kwitansi['biaya_penginapan'] ?? 0,
            'biaya_taxi' => $kwitansi['biaya_taxi'] ?? 0,
            'biaya_tiket' => $kwitansi['biaya_tiket'] ?? 0,
            'total' => $kwitansi['total_biaya']
        ];
    }
}