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

}
