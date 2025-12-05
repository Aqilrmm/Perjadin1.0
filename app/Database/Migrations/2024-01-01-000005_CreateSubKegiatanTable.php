<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubKegiatanTable extends Migration
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
            'kegiatan_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'kode_sub_kegiatan' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'nama_sub_kegiatan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'anggaran_sub_kegiatan' => [
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
        $this->forge->addUniqueKey('kode_sub_kegiatan');
        $this->forge->addKey('kegiatan_id');
        $this->forge->addKey('status');

        $this->forge->createTable('sub_kegiatan');

        // Add foreign keys
        $this->forge->addForeignKey('kegiatan_id', 'kegiatan', 'id', 'CASCADE', 'CASCADE', 'sub_kegiatan_kegiatan_id_foreign');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE', 'sub_kegiatan_created_by_foreign');
        $this->forge->addForeignKey('approved_by', 'users', 'id', 'SET NULL', 'CASCADE', 'sub_kegiatan_approved_by_foreign');
    }

    public function down()
    {
        $this->forge->dropForeignKey('sub_kegiatan', 'sub_kegiatan_kegiatan_id_foreign');
        $this->forge->dropForeignKey('sub_kegiatan', 'sub_kegiatan_created_by_foreign');
        $this->forge->dropForeignKey('sub_kegiatan', 'sub_kegiatan_approved_by_foreign');
        $this->forge->dropTable('sub_kegiatan');
    }
}