<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of EmployeeController
 * @author Cristian R. Paz
 * @Date 29 sep. 2023
 * @Time 11:30:58
 */
use Modules\Admin\Models\EmployeeModel;

class EmployeeController extends \App\Controllers\BaseController {

    protected $dirViewModule;
    protected $empModel;

    public function __construct() {
        $this->dirViewModule = 'Modules\Admin\Views';
        $this->empModel = new EmployeeModel();
    }

    public function index() {
        $this->user->validateSession();
        $data['user'] = $this->user;
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaCargos'] = $this->ccm->getData('cc_cargo', ['carg_estado' => 1]);
        $data['listaDepartamentos'] = $this->ccm->getData('cc_departamento', ['dep_estado' => 1]);
        $data['listaRoles'] = $this->ccm->getData('cc_roles', ['rol_estado' => 1]);
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\employee\viewEmployee', $data);
        $send['user'] = $this->user;
        $send['ccm'] = $this->ccm;
        return $this->response->setJSON($send);
//        return view($this->dirTemplate . '\dashboard', $send);
    }

    public function getEmpleados() {
        $is_root = $this->ccm->getValue('cc_empleados', $this->user->id, 'is_root', 'id');

        $response = $this->empModel->getEmpleados($is_root);
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }

    public function getBodegas() {
        $data = json_decode(file_get_contents('php://input'));
        $response = $this->empModel->getBodegasEmpleado($data->idEmp);
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }

    public function saveEmpleado() {

        $nombres = $this->request->getPost('nombres');
        $apellidos = $this->request->getPost('apellidos');
        $dni = $this->request->getPost('dni');
        $usuario = $this->request->getPost('usuario');
        $password = $this->request->getPost('password');
        $email = $this->request->getPost('email');
        $telefono = $this->request->getPost('telefono');
        $celular = $this->request->getPost('celular');
        $rol = $this->request->getPost('rol');
        $cargo = $this->request->getPost('cargo');
        $departamento = $this->request->getPost('departamento');
        $bodegaMain = $this->request->getPost('bodegaMain');
        $estado = $this->request->getPost('estado');
        $bodegas = $this->request->getPost('bodegas');

        $this->db->transBegin();

        $this->validation->setRules([
            'dni' => ['label' => 'DNI', 'rules' => 'trim|required'],
            'nombres' => ['label' => 'Nombres', 'rules' => 'trim|required'],
            'apellidos' => ['label' => 'Apellidos', 'rules' => 'trim|required'],
            'usuario' => ['label' => 'Usuario', 'rules' => 'trim|required'],
            'password' => ['label' => 'Password', 'rules' => 'trim|required'],
            'celular' => ['label' => 'Celular', 'rules' => 'trim|required'],
            'email' => ['label' => 'Email', 'rules' => 'trim|required|valid_email'],
            'rol' => ['label' => 'Rol', 'rules' => 'trim|required'],
            'bodegaMain' => ['label' => 'Bodega Principal', 'rules' => 'trim|required'],
            'bodegas' => ['label' => 'Bodega (Debe seleccionar al menos una bodega)', 'rules' => 'trim|required'],
        ]);
        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_empleados', ['emp_dni' => $dni]);
            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un empleado registrado con el DNI ' . $dni . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'emp_nombre' => mb_strtoupper($nombres, 'UTF-8'),
                'emp_apellido' => mb_strtoupper($apellidos, 'UTF-8'),
                'emp_dni' => $dni,
                'emp_username' => $usuario,
                'emp_password' => password_hash($password, PASSWORD_BCRYPT),
                'emp_email' => $email,
                'emp_telefono' => $telefono,
                'emp_celular' => $celular,
                'emp_estado' => $estado,
                'theme_system' => "#505e9b,#6ce3db",
                'fk_rol' => $rol,
                'fk_cargo' => $cargo,
                'fk_departamento' => $departamento,
                'fk_bodega_main' => $bodegaMain,
            ];

            $idEmp = $this->ccm->guardar($datos, 'cc_empleados');

