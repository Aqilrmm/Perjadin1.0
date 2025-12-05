<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProgramsTable extends Migration
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
            'kode_program' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'nama_program' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'bidang_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'tahun_anggaran' => [
                'type' => 'YEAR',
                'null' => false,
            ],
            'jumlah_anggaran' => [
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
        $this->forge->addUniqueKey('kode_program');
        $this->forge->addKey('bidang_id');
        $this->forge->addKey('status');
        $this->forge->addKey('tahun_anggaran');

        $this->forge->createTable('programs');

        // Add foreign keys
        $this->forge->addForeignKey('bidang_id', 'bidang', 'id', 'CASCADE', 'CASCADE', 'programs_bidang_id_foreign');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE', 'programs_created_by_foreign');
        $this->forge->addForeignKey('approved_by', 'users', 'id', 'SET NULL', 'CASCADE', 'programs_approved_by_foreign');
    }

    public function down()
    {
        $this->forge->dropForeignKey('programs', 'programs_bidang_id_foreign');
        $this->forge->dropForeignKey('programs', 'programs_created_by_foreign');
        $this->forge->dropForeignKey('programs', 'programs_approved_by_foreign');
        $this->forge->dropTable('programs');
    }
}