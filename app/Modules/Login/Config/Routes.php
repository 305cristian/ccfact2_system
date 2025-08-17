<?php

//TODO:TODA LA CONFIGURACION INICIAL PÁRA QUE ARRANQUE EL SISTEMA ESTA EN EL ARCHIVO Routes.php en la carpeta Config del sistema
//$routes->setDefaultNamespace('\Modules\Login\Controllers'); ruta inicial
//$routes->setDefaultController('IndexController'); metodo inicial

//TODO: EN GENERAL el primer parametro es es alias que se usara para llamar a la direccion
//TODO: El segundo parametros es la ruta del controlador, luego de los 2 puntos ::viene el metodo al cual estoy invocando
//TODO: como recomendacion SE RECOMIENDA QUE EL ALIAS LLEVE COMO DIRECCION TAMBIEN EL NOMBRE DEL CONTROLADOR SIN CONTROLLER EJM: /welcome/closeSession

//$routes->get('/', '\Modules\Login\Controllers\IndexController::index'); //Este se esta definiendo en el archivo principal Config/Routes.php
$routes->post('index/login', '\Modules\Login\Controllers\IndexController::login');
$routes->get('/welcome/closeSession', '\Modules\Login\Controllers\WelcomeController::closeSession');
$routes->get('welcome', '\Modules\Login\Controllers\WelcomeController::index');

