<?php

namespace App\Libraries\SPPD;

use App\Libraries\PDF\PDFGenerator;
use App\Models\SPPD\SPPDModel;
use App\Models\SPPD\SPPDPegawaiModel;
use App\Models\User\UserModel;
use App\Models\Bidang\BidangModel;

/**
 * Surat Perjalanan Dinas Generator
 * 
 * Generates Surat Perjalanan Dinas (SPD) PDF for SPPD
 */
class SuratPerjalananDinasGenerator
{
    protected $sppdModel;
    protected $sppdPegawaiModel;
    protected $userModel;
    protected $bidangModel;
    protected $pdfGenerator;

    public function __construct()
    {
        $this->sppdModel = new SPPDModel();
        $this->sppdPegawaiModel = new SPPDPegawaiModel();
        $this->userModel = new UserModel();
        $this->bidangModel = new BidangModel();
        $this->pdfGenerator = new PDFGenerator([
            'format' => 'A4',
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 20,
            'margin_bottom' => 20,
        ]);
    }

    /**
     * Generate Surat Perjalanan Dinas PDF
     * 
     * @param int $sppdId
     * @param string $outputType 'I' = inline, 'D' = download, 'F' = save to file, 'S' = string
     * @param string|null $filename
     * @return mixed
     */
    public function generate(int $sppdId, string $outputType = 'I', ?string $filename = null)
    {
        $sppd = $this->sppdModel->getWithRelations($sppdId);
        
        if (!$sppd) {
            throw new \Exception('SPPD tidak ditemukan');
        }

        $pegawaiList = $this->sppdPegawaiModel->getPegawaiBySppdId($sppdId);
        
        // Generate HTML
        $html = $this->generateHTML($sppd, $pegawaiList);

        // Set document properties
        $this->pdfGenerator
            ->setTitle('Surat Perjalanan Dinas - ' . ($sppd['no_sppd'] ?? 'Draft'))
            ->setAuthor('Aplikasi Perjadin')
            ->setSubject('Surat Perjalanan Dinas')
            ->generate($html);

        // Set default filename
        if (!$filename) {
            $noSppd = $sppd['no_sppd'] ?? 'DRAFT_' . $sppdId;
            $filename = 'SPD_' . str_replace('/', '_', $noSppd) . '.pdf';
        }

        return $this->pdfGenerator->output($filename, $outputType);
    }

    /**
     * Generate HTML for SPD
     * 
     * @param array $sppd
     * @param array $pegawaiList
     * @return string
     */
    protected function generateHTML(array $sppd, array $pegawaiList): string
    {
        // Get first pegawai as main person
        $mainPegawai = !empty($pegawaiList) ? $pegawaiList[0] : null;
        
        $html = '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Perjalanan Dinas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        
        .container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 30px 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .header {
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            display: flex;
            align-items: center;
            position: relative;
        }
        
        .header-text {
            flex: 1;
            text-align: center;
        }
        
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 3px 0;
        }
        
        .header p {
            font-size: 11px;
            margin: 2px 0;
        }
        
        .title {
            text-align: center;
            margin: 30px 0 30px 0;
        }
        
        .title h2 {
            font-size: 16px;
            text-decoration: underline;
            font-weight: bold;
        }
        
