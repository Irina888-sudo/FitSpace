<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
public function run()
    {
        // USERS
        $this->db->table('users')->insertBatch([
            ['nom' => 'Admin', 'email' => 'admin@test.com', 'password' => password_hash('admin123', PASSWORD_DEFAULT), 'role' => 'admin',  'created_at' => date('Y-m-d H:i:s')],
            ['nom' => 'Alice', 'email' => 'alice@test.com', 'password' => password_hash('alice123', PASSWORD_DEFAULT), 'role' => 'client', 'created_at' => date('Y-m-d H:i:s')],
            ['nom' => 'Bob',   'email' => 'bob@test.com',   'password' => password_hash('bob123',   PASSWORD_DEFAULT), 'role' => 'client', 'created_at' => date('Y-m-d H:i:s')],
        ]);

        // RESSOURCES
        $this->db->table('ressources')->insertBatch([
            ['nom' => 'Salle A',   'type' => 'salle',   'capacite' => 20, 'description' => 'Grande salle polyvalente'],
            ['nom' => 'Terrain B', 'type' => 'terrain', 'capacite' => 10, 'description' => 'Terrain extérieur'],
        ]);

        // CRENEAUX
        $this->db->table('creneaux')->insertBatch([
            ['ressource_id' => 1, 'date_debut' => '2025-06-01 09:00:00', 'date_fin' => '2025-06-01 10:00:00', 'places_dispo' => 10, 'actif' => 1],
            ['ressource_id' => 1, 'date_debut' => '2025-06-01 11:00:00', 'date_fin' => '2025-06-01 12:00:00', 'places_dispo' => 8,  'actif' => 1],
            ['ressource_id' => 2, 'date_debut' => '2025-06-02 14:00:00', 'date_fin' => '2025-06-02 16:00:00', 'places_dispo' => 5,  'actif' => 1],
        ]);
    }
}
