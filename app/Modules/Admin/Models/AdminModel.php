<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Models;

/**
 * Description of ModulosModModel
 *
  /**
 * @author CRISTIAN PAZ
 * @date 14 feb. 2024
 * @time 16:32:40
 */
class AdminModel extends \CodeIgniter\Model {

    public function getModulos() {
        $builder = $this->db->table('cc_modulos tb1');
        $builder->select('tb2.*, tb1.md_nombre modulo_padre');
        $builder->join('cc_modulos tb2 ', 'tb2.md_padre = tb1.id', 'right');
//        $builder->where('tb2.md_estado',1);

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function getAcciones() {
        $builder = $this->db->table('cc_acciones tb1');
        $builder->select('tb1.*,tb2.md_nombre modulo,tb3.md_nombre submodulo');
        $builder->join('cc_modulos tb2 ', 'tb2.id = tb1.fk_modulo', 'left');
        $builder->join('cc_modulos tb3 ', 'tb3.id = tb1.fk_submodulo', 'left');
//        $builder->where('tb1.ac_estado', 1);

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function getRolesModulos($idRol) {
        $builder = $this->db->table('cc_roles_modulos tb1');
        $builder->select('tb1.fk_modulo');
        $builder->join('cc_modulos tb2 ', 'tb2.id = tb1.fk_modulo', 'left');
       $builder->where(['fk_rol'=>$idRol,'tb2.md_estado'=> 1]);

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function getRolesAcciones($idRol) {
         $builder = $this->db->table('cc_roles_accion tb1');
        $builder->select('tb1.fk_accion');
        $builder->join('cc_acciones tb2 ', 'tb2.id = tb1.fk_accion', 'left');
        $builder->where(['fk_rol'=>$idRol,'tb2.ac_estado'=> 1]);
        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }
}
