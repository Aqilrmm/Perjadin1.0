<?php

namespace App\Models\Program;

use App\Models\BaseModel;

class ProgramModel extends BaseModel
{
    protected $table = 'programs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'uuid',
        'kode_program',
        'nama_program',
        'bidang_id',
        'tahun_anggaran',
        'jumlah_anggaran',
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
        'kode_program' => 'required|min_length[5]|max_length[50]|is_unique[programs.kode_program,id,{id}]',
        'nama_program' => 'required|min_length[10]|max_length[255]',
        'bidang_id' => 'required|numeric',
        'tahun_anggaran' => 'required|numeric',
        'jumlah_anggaran' => 'required|numeric|greater_than[1000000]',
    ];

    protected $validationMessages = [
        'kode_program' => [
            'required' => 'Kode program wajib diisi',
            'is_unique' => 'Kode program sudah digunakan'
        ],
        'nama_program' => [
            'required' => 'Nama program wajib diisi',
            'min_length' => 'Nama program minimal 10 karakter'
        ],
        'jumlah_anggaran' => [
            'required' => 'Jumlah anggaran wajib diisi',
            'greater_than' => 'Jumlah anggaran minimal Rp 1.000.000'
        ]
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

    /**
     * Get program with relations
     */
    public function getWithRelations($id)
    {
        return $this->select('programs.*, bidang.nama_bidang, bidang.kode_bidang, 
                              users.nama as created_by_name, 
                              approver.nama as approved_by_name')
            ->join('bidang', 'bidang.id = programs.bidang_id', 'left')
            ->join('users', 'users.id = programs.created_by', 'left')
            ->join('users as approver', 'approver.id = programs.approved_by', 'left')
            ->where('programs.id', $id)
            ->first();
    }

    /**
     * Get programs by bidang
     */
    public function getByBidang($bidangId, $status = null)
    {
        $builder = $this->where('bidang_id', $bidangId);

        if ($status) {
            $builder->where('status', $status);
        }

        return $builder->findAll();
    }

    /**
     * Get programs by status
     */
    public function getByStatus($status, $bidangId = null)
    {
        $builder = $this->where('status', $status);

        if ($bidangId) {
            $builder->where('bidang_id', $bidangId);
        }

        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Get approved programs for dropdown
     */
    public function getApprovedOptions($bidangId = null)
    {
        $builder = $this->select('id, kode_program, nama_program, jumlah_anggaran')
            ->where('status', 'approved')
            ->where('deleted_at', null);

        if ($bidangId) {
            $builder->where('bidang_id', $bidangId);
        }

        return $builder->findAll();
    }

    /**
     * Submit program for approval
     */
    public function submitProgram($id)
    {
        return $this->update($id, [
            'status' => 'pending',
            'submitted_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Approve program
     */
    public function approveProgram($id, $approvedBy, $catatan = null)
    {
        return $this->update($id, [
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s'),
            'catatan_kepala_dinas' => $catatan
        ]);
    }

    /**
     * Reject program
     */
    public function rejectProgram($id, $catatan)
    {
        return $this->update($id, [
            'status' => 'rejected',
            'catatan_kepala_dinas' => $catatan
        ]);
    }

    /**
     * Get sisa anggaran program
     */
    public function getSisaAnggaran($programId)
    {
        $program = $this->find($programId);
        if (!$program) return 0;

        $db = \Config\Database::connect();
        $totalTerpakai = $db->table('kegiatan')
            ->selectSum('anggaran_kegiatan')
            ->where('program_id', $programId)
            ->where('deleted_at', null)
            ->get()
            ->getRow()
            ->anggaran_kegiatan ?? 0;

        return $program['jumlah_anggaran'] - $totalTerpakai;
    }

    /**
     * Get programs with budget info
     */
    public function getWithBudgetInfo($bidangId = null)
    {
        $builder = $this->select('programs.*, 
                                  bidang.nama_bidang,
                                  COALESCE(SUM(kegiatan.anggaran_kegiatan), 0) as total_terpakai,
                                  (programs.jumlah_anggaran - COALESCE(SUM(kegiatan.anggaran_kegiatan), 0)) as sisa_anggaran')
            ->join('bidang', 'bidang.id = programs.bidang_id', 'left')
            ->join('kegiatan', 'kegiatan.program_id = programs.id AND kegiatan.deleted_at IS NULL', 'left')
            ->where('programs.deleted_at', null)
            ->groupBy('programs.id');

        if ($bidangId) {
            $builder->where('programs.bidang_id', $bidangId);
        }

        return $builder->findAll();
    }

    /**
     * Get statistics by bidang
     */
    public function getStatisticsByBidang($bidangId, $tahunAnggaran = null)
    {
        $builder = $this->where('bidang_id', $bidangId);

        if ($tahunAnggaran) {
            $builder->where('tahun_anggaran', $tahunAnggaran);
        }

        return [
            'total_program' => $builder->countAllResults(false),
            'total_anggaran' => $builder->selectSum('jumlah_anggaran')->get()->getRow()->jumlah_anggaran ?? 0,
            'approved' => $this->where('bidang_id', $bidangId)
                ->where('status', 'approved')
                ->countAllResults(),
            'pending' => $this->where('bidang_id', $bidangId)
                ->where('status', 'pending')
                ->countAllResults(),
        ];
    }

    /**
     * Get DataTables data
     */
    public function getDatatablesData($request)
    {
        $draw = $request['draw'];
        $start = $request['start'];
        $length = $request['length'];
        $searchValue = $request['search']['value'] ?? '';

        $totalRecords = $this->countAll();

        $builder = $this->builder();
        $builder->select('programs.*, 
                          bidang.nama_bidang,
                          users.nama as created_by_name,
                          COALESCE(SUM(kegiatan.anggaran_kegiatan), 0) as total_terpakai,
                          (programs.jumlah_anggaran - COALESCE(SUM(kegiatan.anggaran_kegiatan), 0)) as sisa_anggaran')
            ->join('bidang', 'bidang.id = programs.bidang_id', 'left')
            ->join('users', 'users.id = programs.created_by', 'left')
            ->join('kegiatan', 'kegiatan.program_id = programs.id AND kegiatan.deleted_at IS NULL', 'left')
            ->where('programs.deleted_at', null)
            ->groupBy('programs.id');

        // Apply search
        if ($searchValue) {
            $builder->groupStart()
                ->like('programs.kode_program', $searchValue)
                ->orLike('programs.nama_program', $searchValue)
                ->orLike('bidang.nama_bidang', $searchValue)
                ->groupEnd();
        }

        // Apply filters
        if (isset($request['filters'])) {
            foreach ($request['filters'] as $key => $value) {
                if ($value !== null && $value !== '') {
                    $builder->where("programs.{$key}", $value);
                }
            }
        }

        $totalFiltered = $builder->countAllResults(false);

        $data = $builder->orderBy('programs.id', 'DESC')
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
