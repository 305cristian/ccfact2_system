<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\NotasCredito\Libraries;

use Modules\Comun\Libraries\AsientoContableLib;
use Modules\Comun\Libraries\CuentasConfigLib;

/**
 * Description of NotasCreditoAsientosLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 26 jul 2026
 * @time 5:55:32 p.m.
 */
class NotasCreditoAsientosLib {

    protected $ccm;
    protected AsientoContableLib $asientoLib;
    protected CuentasConfigLib $cuentasConfigLib;
    protected string $tipoTransaccionNotaCredito = '11';

    public function __construct() {
        $this->ccm = service('ccModel');
        $this->asientoLib = new AsientoContableLib();
        $this->cuentasConfigLib = new CuentasConfigLib();
    }

    public function generarAsientoNotaCredito(int $notaCreditoId): int {

        $notaCredito = $this->ccm->getData('cc_compras', ['id' => $notaCreditoId], '*', null, 1);

        if (!$notaCredito) {
            throw new \RuntimeException('No se encontro la nota de credito registrada.');
        }

        if ($notaCredito->comp_estado !== 'ARCHIVADO') {
            throw new \RuntimeException('Solo las notas de credito archivadas generan asientos contables.');
        }

        if (empty($notaCredito->fk_compra_relacionada)) {
            throw new \RuntimeException('La nota de credito no tiene compra relacionada.');
        }

        $asientoExistente = $this->ccm->getData(
                'cc_asiento_contable',
                [
                    'ac_codigo_transaccion' => $this->tipoTransaccionNotaCredito,
                    'ac_documento_id' => $notaCreditoId,
                    'ac_estado' => 1,
                ],
                'id',
                null,
                1
        );

        if ($asientoExistente) {
            throw new \RuntimeException('La nota de credito ya tiene un asiento contable registrado.');
        }

        $detalles = $this->ccm->getData('cc_compras_det', ['fk_compra' => $notaCreditoId, 'compd_estado' => 1], '*');

        if (empty($detalles)) {
            throw new \RuntimeException('La nota de credito no tiene detalles para generar el asiento.');
        }

        $detalleAsiento = 'NOTA CREDITO COMPRA NRO. '
                . $notaCredito->comp_numero_establecimiento . '-'
                . $notaCredito->comp_numero_emision . '-'
                . $notaCredito->comp_numero_comprobante;

        $asientoId = $this->asientoLib->guardarAsiento(
                $this->tipoTransaccionNotaCredito,
                $notaCreditoId,
                $detalleAsiento,
                $notaCredito->comp_fecha_emision
        );

        $this->guardarDebitoCuentaPorPagarNdc($asientoId, $notaCredito);
        $this->guardarCreditosItemsNdc($asientoId, $notaCredito, $detalles);
        $this->guardarCreditosIvaNdc($asientoId, $notaCredito);

        if (!$this->asientoLib->validarAsientoCuadrado($asientoId)) {
            throw new \RuntimeException('El asiento contable de la nota de credito no esta cuadrado.');
        }

        return (int) $asientoId;
    }

    public function anularAsientoNotaCredito(int $notaCreditoId): void {

        $asiento = $this->ccm->getData('cc_asiento_contable', ['ac_codigo_transaccion' => $this->tipoTransaccionNotaCredito,'ac_documento_id' => $notaCreditoId, 'ac_estado' => 1, ],'id', null, 1 );

        if (!$asiento) {
            return;
        }

        $anulado = $this->asientoLib->anularAsiento($this->tipoTransaccionNotaCredito, $notaCreditoId);

        if (!$anulado) {
            throw new \RuntimeException('No se pudo anular el asiento contable de la nota de credito.');
        }
    }

    private function guardarDebitoCuentaPorPagarNdc(int $asientoId, object $notaCredito): void {

        $proveedor = $this->ccm->getData(
                'cc_proveedores',
                ['id' => (int) $notaCredito->fk_proveedor],
                'id, prov_nombres, fk_codigo_cuenta_contable',
                null,
                1
        );

        if (!$proveedor) {
            throw new \RuntimeException('No se encontro el proveedor de la nota de credito.');
        }

        $cuenta = trim((string) ($proveedor->fk_codigo_cuenta_contable ?? ''));

        if ($cuenta === '') {
            $cuenta = $this->cuentasConfigLib->obtenerSettingCuentaContable('021');

            if (!$cuenta) {
                throw new \RuntimeException("El proveedor {$proveedor->prov_nombres} no tiene cuenta contable y no esta configurada la cuenta general 021.");
            }
        }

        $valor = round((float) $notaCredito->comp_total, 4);

        if ($valor <= 0) {
            throw new \RuntimeException('El valor de la nota de credito debe ser mayor a cero para generar el asiento.');
        }

        $this->asientoLib->guardarDetalleAsiento(
                $asientoId,
                $cuenta,
                $valor,
                'DEBE',
                $this->tipoTransaccionNotaCredito,
                (int) $notaCredito->id,
                'Disminucion de cuenta por pagar por NDC',
                null,
                null,
                $notaCredito->fk_centro_costo
        );
    }

