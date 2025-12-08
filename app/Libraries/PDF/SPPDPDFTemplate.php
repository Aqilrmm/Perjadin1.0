<?php

namespace App\Libraries\PDF;

use App\Models\SPPD\SPPDModel;
use App\Models\SPPD\SPPDPegawaiModel;
use App\Models\SPPD\LPPDModel;
use App\Models\SPPD\KwitansiModel;
use App\Models\User\UserModel;

/**
 * SPPD PDF Template Generator
 * 
 * Generates various SPPD-related PDF documents
 */
class SPPDPDFTemplate
{
    protected $sppdModel;
    protected $sppdPegawaiModel;
    protected $lppdModel;
    protected $kwitansiModel;
    protected $userModel;
    protected $pdfGenerator;

    public function __construct()
    {
        $this->sppdModel = new SPPDModel();
        $this->sppdPegawaiModel = new SPPDPegawaiModel();
        $this->lppdModel = new LPPDModel();
        $this->kwitansiModel = new KwitansiModel();
        $this->userModel = new UserModel();
        $this->pdfGenerator = new PDFGenerator();
    }

    /**
     * Generate SPPD document
     * 
     * @param int $sppdId
     * @param string $outputType 'I', 'D', 'F', 'S'
     * @param string|null $filename
     * @return mixed
     */
    public function generateSPPD(int $sppdId, string $outputType = 'I', ?string $filename = null)
    {
        $sppd = $this->sppdModel->getWithRelations($sppdId);
        
        if (!$sppd) {
            throw new \Exception('SPPD tidak ditemukan');
        }

        $pegawaiList = $this->sppdPegawaiModel->getPegawaiBySppdId($sppdId);
        
        $data = [
            'sppd' => $sppd,
            'pegawai_list' => $pegawaiList,
            'generated_at' => date('d F Y H:i:s')
        ];

        $html = $this->getTemplate('sppd');
        $html = $this->fillTemplate($html, $data);

        $this->pdfGenerator
            ->setTitle('SPPD - ' . $sppd['no_sppd'])
            ->setAuthor('Aplikasi Perjadin')
            ->generate($html);

        if (!$filename) {
            $filename = 'SPPD_' . str_replace('/', '_', $sppd['no_sppd']) . '.pdf';
        }

        return $this->pdfGenerator->output($filename, $outputType);
    }

    /**
     * Generate LPPD document
     * 
     * @param int $sppdId
     * @param string $outputType
     * @param string|null $filename
     * @return mixed
     */
    public function generateLPPD(int $sppdId, string $outputType = 'I', ?string $filename = null)
    {
        $sppd = $this->sppdModel->getWithRelations($sppdId);
        $lppd = $this->lppdModel->getBySppd($sppdId);
        
        if (!$sppd || !$lppd) {
            throw new \Exception('SPPD atau LPPD tidak ditemukan');
        }

        $pegawaiList = $this->sppdPegawaiModel->getPegawaiBySppdId($sppdId);
        
        $data = [
            'sppd' => $sppd,
            'lppd' => $lppd,
            'pegawai_list' => $pegawaiList,
            'dokumentasi' => json_decode($lppd['dokumentasi'] ?? '[]', true),
            'generated_at' => date('d F Y H:i:s')
        ];

        $html = $this->getTemplate('lppd');
        $html = $this->fillTemplate($html, $data);

        $this->pdfGenerator
            ->setTitle('LPPD - ' . $sppd['no_sppd'])
            ->setAuthor('Aplikasi Perjadin')
            ->generate($html);

        if (!$filename) {
            $filename = 'LPPD_' . str_replace('/', '_', $sppd['no_sppd']) . '.pdf';
        }

        return $this->pdfGenerator->output($filename, $outputType);
    }

    /**
     * Generate Kwitansi document
     * 
     * @param int $sppdId
     * @param string $outputType
     * @param string|null $filename
     * @return mixed
     */
    public function generateKwitansi(int $sppdId, string $outputType = 'I', ?string $filename = null)
    {
        $sppd = $this->sppdModel->getWithRelations($sppdId);
        $kwitansi = $this->kwitansiModel->getBySppd($sppdId);
        
        if (!$sppd || !$kwitansi) {
            throw new \Exception('SPPD atau Kwitansi tidak ditemukan');
        }

        $pegawai = $this->userModel->find($kwitansi['pegawai_id']);
        
        $data = [
            'sppd' => $sppd,
            'kwitansi' => $kwitansi,
            'pegawai' => $pegawai,
            'generated_at' => date('d F Y H:i:s')
        ];

        $html = $this->getTemplate('kwitansi');
        $html = $this->fillTemplate($html, $data);

        $this->pdfGenerator
            ->setTitle('Kwitansi - ' . $sppd['no_sppd'])
            ->setAuthor('Aplikasi Perjadin')
            ->generate($html);

        if (!$filename) {
            $filename = 'Kwitansi_' . str_replace('/', '_', $sppd['no_sppd']) . '.pdf';
        }

        return $this->pdfGenerator->output($filename, $outputType);
    }

