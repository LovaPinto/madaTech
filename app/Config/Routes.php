<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'AuthController::login', ['as' => 'login']);
$routes->post('login', 'AuthController::attempt', ['as' => 'login.attempt']);
$routes->get('logout', 'AuthController::logout', ['as' => 'logout']);

$routes->get('dashboard', 'PageController::dashboardEmploye', ['as' => 'dashboard']);
$routes->get('dashboard/admin', 'AdminController::dashboard', ['as' => 'dashboard.admin']);
$routes->get('dashboard/rh', 'PageController::dashboardRh', ['as' => 'dashboard.rh']);

$routes->get('liste-demande', 'PageController::listeDemandeEmploye', ['as' => 'liste.demande']);
$routes->get('formulaire-demande', 'PageController::formulaireDemandeEmploye', ['as' => 'formulaire.demande']);
$routes->post('formulaire-demande', 'PageController::submitDemandeEmploye', ['as' => 'formulaire.demande.submit']);
$routes->get('profil', 'PageController::profilEmploye', ['as' => 'profil.employe']);
$routes->post('profil/mot-de-passe', 'PageController::updatePassword', ['as' => 'profil.password']);

$routes->post('rh/demande/(:num)/approve', 'PageController::approveDemandeRh/$1', ['as' => 'rh.demande.approve']);
$routes->post('rh/demande/(:num)/refuse', 'PageController::refuseDemandeRh/$1', ['as' => 'rh.demande.refuse']);

$routes->get('admin', 'AdminController::dashboard', ['as' => 'admin.dashboard']);
$routes->get('admin/employes', 'AdminController::employesIndex', ['as' => 'admin.employes']);
$routes->post('admin/employes', 'AdminController::employeStore', ['as' => 'admin.employes.store']);
$routes->post('admin/employes/(:num)/toggle', 'AdminController::employeToggle/$1', ['as' => 'admin.employes.toggle']);

$routes->get('admin/departements', 'AdminController::departementsIndex', ['as' => 'admin.departements']);
$routes->post('admin/departements', 'AdminController::departementStore', ['as' => 'admin.departements.store']);
$routes->post('admin/departements/(:num)/delete', 'AdminController::departementDelete/$1', ['as' => 'admin.departements.delete']);

$routes->get('admin/types-conge', 'AdminController::typesIndex', ['as' => 'admin.types']);
$routes->post('admin/types-conge', 'AdminController::typeStore', ['as' => 'admin.types.store']);
$routes->post('admin/types-conge/(:num)/delete', 'AdminController::typeDelete/$1', ['as' => 'admin.types.delete']);

$routes->get('admin/soldes', 'AdminController::soldesIndex', ['as' => 'admin.soldes']);
$routes->post('admin/soldes', 'AdminController::soldeStore', ['as' => 'admin.soldes.store']);

$routes->get('admin/historique', 'AdminController::historiqueIndex', ['as' => 'admin.historique']);


// Pages personnalisées
$routes->get('/dashboard', 'Home::dashboard');
$routes->get('/dashboard-admin', 'Home::dashboardAdmin');
$routes->get('/gestion-employe', 'Home::gestionEmploye');
$routes->get('/liste-rh', 'Home::listeRh');
$routes->get('/login', 'Home::login');
$routes->get('/etudiant', 'Home::etudiantView');
$routes->get('/template-conges-rh', 'Home::templateCongesRh');

