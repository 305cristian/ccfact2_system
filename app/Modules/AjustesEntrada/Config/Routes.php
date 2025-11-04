<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Rotes
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 8 oct 2025
 * @time 4:54:34 p.m.
 */
//TODO:TODA LA CONFIGURACION INICIAL PÁRA QUE ARRANQUE EL SISTEMA ESTA EN EL ARCHIVO Routes.php en la carpeta Config del sistema
//$routes->setDefaultNamespace('\Modules\Login\Controllers'); ruta inicial
//$routes->setDefaultController('IndexController'); metodo inicial
//TODO: EN GENERAL el primer parametro es es alias que se usara para llamar a la direccion
//TODO: El segundo parametros es la ruta del controlador, luego de los 2 puntos ::viene el metodo al cual estoy invocando
//TODO: como recomendacion SE RECOMIENDA QUE EL ALIAS LLEVE COMO DIRECCION TAMBIEN EL NOMBRE DEL CONTROLADOR SIN CONTROLLER EJM: /welcome/closeSession


if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('ajustesentrada', ['namespace' => '\Modules\AjustesEntrada\Controllers'], function ($subroutes) {

    $subroutes->get('nuevoAjuste', 'IndexController::index');
    $subroutes->post('insertProduct', 'IndexController::insertProduct');
    $subroutes->post('updateProduct', 'IndexController::updateProduct');
    $subroutes->get('deleteProduct/(:segment)', 'IndexController::deleteProduct/$1');
    $subroutes->get('changeBodega/(:num)', 'IndexController::changeBodega/$1');
    $subroutes->post('showDetailCart', 'IndexController::showDetailCart');
    $subroutes->post('cancelarAjuste', 'IndexController::cancelarAjuste');
    $subroutes->post('saveAjuste', 'IndexController::saveAjuste');
    $subroutes->post('updateAjuste', 'IndexController::updateAjuste');
    $subroutes->get('loadAjusteEdit/(:num)', 'IndexController::loadAjusteEdit/$1');
    $subroutes->get('indexEdit/(:num)', 'IndexController::indexEdit/$1');

    //GESTION DE AJUSTES
    $subroutes->get('gestionAjustes', 'GestionController::index');
    $subroutes->post('getAjustes', 'GestionController::getAjustes');
    $subroutes->get('getDataDetalle/(:num)', 'GestionController::getDataDetalle/$1');
    $subroutes->get('generarPDF/(:num)', 'GestionController::generarPDF/$1');

    //AJUSTE INICIAL
    $subroutes->get('ajusteInicial', 'AjusteInicialController::index');
});

$routes->group('comun', ['namespace' => '\Modules\Comun\Controllers'], function ($subroutes) {
    $subroutes->post('proveedores/searchProveedor', 'SearchsController::searchProveedor');
    $subroutes->post('productos/searchProductos', 'SearchsController::searchProductos');
    $subroutes->post('productos/searchProductosStock', 'SearchsController::searchProductosStock');
    $subroutes->get('productos/searchProductoCode/(:segment)', 'SearchsController::searchProductoCode/$1');
    $subroutes->post('exportar/generarExcel', 'IndexController::generarExcel');
});

