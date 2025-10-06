<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Models;

/**
 * Description of CuentasContablesModel
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 2 oct 2025
 * @time 2:44:38 p.m.
 */
class CuentasContablesModel extends \CodeIgniter\Model {

    //put your code here

    public function getCuentasContables() {
        $builder = $this->db->table('cc_cuenta_contabledet tb1');
        $builder->select('tb1.ctad_codigo,
                            tb1.ctad_nombre_cuenta,
                            tb3.ctad_codigo AS codigo_cuenta_padre,
                            tb3.ctad_nombre_cuenta AS cuenta_padre,
                            tb1.ctad_estado,
                            tb2.cta_codigo,
                            tb2.cta_nombre tipo_cuenta');
        $builder->join('cc_cuenta_contable tb2', 'tb2.cta_codigo = tb1.fk_cta_contable', 'left');
        $builder->join('cc_cuenta_contabledet tb3', 'tb3.ctad_codigo = tb1.ctad_cuenta_padre', 'left');

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function getCuentasConfig() {
        $builder = $this->db->table('cc_cuenta_contabledet_config  tb1');
        $builder->select('tb1.id, tb1.ctcf_codigo,
                            tb1.ctcf_nombre,
                            tb1.ctcf_detalle,
                            tb1.fk_cuentacontable_det,
                            CONCAT(tb2.ctad_codigo," ",tb2.ctad_nombre_cuenta) cuenta_contable,
                            tb1.ctcf_estado');

        $builder->join('cc_cuenta_contabledet tb2 ', 'tb2.ctad_codigo = tb1.fk_cuentacontable_det', 'left');

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function searchCuentasContables($dataSearch) {
        $builder = $this->db->table('cc_cuenta_contabledet tb1');
        $builder->select('tb1.ctad_codigo, tb1.ctad_nombre_cuenta');
        $builder->groupStart();
        $builder->like('tb1.ctad_codigo', $dataSearch);
        $builder->orLike('tb1.ctad_nombre_cuenta', $dataSearch);
        $builder->groupEnd();
        $builder->limit(10);

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }
}
