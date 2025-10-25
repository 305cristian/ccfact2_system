<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of SustentosController
 *
  /**
 * @author CRISTIAN PAZ
 * @date 10 abr. 2024
 * @time 14:57:04
 */
class SustentosController extends \App\Controllers\BaseController {

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
        $send['view'] = view($this->dirViewModule . '\sustentos\viewSustentos');
        return $this->response->setJSON($send);
    }

    public function getSustentos() {
        $response = $this->ccm->getData('cc_sustentos');
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }

    public function saveSustento() {
        $sustentoCodigo = $this->request->getPost('sustentoCodigo');
        $sustentoNombre = $this->request->getPost('sustentoNombre');
        $sustentoTipoComprobante = $this->request->getPost('sustentoTipoComprobante');
        $sustentoEstado = $this->request->getPost('sustentoEstado');

        $this->validation->setRules([
            'sustentoCodigo' => ['label' => 'Código Sustento', 'rules' => 'trim|required'],
            'sustentoNombre' => ['label' => 'Nombre Sustento', 'rules' => 'trim|required'],
            'sustentoTipoComprobante' => ['label' => 'Tipo comprobante Sustento 01,02,03', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_sustentos', ['sus_nombre' => $sustentoNombre]);
            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe una sustento registrado con el nombre ' . $sustentoNombre . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'sus_codigo' => $sustentoCodigo,
                'sus_nombre' => mb_strtoupper($sustentoNombre, 'UTF-8'),
                'sus_tipo_comprobante' => $sustentoTipoComprobante,
                'sus_estado' => $sustentoEstado
            ];

            $susSave = $this->ccm->guardar($datos, 'cc_sustentos');
            $this->logs->logSuccess('SE HA CREADO UN SUSTENTO CON EL ID ' . $susSave);
            $response['status'] = 'success';
            $response['msg'] = '<h5>Sustento registrado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'sustentoCodigo' => $this->validation->getError('sustentoCodigo'),
                'sustentoNombre' => $this->validation->getError('sustentoNombre'),
                'sustentoTipoComprobante' => $this->validation->getError('sustentoTipoComprobante'),
            ];
        }
        return $this->response->setJson($response);
    }

    public function updateSustento() {
        $sustentoCodigo = $this->request->getPost('sustentoCodigo');
        $sustentoNombre = $this->request->getPost('sustentoNombre');
        $sustentoTipoComprobante = $this->request->getPost('sustentoTipoComprobante');
        $sustentoEstado = $this->request->getPost('sustentoEstado');

        $idSus = $this->request->getPost('idSus');
        $nameAux = $this->request->getPost('nameAux');

        $this->validation->setRules([
            'sustentoCodigo' => ['label' => 'Código Sustento', 'rules' => 'trim|required'],
            'sustentoNombre' => ['label' => 'Nombre Sustento', 'rules' => 'trim|required'],
            'sustentoTipoComprobante' => ['label' => 'Tipo comprobante Sustento 01,02,03', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_sustentos', ['sus_nombre' => $sustentoNombre], '*', $orderBy = null, 1);
            if ($existe && $existe->sus_nombre != $nameAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe una sustento registrado con el nombre ' . $sustentoNombre . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'sus_codigo' => $sustentoCodigo,
                'sus_nombre' => mb_strtoupper($sustentoNombre, 'UTF-8'),
                'sus_tipo_comprobante' => $sustentoTipoComprobante,
                'sus_estado' => $sustentoEstado
            ];

            $this->ccm->actualizar('cc_sustentos', $datos, ['id' => $idSus]);
            $response['status'] = 'success';
            $response['msg'] = '<h5>Sustento actualizado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'sustentoCodigo' => $this->validation->getError('sustentoCodigo'),
                'sustentoNombre' => $this->validation->getError('sustentoNombre'),
                'sustentoTipoComprobante' => $this->validation->getError('sustentoTipoComprobante'),
            ];
        }
        return $this->response->setJson($response);
    }
}
