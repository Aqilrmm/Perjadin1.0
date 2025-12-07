<?php

namespace App\Libraries\SPPD;

use App\Models\SPPD\SPPDModel;
use App\Models\SPPD\SPPDPegawaiModel;
use App\Models\Program\SubKegiatanModel;
use App\Models\User\UserModel;

/**
 * SPPD Validator
 * 
 * Validates business rules for SPPD operations
 */
class SPPDValidator
{
    protected $sppdModel;
    protected $sppdPegawaiModel;
    protected $subKegiatanModel;
    protected $userModel;
    protected $errors = [];

    public function __construct()
    {
        $this->sppdModel = new SPPDModel();
        $this->sppdPegawaiModel = new SPPDPegawaiModel();
        $this->subKegiatanModel = new SubKegiatanModel();
        $this->userModel = new UserModel();
    }

    /**
     * Validate SPPD creation
     */
    public function validateCreate(array $data): bool
    {
        $this->errors = [];

        // Validate sub kegiatan is approved
        if (!$this->validateSubKegiatanApproved($data['sub_kegiatan_id'])) {
            return false;
        }

        // Validate tanggal berangkat
        if (!$this->validateTanggalBerangkat($data['tanggal_berangkat'])) {
            return false;
        }

        // Validate tanggal kembali
        if (!$this->validateTanggalKembali($data['tanggal_berangkat'], $data['tanggal_kembali'])) {
            return false;
        }

        // Validate lama perjalanan
        if (!$this->validateLamaPerjalanan($data['tanggal_berangkat'], $data['tanggal_kembali'], $data['lama_perjalanan'])) {
            return false;
        }

        // Validate estimasi biaya
        if (!$this->validateEstimasiBiaya($data['sub_kegiatan_id'], $data['estimasi_biaya'])) {
            return false;
        }

        // Validate pegawai list
        if (!$this->validatePegawaiList($data['pegawai_ids'])) {
            return false;
        }

        // Check pegawai overlap (warning only)
        $this->checkPegawaiOverlap(
            $data['pegawai_ids'], 
            $data['tanggal_berangkat'], 
            $data['tanggal_kembali']
        );

        // Validate penanggung jawab
        if (!$this->validatePenanggungJawab($data['penanggung_jawab'])) {
            return false;
        }

        // Validate no SPPD if provided
        if (!empty($data['no_sppd'])) {
            if (!$this->validateNoSPPD($data['no_sppd'])) {
                return false;
            }
        }

        return empty($this->errors);
    }

    /**
     * Validate SPPD update
     */
    public function validateUpdate(int $sppdId, array $data): bool
    {
        $this->errors = [];

        // Get existing SPPD
        $sppd = $this->sppdModel->find($sppdId);
        if (!$sppd) {
            $this->errors[] = 'SPPD tidak ditemukan';
            return false;
        }

        // Only draft can be edited
        if ($sppd['status'] !== 'draft') {
            $this->errors[] = 'Hanya SPPD dengan status draft yang dapat diedit';
            return false;
        }

        // Run same validations as create
        return $this->validateCreate($data);
    }

    /**
     * Validate sub kegiatan is approved
     */
    protected function validateSubKegiatanApproved($subKegiatanId): bool
    {
        $subKegiatan = $this->subKegiatanModel->find($subKegiatanId);

        if (!$subKegiatan) {
            $this->errors[] = 'Sub kegiatan tidak ditemukan';
            return false;
        }

        if ($subKegiatan['status'] !== 'approved') {
            $this->errors[] = 'Sub kegiatan belum disetujui';
            return false;
        }

        return true;
    }

    /**
     * Validate tanggal berangkat (minimum H+1)
     */
    protected function validateTanggalBerangkat($tanggalBerangkat): bool
    {
        $berangkat = strtotime($tanggalBerangkat);
        $today = strtotime(date('Y-m-d'));

        if ($berangkat <= $today) {
            $this->errors[] = 'Tanggal berangkat minimal H+1 dari hari ini';
            return false;
        }

        return true;
    }

    /**
     * Validate tanggal kembali
     */
    protected function validateTanggalKembali($tanggalBerangkat, $tanggalKembali): bool
    {
        $berangkat = strtotime($tanggalBerangkat);
        $kembali = strtotime($tanggalKembali);

        if ($kembali < $berangkat) {
            $this->errors[] = 'Tanggal kembali tidak boleh lebih awal dari tanggal berangkat';
            return false;
        }

        return true;
    }

