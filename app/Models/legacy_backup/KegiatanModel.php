<?php

namespace App\Models\Program;

use App\Models\BaseModel;

class KegiatanModel extends BaseModel
{
    protected $table = 'kegiatan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'uuid',
        'program_id',
        'kode_kegiatan',
        'nama_kegiatan',
        'anggaran_kegiatan',
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
        'program_id' => 'required|numeric',
        'kode_kegiatan' => 'required|min_length[5]|max_length[50]|is_unique[kegiatan.kode_kegiatan,id,{id}]',
        'nama_kegiatan' => 'required|min_length[10]|max_length[255]',
        'anggaran_kegiatan' => 'required|numeric|greater_than[0]',
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
        return $this->select('kegiatan.*, 
                              programs.nama_program, programs.kode_program,
                              bidang.nama_bidang,
                              users.nama as created_by_name')
            ->join('programs', 'programs.id = kegiatan.program_id', 'left')
            ->join('bidang', 'bidang.id = programs.bidang_id', 'left')
            ->join('users', 'users.id = kegiatan.created_by', 'left')
            ->where('kegiatan.id', $id)
            ->first();
    }

    public function getByProgram($programId, $status = null)
    {
        $builder = $this->where('program_id', $programId);

        if ($status) {
            $builder->where('status', $status);
        }

        return $builder->findAll();
    }

    public function getApprovedOptions($programId = null)
    {
        $builder = $this->select('kegiatan.id, kegiatan.kode_kegiatan, kegiatan.nama_kegiatan, 
                                  kegiatan.anggaran_kegiatan, programs.nama_program')
            ->join('programs', 'programs.id = kegiatan.program_id')
            ->where('kegiatan.status', 'approved')
            ->where('kegiatan.deleted_at', null);

        if ($programId) {
            $builder->where('kegiatan.program_id', $programId);
        }

        return $builder->findAll();
    }

    public function submitKegiatan($id)
    {
        return $this->update($id, [
            'status' => 'pending',
            'submitted_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function approveKegiatan($id, $approvedBy, $catatan = null)
    {
        return $this->update($id, [
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s'),
            'catatan_kepala_dinas' => $catatan
        ]);
    }

    public function rejectKegiatan($id, $catatan)
    {
        return $this->update($id, [
            'status' => 'rejected',
            'catatan_kepala_dinas' => $catatan
        ]);
    }

    public function getSisaAnggaran($kegiatanId)
    {
        $kegiatan = $this->find($kegiatanId);
        if (!$kegiatan) return 0;

        $db = \Config\Database::connect();
        $totalTerpakai = $db->table('sub_kegiatan')
            ->selectSum('anggaran_sub_kegiatan')
            ->where('kegiatan_id', $kegiatanId)
            ->where('deleted_at', null)
            ->get()
            ->getRow()
            ->anggaran_sub_kegiatan ?? 0;

        return $kegiatan['anggaran_kegiatan'] - $totalTerpakai;
    }

    public function validateAnggaran($programId, $anggaranKegiatan, $kegiatanId = null)
    {
        $programModel = new \App\Models\Program\ProgramModel();
        $sisaAnggaranProgram = $programModel->getSisaAnggaran($programId);

        if ($kegiatanId) {
            $currentKegiatan = $this->find($kegiatanId);
            $sisaAnggaranProgram += $currentKegiatan['anggaran_kegiatan'];
        }

        return $anggaranKegiatan <= $sisaAnggaranProgram;
    }

    public function getWithBudgetInfo($programId = null)
    {
        $builder = $this->select('kegiatan.*, 
                                  programs.nama_program,
                                  COALESCE(SUM(sub_kegiatan.anggaran_sub_kegiatan), 0) as total_terpakai,
                                  (kegiatan.anggaran_kegiatan - COALESCE(SUM(sub_kegiatan.anggaran_sub_kegiatan), 0)) as sisa_anggaran')
            ->join('programs', 'programs.id = kegiatan.program_id', 'left')
            ->join('sub_kegiatan', 'sub_kegiatan.kegiatan_id = kegiatan.id AND sub_kegiatan.deleted_at IS NULL', 'left')
            ->where('kegiatan.deleted_at', null)
            ->groupBy('kegiatan.id');

        if ($programId) {
            $builder->where('kegiatan.program_id', $programId);
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
        $builder->select('kegiatan.*, 
                          programs.nama_program, programs.kode_program,
                          users.nama as created_by_name,
                          COALESCE(SUM(sub_kegiatan.anggaran_sub_kegiatan), 0) as total_terpakai,
                          (kegiatan.anggaran_kegiatan - COALESCE(SUM(sub_kegiatan.anggaran_sub_kegiatan), 0)) as sisa_anggaran')
            ->join('programs', 'programs.id = kegiatan.program_id', 'left')
            ->join('users', 'users.id = kegiatan.created_by', 'left')
            ->join('sub_kegiatan', 'sub_kegiatan.kegiatan_id = kegiatan.id AND sub_kegiatan.deleted_at IS NULL', 'left')
            ->where('kegiatan.deleted_at', null)
            ->groupBy('kegiatan.id');

        if ($searchValue) {
            $builder->groupStart()
                ->like('kegiatan.kode_kegiatan', $searchValue)
                ->orLike('kegiatan.nama_kegiatan', $searchValue)
                ->orLike('programs.nama_program', $searchValue)
                ->groupEnd();
        }

        if (isset($request['filters'])) {
            foreach ($request['filters'] as $key => $value) {
                if ($value !== null && $value !== '') {
                    $builder->where("kegiatan.{$key}", $value);
                }
            }
        }

        $totalFiltered = $builder->countAllResults(false);

        $data = $builder->orderBy('kegiatan.id', 'DESC')
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
