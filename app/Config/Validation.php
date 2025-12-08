<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var list<string>
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
        \App\Validation\SPPDRules::class,
        \App\Validation\KwitansiRules::class,
        \App\Validation\CommonRules::class,
        \App\Validation\ProgramRules::class,
        \App\Validation\UserRoles::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------
    /**
     * Common named rule groups to avoid duplication in controllers.
     * Controllers can call `$this->validate('user_create')` etc.
     *
     * @var array<string, array>
     */
    public array $rules = [
        'catatan' => [
            'catatan' => 'required|min_length[10]'
        ],
        'user_create' => [
            'nip_nik' => 'required|min_length[16]|max_length[18]|is_unique[users.nip_nik]',
            'nama' => 'required|min_length[3]|max_length[255]',
            'jenis_pegawai' => 'required|in_list[ASN,Non-ASN]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'jabatan' => 'required|min_length[3]',
            'role' => 'required|in_list[superadmin,kepaladinas,kepalabidang,pegawai,keuangan]'
        ],
        'user_update' => [
            'nip_nik' => 'required|min_length[16]|max_length[18]|is_unique[users.nip_nik,id,{id}]',
            'nama' => 'required|min_length[3]|max_length[255]',
            'jenis_pegawai' => 'required|in_list[ASN,Non-ASN]',
            'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
            'jabatan' => 'required|min_length[3]',
            'role' => 'required|in_list[superadmin,kepaladinas,kepalabidang,pegawai,keuangan]'
        ],
        'program' => [
            'kode_program' => 'required|min_length[5]|max_length[50]|is_unique[programs.kode_program,id,{id}]',
            'nama_program' => 'required|min_length[10]|max_length[255]',
            'bidang_id' => 'required|numeric',
            'tahun_anggaran' => 'required|numeric',
            'jumlah_anggaran' => 'required|numeric|greater_than[1000000]'
        ],
        'kegiatan' => [
            'program_id' => 'required|numeric',
            'kode_kegiatan' => 'required|min_length[5]|max_length[50]|is_unique[kegiatan.kode_kegiatan,id,{id}]',
            'nama_kegiatan' => 'required|min_length[10]|max_length[255]',
            'anggaran_kegiatan' => 'required|numeric|greater_than[0]'
        ],
        'sub_kegiatan' => [
            'kegiatan_id' => 'required|numeric',
            'kode_sub_kegiatan' => 'required|min_length[5]|max_length[50]|is_unique[sub_kegiatan.kode_sub_kegiatan,id,{id}]',
            'nama_sub_kegiatan' => 'required|min_length[10]|max_length[255]',
            'anggaran_sub_kegiatan' => 'required|numeric|greater_than[0]'
        ],
        'bidang' => [
            'kode_bidang' => 'required|min_length[2]|max_length[10]|is_unique[bidang.kode_bidang,id,{id}]',
            'nama_bidang' => 'required|min_length[3]|max_length[255]|is_unique[bidang.nama_bidang,id,{id}]'
        ],
        'sppd_basic' => [
            'program_id' => 'required|numeric',
            'kegiatan_id' => 'required|numeric',
            'sub_kegiatan_id' => 'required|numeric',
            'bidang_id' => 'required|numeric',
            'tipe_perjalanan' => 'required',
            'maksud_perjalanan' => 'required|min_length[20]',
            'dasar_surat' => 'required',
            'alat_angkut' => 'required',
            'tempat_berangkat' => 'required',
            'tempat_tujuan' => 'required',
            'tanggal_berangkat' => 'required|valid_date',
            'lama_perjalanan' => 'required|numeric|greater_than[0]',
            'penanggung_jawab' => 'required|numeric',
            'estimasi_biaya' => 'required|numeric|greater_than[0]',
            'pegawai_ids' => 'required'
        ],
        'kwitansi' => [
            'sppd_id' => 'required|numeric',
            'pegawai_id' => 'required|numeric',
            'total_biaya' => 'required|numeric|greater_than[0]|total_not_exceed_estimasi'
        ],
        'lppd' => [
            'sppd_id' => 'required|numeric',
            'pegawai_id' => 'required|numeric',
            'hasil_kegiatan' => 'required|min_length[50]',
            'tanggal_pengisian' => 'required|valid_date'
        ],
        'laporan' => [
            'periode_start' => 'required|valid_date',
            'periode_end' => 'required|valid_date',
            'format' => 'required|in_list[pdf,excel]'
        ]
    ];
}
