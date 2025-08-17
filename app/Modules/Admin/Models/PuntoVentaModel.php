<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Models;

/**
 * Description of PuntoVentaModel
 *
  /**
 * @author CRISTIAN PAZ
 * @date 12 abr. 2024
 * @time 12:57:54
 */
class PuntoVentaModel extends \CodeIgniter\Model {

    function getPuntosVenta() {
        $builder = $this->db->table('cc_puntos_venta tb1');
        $builder->select('tb1.*, tb2.bod_nombre, tb3.comp_nombre ');
        $builder->join('cc_bodegas tb2', 'tb2.id = tb1.pv_fk_bodega');
        $builder->join('cc_tipos_comprobante tb3', 'tb3.comp_codigo = tb1.fk_comprobante');

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function showEmpleados($idPv) {

        $builder = $this->db->table('cc_puntoventa_empleado tb1');
        $builder->select('CONCAT(tb2.emp_nombre," ",tb2.emp_apellido) empleado, id');
        $builder->join('cc_empleados tb2', 'tb2.id = tb1.fk_empleado');
        $builder->where('fk_punto_venta', $idPv);
        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }
}
