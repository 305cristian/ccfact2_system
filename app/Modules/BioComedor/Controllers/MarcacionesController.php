<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\BioComedor\Controllers;

use App\Controllers\BaseController;
use Modules\BioComedor\Libraries\BioMarcacionesLib;
use Modules\BioComedor\Models\BioModel;

/**
 * Description of MarcacionesController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 24 jul 2026
 * @time 1:36:44 a.m.
 */
class MarcacionesController extends BaseController {

    //put your code here

    protected BioMarcacionesLib $bioMarcacionesLib;
    protected string $dirViewModule;
    protected BioModel $bioModel;

    public function __construct() {

        $this->dirViewModule = 'Modules\BioComedor\Views';
        $this->bioModel = new BioModel();

        $this->bioMarcacionesLib = new BioMarcacionesLib();
    }

    public function index() {

        $this->user->validateSession();

        $data['title'] = "Marcaciones";
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;
        $data['listaComedores'] = $this->ccm->getData('cc_bio_comedores', ['com_estado' => 1], 'id, com_codigo, com_nombre');
        $data['listaEquipos'] = $this->ccm->getData('cc_bio_equipos', ['eq_estado' => 1], 'id, fk_comedor, eq_codigo, eq_nombre');
        $data['listaServicios'] = $this->ccm->getData('cc_bio_servicios', ['serv_estado' => 1], 'id, serv_codigo, serv_nombre');

        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewMarcaciones', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
    }

    public function responseSetJSON(string $status, string $mensaje, mixed $data = null) {
        return $this->response->setJSON([
                    'status' => $status,
                    'msg' => $mensaje,
                    'data' => $data,
        ]);
    }

    public function getMarcaciones() {

        $this->user->validateSession();

        $filtros = [
            'fechaDesde' => $this->request->getPost('fechaDesde'),
            'fechaHasta' => $this->request->getPost('fechaHasta'),
            'fkComedor' => $this->request->getPost('fkComedor'),
            'fkServicio' => $this->request->getPost('fkServicio'),
            'marcEstado' => $this->request->getPost('marcEstado'),
            'marcRetraso' => $this->request->getPost('marcRetraso'),
            'texto' => $this->request->getPost('texto'),
        ];

        $response = $this->bioModel->getListaMarcaciones($filtros);

        return $this->response->setJSON($response ?: false);
    }

    public function registrarMarcacionManual() {

        $this->user->validateSession();

        $validacion = $this->validarDatosMarcacionManual();
        if ($validacion !== null) {
            return $this->response->setJSON($validacion);
        }

        $dataMarcacion = (object) [
                    'fkComedor' => $this->request->getPost('fkComedor'),
                    'fkEquipo' => $this->request->getPost('fkEquipo'),
                    'tipoIdentificacion' => $this->request->getPost('tipoIdentificacion'),
                    'identificador' => $this->request->getPost('identificador'),
                    'marcFecha' => $this->request->getPost('marcFecha'),
                    'marcHora' => $this->request->getPost('marcHora'),
                    'marcOrigen' => 'MANUAL',
        ];

        $respuesta = $this->bioMarcacionesLib->procesarMarcacion($dataMarcacion);
        return $this->response->setJSON($respuesta);
    }

    public function updateMarcacion() {

        $this->user->validateSession();

        $validacion = $this->validarDatosUpdateMarcacion();
        if ($validacion !== null) {
            return $this->response->setJSON($validacion);
        }

        $idMarcacion = (int) $this->request->getPost('idMarcacion');
        $marcacionActual = $this->ccm->getData('cc_bio_marcaciones', ['id' => $idMarcacion], '*', null, 1);

        if (!$marcacionActual) {
            return $this->responseSetJSON('warning', 'No se encontro la marcacion que intenta corregir.');
        }

        if ($marcacionActual->marc_estado === 'ANULADA') {
            return $this->responseSetJSON('warning', 'No se puede corregir una marcacion anulada.');
        }

        $comedorId = (int) $this->request->getPost('fkComedor');
        $equipoId = (int) $this->request->getPost('fkEquipo');
        $servicioId = (int) $this->request->getPost('fkServicio');
        $fecha = trim((string) $this->request->getPost('marcFecha'));
        $hora = trim((string) $this->request->getPost('marcHora'));
        $motivo = trim((string) $this->request->getPost('motivoCorreccion'));

        $equipo = $this->bioModel->getEquipoActivo($equipoId, $comedorId);
        if (!$equipo) {
            return $this->responseSetJSON('warning', 'El equipo no pertenece al comedor seleccionado o esta inactivo.');
        }

        $horario = $this->bioModel->getHorarioServicioPorServicioHora($servicioId, $hora);
        if (!$horario) {
            return $this->responseSetJSON('warning', 'El servicio seleccionado no tiene un horario activo que cubra la hora ingresada.');
        }

        $esRetraso = !$this->horaEstaDentroDelRango($horario->hor_hora_inicio, $hora, $horario->hor_hora_fin_normal) ? 1 : 0;
        $observacionAnterior = trim((string) ($marcacionActual->marc_observacion ?? ''));
        $observacionCorreccion = 'Correccion administrativa: ' . $motivo;

        $dataMarcacion = [
            'fk_comedor' => $comedorId,
            'fk_equipo' => $equipoId,
            'fk_servicio' => $servicioId,
            'marc_fecha' => $fecha,
            'marc_hora' => $hora,
            'marc_fecha_hora' => $fecha . ' ' . $hora,
            'marc_es_retraso' => $esRetraso,
            'marc_observacion' => $observacionAnterior !== '' ? $observacionAnterior . "\n" . $observacionCorreccion : $observacionCorreccion,
        ];

        $this->ccm->actualizar('cc_bio_marcaciones', $dataMarcacion, ['id' => $idMarcacion]);
        $this->logs->logSuccess('SE HA CORREGIDO LA MARCACION BIO COMEDOR CON EL ID ' . $idMarcacion);

        return $this->responseSetJSON('success', 'Marcacion corregida exitosamente.');
    }

