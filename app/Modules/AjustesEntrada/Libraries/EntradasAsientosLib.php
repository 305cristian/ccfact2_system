<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\AjustesEntrada\Libraries;

/**
 * Description of EntradasAsientosLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 19 oct 2025
 * @time 9:55:33 a.m.
 */
use Modules\Comun\Libraries\AsientoContableLib;
use Modules\Comun\Libraries\CuentasConfigLib;

class EntradasAsientosLib {

    //put your code here
    protected $tipotransaccionCod = '39';
    protected $ccm;
    protected $user;
    protected $asientoLib;
    protected $cuentasConfigLib;
    protected $logs;

    public function __construct() {
        //Import Servicios
        $this->ccm = service('ccModel');
        $this->user = service('userSecion');
        $this->logs = service('logs305');

        //Import Librerias
        $this->asientoLib = new AsientoContableLib();
        $this->cuentasConfigLib = new CuentasConfigLib();
    }

    /**
     * Genera el asiento contable para un ajuste de entrada
     * 
     * @param int $ajusteId ID del ajuste de entrada
     * @return int ID del asiento creado
     */
    public function generarAsiento($ajusteId) {
        try {
            // Obtener datos del ajuste
            $ajuste = $this->ccm->getData('cc_ajuste_entrada', ['id' => $ajusteId], '*', null, 1);

            if (!$ajuste) {
                throw new \Exception('Ajuste de entrada no encontrado');
            }

            // Validamos que el ajuste esté aprobado
            if ($ajuste->ajen_estado != 2) {
                throw new \Exception('Solo se puede generar asiento contable para ajustes aprobados');
            }

            // Validamos que no exista ya un asiento contable
            $asientoExiste = $this->ccm->getData('cc_asiento_contable', ['ac_codigo_transaccion' => $this->tipotransaccionCod, 'ac_documento_id' => $ajusteId], 'id');
            if (!empty($asientoExiste)) {
                throw new \Exception('Ya existe un asiento contable para este ajuste');
            }

            // Crear el asiento contable principal
            $detalle = 'AJUSTE DE ENTRADA - ' . ($ajuste->ajen_observaciones ?? '');
            $asientoId = $this->asientoLib->guardarAsiento($this->tipotransaccionCod, $ajusteId, $detalle, $ajuste->ajen_fecha);

            if (!$asientoId) {
                return [
                    'status' => 'warning',
                    'msg' => ' Ha ocurrido un error al generar asiento contable <br>',
                ];
            }

            // Obtener configuración de cuentas de la bodega
            $bodega = $this->ccm->getData('cc_bodegas', ['id' => $ajuste->fk_bodega], '*', null, 1);

            // ==================== DEBE ====================
            // DÉBITO 1: Inventario TARIFA 0% (si aplica)
            if ($ajuste->ajen_tarifacero_neto > 0) {
                $cuentaDebeTarifa0 = $this->obtenerCuentaBodega($bodega, 'bod_ctacont0', '010');
                if (!$cuentaDebeTarifa0) {
                    return [
                        'status' => 'warning',
                        'msg' => 'No se ha configurado la cuenta DEBE para ajustes de entrada (Código: 010)',
                    ];
                }

                $totalDebito = $ajuste->ajen_tarifacero_neto;

                $this->asientoLib->guardarDetalleAsiento(
                        $asientoId,
                        $cuentaDebeTarifa0,
                        $totalDebito, // DÉBITO
                        'DEBE', // DÉBITO
                        $this->tipotransaccionCod,
                        $ajusteId,
                        'Ajuste Entrada - Inventario Tarifa 0%',
                        null,
                        null,
                        $ajuste->fk_centro_costo
                );
            }

            // DÉBITO 2: Inventario TARIFA 12% + IVA (si aplica)
            if ($ajuste->ajen_tarifaiva_neto > 0) {
                $cuentaDebeTarifaImp = $this->obtenerCuentaBodega($bodega, 'bod_ctacont_iva', '011');
                if (!$cuentaDebeTarifaImp) {
                    return [
                        'status' => 'warning',
                        'msg' => 'No se ha configurado la cuenta DEBE para ajustes de entrada (Código: 011)',
                    ];
                }

                $totalDebito = $ajuste->ajen_tarifaiva_neto + $ajuste->ajen_totaliva;

                $this->asientoLib->guardarDetalleAsiento(
                        $asientoId,
                        $cuentaDebeTarifaImp,
                        $totalDebito, // DÉBITO
                        'DEBE', // DÉBITO
                        $this->tipotransaccionCod,
                        $ajusteId,
                        'Ajuste Entrada - Inventario Tarifa ' . getSettings('IVA') . ' % + IVA',
                        null,
                        null,
                        $ajuste->fk_centro_costo
                );
            }

            // ==================== HABER ====================
            // CRÉDITO: Cuenta de ajuste de inventario (contrapartida)
            $cuentaHaber = $this->cuentasConfigLib->obtenerSettingCuentaContable('012');
            if (empty($cuentaHaber)) {
                return [
                    'status' => 'warning',
                    'msg' => ' No se ha configurado la cuenta HABER para ajustes de entrada (Código: 012)',
                ];
            }

            $totalCredito = $ajuste->ajen_tarifacero_neto + $ajuste->ajen_tarifaiva_neto + $ajuste->ajen_totaliva;

            $this->asientoLib->guardarDetalleAsiento(
                    $asientoId,
                    $cuentaHaber,
                    $totalCredito, // CRÉDITO
                    'HABER', // CRÉDITO
                    $this->tipotransaccionCod,
                    $ajusteId,
                    'Ajuste Entrada - Cuenta de ajuste de entrada',
                    null,
                    null,
                    $ajuste->fk_centro_costo
            );

            // Validar que el asiento esté cuadrado
            $validacion = $this->asientoLib->validarAsientoCuadrado($asientoId);

            if (!$validacion) {
                return [
                    'status' => 'warning',
                    'msg' => ' El asiento contable no está cuadrado.',
                ];
            }

            $this->logs->logInfo('Asiento #' . $asientoId . ' generado para Ajuste #' . $ajusteId . ' ');

            return ['status' => 'success', 'data' => $asientoId];
        } catch (\Throwable $e) {
            throw new \Exception('Error al generar asiento contable <br> ' . $e->getMessage());
        }
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
