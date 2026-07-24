<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\BioComedor\Controllers;

use App\Controllers\BaseController;

/**
 * Description of ComedoresController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 23 jul 2026
 * @time 10:45:00 p.m.
 */
class ComedoresController extends BaseController {
    //put your code here

    protected string $dirViewModule;

    public function __construct() {

        $this->dirViewModule = 'Modules\BioComedor\Views';
    }

    public function index() {

        $this->user->validateSession();

        $data['title'] = "Comedores";
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;

        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewComedores', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
    }

    public function getComedores() {

        $this->user->validateSession();
        $response = $this->ccm->getData('cc_bio_comedores');

        return $this->response->setJSON($response ?: false);
    }

    public function saveComedor() {

        $this->user->validateSession();

        $comCodigo = trim((string) $this->request->getPost('comCodigo'));
        $comNombre = trim((string) $this->request->getPost('comNombre'));
        $comUbicacion = trim((string) $this->request->getPost('comUbicacion'));
        $comDescripcion = trim((string) $this->request->getPost('comDescripcion'));
        $comEstado = $this->request->getPost('comEstado') ?? 1;

        $this->validation->setRules([
            'comCodigo' => ['label' => 'Codigo', 'rules' => 'trim|required'],
            'comNombre' => ['label' => 'Nombre', 'rules' => 'trim|required'],
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                        'status' => 'vacio',
                        'msg' => [
                            'comCodigo' => $this->validation->getError('comCodigo'),
                            'comNombre' => $this->validation->getError('comNombre'),
                        ],
            ]);
        }

        $codigoNormalizado = mb_strtoupper($comCodigo, 'UTF-8');
        $existeCodigo = $this->ccm->getData('cc_bio_comedores', ['com_codigo' => $codigoNormalizado], 'id', null, 1);

        if ($existeCodigo) {
            return $this->response->setJSON([
                        'status' => 'existe',
                        'msg' => '<h5>Ya existe un comedor registrado con el codigo ' . $comCodigo . '</h5>',
            ]);
        }

        $datos = [
            'com_codigo' => $codigoNormalizado,
            'com_nombre' => mb_strtoupper($comNombre, 'UTF-8'),
            'com_ubicacion' => $comUbicacion !== '' ? mb_strtoupper($comUbicacion, 'UTF-8') : null,
            'com_descripcion' => $comDescripcion !== '' ? $comDescripcion : null,
            'com_estado' => $comEstado,
        ];

        $comedorId = $this->ccm->guardar($datos, 'cc_bio_comedores');
        $this->logs->logSuccess('SE HA CREADO UN COMEDOR CON EL ID ' . $comedorId);

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => '<h5>Comedor registrado exitosamente</h5>',
        ]);
    }

    public function updateComedor() {

        $this->user->validateSession();

        $idComedor = $this->request->getPost('idComedor');
        $comCodigo = trim((string) $this->request->getPost('comCodigo'));
        $comNombre = trim((string) $this->request->getPost('comNombre'));
        $comUbicacion = trim((string) $this->request->getPost('comUbicacion'));
        $comDescripcion = trim((string) $this->request->getPost('comDescripcion'));
        $comEstado = $this->request->getPost('comEstado') ?? 1;

        $this->validation->setRules([
            'comCodigo' => ['label' => 'Codigo', 'rules' => 'trim|required'],
            'comNombre' => ['label' => 'Nombre', 'rules' => 'trim|required'],
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                        'status' => 'vacio',
                        'msg' => [
                            'comCodigo' => $this->validation->getError('comCodigo'),
                            'comNombre' => $this->validation->getError('comNombre'),
                        ],
            ]);
        }

        $codigoNormalizado = mb_strtoupper($comCodigo, 'UTF-8');
        $existeCodigo = $this->ccm->getData('cc_bio_comedores', ['com_codigo' => $codigoNormalizado], 'id, com_codigo', null, 1);

        if ($existeCodigo && (int) $existeCodigo->id !== (int) $idComedor) {
            return $this->response->setJSON([
                        'status' => 'existe',
                        'msg' => '<h5>Ya existe un comedor registrado con el codigo ' . $comCodigo . '</h5>',
            ]);
        }

        $datos = [
            'com_codigo' => $codigoNormalizado,
            'com_nombre' => mb_strtoupper($comNombre, 'UTF-8'),
            'com_ubicacion' => $comUbicacion !== '' ? mb_strtoupper($comUbicacion, 'UTF-8') : null,
            'com_descripcion' => $comDescripcion !== '' ? $comDescripcion : null,
            'com_estado' => $comEstado,
        ];

        $this->ccm->actualizar('cc_bio_comedores', $datos, ['id' => $idComedor]);

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => '<h5>Comedor actualizado exitosamente</h5>',
        ]);
    }
}
