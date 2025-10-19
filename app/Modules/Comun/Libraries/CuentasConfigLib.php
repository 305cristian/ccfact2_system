<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Comun\Libraries;

/**
 * Description of CuentasConfigLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 19 oct 2025
 * @time 12:52:45 p.m.
 */
class CuentasConfigLib {

    //put your code here

    protected $ccm;

    public function __construct() {

        $this->ccm = service('ccModel');
    }

    public function obtenerSettingCuentaContable($codigo) {
        return $this->ccm->getValueWhere('cc_cuenta_contabledet_config', ['ctcf_codigo' => $codigo], 'fk_cuentacontable_det');
    }
}
