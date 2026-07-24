<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\BioComedor\Libraries;

use Modules\BioComedor\Models\BioModel;

/**
 * Description of BioMarcacionesLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 24 jul 2026
 * @time 1:37:27 a.m.
 */
class BioMarcacionesLib {

    //put your code here

    protected BioModel $bioModel;
    protected $ccm;
    protected $user;
    protected $logs;

    public function __construct() {

        $this->ccm = service('ccModel');
        $this->user = service('userSecion');
        $this->logs = service('logs305');
        $this->bioModel = new BioModel();
    }

    public function procesarMarcacion(object $dataMarcacion): array {

        $comedorId = (int) ($dataMarcacion->fkComedor ?? 0);
        $equipoId = (int) ($dataMarcacion->fkEquipo ?? 0);
        $tipoIdentificacion = trim((string) ($dataMarcacion->tipoIdentificacion ?? 'CODIGO'));
        $identificador = mb_strtoupper(trim((string) ($dataMarcacion->identificador ?? '')), 'UTF-8');
        $fecha = trim((string) ($dataMarcacion->marcFecha ?? ''));
        $hora = trim((string) ($dataMarcacion->marcHora ?? ''));
        $origen = trim((string) ($dataMarcacion->marcOrigen ?? 'MANUAL'));
        $fechaHora = $fecha . ' ' . $hora;

        $equipo = $comedorId > 0 ? $this->bioModel->getEquipoActivo($equipoId, $comedorId) : $this->bioModel->getEquipoActivoPorId($equipoId);
        if (!$equipo) {
            return $this->responseArray('warning', 'El equipo de marcacion no existe o esta inactivo.');
        }

        $comedorId = (int) $equipo->fk_comedor;
        $comensal = $tipoIdentificacion === 'AUTO' ? $this->bioModel->getComensalPorIdentificadorAutomatico($identificador) : $this->bioModel->getComensalPorIdentificador($tipoIdentificacion, $identificador);
        if (!$comensal || (int) $comensal->comens_estado !== 1) {
            return $this->responseArray('warning', 'No se encontro un comensal activo con el identificador ingresado.');
        }

        $horario = $this->bioModel->getHorarioServicioPorHora($hora);
        if (!$horario) {
            return $this->responseArray('warning', 'No existe un servicio activo configurado para la hora ingresada.');
        }

        $permiteMultiple = (string) getSettings("PERMITIR_MULTIPLE_CONSUMO_SERVICIO");
//        $permiteMultiple = (string) ($this->ccm->getValue('cc_settings', 'PERMITIR_MULTIPLE_CONSUMO_SERVICIO', 'st_value', 'st_nombre') ?? '0');
        $toleranciaMinutos = (int) getSettings("TOLERANCIA_MARCACION_MINUTOS");
//        $toleranciaMinutos = (int) ($this->ccm->getValue('cc_settings', 'TOLERANCIA_MARCACION_MINUTOS', 'st_value', 'st_nombre') ?? 5);
        $fechaHoraDesde = date('Y-m-d H:i:s', strtotime($fechaHora . ' -' . $toleranciaMinutos . ' minutes'));
        $fechaHoraHasta = date('Y-m-d H:i:s', strtotime($fechaHora . ' +' . $toleranciaMinutos . ' minutes'));
        $marcacionTolerancia = $this->bioModel->getMarcacionPorTolerancia((int) $comensal->id, $fechaHoraDesde, $fechaHoraHasta);
        $marcacionDia = $this->bioModel->getMarcacionValidaServicioDia((int) $comensal->id, (int) $horario->fk_servicio, $fecha);
        $estado = 'VALIDA';
        $generaConsumo = 1;
        $observacion = 'Marcacion registrada ' . mb_strtolower($origen, 'UTF-8') . '.';

        if ($marcacionTolerancia) {
            $estado = 'REPETIDA';
            $generaConsumo = 0;
            $observacion = 'Marcacion repetida: existe una marcacion valida dentro de la tolerancia configurada.';
        } else if ($permiteMultiple !== '1' && $marcacionDia) {
            $estado = 'REPETIDA';
            $generaConsumo = 0;
            $observacion = 'Marcacion repetida: el comensal ya registra un consumo valido para este servicio en la fecha seleccionada.';
        }

        $esRetraso = $estado === 'VALIDA' && !$this->horaEstaDentroDelRango($horario->hor_hora_inicio, $hora, $horario->hor_hora_fin_normal) ? 1 : 0;

        $dataSave = [
            'fk_comensal' => $comensal->id,
            'fk_comedor' => $comedorId,
            'fk_equipo' => $equipoId,
            'fk_servicio' => $horario->fk_servicio,
            'fk_contratista' => $comensal->fk_contratista,
            'fk_proyecto' => $comensal->fk_proyecto,
            'marc_fecha' => $fecha,
            'marc_hora' => $hora,
            'marc_fecha_hora' => $fechaHora,
            'marc_estado' => $estado,
            'marc_genera_consumo' => $generaConsumo,
            'marc_es_retraso' => $esRetraso,
            'marc_origen' => $origen,
            'marc_codigo_biometrico' => $identificador,
            'marc_observacion' => $observacion,
        ];

        $marcacionId = $this->ccm->guardar($dataSave, 'cc_bio_marcaciones');
        $this->logs->logSuccess('SE HA REGISTRADO UNA MARCACION BIO COMEDOR CON EL ID ' . $marcacionId);

        return [
            'status' => $estado === 'VALIDA' ? 'success' : 'warning',
            'msg' => $estado === 'VALIDA' ? 'Marcacion registrada exitosamente.' : $observacion,
            'data' => [
                'id' => $marcacionId,
                'estado' => $estado,
                'retraso' => $esRetraso,
                'comensal' => trim($comensal->comens_nombres . ' ' . $comensal->comens_apellidos),
                'codigoComensal' => $comensal->comens_codigo,
                'contratista' => $comensal->cont_nombre,
                'proyecto' => $comensal->proy_nombre,
                'departamento' => $comensal->dep_nombre ?? '',
                'area' => $comensal->area_nombre ?? '',
                'foto' => !empty($comensal->comens_foto) ? base_url('uploads/img/bio_comensales/' . $comensal->comens_foto) : '',
                'servicio' => $horario->serv_nombre,
                'comedor' => $equipo->com_nombre,
                'equipo' => $equipo->eq_nombre,
            ],
        ];
    }

    private function responseArray(string $status, string $mensaje, mixed $data = null): array {

        return [
            'status' => $status,
            'msg' => $mensaje,
            'data' => $data,
        ];
    }

    private function horaEstaDentroDelRango(string $horaInicio, string $horaEvaluar, string $horaFin): bool {

        if ($horaInicio < $horaFin) {
            return $horaEvaluar >= $horaInicio && $horaEvaluar <= $horaFin;
        }

        return $horaEvaluar >= $horaInicio || $horaEvaluar <= $horaFin;
    }
}
