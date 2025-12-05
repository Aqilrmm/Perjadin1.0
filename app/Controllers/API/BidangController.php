<?php

// ========================================
// BIDANG CONTROLLER (API)
// ========================================

namespace App\Controllers\API;

use App\Controllers\BaseController;

class BidangController extends BaseController
{
    protected $bidangModel;

    public function __construct()
    {
        $this->bidangModel = new \App\Models\Bidang\BidangModel();
    }

    /**
     * Get bidang options for Select2
     */
    public function options()
    {
        $search = $this->request->getGet('search');

        $builder = $this->bidangModel->where('is_active', 1)
                                     ->where('deleted_at', null);

        if ($search) {
            $builder->groupStart()
                    ->like('kode_bidang', $search)
                    ->orLike('nama_bidang', $search)
                    ->groupEnd();
        }

        $bidang = $builder->findAll();

        $results = [];
        foreach ($bidang as $b) {
            $results[] = [
                'id' => $b['id'],
                'text' => $b['nama_bidang'],
            ];
        }

        return $this->respond(['results' => $results]);
    }
}