<?php
namespace Config;
use CodeIgniter\Router\RouteCollection;


// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('\Modules\Login\Controllers');
$routes->setDefaultController('IndexController');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();


/**
 * @var RouteCollection $routes
 */
//$routes->get('/', 'Home::index');


$routes->get('/', 'IndexController::index');

/**
  Include Modules routes file HMVC
 */
foreach (glob(APPPATH . 'Modules/*', GLOB_ONLYDIR) as $item_dir) {
    if (file_exists($item_dir . '/Config/Routes.php')) {
        require_once($item_dir . '/Config/Routes.php');
    }
}