    /**
     * Validate lama perjalanan
     */
    protected function validateLamaPerjalanan($tanggalBerangkat, $tanggalKembali, $lamaPerjalanan): bool
    {
        $berangkat = strtotime($tanggalBerangkat);
        $kembali = strtotime($tanggalKembali);
        $calculatedDays = floor(($kembali - $berangkat) / 86400) + 1;

        if ($lamaPerjalanan != $calculatedDays) {
            $this->errors[] = "Lama perjalanan tidak sesuai. Seharusnya {$calculatedDays} hari";
            return false;
        }

        if ($lamaPerjalanan < 1) {
            $this->errors[] = 'Lama perjalanan minimal 1 hari';
            return false;
        }

        if ($lamaPerjalanan > 365) {
            $this->errors[] = 'Lama perjalanan maksimal 365 hari';
            return false;
        }

        return true;
    }

    /**
     * Validate estimasi biaya tidak melebihi sisa anggaran
     */
    protected function validateEstimasiBiaya($subKegiatanId, $estimasiBiaya, $sppdId = null): bool
    {
        $sisaAnggaran = $this->subKegiatanModel->getSisaAnggaran($subKegiatanId);

        // If updating, add back current estimasi
        if ($sppdId) {
            $currentSppd = $this->sppdModel->find($sppdId);
            if ($currentSppd) {
                $sisaAnggaran += $currentSppd['estimasi_biaya'];
            }
        }

        if ($estimasiBiaya > $sisaAnggaran) {
            $this->errors[] = 'Estimasi biaya melebihi sisa anggaran sub kegiatan: Rp ' . number_format($sisaAnggaran, 0, ',', '.');
            return false;
        }

        // Warning if > 80%
        $percentage = ($estimasiBiaya / $sisaAnggaran) * 100;
        if ($percentage > 80) {
            $this->errors['warning'][] = 'Estimasi biaya mencapai ' . number_format($percentage, 1) . '% dari sisa anggaran';
        }

        return true;
    }

    /**
     * Validate pegawai list
     */
    protected function validatePegawaiList($pegawaiIds): bool
    {
        if (empty($pegawaiIds) || !is_array($pegawaiIds)) {
            $this->errors[] = 'Minimal 1 pegawai harus dipilih';
            return false;
        }

        // Validate each pegawai exists and is active
        foreach ($pegawaiIds as $pegawaiId) {
            $pegawai = $this->userModel->find($pegawaiId);
            
            if (!$pegawai) {
                $this->errors[] = "Pegawai dengan ID {$pegawaiId} tidak ditemukan";
                return false;
            }

            if (!$pegawai['is_active'] || $pegawai['is_blocked']) {
                $this->errors[] = "Pegawai {$pegawai['nama']} tidak aktif atau diblokir";
                return false;
            }
        }

        return true;
    }

    /**
     * Check pegawai overlap (warning only, not blocking)
     */
    protected function checkPegawaiOverlap($pegawaiIds, $tanggalBerangkat, $tanggalKembali, $excludeSppdId = null): void
    {
        foreach ($pegawaiIds as $pegawaiId) {
            $overlaps = $this->sppdModel->checkPegawaiOverlap(
                $pegawaiId,
                $tanggalBerangkat,
                $tanggalKembali,
                $excludeSppdId
            );

            if (!empty($overlaps)) {
                $pegawai = $this->userModel->find($pegawaiId);
                $this->errors['warning'][] = "Pegawai {$pegawai['nama']} memiliki SPPD yang overlap pada tanggal tersebut";
            }
        }
    }

    /**
     * Validate penanggung jawab
     */
    protected function validatePenanggungJawab($penanggungJawabId): bool
    {
        $user = $this->userModel->find($penanggungJawabId);

        if (!$user) {
            $this->errors[] = 'Penanggung jawab tidak ditemukan';
            return false;
        }

        if (!$user['is_active'] || $user['is_blocked']) {
            $this->errors[] = 'Penanggung jawab tidak aktif atau diblokir';
            return false;
        }

        // Penanggung jawab should be Kepala Bidang or higher
        $validRoles = ['superadmin', 'kepaladinas', 'kepalabidang'];
        if (!in_array($user['role'], $validRoles)) {
            $this->errors[] = 'Penanggung jawab harus Kepala Bidang atau lebih tinggi';
            return false;
        }

        return true;
    }