        .spd-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 12px;
        }
        
        .spd-table td {
            padding: 8px;
            vertical-align: top;
            border: 1px solid #000;
        }
        
        .spd-table td:first-child {
            width: 30px;
            text-align: center;
        }
        
        .spd-table .label {
            width: 250px;
        }
        
        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin: 25px 0 15px 0;
        }
        
        .berangkat-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11px;
        }
        
        .berangkat-table th,
        .berangkat-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        
        .berangkat-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .berangkat-table .row-height {
            height: 60px;
        }
        
        .signature-section {
            margin-top: 30px;
            font-size: 12px;
        }
        
        .signature-box {
            text-align: center;
            margin: 20px 0;
        }
        
        .signature-box p {
            margin-bottom: 5px;
        }
        
        .signature-space {
            height: 60px;
        }
        
        .signature-box .name {
            font-weight: bold;
        }
        
        .signature-box .nip {
            font-size: 11px;
        }
        
        .footer-note {
            margin-top: 20px;
            font-size: 10px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-text">
                <h1>PEMERINTAH KABUPATEN TANAH BUMBU</h1>
                <h1>DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU</h1>
                <p>Alamat: Jln. Dharma Praja No. 01 Gunung Tinggi Kec. Batulicin Kab. Tanah Bumbu</p>
                <p>Telp. (0518) 61/0053</p>
                <p>Laman: https://dpmptsp.tanahbumbukab.go.id</p>
            </div>
        </div>
        
        <div class="title">
            <h2>SURAT PERJALANAN DINAS</h2>
        </div>
        
        <table class="spd-table">
            <tr>
                <td>1.</td>
                <td class="label">Pejabat yang memberi perintah</td>
                <td>: ' . htmlspecialchars($sppd['penanggung_jawab_nama'] ?? 'Kepala Dinas DPMPTSP') . '</td>
            </tr>
            <tr>
                <td>2.</td>
                <td class="label">Nama/NIP Pegawai yang melaksanakan perjalanan dinas</td>
                <td>: ';
        
        if ($mainPegawai) {
            $html .= '<b>a.</b> Nama: ' . htmlspecialchars($mainPegawai['nama'] ?? '-') . '<br>';
            $html .= '<b>b.</b> NIP: ' . htmlspecialchars($mainPegawai['nip_nik'] ?? '-') . '<br>';
            $html .= '<b>c.</b> Pangkat/Gol: ' . htmlspecialchars($mainPegawai['pangkat'] ?? 'Lorem ipsum') . ' / IV/c';
        } else {
            $html .= '-';
        }
        
        $html .= '</td>
            </tr>
            <tr>
                <td>3.</td>
                <td class="label">a. Pangkat dan Golongan<br>b. Jabatan/Instansi<br>c. Tingkat Biaya Perjalanan Dinas</td>
                <td>: a. ' . htmlspecialchars($mainPegawai['pangkat'] ?? 'Lorem ipsum dolor') . ' / IV/c<br>
                    b. ' . htmlspecialchars($mainPegawai['jabatan'] ?? 'Pegawai') . '<br>
                    c. A</td>
            </tr>
            <tr>
                <td>4.</td>
                <td class="label">Maksud Perjalanan Dinas</td>
                <td>: ' . nl2br(htmlspecialchars($sppd['maksud_perjalanan'] ?? '-')) . '</td>
            </tr>
            <tr>
                <td>5.</td>
                <td class="label">Alat Angkutan yang dipergunakan</td>
                <td>: ' . htmlspecialchars($sppd['alat_angkut'] ?? 'Darat') . '</td>
            </tr>
            <tr>
                <td>6.</td>
                <td class="label">a. Tempat berangkat<br>b. Tempat tujuan</td>
                <td>: a. ' . htmlspecialchars($sppd['tempat_berangkat'] ?? 'Lorem ipsum') . '<br>
                    b. ' . htmlspecialchars($sppd['tempat_tujuan'] ?? 'Dapil I - Kecamatan Lorem ipsum') . '</td>
            </tr>
            <tr>
                <td>7.</td>
                <td class="label">a. Lamanya Perjalanan Dinas<br>b. Tanggal berangkat<br>c. Tanggal harus kembali</td>
                <td>: a. ' . ($sppd['lama_perjalanan'] ?? 3) . ' (tiga) hari/' . (($sppd['lama_perjalanan'] ?? 3) - 1) . ' (' . $this->terbilang(($sppd['lama_perjalanan'] ?? 3) - 1) . ') malam<br>
                    b. ' . $this->formatDate($sppd['tanggal_berangkat']) . '<br>
                    c. ' . $this->formatDate($sppd['tanggal_kembali']) . '</td>
            </tr>
            <tr>
                <td>8.</td>
                <td class="label">Pembebanan Anggaran</td>
                <td>
                    a. APBD/APBN<br>
                    b. Tahun Anggaran: ' . date('Y', strtotime($sppd['tanggal_berangkat'])) . '<br>
                    c. Nomor DPA: ' . htmlspecialchars($sppd['kode_program'] ?? 'Lorem ipsum dolor') . '<br>
                    d. Kode Rekening Belanja: 5.1.02.04.01.0001
                </td>
            </tr>
            <tr>
                <td>9.</td>
                <td class="label">Keterangan lain-lain</td>
                <td>: ' . htmlspecialchars($sppd['catatan_kepala_dinas'] ?? 'Melaksanakan tugas dengan sebaik-baiknya') . '</td>
            </tr>
        </table>
        
        <div class="signature-section">
            <div class="signature-box" style="text-align: right; display: inline-block; width: 100%;">
                <div style="display: inline-block; text-align: center; min-width: 250px;">
                    <p>Dikeluarkan di: Batulicin</p>
                    <p>Pada Tanggal: ' . $this->formatDate($sppd['tanggal_berangkat'] ?? date('Y-m-d')) . '</p>
                    <p style="margin-top: 15px;">Pejabat yang memberi Perintah</p>
                    <div class="signature-space"></div>
                    <p class="name">' . htmlspecialchars($sppd['penanggung_jawab_nama'] ?? 'Andriano Wirakusno SE., M.Sos') . '</p>
                    <p class="nip">NIP: ' . htmlspecialchars($sppd['penanggung_jawab_nip'] ?? '19717223.200312.1.010') . '</p>
                </div>
            </div>
        </div>
        
        <div class="section-title">I. Berangkat dari: ' . htmlspecialchars($sppd['tempat_berangkat'] ?? 'Lorem Ipsum') . ' (Kantor/Tempat Kedudukan)</div>
        <div style="font-size: 12px; margin-bottom: 10px;">Ke: ' . htmlspecialchars($sppd['tempat_tujuan'] ?? 'Dapil I - Kecamatan Lorem ipsum dolor sit') . '</div>
        
        <table class="berangkat-table">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th colspan="3">Tempat/Tgl/Tanda tangan Pejabat</th>
                </tr>
                <tr>
                    <th>Berangkat dari</th>
                    <th>Tiba di</th>
                    <th>Berangkat dari</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td class="row-height"></td>
                    <td class="row-height"></td>
                    <td class="row-height"></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td class="row-height"></td>
                    <td class="row-height"></td>
                    <td class="row-height"></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td class="row-height"></td>
                    <td class="row-height"></td>
                    <td class="row-height"></td>
                </tr>
                <tr>
                    <td>4</td>
                    <td class="row-height"></td>
                    <td class="row-height"></td>
                    <td class="row-height"></td>
                </tr>
            </tbody>
        </table>
        
        <div class="section-title">II. Tiba kembali di: ' . htmlspecialchars($sppd['tempat_berangkat'] ?? 'Lorem ipsum') . ' (tempat kedudukan)</div>
        <div style="font-size: 12px; margin-bottom: 10px;">Pada Tanggal: ' . $this->formatDate($sppd['tanggal_kembali']) . '</div>
        
        <div class="signature-section">
            <div class="signature-box" style="text-align: right; display: inline-block; width: 100%;">
                <div style="display: inline-block; text-align: center; min-width: 250px;">
                    <p>Pejabat yang memberi Perintah</p>
                    <div class="signature-space"></div>
                    <p class="name">' . htmlspecialchars($sppd['penanggung_jawab_nama'] ?? 'Andriano Wirakusno SE., M.Sos') . '</p>
                    <p class="nip">NIP: ' . htmlspecialchars($sppd['penanggung_jawab_nip'] ?? '19717223.200312.1.010') . '</p>
                </div>
            </div>
        </div>
        
        <div class="section-title">III. Catatan Lain-lain</div>
        <div style="border: 1px solid #000; min-height: 80px; padding: 10px; font-size: 12px;">
            ' . nl2br(htmlspecialchars($sppd['catatan_kepala_dinas'] ?? 'Melaksanakan tugas dengan sebaik-baiknya dan penuh tanggung jawab')) . '
        </div>
        
        <div class="section-title">IV. PERHATIAN:</div>
        <div style="font-size: 11px; line-height: 1.6;">
            <p>1) Pejabat yang berwenang menerbitkan SPPD, pegawai yang melakukan perjalanan dinas, para pejabat yang mengesahkan tanggal berangkat/tiba, serta bendahara pengeluaran bertanggung jawab berdasarkan peraturan-peraturan keuangan negara apabila negara menderita rugi akibat kesalahan, kelalaian, dan kealpaannya.</p>
            <p>2) Perjalanan dinas yang tidak disertai SPPD yang sah, tidak akan dibayarkan biayanya.</p>
        </div>
        
        <div class="footer-note">
            <p>Dokumen ini digenerate oleh sistem pada ' . date('d F Y H:i:s') . '</p>
        </div>
    </div>
