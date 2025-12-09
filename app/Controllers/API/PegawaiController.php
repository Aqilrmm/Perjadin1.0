<?php

// ========================================
// PEGAWAI CONTROLLER (API)
// ========================================

namespace App\Controllers\API;

use App\Controllers\BaseController;

use App\Models\Bidang\BidangModel;

class PegawaiController extends BaseController
{
    protected $userModel;
    
    protected $bidangModel;

    public function __construct()
    {
        $this->userModel = new \App\Models\User\UserModel();
        
        $this->bidangModel = new BidangModel();
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
    public function listAll()
    {
        $search = $this->request->getGet('search');
        $bidangId = $this->request->getGet('bidang_id');
        
        $builder = $this->userModel->builder();
        
        // Base query: only active users with role pegawai
        $builder->where('users.is_active', 1)
                ->where('users.is_blocked', 0)
                ->where('users.deleted_at', null);
        
        // Join with bidang
        $builder->select('users.*, bidang.nama_bidang as bidang_nama');
        $builder->join('bidang', 'bidang.id = users.bidang_id', 'left');
        
        // Apply search filter
        if ($search) {
            $builder->groupStart()
                    ->like('users.nama', $search)
                    ->orLike('users.nip_nik', $search)
                    ->orLike('users.jabatan', $search)
                    ->orLike('bidang.nama_bidang', $search)
                    ->groupEnd();
        }
        
        // Apply bidang filter
        if ($bidangId) {
            $builder->where('users.bidang_id', $bidangId);
        }
        
        // Order by name
        $builder->orderBy('users.nama', 'ASC');
        
        $pegawai = $builder->get()->getResultArray();
        
        // Format for frontend
        $results = array_map(function($p) {
            return [
                'id' => $p['id'],
                'text' => $p['nama'],
                'nama' => $p['nama'],
                'nip' => $p['nip_nik'],
                'nip_nik' => $p['nip_nik'],
                'jabatan' => $p['jabatan'] ?? 'Pegawai',
                'bidang_nama' => $p['bidang_nama'] ?? '-',
                'bidang_id' => $p['bidang_id'],
                'role' => $p['role'],
                'email' => $p['email']
            ];
        }, $pegawai);
        
        return $this->respond([
            'status' => true,
            'data' => $results,
            'message' => 'Data pegawai berhasil dimuat',
            'total' => count($results)
        ]);
    }
     public function get($id)
    {
        $builder = $this->userModel->builder();
        $builder->select('users.*, bidang.nama_bidang as bidang_nama, bidang.kode_bidang');
        $builder->join('bidang', 'bidang.id = users.bidang_id', 'left');
        $builder->where('users.id', $id);
        
        $pegawai = $builder->get()->getRowArray();
        
        if (!$pegawai) {
            return $this->respondError('Pegawai tidak ditemukan', null, 404);
        }
        
        // Format response
        $data = [
            'id' => $pegawai['id'],
            'text' => $pegawai['nama'],
            'nama' => $pegawai['nama'],
            'nip' => $pegawai['nip_nik'],
            'nip_nik' => $pegawai['nip_nik'],
            'jabatan' => $pegawai['jabatan'] ?? 'Pegawai',
            'bidang_nama' => $pegawai['bidang_nama'] ?? '-',
            'bidang_id' => $pegawai['bidang_id'],
            'role' => $pegawai['role'],
            'email' => $pegawai['email'],
            'foto' => $pegawai['foto'] ?? null
        ];
        
        return $this->respondSuccess('Data pegawai berhasil dimuat', $data);
    }

    /**
     * Get pegawai by multiple IDs
     * POST /api/pegawai/get-multiple
     */
    public function getMultiple()
    {
        $ids = $this->request->getPost('ids');
        
        if (!$ids || !is_array($ids)) {
            return $this->respondError('IDs tidak valid', null, 400);
        }
        
        $builder = $this->userModel->builder();
        $builder->select('users.*, bidang.nama_bidang as bidang_nama, bidang.kode_bidang');
        $builder->join('bidang', 'bidang.id = users.bidang_id', 'left');
        $builder->whereIn('users.id', $ids);
        
        $pegawai = $builder->get()->getResultArray();
        
        // Format response
        $results = array_map(function($p) {
            return [
                'id' => $p['id'],
                'text' => $p['nama'],
                'nama' => $p['nama'],
                'nip' => $p['nip_nik'],
                'nip_nik' => $p['nip_nik'],
                'jabatan' => $p['jabatan'] ?? 'Pegawai',
                'bidang_nama' => $p['bidang_nama'] ?? '-',
                'bidang_id' => $p['bidang_id'],
                'role' => $p['role']
            ];
        }, $pegawai);
        
        return $this->respondSuccess('Data pegawai berhasil dimuat', $results);
    }

    /**
     * Get pegawai count by bidang
     * GET /api/pegawai/count-by-bidang
     */
    public function countByBidang()
    {
        $bidangId = $this->request->getGet('bidang_id');
        
        if (!$bidangId) {
            return $this->respondError('Bidang ID tidak valid', null, 400);
        }
        
        $count = $this->userModel->where('bidang_id', $bidangId)
                                  ->where('is_active', 1)
                                  ->where('is_blocked', 0)
                                  ->where('deleted_at', null)
                                  ->countAllResults();
        
        return $this->respondSuccess('Jumlah pegawai berhasil dihitung', [
            'bidang_id' => $bidangId,
            'count' => $count
        ]);
    }

    /**
     * Check pegawai availability (for overlap check)
     * POST /api/pegawai/check-availability
     */
    public function checkAvailability()
    {
        $pegawaiId = $this->request->getPost('pegawai_id');
        $tanggalBerangkat = $this->request->getPost('tanggal_berangkat');
        $tanggalKembali = $this->request->getPost('tanggal_kembali');
        $excludeSppdId = $this->request->getPost('exclude_sppd_id');
        
        if (!$pegawaiId || !$tanggalBerangkat || !$tanggalKembali) {
            return $this->respondError('Parameter tidak lengkap', null, 400);
        }
        
        $sppdModel = new \App\Models\SPPD\SPPDModel();
        $overlaps = $sppdModel->checkPegawaiOverlap(
            $pegawaiId,
            $tanggalBerangkat,
            $tanggalKembali,
            $excludeSppdId
        );
        
        return $this->respondSuccess('Pengecekan ketersediaan berhasil', [
            'pegawai_id' => $pegawaiId,
            'available' => empty($overlaps),
            'overlaps' => $overlaps
        ]);
    }
}