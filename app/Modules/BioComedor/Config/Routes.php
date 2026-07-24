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
 * @date 23 jul 2026
 * @time 7:18:54 p.m.
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

$routes->group('biocomedor', ['namespace' => '\Modules\BioComedor\Controllers'], function ($subroutes) {

    $subroutes->get('dashboard', 'DashboardController::index');
    $subroutes->get('reportes', 'DashboardController::reportes');
    $subroutes->post('reportes/getReportes', 'DashboardController::getReportes');
    $subroutes->get('terminal', 'TerminalController::index');
    $subroutes->get('terminal/getServicioActual', 'TerminalController::getServicioActual');
    $subroutes->post('terminal/registrarMarcacion', 'TerminalController::registrarMarcacion');
    $subroutes->get('comedores', 'ComedoresController::index');
    $subroutes->get('comedores/getComedores', 'ComedoresController::getComedores');
    $subroutes->post('comedores/saveComedor', 'ComedoresController::saveComedor');
    $subroutes->post('comedores/updateComedor', 'ComedoresController::updateComedor');
    $subroutes->get('equipos', 'EquiposController::index');
    $subroutes->get('equipos/getEquipos', 'EquiposController::getEquipos');
    $subroutes->post('equipos/saveEquipo', 'EquiposController::saveEquipo');
    $subroutes->post('equipos/updateEquipo', 'EquiposController::updateEquipo');
    $subroutes->get('contratistas', 'ContratistasController::index');
    $subroutes->get('contratistas/getContratistas', 'ContratistasController::getContratistas');
    $subroutes->post('contratistas/saveContratista', 'ContratistasController::saveContratista');
    $subroutes->post('contratistas/updateContratista', 'ContratistasController::updateContratista');
    $subroutes->get('departamentos', 'DepartamentosController::index');
    $subroutes->get('departamentos/getDepartamentos', 'DepartamentosController::getDepartamentos');
    $subroutes->post('departamentos/saveDepartamento', 'DepartamentosController::saveDepartamento');
    $subroutes->post('departamentos/updateDepartamento', 'DepartamentosController::updateDepartamento');
    $subroutes->get('areas', 'AreasController::index');
    $subroutes->get('areas/getAreas', 'AreasController::getAreas');
    $subroutes->post('areas/saveArea', 'AreasController::saveArea');
    $subroutes->post('areas/updateArea', 'AreasController::updateArea');
    $subroutes->get('proyectos', 'ProyectosController::index');
    $subroutes->get('proyectos/getProyectos', 'ProyectosController::getProyectos');
    $subroutes->post('proyectos/saveProyecto', 'ProyectosController::saveProyecto');
    $subroutes->post('proyectos/updateProyecto', 'ProyectosController::updateProyecto');
    $subroutes->get('servicios', 'ServiciosController::index');
    $subroutes->get('servicios/getServicios', 'ServiciosController::getServicios');
    $subroutes->post('servicios/saveServicio', 'ServiciosController::saveServicio');
    $subroutes->post('servicios/updateServicio', 'ServiciosController::updateServicio');
    $subroutes->get('horarios', 'HorariosController::index');
    $subroutes->get('horarios/getHorarios', 'HorariosController::getHorarios');
    $subroutes->post('horarios/saveHorario', 'HorariosController::saveHorario');
    $subroutes->post('horarios/updateHorario', 'HorariosController::updateHorario');
    $subroutes->get('comensales', 'ComensalesController::index');
    $subroutes->get('comensales/getComensales', 'ComensalesController::getComensales');
    $subroutes->get('comensales/getCodigoComensal', 'ComensalesController::getCodigoComensal');
    $subroutes->post('comensales/saveComensal', 'ComensalesController::saveComensal');
    $subroutes->post('comensales/updateComensal', 'ComensalesController::updateComensal');
    $subroutes->get('marcaciones', 'MarcacionesController::index');
    $subroutes->post('marcaciones/getMarcaciones', 'MarcacionesController::getMarcaciones');
    $subroutes->post('marcaciones/registrarMarcacionManual', 'MarcacionesController::registrarMarcacionManual');
    $subroutes->post('marcaciones/updateMarcacion', 'MarcacionesController::updateMarcacion');
    $subroutes->post('marcaciones/anularMarcacion', 'MarcacionesController::anularMarcacion');

});

$routes->group('comun', ['namespace' => '\Modules\Comun\Controllers'], function ($subroutes) {
//    $subroutes->post('proveedores/searchProveedor', 'SearchsController::searchProveedor');
});
