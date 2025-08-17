<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of User
 * @author Cristian R. Paz
 * @Date 1 feb. 2023
 * @Time 17:24:10
 */

namespace app\Libraries;

if (!isset($_SESSION)) {
    session_start();
}

use App\Models\Roles;

class User extends \App\Controllers\BaseController {

    //put your code here
    public $id;
    public $user_dni;
    public $apellidos;
    public $nombres;
    public $celular;
    public $telefono;
    public $email;
    public $root;
    public $bodega_main;
    public $cargo_empleado;
    protected $ci;
    public $rol;

    public function __construct() {

        global $_CI4;
        $this->ci = $_CI4;
        
        $this->rol = new Roles();

        $this->id = '';
        if (!empty($this->ci->session->get('id'))) {
            $this->id = $this->ci->session->get('id');
        }
        if (!empty($this->ci->session->get('user_dni'))) {
            $this->user_dni = $this->ci->session->get('user_dni');
        }
        if (!empty($this->ci->session->get('apellidos'))) {
            $this->apellidos = $this->ci->session->get('apellidos');
        }
        if (!empty($this->ci->session->get('nombres'))) {
            $this->nombres = $this->ci->session->get('nombres');
        }
        if (!empty($this->ci->session->get('celular'))) {
            $this->celular = $this->ci->session->get('celular');
        }
        if (!empty($this->ci->session->get('telefono'))) {
            $this->telefono = $this->ci->session->get('telefono');
        }
        if (!empty($this->ci->session->get('email'))) {
            $this->email = $this->ci->session->get('email');
        }
        if (!empty($this->ci->session->get('root'))) {
            $this->root = $this->ci->session->get('root');
        }
        if (!empty($this->ci->session->get('bodega_main'))) {
            $this->bodega_main = $this->ci->session->get('bodega_main');
        }
        if (!empty($this->ci->session->get('cargo_empleado'))) {
            $this->cargo_empleado = $this->ci->session->get('cargo_empleado');
        }
    }

    public function getAllModules() {
        
    }

    public function validatePermisos($persmiso, $user) {
        if ($this->root == 1) {
            return true;
        }
        
        $response = $this->rol->validatePermisos($persmiso, $user);
        return $response;
        
    }

    public function validateSession() {
        $userId = $this->id;
        if (empty($userId)) {
            $this->ci->session->set('message', '!Atencion, su sesión ha caducado');
            echo '<script>window.location.replace("' . site_url() . '") </script>';
            die();
        }
    }
}
