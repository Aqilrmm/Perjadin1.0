<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        // Insert Bidang
        $this->insertBidang();

        // Insert System Settings
        $this->insertSystemSettings();

        // Insert Super Admin
        $this->insertSuperAdmin();
    }

    private function insertBidang()
    {
        $data = [
            [
                'uuid' => $this->generateUUIDD(),
                'kode_bidang' => 'IT',
                'nama_bidang' => 'Bidang Teknologi Informasi',
                'keterangan' => 'Mengelola infrastruktur dan aplikasi teknologi informasi',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'uuid' => $this->generateUUIDD(),
                'kode_bidang' => 'KEU',
                'nama_bidang' => 'Bidang Keuangan',
                'keterangan' => 'Mengelola keuangan dan anggaran instansi',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'uuid' => $this->generateUUIDD(),
                'kode_bidang' => 'SDM',
                'nama_bidang' => 'Bidang Sumber Daya Manusia',
                'keterangan' => 'Mengelola kepegawaian dan pengembangan SDM',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'uuid' => $this->generateUUIDD(),
                'kode_bidang' => 'PEL',
                'nama_bidang' => 'Bidang Pelayanan Publik',
                'keterangan' => 'Mengelola layanan publik dan perizinan',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('bidang')->insertBatch($data);
    }

    private function insertSystemSettings()
    {
        $data = [
            [
                'key' => 'app_name',
                'value' => 'Aplikasi Perjadin',
                'description' => 'Nama Aplikasi',
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key' => 'app_version',
                'value' => '1.0.0',
                'description' => 'Versi Aplikasi',
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key' => 'default_password',
                'value' => 'password123',
                'description' => 'Password default untuk user baru',
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key' => 'max_login_attempts',
                'value' => '3',
                'description' => 'Maksimal percobaan login',
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key' => 'session_timeout',
                'value' => '60',
                'description' => 'Session timeout dalam menit',
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key' => 'lumsum_dalam_daerah',
                'value' => '150000',
                'description' => 'Biaya lumsum perjalanan dalam daerah per hari',
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key' => 'lumsum_luar_daerah_dalam_provinsi',
                'value' => '300000',
                'description' => 'Biaya lumsum luar daerah dalam provinsi per hari',
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key' => 'lumsum_luar_daerah_luar_provinsi',
                'value' => '500000',
                'description' => 'Biaya lumsum luar daerah luar provinsi per hari',
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key' => 'max_file_size_mb',
                'value' => '5',
                'description' => 'Maksimal ukuran file upload dalam MB',
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key' => 'allowed_file_types',
                'value' => 'jpg,jpeg,png,pdf',
                'description' => 'Tipe file yang diizinkan untuk upload',
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('system_settings')->insertBatch($data);
    }

    private function insertSuperAdmin()
    {
        $data = [
            'uuid' => $this->generateUUIDD(),
            'nip_nik' => '199001012020011001',
            'gelar_depan' => null,
            'nama' => 'Super Administrator',
            'gelar_belakang' => null,
            'jenis_pegawai' => 'ASN',
            'email' => 'admin@perjadin.com',
            'password' => password_hash('admin123', PASSWORD_BCRYPT),
            'jabatan' => 'Administrator Sistem',
            'bidang_id' => null,
            'role' => 'superadmin',
            'foto' => null,
            'is_active' => 1,
            'is_blocked' => 0,
            'blocked_reason' => null,
            'blocked_at' => null,
            'blocked_by' => null,
            'login_attempts' => 0,
            'last_login' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'deleted_at' => null,
        ];

        $this->db->table('users')->insert($data);

        echo "Super Admin created:\n";
        echo "NIP/NIK: 199001012020011001\n";
        echo "Email: admin@perjadin.com\n";
        echo "Password: admin123\n";
    }

    private function generateUUIDD()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
