<?php

namespace App\Controllers\Pegawai;

use App\Controllers\BaseController;
use App\Models\SPPD\SPPDModel;
use App\Models\SPPD\LPPDModel;
use App\Models\SPPD\KwitansiModel;

// ========================================
// LPPD CONTROLLER
// ========================================

class LPPDController extends BaseController
{
    protected $sppdModel;
    protected $lppdModel;

    public function __construct()
    {
        $this->sppdModel = new SPPDModel();
        $this->lppdModel = new LPPDModel();
    }

    /**
     * Display LPPD form
     */
    public function form($sppdId)
    {
        $sppd = $this->sppdModel->getWithRelations($sppdId);

        if (!$sppd) {
            return redirect()->to('/pegawai/sppd')->with('error', 'SPPD tidak ditemukan');
        }

        // Check if user is in SPPD pegawai list
        $sppdPegawaiModel = new \App\Models\SPPD\SPPDPegawaiModel();
        if (!$sppdPegawaiModel->isPegawaiInSppd($sppdId, user_id())) {
            return redirect()->to('/pegawai/sppd')->with('error', 'Unauthorized');
        }

        // Check if can fill LPPD (after tanggal_kembali)
        if (strtotime(date('Y-m-d')) < strtotime($sppd['tanggal_kembali'])) {
            return redirect()->to('/pegawai/sppd')->with('error', 'LPPD hanya dapat diisi setelah tanggal kembali');
        }

        // Get existing LPPD if any
        $lppd = $this->lppdModel->getBySppd($sppdId);

        $data = [
            'title' => 'Isi LPPD',
            'sppd' => $sppd,
            'lppd' => $lppd,
        ];

        return view('pegawai/lppd/form', $data);
    }

    /**
     * Save LPPD (draft)
     */
    public function save($sppdId)
    {
        $rules = [
            'hasil_kegiatan' => 'required|min_length[50]',
        ];

        $errors = $this->validate($rules);
        if ($errors !== true) {
            return $this->respondError('Validasi gagal', $errors, 422);
        }

        $lppdData = [
            'sppd_id' => $sppdId,
            'pegawai_id' => user_id(),
            'hasil_kegiatan' => $this->request->getPost('hasil_kegiatan'),
            'hambatan' => $this->request->getPost('hambatan'),
            'saran' => $this->request->getPost('saran'),
            'tanggal_pengisian' => date('Y-m-d'),
            'is_submitted' => 0,
        ];

        // Handle dokumentasi upload
        $files = $this->request->getFiles();
        $dokumentasi = [];
        
        if (isset($files['dokumentasi'])) {
            foreach ($files['dokumentasi'] as $file) {
                if ($file->isValid()) {
                    $newName = 'dok_' . time() . '_' . uniqid() . '.' . $file->getExtension();
                    $file->move(FCPATH . 'uploads/dokumentasi_kegiatan', $newName);
                    $dokumentasi[] = $newName;
                }
            }
        }

        $lppdData['dokumentasi'] = json_encode($dokumentasi);

        // Check if LPPD exists
        $existingLppd = $this->lppdModel->getBySppd($sppdId);

        if ($existingLppd) {
            if ($this->lppdModel->update($existingLppd['id'], $lppdData)) {
                return $this->respondSuccess('LPPD berhasil disimpan');
            }
        } else {
            if ($this->lppdModel->insert($lppdData)) {
                return $this->respondSuccess('LPPD berhasil disimpan');
            }
        }

        return $this->respondError('Gagal menyimpan LPPD', null, 500);
    }

    /**
     * Submit LPPD
     */
    public function submit($sppdId)
    {
        $lppd = $this->lppdModel->getBySppd($sppdId);

        if (!$lppd) {
            return $this->respondError('LPPD belum diisi', null, 400);
        }

        // Validate dokumentasi (min 1 foto)
        $dokumentasi = json_decode($lppd['dokumentasi'], true);
        if (empty($dokumentasi)) {
            return $this->respondError('Minimal upload 1 foto dokumentasi', null, 422);
        }

        if ($this->lppdModel->submitLPPD($lppd['id'])) {
            $this->logActivity('SUBMIT_LPPD', "Submitted LPPD for SPPD ID: {$sppdId}");
            return $this->respondSuccess('LPPD berhasil disubmit');
        }

        return $this->respondError('Gagal submit LPPD', null, 500);
    }
}