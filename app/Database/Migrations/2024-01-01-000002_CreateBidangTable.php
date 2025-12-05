<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBidangTable extends Migration
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
            'kode_bidang' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => false,
            ],
            'nama_bidang' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
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
        $this->forge->addUniqueKey('kode_bidang');
        $this->forge->addUniqueKey('nama_bidang');

        $this->forge->createTable('bidang');
    }

    public function down()
    {
        $this->forge->dropTable('bidang');
    }
}