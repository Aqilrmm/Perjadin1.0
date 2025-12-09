<?php

namespace App\Controllers\KepalaDinas;

use App\Controllers\BaseController;
use App\Libraries\SPPD\NotaDinasGenerator;
use App\Libraries\SPPD\SPPDGenerator;

// ========================================
// APPROVAL SPPD CONTROLLER
// ========================================

class ApprovalSPPDController extends BaseController
{
    protected $sppdModel;
    protected $notaDinasGenerator;
    protected $sppdGenerator;

    public function __construct()
    {
        $this->sppdModel = new \App\Models\SPPD\SPPDModel();
        $this->notaDinasGenerator = new NotaDinasGenerator();
        $this->sppdGenerator = new SPPDGenerator();
    }

    public function index()
    {
        return view('kepaladinas/approval/sppd', ['title' => 'Persetujuan SPPD']);
    }

    public function datatable()
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $request = $this->request->getPost();
        $data = $this->sppdModel->getDatatablesData($request);

        foreach ($data['data'] as $key => $row) {
            $data['data'][$key]->status_badge = get_sppd_status_badge($row->status);
            $data['data'][$key]->estimasi_formatted = format_rupiah($row->estimasi_biaya);
            $data['data'][$key]->action = $this->getActionButtons($row->id, $row->status);
        }

        return $this->respond($data);
    }

    public function detail($id)
    {
        if (!$this->isAjax()) {
            return $this->respondError('Invalid request', null, 400);
        }

        $sppd = $this->sppdModel->getWithRelations($id);
        if (!$sppd) {
            return $this->respondError('SPPD tidak ditemukan', null, 404);
        }

        $sppdPegawaiModel = new \App\Models\SPPD\SPPDPegawaiModel();
        $pegawaiList = $sppdPegawaiModel->getPegawaiBySppdId($id);

        // Add formatted data
        $sppd['status_badge'] = get_sppd_status_badge($sppd['status']);
        $sppd['estimasi_formatted'] = format_rupiah($sppd['estimasi_biaya']);
        if ($sppd['realisasi_biaya']) {
            $sppd['realisasi_formatted'] = format_rupiah($sppd['realisasi_biaya']);
        }

        return $this->respondSuccess('Detail SPPD', [
            'sppd' => $sppd,
            'pegawai_list' => $pegawaiList
        ]);
    }

    public function preview($id)
    {
        try {
            $sppd = $this->sppdModel->find($id);
            if (!$sppd) {
                return redirect()->back()->with('error', 'SPPD tidak ditemukan');
            }

            // Generate Nota Dinas PDF and display inline
            $this->notaDinasGenerator->generate($id, 'I');
            
        } catch (\Exception $e) {
            log_message('error', 'Preview SPPD Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat preview: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        $sppd = $this->sppdModel->find($id);
        if (!$sppd || $sppd['status'] != 'pending') {
            return $this->respondError('Hanya SPPD pending yang dapat disetujui', null, 400);
        }

        // Generate No SPPD if not exists
        $noSppd = $sppd['no_sppd'];
        if (!$noSppd) {
            try {
                // Auto-generate nomor SPPD using SPPDGenerator
                $noSppd = $this->sppdGenerator->generateNoSPPD(
                    $sppd['bidang_id'], 
                    $sppd['tanggal_berangkat']
                );
            } catch (\Exception $e) {
                return $this->respondError('Gagal generate nomor SPPD: ' . $e->getMessage(), null, 500);
            }
        }

        $catatan = $this->request->getPost('catatan');

        if ($this->sppdModel->approveSPPD($id, user_id(), $noSppd, $catatan)) {
            $this->logActivity('APPROVE_SPPD', "Approved SPPD: {$noSppd}");

            try {
                // Generate and save Nota Dinas PDF
                $notaDinasFile = $this->notaDinasGenerator->saveToFile($id);
                
                // Update SPPD with nota dinas file info (if you have a field for it)
                // $this->sppdModel->update($id, ['file_nota_dinas' => $notaDinasFile['filename']]);
                
            } catch (\Exception $e) {
                log_message('error', 'Nota Dinas Generation Error: ' . $e->getMessage());
                // Don't fail the approval if PDF generation fails
            }

            // Send notification
            notify_sppd_approved($id);

            return $this->respondSuccess('SPPD berhasil disetujui', ['no_sppd' => $noSppd]);
        }

        return $this->respondError('Gagal menyetujui SPPD', null, 500);
    }

    public function reject($id)
    {
        $sppd = $this->sppdModel->find($id);
        if (!$sppd || $sppd['status'] != 'pending') {
            return $this->respondError('Hanya SPPD pending yang dapat ditolak', null, 400);
        }

        $rules = config('Validation')->rules['catatan'];
        $valid = $this->validate($rules);
        if ($valid !== true) {
            return $this->respondError('Catatan penolakan wajib diisi minimal 10 karakter', $this->getValidationErrors(), 422);
        }

        $catatan = $this->request->getPost('catatan');
        if ($this->sppdModel->rejectSPPD($id, $catatan)) {
            $this->logActivity('REJECT_SPPD', "Rejected SPPD ID: {$id}");
            
            // Send notification
            notify_sppd_rejected($id);
            
            return $this->respondSuccess('SPPD berhasil ditolak');
        }

        return $this->respondError('Gagal menolak SPPD', null, 500);
    }

    /**
     * Download Nota Dinas PDF
     */
    public function downloadNotaDinas($id)
    {
        try {
            $sppd = $this->sppdModel->find($id);
            if (!$sppd) {
                return redirect()->back()->with('error', 'SPPD tidak ditemukan');
            }

            if ($sppd['status'] !== 'approved') {
                return redirect()->back()->with('error', 'SPPD belum disetujui');
            }

            // Generate and download Nota Dinas
            $this->notaDinasGenerator->generate($id, 'D');
            
        } catch (\Exception $e) {
            log_message('error', 'Download Nota Dinas Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal download nota dinas');
        }
    }

    private function getActionButtons($id, $status)
    {
        $buttons = '<div class="flex gap-2">';
        $buttons .= '<button class="btn-detail text-blue-600 hover:text-blue-800" data-id="' . $id . '" title="Detail"><i class="fas fa-eye"></i></button>';
        
        // Preview button - show for all pending SPPD
        if ($status == 'pending') {
            $buttons .= '<button class="btn-preview text-purple-600 hover:text-purple-800" data-id="' . $id . '" title="Preview Nota Dinas"><i class="fas fa-file-pdf"></i></button>';
        }
        
        // Download button - show for approved SPPD
        if ($status == 'approved') {
            $buttons .= '<a href="' . site_url('kepaladinas/sppd/download-nota-dinas/' . $id) . '" class="text-purple-600 hover:text-purple-800" title="Download Nota Dinas" target="_blank"><i class="fas fa-download"></i></a>';
        }
        
        if ($status == 'pending') {
            $buttons .= '<button class="btn-approve text-green-600 hover:text-green-800" data-id="' . $id . '" title="Setujui"><i class="fas fa-check-circle"></i></button>';
            $buttons .= '<button class="btn-reject text-red-600 hover:text-red-800" data-id="' . $id . '" title="Tolak"><i class="fas fa-times-circle"></i></button>';
        }
        
        $buttons .= '</div>';
        return $buttons;
    }
}