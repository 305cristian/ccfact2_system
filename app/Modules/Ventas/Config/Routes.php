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
 * @date 9 ago 2026
 * @time 8:25:05 a.m.
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

$routes->group('ventas', ['namespace' => '\Modules\Ventas\Controllers'], function ($subroutes) {

    $subroutes->get('dashboard', 'DashboardController::index');

    $subroutes->post('getDataDashboard', 'DashboardController::getDataDashboard');
    
    // NUEVA VENTA
    $subroutes->get('nuevaVenta', 'IndexController::nuevaVenta');
    $subroutes->post('insertProduct', 'IndexController::insertProduct');
    $subroutes->post('updateProduct', 'IndexController::updateProduct');

    $subroutes->get('deleteProduct/(:segment)', 'IndexController::deleteProduct/$1');
    $subroutes->get('changeBodega/(:num)', 'IndexController::changeBodega/$1');
    $subroutes->post('showDetailCart', 'IndexController::showDetailCart');
    $subroutes->post('cancelarVenta', 'IndexController::cancelarVenta');
    $subroutes->post('saveVenta', 'IndexController::saveVenta');
    $subroutes->post('updateVenta', 'IndexController::updateVenta');
    $subroutes->post('anularVenta', 'IndexController::anularVenta');
    $subroutes->get('clonarVenta/(:num)', 'IndexController::clonarVenta/$1');
    $subroutes->post('importarExcel', 'IndexController::importarExcel');
    $subroutes->get('loadVentaEdit/(:num)', 'IndexController::loadVentaEdit/$1');
    $subroutes->get('indexEdit/(:num)', 'IndexController::indexEdit/$1');

// GESTIÓN DE VENTAS
    $subroutes->get('gestionVentas', 'GestionController::index');
    $subroutes->post('searchVentas', 'GestionController::searchVentas');
    $subroutes->post('contadoresVentas', 'GestionController::contadoresVentas');
    $subroutes->post('updateEdicionRapida', 'GestionController::updateEdicionRapida');
    $subroutes->get('getCentrosCostosVenta/(:num)', 'GestionController::getCentrosCostosVenta/$1');
    $subroutes->post('updateCentrosCostosVenta', 'GestionController::updateCentrosCostosVenta');
    $subroutes->get('getLotesVenta/(:num)', 'GestionController::getLotesVenta/$1');
    $subroutes->post('updateLotesVenta', 'GestionController::updateLotesVenta');
    $subroutes->get('getDataDetalle/(:num)', 'GestionController::getDataDetalle/$1');
    $subroutes->get('getAsientoContable/(:num)', 'GestionController::getAsientoContable/$1');
    $subroutes->get('generarPDF/(:num)', 'GestionController::generarPDF/$1');
    $subroutes->post('sendEmailReport', 'GestionController::sendEmailReport');
});

$routes->group('comun', ['namespace' => '\Modules\Comun\Controllers'], function ($subroutes) {
    $subroutes->post('clientes/searchCliente', 'SearchsController::searchCliente');
    $subroutes->post('productos/searchProductos', 'SearchsController::searchProductos');
    $subroutes->post('productos/searchProductosStock', 'SearchsController::searchProductosStock');
    $subroutes->get('productos/searchProductoCode/(:segment)', 'SearchsController::searchProductoCode/$1');
    $subroutes->post('exportar/generarExcel', 'IndexController::generarExcel');
    $subroutes->get('descargar/downloadPlantillaExcelCompra', 'IndexController::downloadPlantillaExcelCompra');
    $subroutes->get('descargar/downloadPlantillaExcelEntrada', 'IndexController::downloadPlantillaExcelEntrada');
    $subroutes->get('descargar/downloadPlantillaExcelAjusteInicial', 'IndexController::downloadPlantillaExcelAjusteInicial');
});
