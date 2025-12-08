<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{
    protected $allowedFields = [];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $dateFormat    = 'datetime';

    public function getAllPaginated($perPage = 10, $page = 1, $search = null, $filters = [])
    {
        $builder = $this->builder();

        if ($search && !empty($this->allowedFields)) {
            $builder->groupStart();
            foreach ($this->allowedFields as $field) {
                $builder->orLike($field, $search);
            }
            $builder->groupEnd();
        }

        foreach ($filters as $key => $value) {
            if ($value !== null && $value !== '') {
                $builder->where($key, $value);
            }
        }

        return [
            'data' => $builder->paginate($perPage, 'default', $page),
            'pager' => $this->pager
        ];
    }

    public function softDelete($id)
    {
        return $this->update($id, [$this->deletedField => date('Y-m-d H:i:s')]);
    }

    public function restore($id)
    {
        return $this->update($id, [$this->deletedField => null]);
    }

    public function getWithRelations($id)
    {
        return $this->find($id);
    }

    public function exists($id)
    {
        return $this->find($id) !== null;
    }

    public function getByUuid($uuid)
    {
        return $this->where('uuid', $uuid)->first();
    }

    public function isUnique($field, $value, $exceptId = null)
    {
        $builder = $this->builder();
        $builder->where($field, $value);

        if ($exceptId) {
            $builder->where($this->primaryKey . ' !=', $exceptId);
        }

        return $builder->countAllResults() === 0;
    }

    public function getDatatablesData($request)
    {
        $draw = $request['draw'];
        $start = $request['start'];
        $length = $request['length'];
        $searchValue = $request['search']['value'] ?? '';
        $orderColumnIndex = $request['order'][0]['column'] ?? 0;
        $orderDir = $request['order'][0]['dir'] ?? 'asc';

        $columns = array_column($request['columns'], 'data');
        $orderColumn = $columns[$orderColumnIndex] ?? $this->primaryKey;

        $totalRecords = $this->countAll();

        $builder = $this->builder();

        if ($searchValue) {
            $builder->groupStart();
            foreach ($this->allowedFields as $field) {
                $builder->orLike($field, $searchValue);
            }
            $builder->groupEnd();
        }

        if (isset($request['filters'])) {
            foreach ($request['filters'] as $key => $value) {
                if ($value !== null && $value !== '') {
                    $builder->where($key, $value);
                }
            }
        }

        $totalFiltered = $builder->countAllResults(false);

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

    public function bulkInsert($data)
    {
        return $this->insertBatch($data);
    }

    public function bulkUpdate($data)
    {
        return $this->updateBatch($data, $this->primaryKey);
    }

    public function getValidationRuless($context = null)
    {
        return $this->validationRules ?? [];
    }
}
