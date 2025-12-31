<?php

namespace App\Controllers\KepalaDinas;

use App\Controllers\BaseController;
use App\Libraries\SPPD\NotaDinasGenerator;
use App\Libraries\SPPD\SPPDGenerator;
use App\Libraries\SPPD\SuratTugasGenerator;
use App\Libraries\SPPD\SuratPerjalananDinasGenerator;

class ApprovalSPPDController extends BaseController
{
    protected $sppdModel;
    protected $notaDinasGenerator;
    protected $sppdGenerator;
    protected $suratTugasGenerator;
    protected $suratPerjalananDinasGenerator;
    protected $Users;

    public function __construct()
    {
        $this->sppdModel = new \App\Models\SPPD\SPPDModel();
        $this->Users = new \App\Models\User\UserModel();
        $this->notaDinasGenerator = new NotaDinasGenerator();
        $this->sppdGenerator = new SPPDGenerator();
        $this->suratTugasGenerator = new SuratTugasGenerator();
        $this->suratPerjalananDinasGenerator = new SuratPerjalananDinasGenerator();
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
        $kadis = $this->Users->getKepalaDinas();

        // Add formatted data
        $sppd['status_badge'] = get_sppd_status_badge($sppd['status']);
        $sppd['estimasi_formatted'] = format_rupiah($sppd['estimasi_biaya']);
        if ($sppd['realisasi_biaya']) {
            $sppd['realisasi_formatted'] = format_rupiah($sppd['realisasi_biaya']);
        }

        return $this->respondSuccess('Detail SPPD', [
            'sppd' => $sppd,
            'pegawai_list' => $pegawaiList,
            'kadis' => $kadis
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
                // Generate dan save semua dokumen
                
                // 1. Nota Dinas
                $notaDinasFile = $this->notaDinasGenerator->saveToFile($id);
                log_message('info', 'Nota Dinas generated: ' . $notaDinasFile['filename']);
                
                // 2. Surat Tugas
                $suratTugasFile = $this->suratTugasGenerator->saveToFile($id);
                log_message('info', 'Surat Tugas generated: ' . $suratTugasFile['filename']);
                
                // 3. Surat Perjalanan Dinas
                $spdFile = $this->suratPerjalananDinasGenerator->saveToFile($id);
                log_message('info', 'Surat Perjalanan Dinas generated: ' . $spdFile['filename']);

                // Update SPPD dengan file path dokumen
                $this->sppdModel->update($id, [
                    'file_nota_dinas' => $notaDinasFile['filename'],
                    'file_surat_tugas' => $suratTugasFile['filename'],
                    'file_spd' => $spdFile['filename']
                ]);

            } catch (\Exception $e) {
                log_message('error', 'Document Generation Error: ' . $e->getMessage());
                log_message('error', 'Stack trace: ' . $e->getTraceAsString());
                // Tidak menggagalkan approval, hanya log error
            }

            // Send notification
            notify_sppd_approved($id);

            return $this->respondSuccess('SPPD berhasil disetujui dan dokumen berhasil digenerate', [
                'no_sppd' => $noSppd,
                'documents' => [
                    'nota_dinas' => $notaDinasFile ?? null,
                    'surat_tugas' => $suratTugasFile ?? null,
                    'spd' => $spdFile ?? null
                ]
            ]);
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
     * Download Nota Dinas
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
            return redirect()->back()->with('error', 'Gagal download nota dinas: ' . $e->getMessage());
        }
    }

    /**
     * Download Surat Tugas
     */
    public function downloadSuratTugas($id)
    {
        try {
            $sppd = $this->sppdModel->find($id);
            if (!$sppd) {
                return redirect()->back()->with('error', 'SPPD tidak ditemukan');
            }

            if ($sppd['status'] !== 'approved') {
                return redirect()->back()->with('error', 'SPPD belum disetujui');
            }

            // Generate and download Surat Tugas
            $this->suratTugasGenerator->generate($id, 'D');
            
        } catch (\Exception $e) {
            log_message('error', 'Download Surat Tugas Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal download surat tugas: ' . $e->getMessage());
        }
    }

    /**
     * Download Surat Perjalanan Dinas
     */
    public function downloadSPD($id)
    {
        try {
            $sppd = $this->sppdModel->find($id);
            if (!$sppd) {
                return redirect()->back()->with('error', 'SPPD tidak ditemukan');
            }

            if ($sppd['status'] !== 'approved') {
                return redirect()->back()->with('error', 'SPPD belum disetujui');
            }

            // Generate and download SPD
            $this->suratPerjalananDinasGenerator->generate($id, 'D');
            
        } catch (\Exception $e) {
            log_message('error', 'Download SPD Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal download SPD: ' . $e->getMessage());
        }
    }

