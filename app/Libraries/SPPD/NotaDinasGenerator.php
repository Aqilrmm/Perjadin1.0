<?php

namespace App\Libraries\SPPD;

use App\Libraries\PDF\PDFGenerator;
use App\Models\SPPD\SPPDModel;
use App\Models\SPPD\SPPDPegawaiModel;
use App\Models\User\UserModel;
use App\Models\Bidang\BidangModel;

/**
 * Nota Dinas Generator
 * 
 * Generates Nota Dinas PDF for SPPD
 */
class NotaDinasGenerator
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
     * Generate Nota Dinas PDF
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
            ->setTitle('Nota Dinas - ' . ($sppd['no_sppd'] ?? 'Draft'))
            ->setAuthor('Aplikasi Perjadin')
            ->setSubject('Nota Dinas Perjalanan Dinas')
            ->generate($html);

        // Set default filename
        if (!$filename) {
            $noSppd = $sppd['no_sppd'] ?? 'DRAFT_' . $sppdId;
            $filename = 'Nota_Dinas_' . str_replace('/', '_', $noSppd) . '.pdf';
        }

        return $this->pdfGenerator->output($filename, $outputType);
    }

    /**
     * Generate HTML for Nota Dinas
     * 
     * @param array $sppd
     * @param array $pegawaiList
     * @return string
     */
    protected function generateHTML(array $sppd, array $pegawaiList): string
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body {
                    font-family: "Arial", sans-serif;
                    font-size: 11pt;
                    line-height: 1.5;
                    color: #000;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                }
                .header h2 {
                    margin: 5px 0;
                    font-size: 14pt;
                }
                .kop-surat {
                    border-bottom: 3px solid #000;
                    padding-bottom: 10px;
                    margin-bottom: 20px;
                }
                .kop-surat h1 {
                    margin: 0;
                    font-size: 16pt;
                    font-weight: bold;
                }
                .kop-surat p {
                    margin: 2px 0;
                    font-size: 10pt;
                }
                .content {
                    margin: 20px 0;
                }
                .info-table {
                    width: 100%;
                    margin: 15px 0;
                }
                .info-table td {
                    padding: 5px;
                    vertical-align: top;
                }
                .info-table td:first-child {
                    width: 150px;
                }
                .pegawai-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 15px 0;
                }
                .pegawai-table th,
                .pegawai-table td {
                    border: 1px solid #000;
                    padding: 8px;
                    text-align: left;
                }
                .pegawai-table th {
                    background-color: #f0f0f0;
                    font-weight: bold;
                }
                .signature {
                    margin-top: 40px;
                }
                .signature-box {
                    float: right;
                    width: 250px;
                    text-align: center;
                }
                .signature-box p {
                    margin: 5px 0;
                }
                .signature-space {
                    height: 60px;
                }
                .footer {
                    clear: both;
                    margin-top: 80px;
                    font-size: 9pt;
                    color: #666;
                }
            </style>
        </head>
        <body>';

        // Kop Surat
        $html .= '
            <div class="kop-surat">
                <table width="100%">
                    <tr>
                        <td width="80" style="text-align: center;">
                            <!-- Logo dinas bisa ditambahkan di sini -->
                        </td>
                        <td style="text-align: center;">
                            <h1>PEMERINTAH KOTA/KABUPATEN</h1>
                            <h1>DINAS [NAMA DINAS]</h1>
                            <p>Alamat: [Alamat Kantor]</p>
                            <p>Telepon: [No Telepon] | Email: [Email]</p>
                        </td>
                    </tr>
                </table>
            </div>';

        // Header Nota Dinas
        $html .= '
            <div class="header">
                <h2><u>NOTA DINAS</u></h2>
                <p>Nomor: ' . ($sppd['no_sppd'] ?? '[Nomor akan digenerate]') . '</p>
            </div>';

        // Kepada/Dari
        $html .= '
            <table class="info-table">
                <tr>
                    <td>Kepada</td>
                    <td>: Kepala Dinas [Nama Dinas]</td>
                </tr>
                <tr>
                    <td>Dari</td>
                    <td>: ' . htmlspecialchars($sppd['nama_bidang'] ?? '-') . '</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>: ' . date('d F Y') . '</td>
                </tr>
                <tr>
                    <td>Perihal</td>
                    <td>: <strong>Permohonan Perjalanan Dinas</strong></td>
                </tr>
            </table>';

        // Isi Nota Dinas
        $html .= '
            <div class="content">
                <p>Yang bertanda tangan di bawah ini, dengan hormat mengajukan permohonan perjalanan dinas dengan rincian sebagai berikut:</p>
                
                <table class="info-table">
                    <tr>
                        <td><strong>Maksud Perjalanan</strong></td>
                        <td>: ' . nl2br(htmlspecialchars($sppd['maksud_perjalanan'] ?? '-')) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Dasar</strong></td>
                        <td>: ' . htmlspecialchars($sppd['dasar_surat'] ?? '-') . '</td>
                    </tr>
                    <tr>
                        <td><strong>Tipe Perjalanan</strong></td>
                        <td>: ' . ucfirst($sppd['tipe_perjalanan'] ?? '-') . '</td>
                    </tr>
                    <tr>
                        <td><strong>Tempat Tujuan</strong></td>
                        <td>: ' . htmlspecialchars($sppd['tempat_tujuan'] ?? '-') . '</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Berangkat</strong></td>
                        <td>: ' . $this->formatDate($sppd['tanggal_berangkat']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Kembali</strong></td>
                        <td>: ' . $this->formatDate($sppd['tanggal_kembali']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Lama Perjalanan</strong></td>
                        <td>: ' . ($sppd['lama_perjalanan'] ?? 0) . ' hari</td>
                    </tr>
                    <tr>
                        <td><strong>Alat Angkut</strong></td>
                        <td>: ' . htmlspecialchars($sppd['alat_angkut'] ?? '-') . '</td>
                    </tr>
                    <tr>
                        <td><strong>Estimasi Biaya</strong></td>
                        <td>: <strong>' . $this->formatRupiah($sppd['estimasi_biaya'] ?? 0) . '</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Program</strong></td>
                        <td>: ' . htmlspecialchars($sppd['nama_program'] ?? '-') . '</td>
                    </tr>
                </table>

                <p style="margin-top: 20px;"><strong>Daftar Pegawai yang Melaksanakan Perjalanan Dinas:</strong></p>
                
                <table class="pegawai-table">
                    <thead>
                        <tr>
                            <th width="30" style="text-align: center;">No</th>
                            <th>Nama Pegawai</th>
                            <th>NIP/NIK</th>
                            <th>Jabatan</th>
                        </tr>
                    </thead>
                    <tbody>';

        // Daftar Pegawai
        $no = 1;
        foreach ($pegawaiList as $pegawai) {
            $html .= '
                        <tr>
                            <td style="text-align: center;">' . $no++ . '</td>
                            <td>' . htmlspecialchars($pegawai['nama'] ?? '-') . '</td>
                            <td>' . htmlspecialchars($pegawai['nip_nik'] ?? '-') . '</td>
                            <td>' . htmlspecialchars($pegawai['jabatan'] ?? '-') . '</td>
                        </tr>';
        }

        $html .= '
                    </tbody>
                </table>

                <p style="margin-top: 20px;">Demikian nota dinas ini kami sampaikan, atas perhatian dan persetujuannya kami ucapkan terima kasih.</p>
            </div>';

        // Tanda Tangan
        $penanggungJawab = $sppd['penanggung_jawab_nama'] ?? 'Penanggung Jawab';
        $html .= '
            <div class="signature">
                <div class="signature-box">
                    <p>' . date('d F Y') . '</p>
                    <p><strong>Penanggung Jawab</strong></p>
                    <div class="signature-space"></div>
                    <p><strong><u>' . htmlspecialchars($penanggungJawab) . '</u></strong></p>
                    <p>NIP. ' . ($sppd['penanggung_jawab_nip'] ?? '[NIP]') . '</p>
                </div>
            </div>';

        // Catatan Persetujuan (jika sudah approved)
        if (!empty($sppd['status']) && $sppd['status'] === 'approved') {
            $html .= '
            <div style="clear: both; margin-top: 100px; border-top: 2px solid #000; padding-top: 20px;">
                <p><strong>PERSETUJUAN KEPALA DINAS</strong></p>
                <table class="info-table">
                    <tr>
                        <td width="150">Nomor SPPD</td>
                        <td>: <strong>' . htmlspecialchars($sppd['no_sppd']) . '</strong></td>
                    </tr>
                    <tr>
                        <td>Tanggal Persetujuan</td>
                        <td>: ' . $this->formatDate($sppd['approved_at_kepaladinas']) . '</td>
                    </tr>';
            
            if (!empty($sppd['catatan_kepala_dinas'])) {
                $html .= '
                    <tr>
                        <td>Catatan</td>
                        <td>: ' . nl2br(htmlspecialchars($sppd['catatan_kepala_dinas'])) . '</td>
                    </tr>';
            }
            
            $html .= '
                </table>
                
                <div class="signature-box" style="margin-top: 20px;">
                    <p><strong>Kepala Dinas</strong></p>
                    <div class="signature-space"></div>
                    <p><strong><u>[Nama Kepala Dinas]</u></strong></p>
                    <p>NIP. [NIP Kepala Dinas]</p>
                </div>
            </div>';
        }

        // Footer
        $html .= '
            <div class="footer">
                <p>Dokumen ini digenerate oleh sistem pada ' . date('d F Y H:i:s') . '</p>
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
            $directory = FCPATH . 'uploads/nota_dinas/';
        }

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $sppd = $this->sppdModel->find($sppdId);
        
        if (!$sppd) {
            throw new \Exception('SPPD tidak ditemukan');
        }

        $noSppd = $sppd['no_sppd'] ?? 'DRAFT_' . $sppdId;
        $filename = 'Nota_Dinas_' . str_replace('/', '_', $noSppd) . '.pdf';
        $filepath = $directory . $filename;

        $this->generate($sppdId, 'F', $filepath);

        return [
            'filename' => $filename,
            'filepath' => $filepath,
            'url' => base_url('uploads/nota_dinas/' . $filename),
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
     * Format rupiah helper
     */
    protected function formatRupiah($amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}