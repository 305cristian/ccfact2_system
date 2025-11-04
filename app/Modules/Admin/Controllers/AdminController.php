<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of Adminn
 * @author Cristian R. Paz
 * @Date 29 ene. 2023
 * @Time 15:40:39
 */
class AdminController extends \App\Controllers\BaseController {

    protected $dirViewModule;

    public function __construct() {

        $this->dirViewModule = 'Modules\Admin\Views';
    }

    public function index() {

        $this->user->validateSession();

        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);

        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewControlPanel');

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function getDataEmpleado() {
        $data = json_decode(file_get_contents('php://input'));
        $respuesta = $this->ccm->getData('cc_empleados', ['id' => $data->idUser], 'id, emp_nombre,emp_apellido,emp_username,emp_email,emp_celular', $order = null, 1);

        if ($respuesta) {
            return $this->response->setJSON($respuesta);
        }
    }

    public function updateEmployee() {
        $data = json_decode(file_get_contents('php://input'));

        $datos = [
            'emp_apellido' => $data->apellido,
            'emp_nombre' => $data->nombre,
            'emp_username' => $data->usuario,
            'emp_email' => $data->email,
            'emp_celular' => $data->celular,
        ];
        $update = $this->ccm->actualizar('cc_empleados', $datos, ['id' => $data->idUser]);

        if ($update) {
            $this->session->destroy();
            return $this->response->setJSON($update);
        }
    }

    public function resetPassword() {
        $data = json_decode(file_get_contents('php://input'));

        $empleado = $this->ccm->getData('cc_empleados', ['id' => $this->user->id], 'emp_password', $order = null, 1);
        $validacion = $this->validatePasswordActual($data->passActual, $empleado->emp_password);

        $response['estado'] = '';
        $response['msg'] = '';

        if ($validacion) {
            if ($data->passNew == $data->passConfNew) {
                $hashedPassword = password_hash($data->passNew, PASSWORD_BCRYPT);
                $this->ccm->actualizar('cc_empleados', ['emp_password' => $hashedPassword], ['id' => $this->user->id]);
                $this->session->destroy();
                $response['estado'] = 'success';
                $response['msg'] = 'Contraseña actualizada exitosamente.';
            } else {
                $response['estado'] = 'danger';
                $response['msg'] = 'La confirmacion de contraseñas es incorrecta';
            }
        } else {
            $response['estado'] = 'danger';
            $response['msg'] = 'La contraseña actual no es la correcta';
        }
        return $this->response->setJSON($response);
    }

    public function validatePasswordActual($password, $passwordDb) {
        if (password_verify($password, $passwordDb)) {
            return true;
        } else {
            return false;
        }
    }

    public function changeThemes() {
        $data = json_decode(file_get_contents('php://input'));
        $newTheme = $data->color1 . ',' . $data->color2;
        $update = $this->ccm->actualizar('cc_empleados', ['theme_system' => $newTheme], ['id' => $this->user->id]);
        if ($update) {
            return $this->response->setJSON($update);
        }
    }
}
