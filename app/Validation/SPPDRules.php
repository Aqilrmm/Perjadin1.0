<?php

namespace App\Validation;

/**
 * Custom Validation Rules for SPPD
 */
class SPPDRules
{
    /**
     * Validate No SPPD is unique
     */
    public function unique_no_sppd(string $str, string $params, array $data, string &$error = null): bool
    {
        $ignoreId = $params ?? null;
        
        $db = \Config\Database::connect();
        $builder = $db->table('sppd');
        
        $builder->where('no_sppd', $str);
        $builder->where('deleted_at', null);
        
        if ($ignoreId) {
            $builder->where('id !=', $ignoreId);
        }

        if ($builder->countAllResults() > 0) {
            $error = 'Nomor SPPD sudah digunakan';
            return false;
        }

        return true;
    }

    /**
     * Validate tanggal berangkat (min H+1)
     */
    public function valid_tanggal_berangkat(string $str, string &$error = null): bool
    {
        $tanggal = strtotime($str);
        $today = strtotime(date('Y-m-d'));

        if ($tanggal <= $today) {
            $error = 'Tanggal berangkat minimal H+1 dari hari ini';
            return false;
        }

        return true;
    }

    /**
     * Validate tanggal kembali after tanggal berangkat
     */
    public function tanggal_kembali_after_berangkat(string $str, string $params, array $data, string &$error = null): bool
    {
        $tanggalBerangkat = $data['tanggal_berangkat'] ?? null;
        
        if (!$tanggalBerangkat) {
            return true;
        }

        $berangkat = strtotime($tanggalBerangkat);
        $kembali = strtotime($str);

        if ($kembali < $berangkat) {
            $error = 'Tanggal kembali tidak boleh lebih awal dari tanggal berangkat';
            return false;
        }

        return true;
    }

    /**
     * Validate lama perjalanan
     */
    public function valid_lama_perjalanan(string $str, string $params, array $data, string &$error = null): bool
    {
        $lamaDays = intval($str);
        $tanggalBerangkat = $data['tanggal_berangkat'] ?? null;
        $tanggalKembali = $data['tanggal_kembali'] ?? null;

        if ($tanggalBerangkat && $tanggalKembali) {
            $berangkat = strtotime($tanggalBerangkat);
            $kembali = strtotime($tanggalKembali);
            $calculatedDays = floor(($kembali - $berangkat) / 86400) + 1;

            if ($lamaDays != $calculatedDays) {
                $error = "Lama perjalanan tidak sesuai dengan tanggal ({$calculatedDays} hari)";
                return false;
            }
        }

        if ($lamaDays < 1) {
            $error = 'Lama perjalanan minimal 1 hari';
            return false;
        }

        if ($lamaDays > 365) {
            $error = 'Lama perjalanan maksimal 365 hari';
            return false;
        }

        return true;
    }

    /**
     * Validate tipe perjalanan
     */
    public function valid_tipe_perjalanan(string $str, string &$error = null): bool
    {
        $validTypes = [
            'Dalam Daerah',
            'Luar Daerah Dalam Provinsi',
            'Luar Daerah Luar Provinsi'
        ];

        if (!in_array($str, $validTypes)) {
            $error = 'Tipe perjalanan tidak valid';
            return false;
        }

        return true;
    }

    /**
     * Validate minimal pegawai
     */
    public function min_pegawai(string $str, string $min, array $data, string &$error = null): bool
    {
        $pegawaiIds = is_array($str) ? $str : json_decode($str, true);
        
        if (!is_array($pegawaiIds)) {
            $pegawaiIds = explode(',', $str);
        }

        $count = count($pegawaiIds);
        $minCount = intval($min);

        if ($count < $minCount) {
            $error = "Minimal {$minCount} pegawai harus dipilih";
            return false;
        }

        return true;
    }

