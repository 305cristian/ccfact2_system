<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Routes
 *
 */
 
/**
 * @author CRISTIAN R. PAZ
 * @date 3 may 2026
 * @time 3:38:12 p.m.
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

$routes->group('compras', ['namespace' => '\Modules\Compras\Controllers'], function ($subroutes) {

    $subroutes->get('dashboard/(:num)', 'IndexController::index/$1');
    $subroutes->get('nuevaCompra', 'IndexController::nuevaCompra');
    $subroutes->post('insertProduct', 'IndexController::insertProduct');
    $subroutes->post('updateProduct', 'IndexController::updateProduct');

    $subroutes->get('deleteProduct/(:segment)', 'IndexController::deleteProduct/$1');
    $subroutes->get('changeBodega/(:num)', 'IndexController::changeBodega/$1');
    $subroutes->post('showDetailCart', 'IndexController::showDetailCart');
    $subroutes->post('cancelarCompra', 'IndexController::cancelarCompra');
    $subroutes->post('saveCompra', 'IndexController::saveCompra');
    $subroutes->post('updateCompra', 'IndexController::updateCompra');
    $subroutes->post('anularCompra', 'IndexController::anularCompra');
    $subroutes->post('updateValoresGlobales', 'IndexController::updateValoresGlobales');
//    $subroutes->get('clonarCompra/(:num)', 'IndexController::clonarCompra/$1');
//    $subroutes->post('importarExcel', 'IndexController::importarExcel');
    $subroutes->get('loadCompraEdit/(:num)', 'IndexController::loadCompraEdit/$1');
    $subroutes->get('indexEdit/(:num)', 'IndexController::indexEdit/$1');

    //GESTION DE AJUSTES
    $subroutes->get('gestionCompras', 'GestionController::index');
    $subroutes->post('searchCompras', 'GestionController::searchCompras');
    $subroutes->post('contadoresCompras', 'GestionController::contadoresCompras');
    $subroutes->get('getDataDetalle/(:num)', 'GestionController::getDataDetalle/$1');
    $subroutes->get('generarPDF/(:num)', 'GestionController::generarPDF/$1');
    $subroutes->post('sendEmailReport', 'GestionController::sendEmailReport');
});

$routes->group('comun', ['namespace' => '\Modules\Comun\Controllers'], function ($subroutes) {
    $subroutes->post('proveedores/searchProveedor', 'SearchsController::searchProveedor');
    $subroutes->post('productos/searchProductos', 'SearchsController::searchProductos');
    $subroutes->post('productos/searchProductosStock', 'SearchsController::searchProductosStock');
    $subroutes->get('productos/searchProductoCode/(:segment)', 'SearchsController::searchProductoCode/$1');
    $subroutes->post('exportar/generarExcel', 'IndexController::generarExcel');
    $subroutes->get('descargar/downloadPlantillaExcelEntrada', 'IndexController::downloadPlantillaExcelEntrada');
    $subroutes->get('descargar/downloadPlantillaExcelAjusteInicial', 'IndexController::downloadPlantillaExcelAjusteInicial');
});
