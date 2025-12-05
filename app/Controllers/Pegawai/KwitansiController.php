<?php

// ========================================
// KWITANSI CONTROLLER
// ========================================

namespace App\Controllers\Pegawai;

use App\Controllers\BaseController;
use App\Models\SPPD\SPPDModel;
use App\Models\SPPD\KwitansiModel;

class KwitansiController extends BaseController
{
    protected $sppdModel;
    protected $kwitansiModel;

    public function __construct()
    {
        $this->sppdModel = new SPPDModel();
        $this->kwitansiModel = new KwitansiModel();
    }

    /**
     * Display kwitansi form
     */
    public function form($sppdId)
    {
        $sppd = $this->sppdModel->getWithRelations($sppdId);

        if (!$sppd) {
            return redirect()->to('/pegawai/sppd')->with('error', 'SPPD tidak ditemukan');
        }

        // Check authorization
        $sppdPegawaiModel = new \App\Models\SPPD\SPPDPegawaiModel();
        if (!$sppdPegawaiModel->isPegawaiInSppd($sppdId, user_id())) {
            return redirect()->to('/pegawai/sppd')->with('error', 'Unauthorized');
        }

        // Get existing kwitansi if any
        $kwitansi = $this->kwitansiModel->getBySppd($sppdId);

        // Get system settings for lumsum
        $db = \Config\Database::connect();
        $settings = $db->table('system_settings')->whereIn('key', [
            'lumsum_dalam_daerah',
            'lumsum_luar_daerah_dalam_provinsi',
            'lumsum_luar_daerah_luar_provinsi'
        ])->get()->getResult();

        $lumsum = [];
        foreach ($settings as $setting) {
            $lumsum[$setting->key] = $setting->value;
        }

        $data = [
            'title' => 'Isi Kwitansi',
            'sppd' => $sppd,
            'kwitansi' => $kwitansi,
            'lumsum' => $lumsum,
        ];

        return view('pegawai/kwitansi/form', $data);
    }

    /**
     * Save kwitansi (draft)
     */
    public function save($sppdId)
    {
        $sppd = $this->sppdModel->find($sppdId);

        if (!$sppd) {
            return $this->respondError('SPPD tidak ditemukan', null, 404);
        }

        $kwitansiData = [
            'sppd_id' => $sppdId,
            'pegawai_id' => user_id(),
            'biaya_perjalanan' => $this->request->getPost('biaya_perjalanan') ?: 0,
            'keterangan_perjalanan' => $this->request->getPost('keterangan_perjalanan'),
            'biaya_lumsum' => $this->request->getPost('biaya_lumsum') ?: 0,
            'keterangan_lumsum' => $this->request->getPost('keterangan_lumsum'),
            'biaya_penginapan' => $this->request->getPost('biaya_penginapan') ?: 0,
            'keterangan_penginapan' => $this->request->getPost('keterangan_penginapan'),
            'biaya_taxi' => $this->request->getPost('biaya_taxi') ?: 0,
            'keterangan_taxi' => $this->request->getPost('keterangan_taxi'),
            'biaya_tiket' => $this->request->getPost('biaya_tiket') ?: 0,
            'keterangan_tiket' => $this->request->getPost('keterangan_tiket'),
            'is_submitted' => 0,
        ];

        // Handle file uploads
        $fileFields = ['bukti_perjalanan', 'bukti_penginapan', 'bukti_taxi', 'bukti_tiket'];
        
        foreach ($fileFields as $field) {
            $file = $this->request->getFile($field);
            if ($file && $file->isValid()) {
                $newName = $field . '_' . time() . '.' . $file->getExtension();
                $file->move(FCPATH . 'uploads/' . $field, $newName);
                $kwitansiData[$field] = $newName;
            }
        }

        // Calculate total
        $kwitansiData['total_biaya'] = $this->kwitansiModel->calculateTotal($kwitansiData);

        // Validate total tidak melebihi estimasi
        if ($kwitansiData['total_biaya'] > $sppd['estimasi_biaya']) {
            return $this->respondError('Total biaya melebihi estimasi biaya SPPD', null, 422);
        }

        // Check if kwitansi exists
        $existingKwitansi = $this->kwitansiModel->getBySppd($sppdId);

        if ($existingKwitansi) {
            if ($this->kwitansiModel->update($existingKwitansi['id'], $kwitansiData)) {
                return $this->respondSuccess('Kwitansi berhasil disimpan', [
                    'total_biaya' => $kwitansiData['total_biaya'],
                    'estimasi_biaya' => $sppd['estimasi_biaya'],
                    'selisih' => $sppd['estimasi_biaya'] - $kwitansiData['total_biaya'],
                ]);
            }
        } else {
            if ($this->kwitansiModel->insert($kwitansiData)) {
                return $this->respondSuccess('Kwitansi berhasil disimpan', [
                    'total_biaya' => $kwitansiData['total_biaya'],
                    'estimasi_biaya' => $sppd['estimasi_biaya'],
                    'selisih' => $sppd['estimasi_biaya'] - $kwitansiData['total_biaya'],
                ]);
            }
        }

        return $this->respondError('Gagal menyimpan kwitansi', null, 500);
    }

    /**
     * Submit kwitansi
     */
    public function submit($sppdId)
    {
        $kwitansi = $this->kwitansiModel->getBySppd($sppdId);

        if (!$kwitansi) {
            return $this->respondError('Kwitansi belum diisi', null, 400);
        }

        // Check if LPPD is submitted
        $lppdModel = new \App\Models\SPPD\LPPDModel();
        $lppd = $lppdModel->getBySppd($sppdId);

        if (!$lppd || !$lppd['is_submitted']) {
            return $this->respondError('LPPD harus disubmit terlebih dahulu', null, 400);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Submit kwitansi
            $this->kwitansiModel->submitKwitansi($kwitansi['id']);

            // Update SPPD status to submitted
            $this->sppdModel->submitForVerification($sppdId);

            // Update realisasi_biaya
            $this->sppdModel->update($sppdId, [
                'realisasi_biaya' => $kwitansi['total_biaya']
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->respondError('Gagal submit kwitansi', null, 500);
            }

            $this->logActivity('SUBMIT_KWITANSI', "Submitted kwitansi for SPPD ID: {$sppdId}");

            // Send notification to keuangan
            notify_sppd_submitted($sppdId);

            return $this->respondSuccess('Kwitansi berhasil disubmit. SPPD dikirim ke bagian Keuangan untuk verifikasi');

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->respondError($e->getMessage(), null, 500);
        }
    }
}