<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReservationsTable extends Migration
{
public function up()
{
    $this->forge->addField([
        'id'         => ['type' => 'INT', 'auto_increment' => true],
        'user_id'    => ['type' => 'INT'],
        'creneau_id' => ['type' => 'INT'],
        'statut'     => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'en_attente'],
        'created_at' => ['type' => 'DATETIME', 'null' => true],
    ]);
    $this->forge->addPrimaryKey('id');
    $this->forge->createTable('reservations');
}
public function down()
{
    $this->forge->dropTable('reservations');
}
}
