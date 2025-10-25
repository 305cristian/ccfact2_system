<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of BancosController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 2 oct 2025
 * @time 3:28:07 p.m.
 */
class BancosController extends \App\Controllers\BaseController {

    //put your code here

    public function __construct() {

        $this->dirViewModule = 'Modules\Admin\Views';
    }

    public function index() {
        $this->user->validateSession();
        $mod['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user; //Esto se envia a la vista para validar roles y permisos
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $mod);
        $send['view'] = view($this->dirViewModule . '\bancos\viewBancos', $data);
//        return view($this->dirTemplate . '\dashboard', $send);
        return $this->response->setJSON($send);
    }

    public function getBancos() {
        $response = $this->ccm->getData('cc_bancos_list');

        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }

    public function saveBancos() {
        $bancNombre = $this->request->getPost('bancNombre');
        $bancTipo = $this->request->getPost('bancTipo');
        $bancEstado = $this->request->getPost('bancEstado');

        $this->validation->setRules([
            'bancNombre' => ['label' => 'Nombre Banco', 'rules' => 'trim|required'],
            'bancTipo' => ['label' => 'Tipo de Cuenta', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {
            $existe = $this->ccm->getData('cc_bancos_list', ['banc_nombre' => $bancNombre]);

            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un banco registrado con el nombre ' . $bancNombre . '</h5>';
                return $this->response->setJSON($response);
            }

            $datos = [
                'banc_nombre' => mb_strtoupper($bancNombre, 'UTF-8'),
                'banc_tipo' => $bancTipo,
                'banc_estado' => $bancEstado
            ];

            $bancSave = $this->ccm->guardar($datos, 'cc_bancos_list');
            $this->logs->logSuccess('SE HA CREADO UN BANCO CON EL ID ' . $bancSave);

            $response['status'] = 'success';
            $response['msg'] = '<h5>Banco registrado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'bancNombre' => $this->validation->getError('bancNombre'),
                'bancTipo' => $this->validation->getError('bancTipo'),
            ];
        }

        return $this->response->setJSON($response);
    }

    public function updateBancos() {
        $bancNombre = $this->request->getPost('bancNombre');
        $bancTipo = $this->request->getPost('bancTipo');
        $bancEstado = $this->request->getPost('bancEstado');

        $idBanc = $this->request->getPost('idBanc');
        $nameAux = $this->request->getPost('nameAux');

        $this->validation->setRules([
            'bancNombre' => ['label' => 'Nombre Banco', 'rules' => 'trim|required'],
            'bancTipo' => ['label' => 'Tipo de Cuenta', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {
            $existe = $this->ccm->getData('cc_bancos_list', ['banc_nombre' => $bancNombre], '*', $orderBy = null, 1);

            if ($existe && $existe->banc_nombre != $nameAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un banco registrado con el nombre ' . $bancNombre . '</h5>';
                return $this->response->setJSON($response);
            }

            $datos = [
                'banc_nombre' => mb_strtoupper($bancNombre, 'UTF-8'),
                'banc_tipo' => $bancTipo,
                'banc_estado' => $bancEstado
            ];

            $this->ccm->actualizar('cc_bancos_list', $datos, ['id' => $idBanc]);

            $response['status'] = 'success';
            $response['msg'] = '<h5>Banco actualizado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'bancNombre' => $this->validation->getError('bancNombre'),
                'bancTipo' => $this->validation->getError('bancTipo'),
            ];
        }
        return $this->response->setJSON($response);
    }
}
