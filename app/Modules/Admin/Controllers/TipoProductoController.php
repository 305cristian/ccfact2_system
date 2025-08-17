<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of TipoProductoController
 *
  /**
 * @author CRISTIAN PAZ
 * @date 15 abr. 2024
 * @time 11:49:30
 */
class TipoProductoController extends \App\Controllers\BaseController {

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
        $send['view'] = view($this->dirViewModule . '\tipoproducto\viewTipoProducto');
        $send['user'] = $this->user;
        $send['ccm'] = $this->ccm;
        return $this->response->setJSON($send);
    }

    public function saveTipoProducto() {

        $tpNombre = $this->request->getPost('tpNombre');
        $tpDescripcion = $this->request->getPost('tpDescripcion');
        $tpEstado = $this->request->getPost('tpEstado');

        $this->validation->setRules([
            'tpNombre' => ['label' => 'Nombre Tipo Producto', 'rules' => 'trim|required'],
            'tpDescripcion' => ['label' => 'Descripcion Tipo Producto', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_tipo_producto', ['tp_nombre' => $tpNombre]);
            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un tipo de producto registrado con el nombre ' . $tpNombre . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'tp_nombre' => mb_strtoupper($tpNombre, 'UTF-8'),
                'tp_descripcion' => mb_strtoupper($tpDescripcion, 'UTF-8'),
                'tp_estado' => $tpEstado,
                'tp_fecha_creacion' => date('Y-m-d')
            ];

            $tpSave = $this->ccm->guardar($datos, 'cc_tipo_producto');
            $this->logs->logSuccess('SE HA CREADO UN TIPO DE PRODUCTO CON EL ID ' . $tpSave);
            $response['status'] = 'success';
            $response['msg'] = '<h5>Tipo de producto registrado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'tpNombre' => $this->validation->getError('tpNombre'),
                'tpDescripcion' => $this->validation->getError('tpDescripcion'),
            ];
        }
        return $this->response->setJson($response);
    }

    public function updateTipoProducto() {
        $tpNombre = $this->request->getPost('tpNombre');
        $tpDescripcion = $this->request->getPost('tpDescripcion');
        $tpEstado = $this->request->getPost('tpEstado');

        $idTP = $this->request->getPost('idTP');
        $nameAux = $this->request->getPost('nameAux');

        $this->validation->setRules([
            'tpNombre' => ['label' => 'Nombre Tipo Producto', 'rules' => 'trim|required'],
            'tpDescripcion' => ['label' => 'Descripcion Tipo Producto', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_tipo_producto', ['tp_nombre' => $tpNombre], '*', $orderBy = null, 1);

            if ($existe && $existe->tp_nombre != $nameAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un tipo de producto registrado con el nombre ' . $tpNombre . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'tp_nombre' => mb_strtoupper($tpNombre, 'UTF-8'),
                'tp_descripcion' => mb_strtoupper($tpDescripcion, 'UTF-8'),
                'tp_estado' => $tpEstado,
                'tp_fecha_creacion' => date('Y-m-d')
            ];

            $this->ccm->actualizar('cc_tipo_producto', $datos, ['id' => $idTP]);
            $this->logs->logSuccess('SE HA ACTUALIZADO UN TIPO DE PRODUCTO CON EL ID ' . $idTP);
            $response['status'] = 'success';
            $response['msg'] = '<h5>Tipo de producto registrado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'tpNombre' => $this->validation->getError('tpNombre'),
                'tpDescripcion' => $this->validation->getError('tpDescripcion'),
            ];
        }
        return $this->response->setJson($response);
    }

    public function getTiposProducto() {
        $response = $this->ccm->getData('cc_tipo_producto');
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }
}
