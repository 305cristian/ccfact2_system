<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Compras\Libraries;

use Modules\Comun\Libraries\AsientoContableLib;
use Modules\Comun\Libraries\CuentasConfigLib;

/**
 * Description of ComprasAsientosLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 5 jul 2026
 * @time 6:26:33 p.m.
 */
class ComprasAsientosLib {

    protected $ccm;
    protected AsientoContableLib $asientoLib;
    protected CuentasConfigLib $cuentasConfigLib;
    protected string $tipoTransaccion = '02';

    public function __construct() {
        $this->ccm = service('ccModel');
        $this->asientoLib = new AsientoContableLib();
        $this->cuentasConfigLib = new CuentasConfigLib();
    }

    public function generarAsiento(int $compraId): int {
        $compra = $this->ccm->getData('cc_compras', ['id' => $compraId], '*', null, 1);

        if (!$compra) {
            throw new \RuntimeException('No se encontró la compra registrada.');
        }

        if ($compra->comp_estado !== 'ARCHIVADO') {
            throw new \RuntimeException('Solo las compras archivadas generan asientos contables.');
        }

        $asientoExistente = $this->ccm->getData('cc_asiento_contable', ['ac_codigo_transaccion' => $this->tipoTransaccion, 'ac_documento_id' => $compraId, 'ac_estado' => 1,], 'id', null, 1);

        if ($asientoExistente) {
            throw new \RuntimeException('La compra ya tiene un asiento contable registrado.');
        }

        $detalles = $this->ccm->getData('cc_compras_det', ['fk_compra' => $compraId, 'compd_estado' => 1], '*');

        if (empty($detalles)) {
            throw new \RuntimeException('La compra no tiene detalles para generar el asiento.');
        }

        $detalleAsiento = 'COMPRA NRO. '
                . $compra->comp_numero_establecimiento . '-'
                . $compra->comp_numero_emision . '-'
                . $compra->comp_numero_comprobante;

        $asientoId = $this->asientoLib->guardarAsiento($this->tipoTransaccion, $compraId, $detalleAsiento, $compra->comp_fecha_emision);

        $this->guardarDebitosItems($asientoId, $compra, $detalles);
        $this->guardarDebitosIva($asientoId, $compra);

        if ((float) $compra->comp_totalice != 0.0|| (float) $compra->comp_totalirbpnr != 0.0 ) {
            $this->guardarDebitosOtrosImpuestos($asientoId, $compra);
        }

        if ((float) $compra->comp_recargo != 0.0 || (float) $compra->comp_servicios_adicionales != 0.0 ) {
            $this->guardarDebitosAdicionales($asientoId, $compra);
        }

        $totalRetenido = 0.0;

        if ((int) $compra->comp_aplica_retencion === 1) {
            if (empty($compra->fk_retencion)) {
                throw new \RuntimeException('La compra indica retención, pero no tiene una retención registrada.');
            }

            $totalRetenido = $this->guardarCreditosRetencion($asientoId, $compra);
        }

        $this->guardarCreditoProveedor($asientoId, $compra, $totalRetenido);

        if (!$this->asientoLib->validarAsientoCuadrado($asientoId)) {
            throw new \RuntimeException('El asiento contable de la compra no está cuadrado.');
        }

        return (int) $asientoId;
    }

    public function anularAsientoCompra(int $compraId): void {
        $asiento = $this->ccm->getData( 'cc_asiento_contable', ['ac_codigo_transaccion' => $this->tipoTransaccion, 'ac_documento_id' => $compraId, 'ac_estado' => 1,], 'id', null, 1 );

        if (!$asiento) {
            return;
        }

        $anulado = $this->asientoLib->anularAsiento($this->tipoTransaccion, $compraId);

        if (!$anulado) {
            throw new \RuntimeException('No se pudo anular el asiento contable de la compra.');
        }
    }

