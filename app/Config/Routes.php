<?php
use CodeIgniter\Router\RouteCollection;
/** @var RouteCollection $routes */

// Routes publiques
$routes->get('/',          'Auth::index');
$routes->get('/login',     'Auth::login');
$routes->post('/login',    'Auth::loginPost');
$routes->get('/register',  'Auth::register');
$routes->post('/register', 'Auth::registerPost');
$routes->get('/logout',    'Auth::logout');

// Routes client (protégées)
$routes->group('client', ['filter' => 'auth'], function($routes) {
    $routes->get('creneaux',              'Client\Creneaux::index');
    $routes->post('reserver',             'Client\Creneaux::reserver');
    $routes->get('reservations',          'Client\Reservations::index');
    $routes->post('annuler/(:num)',       'Client\Reservations::annuler/$1');
});

// Routes admin (protégées)
$routes->group('admin', ['filter' => 'auth:admin'], function($routes) {
    $routes->get('/',                           'Admin\Creneaux::index');
    $routes->get('creneaux',                    'Admin\Creneaux::index');
    $routes->get('creneaux/create',             'Admin\Creneaux::create');
    $routes->post('creneaux/create',            'Admin\Creneaux::store');
    $routes->get('creneaux/edit/(:num)',        'Admin\Creneaux::edit/$1');
    $routes->post('creneaux/edit/(:num)',       'Admin\Creneaux::update/$1');
    $routes->post('creneaux/delete/(:num)',     'Admin\Creneaux::delete/$1');
    $routes->get('reservations',                'Admin\Reservations::index');
    $routes->post('reservations/statut/(:num)', 'Admin\Reservations::statut/$1');
    $routes->get('ressources',                  'Admin\Ressources::index');
    $routes->post('ressources/create',          'Admin\Ressources::store');
});