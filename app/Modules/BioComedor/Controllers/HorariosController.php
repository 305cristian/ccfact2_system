<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\BioComedor\Controllers;

use App\Controllers\BaseController;
use Modules\BioComedor\Models\BioModel;

/**
 * Description of HorariosController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 24 jul 2026
 * @time 1:08:35 a.m.
 */
class HorariosController extends BaseController {
    //put your code here

    protected string $dirViewModule;
    protected BioModel $bioModel;

    public function __construct() {

        $this->dirViewModule = 'Modules\BioComedor\Views';
        $this->bioModel = new BioModel();
    }

    public function index() {

        $this->user->validateSession();

        $data['title'] = "Horarios";
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;
        $data['listaServicios'] = $this->ccm->getData('cc_bio_servicios', ['serv_estado' => 1], 'id, serv_codigo, serv_nombre');

        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewHorarios', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
    }

    public function getHorarios() {

        $this->user->validateSession();
        $response = $this->bioModel->getListaHorarios();

        return $this->response->setJSON($response ?: false);
    }

    public function saveHorario() {

        $this->user->validateSession();
        $dataHorario = $this->getDataHorarioPost();

        $validacion = $this->validarDatosHorario();
        if ($validacion !== null) {
            return $this->response->setJSON($validacion);
        }

        $horarioId = $this->ccm->guardar($dataHorario, 'cc_bio_servicio_horarios');
        $this->logs->logSuccess('SE HA CREADO UN HORARIO DE SERVICIO BIO COMEDOR CON EL ID ' . $horarioId);

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => '<h5>Horario registrado exitosamente</h5>',
        ]);
    }

    public function updateHorario() {

        $this->user->validateSession();

        $idHorario = $this->request->getPost('idHorario');
        $dataHorario = $this->getDataHorarioPost();

        $validacion = $this->validarDatosHorario();
        if ($validacion !== null) {
            return $this->response->setJSON($validacion);
        }

        $this->ccm->actualizar('cc_bio_servicio_horarios', $dataHorario, ['id' => $idHorario]);

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => '<h5>Horario actualizado exitosamente</h5>',
        ]);
    }

    private function validarDatosHorario(): ?array {

        $this->validation->setRules([
            'fkServicio' => ['label' => 'Servicio', 'rules' => 'trim|required'],
            'horHoraInicio' => ['label' => 'Hora inicio', 'rules' => 'trim|required'],
            'horHoraFinNormal' => ['label' => 'Hora fin normal', 'rules' => 'trim|required'],
            'horHoraFin' => ['label' => 'Hora fin', 'rules' => 'trim|required'],
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
            return [
                'status' => 'vacio',
                'msg' => [
                    'fkServicio' => $this->validation->getError('fkServicio'),
                    'horHoraInicio' => $this->validation->getError('horHoraInicio'),
                    'horHoraFinNormal' => $this->validation->getError('horHoraFinNormal'),
                    'horHoraFin' => $this->validation->getError('horHoraFin'),
                ],
            ];
        }

        $horaInicio = trim((string) $this->request->getPost('horHoraInicio'));
        $horaFinNormal = trim((string) $this->request->getPost('horHoraFinNormal'));
        $horaFin = trim((string) $this->request->getPost('horHoraFin'));

        if ($horaInicio === $horaFin) {
            return [
                'status' => 'vacio',
                'msg' => [
                    'horHoraFin' => 'La hora fin debe ser diferente a la hora inicio.',
                ],
            ];
        }

        if ($horaInicio === $horaFinNormal) {
            return [
                'status' => 'vacio',
                'msg' => [
                    'horHoraFinNormal' => 'La hora fin normal debe ser diferente a la hora inicio.',
                ],
            ];
        }

        if (!$this->horaEstaDentroDelRango($horaInicio, $horaFinNormal, $horaFin)) {
            return [
                'status' => 'vacio',
                'msg' => [
                    'horHoraFinNormal' => 'La hora fin normal debe estar dentro del horario permitido.',
                ],
            ];
        }

        return null;
    }

    private function getDataHorarioPost(): array {

        $horaInicio = trim((string) $this->request->getPost('horHoraInicio'));
        $horaFinNormal = trim((string) $this->request->getPost('horHoraFinNormal'));
        $horaFin = trim((string) $this->request->getPost('horHoraFin'));

        return [
            'fk_servicio' => $this->request->getPost('fkServicio'),
            'hor_hora_inicio' => $horaInicio,
            'hor_hora_fin' => $horaFin,
            'hor_hora_fin_normal' => $horaFinNormal,
            'hor_cruza_medianoche' => $horaInicio > $horaFin ? 1 : 0,
            'hor_estado' => $this->request->getPost('horEstado') ?? 1,
        ];
    }

    private function horaEstaDentroDelRango(string $horaInicio, string $horaEvaluar, string $horaFin): bool {

        if ($horaInicio < $horaFin) {
            return $horaEvaluar > $horaInicio && $horaEvaluar <= $horaFin;
        }

        return $horaEvaluar > $horaInicio || $horaEvaluar <= $horaFin;
    }
}
