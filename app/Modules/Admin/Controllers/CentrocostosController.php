<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of CentrocostosController
 *
  /**
 * @author CRISTIAN PAZ
 * @date 9 abr. 2024
 * @time 14:31:49
 */
class CentrocostosController extends \App\Controllers\BaseController {

    //put your code here
    protected $dirViewModule;

    public function __construct() {
        $this->dirViewModule = 'Modules\Admin\Views';
    }

    public function index() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\centrocostos\viewCentroCostos');
        $send['user'] = $this->user;
        $send['ccm'] = $this->ccm;
        return $this->response->setJSON($send);

//        return view($this->dirTemplate . '\dashboard', $send);
    }

    public function getCentrosCostos() {
        $response = $this->ccm->getData('cc_centroscosto');
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }

    public function saveCentroCosto() {
        $ccNombre = $this->request->getPost('ccNombre');
        $ccDescripcion = $this->request->getPost('ccDescripcion');
        $ccEstado = $this->request->getPost('ccEstado');

        $this->validation->setRules([
            'ccNombre' => ['label' => 'Nombre Centro de costos', 'rules' => 'trim|required'],
            'ccDescripcion' => ['label' => 'Descripción Centro de costos', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_centroscosto', ['cc_nombre' => $ccNombre]);
            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe una centro de costos registrado con el nombre ' . $ccNombre . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'cc_nombre' => mb_strtoupper($ccNombre, 'UTF-8'),
                'cc_descripcion' => $ccDescripcion,
                'cc_estado' => $ccEstado
            ];

            $ccSave = $this->ccm->guardar($datos, 'cc_centroscosto');
            $this->logs->logSuccess('SE HA CREADO UN CENTRO DE COSTOS CON EL ID ' . $ccSave);

            $response['status'] = 'success';
            $response['msg'] = '<h5>Centro de costos registrado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'ccNombre' => $this->validation->getError('ccNombre'),
                'ccDescripcion' => $this->validation->getError('ccDescripcion'),
            ];
        }
        return $this->response->setJson($response);
    }

    public function updateCentroCosto() {
        $ccNombre = $this->request->getPost('ccNombre');
        $ccDescripcion = $this->request->getPost('ccDescripcion');
        $ccEstado = $this->request->getPost('ccEstado');

        $idCC = $this->request->getPost('idCC');
        $nameAux = $this->request->getPost('nameAux');

        $this->validation->setRules([
            'ccNombre' => ['label' => 'Nombre Centro de costos', 'rules' => 'trim|required'],
            'ccDescripcion' => ['label' => 'Descripción Centro de costos', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_centroscosto', ['cc_nombre' => $ccNombre], '*', $orderBy = null, 1);
            if ($existe && $existe->cc_nombre != $nameAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe una centro de costos registrado con el nombre ' . $ccNombre . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'cc_nombre' => mb_strtoupper($ccNombre, 'UTF-8'),
                'cc_descripcion' => $ccDescripcion,
                'cc_estado' => $ccEstado
            ];

            $this->ccm->actualizar('cc_centroscosto', $datos, ['id' => $idCC]);
            $response['status'] = 'success';
            $response['msg'] = '<h5>Centro de costos actualizado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'ccNombre' => $this->validation->getError('ccNombre'),
                'ccDescripcion' => $this->validation->getError('ccDescripcion'),
            ];
        }
        return $this->response->setJson($response);
    }
}
