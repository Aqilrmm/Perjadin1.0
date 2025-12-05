<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
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
            'nip_nik' => [
                'type' => 'VARCHAR',
                'constraint' => 18,
                'null' => false,
            ],
            'gelar_depan' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'gelar_belakang' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'jenis_pegawai' => [
                'type' => 'ENUM',
                'constraint' => ['ASN', 'Non-ASN'],
                'default' => 'ASN',
                'null' => false,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'jabatan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'bidang_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['superadmin', 'kepaladinas', 'kepalabidang', 'pegawai', 'keuangan'],
                'default' => 'pegawai',
                'null' => false,
            ],
            'foto' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'null' => false,
            ],
            'is_blocked' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
            ],
            'blocked_reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'blocked_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'blocked_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'login_attempts' => [
                'type' => 'TINYINT',
                'constraint' => 3,
                'unsigned' => true,
                'default' => 0,
                'null' => false,
            ],
            'last_login' => [
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('uuid');
        $this->forge->addUniqueKey('nip_nik');
        $this->forge->addUniqueKey('email');
        $this->forge->addKey('bidang_id');
        $this->forge->addKey('role');

        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}