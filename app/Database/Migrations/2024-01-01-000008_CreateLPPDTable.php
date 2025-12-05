<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLPPDTable extends Migration
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
            'sppd_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'pegawai_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'hasil_kegiatan' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'hambatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'saran' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'dokumentasi' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'tanggal_pengisian' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'is_submitted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
            ],
            'submitted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('uuid');
        $this->forge->addUniqueKey('sppd_id');
        $this->forge->addKey('pegawai_id');

        $this->forge->createTable('lppd');

        // Add foreign keys
        $this->forge->addForeignKey('sppd_id', 'sppd', 'id', 'CASCADE', 'CASCADE', 'lppd_sppd_id_foreign');
        $this->forge->addForeignKey('pegawai_id', 'users', 'id', 'CASCADE', 'CASCADE', 'lppd_pegawai_id_foreign');
    }

    public function down()
    {
        $this->forge->dropForeignKey('lppd', 'lppd_sppd_id_foreign');
        $this->forge->dropForeignKey('lppd', 'lppd_pegawai_id_foreign');
        $this->forge->dropTable('lppd');
    }
}