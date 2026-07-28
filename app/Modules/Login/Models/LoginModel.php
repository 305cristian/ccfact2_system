<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of LoginModel
 * @author Cristian R. Paz
 * @Date 31 ene. 2023
 * @Time 17:43:20
 */

namespace Modules\Login\Models;

class LoginModel extends \CodeIgniter\Model {

        
    public function loginValidate($username) {
        
        //TODO: Metodo de consultas 1 recomendado
        $query = 'SELECT id,
            emp_nombre,
            emp_apellido,
            emp_dni,
            emp_username,
            emp_password, 
            is_root,
            emp_telefono,
            emp_email,
            emp_celular,
            emp_foto,
            fk_cargo,
            fk_bodega_main
            FROM cc_empleados 
            WHERE BINARY emp_username = ? AND emp_estado=1';

         $resultado = $this->db->query($query, [$username]);
        if($resultado->getNumRows() > 0){
            return $resultado->getRow();
        }else{
            return false;
        }

    }

    public function getProyectosEmpleado(int $empleadoId): array {

        $builder = $this->db->table("cc_empleado_proyecto tb1");
        $builder->select("tb2.id, tb2.proy_codigo, tb2.proy_nombre");
        $builder->join("cc_proyectos tb2", "tb2.id = tb1.fk_proyecto");
        $builder->where([ "tb1.fk_empleado" => $empleadoId, "tb1.estado" => 1, "tb2.proy_estado" => 1]);
        $builder->orderBy("tb2.proy_nombre", "ASC");
        return $builder->get()->getResult();
    }

    public function getProyectoEmpleado(int $empleadoId, int $proyectoId): ?object {

        $builder = $this->db->table("cc_empleado_proyecto tb1");
        $builder->select("tb2.id, tb2.proy_codigo, tb2.proy_nombre");
        $builder->join("cc_proyectos tb2", "tb2.id = tb1.fk_proyecto");
        $builder->where([ "tb1.fk_empleado" => $empleadoId,"tb1.fk_proyecto" => $proyectoId,"tb1.estado" => 1, "tb2.proy_estado" => 1,
        ]);
        return $builder->get()->getRow();
    }

}
