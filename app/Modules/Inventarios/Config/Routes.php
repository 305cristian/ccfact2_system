<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Routes
 * @author Cristian R. Paz
 * @Date 27 sep. 2023
 * @Time 17:30:13
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

$routes->get('inventarios/(:num)', '\Modules\Inventarios\Controllers\indexController::index/$1');

$routes->group('inventarios', ['namespace' => '\Modules\Inventarios\Controllers'], function ($subroutes) {

    $subroutes->get('existencias', 'IndexController::viewExistencias');

    $subroutes->get('viewInventarioGeneral', 'IndexController::viewInventarioGeneral');
    $subroutes->get('viewInventarioLotes', 'IndexController::viewInventarioLotes');
    $subroutes->get('viewInventarioConsolidado', 'IndexController::viewInventarioConsolidado');
});

$routes->group('inventarios', ['namespace' => '\Modules\Inventarios\Controllers'], function ($subroutes) {

    //INVENTARIO GENERAL
    $subroutes->post('general', 'ExistenciasController::getInventarioGeneral');
    $subroutes->post('exportExcelGeneral', 'ExcelExportController::exportInventarioGeneralExcel');
    $subroutes->post('exportPdfGeneral', 'PdfExportController::exportInventarioGeneralPdf');
    $subroutes->get('viewStockBodega/(:num)', 'ExistenciasController::viewStockBodega/$1');
    $subroutes->post('viewReserva', 'ExistenciasController::viewReserva');

    //INVENTARIO POR LOTES
    $subroutes->post('lotes', 'ExistenciasController::getInventarioLotes');
    $subroutes->post('exportExcelLotes', 'ExcelExportController::exportInventarioLotesExcel');
    $subroutes->post('exportPdfLotes', 'PdfExportController::exportInventarioLotesPdf');
    $subroutes->get('viewStockBodegaLote/(:num)/(:num)', 'ExistenciasController::viewStockBodegaLote/$1/$2');
    $subroutes->post('viewReservaLote', 'ExistenciasController::viewReservaLote');

    //INVENTARIO CONSOLIDADO
    $subroutes->post('consolidado', 'ExistenciasController::getInventarioConsolidado');
    $subroutes->post('exportExcelConsolidado', 'ExcelExportController::exportInventarioConsolidadoExcel');
    $subroutes->post('exportPdfConsolidado', 'PdfExportController::exportInventarioConsolidadoPdf');
    $subroutes->get('viewStockBodegaLote/(:num)/(:num)', 'ExistenciasController::viewStockBodegaLote/$1/$2');
    $subroutes->post('viewReservaLote', 'ExistenciasController::viewReservaLote');
});

//INVENTARIO HISTORICO
$routes->get('inv/historico', '\Modules\Inventarios\Controllers\HistoricoController::index');
$routes->post('inv/getInventarioHistorico', '\Modules\Inventarios\Controllers\HistoricoController::getInventarioHistorico');
$routes->post('inv/exportPdfHistorico', '\Modules\Inventarios\Controllers\PdfExportController::exportPdfHistorico');
$routes->post('inv/exportExcelHistorico', '\Modules\Inventarios\Controllers\ExcelExportController::exportExcelHistorico');

//CONTROL DE CADUCIDAD
$routes->get('control/caducidad', '\Modules\Inventarios\Controllers\CaducidadController::index');
$routes->post('control/consultarProductos', '\Modules\Inventarios\Controllers\CaducidadController::consultarProductos');
$routes->post('control/exportPdfCaducidad', '\Modules\Inventarios\Controllers\PdfExportController::exportPdfCaducidad');
$routes->post('control/exportExcelCaducidad', '\Modules\Inventarios\Controllers\ExcelExportController::exportExcelCaducidad');


//CONTROL KARDEX
$routes->group('kardex', ['namespace' => '\Modules\Inventarios\Controllers'], function ($subroutes) {

    $subroutes->get('kardex', 'KardexController::viewKardex');

    $subroutes->get('producto', 'KardexController::viewKardexProducto');
    $subroutes->get('general', 'KardexController::viewKardexGeneral');
    $subroutes->get('lotes', 'KardexController::viewKardexlotes');
});

$routes->group('comun', ['namespace' => '\Modules\Comun\Controllers'], function ($subroutes) {
    $subroutes->post('productos/searchProductosFull', 'SearchsController::searchProductosFull');
    $subroutes->get('subgrupos/getSubgrupoByGrupo/(:num)', 'IndexController::getSubgrupoByGrupo/$1');
});

