<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of MotivosAjustesController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 7 oct 2025
 * @time 7:29:49 p.m.
 */
class MotivosAjustesController extends \App\Controllers\BaseController {

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
        $send['view'] = view($this->dirViewModule . '\motivosajuste\viewMotivosAjuste');
//        $send['user'] = $this->user;
//        $send['ccm'] = $this->ccm;
        return $this->response->setJSON($send);
        //return view($this->dirTemplate . '\dashboard', $send);
    }

    public function getMotivos() {
        $response = $this->ccm->getData('cc_motivos_ajuste', null, '*', ['id' => 'ASC']);
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }

    public function saveMotivo() {
        $motNombre = $this->request->getPost('motNombre');
        $motDetalle = $this->request->getPost('motDetalle');
        $motTipo = $this->request->getPost('motTipo');
        $motEstado = $this->request->getPost('motEstado');

        $this->validation->setRules([
            'motNombre' => ['label' => 'Nombre', 'rules' => 'trim|required'],
            'motDetalle' => ['label' => 'Detalle', 'rules' => 'trim|required'],
            'motTipo' => ['label' => 'Tipo', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {
            // Validar nombre único
            $existe = $this->ccm->getData('cc_motivos_ajuste', ['mot_nombre' => mb_strtoupper(trim($motNombre), 'UTF-8')]);

            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un motivo con el nombre "' . mb_strtoupper($motNombre, 'UTF-8') . '"</h5>';
                return $this->response->setJSON($response);
            }

            $datos = [
                'mot_nombre' => mb_strtoupper($motNombre, 'UTF-8'),
                'mot_detalle' => $motDetalle,
                'mot_tipo' => $motTipo,
                'mot_estado' => $motEstado,
            ];

            $save = $this->ccm->guardar($datos, 'cc_motivos_ajuste');
            $this->logs->logSuccess('SE HA REGISTRADO UN MOTIVO DE AJUSTE CON EL ID ' . $save);

            $response['status'] = 'success';
            $response['msg'] = '<h5>Motivo de ajuste registrado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'motNombre' => $this->validation->getError('motNombre'),
                'motDetalle' => $this->validation->getError('motDetalle'),
                'motTipo' => $this->validation->getError('motTipo'),
            ];
        }

        return $this->response->setJSON($response);
    }

    public function updateMotivo() {
        $motNombre = $this->request->getPost('motNombre');
        $motDetalle = $this->request->getPost('motDetalle');
        $motTipo = $this->request->getPost('motTipo');
        $motEstado = $this->request->getPost('motEstado');

        $idMotivo = $this->request->getPost('idMotivo');
        $nombreAux = $this->request->getPost('nombreAux');

        $this->validation->setRules([
            'motNombre' => ['label' => 'Nombre', 'rules' => 'trim|required'],
            'motDetalle' => ['label' => 'Detalle', 'rules' => 'trim|required'],
            'motTipo' => ['label' => 'Tipo', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {


            // Validar nombre único solo si cambió el nombre

            $existe = $this->ccm->getData('cc_motivos_ajuste', ['mot_nombre' =>  mb_strtoupper(trim($motNombre))], '*', null, 1);

            if ($existe && $existe->mot_nombre !== $nombreAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un motivo con el nombre "' . $motNombre . '"</h5>';
                return $this->response->setJSON($response);
            }


            $datos = [
                'mot_nombre' => mb_strtoupper($motNombre, 'UTF-8'),
                'mot_detalle' => $motDetalle,
                'mot_tipo' => $motTipo,
                'mot_estado' => $motEstado,
            ];

            $this->ccm->actualizar('cc_motivos_ajuste', $datos, ['id' => $idMotivo]);
            $this->logs->logSuccess('SE HA ACTUALIZADO EL MOTIVO DE AJUSTE CON EL ID ' . $idMotivo);

            $response['status'] = 'success';
            $response['msg'] = '<h5>Motivo de ajuste actualizado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'motNombre' => $this->validation->getError('motNombre'),
                'motDetalle' => $this->validation->getError('motDetalle'),
                'motTipo' => $this->validation->getError('motTipo'),
            ];
        }

        return $this->response->setJSON($response);
    }
}
