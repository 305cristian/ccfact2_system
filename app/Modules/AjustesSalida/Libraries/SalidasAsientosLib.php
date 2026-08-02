<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\AjustesSalida\Libraries;

/**
 * Description of SalidasAsientosLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 27 nov 2025
 * @time 12:27:42 p.m.
 */
use Modules\Comun\Libraries\AsientoContableLib;
use Modules\Comun\Libraries\CuentasConfigLib;

class SalidasAsientosLib {

    //put your code here

    protected $tipotransaccionCod = '38';
    protected $ccm;
    protected $user;
    protected AsientoContableLib $asientoLib;
    protected CuentasConfigLib $cuentasConfigLib;
    protected $logs;

    public function __construct() {

        // IMPORTAMOS SERVICIOS
        $this->ccm = service('ccModel');
        $this->user = service('userSecion');
        $this->logs = service('logs305');

        //IMPORTAMOS LIBRERIAS
        $this->asientoLib = new AsientoContableLib();
        $this->cuentasConfigLib = new CuentasConfigLib();
    }

    /**
     * @param int $ajusteId Identificador del ajuste de salida para el cual se generará el asiento contable
     * @return array Resultado de la operación con el estado ('success' o 'warning'), mensaje descriptivo y el identificador del asiento generado (si aplica)
     * La función generarAsiento es responsable de crear un asiento contable para un ajuste de salida específico, validando el estado del ajuste, verificando la existencia de un asiento previo, y luego generando los detalles del
    */
    public function generarAsiento(int $ajusteId) {
        $ajuste = $this->ccm->getData('cc_ajuste_salida', ['id' => $ajusteId, 'fk_proyecto' => getProyectoId()], '*', null, 1);
        if (!$ajuste) {
            throw new \Exception('Ajuste de salida no encontrado');
        }
        if ($ajuste->ajes_estado != 2) {
            throw new \Exception('Solo se puede generar asiento contable para ajustes aprobados');
        }

        $asientoExiste = $this->ccm->getData('cc_asiento_contable', [ 'ac_codigo_transaccion' => $this->tipotransaccionCod, 'ac_documento_id' => $ajusteId, 'fk_proyecto' => getProyectoId(),'ac_estado' => 1 ], 'id');
        if (!empty($asientoExiste)) {
            throw new \Exception('Ya existe asiento contable para este ajuste de salida');
        }

        $detalle = 'AJUSTE DE SALIDA - ' . ($ajuste->ajes_observaciones ?? '');
        $asientoId = $this->asientoLib->guardarAsiento($this->tipotransaccionCod, $ajusteId, $detalle, $ajuste->ajes_fecha);

        if (!$asientoId) {
            return ['status' => 'warning', 'msg' => 'Error al generar asiento contable'];
        }

        $bodega = $this->ccm->getData('cc_bodegas', ['id' => $ajuste->fk_bodega], '*', null, 1);

        // ==================== DEBE ====================
        // DEBE -> Inventario (cuenta de la bodega o genérica)
        //  DEBITO: Cuenta de ajuste de inventario (contrapartida)
        $cuentaDebe = $this->cuentasConfigLib->obtenerSettingCuentaContable('013');
        if (empty($cuentaDebe)) {
            return [
                'status' => 'warning',
                'msg' => ' No se ha configurado la cuenta HABER para ajustes de entrada (Código: 013)',
            ];
        }


        $totalDebito = $ajuste->ajes_tarifacero_neto + $ajuste->ajes_tarifaiva_neto + $ajuste->ajes_totaliva;

        $this->asientoLib->guardarDetalleAsiento(
                $asientoId,
                $cuentaDebe,
                $totalDebito, // DEBITO
                'DEBE', // DEBITO
                $this->tipotransaccionCod,
                $ajusteId,
                'Ajuste Salida - Cuenta de ajuste de entrada',
                null,
                null,
                $ajuste->fk_centro_costo
        );

        // ==================== HABER ====================
        if ($ajuste->ajes_tarifacero_neto > 0) {
            $cuentaHaberTarifa0 = $this->obtenerCuentaBodega($bodega, 'bod_ctacont0', '010');
            if (!$cuentaHaberTarifa0) {
                return [
                    'status' => 'warning',
                    'msg' => 'No se ha configurado la cuenta HABER para ajustes de salida (Código: 010)',
                ];
            }
            $totalCredito = $ajuste->ajes_tarifacero_neto;

            $this->asientoLib->guardarDetalleAsiento(
                    $asientoId,
                    $cuentaHaberTarifa0,
                    $totalCredito, //CRÉDITO
                    'HABER', //CRÉDITO
                    $this->tipotransaccionCod,
                    $ajusteId,
                    'Ajuste Salida - Inventario Tarifa 0%',
                    null,
                    null,
                    $ajuste->fk_centro_costo
            );
        }

        // DÉBITO 2: Inventario TARIFA 12% + IVA (si aplica)
        if ($ajuste->ajes_tarifaiva_neto > 0) {
            $cuentaHaberTarifaImp = $this->obtenerCuentaBodega($bodega, 'bod_ctacont_iva', '011');
            if (!$cuentaHaberTarifaImp) {
                return [
                    'status' => 'warning',
                    'msg' => 'No se ha configurado la cuenta HABER para ajustes de salida (Código: 011)',
                ];
            }

            $totalCredito = $ajuste->ajes_tarifaiva_neto + $ajuste->ajes_totaliva;

            $this->asientoLib->guardarDetalleAsiento(
                    $asientoId,
                    $cuentaHaberTarifaImp,
                    $totalCredito, // CRÉDITO
                    'HABER', // CRÉDITO
                    $this->tipotransaccionCod,
                    $ajusteId,
                    'Ajuste Salida - Inventario Tarifa ' . (float) $ajuste->iva_porcentaje . ' % + IVA',
                    null,
                    null,
                    $ajuste->fk_centro_costo
            );
        }



        $ok = $this->asientoLib->validarAsientoCuadrado($asientoId);
        if (!$ok) {
            return ['status' => 'warning', 'msg' => 'El asiento contable del ajuste de salida no está cuadrado'];
        }

        $this->logs->logInfo("Asiento #{$asientoId} generado para Ajuste de Salida #{$ajusteId}");
        return ['status' => 'success', 'data' => $asientoId];
    }
    
     /**
     * Obtiene la cuenta contable de la bodega con fallback
     * 
     * @param object $bodega Objeto bodega
     * @param string $campo Campo de la bodega (bod_cuenta_tarifa0, bod_cuenta_tarifa12)
     * @param string $codigoFallback Código de configuración por defecto
     * @return string Código de cuenta contable
     */
    protected function obtenerCuentaBodega($bodega, $campo, $codigoFallback) {
        // Prioridad 1: Cuenta configurada en la bodega
        if ($bodega && !empty($bodega->$campo)) {
            return $bodega->$campo;
        }

        // Prioridad 2: Cuenta configurada en config_cuentas
        $cuentaConfig = $this->cuentasConfigLib->obtenerSettingCuentaContable($codigoFallback);
        if (!empty($cuentaConfig)) {
            return $cuentaConfig;
        }

        return false;
    }
}
