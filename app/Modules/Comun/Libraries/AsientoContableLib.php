<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Comun\Libraries;

/**
 * Description of AsientoContableLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 19 oct 2025
 * @time 9:53:10 a.m.
 */
class AsientoContableLib {

//put your code here
    protected $ccm;
    protected $user;
    protected $periodosCont;

    public function __construct() {

//IMPORT SERVICIOS
        $this->ccm = service('ccModel');
        $this->user = service('userSecion');

//IMPORT LIBRERIAS
    }

    /**
     * Guarda un nuevo asiento contable
     * 
     * @param string $tipoTransaccionCod Código de tipo de transacción (AJEN, COMP, VENT, etc)
     * @param int $docId ID del documento origen
     * @param string $detalle Detalle del asiento
     * @param string|null $fecha Fecha del asiento (null = hoy)
     * @param int|null $userId ID del usuario (null = usuario actual)
     * @return int ID del asiento creado
     */
    public function guardarAsiento($tipoTransaccionCod, $docId, $detalle = '', $fecha = null, $userId = null) {

        try {



            if ($fecha === null) {
                $fecha = date('Y-m-d');
            }

            $fechaArray = explode('-', $fecha);
            $anio = $fechaArray[0];
            $mes = $fechaArray[1];

            // Usuario
            $usuarioId = $userId ?? $this->user->id;

            // Obtener índice (periodo contable)
            $periodoContable = getPeriodoContable($fecha);
            if (!$periodoContable) {
                throw new \Exception('No se ha encontrado un periodo contable habil para la fecha dada');
            }

            // Obtener siguiente número de asiento en el periodo actual
            $nroAsiento = getNumeroAsiento($fecha);

            // Obtener numero secuencial
            $dataCecuencial = $this->ccm->getData('cc_asiento_contable', null, 'ac_secuencial', ['ac_secuencial' => 'DESC'], 1);
            $secuencial = (isset($dataCecuencial) ? $dataCecuencial->ac_secuencial + 1 : 1);

            // Datos del asiento
            $datosAsiento = [
                'fk_periodo' => $periodoContable,
                'ac_num_asiento' => $nroAsiento,
                'ac_secuencial' => $secuencial,
                'ac_anio' => $anio,
                'fk_mes' => $mes,
                'ac_fecha' => $fecha,
                'ac_hora' => date('H:i:s'),
                'ac_estado' => 1,
                'ac_detalle' => $detalle,
                'ac_codigo_transaccion' => $tipoTransaccionCod,
                'ac_documento_id' => $docId,
                'fk_user_id' => $usuarioId,
            ];

            $asientoId = $this->ccm->guardar($datosAsiento, 'cc_asiento_contable');

            if (!$asientoId) {
                throw new \Exception('Error al guardar el asiento contable');
            }

            return $asientoId;
        } catch (\Throwable $exc) {
            throw new \Exception('Error al generar asiento contable: ' . $exc->getMessage().$exc->getTraceAsString());
        }
    }

    /**
     * Guarda el detalle de un asiento contable
     * 
     * @param int $asientoId ID del asiento contable
     * @param string $cuentaContable Código de cuenta contable
     * @param float $valor Valor del débito o crédito
     * @param float $tipo Si es DEBE o HABER
     * @param string $tipoTransaccion Código de transacción
     * @param int $docId ID del documento
     * @param string $detalle Detalle del movimiento
     * @param string|null $tipoPago Tipo de pago (efectivo, cheque, transferencia, etc)
     * @param int|null $docIdPago ID del documento de pago
     * @param int|null $centroCosto ID del centro de costo
     * @return int ID del detalle creado
     */
    public function guardarDetalleAsiento(
            $asientoId,
            $cuentaContable,
            $valor,
            $tipo,
            $tipoTransaccion,
            $docId,
            $detalle,
            $tipoPago = null,
            $docIdPago = null,
            $centroCosto = null
    ) {
        try {

            // Validar que al menos uno tenga valor
            if ($valor == 0) {
                throw new \Exception('Debe especificar un valor para débito o crédito');
            }
            $datosDetalle = [
                'fk_asiento_contable' => $asientoId,
                'codigo_cuenta_contable' => $cuentaContable,
                'acd_valor' => $valor,
                'acd_tipo' => $tipo,
                'acd_codigo_transaccion' => $tipoTransaccion,
                'acd_documento_id' => $docId,
                'acd_detalle' => $detalle,
                'acd_tipo_pago' => $tipoPago,
                'acd_documento_id_pago' => $docIdPago,
                'fk_centro_costos' => $centroCosto,
            ];

            $detalleId = $this->ccm->guardar($datosDetalle, 'cc_asiento_contable_det');

            if (!$detalleId) {
                throw new \Exception('Error al guardar el detalle del asiento contable');
            }

            return $detalleId;
        } catch (\Throwable $e) {
            throw new \Exception('Error al guardar detalle de asiento: ' . $e->getMessage());
        }
    }

    /**
     * Valida que un asiento esté cuadrado (débitos = créditos)
     * 
     * @param int $asientoId ID del asiento
     */
    public function validarAsientoCuadrado($asientoId) {
        try {
            $whereData = [
                'fk_asiento_contable' => $asientoId,
                'acd_estado' => 1
            ];
            $asientosDet = $this->ccm->getData('cc_asiento_contable_det', $whereData, 'acd_tipo, acd_valor');

            $totalDebe = 0;
            $totalHaber = 0;

            foreach ($asientosDet as $det) {
                if ($det->acd_tipo === 'DEBE') {
                    $totalDebe += $det->acd_valor;
                } elseif ($det->acd_tipo === 'HABER') {
                    $totalHaber += $det->acd_valor;
                }
            }

            $diferencia = abs($totalDebe - $totalHaber);
            return $diferencia < 0.01 ? true : false; //Se da la tolerencia de 1 centavo
        } catch (\Throwable $e) {
            log_message('error', '[AsientoContableLib::validarAsientoCuadrado] ' . $e->getMessage());
            throw new \Exception('Error al validar asiento: ' . $e->getMessage());
        }
    }

    /**
     * Anula un asiento contable por tipo de transacción y doc_id
     * 
     * @param string $tipoTransaccion Código de tipo de transacción
     * @param int $docId ID del documento
     * @return bool
     */
    public function anularAsiento($tipoTransaccion, $docId) {
        try {
            $resultado = $this->ccm->actualizar(
                    ['ac_estado' => -1, 'ac_fecha_anulacion' => date('Y-m-d H:i:s')],
                    ['ac_codigo_transaccion' => $tipoTransaccion, 'ac_doc_id' => ac_documento_id],
                    'cc_asiento_contable'
            );

            if ($resultado) {
                log_message('info', "[AsientoContable] Anulado. Tipo: {$tipoTransaccion}, DocID: {$docId}");
            }

            return $resultado;
        } catch (\Throwable $e) {
            log_message('error', '[AsientoContableLib::anularAsiento] ' . $e->getMessage());
            throw new \Exception('Error al anular asiento contable: ' . $e->getMessage());
        }
    }
}
