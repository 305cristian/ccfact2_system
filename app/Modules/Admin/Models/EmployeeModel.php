<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of EmployeeModel
 *
  /**
 * @author CRISTIAN PAZ
 * @date 31 oct. 2023
 * @time 14:41:57
 */

namespace Modules\Admin\Models;

class EmployeeModel extends \CodeIgniter\Model {

    public function getEmpleados($isRoot) {

        $data = $this->db->table('cc_empleados tb1');
        $data->select('tb1.*, tb2.carg_nombre, tb3.dep_nombre, tb4.rol_nombre');
        $data->join('cc_cargo tb2', 'tb2.id = tb1.fk_cargo', 'left');
        $data->join('cc_departamento tb3', 'tb3.id = tb1.fk_departamento', 'left');
        $data->join('cc_roles tb4', 'tb4.id = tb1.fk_rol', 'left');
        if($isRoot != 1){
            $data->where('is_root',0);
        }

        $response = $data->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function getBodegasEmpleado($idEmp) {

        $builder = $this->db->table('cc_empleado_bodegas tb1');
        $builder->select('tb2.id, tb2.bod_nombre');
        $builder->join('cc_bodegas tb2', 'tb2.id = tb1.fk_bodega');
        $builder->where('tb1.fk_empleado',$idEmp);
        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }
}
