<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class BaseModel
 *
 * Extended Model with common CRUD operations
 */
class BaseModel extends Model
{
    protected $allowedFields = [];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $dateFormat    = 'datetime';

    /**
     * Get all with pagination
     *
     * @param int $perPage
     * @param int $page
     * @param string|null $search
     * @param array $filters
     * @return array
     */
    public function getAllPaginated($perPage = 10, $page = 1, $search = null, $filters = [])
    {
        $builder = $this->builder();

        // Apply search
        if ($search && !empty($this->allowedFields)) {
            $builder->groupStart();
            foreach ($this->allowedFields as $field) {
                $builder->orLike($field, $search);
            }
            $builder->groupEnd();
        }

        // Apply filters
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

    /**
     * Soft delete record
     *
     * @param int $id
     * @return bool
     */
    public function softDelete($id)
    {
        return $this->update($id, [$this->deletedField => date('Y-m-d H:i:s')]);
    }

    /**
     * Restore soft deleted record
     *
     * @param int $id
     * @return bool
     */
    public function restore($id)
    {
        return $this->update($id, [$this->deletedField => null]);
    }

    /**
     * Get record with relations
     * Override in child models
     *
     * @param int $id
     * @return array|object|null
     */
    public function getWithRelations($id)
    {
        return $this->find($id);
    }

    /**
     * Check if record exists
     *
     * @param int $id
     * @return bool
     */
    public function exists($id)
    {
        return $this->find($id) !== null;
    }

    /**
     * Get by UUID
     *
     * @param string $uuid
     * @return array|object|null
     */
    public function getByUuid($uuid)
    {
        return $this->where('uuid', $uuid)->first();
    }

    /**
     * Check if field value is unique
     *
     * @param string $field
     * @param mixed $value
     * @param int|null $exceptId
     * @return bool
     */
    public function isUnique($field, $value, $exceptId = null)
    {
        $builder = $this->builder();
        $builder->where($field, $value);

        if ($exceptId) {
            $builder->where($this->primaryKey . ' !=', $exceptId);
        }

        return $builder->countAllResults() === 0;
    }

    /**
     * Get DataTables data (server-side)
     *
     * @param array $request
     * @return array
     */
    public function getDatatablesData($request)
    {
        $draw = $request['draw'];
        $start = $request['start'];
        $length = $request['length'];
        $searchValue = $request['search']['value'] ?? '';
        $orderColumnIndex = $request['order'][0]['column'] ?? 0;
        $orderDir = $request['order'][0]['dir'] ?? 'asc';

        // Get columns
        $columns = array_column($request['columns'], 'data');
        $orderColumn = $columns[$orderColumnIndex] ?? $this->primaryKey;

        // Total records
        $totalRecords = $this->countAll();

        // Build query
        $builder = $this->builder();

        // Apply search
        if ($searchValue) {
            $builder->groupStart();
            foreach ($this->allowedFields as $field) {
                $builder->orLike($field, $searchValue);
            }
            $builder->groupEnd();
        }

        // Apply filters if provided
        if (isset($request['filters'])) {
            foreach ($request['filters'] as $key => $value) {
                if ($value !== null && $value !== '') {
                    $builder->where($key, $value);
                }
            }
        }

        // Count filtered records
        $totalFiltered = $builder->countAllResults(false);

        // Get data
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

    /**
     * Bulk insert
     *
     * @param array $data
     * @return bool
     */
    public function bulkInsert($data)
    {
        return $this->insertBatch($data);
    }
}

// End of BaseModel
