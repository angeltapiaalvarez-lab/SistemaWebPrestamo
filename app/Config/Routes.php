<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->post('/login', 'LoginController::validar');
$routes->get('/admin', 'AdminController::index');
$routes->get('/dashboard', 'AdminController::dashboard');
$routes->put('/admin/(:num)', 'AdminController::update/$1');

$routes->get('/usuarios/list', 'UsuariosController::listar');
$routes->get('/usuarios/logout', 'UsuariosController::logout');
$routes->resource('usuarios', ['controller' => 'UsuariosController']);

$routes->get('/clientes/list', 'ClientesController::listar');
$routes->put('/clientes/(:num)/estado', 'ClientesController::estado/$1');
$routes->resource('clientes', ['controller' => 'ClientesController']);

$routes->get('/prestamos', 'PrestamosController::index');
$routes->get('/prestamos/historial', 'PrestamosController::historial');
$routes->get('/prestamos/listHistorial', 'PrestamosController::listHistorial');
$routes->get('/prestamos/buscarCliente', 'PrestamosController::buscarCliente');
$routes->get('/prestamos/(:num)/detail', 'PrestamosController::detail/$1');
$routes->get('/prestamos/(:num)/reporte', 'PrestamosController::reporte/$1');
$routes->post('/prestamos', 'PrestamosController::create');
$routes->post('/prestamos/enviarCorreo', 'PrestamosController::enviarCorreo');
$routes->put('/prestamos/(:num)', 'PrestamosController::update/$1');
$routes->delete('/prestamos/(:num)', 'PrestamosController::delete/$1');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
