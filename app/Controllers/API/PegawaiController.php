<?php

// ========================================
// PEGAWAI CONTROLLER (API)
// ========================================

namespace App\Controllers\API;

use App\Controllers\BaseController;

class PegawaiController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new \App\Models\User\UserModel();
    }

    /**
     * Search pegawai for autocomplete/Select2
     */
    public function search()
    {
        $search = $this->request->getGet('q');
        $bidangId = $this->request->getGet('bidang_id');

        $builder = $this->userModel->select('users.id, users.nip_nik, users.nama, users.jabatan, bidang.nama_bidang')
                                   ->join('bidang', 'bidang.id = users.bidang_id', 'left')
                                   ->where('users.is_active', 1)
                                   ->where('users.is_blocked', 0)
                                   ->where('users.deleted_at', null);

        if ($bidangId) {
            $builder->where('users.bidang_id', $bidangId);
        }

        if ($search) {
            $builder->groupStart()
                    ->like('users.nama', $search)
                    ->orLike('users.nip_nik', $search)
                    ->groupEnd();
        }

        $pegawai = $builder->limit(20)->findAll();

        $results = [];
        foreach ($pegawai as $p) {
            $results[] = [
                'id' => $p['id'],
                'text' => get_nama_lengkap($p) . ' - ' . $p['nip_nik'],
                'jabatan' => $p['jabatan'],
                'bidang' => $p['nama_bidang'],
            ];
        }

        return $this->respond(['results' => $results]);
    }
}