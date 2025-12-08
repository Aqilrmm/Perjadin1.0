<?php

namespace App\Models\Log;

use App\Models\BaseModel;

class SecurityLogModel extends BaseModel
{
    protected $table = 'security_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent'
    ];

    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $createdField = 'created_at';

    public function logActivity($userId, $action, $description = null, $ipAddress = null, $userAgent = null)
    {
        return $this->insert([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => $ipAddress ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            'user_agent' => $userAgent ?? $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ]);
    }

    public function getByUser($userId, $limit = 50)
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function getByAction($action, $limit = 50)
    {
        return $this->where('action', $action)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function getByDateRange($startDate, $endDate)
    {
        return $this->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getFailedLoginAttempts($hours = 24)
    {
        $since = date('Y-m-d H:i:s', strtotime("-{$hours} hours"));

        return $this->where('action', 'LOGIN_FAILED')
            ->where('created_at >=', $since)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getStatistics($startDate = null, $endDate = null)
    {
        $builder = $this->builder();

        if ($startDate) {
            $builder->where('created_at >=', $startDate);
        }

        if ($endDate) {
            $builder->where('created_at <=', $endDate);
        }

        return [
            'total_logs' => $builder->countAllResults(false),
            'login_success' => $this->where('action', 'LOGIN_SUCCESS')->countAllResults(),
            'login_failed' => $this->where('action', 'LOGIN_FAILED')->countAllResults(),
            'auto_blocked' => $this->where('action', 'AUTO_BLOCKED')->countAllResults(),
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
        $builder->select('security_logs.*, users.nama, users.nip_nik')
            ->join('users', 'users.id = security_logs.user_id', 'left');

        if ($searchValue) {
            $builder->groupStart()
                ->like('security_logs.action', $searchValue)
                ->orLike('security_logs.description', $searchValue)
                ->orLike('users.nama', $searchValue)
                ->groupEnd();
        }

        if (isset($request['filters'])) {
            foreach ($request['filters'] as $key => $value) {
                if ($value !== null && $value !== '') {
                    if ($key === 'date_range') {
                        $dates = explode(' to ', $value);
                        if (count($dates) === 2) {
                            $builder->where('security_logs.created_at >=', $dates[0]);
                            $builder->where('security_logs.created_at <=', $dates[1] . ' 23:59:59');
                        }
                    } else {
                        $builder->where("security_logs.{$key}", $value);
                    }
                }
            }
        }

        $totalFiltered = $builder->countAllResults(false);

        $data = $builder->orderBy('security_logs.created_at', 'DESC')
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

    public function cleanOldLogs($days = 365)
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        return $this->where('created_at <', $date)->delete();
    }
}
