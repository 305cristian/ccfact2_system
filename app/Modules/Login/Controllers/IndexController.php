<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Index_controller
 * @author Cristian R. Paz
 * @Date 30 ene. 2023
 * @Time 10:58:09
 */

namespace Modules\Login\Controllers;

use Modules\Login\Models\LoginModel;

class IndexController extends \App\Controllers\BaseController {

    protected $rutaModule;
    protected $lModel;

    //put your code here
    public function __construct() {
        $this->rutaModule = 'Modules\Login\Views';
        $this->lModel = new LoginModel();
    }

    public function index() {

        if ($this->session->get('id')) {
            return redirect('welcome');
        }
        $send['title'] = 'LOGIN CCFACT';
        $send['validation'] = $this->validation->listErrors();
        return view($this->rutaModule . '\viewLogin', $send);
    }

    public function login() {
        $this->validation->setRules([
            'username' => ['label' => 'Username', 'rules' => 'trim|required'],
            'pass' => ['label' => 'Password', 'rules' => 'trim|required']
        ]);
        if ($this->validation->withRequest($this->request)->run()) {
            $username = $this->request->getPost('username');
            $pass = $this->request->getPost('pass');
            $validation = $this->checkCredencialesDb($username, $pass);
            if ($validation['request']['msg'] == 'success') {
                $this->session->start();

                return redirect('welcome');
                
            } else if ($validation['request']['msg'] == 'fail') {
                $send['validation'] = '<span><i class="fas fa-warning"></i></span> Contraseña incorrecta';
            } else if ($validation['request']['msg'] == 'no_user') {
                $send['validation'] = '<span><i class="fas fa-warning"></i></span> Usuario no encontrado';
            }
            $send['title'] = 'LOGIN CCFACT';
            return view($this->rutaModule . '\viewLogin', $send);
        } else {
            $send['title'] = 'LOGIN CCFACT';
            $send['validation'] = $this->validation->listErrors();
            return view($this->rutaModule . '\viewLogin', $send);
        }
    }

    public function checkCredencialesDb($username, $password) {
//        $p= password_hash($password, PASSWORD_DEFAULT);
//        echo $p;

        $respuesta = $this->lModel->loginValidate($username);
        $resp = [];
        if ($respuesta) {
            $resp['request'] = $this->decriptPassword($respuesta, $password);
        } else {
            $resp['request']['msg'] = 'no_user';
        }
        return $resp;
    }

    public function decriptPassword($respuesta, $password) {
        $msg = [];
        if (password_verify($password, $respuesta[0]->emp_password)) {

            $USER = [];
            foreach ($respuesta as $user) {
                $USER = [
                    'id' => $user->id,
                    'user_dni' => $user->emp_dni,
                    'apellidos' => $user->emp_apellido,
                    'nombres' => $user->emp_nombre,
                    'telefono' => $user->emp_telefono,
                    'celular' => $user->emp_celular,
                    'email' => $user->emp_email,
                    'root' => $user->is_root,
                    'bodega_main' => $user->fk_bodega_main,
                    'cargo_empleado' => $user->fk_cargo,
                ];
                $_SESSION['userdata'] = $USER;
                $this->session->set($USER);
            }
            $msg['msg'] = 'success';
        } else {
            $msg['msg'] = 'fail';
        }
        return $msg;
    }

}
