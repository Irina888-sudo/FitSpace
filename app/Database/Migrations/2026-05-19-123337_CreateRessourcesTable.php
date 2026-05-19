<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRessourcesTable extends Migration
{
public function up()
{
    $this->forge->addField([
        'id'          => ['type' => 'INT', 'auto_increment' => true],
        'nom'         => ['type' => 'VARCHAR', 'constraint' => 100],
        'type'        => ['type' => 'VARCHAR', 'constraint' => 100],
        'capacite'    => ['type' => 'INT'],
        'description' => ['type' => 'TEXT', 'null' => true],
    ]);
    $this->forge->addPrimaryKey('id');
    $this->forge->createTable('ressources');
}
public function down()
{
    $this->forge->dropTable('ressources');
}
}
