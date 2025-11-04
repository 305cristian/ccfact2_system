<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of SettingsController
 *
  /**
 * @author CRISTIAN PAZ
 * @date 21 mar. 2024
 * @time 15:29:30
 */
class SettingsController extends \App\Controllers\BaseController {

    protected $dirViewModule;

    public function __construct() {
        $this->dirViewModule = 'Modules\Admin\Views';
    }

    public function index() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\settingtransacciones\viewSettings', $data);
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function getSettings() {
        $response = $this->ccm->getData('cc_settings');
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }

    public function saveSettings() {
        $nombreSet = $this->request->getPost('nombreSett');
        $valueSet = $this->request->getPost('valueSett');
        $detalleSet = $this->request->getPost('detalleSett');

        $this->validation->setRules([
            'nombreSett' => ['label' => 'Nombre Setting', 'rules' => 'trim|required'],
            'valueSett' => ['label' => 'Valor Setting', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_settings', ['st_nombre' => $nombreSet]);
            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe una variable registrado con el nombre ' . $nombreSet . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'st_nombre' => mb_strtoupper($nombreSet, 'UTF-8'),
                'st_value' => $valueSet,
                'st_detalle' => $detalleSet
            ];

            $this->ccm->guardar($datos, 'cc_settings');
            $response['status'] = 'success';
            $response['msg'] = '<h5>Variable registrado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'nombreSett' => $this->validation->getError('nombreSett'),
                'valueSett' => $this->validation->getError('valueSett'),
            ];
        }
        return $this->response->setJson($response);
    }

    public function updateSettings() {
        $nombreSet = $this->request->getPost('nombreSett');
        $valueSet = $this->request->getPost('valueSett');
        $detalleSet = $this->request->getPost('detalleSett');

        $idSet = $this->request->getPost('idSett');
        $nameAux = $this->request->getPost('nameAux');

        $this->validation->setRules([
            'nombreSett' => ['label' => 'Nombre Setting', 'rules' => 'trim|required'],
            'valueSett' => ['label' => 'Valor Setting', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_settings', ['st_nombre' => $nombreSet], '*', $orderBy = null, 1);

            if ($existe && $existe->st_nombre != $nameAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe una variable registrado con el nombre ' . $nombreSet . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'st_nombre' => mb_strtoupper($nombreSet, 'UTF-8'),
                'st_value' => $valueSet,
                'st_detalle' => $detalleSet
            ];

            $this->ccm->actualizar('cc_settings', $datos, ['id' => $idSet]);
            $response['status'] = 'success';
            $response['msg'] = '<h5>Variable actualizada exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'nombreSett' => $this->validation->getError('nombreSett'),
                'valueSett' => $this->validation->getError('valueSett'),
            ];
        }
        return $this->response->setJson($response);
    }

//put your code here
}
