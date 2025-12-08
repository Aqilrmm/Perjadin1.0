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

    public function getPegawaiBySppdId($sppdId)
    {
        return $this->select('sppd_pegawai.*, users.*')
            ->join('users', 'users.id = sppd_pegawai.pegawai_id')
            ->where('sppd_pegawai.sppd_id', $sppdId)
            ->findAll();
    }

    public function getSppdByPegawaiId($pegawaiId)
    {
        return $this->select('sppd_pegawai.*, sppd.*')
            ->join('sppd', 'sppd.id = sppd_pegawai.sppd_id')
            ->where('sppd_pegawai.pegawai_id', $pegawaiId)
            ->where('sppd.deleted_at', null)
            ->findAll();
    }

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

    public function removePegawai($sppdId, $pegawaiId = null)
    {
        $builder = $this->where('sppd_id', $sppdId);

        if ($pegawaiId) {
            $builder->where('pegawai_id', $pegawaiId);
        }

        return $builder->delete();
    }

    public function removeAllPegawai($sppdId)
    {
        return $this->where('sppd_id', $sppdId)->delete();
    }

    public function updatePegawaiList($sppdId, $pegawaiIds)
    {
        $this->removeAllPegawai($sppdId);
        return $this->addPegawai($sppdId, $pegawaiIds);
    }

    public function isPegawaiInSppd($sppdId, $pegawaiId)
    {
        return $this->where('sppd_id', $sppdId)
            ->where('pegawai_id', $pegawaiId)
            ->countAllResults() > 0;
    }

    public function countPegawai($sppdId)
    {
        return $this->where('sppd_id', $sppdId)->countAllResults();
    }

    public function getPegawaiIds($sppdId)
    {
        $result = $this->select('pegawai_id')
            ->where('sppd_id', $sppdId)
            ->findAll();

        return array_column($result, 'pegawai_id');
    }
}
