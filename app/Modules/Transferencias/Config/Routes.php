<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Routes
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 4 dic 2025
 * @time 11:34:17 p.m.
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


$routes->group('transferencias', ['namespace' => '\Modules\Transferencias\Controllers'], function ($subroutes) {

    $subroutes->get('nuevaTransferencia', 'IndexController::index');
    $subroutes->post('insertProduct', 'IndexController::insertProduct');
    $subroutes->post('updateProduct', 'IndexController::updateProduct');
    $subroutes->get('deleteProduct/(:segment)', 'IndexController::deleteProduct/$1');
    $subroutes->get('changeBodega/(:num)', 'IndexController::changeBodega/$1');
    $subroutes->get('loadUsersConfirm/(:num)', 'IndexController::loadUsersConfirm/$1');
    $subroutes->post('showDetailCart', 'IndexController::showDetailCart');
    $subroutes->post('cancelarTransferencia', 'IndexController::cancelarTransferencia');
    $subroutes->post('saveTransferencia', 'IndexController::saveTransferencia');
    $subroutes->post('updateTransferencia', 'IndexController::updateTransferencia');
    $subroutes->post('anularTransferencia', 'IndexController::anularTransferencia');
    $subroutes->get('clonarTransferencia/(:num)', 'IndexController::clonarTransferencia/$1');
    $subroutes->get('confirmarTransferencia/(:num)', 'IndexController::confirmarTransferencia/$1');
    $subroutes->post('rechazarTransferencia', 'IndexController::rechazarTransferencia');
    $subroutes->post('importarExcel', 'IndexController::importarExcel');
    $subroutes->get('loadTransferenciaEdit/(:num)', 'IndexController::loadTransferenciaEdit/$1');
    $subroutes->get('indexEdit/(:num)', 'IndexController::indexEdit/$1');
//
//    //GESTION DE AJUSTES
    $subroutes->get('gestionTransferencias', 'GestionController::index');
    $subroutes->post('searchTransferencias', 'GestionController::searchTransferencias');
    $subroutes->post('contadoresTransferencias', 'GestionController::contadoresTransferencias');
    $subroutes->get('getDataDetalle/(:num)', 'GestionController::getDataDetalle/$1');
    $subroutes->get('generarPDF/(:num)', 'GestionController::generarPDF/$1');
    $subroutes->post('sendEmailReport', 'GestionController::sendEmailReport');

   
});

$routes->group('comun', ['namespace' => '\Modules\Comun\Controllers'], function ($subroutes) {
//    $subroutes->post('proveedores/searchProveedor', 'SearchsController::searchProveedor');
//    $subroutes->post('productos/searchProductos', 'SearchsController::searchProductos');
    $subroutes->post('productos/searchProductosStock', 'SearchsController::searchProductosStock');
//    $subroutes->get('productos/searchProductoCode/(:segment)', 'SearchsController::searchProductoCode/$1');
//    $subroutes->post('exportar/generarExcel', 'IndexController::generarExcel');
    $subroutes->get('descargar/downloadPlantillaExcelTransferencias', 'IndexController::downloadPlantillaExcelTransferencias');
});