    private function guardarCreditosItemsNdc(int $asientoId, object $notaCredito, array $detalles): void {

        $cuentas = [];
        $subtotalDetalles = 0.0;

        foreach ($detalles as $detalle) {
            $cuenta = trim((string) ($detalle->compd_cta_entrada ?? ''));

            if ($cuenta === '') {
                throw new \RuntimeException("El detalle {$detalle->id} no tiene cuenta contable.");
            }

            $valor = (float) $detalle->compd_total_neto;

            if ($valor < 0) {
                throw new \RuntimeException("El detalle {$detalle->id} tiene un valor neto invalido.");
            }

            $cuentas[$cuenta] = ($cuentas[$cuenta] ?? 0) + $valor;
            $subtotalDetalles += $valor;
        }

        $subtotalNotaCredito = round((float) $notaCredito->comp_subtotal_neto, 4);

        if ($subtotalDetalles <= 0 || $subtotalNotaCredito < 0) {
            throw new \RuntimeException('El subtotal de la nota de credito no es valido para generar el asiento.');
        }

        $cuentasContables = array_keys($cuentas);
        $ultimaCuenta = array_key_last($cuentasContables);
        $valorAcumulado = 0.0;

        foreach ($cuentasContables as $indice => $cuenta) {
            if ($indice === $ultimaCuenta) {
                $valorCuenta = round($subtotalNotaCredito - $valorAcumulado, 4);
            } else {
                $proporcion = $cuentas[$cuenta] / $subtotalDetalles;
                $valorCuenta = round($subtotalNotaCredito * $proporcion, 4);
                $valorAcumulado += $valorCuenta;
            }

            if ($valorCuenta <= 0) {
                continue;
            }

            $this->asientoLib->guardarDetalleAsiento(
                    $asientoId,
                    $cuenta,
                    $valorCuenta,
                    'HABER',
                    $this->tipoTransaccionNotaCredito,
                    (int) $notaCredito->id,
                    'Nota de credito - bienes, servicios o descuentos',
                    null,
                    null,
                    $notaCredito->fk_centro_costo
            );
        }
    }

    private function guardarCreditosIvaNdc(int $asientoId, object $notaCredito): void {

        $bases = $this->ccm->getData(
                'cc_compras_bases_impuesto',
                [
                    'fk_compra' => (int) $notaCredito->id,
                    'tipo_impuesto' => 'IVA',
                    'estado' => 1,
                ],
                '*'
        );

        $cuentasIva = [];
        $totalBasesIva = 0.0;

        foreach ($bases ?? [] as $base) {
            $valorIva = (float) $base->iva_valor;

            if ($valorIva <= 0) {
                continue;
            }

            $porcentaje = (float) $base->imp_porcentaje;
            $codigoConfig = $this->obtenerConfiguracionIvaNdc((int) $base->fk_impuesto_tarifa);

            if (!isset($cuentasIva[$codigoConfig])) {
                $cuentasIva[$codigoConfig] = ['porcentaje' => $porcentaje, 'valor' => 0.0];
            }

            $cuentasIva[$codigoConfig]['valor'] += $valorIva;
            $totalBasesIva += $valorIva;
        }

        $totalNotaCreditoIva = round((float) $notaCredito->comp_totaliva, 4);

        if (abs($totalBasesIva - $totalNotaCreditoIva) > 0.01) {
            throw new \RuntimeException('El IVA de las bases no coincide con el total del IVA de la nota de credito.');
        }

        foreach ($cuentasIva as $codigoConfig => $datosIva) {
            $cuenta = $this->cuentasConfigLib->obtenerSettingCuentaContable($codigoConfig);

            if (!$cuenta) {
                throw new \RuntimeException("No esta configurada la cuenta para IVA {$datosIva['porcentaje']}%.");
            }

            $this->asientoLib->guardarDetalleAsiento(
                    $asientoId,
                    $cuenta,
                    round($datosIva['valor'], 4),
                    'HABER',
                    $this->tipoTransaccionNotaCredito,
                    (int) $notaCredito->id,
                    "IVA compras NDC {$datosIva['porcentaje']}%",
                    null,
                    null,
                    $notaCredito->fk_centro_costo
            );
        }
    }

    private function obtenerConfiguracionIvaNdc(int $tarifaId): string {

        $tarifa = $this->ccm->getData(
                'cc_impuesto_tarifa',
                ['id' => $tarifaId, 'fk_impuesto' => 1],
                'id, impt_porcentage, impt_grupo',
                null,
                1
        );

        if (!$tarifa || (float) $tarifa->impt_porcentage <= 0) {
            throw new \RuntimeException("La tarifa IVA {$tarifaId} no existe o no genera IVA.");
        }

        if ($tarifa->impt_grupo === 'GENERAL') {
            return '016';
        }

        if ($tarifa->impt_grupo === 'ESPECIAL') {
            return '017';
        }

        throw new \RuntimeException("No existe configuracion contable para IVA {$tarifa->impt_porcentage}%.");
    }
}
