<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Comun\Models;

/**
 * Description of BodegasUserModel
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 8 dic 2025
 * @time 4:45:34 p.m.
 */
class BodegasUserModel extends \CodeIgniter\Model {

    //put your code here

    public function getUsuariosByBodega($bodegaId) {

        $builder = $this->db->table('cc_empleado_bodegas tb1');
        $builder->select("CONCAT( tb2.emp_nombre, ' ', tb2.emp_apellido ) empleado,
                            tb2.id,
                            tb3.bod_nombre ");
        $builder->join("cc_empleados tb2", "tb2.id = tb1.fk_empleado");
        $builder->join("cc_bodegas tb3", "tb3.id = tb1.fk_bodega");
        $builder->where("tb1.fk_bodega", $bodegaId);

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }
}