    protected function guardarDebitosItems(int $asientoId, object $compra, array $detalles): void {

        $cuentas = [];
        $subtotalDetalles = 0.0;

        foreach ($detalles as $detalle) {
            $cuenta = trim((string) ($detalle->compd_cta_entrada ?? ''));

            if ($cuenta === '') {
                throw new \RuntimeException("El producto {$detalle->fk_producto} no tiene cuenta contable de entrada.");
            }

            $valor = (float) $detalle->compd_total_neto;

            if ($valor < 0) {
                throw new \RuntimeException("El producto {$detalle->fk_producto} tiene un valor neto inválido.");
            }

            $cuentas[$cuenta] = ($cuentas[$cuenta] ?? 0) + $valor;
            $subtotalDetalles += $valor;
        }

        $subtotalCompra = round((float) $compra->comp_subtotal_neto, 4);

        if ($subtotalDetalles <= 0 || $subtotalCompra < 0) {
            throw new \RuntimeException(
                            'El subtotal de la compra no es válido para generar el asiento.'
            );
        }

        $cuentasContables = array_keys($cuentas);
        $ultimaCuenta = array_key_last($cuentasContables);
        $valorAcumulado = 0.0;

        foreach ($cuentasContables as $indice => $cuenta) {
            if ($indice === $ultimaCuenta) {
                $valorCuenta = round($subtotalCompra - $valorAcumulado, 4);
            } else {
                $proporcion = $cuentas[$cuenta] / $subtotalDetalles;
                $valorCuenta = round($subtotalCompra * $proporcion, 4);
                $valorAcumulado += $valorCuenta;
            }

            if ($valorCuenta <= 0) {
                continue;
            }

            $this->asientoLib->guardarDetalleAsiento(
                    $asientoId,
                    $cuenta,
                    $valorCuenta,
                    'DEBE',
                    $this->tipoTransaccion,
                    (int) $compra->id,
                    'Compra - bienes y servicios',
                    null,
                    null,
                    $compra->fk_centro_costo
            );
        }
    }

    protected function guardarDebitosIva(int $asientoId, object $compra): void {
        $bases = $this->ccm->getData('cc_compras_bases_impuesto', ['fk_compra' => $compra->id, 'tipo_impuesto' => 'IVA', 'estado' => 1,], '*');

        $cuentasIva = [];
        $totalBasesIva = 0.0;

        foreach ($bases ?? [] as $base) {
            $valorIva = (float) $base->iva_valor;

            if ($valorIva <= 0) {
                continue;
            }

            $porcentaje = (float) $base->imp_porcentaje;
            $codigoConfig = $this->obtenerConfiguracionIva( (int) $base->fk_impuesto_tarifa );

            if (!isset($cuentasIva[$codigoConfig])) {
                $cuentasIva[$codigoConfig] = ['porcentaje' => $porcentaje, 'valor' => 0.0];
            }

            $cuentasIva[$codigoConfig]['valor'] += $valorIva;
            $totalBasesIva += $valorIva;
        }

        $totalCompraIva = round((float) $compra->comp_totaliva, 4);

        if (abs($totalBasesIva - $totalCompraIva) > 0.01) {
            throw new \RuntimeException('El IVA de las bases no coincide con el total del IVA de la compra.');
        }

        foreach ($cuentasIva as $codigoConfig => $datosIva) {
            $cuenta = $this->cuentasConfigLib->obtenerSettingCuentaContable($codigoConfig);

            if (!$cuenta) {
                throw new \RuntimeException("No está configurada la cuenta para IVA {$datosIva['porcentaje']}%.");
            }

            $this->asientoLib->guardarDetalleAsiento(
                    $asientoId,
                    $cuenta,
                    round($datosIva['valor'], 4),
                    'DEBE',
                    $this->tipoTransaccion,
                    (int) $compra->id,
                    "IVA compras {$datosIva['porcentaje']}%",
                    null,
                    null,
                    $compra->fk_centro_costo
            );
        }
    }

    protected function guardarDebitosOtrosImpuestos(int $asientoId, object $compra): void {
        $impuestos = [
            [
                'codigo' => '015',
                'valor' => (float) $compra->comp_totalice,
                'detalle' => 'ICE en compras',
            ],
            [
                'codigo' => '018',
                'valor' => (float) $compra->comp_totalirbpnr,
                'detalle' => 'IRBPNR en compras',
            ],
        ];

        foreach ($impuestos as $impuesto) {
            $this->guardarDebitoConfigurado($asientoId, $compra, $impuesto['codigo'], $impuesto['valor'], $impuesto['detalle']);
        }
    }

    protected function guardarDebitosAdicionales(int $asientoId, object $compra): void {
        $adicionales = [
            [
                'codigo' => '019',
                'valor' => (float) $compra->comp_recargo,
                'detalle' => 'Recargos en compras',
            ],
            [
                'codigo' => '020',
                'valor' => (float) $compra->comp_servicios_adicionales,
                'detalle' => 'Servicios adicionales en compras',
            ],
        ];

        foreach ($adicionales as $adicional) {
            $this->guardarDebitoConfigurado( $asientoId, $compra, $adicional['codigo'], $adicional['valor'], $adicional['detalle'] );
        }
    }