    /**
     * Generate complete package (SPPD + LPPD + Kwitansi)
     * 
     * @param int $sppdId
     * @param string $outputType
     * @param string|null $filename
     * @return mixed
     */
    public function generatePackage(int $sppdId, string $outputType = 'I', ?string $filename = null)
    {
        $sppd = $this->sppdModel->getWithRelations($sppdId);
        
        if (!$sppd) {
            throw new \Exception('SPPD tidak ditemukan');
        }

        $this->pdfGenerator->setTitle('Paket Dokumen - ' . $sppd['no_sppd']);

        // Add SPPD
        $pegawaiList = $this->sppdPegawaiModel->getPegawaiBySppdId($sppdId);
        $sppdData = ['sppd' => $sppd, 'pegawai_list' => $pegawaiList, 'generated_at' => date('d F Y H:i:s')];
        $sppdHtml = $this->fillTemplate($this->getTemplate('sppd'), $sppdData);
        $this->pdfGenerator->generate($sppdHtml);

        // Add LPPD if exists
        $lppd = $this->lppdModel->getBySppd($sppdId);
        if ($lppd) {
            $this->pdfGenerator->addPage();
            $lppdData = [
                'sppd' => $sppd,
                'lppd' => $lppd,
                'pegawai_list' => $pegawaiList,
                'dokumentasi' => json_decode($lppd['dokumentasi'] ?? '[]', true),
                'generated_at' => date('d F Y H:i:s')
            ];
            $lppdHtml = $this->fillTemplate($this->getTemplate('lppd'), $lppdData);
            $this->pdfGenerator->generate($lppdHtml);
        }

        // Add Kwitansi if exists
        $kwitansi = $this->kwitansiModel->getBySppd($sppdId);
        if ($kwitansi) {
            $this->pdfGenerator->addPage();
            $pegawai = $this->userModel->find($kwitansi['pegawai_id']);
            $kwitansiData = [
                'sppd' => $sppd,
                'kwitansi' => $kwitansi,
                'pegawai' => $pegawai,
                'generated_at' => date('d F Y H:i:s')
            ];
            $kwitansiHtml = $this->fillTemplate($this->getTemplate('kwitansi'), $kwitansiData);
            $this->pdfGenerator->generate($kwitansiHtml);
        }

        if (!$filename) {
            $filename = 'Paket_' . str_replace('/', '_', $sppd['no_sppd']) . '.pdf';
        }

        return $this->pdfGenerator->output($filename, $outputType);
    }

    /**
     * Get HTML template for document type
     * 
     * @param string $type
     * @return string
     */
    public function getTemplate(string $type): string
    {
        $templatePath = APPPATH . 'Views/pdf_templates/' . $type . '.php';
        
        if (!file_exists($templatePath)) {
            throw new \Exception("Template {$type} tidak ditemukan");
        }

        return file_get_contents($templatePath);
    }

    /**
     * Fill template with data
     * 
     * @param string $template
     * @param array $data
     * @return string
     */
    public function fillTemplate(string $template, array $data): string
    {
        // Use view parser
        $parser = \Config\Services::parser();
        return $parser->setData($data)->renderString($template);
    }

    /**
     * Format SPPD data for template
     * 
     * @param array $sppd
     * @return array
     */
    public function formatData(array $sppd): array
    {
        return [
            'no_sppd' => $sppd['no_sppd'],
            'tanggal_berangkat' => format_tanggal($sppd['tanggal_berangkat']),
            'tanggal_kembali' => format_tanggal($sppd['tanggal_kembali']),
            'lama_perjalanan' => $sppd['lama_perjalanan'] . ' hari',
            'tempat_tujuan' => $sppd['tempat_tujuan'],
            'maksud_perjalanan' => $sppd['maksud_perjalanan'],
            'estimasi_biaya' => format_rupiah($sppd['estimasi_biaya']),
            'realisasi_biaya' => format_rupiah($sppd['realisasi_biaya'] ?? 0),
            'tipe_perjalanan' => $sppd['tipe_perjalanan'],
            'alat_angkut' => $sppd['alat_angkut'],
            'nama_bidang' => $sppd['nama_bidang'],
            'nama_program' => $sppd['nama_program']
        ];
    }

    /**
     * Get common CSS styles for PDFs
     * 
     * @return string
     */
    protected function getCommonStyles(): string
    {
        return '
        <style>
            body {
                font-family: "DejaVu Sans", Arial, sans-serif;
                font-size: 11pt;
                line-height: 1.6;
            }
            h1 { font-size: 16pt; font-weight: bold; margin-bottom: 10px; }
            h2 { font-size: 14pt; font-weight: bold; margin-bottom: 8px; }
            h3 { font-size: 12pt; font-weight: bold; margin-bottom: 6px; }
            table { width: 100%; border-collapse: collapse; margin: 10px 0; }
            th, td { padding: 8px; text-align: left; }
            th { background-color: #f0f0f0; font-weight: bold; }
            .border { border: 1px solid #000; }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .font-bold { font-weight: bold; }
            .mb-2 { margin-bottom: 10px; }
            .mt-2 { margin-top: 10px; }
        </style>
        ';
    }

    /**
     * Save document to file system
     * 
     * @param int $sppdId
     * @param string $type
     * @param string|null $directory
     * @return array
     */
    public function saveToFile(int $sppdId, string $type = 'sppd', ?string $directory = null): array
    {
        if (!$directory) {
            $directory = FCPATH . 'uploads/documents/';
        }

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $sppd = $this->sppdModel->find($sppdId);
        $filename = strtoupper($type) . '_' . str_replace('/', '_', $sppd['no_sppd']) . '.pdf';
        $filepath = $directory . $filename;

        switch ($type) {
            case 'sppd':
                $this->generateSPPD($sppdId, 'F', $filepath);
                break;
            case 'lppd':
                $this->generateLPPD($sppdId, 'F', $filepath);
                break;
            case 'kwitansi':
                $this->generateKwitansi($sppdId, 'F', $filepath);
                break;
            case 'package':
                $this->generatePackage($sppdId, 'F', $filepath);
                break;
            default:
                throw new \Exception('Invalid document type');
        }

        return [
            'filename' => $filename,
            'filepath' => $filepath,
            'url' => base_url('uploads/documents/' . $filename),
            'size' => filesize($filepath)
        ];
    }
}