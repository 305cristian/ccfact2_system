<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of MarcasController
 *
  /**
 * @author CRISTIAN PAZ
 * @date 15 abr. 2024
 * @time 11:46:43
 */
class MarcasController extends \App\Controllers\BaseController {

    protected $dirViewModule;

    public function __construct() {
        $this->dirViewModule = 'Modules\Admin\Views';
    }

    public function index() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\marcas\viewMarcas');
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function saveMarca() {
        $mrcNombre = $this->request->getPost('mrcNombre');
        $mrcEstado = $this->request->getPost('mrcEstado');

        $this->validation->setRules([
            'mrcNombre' => ['label' => 'Nombre Marca', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_marcas', ['mrc_nombre' => $mrcNombre]);
            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe una marca registrado con el nombre ' . $mrcNombre . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'mrc_nombre' => mb_strtoupper($mrcNombre, 'UTF-8'),
                'mrc_estado' => $mrcEstado,
                'mrc_fecha_creacion' => date('Y-m-d')
            ];

            $mrcSave = $this->ccm->guardar($datos, 'cc_marcas');
            $this->logs->logSuccess('SE HA CREADO UNA MARCA CON EL ID ' . $mrcSave);
            $response['status'] = 'success';
            $response['msg'] = '<h5>Marca registrado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'mrcNombre' => $this->validation->getError('mrcNombre'),
            ];
        }
        return $this->response->setJson($response);
    }

    public function updateMarca() {
        $mrcNombre = $this->request->getPost('mrcNombre');
        $mrcEstado = $this->request->getPost('mrcEstado');

        $idMarca = $this->request->getPost('idMarca');
        $nameAux = $this->request->getPost('nameAux');

        $this->validation->setRules([
            'mrcNombre' => ['label' => 'Nombre Marca', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_marcas', ['mrc_nombre' => $mrcNombre], '*', $orderBy = null, 1);

            if ($existe && $existe->mrc_nombre != $nameAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe una marca registrado con el nombre ' . $mrcNombre . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'mrc_nombre' => mb_strtoupper($mrcNombre, 'UTF-8'),
                'mrc_estado' => $mrcEstado,
                'mrc_fecha_creacion' => date('Y-m-d')
            ];

            $this->ccm->actualizar('cc_marcas', $datos, ['id' => $idMarca]);
            $this->logs->logSuccess('SE HA ACTUALIZADO UNA MARCA CON EL ID ' . $idMarca);
            $response['status'] = 'success';
            $response['msg'] = '<h5>Marca actualizada exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'mrcNombre' => $this->validation->getError('mrcNombre'),
            ];
        }
        return $this->response->setJson($response);
    }

    public function getMarcas() {
        $response = $this->ccm->getData('cc_marcas');
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }
}
