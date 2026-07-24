<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\BioComedor\Controllers;

use App\Controllers\BaseController;

/**
 * Description of ServiciosController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 24 jul 2026
 * @time 1:00:15 a.m.
 */
class ServiciosController extends BaseController {
    //put your code here

    protected string $dirViewModule;

    public function __construct() {

        $this->dirViewModule = 'Modules\BioComedor\Views';
    }

    public function index() {

        $this->user->validateSession();

        $data['title'] = "Servicios";
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;

        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewServicios', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
    }

    public function getServicios() {

        $this->user->validateSession();
        $response = $this->ccm->getData('cc_bio_servicios');

        return $this->response->setJSON($response ?: false);
    }

    public function saveServicio() {

        $this->user->validateSession();
        $dataServicio = $this->getDataServicioPost();

        $validacion = $this->validarDatosServicio();
        if ($validacion !== null) {
            return $this->response->setJSON($validacion);
        }

        $existeCodigo = $this->ccm->getData('cc_bio_servicios', ['serv_codigo' => $dataServicio['serv_codigo']], 'id', null, 1);

        if ($existeCodigo) {
            return $this->response->setJSON([
                        'status' => 'existe',
                        'msg' => '<h5>Ya existe un servicio registrado con el codigo ' . $dataServicio['serv_codigo'] . '</h5>',
            ]);
        }

        $servicioId = $this->ccm->guardar($dataServicio, 'cc_bio_servicios');
        $this->logs->logSuccess('SE HA CREADO UN SERVICIO BIO COMEDOR CON EL ID ' . $servicioId);

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => '<h5>Servicio registrado exitosamente</h5>',
        ]);
    }

    public function updateServicio() {

        $this->user->validateSession();

        $idServicio = $this->request->getPost('idServicio');
        $dataServicio = $this->getDataServicioPost();

        $validacion = $this->validarDatosServicio();
        if ($validacion !== null) {
            return $this->response->setJSON($validacion);
        }

        $existeCodigo = $this->ccm->getData('cc_bio_servicios', ['serv_codigo' => $dataServicio['serv_codigo']], 'id', null, 1);

        if ($existeCodigo && (int) $existeCodigo->id !== (int) $idServicio) {
            return $this->response->setJSON([
                        'status' => 'existe',
                        'msg' => '<h5>Ya existe un servicio registrado con el codigo ' . $dataServicio['serv_codigo'] . '</h5>',
            ]);
        }

        $this->ccm->actualizar('cc_bio_servicios', $dataServicio, ['id' => $idServicio]);

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => '<h5>Servicio actualizado exitosamente</h5>',
        ]);
    }

    private function validarDatosServicio(): ?array {

        $this->validation->setRules([
            'servCodigo' => ['label' => 'Codigo', 'rules' => 'trim|required'],
            'servNombre' => ['label' => 'Nombre', 'rules' => 'trim|required'],
            'servOrden' => ['label' => 'Orden', 'rules' => 'trim|required|is_natural_no_zero'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {
            return null;
        }

        return [
            'status' => 'vacio',
            'msg' => [
                'servCodigo' => $this->validation->getError('servCodigo'),
                'servNombre' => $this->validation->getError('servNombre'),
                'servOrden' => $this->validation->getError('servOrden'),
            ],
        ];
    }

    private function getDataServicioPost(): array {

        $servCodigo = trim((string) $this->request->getPost('servCodigo'));
        $servNombre = trim((string) $this->request->getPost('servNombre'));
        $servDescripcion = trim((string) $this->request->getPost('servDescripcion'));

        return [
            'serv_codigo' => mb_strtoupper($servCodigo, 'UTF-8'),
            'serv_nombre' => mb_strtoupper($servNombre, 'UTF-8'),
            'serv_descripcion' => $servDescripcion !== '' ? $servDescripcion : null,
            'serv_orden' => $this->request->getPost('servOrden') ?: 1,
            'serv_estado' => $this->request->getPost('servEstado') ?? 1,
        ];
    }
}
