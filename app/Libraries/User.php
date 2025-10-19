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

use App\Models\Roles;
use Config\Services;

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

    public function __construct($session = null) {

        $this->session = $session ?? Services::session();

        $this->rol = new Roles();

        $this->id = '';
        if (!empty($this->session->get('id'))) {
            $this->id = $this->session->get('id');
        }
        if (!empty($this->session->get('user_dni'))) {
            $this->user_dni = $this->session->get('user_dni');
        }
        if (!empty($this->session->get('apellidos'))) {
            $this->apellidos = $this->session->get('apellidos');
        }
        if (!empty($this->session->get('nombres'))) {
            $this->nombres = $this->session->get('nombres');
        }
        if (!empty($this->session->get('celular'))) {
            $this->celular = $this->session->get('celular');
        }
        if (!empty($this->session->get('telefono'))) {
            $this->telefono = $this->session->get('telefono');
        }
        if (!empty($this->session->get('email'))) {
            $this->email = $this->session->get('email');
        }
        if (!empty($this->session->get('root'))) {
            $this->root = $this->session->get('root');
        }
        if (!empty($this->session->get('bodega_main'))) {
            $this->bodega_main = $this->session->get('bodega_main');
        }
        if (!empty($this->session->get('cargo_empleado'))) {
            $this->cargo_empleado = $this->session->get('cargo_empleado');
        }
    }

    public function validatePermisos($persmiso, $user) {
        if ($this->root == 1) {
            return true;
        }

        $response = $this->rol->validatePermisos($persmiso, $user);
        return $response;
    }

    public function validateSession() {

        if (empty($this->id)) {
            $this->session->set('message', '!Atencion, su sesión ha caducado');
            echo '<script>window.location.replace("' . site_url() . '") </script>';
            die();
        }
    }
}
