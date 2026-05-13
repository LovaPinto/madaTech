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
