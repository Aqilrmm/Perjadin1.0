<?php

namespace App\Models\User;

use App\Models\BaseModel;

class UserModel extends BaseModel
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'uuid',
        'nip_nik',
        'gelar_depan',
        'nama',
        'gelar_belakang',
        'jenis_pegawai',
        'email',
        'password',
        'jabatan',
        'bidang_id',
        'role',
        'pangkat_golongan',
        'foto',
        'is_active',
        'is_blocked',
        'blocked_reason',
        'blocked_at',
        'blocked_by',
        'login_attempts',
        'last_login'
    ];

    protected $useTimestamps = true;
    protected $useSoftDeletes = false;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'nip_nik' => 'required|min_length[16]|max_length[18]|is_unique[users.nip_nik,id,{id}]',
        'nama' => 'required|min_length[3]|max_length[255]',
        'jenis_pegawai' => 'required|in_list[ASN,Non-ASN]',
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'permit_empty|min_length[8]',
        'jabatan' => 'required|min_length[3]',
        'role' => 'required|in_list[superadmin,kepaladinas,kepalabidang,pegawai,keuangan]'
    ];

    protected $validationMessages = [
        'nip_nik' => [
            'required' => 'NIP/NIK wajib diisi',
            'min_length' => 'NIP/NIK minimal 16 digit',
            'max_length' => 'NIP/NIK maksimal 18 digit',
            'is_unique' => 'NIP/NIK sudah digunakan'
        ],
        'email' => [
            'required' => 'Email wajib diisi',
            'valid_email' => 'Format email tidak valid',
            'is_unique' => 'Email sudah digunakan'
        ],
        'password' => [
            'required' => 'Password wajib diisi',
            'min_length' => 'Password minimal 8 karakter'
        ]
    ];

    protected $beforeInsert = ['generateUuid', 'hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Generate UUID before insert
     */
    protected function generateUuid(array $data)
    {
        if (!isset($data['data']['uuid']) || empty($data['data']['uuid'])) {
            $data['data']['uuid'] = $this->generateUUIDD();
        }
        return $data;
    }
    public function getKepalaDinas()
    {
        return $this->where('role', 'kepaladinas')
                    ->first();
    }

    /**
     * Hash password before insert/update
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
        }
        return $data;
    }

    /**
     * Generate UUID v4
     */
    public function generateUUIDD()
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
     * Get user with bidang relation
     */
    public function getWithRelations($id)
    {
        return $this->select('users.*, bidang.nama_bidang, bidang.kode_bidang')
            ->join('bidang', 'bidang.id = users.bidang_id', 'left')
            ->where('users.id', $id)
            ->first();
    }

    /**
     * Get all users with bidang
     */
    public function getAllWithBidang()
    {
        return $this->select('users.*, bidang.nama_bidang')
            ->join('bidang', 'bidang.id = users.bidang_id', 'left')
            ->findAll();
    }

    /**
     * Get users by role
     */
    public function getByRole($role)
    {
        return $this->where('role', $role)
            ->where('is_active', 1)
            ->where('is_blocked', 0)
            ->findAll();
    }

    /**
     * Get users by bidang
     */
    public function getByBidang($bidangId)
    {
        return $this->where('bidang_id', $bidangId)
            ->where('is_active', 1)
            ->where('is_blocked', 0)
            ->findAll();
    }

    /**
     * Block user
     */
    public function blockUser($id, $reason, $blockedBy)
    {
        return $this->update($id, [
            'is_blocked' => 1,
            'blocked_reason' => $reason,
            'blocked_at' => date('Y-m-d H:i:s'),
            'blocked_by' => $blockedBy
        ]);
    }

    /**
     * Unblock user
     */
    public function unblockUser($id)
    {
        return $this->update($id, [
            'is_blocked' => 0,
            'blocked_reason' => null,
            'blocked_at' => null,
            'blocked_by' => null,
            'login_attempts' => 0
        ]);
    }

    /**
     * Reset login attempts
     */
    public function resetLoginAttempts($id)
    {
        return $this->update($id, ['login_attempts' => 0]);
    }

    /**
     * Get blocked users
     */
    public function getBlockedUsers()
    {
        return $this->select('users.*, blocker.nama as blocked_by_name')
            ->join('users as blocker', 'blocker.id = users.blocked_by', 'left')
            ->where('users.is_blocked', 1)
            ->findAll();
    }

    /**
     * Return validation rules for this model or a specific context
     *
     * @param string|null $context
     * @return array
     */
    public function getValidationRuless($context = null)
    {
        if ($context === 'block') {
            return [
                'user_id' => 'required|numeric',
                'reason' => 'required|min_length[10]'
            ];
        }

        return $this->validationRules ?? [];
    }

    /**
     * Get DataTables data for users
     */
    public function getDatatablesData($request)
    {
        $draw = $request['draw'];
        $start = isset($request['start']) ? (int) $request['start'] : 0;
        $length = isset($request['length']) ? (int) $request['length'] : 10;
        $searchValue = $request['search']['value'] ?? '';

        // Total records
        $totalRecords = $this->countAll();

        // Build query
        $builder = $this->builder();
        $builder->select('users.*, bidang.nama_bidang')
            ->join('bidang', 'bidang.id = users.bidang_id', 'left');

        // No soft-delete filtering; hard-deletes are used so show all existing rows

        // Apply search
        if ($searchValue) {
            $builder->groupStart()
                ->like('users.nip_nik', $searchValue)
                ->orLike('users.nama', $searchValue)
                ->orLike('users.email', $searchValue)
                ->orLike('users.jabatan', $searchValue)
                ->groupEnd();
        }

        // Apply filters
        if (isset($request['filters'])) {
            foreach ($request['filters'] as $key => $value) {
                if ($value !== null && $value !== '') {
                    $builder->where("users.{$key}", $value);
                }
            }
        }

        // Count filtered records
        $totalFiltered = $builder->countAllResults(false);

        // Get data
        $data = $builder->orderBy('users.id', 'DESC')
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
