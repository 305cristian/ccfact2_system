<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\BioComedor\Controllers;

use App\Controllers\BaseController;
use Modules\BioComedor\Models\BioModel;

/**
 * Description of EquiposController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 23 jul 2026
 * @time 11:00:05 p.m.
 */
class EquiposController extends BaseController {
    //put your code here

    protected string $dirViewModule;
    protected BioModel $bioModel;

    public function __construct() {

        $this->dirViewModule = 'Modules\BioComedor\Views';
        $this->bioModel = new BioModel();
    }

    public function index() {

        $this->user->validateSession();

        $data['title'] = "Equipos";
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;
        $data['listaComedores'] = $this->ccm->getData('cc_bio_comedores', ['com_estado' => 1], 'id, com_codigo, com_nombre');

        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewEquipos', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
    }

    public function getEquipos() {

        $this->user->validateSession();
        $response = $this->bioModel->getListaEquipos();

        return $this->response->setJSON($response ?: false);
    }

    public function saveEquipo() {

        $this->user->validateSession();
        $dataEquipo = $this->getDataEquipoPost();

        $this->validation->setRules([
            'fkComedor' => ['label' => 'Comedor', 'rules' => 'trim|required'],
            'eqCodigo' => ['label' => 'Codigo', 'rules' => 'trim|required'],
            'eqNombre' => ['label' => 'Nombre', 'rules' => 'trim|required'],
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                        'status' => 'vacio',
                        'msg' => [
                            'fkComedor' => $this->validation->getError('fkComedor'),
                            'eqCodigo' => $this->validation->getError('eqCodigo'),
                            'eqNombre' => $this->validation->getError('eqNombre'),
                        ],
            ]);
        }

        $existeCodigo = $this->ccm->getData('cc_bio_equipos', ['eq_codigo' => $dataEquipo['eq_codigo']], 'id', null, 1);

        if ($existeCodigo) {
            return $this->response->setJSON([
                        'status' => 'existe',
                        'msg' => '<h5>Ya existe un equipo registrado con el codigo ' . $dataEquipo['eq_codigo'] . '</h5>',
            ]);
        }

        $equipoId = $this->ccm->guardar($dataEquipo, 'cc_bio_equipos');
        $this->logs->logSuccess('SE HA CREADO UN EQUIPO BIOMETRICO CON EL ID ' . $equipoId);

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => '<h5>Equipo registrado exitosamente</h5>',
        ]);
    }

    public function updateEquipo() {

        $this->user->validateSession();

        $idEquipo = $this->request->getPost('idEquipo');
        $dataEquipo = $this->getDataEquipoPost();

        $this->validation->setRules([
            'fkComedor' => ['label' => 'Comedor', 'rules' => 'trim|required'],
            'eqCodigo' => ['label' => 'Codigo', 'rules' => 'trim|required'],
            'eqNombre' => ['label' => 'Nombre', 'rules' => 'trim|required'],
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                        'status' => 'vacio',
                        'msg' => [
                            'fkComedor' => $this->validation->getError('fkComedor'),
                            'eqCodigo' => $this->validation->getError('eqCodigo'),
                            'eqNombre' => $this->validation->getError('eqNombre'),
                        ],
            ]);
        }

        $existeCodigo = $this->ccm->getData('cc_bio_equipos', ['eq_codigo' => $dataEquipo['eq_codigo']], 'id, eq_codigo', null, 1);

        if ($existeCodigo && (int) $existeCodigo->id !== (int) $idEquipo) {
            return $this->response->setJSON([
                        'status' => 'existe',
                        'msg' => '<h5>Ya existe un equipo registrado con el codigo ' . $dataEquipo['eq_codigo'] . '</h5>',
            ]);
        }

        $this->ccm->actualizar('cc_bio_equipos', $dataEquipo, ['id' => $idEquipo]);

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => '<h5>Equipo actualizado exitosamente</h5>',
        ]);
    }

    private function getDataEquipoPost(): array {

        $eqCodigo = trim((string) $this->request->getPost('eqCodigo'));
        $eqNombre = trim((string) $this->request->getPost('eqNombre'));
        $eqMarca = trim((string) $this->request->getPost('eqMarca'));
        $eqModelo = trim((string) $this->request->getPost('eqModelo'));
        $eqIp = trim((string) $this->request->getPost('eqIp'));
        $eqPuerto = trim((string) $this->request->getPost('eqPuerto'));
        $eqUbicacion = trim((string) $this->request->getPost('eqUbicacion'));

        return [
            'fk_comedor' => $this->request->getPost('fkComedor'),
            'eq_codigo' => mb_strtoupper($eqCodigo, 'UTF-8'),
            'eq_nombre' => mb_strtoupper($eqNombre, 'UTF-8'),
            'eq_marca' => $eqMarca !== '' ? mb_strtoupper($eqMarca, 'UTF-8') : null,
            'eq_modelo' => $eqModelo !== '' ? mb_strtoupper($eqModelo, 'UTF-8') : null,
            'eq_ip' => $eqIp !== '' ? $eqIp : null,
            'eq_puerto' => $eqPuerto !== '' ? $eqPuerto : null,
            'eq_ubicacion' => $eqUbicacion !== '' ? mb_strtoupper($eqUbicacion, 'UTF-8') : null,
            'eq_estado' => $this->request->getPost('eqEstado') ?? 1,
        ];
    }

}
