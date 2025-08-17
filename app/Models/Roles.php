<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Models;

/**
 * Description of Roles
 *
  /**
 * @author CRISTIAN PAZ
 * @date 27 dic. 2023
 * @time 12:11:57
 */
class Roles extends \CodeIgniter\Model {

    public function validatePermisos($permiso, $user) {
        $builder = $this->db->table('cc_roles_accion tb1');
        $builder->select('tb2.rol_nombre,tb3.ac_nombre ');
        $builder->join('cc_roles tb2', 'tb2.id = tb1.fk_rol');
        $builder->join('cc_acciones tb3', 'tb3.id = tb1.fk_accion');
        $builder->join('cc_empleados tb4 ', 'tb4.fk_rol = tb2.id ');
        $builder->where(['tb4.id' => $user, 'tb3.ac_nombre' => $permiso]);

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
