<?php

namespace App\Controllers\KepalaDinas;

use App\Controllers\BaseController;
use App\Libraries\SPPD\NotaDinasGenerator;
use App\Libraries\SPPD\SPPDGenerator;

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
                throw new \Exception('SPPD tidak ditemukan');
            }

            // Clear any previous output
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Set proper headers for PDF
            $this->response->setHeader('Content-Type', 'application/pdf');
            $this->response->setHeader('Content-Disposition', 'inline; filename="Preview_Nota_Dinas_' . $id . '.pdf"');
            $this->response->setHeader('Cache-Control', 'private, max-age=0, must-revalidate');
            $this->response->setHeader('Pragma', 'public');
            
            // Generate PDF and get binary content
            $pdfContent = $this->notaDinasGenerator->generate($id, 'S');
            
            // Send PDF content
            return $this->response->setBody($pdfContent);
            
        } catch (\Exception $e) {
            log_message('error', 'Preview SPPD Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            // Return HTML error page
            return $this->response
                ->setStatusCode(500)
                ->setHeader('Content-Type', 'text/html')
                ->setBody(view('errors/pdf_error', [
                    'message' => $e->getMessage(),
                    'trace' => ENVIRONMENT === 'development' ? $e->getTraceAsString() : null
                ]));
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
            } catch (\Exception $e) {
                log_message('error', 'Nota Dinas Generation Error: ' . $e->getMessage());
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
            return redirect()->back()->with('error', 'Gagal download nota dinas: ' . $e->getMessage());
        }
    }

    private function getActionButtons($id, $status)
    {
        $buttons = '<div class="flex gap-2">';
        $buttons .= '<button class="btn-detail text-blue-600 hover:text-blue-800" data-id="' . $id . '" title="Detail"><i class="fas fa-eye"></i></button>';
        
        if ($status == 'pending') {
            $buttons .= '<button class="btn-preview text-purple-600 hover:text-purple-800" data-id="' . $id . '" title="Preview Nota Dinas"><i class="fas fa-file-pdf"></i></button>';
            $buttons .= '<button class="btn-approve text-green-600 hover:text-green-800" data-id="' . $id . '" title="Setujui"><i class="fas fa-check-circle"></i></button>';
            $buttons .= '<button class="btn-reject text-red-600 hover:text-red-800" data-id="' . $id . '" title="Tolak"><i class="fas fa-times-circle"></i></button>';
        }
        
        if ($status == 'approved') {
            $buttons .= '<a href="' . site_url('kepaladinas/sppd/download-nota-dinas/' . $id) . '" class="text-purple-600 hover:text-purple-800" title="Download Nota Dinas" target="_blank"><i class="fas fa-download"></i></a>';
        }
        
        $buttons .= '</div>';
        return $buttons;
    }
}