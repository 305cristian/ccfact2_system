<?php

//TODO:TODA LA CONFIGURACION INICIAL PÁRA QUE ARRANQUE EL SISTEMA ESTA EN EL ARCHIVO Routes.php en la carpeta Config del sistema
//$routes->setDefaultNamespace('\Modules\Login\Controllers'); ruta inicial
//$routes->setDefaultController('IndexController'); metodo inicial

//TODO: EN GENERAL el primer parametro es es alias que se usara para llamar a la direccion
//TODO: El segundo parametros es la ruta del controlador, luego de los 2 puntos ::viene el metodo al cual estoy invocando
//TODO: como recomendacion SE RECOMIENDA QUE EL ALIAS LLEVE COMO DIRECCION TAMBIEN EL NOMBRE DEL CONTROLADOR SIN CONTROLLER EJM: /welcome/closeSession

$routes->get('admin', '\Modules\Admin\Controllers\Admin::index');
//
//if(!isset($routes))
//{ 
//    $routes = \Config\Services::routes(true);
//}
//
//$routes->group('admin', ['namespace' => 'App\Modules\Admin\Controllers'], function($subroutes){
//
//	/*** Route for Dashboard ***/
//	$subroutes->get('admin', 'Admin::index');
//
//});
