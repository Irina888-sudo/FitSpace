<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
        public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'auto_increment' => true],
            'nom'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 150],
            'password'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'       => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'client'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('users');
    }
        public function down()
    {
        $this->forge->dropTable('users');
    }
}
