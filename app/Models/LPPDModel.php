<?php

namespace App\Models\SPPD;

use App\Models\BaseModel;

// ========================================
// LPPD MODEL
// ========================================

class LPPDModel extends BaseModel
{
    protected $table = 'lppd';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'uuid', 'sppd_id', 'pegawai_id', 'hasil_kegiatan', 'hambatan',
        'saran', 'dokumentasi', 'tanggal_pengisian', 'is_submitted', 'submitted_at'
    ];

    protected $useTimestamps = true;
    protected $useSoftDeletes = false;

    protected $validationRules = [
        'sppd_id' => 'required|numeric',
        'pegawai_id' => 'required|numeric',
        'hasil_kegiatan' => 'required|min_length[50]',
        'tanggal_pengisian' => 'required|valid_date',
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
     * Get LPPD by SPPD ID
     */
    public function getBySppd($sppdId)
    {
        return $this->where('sppd_id', $sppdId)->first();
    }

    /**
     * Submit LPPD
     */
    public function submitLPPD($id)
    {
        return $this->update($id, [
            'is_submitted' => 1,
            'submitted_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Check if LPPD exists for SPPD
     */
    public function existsForSppd($sppdId)
    {
        return $this->where('sppd_id', $sppdId)->countAllResults() > 0;
    }

    /**
     * Get dokumentasi files
     */
    public function getDokumentasi($lppdId)
    {
        $lppd = $this->find($lppdId);
        return $lppd ? json_decode($lppd['dokumentasi'], true) : [];
    }

    /**
     * Add dokumentasi file
     */
    public function addDokumentasi($lppdId, $filename)
    {
        $lppd = $this->find($lppdId);
        $dokumentasi = json_decode($lppd['dokumentasi'] ?? '[]', true);
        $dokumentasi[] = $filename;
        
        return $this->update($lppdId, [
            'dokumentasi' => json_encode($dokumentasi)
        ]);
    }
}
