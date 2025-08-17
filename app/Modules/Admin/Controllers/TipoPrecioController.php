<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of TipoPrecioController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 3 jul 2024
 * @time 12:22:26 p.m.
 */
class TipoPrecioController extends \App\Controllers\BaseController {

    protected $dirViewModule;

    public function __construct() {
        $this->dirViewModule = 'Modules\Admin\Views';
    }

    public function index() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\tipoprecio\viewTipoPrecio');
        $send['user'] = $this->user;
        $send['ccm'] = $this->ccm;
        return $this->response->setJSON($send);
//        return view($this->dirTemplate . '\dashboard', $send);
    }

    public function getTipoPrecio() {
        $response = $this->ccm->getData("cc_tipo_precios");
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }

    public function saveTipoPrecio() {
        $tpNombre = $this->request->getPost('tpNombre');
        $tpDescripcion = $this->request->getPost('tpDescripcion');
        $tpEstado = $this->request->getPost('tpEstado');

        $this->validation->setRules([
            'tpNombre' => ['label' => 'Nombre Tipo precio', 'rules' => 'trim|required'],
            'tpDescripcion' => ['label' => 'Descripción tipo precio', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_tipo_precios', ['tpc_nombre' => $tpNombre]);
            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un tipo de precio registrado con el nombre ' . $tpNombre . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'tpc_nombre' => mb_strtoupper($tpNombre, 'UTF-8'),
                'tpc_descripcion' => $tpDescripcion,
                'tpc_estado' => $tpEstado,
                'tpc_fecha_creacion' => date('Y-m-d')
            ];

            $tpSave = $this->ccm->guardar($datos, 'cc_tipo_precios');
            $this->logs->logSuccess('SE HA CREADO UN TIPO DE PRECIO CON EL ID ' . $tpSave);
            $response['status'] = 'success';
            $response['msg'] = '<h5>Tipo de precio registrado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'tpNombre' => $this->validation->getError('tpNombre'),
                'tpDescripcion' => $this->validation->getError('tpDescripcion'),
            ];
        }
        return $this->response->setJson($response);
    }

    public function updateTipoPrecio() {
        $tpNombre = $this->request->getPost('tpNombre');
        $tpDescripcion = $this->request->getPost('tpDescripcion');
        $tpEstado = $this->request->getPost('tpEstado');

        $idTp = $this->request->getPost('idTp');
        $nameAux = $this->request->getPost('nameAux');

        $this->validation->setRules([
            'tpNombre' => ['label' => 'Nombre Tipo precio', 'rules' => 'trim|required'],
            'tpDescripcion' => ['label' => 'Descripción tipo precio', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_tipo_precios', ['tpc_nombre' => $tpNombre], '*', $orderBy = null, 1);
            if ($existe && $existe->tpc_nombre != $nameAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un tipo de precio registrado con el nombre ' . $tpNombre . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'tpc_nombre' => $tpNombre,
                'tpc_descripcion' => $tpDescripcion,
                'tpc_estado' => $tpEstado,
                'tpc_fecha_creacion' => date('Y-m-d')
            ];

            $this->ccm->actualizar('cc_tipo_precios', $datos, ['id' => $idTp]);
            $this->logs->logSuccess('SE HA ACTUALIZADO UN TIPO DE PRECIO CON EL ID ' . $idTp);

            $response['status'] = 'success';
            $response['msg'] = '<h5>Tipo de precio actualizado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'tpNombre' => $this->validation->getError('tpNombre'),
                'tpDescripcion' => $this->validation->getError('tpDescripcion'),
            ];
        }
        return $this->response->setJson($response);
    }
}
