<?php

namespace App\Models\SPPD;

use App\Models\BaseModel;

class SPPDPegawaiModel extends BaseModel
{
    protected $table = 'sppd_pegawai';
    protected $primaryKey = 'id';
    protected $allowedFields = ['sppd_id', 'pegawai_id'];

    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $createdField = 'created_at';

    /**
     * Get pegawai list by SPPD
     */
    public function getPegawaiBySppdId($sppdId)
    {
        return $this->select('sppd_pegawai.*, users.*')
                    ->join('users', 'users.id = sppd_pegawai.pegawai_id')
                    ->where('sppd_pegawai.sppd_id', $sppdId)
                    ->findAll();
    }

    /**
     * Get SPPD list by pegawai
     */
    public function getSppdByPegawaiId($pegawaiId)
    {
        return $this->select('sppd_pegawai.*, sppd.*')
                    ->join('sppd', 'sppd.id = sppd_pegawai.sppd_id')
                    ->where('sppd_pegawai.pegawai_id', $pegawaiId)
                    ->where('sppd.deleted_at', null)
                    ->findAll();
    }

    /**
     * Add pegawai to SPPD
     */
    public function addPegawai($sppdId, $pegawaiIds)
    {
        if (!is_array($pegawaiIds)) {
            $pegawaiIds = [$pegawaiIds];
        }

        $data = [];
        foreach ($pegawaiIds as $pegawaiId) {
            $data[] = [
                'sppd_id' => $sppdId,
                'pegawai_id' => $pegawaiId,
                'created_at' => date('Y-m-d H:i:s')
            ];
        }

        return $this->insertBatch($data);
    }

    /**
     * Remove pegawai from SPPD
     */
    public function removePegawai($sppdId, $pegawaiId = null)
    {
        $builder = $this->where('sppd_id', $sppdId);
        
        if ($pegawaiId) {
            $builder->where('pegawai_id', $pegawaiId);
        }
        
        return $builder->delete();
    }

    /**
     * Remove all pegawai from SPPD
     */
    public function removeAllPegawai($sppdId)
    {
        return $this->where('sppd_id', $sppdId)->delete();
    }

    /**
     * Update pegawai list for SPPD
     */
    public function updatePegawaiList($sppdId, $pegawaiIds)
    {
        // Remove all existing
        $this->removeAllPegawai($sppdId);
        
        // Add new list
        return $this->addPegawai($sppdId, $pegawaiIds);
    }

    /**
     * Check if pegawai is in SPPD
     */
    public function isPegawaiInSppd($sppdId, $pegawaiId)
    {
        return $this->where('sppd_id', $sppdId)
                    ->where('pegawai_id', $pegawaiId)
                    ->countAllResults() > 0;
    }

    /**
     * Count pegawai in SPPD
     */
    public function countPegawai($sppdId)
    {
        return $this->where('sppd_id', $sppdId)->countAllResults();
    }

    /**
     * Get pegawai IDs by SPPD
     */
    public function getPegawaiIds($sppdId)
    {
        $result = $this->select('pegawai_id')
                       ->where('sppd_id', $sppdId)
                       ->findAll();
        
        return array_column($result, 'pegawai_id');
    }
}