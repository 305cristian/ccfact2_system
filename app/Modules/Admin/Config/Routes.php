<?php

//TODO:TODA LA CONFIGURACION INICIAL PÁRA QUE ARRANQUE EL SISTEMA ESTA EN EL ARCHIVO Routes.php en la carpeta Config del sistema
//$routes->setDefaultNamespace('\Modules\Login\Controllers'); ruta inicial
//$routes->setDefaultController('IndexController'); metodo inicial
//TODO: EN GENERAL el primer parametro es es alias que se usara para llamar a la direccion
//TODO: El segundo parametros es la ruta del controlador, luego de los 2 puntos ::viene el metodo al cual estoy invocando
//TODO: como recomendacion SE RECOMIENDA QUE EL ALIAS LLEVE COMO DIRECCION TAMBIEN EL NOMBRE DEL CONTROLADOR SIN CONTROLLER EJM: /welcome/closeSession


if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->get('admin/(:num)', '\Modules\Admin\Controllers\AdminController::index/$1');

$routes->group('admin', ['namespace' => '\Modules\Admin\Controllers'], function ($subroutes) {

    $subroutes->get('managamentEmpleado', 'EmployeeController::index');
    $subroutes->get('employee/getEmpleados', 'EmployeeController::getEmpleados');
    $subroutes->post('employee/saveEmpleado', 'EmployeeController::saveEmpleado');
    $subroutes->post('employee/updateEmpleado', 'EmployeeController::updateEmpleado');
    $subroutes->post('employee/getBodegas', 'EmployeeController::getBodegas');
    $subroutes->post('employee/resetPassword', 'EmployeeController::resetPassword');

    $subroutes->post('empleado', 'AdminController::getDataEmpleado');
    $subroutes->post('updateEmployee', 'AdminController::updateEmployee');
    $subroutes->post('resetPassword', 'AdminController::resetPassword');
    $subroutes->post('changeThemes', 'AdminController::changeThemes');

    //TODO: PRODUCTOS
    $subroutes->get('productos/managamentProductos', 'ProductosController::index');
    $subroutes->post('productos/getProductos', 'ProductosController::getProductos');
    $subroutes->post('productos/saveProducto', 'ProductosController::saveProducto');
    $subroutes->post('productos/updateProducto', 'ProductosController::updateProducto');
    $subroutes->get('productos/getPreciosProducto/(:num)', 'ProductosController::getPreciosProducto/$1');
    $subroutes->get('productos/consultarAutoCodigo', 'ProductosController::consultarAutoCodigo');
    $subroutes->post('productos/searchProductos', 'ProductosController::searchProductos');

    //TODO: GRUPOS Y SUBGRUPOS
    $subroutes->get('grupos/managamentGrupos', 'GruposController::index');
    $subroutes->get('grupos/getGrupos', 'GruposController::getGrupos');
    $subroutes->post('grupos/saveGrupo', 'GruposController::saveGrupo');
    $subroutes->post('grupos/updateGrupo', 'GruposController::updateGrupo');

    $subroutes->get('grupos/getSubGrupos', 'GruposController::getSubGrupos');
    $subroutes->post('grupos/getSubgrupoByGrupo', 'GruposController::getSubgrupoByGrupo');
    $subroutes->post('grupos/saveSubGrupo', 'GruposController::saveSubGrupo');
    $subroutes->post('grupos/updateSubGrupo', 'GruposController::updateSubGrupo');

    //TODO: MARCAS
    $subroutes->get('marcas/managamentMarcas', 'MarcasController::index');
    $subroutes->get('marcas/getMarcas', 'MarcasController::getMarcas');
    $subroutes->post('marcas/saveMarca', 'MarcasController::saveMarca');
    $subroutes->post('marcas/updateMarca', 'MarcasController::updateMarca');

    //TODO: TIPOS DE PRODUCTOS
    $subroutes->get('tiposprod/managamentTiposProd', 'TipoProductoController::index');
    $subroutes->get('tiposprod/getTiposProducto', 'TipoProductoController::getTiposProducto');
    $subroutes->post('tiposprod/saveTipoProducto', 'TipoProductoController::saveTipoProducto');
    $subroutes->post('tiposprod/updateTipoProducto', 'TipoProductoController::updateTipoProducto');
    //TODO: TIPOS DE PRECIO
    $subroutes->get("tiposprice/namagamentTipoPrecio", "TipoPrecioController::index");
    $subroutes->get("tiposprice/getTipoPrecio", "TipoPrecioController::getTipoPrecio");
    $subroutes->post("tiposprice/saveTipoPrecio", "TipoPrecioController::saveTipoPrecio");
    $subroutes->post("tiposprice/updateTipoPrecio", "TipoPrecioController::updateTipoPrecio");

    //TODO: UNIDADES DE MEDIDA
    $subroutes->get('medida/managamentUnidadesMedida', 'UnidadMedidaController::index');
    $subroutes->get('medida/getUnidadesMedida', 'UnidadMedidaController::getUnidadesMedida');
    $subroutes->post('medida/saveUnidadMedida', 'UnidadMedidaController::saveUnidadMedida');
    $subroutes->post('medida/updateUnidadMedida', 'UnidadMedidaController::updateUnidadMedida');

    //TODO:MODULOS ACCIONES
    $subroutes->get('modacc/managamentModAcc', 'ModulosAcController::index');

    $subroutes->get('modacc/getModulos', 'ModulosAcController::getModulos');
    $subroutes->post('modacc/saveModulo', 'ModulosAcController::saveModulo');
    $subroutes->post('modacc/updateModulo', 'ModulosAcController::updateModulo');

    $subroutes->get('modacc/getAcciones', 'ModulosAcController::getAcciones');
    $subroutes->post('modacc/saveAccion', 'ModulosAcController::saveAccion');
    $subroutes->post('modacc/updateAccion', 'ModulosAcController::updateAccion');
    $subroutes->post('modacc/getSubModulo', 'ModulosAcController::getSubModulo');

    //TODO: TRANSACCIONES
    $subroutes->get('trans/managamentTransacciones', 'TransaccionController::index');
    $subroutes->get('trans/getTransacciones', 'TransaccionController::getTransacciones');
    $subroutes->post('trans/saveTransaccion', 'TransaccionController::saveTransaccion');
    $subroutes->post('trans/updateTransaccion', 'TransaccionController::updateTransaccion');

    $subroutes->get('sett/managamentSettings', 'SettingsController::index');
    $subroutes->get('sett/getSettings', 'SettingsController::getSettings');
    $subroutes->post('sett/saveSettings', 'SettingsController::saveSettings');
    $subroutes->post('sett/updateSettings', 'SettingsController::updateSettings');

    //TODO:BODEGAS
    $subroutes->get('bodegas/managamentBodegas', 'BodegasController::index');
    $subroutes->get('bodegas/getBodegas', 'BodegasController::getBodegas');
    $subroutes->post('bodegas/saveBodegas', 'BodegasController::saveBodegas');
    $subroutes->post('bodegas/updateBodegas', 'BodegasController::updateBodegas');

    //TODO:CENTROS COSTOS
    $subroutes->get('cc/managamentCC', 'CentrocostosController::index');
    $subroutes->get('cc/getCentrosCostos', 'CentrocostosController::getCentrosCostos');
    $subroutes->post('cc/saveCentroCosto', 'CentrocostosController::saveCentroCosto');
    $subroutes->post('cc/updateCentroCosto', 'CentrocostosController::updateCentroCosto');

    //TODO:EMPRESA
    $subroutes->get('enterprice/managamentEmpresa', 'EnterpriceController::index');
    $subroutes->get('enterprice/getEmpresa', 'EnterpriceController::getEmpresa');
    $subroutes->post('enterprice/saveEmpresa', 'EnterpriceController::saveEmpresa');
    $subroutes->post('enterprice/updateEmpresa', 'EnterpriceController::updateEmpresa');

    //TODO: SUSTENTOS
    $subroutes->get('sustento/managamentSustentos', 'SustentosController::index');
    $subroutes->get('sustento/getSustentos', 'SustentosController::getSustentos');
    $subroutes->post('sustento/saveSustento', 'SustentosController::saveSustento');
    $subroutes->post('sustento/updateSustento', 'SustentosController::updateSustento');

    //TODO: PUNTOS DE VENTA
    $subroutes->get('pventa/managamentPuntosVenta', 'PuntoVentaController::index');
    $subroutes->get('pventa/getPuntosVenta', 'PuntoVentaController::getPuntosVenta');
    $subroutes->post('pventa/savePuntoVenta', 'PuntoVentaController::savePuntoVenta');
    $subroutes->post('pventa/updatePuntoVenta', 'PuntoVentaController::updatePuntoVenta');
    $subroutes->post('pventa/showEmpleados/(:num)', 'PuntoVentaController::showEmpleados/$1');

    //TODO: ROLES
    $subroutes->get('managamentRoles', 'RolesController::index');
    $subroutes->get('roles/getRoles', 'RolesController::getRoles');
    $subroutes->post('roles/saveRol', 'RolesController::saveRol');
    $subroutes->post('roles/updateRol', 'RolesController::updateRol');
    $subroutes->post('roles/aplicarPermisos', 'RolesController::aplicarPermisos');
    $subroutes->post('roles/loadPermisosRol', 'RolesController::loadPermisosRol');

    //TODO: CLIENTES
    $subroutes->get('clientes/managamentClientes', 'ClientesController::index');
    $subroutes->post('clientes/getClientes', 'ClientesController::getClientes');
    $subroutes->post('clientes/searchClientes', 'ClientesController::searchClientes');
    $subroutes->post('clientes/saveCliente', 'ClientesController::saveCliente');
    $subroutes->post('clientes/updateCliente', 'ClientesController::updateCliente');

    //TODO:PROVEEDORES
    $subroutes->get('proveedores/managamentProveedores', 'ProveedoresController::index');
    $subroutes->post('proveedores/getProveedores', 'ProveedoresController::getProveedores');
    $subroutes->post('proveedores/searchProveedores', 'ProveedoresController::searchProveedores');
    $subroutes->post('proveedores/saveProveedor', 'ProveedoresController::saveProveedor');
    $subroutes->post('proveedores/updateProveedor', 'ProveedoresController::updateProveedor');
    $subroutes->post('proveedores/getBancos', 'ProveedoresController::getBancos');
    $subroutes->post('proveedores/getRetenciones', 'ProveedoresController::getRetenciones');
    $subroutes->get('proveedores/datosAdicionalesProveedor/(:num)', 'ProveedoresController::datosAdicionalesProveedor/$1');

    //TODO: Retenciones
    $subroutes->get('retenciones/managamentRetenciones', 'RetencionesController::index');
    $subroutes->get('retenciones/getRetenciones', 'RetencionesController::getRetenciones');
    $subroutes->post('retenciones/saveRetenciones', 'RetencionesController::saveRetenciones');
    $subroutes->post('retenciones/updateRetenciones', 'RetencionesController::updateRetenciones');

    //TODO: Cuentas contables
    $subroutes->get('cuentascontables/managamentCuentas', 'CuentasContablesController::index');
    $subroutes->get('cuentascontables/getCuentasContables', 'CuentasContablesController::getCuentasContables');
    $subroutes->post('cuentascontables/searchCuentasContables', 'CuentasContablesController::searchCuentasContables');
    $subroutes->get('cuentascontables/getCuentas', 'CuentasContablesController::getCuentas');
    $subroutes->post('cuentascontables/saveCuenta', 'CuentasContablesController::saveCuenta');
    $subroutes->post('cuentascontables/updateCuenta', 'CuentasContablesController::updateCuenta');

    //TODO: Cuentas config
    $subroutes->get('cuentasconfig/managamentCuentasConfig', 'ConfigCuentasController::index');
    $subroutes->get('cuentasconfig/getCuentasConfig', 'ConfigCuentasController::getCuentasConfig');
    $subroutes->post('cuentasconfig/saveConfigCuenta', 'ConfigCuentasController::saveConfigCuenta');
    $subroutes->post('cuentasconfig/updateConfigCuenta', 'ConfigCuentasController::updateConfigCuenta');

    //TODO: Bancos
    $subroutes->get('bancos/managamentBancos', 'BancosController::index');
    $subroutes->get('bancos/getBancos', 'BancosController::getBancos');
    $subroutes->post('bancos/saveBancos', 'BancosController::saveBancos');
    $subroutes->post('bancos/updateBancos', 'BancosController::updateBancos');
});

$routes->group('comun', ['namespace' => '\Modules\Comun\Controllers'], function ($subroutes) {

    $subroutes->get('clientes/getCantones/(:num)', 'IndexController::getCantonesByProvincia/$1');
    $subroutes->get('clientes/getParroquias/(:num)', 'IndexController::getParroquiasByCanton/$1');
});

