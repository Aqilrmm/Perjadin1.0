<?php

namespace App\Models\Program;

use App\Models\BaseModel;

class SubKegiatanModel extends BaseModel
{
    protected $table = 'sub_kegiatan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'uuid',
        'kegiatan_id',
        'kode_sub_kegiatan',
        'nama_sub_kegiatan',
        'anggaran_sub_kegiatan',
        'deskripsi',
        'status',
        'catatan_kepala_dinas',
        'approved_by',
        'approved_at',
        'submitted_at',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $useSoftDeletes = true;

    protected $validationRules = [
        'kegiatan_id' => 'required|numeric',
        'kode_sub_kegiatan' => 'required|min_length[5]|max_length[50]|is_unique[sub_kegiatan.kode_sub_kegiatan,id,{id}]',
        'nama_sub_kegiatan' => 'required|min_length[10]|max_length[255]',
        'anggaran_sub_kegiatan' => 'required|numeric|greater_than[0]',
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
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    public function getWithRelations($id)
    {
        return $this->select('sub_kegiatan.*, 
                              kegiatan.nama_kegiatan, kegiatan.kode_kegiatan,
                              programs.nama_program, programs.kode_program,
                              bidang.nama_bidang,
                              users.nama as created_by_name')
            ->join('kegiatan', 'kegiatan.id = sub_kegiatan.kegiatan_id', 'left')
            ->join('programs', 'programs.id = kegiatan.program_id', 'left')
            ->join('bidang', 'bidang.id = programs.bidang_id', 'left')
            ->join('users', 'users.id = sub_kegiatan.created_by', 'left')
            ->where('sub_kegiatan.id', $id)
            ->first();
    }

    public function getByKegiatan($kegiatanId, $status = null)
    {
        $builder = $this->where('kegiatan_id', $kegiatanId);

        if ($status) {
            $builder->where('status', $status);
        }

        return $builder->findAll();
    }

    public function getApprovedOptions($kegiatanId = null)
    {
        $builder = $this->select('sub_kegiatan.id, sub_kegiatan.kode_sub_kegiatan, 
                                  sub_kegiatan.nama_sub_kegiatan, sub_kegiatan.anggaran_sub_kegiatan,
                                  kegiatan.nama_kegiatan')
            ->join('kegiatan', 'kegiatan.id = sub_kegiatan.kegiatan_id')
            ->where('sub_kegiatan.status', 'approved')
            ->where('sub_kegiatan.deleted_at', null);

        if ($kegiatanId) {
            $builder->where('sub_kegiatan.kegiatan_id', $kegiatanId);
        }

        return $builder->findAll();
    }

    public function submitSubKegiatan($id)
    {
        return $this->update($id, [
            'status' => 'pending',
            'submitted_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function approveSubKegiatan($id, $approvedBy, $catatan = null)
    {
        return $this->update($id, [
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s'),
            'catatan_kepala_dinas' => $catatan
        ]);
    }

    public function rejectSubKegiatan($id, $catatan)
    {
        return $this->update($id, [
            'status' => 'rejected',
            'catatan_kepala_dinas' => $catatan
        ]);
    }

    public function getSisaAnggaran($subKegiatanId)
    {
        $subKegiatan = $this->find($subKegiatanId);
        if (!$subKegiatan) return 0;

        $db = \Config\Database::connect();
        $totalTerpakai = $db->table('sppd')
            ->selectSum('estimasi_biaya')
            ->where('sub_kegiatan_id', $subKegiatanId)
            ->where('deleted_at', null)
            ->whereNotIn('status', ['rejected'])
            ->get()
            ->getRow()
            ->estimasi_biaya ?? 0;

        return $subKegiatan['anggaran_sub_kegiatan'] - $totalTerpakai;
    }

    public function validateAnggaran($kegiatanId, $anggaranSubKegiatan, $subKegiatanId = null)
    {
        $kegiatanModel = new KegiatanModel();
        $sisaAnggaranKegiatan = $kegiatanModel->getSisaAnggaran($kegiatanId);

        if ($subKegiatanId) {
            $currentSubKegiatan = $this->find($subKegiatanId);
            $sisaAnggaranKegiatan += $currentSubKegiatan['anggaran_sub_kegiatan'];
        }

        return $anggaranSubKegiatan <= $sisaAnggaranKegiatan;
    }

    public function getWithBudgetInfo($kegiatanId = null)
    {
        $builder = $this->select('sub_kegiatan.*, 
                                  kegiatan.nama_kegiatan,
                                  COALESCE(SUM(sppd.estimasi_biaya), 0) as total_terpakai,
                                  (sub_kegiatan.anggaran_sub_kegiatan - COALESCE(SUM(sppd.estimasi_biaya), 0)) as sisa_anggaran')
            ->join('kegiatan', 'kegiatan.id = sub_kegiatan.kegiatan_id', 'left')
            ->join('sppd', 'sppd.sub_kegiatan_id = sub_kegiatan.id AND sppd.deleted_at IS NULL AND sppd.status != "rejected"', 'left')
            ->where('sub_kegiatan.deleted_at', null)
            ->groupBy('sub_kegiatan.id');

        if ($kegiatanId) {
            $builder->where('sub_kegiatan.kegiatan_id', $kegiatanId);
        }

        return $builder->findAll();
    }

    public function getDatatablesData($request)
    {
        $draw = $request['draw'];
        $start = $request['start'];
        $length = $request['length'];
        $searchValue = $request['search']['value'] ?? '';

        $totalRecords = $this->countAll();

        $builder = $this->builder();
        $builder->select('sub_kegiatan.*, 
                          kegiatan.nama_kegiatan, kegiatan.kode_kegiatan,
                          programs.nama_program,
                          users.nama as created_by_name,
                          COALESCE(SUM(sppd.estimasi_biaya), 0) as total_terpakai,
                          (sub_kegiatan.anggaran_sub_kegiatan - COALESCE(SUM(sppd.estimasi_biaya), 0)) as sisa_anggaran')
            ->join('kegiatan', 'kegiatan.id = sub_kegiatan.kegiatan_id', 'left')
            ->join('programs', 'programs.id = kegiatan.program_id', 'left')
            ->join('users', 'users.id = sub_kegiatan.created_by', 'left')
            ->join('sppd', 'sppd.sub_kegiatan_id = sub_kegiatan.id AND sppd.deleted_at IS NULL AND sppd.status != "rejected"', 'left')
            ->where('sub_kegiatan.deleted_at', null)
            ->groupBy('sub_kegiatan.id');

        if ($searchValue) {
            $builder->groupStart()
                ->like('sub_kegiatan.kode_sub_kegiatan', $searchValue)
                ->orLike('sub_kegiatan.nama_sub_kegiatan', $searchValue)
                ->orLike('kegiatan.nama_kegiatan', $searchValue)
                ->groupEnd();
        }

        if (isset($request['filters'])) {
            foreach ($request['filters'] as $key => $value) {
                if ($value !== null && $value !== '') {
                    $builder->where("sub_kegiatan.{$key}", $value);
                }
            }
        }

        $totalFiltered = $builder->countAllResults(false);

        $data = $builder->orderBy('sub_kegiatan.id', 'DESC')
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
