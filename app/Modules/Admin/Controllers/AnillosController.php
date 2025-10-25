<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of AnillosController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 7 oct 2025
 * @time 1:35:01 p.m.
 */
class AnillosController extends \App\Controllers\BaseController {

    //put your code here

    protected $dirViewModule;

    public function __construct() {
        $this->dirViewModule = 'Modules\Admin\Views';
    }

    public function index() {
        $this->user->validateSession();
        $mod['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user; //Esto se envia a la vista para validar roles y permisos
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $mod);
        $send['view'] = view($this->dirViewModule . '\anillos\viewAnillos', $data);
//        return view($this->dirTemplate . '\dashboard', $send);
        return $this->response->setJSON($send);
    }

    public function getAnillos() {
        $response = $this->ccm->getData('cc_anillo');
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }

    public function saveAnillo() {
        $anNombre = $this->request->getPost('anNombre');
        $anDescripcion = $this->request->getPost('anDescripcion');
        $anEstado = $this->request->getPost('anEstado');

        $this->validation->setRules([
            'anNombre' => ['label' => 'Nombre', 'rules' => 'trim|required'],
            'anDescripcion' => ['label' => 'Descripción', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_anillo', ['an_nombre' => $anNombre]);

            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un anillo registrado con el nombre ' . $anNombre . '</h5>';
                return $this->response->setJSON($response);
            }

            $datos = [
                'an_nombre' => mb_strtoupper($anNombre, 'UTF-8'),
                'an_descripcion' => $anDescripcion,
                'an_estado' => $anEstado,
            ];

            $save = $this->ccm->guardar($datos, 'cc_anillo');
            $this->logs->logSuccess('SE HA REGISTRADO UN ANILLO CON EL ID ' . $save);

            $response['status'] = 'success';
            $response['msg'] = '<h5>Anillo registrado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'anNombre' => $this->validation->getError('anNombre'),
                'anDescripcion' => $this->validation->getError('anDescripcion'),
            ];
        }

        return $this->response->setJSON($response);
    }

    public function updateAnillo() {
        $anNombre = $this->request->getPost('anNombre');
        $anDescripcion = $this->request->getPost('anDescripcion');
        $anEstado = $this->request->getPost('anEstado');
        $idAnillo = $this->request->getPost('idAnillo');
        $nameAux = $this->request->getPost('nameAux');

        $this->validation->setRules([
            'anNombre' => ['label' => 'Nombre', 'rules' => 'trim|required'],
            'anDescripcion' => ['label' => 'Descripción', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_anillo', ['an_nombre' => $anNombre], '*', $orderBy = null, 1);

            if ($existe && $existe->an_nombre != $nameAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un anillo registrado con el nombre ' . $anNombre . '</h5>';
                return $this->response->setJSON($response);
            }


            $datos = [
                'an_nombre' => mb_strtoupper($anNombre, 'UTF-8'),
                'an_descripcion' => $anDescripcion,
                'an_estado' => $anEstado,
            ];

            $this->ccm->actualizar('cc_anillo', $datos, ['id' => $idAnillo]);
            $this->logs->logSuccess('SE HA ACTUALIZADO EL ANILLO CON EL ID ' . $idAnillo);

            $response['status'] = 'success';
            $response['msg'] = '<h5>Anillo actualizado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'anNombre' => $this->validation->getError('anNombre'),
                'anDescripcion' => $this->validation->getError('anDescripcion'),
            ];
        }

        return $this->response->setJSON($response);
    }
}
