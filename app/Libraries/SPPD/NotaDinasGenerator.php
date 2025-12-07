<?php

namespace App\Libraries\SPPD;

use App\Models\SPPD\SPPDModel;
use App\Models\SPPD\SPPDPegawaiModel;
use App\Models\User\UserModel;

/**
 * Nota Dinas Generator
 * 
 * Generates Nota Dinas PDF documents for SPPD
 */
class NotaDinasGenerator
{
    protected $sppdModel;
    protected $sppdPegawaiModel;
    protected $userModel;
    protected $mpdf;

    public function __construct()
    {
        $this->sppdModel = new SPPDModel();
        $this->sppdPegawaiModel = new SPPDPegawaiModel();
        $this->userModel = new UserModel();
    }

    /**
     * Generate Nota Dinas PDF
     * 
     * @param int $sppdId
     * @param string $outputType 'I' = inline, 'D' = download, 'F' = save to file
     * @param string|null $filename
     * @return mixed
     */
    public function generate($sppdId, $outputType = 'I', $filename = null)
    {
        // Get SPPD data with relations
        $sppd = $this->sppdModel->getWithRelations($sppdId);
        
        if (!$sppd) {
            throw new \Exception('SPPD tidak ditemukan');
        }

        // Get pegawai list
        $pegawaiList = $this->sppdPegawaiModel->getPegawaiBySppdId($sppdId);

        // Get kepala dinas (approver)
        $kepalaDinas = null;
        if ($sppd['approved_by_kepaladinas']) {
            $kepalaDinas = $this->userModel->find($sppd['approved_by_kepaladinas']);
        }

        // Prepare data
        $data = [
            'sppd' => $sppd,
            'pegawai_list' => $pegawaiList,
            'kepala_dinas' => $kepalaDinas,
            'generated_at' => date('d F Y H:i:s'),
        ];

        // Generate HTML content
        $html = $this->generateHtml($data);

        // Create PDF
        $this->initMpdf();
        $this->mpdf->WriteHTML($html);

        // Set filename
        if (!$filename) {
            $filename = 'Nota_Dinas_' . $sppd['no_sppd'] . '_' . date('YmdHis') . '.pdf';
            $filename = str_replace('/', '_', $filename);
        }

        // Output PDF
        return $this->mpdf->Output($filename, $outputType);
    }

    /**
     * Save Nota Dinas to file
     */
    public function saveToFile($sppdId, $directory = null)
    {
        if (!$directory) {
            $directory = FCPATH . 'uploads/nota_dinas/';
        }

        // Create directory if not exists
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $sppd = $this->sppdModel->find($sppdId);
        $filename = 'Nota_Dinas_' . str_replace('/', '_', $sppd['no_sppd']) . '.pdf';
        $filepath = $directory . $filename;

        $this->generate($sppdId, 'F', $filepath);

        return [
            'filename' => $filename,
            'filepath' => $filepath,
            'url' => base_url('uploads/nota_dinas/' . $filename)
        ];
    }

