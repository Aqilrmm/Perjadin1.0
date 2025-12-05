<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKwitansiTable extends Migration
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
            'biaya_perjalanan' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
            ],
            'bukti_perjalanan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'keterangan_perjalanan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'biaya_lumsum' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
            ],
            'keterangan_lumsum' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'biaya_penginapan' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
            ],
            'bukti_penginapan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'keterangan_penginapan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'biaya_taxi' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
            ],
            'bukti_taxi' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'keterangan_taxi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'biaya_tiket' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
            ],
            'bukti_tiket' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'keterangan_tiket' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'total_biaya' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
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

        $this->forge->createTable('kwitansi');

        // Add foreign keys
        $this->forge->addForeignKey('sppd_id', 'sppd', 'id', 'CASCADE', 'CASCADE', 'kwitansi_sppd_id_foreign');
        $this->forge->addForeignKey('pegawai_id', 'users', 'id', 'CASCADE', 'CASCADE', 'kwitansi_pegawai_id_foreign');
    }

    public function down()
    {
        $this->forge->dropForeignKey('kwitansi', 'kwitansi_sppd_id_foreign');
        $this->forge->dropForeignKey('kwitansi', 'kwitansi_pegawai_id_foreign');
        $this->forge->dropTable('kwitansi');
    }
}