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
        'bidang_id',
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
        'kode_kegiatan' => 'required',
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

    /**
     * Get kegiatan with relations
     */
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

    /**
     * Get kegiatan by program
     */
    public function getByProgram($programId, $status = null)
    {
        $builder = $this->where('program_id', $programId);

        if ($status) {
            $builder->where('status', $status);
        }

        return $builder->findAll();
    }

    /**
     * Get approved kegiatan for dropdown
     */
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

    /**
     * Submit kegiatan
     */
    public function submitKegiatan($id)
    {
        return $this->update($id, [
            'status' => 'pending',
            'submitted_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Approve kegiatan
     */
    public function approveKegiatan($id, $approvedBy, $catatan = null)
    {
        return $this->update($id, [
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s'),
            'catatan_kepala_dinas' => $catatan
        ]);
    }

    /**
     * Reject kegiatan
     */
    public function rejectKegiatan($id, $catatan)
    {
        return $this->update($id, [
            'status' => 'rejected',
            'catatan_kepala_dinas' => $catatan
        ]);
    }

    /**
     * Get sisa anggaran kegiatan
     */
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

    /**
     * Validate anggaran tidak melebihi program
     */
    public function validateAnggaran($programId, $anggaranKegiatan, $kegiatanId = null)
    {
        $programModel = new \App\Models\Program\ProgramModel();
        $sisaAnggaranProgram = $programModel->getSisaAnggaran($programId);

        // If updating, add back current kegiatan anggaran
        if ($kegiatanId) {
            $currentKegiatan = $this->find($kegiatanId);
            $sisaAnggaranProgram += $currentKegiatan['anggaran_kegiatan'];
        }

        return $anggaranKegiatan <= $sisaAnggaranProgram;
    }

    /**
     * Get kegiatan with budget info
     */
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

    /**
     * Datatables server-side with program join and budget aggregation
     */
    public function getDatatablesWithBudget(array $request)
    {
        $draw = isset($request['draw']) ? (int) $request['draw'] : 0;
        $start = isset($request['start']) ? (int) $request['start'] : 0;
        $length = isset($request['length']) ? (int) $request['length'] : 10;
        $searchValue = $request['search']['value'] ?? '';
        $orderColumnIndex = $request['order'][0]['column'] ?? 0;
        $orderDir = $request['order'][0]['dir'] ?? 'asc';

        // Define columns mapping for ordering
        $columns = array_column($request['columns'], 'data');
        $requestedOrderColumn = $columns[$orderColumnIndex] ?? 'kegiatan.id';

        // Map request column names to real DB columns to avoid ambiguous/virtual names
        $orderColumnMap = [
            'kode_kegiatan' => 'kegiatan.kode_kegiatan',
            'nama_kegiatan' => 'kegiatan.nama_kegiatan',
            'nama_program' => 'programs.nama_program',
            'anggaran_formatted' => 'kegiatan.anggaran_kegiatan',
            'anggaran_kegiatan' => 'kegiatan.anggaran_kegiatan',
            'sisa_formatted' => 'sisa_anggaran',
        ];

        $orderColumn = $orderColumnMap[$requestedOrderColumn] ?? $requestedOrderColumn;

        // Base builder with joins and aggregation
        $builder = $this->select('kegiatan.*, programs.nama_program, programs.kode_program, 
                                  COALESCE(SUM(sub_kegiatan.anggaran_sub_kegiatan), 0) as total_terpakai, 
                                  (kegiatan.anggaran_kegiatan - COALESCE(SUM(sub_kegiatan.anggaran_sub_kegiatan), 0)) as sisa_anggaran')
            ->join('programs', 'programs.id = kegiatan.program_id', 'left')
            ->join('sub_kegiatan', 'sub_kegiatan.kegiatan_id = kegiatan.id AND sub_kegiatan.deleted_at IS NULL', 'left')
            ->where('kegiatan.deleted_at', null)
            ->groupBy('kegiatan.id');

        // Apply filters
        if (isset($request['filters'])) {
            foreach ($request['filters'] as $key => $value) {
                if ($value !== null && $value !== '') {
                    // qualify filters to kegiatan table to avoid ambiguous column names
                    $field = (strpos($key, '.') !== false) ? $key : ('kegiatan.' . $key);
                    $builder->where($field, $value);
                }
            }
        }

        // Apply search (search in some key fields)
        if ($searchValue) {
            $builder->groupStart()
                ->like('kegiatan.kode_kegiatan', $searchValue)
                ->orLike('kegiatan.nama_kegiatan', $searchValue)
                ->orLike('programs.nama_program', $searchValue)
                ->groupEnd();
        }

        // Total records (unfiltered) - qualify deleted_at to avoid ambiguity
        $totalRecords = $this->builder()->where('kegiatan.deleted_at', null)->countAllResults(false);

        // Count filtered
        $totalFiltered = $builder->countAllResults(false);

        // Fetch data
        $data = $builder->orderBy($orderColumn, $orderDir)
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