    /**
     * Check pegawai overlap
     */
    public function check_pegawai_overlap(string $str, string $params, array $data, string &$error = null): bool
    {
        $pegawaiIds = is_array($str) ? $str : json_decode($str, true);
        $tanggalBerangkat = $data['tanggal_berangkat'] ?? null;
        $tanggalKembali = $data['tanggal_kembali'] ?? null;
        $sppdId = $data['id'] ?? null;

        if (!$tanggalBerangkat || !$tanggalKembali) {
            return true;
        }

        $sppdModel = new \App\Models\SPPD\SPPDModel();
        $overlaps = [];

        foreach ($pegawaiIds as $pegawaiId) {
            $result = $sppdModel->checkPegawaiOverlap(
                $pegawaiId,
                $tanggalBerangkat,
                $tanggalKembali,
                $sppdId
            );

            if (!empty($result)) {
                $overlaps[] = $pegawaiId;
            }
        }

        // Warning only, not blocking
        if (!empty($overlaps)) {
            $error = 'Beberapa pegawai memiliki SPPD yang overlap (warning)';
            // Return true to allow, just warning
        }

        return true;
    }

    /**
     * Validate estimasi biaya tidak melebihi sisa anggaran
     */
    public function estimasi_not_exceed_anggaran(string $str, string $params, array $data, string &$error = null): bool
    {
        $estimasi = floatval($str);
        $subKegiatanId = $data['sub_kegiatan_id'] ?? null;

        if (!$subKegiatanId) {
            return true;
        }

        $subKegiatanModel = new \App\Models\Program\SubKegiatanModel();
        $sisaAnggaran = $subKegiatanModel->getSisaAnggaran($subKegiatanId);

        // If updating, add back current estimasi
        if (isset($data['id'])) {
            $db = \Config\Database::connect();
            $current = $db->table('sppd')->where('id', $data['id'])->get()->getRow();
            if ($current) {
                $sisaAnggaran += $current->estimasi_biaya;
            }
        }

        if ($estimasi > $sisaAnggaran) {
            $error = 'Estimasi biaya melebihi sisa anggaran sub kegiatan: Rp ' . number_format($sisaAnggaran, 0, ',', '.');
            return false;
        }

        return true;
    }

    /**
     * Validate file upload surat tugas
     */
    public function valid_surat_tugas(string $str, string &$error = null): bool
    {
        $request = \Config\Services::request();
        $file = $request->getFile('file_surat_tugas');

        if (!$file || !$file->isValid()) {
            return true; // Optional field
        }

        // Check file type
        if ($file->getMimeType() !== 'application/pdf') {
            $error = 'File surat tugas harus berupa PDF';
            return false;
        }

        // Check file size (max 2MB)
        if ($file->getSize() > 2048 * 1024) {
            $error = 'Ukuran file maksimal 2MB';
            return false;
        }

        return true;
    }

    /**
     * Validate maksud perjalanan length
     */
    public function valid_maksud_perjalanan(string $str, string $min, array $data, string &$error = null): bool
    {
        $length = strlen($str);
        $minLength = intval($min);

        if ($length < $minLength) {
            $error = "Maksud perjalanan minimal {$minLength} karakter";
            return false;
        }

        return true;
    }

    /**
     * Validate LPPD hasil kegiatan
     */
    public function valid_hasil_kegiatan(string $str, string $min, array $data, string &$error = null): bool
    {
        $length = strlen($str);
        $minLength = intval($min);

        if ($length < $minLength) {
            $error = "Hasil kegiatan minimal {$minLength} karakter";
            return false;
        }

        return true;
    }

    /**
     * Validate dokumentasi foto minimal
     */
    public function min_dokumentasi(string $str, string $min, array $data, string &$error = null): bool
    {
        $request = \Config\Services::request();
        $files = $request->getFiles();
        
        if (!isset($files['dokumentasi'])) {
            $error = "Minimal {$min} foto dokumentasi harus diupload";
            return false;
        }

        $validFiles = 0;
        foreach ($files['dokumentasi'] as $file) {
            if ($file->isValid()) {
                $validFiles++;
            }
        }

        $minCount = intval($min);
        if ($validFiles < $minCount) {
            $error = "Minimal {$minCount} foto dokumentasi harus diupload";
            return false;
        }

        return true;
    }

