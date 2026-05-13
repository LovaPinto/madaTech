<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

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
