<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of SettingsTransController
 *
  /**
 * @author CRISTIAN PAZ
 * @date 21 mar. 2024
 * @time 15:24:21
 */
class TransaccionController extends \App\Controllers\BaseController {

    protected $dirViewModule;

//    protected $admModel;

    public function __construct() {
        $this->dirViewModule = 'Modules\Admin\Views';
//        $this->admModel = new AdminModel();
    }

    public function index() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\settingtransacciones\viewTransacciones', $data);

        return $this->response->setJSON($send);
//        return view($this->dirTemplate . '\dashboard', $send);
    }

    public function getTransacciones() {
        $response = $this->ccm->getData('cc_transacciones');
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }

    public function saveTransaccion() {
        $nombreTrans = $this->request->getPost('nombreTrans');
        $codigoTrans = $this->request->getPost('codigoTrans');
        $descripcionTrans = $this->request->getPost('descripcionTrans');

        $this->validation->setRules([
            'nombreTrans' => ['label' => 'Nombre Transacción', 'rules' => 'trim|required'],
            'codigoTrans' => ['label' => 'Código Transacción', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->get('cc_transacciones', ['tr_nombre' => $nombreTrans]);
            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un tipo de transaccion registrado con estos datos, por favor revise el nombre ' . $nombreTrans . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'tr_nombre' => mb_strtoupper($nombreTrans, 'UTF-8'),
                'tr_codigo' => $codigoTrans,
                'tr_descripcion' => $descripcionTrans
            ];

            $this->ccm->guardar($datos, 'cc_transacciones');
            $response['status'] = 'success';
            $response['msg'] = '<h5>Tipo de Transaccion registrado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'nombreTrans' => $this->validation->getError('nombreTrans'),
                'codigoTrans' => $this->validation->getError('codigoTrans'),
            ];
        }
        return $this->response->setJson($response);
    }

    public function updateTransaccion() {
        $nombreTrans = $this->request->getPost('nombreTrans');
        $codigoTrans = $this->request->getPost('codigoTrans');
        $descripcionTrans = $this->request->getPost('descripcionTrans');

        $idTrans = $this->request->getPost('idTrans');
        $nameAux = $this->request->getPost('nameAux');

        $this->validation->setRules([
            'nombreTrans' => ['label' => 'Nombre Transacción', 'rules' => 'trim|required'],
            'codigoTrans' => ['label' => 'Código Transacción', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_transacciones', ['tr_nombre' => $nombreTrans], '*', $orderBy = null, 1);

            if ($existe && $existe->tr_nombre != $nameAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un tipo de transaccion registrado con estos datos, por favor revise el nombre ' . $nombreTrans . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'tr_nombre' => mb_strtoupper($nombreTrans, 'UTF-8'),
                'tr_codigo' => $codigoTrans,
                'tr_descripcion' => $descripcionTrans
            ];

            $this->ccm->actualizar('cc_transacciones', $datos, ['id' => $idTrans]);
            $response['status'] = 'success';
            $response['msg'] = '<h5>Tipo de Transaccion actualizada exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'nombreTrans' => $this->validation->getError('nombreTrans'),
                'codigoTrans' => $this->validation->getError('codigoTrans'),
            ];
        }
        return $this->response->setJson($response);
    }

    //put your code here
}
