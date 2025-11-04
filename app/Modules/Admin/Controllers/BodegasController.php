<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of BodegasController
 *
  /**
 * @author CRISTIAN PAZ
 * @date 8 abr. 2024
 * @time 21:41:35
 */
class BodegasController extends \App\Controllers\BaseController {

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
        $send['view'] = view($this->dirViewModule . '\bodegas\viewBodegas');
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function getBodegas() {
        $response = $this->ccm->getData('cc_bodegas');
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }

    public function saveBodegas() {
        $bodNombre = $this->request->getPost('bodNombre');
        $bodDescripcion = $this->request->getPost('bodDescripcion');
        $bodEstado = $this->request->getPost('bodEstado');

        $this->validation->setRules([
            'bodNombre' => ['label' => 'Nombre Bodega', 'rules' => 'trim|required'],
            'bodDescripcion' => ['label' => 'Descripción Bodega', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_bodegas', ['bod_nombre' => $bodNombre]);
            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe una bodega registrado con el nombre ' . $bodNombre . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'bod_nombre' => mb_strtoupper($bodNombre, 'UTF-8'),
                'bod_descripcion' => $bodDescripcion,
                'bod_estado' => $bodEstado
            ];

            $bodSave = $this->ccm->guardar($datos, 'cc_bodegas');
            $this->logs->logSuccess('SE HA CREADO UNA BODEGA CON EL ID ' . $bodSave);
            $response['status'] = 'success';
            $response['msg'] = '<h5>Bodega registrado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'bodNombre' => $this->validation->getError('bodNombre'),
                'bodDescripcion' => $this->validation->getError('bodDescripcion'),
            ];
        }
        return $this->response->setJson($response);
    }

    public function updateBodegas() {
        $bodNombre = $this->request->getPost('bodNombre');
        $bodDescripcion = $this->request->getPost('bodDescripcion');
        $bodEstado = $this->request->getPost('bodEstado');

        $idBod = $this->request->getPost('idBod');
        $nameAux = $this->request->getPost('nameAux');

        $this->validation->setRules([
            'bodNombre' => ['label' => 'Nombre Bodega', 'rules' => 'trim|required'],
            'bodDescripcion' => ['label' => 'Descripción Bodega', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_bodegas', ['bod_nombre' => $bodNombre], '*', $orderBy = null, 1);

            if ($existe && $existe->bod_nombre != $nameAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe una bodega registrado con el nombre ' . $bodNombre . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'bod_nombre' => mb_strtoupper($bodNombre, 'UTF-8'),
                'bod_descripcion' => $bodDescripcion,
                'bod_estado' => $bodEstado
            ];

            $this->ccm->actualizar('cc_bodegas', $datos, ['id' => $idBod]);
            $response['status'] = 'success';
            $response['msg'] = '<h5>Bodega actualizada exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'bodNombre' => $this->validation->getError('bodNombre'),
                'bodDescripcion' => $this->validation->getError('bodDescripcion'),
            ];
        }
        return $this->response->setJson($response);
    }
}