    public function anularMarcacion() {

        $this->user->validateSession();

        $this->validation->setRules([
            'idMarcacion' => ['label' => 'Marcacion', 'rules' => 'trim|required'],
            'motivoAnulacion' => ['label' => 'Motivo anulacion', 'rules' => 'trim|required'],
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                        'status' => 'vacio',
                        'msg' => [
                            'motivoAnulacion' => $this->validation->getError('motivoAnulacion'),
                        ],
            ]);
        }

        $idMarcacion = (int) $this->request->getPost('idMarcacion');
        $motivo = trim((string) $this->request->getPost('motivoAnulacion'));
        $marcacionActual = $this->ccm->getData('cc_bio_marcaciones', ['id' => $idMarcacion], '*', null, 1);

        if (!$marcacionActual) {
            return $this->responseSetJSON('warning', 'No se encontro la marcacion que intenta anular.');
        }

        if ($marcacionActual->marc_estado === 'ANULADA') {
            return $this->responseSetJSON('warning', 'La marcacion ya se encuentra anulada.');
        }

        $observacionAnterior = trim((string) ($marcacionActual->marc_observacion ?? ''));
        $observacionAnulacion = 'Anulacion administrativa: ' . $motivo;

        $this->ccm->actualizar('cc_bio_marcaciones', [
            'marc_estado' => 'ANULADA',
            'marc_genera_consumo' => 0,
            'marc_observacion' => $observacionAnterior !== '' ? $observacionAnterior . "\n" . $observacionAnulacion : $observacionAnulacion,
                ], ['id' => $idMarcacion]);

        $this->logs->logSuccess('SE HA ANULADO LA MARCACION BIO COMEDOR CON EL ID ' . $idMarcacion);

        return $this->responseSetJSON('success', 'Marcacion anulada exitosamente.');
    }

    private function validarDatosMarcacionManual(): ?array {

        $this->validation->setRules([
            'fkComedor' => ['label' => 'Comedor', 'rules' => 'trim|required'],
            'fkEquipo' => ['label' => 'Equipo', 'rules' => 'trim|required'],
            'tipoIdentificacion' => ['label' => 'Tipo identificacion', 'rules' => 'trim|required'],
            'identificador' => ['label' => 'Identificador', 'rules' => 'trim|required'],
            'marcFecha' => ['label' => 'Fecha', 'rules' => 'trim|required'],
            'marcHora' => ['label' => 'Hora', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {
            return null;
        }

        return [
            'status' => 'vacio',
            'msg' => [
                'fkComedor' => $this->validation->getError('fkComedor'),
                'fkEquipo' => $this->validation->getError('fkEquipo'),
                'tipoIdentificacion' => $this->validation->getError('tipoIdentificacion'),
                'identificador' => $this->validation->getError('identificador'),
                'marcFecha' => $this->validation->getError('marcFecha'),
                'marcHora' => $this->validation->getError('marcHora'),
            ],
        ];
    }

    private function validarDatosUpdateMarcacion(): ?array {

        $this->validation->setRules([
            'idMarcacion' => ['label' => 'Marcacion', 'rules' => 'trim|required'],
            'fkComedor' => ['label' => 'Comedor', 'rules' => 'trim|required'],
            'fkEquipo' => ['label' => 'Equipo', 'rules' => 'trim|required'],
            'fkServicio' => ['label' => 'Servicio', 'rules' => 'trim|required'],
            'marcFecha' => ['label' => 'Fecha', 'rules' => 'trim|required'],
            'marcHora' => ['label' => 'Hora', 'rules' => 'trim|required'],
            'motivoCorreccion' => ['label' => 'Motivo correccion', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {
            return null;
        }

        return [
            'status' => 'vacio',
            'msg' => [
                'fkComedor' => $this->validation->getError('fkComedor'),
                'fkEquipo' => $this->validation->getError('fkEquipo'),
                'fkServicio' => $this->validation->getError('fkServicio'),
                'marcFecha' => $this->validation->getError('marcFecha'),
                'marcHora' => $this->validation->getError('marcHora'),
                'motivoCorreccion' => $this->validation->getError('motivoCorreccion'),
            ],
        ];
    }

    private function horaEstaDentroDelRango(string $horaInicio, string $horaEvaluar, string $horaFin): bool {

        if ($horaInicio < $horaFin) {
            return $horaEvaluar >= $horaInicio && $horaEvaluar <= $horaFin;
        }

        return $horaEvaluar >= $horaInicio || $horaEvaluar <= $horaFin;
    }
}
