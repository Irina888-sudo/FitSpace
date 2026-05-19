<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCreneauxTable extends Migration
{
public function up()
{
    $this->forge->addField([
        'id'           => ['type' => 'INT', 'auto_increment' => true],
        'ressource_id' => ['type' => 'INT'],
        'date_debut'   => ['type' => 'DATETIME'],
        'date_fin'     => ['type' => 'DATETIME'],
        'places_dispo' => ['type' => 'INT'],
        'actif'        => ['type' => 'INT', 'constraint' => 1, 'default' => 1],
    ]);
    $this->forge->addPrimaryKey('id');
    $this->forge->createTable('creneaux');
}
public function down()
{
    $this->forge->dropTable('creneaux');
}
}