    /**
     * Validate bukti upload
     */
    public function valid_bukti_upload(string $str, string $types, array $data, string &$error = null): bool
    {
        $request = \Config\Services::request();
        $file = $request->getFile($str);

        if (!$file || !$file->isValid()) {
            return true; // Will be validated by required rule if mandatory
        }

        // Check file type
        $allowedTypes = explode(',', $types);
        $mimeType = $file->getMimeType();
        
        $validMimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'pdf' => 'application/pdf'
        ];

        $isValid = false;
        foreach ($allowedTypes as $type) {
            if (isset($validMimeTypes[$type]) && $validMimeTypes[$type] === $mimeType) {
                $isValid = true;
                break;
            }
        }

        if (!$isValid) {
            $error = 'File harus berupa: ' . implode(', ', $allowedTypes);
            return false;
        }

        // Check file size (max 2MB)
        if ($file->getSize() > 2048 * 1024) {
            $error = 'Ukuran file maksimal 2MB';
            return false;
        }

        return true;
    }

    /**
     * Validate total biaya tidak melebihi estimasi
     */
    public function total_not_exceed_estimasi(string $str, string $params, array $data, string &$error = null): bool
    {
        $totalBiaya = floatval($str);
        $sppdId = $data['sppd_id'] ?? null;

        if (!$sppdId) {
            return true;
        }

        $db = \Config\Database::connect();
        $sppd = $db->table('sppd')->where('id', $sppdId)->get()->getRow();

        if (!$sppd) {
            $error = 'SPPD tidak ditemukan';
            return false;
        }

        if ($totalBiaya > $sppd->estimasi_biaya) {
            $error = 'Total biaya melebihi estimasi biaya SPPD: Rp ' . number_format($sppd->estimasi_biaya, 0, ',', '.');
            return false;
        }

        return true;
    }

    /**
     * Check if LPPD can be filled
     */
    public function can_fill_lppd(string $str, string $params, array $data, string &$error = null): bool
    {
        $sppdId = $str;
        
        $db = \Config\Database::connect();
        $sppd = $db->table('sppd')->where('id', $sppdId)->get()->getRow();

        if (!$sppd) {
            $error = 'SPPD tidak ditemukan';
            return false;
        }

        // Check if SPPD is approved
        if ($sppd->status !== 'approved') {
            $error = 'LPPD hanya dapat diisi setelah SPPD disetujui';
            return false;
        }

        // Check if after tanggal kembali
        if (strtotime(date('Y-m-d')) < strtotime($sppd->tanggal_kembali)) {
            $error = 'LPPD hanya dapat diisi setelah tanggal kembali';
            return false;
        }

        return true;
    }

    /**
     * Validate status transition
     */
    public function valid_status_transition(string $str, string $params, array $data, string &$error = null): bool
    {
        $currentStatus = $params;
        $newStatus = $str;

        $validTransitions = [
            'draft' => ['pending', 'rejected'],
            'pending' => ['approved', 'rejected'],
            'approved' => ['submitted'],
            'submitted' => ['verified', 'need_revision'],
            'need_revision' => ['submitted'],
            'rejected' => [],
            'verified' => ['closed']
        ];

        if (!isset($validTransitions[$currentStatus])) {
            $error = 'Status saat ini tidak valid';
            return false;
        }

        if (!in_array($newStatus, $validTransitions[$currentStatus])) {
            $error = "Transisi status dari {$currentStatus} ke {$newStatus} tidak diperbolehkan";
            return false;
        }

        return true;
    }

    /**
     * Check if penanggung jawab is valid
     */
    public function valid_penanggung_jawab(string $str, string $params, array $data, string &$error = null): bool
    {
        $db = \Config\Database::connect();
        $user = $db->table('users')
                   ->where('id', $str)
                   ->where('deleted_at', null)
                   ->where('is_active', 1)
                   ->where('is_blocked', 0)
                   ->get()
                   ->getRow();

        if (!$user) {
            $error = 'Penanggung jawab tidak valid';
            return false;
        }

        // Penanggung jawab should be Kepala Bidang or higher
        $validRoles = ['superadmin', 'kepaladinas', 'kepalabidang'];
        if (!in_array($user->role, $validRoles)) {
            $error = 'Penanggung jawab harus Kepala Bidang atau lebih tinggi';
            return false;
        }

        return true;
    }
}