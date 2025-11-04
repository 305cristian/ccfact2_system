<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of SectoresController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 7 oct 2025
 * @time 1:34:49 p.m.
 */
use Modules\Admin\Models\ProveedorModel;

class SectoresController extends \App\Controllers\BaseController {

    //put your code here
    protected $provModel;
    protected $dirViewModule;

    public function __construct() {

        $this->dirViewModule = 'Modules\Admin\Views';

        $this->provModel = new ProveedorModel();
    }

    public function index() {
        $this->user->validateSession();
        $mod['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user; //Esto se envia a la vista para validar roles y permisos
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $mod);
        $send['view'] = view($this->dirViewModule . '\sectores\viewSectores', $data);
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function getSectores() {
        $response = $this->provModel->getSectores();
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }

    public function saveSector() {
        $secNombre = $this->request->getPost('secNombre');
        $secDescripcion = $this->request->getPost('secDescripcion');
        $fkAnillo = $this->request->getPost('fkAnillo');
        $secEstado = $this->request->getPost('secEstado');

        $this->validation->setRules([
            'secNombre' => ['label' => 'Nombre del Sector', 'rules' => 'trim|required'],
            'secDescripcion' => ['label' => 'Descripción', 'rules' => 'trim|required'],
            'fkAnillo' => ['label' => 'Anillo', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {
            // Validar nombre único
            $existe = $this->ccm->getData('cc_sectores', ['sec_nombre' => mb_strtoupper($secNombre, 'UTF-8')]);

            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un sector con el nombre "' . mb_strtoupper($secNombre, 'UTF-8') . '"</h5>';
                return $this->response->setJSON($response);
            }

            $datos = [
                'sec_nombre' => mb_strtoupper($secNombre, 'UTF-8'),
                'sec_descripcion' => $secDescripcion,
                'fk_anillo' => $fkAnillo,
                'sec_estado' => $secEstado,
            ];

            $save = $this->ccm->guardar($datos, 'cc_sectores');
            $this->logs->logSuccess('SE HA REGISTRADO UN SECTOR CON EL ID ' . $save);

            $response['status'] = 'success';
            $response['msg'] = '<h5>Sector registrado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'secNombre' => $this->validation->getError('secNombre'),
                'secDescripcion' => $this->validation->getError('secDescripcion'),
                'fkAnillo' => $this->validation->getError('fkAnillo'),
            ];
        }

        return $this->response->setJSON($response);
    }

    public function updateSector() {
        $secNombre = $this->request->getPost('secNombre');
        $secDescripcion = $this->request->getPost('secDescripcion');
        $fkAnillo = $this->request->getPost('fkAnillo');
        $secEstado = $this->request->getPost('secEstado');

        $idSector = $this->request->getPost('idSector');
        $nombreAux = $this->request->getPost('nombreAux');

        $this->validation->setRules([
            'secNombre' => ['label' => 'Nombre del Sector', 'rules' => 'trim|required'],
            'secDescripcion' => ['label' => 'Descripción', 'rules' => 'trim|required'],
            'fkAnillo' => ['label' => 'Anillo', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            // Validar nombre único solo si cambió el nombre
            $existe = $this->ccm->getData('cc_sectores', ['sec_nombre' => $secNombre], '*', null, 1);

            if ($existe && $existe->sec_nombre !== $nombreAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un sector con el nombre "' . $secNombre . '"</h5>';
                return $this->response->setJSON($response);
            }


            $datos = [
                'sec_nombre' => mb_strtoupper($secNombre, 'UTF-8'),
                'sec_descripcion' => $secDescripcion,
                'fk_anillo' => $fkAnillo,
                'sec_estado' => $secEstado,
            ];

            $this->ccm->actualizar('cc_sectores', $datos, ['id' => $idSector]);
            $this->logs->logSuccess('SE HA ACTUALIZADO EL SECTOR CON EL ID ' . $idSector);

            $response['status'] = 'success';
            $response['msg'] = '<h5>Sector actualizado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'secNombre' => $this->validation->getError('secNombre'),
                'secDescripcion' => $this->validation->getError('secDescripcion'),
                'fkAnillo' => $this->validation->getError('fkAnillo'),
            ];
        }

        return $this->response->setJSON($response);
    }
}
