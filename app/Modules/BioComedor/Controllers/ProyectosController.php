<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\BioComedor\Controllers;

use App\Controllers\BaseController;

/**
 * Description of ProyectosController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 24 jul 2026
 * @time 12:35:27 a.m.
 */
class ProyectosController extends BaseController {
    //put your code here

    protected string $dirViewModule;

    public function __construct() {

        $this->dirViewModule = 'Modules\BioComedor\Views';
    }

    public function index() {

        $this->user->validateSession();

        $data['title'] = "Proyectos";
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;

        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewProyectos', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
    }

    public function getProyectos() {

        $this->user->validateSession();
        $response = $this->ccm->getData('cc_bio_proyectos');

        return $this->response->setJSON($response ?: false);
    }

    public function saveProyecto() {

        $this->user->validateSession();
        $dataProyecto = $this->getDataProyectoPost();

        $validacion = $this->validarDatosProyecto();
        if ($validacion !== null) {
            return $this->response->setJSON($validacion);
        }

        $existeCodigo = $this->ccm->getData('cc_bio_proyectos', ['proy_codigo' => $dataProyecto['proy_codigo']], 'id', null, 1);

        if ($existeCodigo) {
            return $this->response->setJSON([
                        'status' => 'existe',
                        'msg' => '<h5>Ya existe un proyecto registrado con el codigo ' . $dataProyecto['proy_codigo'] . '</h5>',
            ]);
        }

        $proyectoId = $this->ccm->guardar($dataProyecto, 'cc_bio_proyectos');
        $this->logs->logSuccess('SE HA CREADO UN PROYECTO BIO COMEDOR CON EL ID ' . $proyectoId);

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => '<h5>Proyecto registrado exitosamente</h5>',
        ]);
    }

    public function updateProyecto() {

        $this->user->validateSession();

        $idProyecto = $this->request->getPost('idProyecto');
        $dataProyecto = $this->getDataProyectoPost();

        $validacion = $this->validarDatosProyecto();
        if ($validacion !== null) {
            return $this->response->setJSON($validacion);
        }

        $existeCodigo = $this->ccm->getData('cc_bio_proyectos', ['proy_codigo' => $dataProyecto['proy_codigo']], 'id', null, 1);

        if ($existeCodigo && (int) $existeCodigo->id !== (int) $idProyecto) {
            return $this->response->setJSON([
                        'status' => 'existe',
                        'msg' => '<h5>Ya existe un proyecto registrado con el codigo ' . $dataProyecto['proy_codigo'] . '</h5>',
            ]);
        }

        $this->ccm->actualizar('cc_bio_proyectos', $dataProyecto, ['id' => $idProyecto]);

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => '<h5>Proyecto actualizado exitosamente</h5>',
        ]);
    }

    private function validarDatosProyecto(): ?array {

        $this->validation->setRules([
            'proyCodigo' => ['label' => 'Codigo', 'rules' => 'trim|required'],
            'proyNombre' => ['label' => 'Nombre', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {
            return null;
        }

        return [
            'status' => 'vacio',
            'msg' => [
                'proyCodigo' => $this->validation->getError('proyCodigo'),
                'proyNombre' => $this->validation->getError('proyNombre'),
            ],
        ];
    }

    private function getDataProyectoPost(): array {

        $proyCodigo = trim((string) $this->request->getPost('proyCodigo'));
        $proyNombre = trim((string) $this->request->getPost('proyNombre'));
        $proyDescripcion = trim((string) $this->request->getPost('proyDescripcion'));

        return [
            'proy_codigo' => mb_strtoupper($proyCodigo, 'UTF-8'),
            'proy_nombre' => mb_strtoupper($proyNombre, 'UTF-8'),
            'proy_descripcion' => $proyDescripcion !== '' ? $proyDescripcion : null,
            'proy_estado' => $this->request->getPost('proyEstado') ?? 1,
        ];
    }
}
