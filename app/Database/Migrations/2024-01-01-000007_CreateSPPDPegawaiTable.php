<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSPPDPegawaiTable extends Migration
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
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('sppd_id');
        $this->forge->addKey('pegawai_id');
        $this->forge->addUniqueKey(['sppd_id', 'pegawai_id']);

        $this->forge->createTable('sppd_pegawai');

        // Add foreign keys
        $this->forge->addForeignKey('sppd_id', 'sppd', 'id', 'CASCADE', 'CASCADE', 'sppd_pegawai_sppd_id_foreign');
        $this->forge->addForeignKey('pegawai_id', 'users', 'id', 'CASCADE', 'CASCADE', 'sppd_pegawai_pegawai_id_foreign');
    }

    public function down()
    {
        $this->forge->dropForeignKey('sppd_pegawai', 'sppd_pegawai_sppd_id_foreign');
        $this->forge->dropForeignKey('sppd_pegawai', 'sppd_pegawai_pegawai_id_foreign');
        $this->forge->dropTable('sppd_pegawai');
    }
}