    protected function guardarDebitoConfigurado( int $asientoId, object $compra, string $codigoConfig, float $valor, string $detalle ): void {
        $valor = round($valor, 4);

        if ($valor < 0) {
            throw new \RuntimeException("El valor de {$detalle} no puede ser negativo." );
        }

        if ($valor === 0.0) {
            return;
        }

        $cuenta = $this->cuentasConfigLib->obtenerSettingCuentaContable($codigoConfig);

        if (!$cuenta) {
            throw new \RuntimeException( "No está configurada la cuenta contable para {$detalle}." );
        }

        $this->asientoLib->guardarDetalleAsiento(
                $asientoId,
                $cuenta,
                $valor,
                'DEBE',
                $this->tipoTransaccion,
                (int) $compra->id,
                $detalle,
                null,
                null,
                $compra->fk_centro_costo
        );
    }

    protected function guardarCreditosRetencion(int $asientoId, object $compra): float {

        $retencion = $this->ccm->getData('cc_retencion', ['id' => $compra->fk_retencion, 'ret_documento_id' => $compra->id, 'ret_estado' => 1,], '*', null, 1);

        if (!$retencion) {
            throw new \RuntimeException('No se encontró la retención activa de la compra.');
        }

        $detalles = $this->ccm->getData('cc_retencion_det', ['fk_retencion' => $retencion->id], '*');

        if (empty($detalles)) {
            throw new \RuntimeException('La retención no tiene detalles registrados.');
        }

        $cuentas = [];
        $totalDetalles = 0.0;

        foreach ($detalles as $detalle) {
            $retencionSri = $this->ccm->getData('cc_retencion_sri', ['id' => $detalle->fk_sri_retencion, 'ret_estado' => 1,], 'ret_cta_compras, ret_nombre', null, 1);
            $cuenta = trim((string) ($retencionSri->ret_cta_compras ?? ''));

            if ($cuenta === '') {
                throw new \RuntimeException("La retención {$detalle->retd_codigo_sri} no tiene cuenta de compras.");
            }

            $valor = (float) $detalle->retd_valor_retenido;

            if ($valor <= 0) {
                throw new \RuntimeException('El valor retenido debe ser mayor a cero.');
            }

            $cuentas[$cuenta] = ($cuentas[$cuenta] ?? 0) + $valor;
            $totalDetalles += $valor;
        }

        if (abs($totalDetalles - (float) $retencion->ret_total_retenido) > 0.01) {
            throw new \RuntimeException('Los detalles no coinciden con el total de la retención.');
        }

        foreach ($cuentas as $cuenta => $valor) {
            $this->asientoLib->guardarDetalleAsiento(
                    $asientoId,
                    $cuenta,
                    round($valor, 4),
                    'HABER',
                    $this->tipoTransaccion,
                    (int) $compra->id,
                    'Retenciones de la compra',
                    null,
                    null,
                    $compra->fk_centro_costo
            );
        }

        return round($totalDetalles, 4);
    }

    protected function guardarCreditoProveedor(int $asientoId, object $compra, float $totalRetenido): void {

        $proveedor = $this->ccm->getData('cc_proveedores', ['id' => $compra->fk_proveedor], 'id, prov_nombres, fk_codigo_cuenta_contable', null, 1);

        if (!$proveedor) {
            throw new \RuntimeException('No se encontró el proveedor de la compra.');
        }

        $cuenta = trim((string) ($proveedor->fk_codigo_cuenta_contable ?? ''));

        if ($cuenta === '') {
            $cuenta = $this->cuentasConfigLib->obtenerSettingCuentaContable('021');

            if (!$cuenta) {
                throw new \RuntimeException("El proveedor {$proveedor->prov_nombres} no tiene cuenta contable y no está configurada la cuenta general 021.");
            }
        }

        $valor = round((float) $compra->comp_total - $totalRetenido, 4);

        if ($valor <= 0) {
            throw new \RuntimeException('El valor por pagar al proveedor debe ser mayor a cero.');
        }

        $this->asientoLib->guardarDetalleAsiento(
                $asientoId,
                $cuenta,
                $valor,
                'HABER',
                $this->tipoTransaccion,
                (int) $compra->id,
                'Cuenta por pagar proveedor',
                null,
                null,
                $compra->fk_centro_costo
        );
    }

    protected function obtenerConfiguracionIva(int $tarifaId): string {

        $tarifa = $this->ccm->getData( 'cc_impuesto_tarifa', ['id' => $tarifaId, 'fk_impuesto' => 1], 'id, impt_porcentage, impt_grupo, impt_estado', null, 1);

        if (!$tarifa || (float) $tarifa->impt_porcentage <= 0) {
            throw new \RuntimeException( "La tarifa IVA {$tarifaId} no existe o no genera IVA." );
        }

        $porcentaje = (float) $tarifa->impt_porcentage;

        if ($tarifa->impt_grupo === 'GENERAL') {
            return '016';
        }

        if ($tarifa->impt_grupo === 'ESPECIAL') {
            return '017';
        }


        throw new \RuntimeException("No existe configuración contable para IVA {$porcentaje}%.");
    }
}