    /**
     * Generate HTML content for Nota Dinas
     */
    protected function generateHtml($data)
    {
        $sppd = $data['sppd'];
        $pegawaiList = $data['pegawai_list'];
        $kepalaDinas = $data['kepala_dinas'];

        // Generate nomor nota dinas
        $noNotaDinas = $this->generateNoNotaDinas($sppd);

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Nota Dinas - ' . $sppd['no_sppd'] . '</title>
            <style>
                ' . $this->getStyles() . '
            </style>
        </head>
        <body>
            <div class="header">
                <table width="100%">
                    <tr>
                        <td width="15%" style="text-align: center; vertical-align: middle;">
                            <img src="' . base_url('assets/images/logo.png') . '" height="80" />
                        </td>
                        <td width="85%" style="text-align: center;">
                            <h2 style="margin: 0;">PEMERINTAH KOTA/KABUPATEN</h2>
                            <h3 style="margin: 5px 0;">DINAS [NAMA DINAS]</h3>
                            <p style="font-size: 11px; margin: 5px 0;">
                                Jl. Alamat Kantor No. XX, Kota, Provinsi<br>
                                Telp: (XXX) XXXXXXX | Email: email@domain.com
                            </p>
                        </td>
                    </tr>
                </table>
                <hr style="border: 2px solid #000; margin: 10px 0;">
            </div>

            <div class="content">
                <table class="nota-header">
                    <tr>
                        <td width="20%">Nomor</td>
                        <td width="2%">:</td>
                        <td>' . $noNotaDinas . '</td>
                    </tr>
                    <tr>
                        <td>Sifat</td>
                        <td>:</td>
                        <td>Segera</td>
                    </tr>
                    <tr>
                        <td>Lampiran</td>
                        <td>:</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>Hal</td>
                        <td>:</td>
                        <td><strong>Perjalanan Dinas</strong></td>
                    </tr>
                </table>

                <p style="margin-top: 30px;">
                    Kepada Yth.<br>
                    <strong>Kepala Bagian Keuangan</strong><br>
                    di Tempat
                </p>

                <p style="text-align: justify; text-indent: 50px; margin-top: 20px;">
                    Berdasarkan Surat Tugas Nomor <strong>' . $sppd['dasar_surat'] . '</strong>, dengan ini 
                    diberitahukan bahwa pegawai yang namanya tersebut di bawah ini:
                </p>

                <table class="pegawai-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="35%">Nama</th>
                            <th width="25%">NIP/NIK</th>
                            <th width="35%">Jabatan</th>
                        </tr>
                    </thead>
                    <tbody>';

        $no = 1;
        foreach ($pegawaiList as $pegawai) {
            $html .= '
                        <tr>
                            <td style="text-align: center;">' . $no++ . '</td>
                            <td>' . get_nama_lengkap($pegawai) . '</td>
                            <td>' . $pegawai['nip_nik'] . '</td>
                            <td>' . $pegawai['jabatan'] . '</td>
                        </tr>';
        }

        $html .= '
                    </tbody>
                </table>

                <p style="text-align: justify; margin-top: 20px;">
                    Akan melakukan perjalanan dinas dengan ketentuan sebagai berikut:
                </p>

                <table class="detail-table">
                    <tr>
                        <td width="30%">Maksud Perjalanan</td>
                        <td width="2%">:</td>
                        <td>' . $sppd['maksud_perjalanan'] . '</td>
                    </tr>
                    <tr>
                        <td>Tipe Perjalanan</td>
                        <td>:</td>
                        <td>' . $sppd['tipe_perjalanan'] . '</td>
                    </tr>
                    <tr>
                        <td>Tempat Berangkat</td>
                        <td>:</td>
                        <td>' . $sppd['tempat_berangkat'] . '</td>
                    </tr>
                    <tr>
                        <td>Tempat Tujuan</td>
                        <td>:</td>
                        <td>' . $sppd['tempat_tujuan'] . '</td>
                    </tr>
                    <tr>
                        <td>Tanggal Berangkat</td>
                        <td>:</td>
                        <td>' . format_tanggal($sppd['tanggal_berangkat']) . '</td>
                    </tr>
                    <tr>
                        <td>Tanggal Kembali</td>
                        <td>:</td>
                        <td>' . format_tanggal($sppd['tanggal_kembali']) . '</td>
                    </tr>
                    <tr>
                        <td>Lama Perjalanan</td>
                        <td>:</td>
                        <td>' . $sppd['lama_perjalanan'] . ' hari</td>
                    </tr>
                    <tr>
                        <td>Alat Angkut</td>
                        <td>:</td>
                        <td>' . $sppd['alat_angkut'] . '</td>
                    </tr>
                    <tr>
                        <td>Estimasi Biaya</td>
                        <td>:</td>
                        <td><strong>' . format_rupiah($sppd['estimasi_biaya']) . '</strong></td>
                    </tr>
                </table>

                <p style="text-align: justify; margin-top: 20px;">
                    Demikian nota dinas ini dibuat untuk dapat dipergunakan sebagaimana mestinya.
                </p>

                <table style="width: 100%; margin-top: 40px;">
                    <tr>
                        <td width="50%"></td>
                        <td width="50%" style="text-align: center;">
                            <p>Dikeluarkan di : [KOTA]<br>
                            Pada tanggal : ' . format_tanggal($sppd['approved_at_kepaladinas'] ?? date('Y-m-d')) . '</p>
                            <p style="margin-top: 10px; margin-bottom: 80px;">
                                <strong>KEPALA DINAS</strong>
                            </p>
                            <p style="margin-top: 80px;">
                                <strong><u>' . ($kepalaDinas ? get_nama_lengkap($kepalaDinas) : '[Nama Kepala Dinas]') . '</u></strong><br>
                                NIP. ' . ($kepalaDinas['nip_nik'] ?? '[NIP]') . '
                            </p>
                        </td>
                    </tr>
                </table>

                <div class="footer">
                    <p style="font-size: 9px; text-align: center; color: #666;">
                        Dokumen ini dibuat secara elektronik pada ' . $data['generated_at'] . '<br>
                        SPPD No: ' . $sppd['no_sppd'] . ' | ' . $sppd['nama_bidang'] . '
                    </p>
                </div>
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Generate Nota Dinas number
     */
    protected function generateNoNotaDinas($sppd)
    {
        $kodeBidang = $sppd['kode_bidang'] ?? 'DINAS';
        $bulan = bulan_romawi(date('n'));
        $tahun = date('Y');
        
        // Get counter
        $count = $this->sppdModel->where('YEAR(approved_at_kepaladinas)', $tahun)
                                 ->whereNotNull('approved_at_kepaladinas')
                                 ->countAllResults();

        $urut = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        return "ND/{$kodeBidang}/{$bulan}/{$tahun}/{$urut}";
    }

    /**
     * Get CSS styles for PDF
     */
    protected function getStyles()
    {
        return '
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                line-height: 1.6;
                margin: 20px;
            }
            
            .header {
                margin-bottom: 20px;
            }
            
            h2, h3 {
                color: #000;
            }
            
            .nota-header {
                width: 100%;
                margin-bottom: 20px;
            }
            
            .nota-header td {
                padding: 3px 0;
                vertical-align: top;
            }
            
            .pegawai-table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
            }
            
            .pegawai-table th,
            .pegawai-table td {
                border: 1px solid #000;
                padding: 8px;
            }
            
            .pegawai-table th {
                background-color: #f0f0f0;
                font-weight: bold;
                text-align: center;
            }
            
            .detail-table {
                width: 100%;
                margin: 20px 0;
            }
            
            .detail-table td {
                padding: 5px 0;
                vertical-align: top;
            }
            
            .footer {
                margin-top: 40px;
                border-top: 1px solid #ccc;
                padding-top: 10px;
            }
        ';
    }

    /**
     * Initialize mPDF
     */
    protected function initMpdf()
    {
        $config = [
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_header' => 10,
            'margin_footer' => 10,
        ];

        $this->mpdf = new \Mpdf\Mpdf($config);
        
        // Set document properties
        $this->mpdf->SetTitle('Nota Dinas');
        $this->mpdf->SetAuthor('Aplikasi Perjadin');
        $this->mpdf->SetCreator('Aplikasi Perjadin');
    }

    /**
     * Get file size
     */
    public function getFileSize($sppdId)
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'nota_dinas_');
        $this->generate($sppdId, 'F', $tempFile);
        $size = filesize($tempFile);
        unlink($tempFile);
        
        return $size;
    }
}