    /**
     * Validate No SPPD
     */
    protected function validateNoSPPD($noSppd, $excludeSppdId = null): bool
    {
        $builder = $this->sppdModel->builder();
        $builder->where('no_sppd', $noSppd);
        $builder->where('deleted_at', null);

        if ($excludeSppdId) {
            $builder->where('id !=', $excludeSppdId);
        }

        if ($builder->countAllResults() > 0) {
            $this->errors[] = 'Nomor SPPD sudah digunakan';
            return false;
        }

        return true;
    }

    /**
     * Validate LPPD submission
     */
    public function validateLPPDSubmission($sppdId, array $lppdData): bool
    {
        $this->errors = [];

        $sppd = $this->sppdModel->find($sppdId);

        if (!$sppd) {
            $this->errors[] = 'SPPD tidak ditemukan';
            return false;
        }

        // LPPD can only be filled after tanggal kembali
        if (strtotime(date('Y-m-d')) < strtotime($sppd['tanggal_kembali'])) {
            $this->errors[] = 'LPPD hanya dapat diisi setelah tanggal kembali';
            return false;
        }

        // Validate hasil kegiatan
        if (empty($lppdData['hasil_kegiatan']) || strlen($lppdData['hasil_kegiatan']) < 50) {
            $this->errors[] = 'Hasil kegiatan minimal 50 karakter';
            return false;
        }

        // Validate dokumentasi (minimal 1 foto)
        $dokumentasi = json_decode($lppdData['dokumentasi'] ?? '[]', true);
        if (empty($dokumentasi)) {
            $this->errors[] = 'Minimal 1 foto dokumentasi harus diupload';
            return false;
        }

        // Warning if filled after H+7
        $daysSinceReturn = floor((strtotime(date('Y-m-d')) - strtotime($sppd['tanggal_kembali'])) / 86400);
        if ($daysSinceReturn > 7) {
            $this->errors['warning'][] = "LPPD diisi {$daysSinceReturn} hari setelah tanggal kembali";
        }

        return empty($this->errors);
    }

    /**
     * Validate kwitansi submission
     */
    public function validateKwitansiSubmission($sppdId, array $kwitansiData): bool
    {
        $this->errors = [];

        $sppd = $this->sppdModel->find($sppdId);

        if (!$sppd) {
            $this->errors[] = 'SPPD tidak ditemukan';
            return false;
        }

        // Validate based on tipe perjalanan
        switch ($sppd['tipe_perjalanan']) {
            case 'Dalam Daerah':
                return $this->validateKwitansiDalamDaerah($kwitansiData, $sppd);
            
            case 'Luar Daerah Dalam Provinsi':
                return $this->validateKwitansiLuarDaerahDalamProv($kwitansiData, $sppd);
            
            case 'Luar Daerah Luar Provinsi':
                return $this->validateKwitansiLuarDaerahLuarProv($kwitansiData, $sppd);
            
            default:
                $this->errors[] = 'Tipe perjalanan tidak valid';
                return false;
        }
    }

    /**
     * Validate kwitansi dalam daerah
     */
    protected function validateKwitansiDalamDaerah($data, $sppd): bool
    {
        // Only need lumsum
        if (empty($data['biaya_lumsum']) || $data['biaya_lumsum'] <= 0) {
            $this->errors[] = 'Biaya lumsum wajib diisi';
            return false;
        }

        // Validate total
        $total = $data['biaya_lumsum'];
        if ($total > $sppd['estimasi_biaya']) {
            $this->errors[] = 'Total biaya melebihi estimasi';
            return false;
        }

        return true;
    }

