<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of RolesController
 *
  /**
 * @author CRISTIAN PAZ
 * @date 27 dic. 2023
 * @time 13:21:41
 */
use Modules\Admin\Models\AdminModel;

class RolesController extends \App\Controllers\BaseController {

    protected $dirViewModule;
    protected $admModel;

    public function __construct() {
        $this->dirViewModule = 'Modules\Admin\Views';
        $this->admModel = new AdminModel();
    }

    public function index() {
        $this->user->validateSession();
        $data['user'] = $this->user;
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['listaAllModulos'] = $this->ccm->getData('cc_modulos', ['md_tipo' => 'modulo', 'md_estado' => 1]);
        $data['listaAllSubModulos'] = $this->ccm->getData('cc_modulos', ['md_tipo' => 'submodulo', 'md_estado' => 1]);
        $data['listaAllAcciones'] = $this->ccm->getData('cc_acciones', ['ac_estado' => 1]);
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\roles\viewRoles', $data);
        $send['user'] = $this->user;
        $send['ccm'] = $this->ccm;
         return $this->response->setJSON($send);
//        return view($this->dirTemplate . '\dashboard', $send);
    }

    public function getRoles() {

        $response = $this->ccm->getData('cc_roles');
        if ($response) {
            return $this->response->setJSON($response);
        }else{
            return $this->response->setJSON(false);
        }
    }

    public function saveRol() {
        $nombreRol = $this->request->getPost('nombreRol');
        $estado = $this->request->getPost('estado');

        $this->validation->setRules([
            'nombreRol' => ['label' => 'Nombre Rol', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_roles', ['rol_nombre' => $nombreRol]);
            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un rol registrado con el nombre ' . $nombreRol . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'rol_nombre' => mb_strtoupper($nombreRol, 'UTF-8'),
                'rol_estado' => $estado,
                'rol_fecha_creacion' => date('Y-m-d')
            ];

            $this->ccm->guardar($datos, 'cc_roles');
            $response['status'] = 'success';
            $response['msg'] = '<h5>Rol registrado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'nombreRol' => $this->validation->getError('nombreRol'),
            ];
        }
        return $this->response->setJson($response);
    }

    public function updateRol() {

        $idRol = $this->request->getPost('idRol');
        $nombreRol = $this->request->getPost('nombreRol');
        $estado = $this->request->getPost('estado');
        $nameAux = $this->request->getPost('nameAux');

        $this->validation->setRules([
            'nombreRol' => ['label' => 'Nombre Rol', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_roles', ['rol_nombre' => $nombreRol], '*', $orderBy = null, 1);

            if ($existe && $existe->rol_nombre != $nameAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un rol registrado con el nombre ' . $nombreRol . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'rol_nombre' => mb_strtoupper($nombreRol, 'UTF-8'),
                'rol_estado' => $estado,
                'rol_fecha_creacion' => date('Y-m-d')
            ];

            $this->ccm->actualizar('cc_roles', $datos, ['id' => $idRol]);
            $response['status'] = 'success';
            $response['msg'] = '<h5>Rol actualizado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'nombreRol' => $this->validation->getError('nombreRol'),
            ];
        }
        return $this->response->setJson($response);
    }

    public function aplicarPermisos() {
        $data = json_decode(file_get_contents('php://input'));

        $response['status'] = '';
        $response['msg'] = '';

        $this->db->transBegin();

        if ($data->listaModulos) {

            $existe = $this->ccm->getData('cc_roles_modulos', ['fk_rol' => $data->rolId]);

            if (count($existe) > 0) {
                $this->ccm->eliminar('cc_roles_modulos', ['fk_rol' => $data->rolId]);
            }

            foreach ($data->listaModulos as $mod) {
                $datos = [
                    'fk_modulo' => $mod,
                    'fk_rol' => $data->rolId,
                    'rm_fecha' => date('Y-m-d')
                ];
                $this->ccm->guardar($datos, 'cc_roles_modulos');
            }
        }
        if ($data->listaAcciones) {

            $existe = $this->ccm->getData('cc_roles_accion', ['fk_rol' => $data->rolId]);

            if (count($existe) > 0) {
                $this->ccm->eliminar('cc_roles_accion', ['fk_rol' => $data->rolId]);
            }

            foreach ($data->listaAcciones as $acc) {
                $datos = [
                    'fk_accion' => $acc,
                    'fk_rol' => $data->rolId,
                    'ra_fecha' => date('Y-m-d'),
                ];
                $this->ccm->guardar($datos, 'cc_roles_accion');
            }
        }
        if ($this->db->transStatus() === false) {
            $response['status'] = 'fail';
            $response['msg'] = '<h5>Ha ocurrido un error al aplicar los permisos</h5>';
            // generate an error... or use the log_message() function to log your error
            $this->db->transRollback();
            die();
        } else {
            $response['status'] = 'success';
            $response['msg'] = '<h5>Permisos aplicados exitosamente</h5>';
            $this->db->transCommit();
        }
        
        return $this->response->setJSON($response);
    }
    
    public function loadPermisosRol() {
        $datos = json_decode(file_get_contents('php://input'));
        
        $response['listaModulos']= $this->admModel->getRolesModulos($datos->idRol);
        $response['listaAcciones']= $this->admModel->getRolesAcciones($datos->idRol);
        
        return $this->response->setJSON($response);
    }
}
