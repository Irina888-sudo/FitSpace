# FitSpace
Gestion de salle de sport

# lancer l'app
php -S localhost:8000  

# creation de la migration
php spark make:migration CreateUsersTable
php spark make:migration CreateRessourcesTable
php spark make:migration CreateCreneauxTable
php spark make:migration CreateReservationsTable

# migration ...
php spark migrate

# creation du seeder 
php spark make:seeder MainSeeder 

# ajouter les donnees dans DB ...
php spark db:seed MainSeeder

# creation model
php spark make:model UserModel
php spark make:model RessourceModel
php spark make:model CrenauModel
php spark make:model ReservationModel
