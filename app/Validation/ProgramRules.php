<?php

namespace App\Validation;

/**
 * Custom Validation Rules for Program, Kegiatan, Sub Kegiatan
 */
class ProgramRules
{
    /**
     * Validate kode program format
     */
    public function valid_kode_program(string $str, string &$error = null): bool
    {
        if (strlen($str) < 5) {
            $error = 'Kode program minimal 5 karakter';
            return false;
        }

        if (strlen($str) > 50) {
            $error = 'Kode program maksimal 50 karakter';
            return false;
        }

        // Check if contains only alphanumeric, dash, and underscore
        if (!preg_match('/^[A-Z0-9\-_]+$/', $str)) {
            $error = 'Kode program hanya boleh berisi huruf besar, angka, dash (-), dan underscore (_)';
            return false;
        }

        return true;
    }

    /**
     * Check if kode is unique
     */
    public function unique_kode(string $str, string $params, array $data, string &$error = null): bool
    {
        // Parse params: table,field,ignore_id
        $params = explode(',', $params);
        $table = $params[0] ?? 'programs';
        $field = $params[1] ?? 'kode_program';
        $ignoreId = $params[2] ?? null;

        $db = \Config\Database::connect();
        $builder = $db->table($table);
        
        $builder->where($field, $str);
        $builder->where('deleted_at', null);
        
        if ($ignoreId) {
            $builder->where('id !=', $ignoreId);
        }

        if ($builder->countAllResults() > 0) {
            $error = 'Kode sudah digunakan';
            return false;
        }

        return true;
    }

    /**
     * Validate anggaran minimum
     */
    public function min_anggaran(string $str, string $min, array $data, string &$error = null): bool
    {
        $amount = floatval($str);
        $minAmount = floatval($min);

        if ($amount < $minAmount) {
            $error = 'Jumlah anggaran minimal Rp ' . number_format($minAmount, 0, ',', '.');
            return false;
        }

        return true;
    }

    /**
     * Validate anggaran tidak melebihi parent
     */
    public function anggaran_tidak_melebihi_parent(string $str, string $params, array $data, string &$error = null): bool
    {
        // Parse params: parent_type,parent_id
        $params = explode(',', $params);
        $parentType = $params[0] ?? 'program'; // program, kegiatan
        $parentId = $data[$params[1]] ?? null;

        if (!$parentId) {
            return true; // Skip if no parent specified
        }

        $anggaran = floatval($str);
        $db = \Config\Database::connect();

        // Get sisa anggaran parent
        $sisaAnggaran = 0;

        if ($parentType === 'program') {
            $programModel = new \App\Models\Program\ProgramModel();
            $sisaAnggaran = $programModel->getSisaAnggaran($parentId);
        } elseif ($parentType === 'kegiatan') {
            $kegiatanModel = new \App\Models\Program\KegiatanModel();
            $sisaAnggaran = $kegiatanModel->getSisaAnggaran($parentId);
        }

        // If updating, add back current anggaran
        if (isset($data['id'])) {
            $currentId = $data['id'];
            $table = $parentType === 'program' ? 'kegiatan' : 'sub_kegiatan';
            $field = $parentType === 'program' ? 'anggaran_kegiatan' : 'anggaran_sub_kegiatan';
            
            $current = $db->table($table)->where('id', $currentId)->get()->getRow();
            if ($current) {
                $sisaAnggaran += $current->$field;
            }
        }

        if ($anggaran > $sisaAnggaran) {
            $error = 'Anggaran melebihi sisa anggaran ' . $parentType . ': Rp ' . number_format($sisaAnggaran, 0, ',', '.');
            return false;
        }

        return true;
    }

    /**
     * Validate program is approved
     */
    public function program_is_approved(string $str, string &$error = null): bool
    {
        $db = \Config\Database::connect();
        $program = $db->table('programs')
                      ->where('id', $str)
                      ->where('deleted_at', null)
                      ->get()
                      ->getRow();

        if (!$program) {
            $error = 'Program tidak ditemukan';
            return false;
        }

        if ($program->status !== 'approved') {
            $error = 'Program belum disetujui';
            return false;
        }

        return true;
    }