    /**
     * Download semua dokumen dalam satu ZIP
     */
    public function downloadAllDocuments($id)
    {
        try {
            $sppd = $this->sppdModel->find($id);
            if (!$sppd) {
                return redirect()->back()->with('error', 'SPPD tidak ditemukan');
            }

            if ($sppd['status'] !== 'approved') {
                return redirect()->back()->with('error', 'SPPD belum disetujui');
            }

            $noSppd = str_replace('/', '_', $sppd['no_sppd']);
            
            // Create temp directory
            $tempDir = FCPATH . 'uploads/temp/';
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Generate all documents
            $notaDinas = $this->notaDinasGenerator->saveToFile($id, $tempDir);
            $suratTugas = $this->suratTugasGenerator->saveToFile($id, $tempDir);
            $spd = $this->suratPerjalananDinasGenerator->saveToFile($id, $tempDir);

            // Create ZIP
            $zip = new \ZipArchive();
            $zipFilename = $tempDir . 'Dokumen_SPPD_' . $noSppd . '.zip';
            
            if ($zip->open($zipFilename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
                $zip->addFile($notaDinas['filepath'], $notaDinas['filename']);
                $zip->addFile($suratTugas['filepath'], $suratTugas['filename']);
                $zip->addFile($spd['filepath'], $spd['filename']);
                $zip->close();

                // Download ZIP
                return $this->response->download($zipFilename, null)->setFileName('Dokumen_SPPD_' . $noSppd . '.zip');
            } else {
                throw new \Exception('Gagal membuat file ZIP');
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Download All Documents Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal download dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Regenerate dokumen (untuk koreksi)
     */
    public function regenerateDocuments($id)
    {
        try {
            $sppd = $this->sppdModel->find($id);
            if (!$sppd) {
                return $this->respondError('SPPD tidak ditemukan', null, 404);
            }

            if ($sppd['status'] !== 'approved') {
                return $this->respondError('Hanya SPPD yang sudah disetujui yang dapat regenerate dokumen', null, 400);
            }

            // Regenerate semua dokumen
            $notaDinasFile = $this->notaDinasGenerator->saveToFile($id);
            $suratTugasFile = $this->suratTugasGenerator->saveToFile($id);
            $spdFile = $this->suratPerjalananDinasGenerator->saveToFile($id);

            // Update SPPD
            $this->sppdModel->update($id, [
                'file_nota_dinas' => $notaDinasFile['filename'],
                'file_surat_tugas' => $suratTugasFile['filename'],
                'file_spd' => $spdFile['filename']
            ]);

            $this->logActivity('REGENERATE_DOCUMENTS', "Regenerated documents for SPPD: {$sppd['no_sppd']}");

            return $this->respondSuccess('Dokumen berhasil digenerate ulang', [
                'documents' => [
                    'nota_dinas' => $notaDinasFile,
                    'surat_tugas' => $suratTugasFile,
                    'spd' => $spdFile
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Regenerate Documents Error: ' . $e->getMessage());
            return $this->respondError('Gagal regenerate dokumen: ' . $e->getMessage(), null, 500);
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
            // Dropdown menu untuk download dokumen
            $buttons .= '<div class="relative inline-block text-left">
                <button type="button" class="text-purple-600 hover:text-purple-800 inline-flex items-center" onclick="toggleDropdown(' . $id . ')">
                    <i class="fas fa-download"></i>
                    <i class="fas fa-chevron-down ml-1 text-xs"></i>
                </button>
                <div id="dropdown-' . $id . '" class="hidden origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                    <div class="py-1">
                        <a href="' . site_url('kepaladinas/sppd/download-nota-dinas/' . $id) . '" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" target="_blank">
                            <i class="fas fa-file-pdf mr-2"></i>Nota Dinas
                        </a>
                        <a href="' . site_url('kepaladinas/sppd/download-surat-tugas/' . $id) . '" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" target="_blank">
                            <i class="fas fa-file-pdf mr-2"></i>Surat Tugas
                        </a>
                        <a href="' . site_url('kepaladinas/sppd/download-spd/' . $id) . '" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" target="_blank">
                            <i class="fas fa-file-pdf mr-2"></i>Surat Perjalanan Dinas
                        </a>
                        <hr class="my-1">
                        <a href="' . site_url('kepaladinas/sppd/download-all/' . $id) . '" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-archive mr-2"></i>Download Semua (ZIP)
                        </a>
                    </div>
                </div>
            </div>';
            
            $buttons .= '<button class="btn-regenerate text-orange-600 hover:text-orange-800" data-id="' . $id . '" title="Regenerate Dokumen"><i class="fas fa-sync"></i></button>';
        }
        
        $buttons .= '</div>';
        
        // Add script for dropdown toggle
        $buttons .= '<script>
            function toggleDropdown(id) {
                const dropdown = document.getElementById("dropdown-" + id);
                dropdown.classList.toggle("hidden");
                
                // Close dropdown when clicking outside
                document.addEventListener("click", function(e) {
                    if (!e.target.closest(".relative")) {
                        dropdown.classList.add("hidden");
                    }
                }, { once: true });
            }
        </script>';
        
        return $buttons;
    }
}