</body>
</html>';

        return $html;
    }

    /**
     * Save to file
     * 
     * @param int $sppdId
     * @param string|null $directory
     * @return array
     */
    public function saveToFile(int $sppdId, ?string $directory = null): array
    {
        if (!$directory) {
            $directory = FCPATH . 'uploads/spd/';
        }

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $sppd = $this->sppdModel->find($sppdId);
        
        if (!$sppd) {
            throw new \Exception('SPPD tidak ditemukan');
        }

        $noSppd = $sppd['no_sppd'] ?? 'DRAFT_' . $sppdId;
        $filename = 'SPD_' . str_replace('/', '_', $noSppd) . '.pdf';
        $filepath = $directory . $filename;

        $this->generate($sppdId, 'F', $filepath);

        return [
            'filename' => $filename,
            'filepath' => $filepath,
            'url' => base_url('uploads/spd/' . $filename),
            'size' => filesize($filepath)
        ];
    }

    /**
     * Format date helper
     */
    protected function formatDate($date): string
    {
        if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
            return '-';
        }

        $timestamp = strtotime($date);
        if ($timestamp === false) {
            return '-';
        }

        $months = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        return date('d', $timestamp) . ' ' . 
               $months[(int)date('n', $timestamp)] . ' ' . 
               date('Y', $timestamp);
    }
    
    /**
     * Convert number to Indonesian words
     */
    protected function terbilang($number): string
    {
        $angka = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        
        if ($number < 12) {
            return $angka[$number];
        } elseif ($number < 20) {
            return $angka[$number - 10] . " belas";
        } elseif ($number < 100) {
            return $angka[floor($number / 10)] . " puluh " . $angka[$number % 10];
        } elseif ($number < 200) {
            return "seratus " . $this->terbilang($number - 100);
        } elseif ($number < 1000) {
            return $angka[floor($number / 100)] . " ratus " . $this->terbilang($number % 100);
        }
        
        return (string)$number;
    }
}