<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of UnidadMedidaController
 *
  /**
 * @author CRISTIAN PAZ
 * @date 15 abr. 2024
 * @time 11:23:03
 */
class UnidadMedidaController extends \App\Controllers\BaseController {

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
        $send['view'] = view($this->dirViewModule . '\unidadesmedida\viewUnidades');
        $send['user'] = $this->user;
        $send['ccm'] = $this->ccm;
        return $this->response->setJSON($send);
    }

    public function getUnidadesMedida() {
        $response = $this->ccm->getData('cc_unidades_medida');
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }

    public function saveUnidadMedida() {
        $unNombre = $this->request->getPost('unNombre');
        $unNombreCorto = $this->request->getPost('unNombreCorto');
        $umEstado = $this->request->getPost('umEstado');

        $this->validation->setRules([
            'unNombre' => ['label' => 'Nombre Unidad medida', 'rules' => 'trim|required'],
            'unNombreCorto' => ['label' => 'Abreviatura Unidad medida', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_unidades_medida', ['um_nombre' => $unNombre]);
            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe una unidad de medida registrado con el nombre ' . $unNombre . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'um_nombre' => mb_strtoupper($unNombre, 'UTF-8'),
                'um_nombre_corto' => mb_strtoupper($unNombreCorto, 'UTF-8'),
                'um_estado' => $umEstado,
                'um_fecha_creacion' => date('Y-m-d')
            ];

            $umSave = $this->ccm->guardar($datos, 'cc_unidades_medida');
            $this->logs->logSuccess('SE HA CREADO UNA UNIDAD DE MEDIDA CON EL ID ' . $umSave);
            $response['status'] = 'success';
            $response['msg'] = '<h5>Unidad de medida registrado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'unNombre' => $this->validation->getError('unNombre'),
                'unNombreCorto' => $this->validation->getError('unNombreCorto'),
            ];
        }
        return $this->response->setJson($response);
    }

    public function updateUnidadMedida() {
        $unNombre = $this->request->getPost('unNombre');
        $unNombreCorto = $this->request->getPost('unNombreCorto');
        $umEstado = $this->request->getPost('umEstado');

        $idUM = $this->request->getPost('idUM');
        $nameAux = $this->request->getPost('nameAux');

        $this->validation->setRules([
            'unNombre' => ['label' => 'Nombre Unidad medida', 'rules' => 'trim|required'],
            'unNombreCorto' => ['label' => 'Abreviatura Unidad medida', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_unidades_medida', ['um_nombre' => $unNombre], '*', $orderBy = null, 1);
            if ($existe && $existe->um_nombre != $nameAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe una unidad de medida registrado con el nombre ' . $unNombre . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'um_nombre' => mb_strtoupper($unNombre, 'UTF-8'),
                'um_nombre_corto' => mb_strtoupper($unNombreCorto, 'UTF-8'),
                'um_estado' => $umEstado,
                'um_fecha_creacion' => date('Y-m-d')
            ];

            $this->ccm->actualizar('cc_unidades_medida', $datos, ['id' => $idUM]);
            $this->logs->logSuccess('SE HA ACTUALIZADO UNA UNIDAD DE MEDIDA CON EL ID ' . $idUM);
            $response['status'] = 'success';
            $response['msg'] = '<h5>Unidad de medida actualizada exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'unNombre' => $this->validation->getError('unNombre'),
                'unNombreCorto' => $this->validation->getError('unNombreCorto'),
            ];
        }
        return $this->response->setJson($response);
    }
}