    /**
     * Validate kwitansi luar daerah dalam provinsi
     */
    protected function validateKwitansiLuarDaerahDalamProv($data, $sppd): bool
    {
        // Need: biaya_perjalanan, lumsum, penginapan
        if (empty($data['biaya_perjalanan']) || $data['biaya_perjalanan'] <= 0) {
            $this->errors[] = 'Biaya perjalanan wajib diisi';
            return false;
        }

        if (empty($data['bukti_perjalanan'])) {
            $this->errors[] = 'Bukti perjalanan wajib diupload';
            return false;
        }

        if (empty($data['biaya_lumsum']) || $data['biaya_lumsum'] <= 0) {
            $this->errors[] = 'Biaya lumsum wajib diisi';
            return false;
        }

        if (empty($data['biaya_penginapan']) || $data['biaya_penginapan'] <= 0) {
            $this->errors[] = 'Biaya penginapan wajib diisi';
            return false;
        }

        if (empty($data['bukti_penginapan'])) {
            $this->errors[] = 'Bukti penginapan wajib diupload';
            return false;
        }

        // Validate total
        $total = $data['biaya_perjalanan'] + $data['biaya_lumsum'] + $data['biaya_penginapan'];
        if ($total > $sppd['estimasi_biaya']) {
            $this->errors[] = 'Total biaya melebihi estimasi';
            return false;
        }

        // Warning if > 90%
        $percentage = ($total / $sppd['estimasi_biaya']) * 100;
        if ($percentage > 90) {
            $this->errors['warning'][] = 'Total biaya mencapai ' . number_format($percentage, 1) . '% dari estimasi';
        }

        return true;
    }

    /**
     * Validate kwitansi luar daerah luar provinsi
     */
    protected function validateKwitansiLuarDaerahLuarProv($data, $sppd): bool
    {
        // Need: all fields
        $required = [
            'biaya_perjalanan' => 'Biaya perjalanan',
            'bukti_perjalanan' => 'Bukti perjalanan',
            'biaya_lumsum' => 'Biaya lumsum',
            'biaya_penginapan' => 'Biaya penginapan',
            'bukti_penginapan' => 'Bukti penginapan',
            'biaya_tiket' => 'Biaya tiket',
            'bukti_tiket' => 'Bukti tiket'
        ];

        foreach ($required as $field => $label) {
            if (empty($data[$field])) {
                $this->errors[] = "{$label} wajib diisi";
                return false;
            }
        }

        // Taxi is optional, but if filled, need bukti
        if (!empty($data['biaya_taxi']) && $data['biaya_taxi'] > 0) {
            if (empty($data['bukti_taxi'])) {
                $this->errors[] = 'Bukti taxi wajib diupload jika ada biaya taxi';
                return false;
            }
        }

        // Validate total
        $total = $data['biaya_perjalanan'] + $data['biaya_lumsum'] + 
                 $data['biaya_penginapan'] + ($data['biaya_taxi'] ?? 0) + 
                 $data['biaya_tiket'];

        if ($total > $sppd['estimasi_biaya']) {
            $this->errors[] = 'Total biaya melebihi estimasi';
            return false;
        }

        // Warning if > 90%
        $percentage = ($total / $sppd['estimasi_biaya']) * 100;
        if ($percentage > 90) {
            $this->errors['warning'][] = 'Total biaya mencapai ' . number_format($percentage, 1) . '% dari estimasi';
        }

        return true;
    }

    /**
     * Validate SPPD can be submitted for verification
     */
    public function validateSubmitForVerification($sppdId): bool
    {
        $this->errors = [];

        $sppd = $this->sppdModel->find($sppdId);

        if (!$sppd) {
            $this->errors[] = 'SPPD tidak ditemukan';
            return false;
        }

        if ($sppd['status'] !== 'approved') {
            $this->errors[] = 'Hanya SPPD yang sudah disetujui dapat disubmit';
            return false;
        }

        // Check LPPD
        $lppdModel = new \App\Models\SPPD\LPPDModel();
        $lppd = $lppdModel->getBySppd($sppdId);

        if (!$lppd || !$lppd['is_submitted']) {
            $this->errors[] = 'LPPD harus disubmit terlebih dahulu';
            return false;
        }

        // Check Kwitansi
        $kwitansiModel = new \App\Models\SPPD\KwitansiModel();
        $kwitansi = $kwitansiModel->getBySppd($sppdId);

        if (!$kwitansi || !$kwitansi['is_submitted']) {
            $this->errors[] = 'Kwitansi harus disubmit terlebih dahulu';
            return false;
        }

        return true;
    }

    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get warnings only
     */
    public function getWarnings(): array
    {
        return $this->errors['warning'] ?? [];
    }

    /**
     * Check if has errors (excluding warnings)
     */
    public function hasErrors(): bool
    {
        $errors = $this->errors;
        unset($errors['warning']);
        return !empty($errors);
    }

    /**
     * Clear errors
     */
    public function clearErrors(): void
    {
        $this->errors = [];
    }
}