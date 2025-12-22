<?php

namespace App\Models\SPPD;

use App\Models\BaseModel;

class SPPDModel extends BaseModel
{
    protected $table = 'sppd';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'uuid', 'no_sppd', 'sub_kegiatan_id', 'bidang_id', 'tipe_perjalanan',
        'maksud_perjalanan', 'dasar_surat', 'file_surat_tugas', 'alat_angkut',
        'tempat_berangkat', 'tempat_tujuan', 'tanggal_berangkat', 'tanggal_kembali',
        'lama_perjalanan', 'penanggung_jawab', 'estimasi_biaya', 'realisasi_biaya',
        'status', 'catatan_kepala_dinas', 'catatan_keuangan', 'approved_by_kepaladinas',
        'approved_at_kepaladinas', 'verified_by_keuangan', 'verified_at_keuangan',
        'submitted_at', 'created_by'
    ];

    protected $useTimestamps = true;
    protected $useSoftDeletes = true;

    protected $validationRules = [
        'no_sppd' => 'permit_empty|is_unique[sppd.no_sppd,id,{id}]',
        'sub_kegiatan_id' => 'required|numeric',
        'bidang_id' => 'required|numeric',
        'tipe_perjalanan' => 'required',
        'maksud_perjalanan' => 'required|min_length[20]',
        //'dasar_surat' => 'required',
        'tempat_berangkat' => 'required',
        'tempat_tujuan' => 'required',
        'tanggal_berangkat' => 'required|valid_date',
        'lama_perjalanan' => 'required|numeric|greater_than[0]',
        'penanggung_jawab' => 'required|numeric',
        'estimasi_biaya' => 'permit_empty|numeric|greater_than[0]',
    ];

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        if (!isset($data['data']['uuid']) || empty($data['data']['uuid'])) {
            $data['data']['uuid'] = $this->generateUUIDD();
        }
        return $data;
    }

    protected function generateUUIDD()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public function getWithRelations($id)
    {
        return $this->select('sppd.*, 
                              sub_kegiatan.nama_sub_kegiatan, sub_kegiatan.kode_sub_kegiatan,
                              kegiatan.nama_kegiatan, kegiatan.kode_kegiatan,
                              programs.nama_program, programs.kode_program,
                              bidang.nama_bidang, bidang.kode_bidang,
                              penanggung_jawab.nama as penanggung_jawab_nama,
                              creator.nama as created_by_nama')
            ->join('sub_kegiatan', 'sub_kegiatan.id = sppd.sub_kegiatan_id', 'left')
            ->join('kegiatan', 'kegiatan.id = sub_kegiatan.kegiatan_id', 'left')
            ->join('programs', 'programs.id = kegiatan.program_id', 'left')
            ->join('bidang', 'bidang.id = sppd.bidang_id', 'left')
            ->join('users as penanggung_jawab', 'penanggung_jawab.id = sppd.penanggung_jawab', 'left')
            ->join('users as creator', 'creator.id = sppd.created_by', 'left')
            ->where('sppd.id', $id)
            ->first();
    }

    public function getByBidang($bidangId, $status = null)
    {
        $builder = $this->where('bidang_id', $bidangId);
        if ($status) {
            $builder->where('status', $status);
        }
        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    public function getByPegawai($pegawaiId, $status = null)
    {
        $builder = $this->select('sppd.*')
            ->join('sppd_pegawai', 'sppd_pegawai.sppd_id = sppd.id')
            ->where('sppd_pegawai.pegawai_id', $pegawaiId)
            ->where('sppd.deleted_at', null);

        if ($status) {
            $builder->where('sppd.status', $status);
        }
        return $builder->orderBy('sppd.created_at', 'DESC')->findAll();
    }

    public function getByStatus($status, $bidangId = null)
    {
        $builder = $this->where('status', $status);
        if ($bidangId) {
            $builder->where('bidang_id', $bidangId);
        }
        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    public function submitSPPD($id)
    {
        return $this->update($id, [
            'status' => 'pending',
            'submitted_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function approveSPPD($id, $approvedBy, $noSPPD, $catatan = null)
    {
        return $this->update($id, [
            'status' => 'approved',
            'no_sppd' => $noSPPD,
            'approved_by_kepaladinas' => $approvedBy,
            'approved_at_kepaladinas' => date('Y-m-d H:i:s'),
            'catatan_kepala_dinas' => $catatan
        ]);
    }

    public function rejectSPPD($id, $catatan)
    {
        return $this->update($id, [
            'status' => 'rejected',
            'catatan_kepala_dinas' => $catatan
        ]);
    }

    public function submitForVerification($id)
    {
        return $this->update($id, [
            'status' => 'submitted',
            'submitted_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function verifySPPD($id, $verifiedBy, $catatan = null)
    {
        return $this->update($id, [
            'status' => 'verified',
            'verified_by_keuangan' => $verifiedBy,
            'verified_at_keuangan' => date('Y-m-d H:i:s'),
            'catatan_keuangan' => $catatan
        ]);
    }

    public function returnForRevision($id, $catatan)
    {
        return $this->update($id, [
            'status' => 'need_revision',
            'catatan_keuangan' => $catatan
        ]);
    }

    public function checkPegawaiOverlap($pegawaiId, $tanggalBerangkat, $tanggalKembali, $excludeSppdId = null)
    {
        $builder = $this->db->table('sppd')
            ->select('sppd.*')
            ->join('sppd_pegawai', 'sppd_pegawai.sppd_id = sppd.id')
            ->where('sppd_pegawai.pegawai_id', $pegawaiId)
            ->where('sppd.deleted_at', null)
            ->where("sppd.status NOT IN ('rejected', 'draft')");

        if (!empty($tanggalBerangkat) && !empty($tanggalKembali)) {
            $builder->groupStart()
                ->where('sppd.tanggal_berangkat <=', $tanggalKembali)
                ->where('sppd.tanggal_kembali >=', $tanggalBerangkat)
                ->groupEnd();
        }

        if ($excludeSppdId) {
            $builder->where('sppd.id !=', $excludeSppdId);
        }

        return $builder->get()->getResult();
    }

    public function getStatistics($bidangId = null, $tahun = null)
    {
        $builder = $this->where('deleted_at', null);

        if ($bidangId) {
            $builder->where('bidang_id', $bidangId);
        }

        if ($tahun) {
            $builder->where('YEAR(tanggal_berangkat)', $tahun);
        }

        return [
            'total_sppd' => $builder->countAllResults(false),
            'total_anggaran' => $builder->selectSum('estimasi_biaya')->get()->getRow()->estimasi_biaya ?? 0,
            'pending' => $this->where('status', 'pending')->where('deleted_at', null)->countAllResults(),
            'approved' => $this->where('status', 'approved')->where('deleted_at', null)->countAllResults(),
            'verified' => $this->where('status', 'verified')->where('deleted_at', null)->countAllResults(),
        ];
    }

    public function getDatatablesData($request)
    {
        $draw = $request['draw'];
        $start = $request['start'];
        $length = $request['length'];
        $searchValue = $request['search']['value'] ?? '';

        $totalRecords = $this->where('deleted_at', null)->countAllResults();

        $builder = $this->builder();
        $builder->select('sppd.*, 
                          bidang.nama_bidang,
                          programs.nama_program,
                          penanggung_jawab.nama as penanggung_jawab_nama,
                          COUNT(sppd_pegawai.pegawai_id) as jumlah_pegawai')
            ->join('bidang', 'bidang.id = sppd.bidang_id', 'left')
            ->join('sub_kegiatan', 'sub_kegiatan.id = sppd.sub_kegiatan_id', 'left')
            ->join('kegiatan', 'kegiatan.id = sub_kegiatan.kegiatan_id', 'left')
            ->join('programs', 'programs.id = kegiatan.program_id', 'left')
            ->join('users as penanggung_jawab', 'penanggung_jawab.id = sppd.penanggung_jawab', 'left')
            ->join('sppd_pegawai', 'sppd_pegawai.sppd_id = sppd.id', 'left')
            ->where('sppd.deleted_at', null)
            ->groupBy('sppd.id');

        if ($searchValue) {
            $builder->groupStart()
                ->like('sppd.no_sppd', $searchValue)
                ->orLike('sppd.tempat_tujuan', $searchValue)
                ->orLike('programs.nama_program', $searchValue)
                ->groupEnd();
        }

        if (isset($request['filters'])) {
            foreach ($request['filters'] as $key => $value) {
                if ($value !== null && $value !== '') {
                    $builder->where("sppd.{$key}", $value);
                }
            }
        }

        $totalFiltered = $builder->countAllResults(false);

        $data = $builder->orderBy('sppd.id', 'DESC')
            ->limit($length, $start)
            ->get()
            ->getResult();

        return [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ];
    }
}