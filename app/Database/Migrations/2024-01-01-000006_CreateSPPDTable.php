<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSPPDTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'uuid' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'no_sppd' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'sub_kegiatan_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'bidang_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'tipe_perjalanan' => [
                'type' => 'ENUM',
                'constraint' => ['Dalam Daerah', 'Luar Daerah Dalam Provinsi', 'Luar Daerah Luar Provinsi'],
                'null' => false,
            ],
            'maksud_perjalanan' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'dasar_surat' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'file_surat_tugas' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'alat_angkut' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'tempat_berangkat' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'tempat_tujuan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'tanggal_berangkat' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'tanggal_kembali' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'lama_perjalanan' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'penanggung_jawab' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'estimasi_biaya' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
            ],
            'realisasi_biaya' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'pending', 'approved', 'rejected', 'submitted', 'need_revision', 'verified', 'closed'],
                'default' => 'draft',
                'null' => false,
            ],
            'catatan_kepala_dinas' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'catatan_keuangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'approved_by_kepaladinas' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'approved_at_kepaladinas' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'verified_by_keuangan' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'verified_at_keuangan' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'submitted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('uuid');
        $this->forge->addUniqueKey('no_sppd');
        $this->forge->addKey('sub_kegiatan_id');
        $this->forge->addKey('bidang_id');
        $this->forge->addKey('status');
        $this->forge->addKey('tipe_perjalanan');
        $this->forge->addKey('tanggal_berangkat');
        $this->forge->addKey(['bidang_id', 'status', 'tanggal_berangkat']);

        $this->forge->createTable('sppd');

        // Add foreign keys
        $this->forge->addForeignKey('sub_kegiatan_id', 'sub_kegiatan', 'id', 'CASCADE', 'CASCADE', 'sppd_sub_kegiatan_id_foreign');
        $this->forge->addForeignKey('bidang_id', 'bidang', 'id', 'CASCADE', 'CASCADE', 'sppd_bidang_id_foreign');
        $this->forge->addForeignKey('penanggung_jawab', 'users', 'id', 'CASCADE', 'CASCADE', 'sppd_penanggung_jawab_foreign');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE', 'sppd_created_by_foreign');
        $this->forge->addForeignKey('approved_by_kepaladinas', 'users', 'id', 'SET NULL', 'CASCADE', 'sppd_approved_by_kepaladinas_foreign');
        $this->forge->addForeignKey('verified_by_keuangan', 'users', 'id', 'SET NULL', 'CASCADE', 'sppd_verified_by_keuangan_foreign');
    }

    public function down()
    {
        $this->forge->dropForeignKey('sppd', 'sppd_sub_kegiatan_id_foreign');
        $this->forge->dropForeignKey('sppd', 'sppd_bidang_id_foreign');
        $this->forge->dropForeignKey('sppd', 'sppd_penanggung_jawab_foreign');
        $this->forge->dropForeignKey('sppd', 'sppd_created_by_foreign');
        $this->forge->dropForeignKey('sppd', 'sppd_approved_by_kepaladinas_foreign');
        $this->forge->dropForeignKey('sppd', 'sppd_verified_by_keuangan_foreign');
        $this->forge->dropTable('sppd');
    }
}