    /**
     * Validate kegiatan is approved
     */
    public function kegiatan_is_approved(string $str, string &$error = null): bool
    {
        $db = \Config\Database::connect();
        $kegiatan = $db->table('kegiatan')
                       ->where('id', $str)
                       ->where('deleted_at', null)
                       ->get()
                       ->getRow();

        if (!$kegiatan) {
            $error = 'Kegiatan tidak ditemukan';
            return false;
        }

        if ($kegiatan->status !== 'approved') {
            $error = 'Kegiatan belum disetujui';
            return false;
        }

        return true;
    }

    /**
     * Validate sub kegiatan is approved
     */
    public function subkegiatan_is_approved(string $str, string &$error = null): bool
    {
        $db = \Config\Database::connect();
        $subKegiatan = $db->table('sub_kegiatan')
                          ->where('id', $str)
                          ->where('deleted_at', null)
                          ->get()
                          ->getRow();

        if (!$subKegiatan) {
            $error = 'Sub kegiatan tidak ditemukan';
            return false;
        }

        if ($subKegiatan->status !== 'approved') {
            $error = 'Sub kegiatan belum disetujui';
            return false;
        }

        return true;
    }

    /**
     * Validate tahun anggaran
     */
    public function valid_tahun_anggaran(string $str, string &$error = null): bool
    {
        $tahun = intval($str);
        $currentYear = intval(date('Y'));

        if ($tahun < ($currentYear - 1)) {
            $error = 'Tahun anggaran tidak boleh lebih dari 1 tahun yang lalu';
            return false;
        }

        if ($tahun > ($currentYear + 1)) {
            $error = 'Tahun anggaran tidak boleh lebih dari 1 tahun ke depan';
            return false;
        }

        return true;
    }

    /**
     * Check if can edit based on status
     */
    public function can_edit_status(string $str, string $params, array $data, string &$error = null): bool
    {
        $allowedStatuses = explode(',', $params);
        $currentStatus = $data['status'] ?? null;

        if (!in_array($currentStatus, $allowedStatuses)) {
            $error = 'Hanya data dengan status ' . implode(' atau ', $allowedStatuses) . ' yang dapat diedit';
            return false;
        }

        return true;
    }

    /**
     * Check if can delete based on status
     */
    public function can_delete_status(string $str, string $params, array $data, string &$error = null): bool
    {
        $allowedStatuses = explode(',', $params);
        $currentStatus = $data['status'] ?? null;

        if (!in_array($currentStatus, $allowedStatuses)) {
            $error = 'Hanya data dengan status ' . implode(' atau ', $allowedStatuses) . ' yang dapat dihapus';
            return false;
        }

        return true;
    }

    /**
     * Validate bidang ownership
     */
    public function belongs_to_bidang(string $str, string $params, array $data, string &$error = null): bool
    {
        $session = \Config\Services::session();
        $user = $session->get('user_data');
        
        if (!$user) {
            $error = 'User tidak terautentikasi';
            return false;
        }

        // Super admin and kepala dinas can access all
        if (in_array($user['role'], ['superadmin', 'kepaladinas'])) {
            return true;
        }

        // Get bidang_id from data
        $bidangId = $data['bidang_id'] ?? null;

        if (!$bidangId) {
            // Try to get from parent
            $parentType = $params;
            $parentId = $data[$parentType . '_id'] ?? null;

            if ($parentId) {
                $db = \Config\Database::connect();
                
                if ($parentType === 'program') {
                    $parent = $db->table('programs')->where('id', $parentId)->get()->getRow();
                    $bidangId = $parent->bidang_id ?? null;
                } elseif ($parentType === 'kegiatan') {
                    $parent = $db->table('kegiatan')
                                 ->select('programs.bidang_id')
                                 ->join('programs', 'programs.id = kegiatan.program_id')
                                 ->where('kegiatan.id', $parentId)
                                 ->get()
                                 ->getRow();
                    $bidangId = $parent->bidang_id ?? null;
                }
            }
        }

        if ($bidangId != $user['bidang_id']) {
            $error = 'Anda tidak memiliki akses ke data ini';
            return false;
        }

        return true;
    }

    /**
     * Validate catatan rejection length
     */
    public function valid_catatan_rejection(string $str, string $minLength, array $data, string &$error = null): bool
    {
        if (strlen($str) < intval($minLength)) {
            $error = "Catatan penolakan minimal {$minLength} karakter";
            return false;
        }

        return true;
    }
}