            if ($bodegas) {
                $listaBodegas = explode(',', $bodegas);
                foreach ($listaBodegas as $bod) {
                    $datos = [
                        'fk_empleado' => $idEmp,
                        'fk_bodega' => $bod,
                    ];
                    $this->ccm->guardar($datos, 'cc_empleado_bodegas');
                }
            }
            if ($this->db->transStatus() === false) {
                // generate an error... or use the log_message() function to log your error
                $this->db->transRollback();
                die();
            } else {
                $this->logs->logSuccess('SE HA CREADO UA EMPLEADO CON EL ID ' . $idEmp);
                $response['status'] = 'success';
                $response['msg'] = '<h5>Empleado registrado exitosamente</h5>';
                $this->db->transCommit();
            }
        } else {

            $response['status'] = 'vacio';
            $response['msg'] = [
                'dni' => $this->validation->getError('dni'),
                'nombres' => $this->validation->getError('nombres'),
                'apellidos' => $this->validation->getError('apellidos'),
                'usuario' => $this->validation->getError('usuario'),
                'password' => $this->validation->getError('password'),
                'celular' => $this->validation->getError('celular'),
                'email' => $this->validation->getError('email'),
                'rol' => $this->validation->getError('rol'),
                'bodegaMain' => $this->validation->getError('bodegaMain'),
                'bodegas' => $this->validation->getError('bodegas'),
            ];
        }
        return $this->response->setJson($response);
    }

    public function updateEmpleado() {

        $idEmp = $this->request->getPost('idEmp');
        $dniAux = $this->request->getPost('dniAux');

        $nombres = $this->request->getPost('nombres');
        $apellidos = $this->request->getPost('apellidos');
        $dni = $this->request->getPost('dni');
        $usuario = $this->request->getPost('usuario');
        $email = $this->request->getPost('email');
        $telefono = $this->request->getPost('telefono');
        $celular = $this->request->getPost('celular');
        $rol = $this->request->getPost('rol');
        $cargo = $this->request->getPost('cargo');
        $departamento = $this->request->getPost('departamento');
        $bodegaMain = $this->request->getPost('bodegaMain');
        $estado = $this->request->getPost('estado');
        $bodegas = $this->request->getPost('bodegas');

        $this->db->transBegin();

        $this->validation->setRules([
            'dni' => ['label' => 'DNI', 'rules' => 'trim|required'],
            'nombres' => ['label' => 'Nombres', 'rules' => 'trim|required'],
            'apellidos' => ['label' => 'Apellidos', 'rules' => 'trim|required'],
            'usuario' => ['label' => 'Usuario', 'rules' => 'trim|required'],
            'celular' => ['label' => 'Celular', 'rules' => 'trim|required'],
            'email' => ['label' => 'Email', 'rules' => 'trim|required|valid_email'],
            'rol' => ['label' => 'Rol', 'rules' => 'trim|required'],
            'bodegaMain' => ['label' => 'Bodega Principal', 'rules' => 'trim|required'],
            'bodegas' => ['label' => 'Bodega (Debe seleccionar al menos una bodega)', 'rules' => 'trim|required'],
        ]);
        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_empleados', ['emp_dni' => $dni]);
            if (count($existe) > 0 && $existe[0]->emp_dni != $dniAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un empleado registrado con el DNI ' . $dni . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'emp_nombre' => mb_strtoupper($nombres, 'UTF-8'),
                'emp_apellido' => mb_strtoupper($apellidos, 'UTF-8'),
                'emp_dni' => $dni,
                'emp_username' => $usuario,
                'emp_email' => $email,
                'emp_telefono' => $telefono,
                'emp_celular' => $celular,
                'emp_estado' => $estado,
                'fk_rol' => $rol,
                'fk_cargo' => $cargo,
                'fk_departamento' => $departamento,
                'fk_bodega_main' => $bodegaMain,
            ];

            $this->ccm->actualizar('cc_empleados', $datos, ['id' => $idEmp]);

            if ($bodegas) {
                $listaBodegas = explode(',', $bodegas);
                $this->ccm->eliminar('cc_empleado_bodegas', ['fk_empleado' => $idEmp]);
                foreach ($listaBodegas as $bod) {
                    $datos = [
                        'fk_empleado' => $idEmp,
                        'fk_bodega' => $bod,
                    ];
                    $this->ccm->guardar($datos, 'cc_empleado_bodegas');
                }
            }
            if ($this->db->transStatus() === false) {
                // generate an error... or use the log_message() function to log your error
                $this->db->transRollback();
                die();
            } else {
                $response['status'] = 'success';
                $response['msg'] = '<h5>Empleado Actualizado exitosamente</h5>';
                $this->db->transCommit();
            }
        } else {

            $response['status'] = 'vacio';
            $response['msg'] = [
                'dni' => $this->validation->getError('dni'),
                'nombres' => $this->validation->getError('nombres'),
                'apellidos' => $this->validation->getError('apellidos'),
                'usuario' => $this->validation->getError('usuario'),
                'celular' => $this->validation->getError('celular'),
                'email' => $this->validation->getError('email'),
                'rol' => $this->validation->getError('rol'),
                'bodegaMain' => $this->validation->getError('bodegaMain'),
                'bodegas' => $this->validation->getError('bodegas'),
            ];
        }
        return $this->response->setJson($response);
    }

    public function resetPassword() {
        $data = json_decode(file_get_contents('php://input'));

        $hashedPassword = password_hash($data->newPassword, PASSWORD_BCRYPT);
        $this->ccm->actualizar('cc_empleados', ['emp_password' => $hashedPassword], ['id' => $data->idEmpPR]);
        $response['status'] = 'success';
        $response['msg'] = 'Contraseña actualizada exitosamente.';

        return $this->response->setJSON($response);
    }
}
