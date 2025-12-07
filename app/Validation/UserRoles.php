<?php

namespace App\Validation;

/**
 * Custom Validation Rules for User
 */
class UserRules
{
    /**
     * Validate NIP format (18 digits for ASN)
     */
    public function valid_nip(string $str, string &$error = null): bool
    {
        if (strlen($str) !== 18) {
            $error = 'NIP harus 18 digit';
            return false;
        }

        if (!ctype_digit($str)) {
            $error = 'NIP hanya boleh berisi angka';
            return false;
        }

        return true;
    }

    /**
     * Validate NIK format (16 digits for Non-ASN)
     */
    public function valid_nik(string $str, string &$error = null): bool
    {
        if (strlen($str) !== 16) {
            $error = 'NIK harus 16 digit';
            return false;
        }

        if (!ctype_digit($str)) {
            $error = 'NIK hanya boleh berisi angka';
            return false;
        }

        return true;
    }

    /**
     * Validate password strength
     */
    public function strong_password(string $str, string &$error = null): bool
    {
        if (strlen($str) < 8) {
            $error = 'Password minimal 8 karakter';
            return false;
        }

        if (!preg_match('/[A-Z]/', $str)) {
            $error = 'Password harus mengandung minimal 1 huruf besar';
            return false;
        }

        if (!preg_match('/[a-z]/', $str)) {
            $error = 'Password harus mengandung minimal 1 huruf kecil';
            return false;
        }

        if (!preg_match('/[0-9]/', $str)) {
            $error = 'Password harus mengandung minimal 1 angka';
            return false;
        }

        return true;
    }

    /**
     * Check if NIP/NIK is unique
     */
    public function unique_nip_nik(string $str, string $params, array $data, string &$error = null): bool
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        
        // Parse parameters (table.field,ignore_field,ignore_value)
        $params = explode(',', $params);
        $ignoreField = $params[0] ?? 'id';
        $ignoreValue = $params[1] ?? null;

        $builder->where('nip_nik', $str);
        
        if ($ignoreValue) {
            $builder->where("{$ignoreField} !=", $ignoreValue);
        }

        $builder->where('deleted_at', null);

        if ($builder->countAllResults() > 0) {
            $error = 'NIP/NIK sudah digunakan';
            return false;
        }

        return true;
    }

    /**
     * Validate role
     */
    public function valid_role(string $str, string &$error = null): bool
    {
        $validRoles = ['superadmin', 'kepaladinas', 'kepalabidang', 'pegawai', 'keuangan'];
        
        if (!in_array($str, $validRoles)) {
            $error = 'Role tidak valid';
            return false;
        }

        return true;
    }

    /**
     * Validate jenis pegawai
     */
    public function valid_jenis_pegawai(string $str, string &$error = null): bool
    {
        $validTypes = ['ASN', 'Non-ASN'];
        
        if (!in_array($str, $validTypes)) {
            $error = 'Jenis pegawai tidak valid';
            return false;
        }

        return true;
    }

    /**
     * Check if email domain is allowed
     */
    public function allowed_email_domain(string $str, string $domains, array $data, string &$error = null): bool
    {
        $allowedDomains = explode(',', $domains);
        $emailParts = explode('@', $str);
        
        if (count($emailParts) !== 2) {
            $error = 'Format email tidak valid';
            return false;
        }

        $domain = $emailParts[1];
        
        if (!in_array($domain, $allowedDomains)) {
            $error = "Email harus menggunakan domain: " . implode(', ', $allowedDomains);
            return false;
        }

        return true;
    }

    /**
     * Validate bidang requirement based on role
     */
    public function bidang_required_for_role(string $str, string $params, array $data, string &$error = null): bool
    {
        $role = $data['role'] ?? null;
        
        // Roles that don't require bidang
        $noRequireBidang = ['superadmin', 'kepaladinas', 'keuangan'];
        
        if (in_array($role, $noRequireBidang)) {
            return true;
        }

        // For other roles, bidang is required
        if (empty($str)) {
            $error = 'Bidang wajib diisi untuk role ini';
            return false;
        }

        return true;
    }

    /**
     * Check if user can be blocked
     */
    public function can_block_user(string $str, string $params, array $data, string &$error = null): bool
    {
        // Cannot block super admin
        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $str)->get()->getRow();
        
        if (!$user) {
            $error = 'User tidak ditemukan';
            return false;
        }

        if ($user->role === 'superadmin') {
            $error = 'Super admin tidak dapat diblokir';
            return false;
        }

        // Cannot block self
        $session = \Config\Services::session();
        $currentUser = $session->get('user_data');
        
        if ($currentUser && $currentUser['id'] == $str) {
            $error = 'Tidak dapat memblokir akun sendiri';
            return false;
        }

        return true;
    }

    /**
     * Validate foto upload
     */
    public function valid_foto_upload(string $str, string &$error = null): bool
    {
        $request = \Config\Services::request();
        $file = $request->getFile('foto');

        if (!$file || !$file->isValid()) {
            return true; // Optional field
        }

        // Check file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            $error = 'File harus berupa gambar (JPG, JPEG, PNG)';
            return false;
        }

        // Check file size (max 2MB)
        if ($file->getSize() > 2048 * 1024) {
            $error = 'Ukuran file maksimal 2MB';
            return false;
        }

        return true;
    }
}