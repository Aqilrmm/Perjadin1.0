<?php

namespace App\Models\Bidang;

use App\Models\BaseModel;

class BidangModel extends BaseModel
{
    protected $table = 'bidang';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'uuid',
        'kode_bidang',
        'nama_bidang',
        'keterangan',
        'is_active'
    ];

    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'kode_bidang' => 'required|min_length[2]|max_length[10]|is_unique[bidang.kode_bidang,id,{id}]',
        'nama_bidang' => 'required|min_length[3]|max_length[255]|is_unique[bidang.nama_bidang,id,{id}]',
    ];

    protected $validationMessages = [
        'kode_bidang' => [
            'required' => 'Kode bidang wajib diisi',
            'min_length' => 'Kode bidang minimal 2 karakter',
            'is_unique' => 'Kode bidang sudah digunakan'
        ],
        'nama_bidang' => [
            'required' => 'Nama bidang wajib diisi',
            'min_length' => 'Nama bidang minimal 3 karakter',
            'is_unique' => 'Nama bidang sudah digunakan'
        ]
    ];

    protected $beforeInsert = ['generateUuid'];
    protected $beforeUpdate = [];

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

    public function getWithUserCount()
    {
        return $this->select('bidang.*, COUNT(users.id) as jumlah_pegawai')
            ->join('users', 'users.bidang_id = bidang.id', 'left')
            ->where('bidang.deleted_at', null)
            ->groupBy('bidang.id')
            ->findAll();
    }

    public function getActiveOptions()
    {
        return $this->select('id, nama_bidang')
            ->where('is_active', 1)
            ->where('deleted_at', null)
            ->findAll();
    }

    public function getByKode($kode)
    {
        return $this->where('kode_bidang', $kode)
            ->where('deleted_at', null)
            ->first();
    }

    public function hasUsers($bidangId)
    {
        $db = \Config\Database::connect();
        $count = $db->table('users')
            ->where('bidang_id', $bidangId)
            ->where('deleted_at', null)
            ->countAllResults();

        return $count > 0;
    }

    public function hasPrograms($bidangId)
    {
        $db = \Config\Database::connect();
        $count = $db->table('programs')
            ->where('bidang_id', $bidangId)
            ->where('deleted_at', null)
            ->countAllResults();

        return $count > 0;
    }

    public function activate($id)
    {
        return $this->update($id, ['is_active' => 1]);
    }

    public function deactivate($id)
    {
        return $this->update($id, ['is_active' => 0]);
    }

    public function getStatistics()
    {
        $db = \Config\Database::connect();

        return [
            'total_bidang' => $this->where('deleted_at', null)->countAllResults(),
            'active_bidang' => $this->where('is_active', 1)->where('deleted_at', null)->countAllResults(),
            'total_users' => $db->table('users')->where('deleted_at', null)->countAllResults(),
            'total_programs' => $db->table('programs')->where('deleted_at', null)->countAllResults(),
        ];
    }

    public function getDatatablesData($request)
    {
        $draw = $request['draw'];
        $start = $request['start'];
        $length = $request['length'];
        $searchValue = $request['search']['value'] ?? '';

        $totalRecords = $this->countAll();

        $builder = $this->builder();
        $builder->select('bidang.*, COUNT(users.id) as jumlah_pegawai')
            ->join('users', 'users.bidang_id = bidang.id AND users.deleted_at IS NULL', 'left')
            ->where('bidang.deleted_at', null)
            ->groupBy('bidang.id');

        if ($searchValue) {
            $builder->groupStart()
                ->like('bidang.kode_bidang', $searchValue)
                ->orLike('bidang.nama_bidang', $searchValue)
                ->orLike('bidang.keterangan', $searchValue)
                ->groupEnd();
        }

        if (isset($request['filters']['is_active']) && $request['filters']['is_active'] !== '') {
            $builder->where('bidang.is_active', $request['filters']['is_active']);
        }

        $totalFiltered = $builder->countAllResults(false);

        $data = $builder->orderBy('bidang.id', 'DESC')
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
