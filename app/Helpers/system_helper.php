<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of utility_helper
 * @author Cristian R. Paz
 * @Date 2 feb. 2023
 * @Time 15:24:27
 */
//put your code here
function registerInstanceCI4(\App\Controllers\BaseController &$_ci) {
    global $_CI4;
    $_CI4 = $_ci;
}

use Config\Services;

function enterprice() {
    $ccm = Services::ccModel();
    $respuesa = $ccm->getData('cc_empresa', $where_data = null, $fields = '', $order_by = null, $rows_num = 1);
    return $respuesa;
}

function themeSelect($idUser) {
    $ccm = Services::ccModel();
    $respuesta = $ccm->getValue('cc_empleados', $idUser, 'theme_system', 'id');
    return $respuesta;
}

function getSettings($value) {
    $ccm = Services::ccModel();
    $val = $ccm->getValue('cc_settings', $value, 'st_value', 'st_nombre');
    return $val;
}

function numberDecimal($num) {
    $number = (double) $num;
    return number_format($number, NUMDECIMALES, '.', '');
}

function getPeriodoContable($fecha) {
    $ccm = Services::ccModel();
    $respuesta = $ccm->getData('cc_periodos_contables', ['pc_fecha_inicio <=' => $fecha, 'pc_fecha_fin >=' => $fecha, 'pc_estado' => 'ABIERTO'], 'id', null, 1);
    return $respuesta ? $respuesta->id : null;
}

function getNumeroAsiento($fecha) {
    $ccm = Services::ccModel();
    $respuesta = $ccm->getData('cc_periodos_contables', ['pc_fecha_inicio <=' => $fecha, 'pc_fecha_fin >=' => $fecha, 'pc_estado' => 'ABIERTO'], 'id, pc_valor', null, 1);
    if ($respuesta->pc_valor <= 1) {
        $ccm->actualizar('cc_periodos_contables', ['pc_valor' => 2], ['id' => $respuesta->id]);
        return 1;
    } else {
        $ccm->actualizar('cc_periodos_contables', ['pc_valor' => $respuesta->pc_valor + 1], ['id' => $respuesta->id]);
        return $respuesta->pc_valor;
    }
}
