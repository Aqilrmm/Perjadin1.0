<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKegiatanTable extends Migration
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
            'program_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'kode_kegiatan' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'nama_kegiatan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'anggaran_kegiatan' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'pending', 'approved', 'rejected'],
                'default' => 'draft',
                'null' => false,
            ],
            'catatan_kepala_dinas' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'approved_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'approved_at' => [
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
        $this->forge->addUniqueKey('kode_kegiatan');
        $this->forge->addKey('program_id');
        $this->forge->addKey('status');

        $this->forge->createTable('kegiatan');

        // Add foreign keys
        $this->forge->addForeignKey('program_id', 'programs', 'id', 'CASCADE', 'CASCADE', 'kegiatan_program_id_foreign');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE', 'kegiatan_created_by_foreign');
        $this->forge->addForeignKey('approved_by', 'users', 'id', 'SET NULL', 'CASCADE', 'kegiatan_approved_by_foreign');
    }

    public function down()
    {
        $this->forge->dropForeignKey('kegiatan', 'kegiatan_program_id_foreign');
        $this->forge->dropForeignKey('kegiatan', 'kegiatan_created_by_foreign');
        $this->forge->dropForeignKey('kegiatan', 'kegiatan_approved_by_foreign');
        $this->forge->dropTable('kegiatan');
    }
}