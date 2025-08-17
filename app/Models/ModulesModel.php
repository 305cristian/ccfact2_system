<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of ModulesModel
 * @author Cristian R. Paz
 * @Date 29 sep. 2023
 * @Time 9:02:20
 */

namespace App\Models;

class ModulesModel extends \CodeIgniter\Model {

    public function getModulosUser($idUser) {

        $builder = $this->db->table('cc_roles_modulos tb1');
        $builder->select('tb3.*, tb4.emp_apellido,tb4.emp_nombre');		
        $builder->join('cc_roles tb2 ', 'tb2.id = tb1.fk_rol');
        $builder->join('cc_modulos tb3 ', 'tb3.id = tb1.fk_modulo');
        $builder->join('cc_empleados tb4 ', 'tb4.fk_rol = tb2.id');
        $builder->where(['tb4.id' => $idUser->id, 'tb3.md_tipo' => 'modulo', 'tb3.md_estado' => 1]);
        $builder->orderBy('tb3.md_orden', 'asc');
        $respuesta = $builder->get();

        if ($idUser->root) {
            $builder = $this->db->table('cc_modulos');
            $builder->select('*');
            $builder->where(['md_estado' => 1, 'md_tipo' => 'modulo']);
            $builder->orderBy('md_orden', 'asc');
            $respuesta = $builder->get();
        }
        if ($respuesta->getNumRows() > 0) {
            return $respuesta->getResult();
        } else {
            return false;
        }


    }

    public function getSubModulosUser($idMod, $idUser) {

        $builder = $this->db->table('cc_roles_modulos tb1');
        $builder->select('tb3.*, tb4.emp_apellido,tb4.emp_nombre');		
        $builder->join('cc_roles tb2 ', 'tb2.id = tb1.fk_rol');
        $builder->join('cc_modulos tb3 ', 'tb3.id = tb1.fk_modulo');
        $builder->join('cc_empleados tb4 ', 'tb4.fk_rol = tb2.id');
        $builder->where(['tb4.id' => $idUser->id, 'tb3.md_tipo' => 'submodulo', 'tb3.md_estado' => 1,'tb3.md_padre' => $idMod]);
        $builder->orderBy('tb3.md_orden', 'asc');
        $respuesta = $builder->get();

        if ($idUser->root) {
            $builder = $this->db->table('cc_modulos');
            $builder->select('*');
            $builder->where(['md_estado' => 1, 'md_tipo' => 'submodulo', 'md_padre' => $idMod]);
            $builder->orderBy('md_orden', 'asc');
            $respuesta = $builder->get();
        }

        if ($respuesta->getNumRows() > 0) {
            return $respuesta->getResult();
        } else {
            return false;
        }
//      
    }

}
