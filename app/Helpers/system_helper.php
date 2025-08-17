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

function enterprice(){
    global $_CI4;
    $respuesa = $_CI4->ccm->getData('cc_empresa',$where_data = null, $fields = '', $order_by = null, $rows_num = 1);
    return $respuesa;
}

function themeSelect($idUser) {
    global $_CI4;
    $respuesta = $_CI4->ccm->getValue('cc_empleados', $idUser, 'theme_system', 'id');
    return $respuesta;
}

function getSettings($value) {
    global $_CI4;
    $val = $_CI4->ccm->getValue('cc_settings', $value, 'st_value', 'st_nombre');
    return $val;
}

function numberDecimal($num) {
    $num = (double) $num;
    return number_format($num, NUMDECIMALES, '.', '');
}

function roundNumber($num, $decimales = "2") {
    $number_format = (double) $num;
    $one_conversion = round($number_format, 5);
    $two_conversion = round($one_conversion, 4);
    $tree_conversion = round($two_conversion, 3);
    $reponseValue = round($tree_conversion, NUMDECIMALES);

    return number_format($reponseValue, $decimales, '.', '');
}
