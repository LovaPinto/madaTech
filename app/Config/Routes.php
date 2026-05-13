<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'AuthController::login', ['as' => 'login']);
$routes->post('login', 'AuthController::attempt', ['as' => 'login.attempt']);
$routes->get('logout', 'AuthController::logout', ['as' => 'logout']);

$routes->get('dashboard', 'PageController::dashboardEmploye', ['as' => 'dashboard']);
$routes->get('dashboard/admin', 'PageController::dashboardAdmin', ['as' => 'dashboard.admin']);
$routes->get('dashboard/rh', 'PageController::dashboardRh', ['as' => 'dashboard.rh']);

// Page d’accueil
$routes->get('/', 'Home::index');

// Pages personnalisées
$routes->get('/dashboard', 'Home::dashboard');
$routes->get('/dashboard-admin', 'Home::dashboardAdmin');
$routes->get('/formulaire-demande', 'Home::formulaireDemande');
$routes->get('/gestion-employe', 'Home::gestionEmploye');
$routes->get('/liste-demande', 'Home::listeDemande');
$routes->get('/liste-rh', 'Home::listeRh');
$routes->get('/login', 'Home::login');
$routes->get('/etudiant', 'Home::etudiantView');
$routes->get('/template-conges-rh', 'Home::templateCongesRh');

// Exemple avec un autre contrôleur
$routes->get('/etudiant', 'EtudiantController::index');
