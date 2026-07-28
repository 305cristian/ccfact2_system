<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\NotasCredito\Libraries;

use Modules\Comun\Libraries\ProductoLib;
use Modules\Comun\Libraries\StockBodegaLib;

/**
 * Description of NotaCreditoLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 15 jul 2026
 * @time 3:04:14 p.m.
 */
class NotaCreditoLib {

    protected $ccm;
    protected $user;
    protected ProductoLib $productoLib;
    protected StockBodegaLib $stockBodegaLib;
    protected string $tipoTransaccionKardex = '11';
    protected string $tipoTransaccionKardexAnulacion = '36';

    public function __construct() {
        $this->ccm = service('ccModel');
        $this->user = service('userSecion');
        $this->productoLib = new ProductoLib();
        $this->stockBodegaLib = new StockBodegaLib();
    }

    public function guardarNotaCredito(object $dataPostNotaCredito): int {

        $compra = $dataPostNotaCredito->compra;
        $totales = $dataPostNotaCredito->totales;
        $bases = $dataPostNotaCredito->basesImpuestos ?? [];
        $resumenBases = $this->resumirBasesImpuesto($bases);
        $observacion = trim((string) ($compra->compObservaciones ?? ''));
        $destinoFinanciero = $compra->destinoFinanciero ?? null;
        $observacionFinanciera = trim((string) ($compra->observacionFinanciera ?? ''));

        $observacionDestino = 'Destino financiero NDC: ' . ($destinoFinanciero === 'CXP' ? 'Cuenta por pagar' : 'Anticipo a proveedor');
        if ($observacionFinanciera !== '') {
            $observacionDestino .= '. ' . $observacionFinanciera;
        }

        $observacion = trim($observacion . ($observacion !== '' ? ' | ' : '') . $observacionDestino);

        $ultimo = $this->ccm->getData('cc_compras', null, 'comp_secuencial', ['comp_secuencial' => 'DESC'], 1);
        $secuencial = $ultimo ? (int) $ultimo->comp_secuencial + 1 : 1;

        $datos = [
            'comp_secuencial' => $secuencial,
            'fk_proveedor' => (int) $compra->compProveedor,
            'comp_numero_comprobante' => str_pad(trim((string) $compra->compNumeroComprobante), 9, '0', STR_PAD_LEFT),
            'comp_numero_establecimiento' => trim((string) $compra->compNumeroEstablecimiento),
            'comp_numero_emision' => trim((string) $compra->compNumeroEmision),
            'comp_autorizacion_sri' => trim((string) $compra->compAutSRI),
            'comp_fecha_vencimiento_autorizacion' => $compra->compFechaCaducidad,
            'comp_tipo_comprobante_cod' => $compra->compTipoComprobante,
            'comp_fecha_emision' => $compra->compFechaEmision,
            'fk_bodega' => (int) $compra->compBodega,
            'fk_centro_costo' => $compra->compCentroCosto ?? null,
            'fk_tipo_compra' => $compra->compTipoCompra ?? null,
            'cod_sustento' => $compra->compSustento,
            'comp_es_gasto' => (int) ($compra->compEsGasto ?? 0),
            'comp_es_activo_fijo' => (int) ($compra->compEsActivoFijo ?? 0),
            'comp_subtotal_bruto' => (float) $totales->subtotal,
            'comp_descuento_items' => 0,
            'comp_descuento_global' => 0,
            'comp_descuento_valor' => 0,
            'comp_subtotal_neto' => (float) $totales->subtotal,
            'comp_totaliva' => (float) $totales->iva,
            'comp_totalice' => 0,
            'comp_recargo' => 0,
            'comp_servicios_adicionales' => 0,
            'comp_total' => (float) $totales->total,
            'comp_tarifacero_bruto' => $resumenBases['tarifa_cero'],
            'comp_tarifacero_neto' => $resumenBases['tarifa_cero'],
            'comp_tarifaiva_bruto' => $resumenBases['tarifa_iva'],
            'comp_tarifaiva_neto' => $resumenBases['tarifa_iva'],
            'comp_subtotal_bienes_bruto' => (float) $totales->subtotal,
            'comp_subtotal_bienes_neto' => (float) $totales->subtotal,
            'comp_subtotal_servicios_bruto' => 0,
            'comp_subtotal_servicios_neto' => 0,
            'comp_base_iva' => $resumenBases['tarifa_iva'],
            'comp_aplica_retencion' => 0,
            'fk_retencion' => null,
            'comp_asume_retencion' => null,
            'cod_forma_pago' => null,
            'comp_tipo_pago' => null,
            'comp_dias_credito' => null,
            'comp_num_cuotas' => null,
            'comp_items_duplicados' => 'false',
            'comp_estado' => 'ARCHIVADO',
            'fk_orden_compra' => null,
            'fk_user' => $this->user->id,
            'comp_observacion' => $observacion,
            'tipo_costo' => $compra->compTipoCosto ?? null,
            'fk_compra_relacionada' => (int) $compra->compraRelacionadaId,
            'comp_tipo_nota_credito' => $compra->compTipoNotaCredito,
            'comp_pago_residente' => null,
            'fk_proyecto' => $compra->compProyecto ?? null,
            'comp_fecha_archivada' => date('Y-m-d H:i:s'),
            'comp_total_excento_impuestos' => 0,
            'comp_total_no_objeto_impuestos' => 0,
            'comp_totalirbpnr' => 0,
        ];

        return (int) $this->ccm->guardar($datos, 'cc_compras');
    }

    public function guardarDetalleNotaCredito(int $compraId, object $item, object $compra): int {

        $cantidad = (float) $item->cantidadNdc;
        $subtotal = (float) $item->subtotalNdc;
        $iva = (float) $item->ivaNdc;
        $total = (float) $item->totalNdc;
        $precioUnitarioCredito = $cantidad > 0 ? round($subtotal / $cantidad, 6) : 0;
        $ivaUnitario = $cantidad > 0 ? round($iva / $cantidad, 6) : 0;
        $totalUnitario = $cantidad > 0 ? round($total / $cantidad, 6) : 0;

        $datos = [
            'fk_compra' => $compraId,
            'fk_producto' => (int) $item->productoId,
            'fk_bodega' => (int) $compra->compBodega,
            'compd_cantidad' => $cantidad,
            'compd_precio_bruto' => $precioUnitarioCredito,
            'compd_descuento_valor' => 0,
            'compd_descuento_porcentaje' => 0,
            'compd_precio_neto' => $precioUnitarioCredito,
            'compd_total_neto' => $subtotal,
            'compd_ice_porcentaje' => 0,
            'compd_ice_valor' => 0,
            'compd_total_ice_valor' => 0,
            'compd_precio_con_ice' => $precioUnitarioCredito,
            'compd_total_precio_con_ice' => $subtotal,
            'fk_impuesto_tarifa' => $item->impuestoTarifaId ?? null,
            'compd_impt_codigo' => $item->impuestoCodigo ?? null,
            'compd_valor_iva' => $iva,
            'compd_impt_porcentaje' => (float) ($item->ivaPorcentaje ?? 0),
            'compd_iva_valor' => $ivaUnitario,
            'compd_total_iva_valor' => $iva,
            'compd_precio_con_iva' => $totalUnitario,
            'compd_total_precio_con_iva' => $total,
            'compd_base_iva' => $precioUnitarioCredito,
            'compd_total_base_iva' => $subtotal,
            'compd_irbpnr' => 0,
            'compd_irbpnr_total' => 0,
            'compd_total' => $total,
            'compd_cta_entrada' => $item->cuentaContable,
            'compd_cod_sustento' => $compra->compSustento,
            'compd_centro_costo' => $compra->compCentroCosto ?? null,
            'fk_lote' => $item->loteId ?? null,
            'compd_lote' => $item->lote ?? null,
            'compd_fecha_caducidad' => $item->fechaCaducidad ?? null,
            'compd_fecha_elaboracion' => $item->fechaElaboracion ?? null,
            'compd_impuesto_seleccionado' => $item->impuestoTarifaId ?? null,
            'compd_estado' => 1,
            'fk_compra_det_relacionada' => $item->compraDetalleRelacionadaId ?? null,
        ];

        return (int) $this->ccm->guardar($datos, 'cc_compras_det');
    }

    public function guardarBasesImpuesto(int $compraId, array $basesImpuesto): int {

        if (!$basesImpuesto) {
            return 0;
        }

        $registrosGuardados = 0;

        foreach ($basesImpuesto as $base) {
            $tarifaId = $base->fk_impuesto_tarifa ?? null;

            if (!$tarifaId) {
                $tarifaId = $this->ccm->getValueWhere('cc_impuesto_tarifa', ['impt_codigo' => (string) $base->codigo, 'impt_porcentage' => (float) $base->porcentaje, 'fk_impuesto' => 1], 'id');
            }

            if (!$tarifaId) {
                throw new \RuntimeException("No se encontro la tarifa de impuesto " . "{$base->codigo} - {$base->porcentaje}%.");
            }

            $formData = [
                'fk_compra' => $compraId,
                'fk_impuesto_tarifa' => (int) $tarifaId,
                'imp_codigo' => (string) $base->codigo,
                'imp_detalle' => $base->detalle,
                'imp_porcentaje' => (float) $base->porcentaje,
                'subtotal_bruto' => (float) $base->subtotal_bruto,
                'subtotal_neto' => (float) $base->subtotal_neto,
                'iva_valor' => (float) $base->iva,
                'tipo_impuesto' => 'IVA',
                'estado' => 1,
            ];

            $this->ccm->guardar($formData, 'cc_compras_bases_impuesto');
            $registrosGuardados++;
        }

        return $registrosGuardados;
    }

    public function aplicarNotaCreditoCuentaPorPagar(int $notaCreditoId, object $dataPostNotaCredito): void {

        $compra = $dataPostNotaCredito->compra;
        $valorNotaCredito = round((float) ($dataPostNotaCredito->totales->total ?? 0), 4);

        if ($valorNotaCredito <= 0) {
            throw new \RuntimeException('El valor de la nota de credito debe ser mayor a cero para aplicar a CxP.');
        }

        $cxp = $this->ccm->getData('cc_cxp', ['fk_compra' => (int) $compra->compraRelacionadaId], '*', null, 1);

        if (!$cxp) {
            throw new \RuntimeException('La compra relacionada no tiene cuenta por pagar registrada.');
        }

        if (!in_array($cxp->cxp_estado, ['PENDIENTE', 'PARCIAL'], true)) {
            throw new \RuntimeException('La cuenta por pagar relacionada no tiene saldo pendiente aplicable.');
        }

        $saldoActual = round((float) $cxp->cxp_saldo, 4);

        if ($valorNotaCredito > $saldoActual) {
            throw new \RuntimeException('El valor de la nota de credito no puede superar el saldo pendiente de la cuenta por pagar.');
        }

        $pagoNdcId = $this->guardarMovimientoPagoNotaCredito($notaCreditoId, $cxp, $compra, $valorNotaCredito);

        $nuevoSaldo = round($saldoActual - $valorNotaCredito, 4);
        $nuevoPagado = round((float) $cxp->cxp_valor_pagado + $valorNotaCredito, 4);
        $nuevoEstado = $nuevoSaldo <= 0 ? 'PAGADO' : 'PARCIAL';
        $observacion_ = trim((string) ($cxp->cxp_observacion ?? ''));
        $observacionNdc = "NDC #{$notaCreditoId} aplicada por " . number_format($valorNotaCredito, 4, '.', '');
        $observacion = trim($observacion_ . ($observacion_ !== '' ? ' | ' : '') . $observacionNdc);

        $dataSet = [
            'cxp_valor_pagado' => $nuevoPagado,
            'cxp_saldo' => $nuevoSaldo,
            'cxp_fecha_ultimo_pago' => $compra->compFechaEmision,
            'cxp_estado' => $nuevoEstado,
            'cxp_observacion' => $observacion,
        ];
        $this->ccm->actualizar('cc_cxp', $dataSet, ['id' => (int) $cxp->id]);

        $this->aplicarNotaCreditoCuotas((int) $cxp->id, $valorNotaCredito, $notaCreditoId, $pagoNdcId);
    }

    public function generarKardexItemNotaCredito(int $notaCreditoId, object $item, object $compra): void {

        if (($compra->compTipoNotaCredito ?? null) !== 'DEVOLUCION') {
            return;
        }

        $productoId = (int) ($item->productoId ?? 0);
        $producto = $this->ccm->getData('cc_productos', ['id' => $productoId], 'id, prod_nombre, prod_isservicio', null, 1);

        if (!$producto) {
            throw new \RuntimeException("No se encontro el producto {$productoId}.");
        }

        if ((int) $producto->prod_isservicio === 1) {
            return;
        }

        $loteId = !empty($item->loteId) ? (int) $item->loteId : null;
        $bodegaId = (int) $compra->compBodega;
        $cantidad = abs((float) ($item->cantidadNdc ?? 0));

        $validarStock = $this->stockBodegaLib->validarStockDisponible($productoId, $bodegaId, $cantidad, null, null, $loteId);

        if ($validarStock['status'] !== 'success') {
            throw new \RuntimeException("No se puede registrar la nota de credito porque el producto {$producto->prod_nombre} no tiene stock suficiente.<br>{$validarStock['msg']}");
        }

        $itemKardex = (object) [
                    'id' => $productoId,
                    'name' => $producto->prod_nombre,
                    'qty' => -$cantidad,
                    'priceNeto' => (float) ($item->precioNdc ?? 0),
                    'subtotalNeto' => -abs((float) ($item->subtotalNdc ?? 0)),
        ];

        $fecha = $compra->compFechaEmision;
        $hora = date('H:i:s');

        $costos = $this->actualizarKardexGeneral($notaCreditoId, $itemKardex, $loteId, $fecha, $hora, $bodegaId);

        $this->actualizarKardexBodega($notaCreditoId, $itemKardex, $loteId, $fecha, $hora, $bodegaId, $costos);

        if ($loteId) {
            $this->actualizarKardexBodegaLote($notaCreditoId, $itemKardex, $loteId, $fecha, $hora, $bodegaId, $costos);
        }
    }

    public function anularAplicacionCuentaPorPagarNotaCredito(int $notaCreditoId): void {

        $pagoNdc = $this->ccm->getData('cc_pagos', ['pg_tipo_movimiento' => 'NDC_COMPRA', 'fk_compra_nota_credito' => $notaCreditoId, 'pg_estado' => 'ACTIVO',], '*', null, 1);

        if (!$pagoNdc) {
            return;
        }

        $detallesPago = $this->ccm->getData('cc_pagos_det', ['fk_pago' => (int) $pagoNdc->id], '*');

        if (!$detallesPago) {
            throw new \RuntimeException('No se encontraron detalles de aplicacion para anular la nota de credito.');
        }

        foreach ($detallesPago as $detallePago) {
            if (!empty($detallePago->fk_cuota)) {
                $this->revertirCuotaNotaCredito($detallePago);
            }
        }

        $cxpId = (int) $detallesPago[0]->fk_cxp;
        $cxp = $this->ccm->getData('cc_cxp', ['id' => $cxpId], '*', null, 1);

        if (!$cxp) {
            throw new \RuntimeException('No se encontro la cuenta por pagar vinculada a la nota de credito.');
        }

        $valorNotaCredito = round((float) $pagoNdc->pg_valor, 4);
        $nuevoPagado = round((float) $cxp->cxp_valor_pagado - $valorNotaCredito, 4);
        $nuevoSaldo = round((float) $cxp->cxp_saldo + $valorNotaCredito, 4);

        if ($nuevoPagado < 0) {
            throw new \RuntimeException('No se puede anular la nota de credito porque el valor pagado de la CxP quedaria negativo.');
        }
        $dataSet = [
            'cxp_valor_pagado' => $nuevoPagado,
            'cxp_saldo' => $nuevoSaldo,
            'cxp_estado' => $this->resolverEstadoSaldo($nuevoPagado, $nuevoSaldo),
            'cxp_observacion' => $this->agregarObservacion($cxp->cxp_observacion ?? '', "Anulacion NDC #{$notaCreditoId} por " . number_format($valorNotaCredito, 4, '.', '')),
        ];
        $this->ccm->actualizar('cc_cxp', $dataSet,['id' => $cxpId]);

        $this->ccm->actualizar('cc_pagos', ['pg_estado' => 'ANULADO'], ['id' => (int) $pagoNdc->id]);
    }

    public function revertirKardexNotaCredito(int $notaCreditoId): void {

        $notaCredito = $this->ccm->getData('cc_compras', ['id' => $notaCreditoId], 'id, comp_estado, comp_tipo_nota_credito, comp_fecha_emision', null, 1);

        if (!$notaCredito) {
            throw new \RuntimeException('No se encontro la nota de credito.');
        }

        if ($notaCredito->comp_tipo_nota_credito !== 'DEVOLUCION') {
            return;
        }

        $kardexAnulacion = $this->ccm->getData('cc_kardex', ['kar_documento_id' => $notaCreditoId, 'kar_codigo_transaccion' => $this->tipoTransaccionKardexAnulacion], 'id', null, 1);

        if ($kardexAnulacion) {
            throw new \RuntimeException('La nota de credito ya tiene kardex de anulacion registrado.');
        }

        $detalle = $this->ccm->getData('cc_compras_det', ['fk_compra' => $notaCreditoId, 'compd_estado' => 1], '*');

        if (!$detalle) {
            throw new \RuntimeException('La nota de credito no tiene detalle para revertir kardex.');
        }

        $fecha = date('Y-m-d');
        $hora = date('H:i:s');

        foreach ($detalle as $item) {
            $producto = $this->ccm->getData('cc_productos', ['id' => $item->fk_producto], 'id, prod_nombre, prod_isservicio', null, 1);

            if (!$producto) {
                throw new \RuntimeException("No se encontro el producto {$item->fk_producto}.");
            }

            if ((int) $producto->prod_isservicio === 1) {
                continue;
            }

            $loteId = !empty($item->fk_lote) ? (int) $item->fk_lote : null;
            $bodegaId = (int) $item->fk_bodega;
            $cantidad = abs((float) $item->compd_cantidad);

            $itemKardex = (object) [
                        'id' => (int) $item->fk_producto,
                        'name' => $producto->prod_nombre,
                        'qty' => $cantidad,
                        'priceNeto' => (float) $item->compd_precio_neto,
                        'subtotalNeto' => abs((float) $item->compd_total_neto),
            ];

            $costos = $this->actualizarKardexGeneral($notaCreditoId, $itemKardex, $loteId, $fecha, $hora, $bodegaId, $this->tipoTransaccionKardexAnulacion);

            $this->actualizarKardexBodega($notaCreditoId, $itemKardex, $loteId, $fecha, $hora, $bodegaId, $costos, $this->tipoTransaccionKardexAnulacion);

            if ($loteId) {
                $this->actualizarKardexBodegaLote($notaCreditoId, $itemKardex, $loteId, $fecha, $hora, $bodegaId, $costos, $this->tipoTransaccionKardexAnulacion);
            }
        }
    }

    public function anularNotaCreditoArchivada(int $notaCreditoId, string $motivoAnulacion): bool {

        $dataSet = [
            'comp_estado' => 'ANULADA_EN_ARCHIVADA',
            'comp_fecha_anulacion' => date('Y-m-d H:i:s'),
            'comp_motivo_anulacion' => trim($motivoAnulacion),
            'fk_user_anulacion' => $this->user->id,
        ];
        return (bool) $this->ccm->actualizar('cc_compras', $dataSet, ['id' => $notaCreditoId, 'comp_estado' => 'ARCHIVADO', 'comp_tipo_comprobante_cod' => '04',]);
    }

    private function guardarMovimientoPagoNotaCredito(int $notaCreditoId, object $cxp, object $compra, float $valorNotaCredito): int {

        $movimientoExistente = $this->ccm->getData('cc_pagos', ['pg_tipo_movimiento' => 'NDC_COMPRA', 'fk_compra_nota_credito' => $notaCreditoId, 'pg_estado' => 'ACTIVO',], 'id', null, 1);

        if ($movimientoExistente) {
            throw new \RuntimeException('La nota de credito ya tiene un movimiento financiero activo aplicado a CxP.');
        }

        $ultimoPago = $this->ccm->getData('cc_pagos', null, 'pg_numero_secuencial', ['id' => 'DESC'], 1);
        $secuencial = $ultimoPago ? (int) $ultimoPago->pg_numero_secuencial + 1 : 1;

        $dataPago = [
            'fk_proveedor' => (int) $cxp->fk_proveedor,
            'pg_numero_secuencial' => (string) $secuencial,
            'pg_tipo_movimiento' => 'NDC_COMPRA',
            'fk_compra_nota_credito' => $notaCreditoId,
            'pg_fecha' => $compra->compFechaEmision,
            'fk_forma_pago' => null,
            'fk_cuenta_contable' => null,
            'fk_banco' => null,
            'pg_referencia' => "NDC #{$notaCreditoId}",
            'pg_valor' => $valorNotaCredito,
            'pg_estado' => 'ACTIVO',
            'pg_observacion' => 'Aplicacion de nota de credito de compra a CxP.',
            'fk_user' => $this->user->id,
        ];

        $pagoNdcId = (int) $this->ccm->guardar($dataPago, 'cc_pagos');

        if (!$pagoNdcId) {
            throw new \RuntimeException('No se pudo registrar el movimiento financiero de la nota de credito.');
        }

        return $pagoNdcId;
    }

    private function aplicarNotaCreditoCuotas(int $cxpId, float $valorNotaCredito, int $notaCreditoId, int $pagoNdcId): void {

        $cuotas = $this->ccm->getData('cc_cxp_cuotas', ['fk_cxp' => $cxpId], '*', ['cxpc_numero' => 'ASC']);

        if (!$cuotas) {
            $this->guardarDetalleMovimientoPagoNotaCredito($pagoNdcId, $cxpId, null, $valorNotaCredito);
            return;
        }

        $saldoAplicar = round($valorNotaCredito, 4);

        foreach ($cuotas as $cuota) {
            if ($saldoAplicar <= 0) {
                break;
            }

            if (!in_array($cuota->cxpc_estado, ['PENDIENTE', 'PARCIAL'], true)) {
                continue;
            }

            $saldoCuota = round((float) $cuota->cxpc_saldo, 4);

            if ($saldoCuota <= 0) {
                continue;
            }

            $valorAplicado = min($saldoAplicar, $saldoCuota);
            $nuevoSaldoCuota = round($saldoCuota - $valorAplicado, 4);
            $nuevoPagadoCuota = round((float) $cuota->cxpc_pagado + $valorAplicado, 4);
            $nuevoEstadoCuota = $nuevoSaldoCuota <= 0 ? 'PAGADO' : 'PARCIAL';

            $dataSet = [
                'cxpc_pagado' => $nuevoPagadoCuota,
                'cxpc_saldo' => $nuevoSaldoCuota,
                'cxpc_estado' => $nuevoEstadoCuota,
            ];
            $this->ccm->actualizar('cc_cxp_cuotas', $dataSet, ['id' => (int) $cuota->id]);
            $this->guardarDetalleMovimientoPagoNotaCredito($pagoNdcId, $cxpId, (int) $cuota->id, $valorAplicado);

            $saldoAplicar = round($saldoAplicar - $valorAplicado, 4);
        }

        if ($saldoAplicar > 0.0001) {
            throw new \RuntimeException("No se pudo aplicar completamente la NDC #{$notaCreditoId} a las cuotas de la CxP.");
        }
    }

    private function guardarDetalleMovimientoPagoNotaCredito(int $pagoNdcId, int $cxpId, ?int $cuotaId, float $valorAplicado): int {

        $valorAplicado = round($valorAplicado, 4);

        if ($valorAplicado <= 0) {
            throw new \RuntimeException('El valor aplicado por la nota de credito debe ser mayor a cero.');
        }

        $dataPagoDet = [
            'fk_pago' => $pagoNdcId,
            'fk_cxp' => $cxpId,
            'fk_cuota' => $cuotaId,
            'pgd_valor' => $valorAplicado,
        ];

        $pagoDetId = (int) $this->ccm->guardar($dataPagoDet, 'cc_pagos_det');

        if (!$pagoDetId) {
            throw new \RuntimeException('No se pudo registrar el detalle de aplicacion de la nota de credito.');
        }

        return $pagoDetId;
    }

    private function revertirCuotaNotaCredito(object $detallePago): void {

        $cuota = $this->ccm->getData('cc_cxp_cuotas', ['id' => (int) $detallePago->fk_cuota], '*', null, 1);

        if (!$cuota) {
            throw new \RuntimeException("No se encontro la cuota {$detallePago->fk_cuota} vinculada a la nota de credito.");
        }

        $valorAplicado = round((float) $detallePago->pgd_valor, 4);
        $nuevoPagado = round((float) $cuota->cxpc_pagado - $valorAplicado, 4);
        $nuevoSaldo = round((float) $cuota->cxpc_saldo + $valorAplicado, 4);

        if ($nuevoPagado < 0) {
            throw new \RuntimeException("No se puede anular la nota de credito porque la cuota {$cuota->cxpc_numero} quedaria con pagado negativo.");
        }

        $this->ccm->actualizar(
                'cc_cxp_cuotas',
                [
                    'cxpc_pagado' => $nuevoPagado,
                    'cxpc_saldo' => $nuevoSaldo,
                    'cxpc_estado' => $this->resolverEstadoSaldo($nuevoPagado, $nuevoSaldo),
                ],
                ['id' => (int) $cuota->id]
        );
    }

    private function resolverEstadoSaldo(float $pagado, float $saldo): string {

        if (round($pagado, 4) <= 0) {
            return 'PENDIENTE';
        }

        if (round($saldo, 4) <= 0) {
            return 'PAGADO';
        }

        return 'PARCIAL';
    }

    private function agregarObservacion(string $observacionActual, string $observacionNueva): string {

        $observacionActual = trim($observacionActual);

        return trim($observacionActual . ($observacionActual !== '' ? ' | ' : '') . $observacionNueva);
    }

    private function actualizarKardexGeneral(int $notaCreditoId, object $item, ?int $loteId, string $fecha, string $hora, int $bodegaId, ?string $tipoTransaccion = null): array {

        $cantidad = (float) $item->qty;
        $costoUnitario = (float) $item->priceNeto;
        $costoTotal = (float) $item->subtotalNeto;

        if ($cantidad == 0.0) {
            throw new \RuntimeException("La cantidad del producto {$item->name} no puede ser 0.");
        }

        if ($costoUnitario <= 0.0) {
            throw new \RuntimeException("El costo unitario del producto {$item->name} no puede ser 0.");
        }

        $stockActual = (float) $this->productoLib->getStockProducto($item->id);
        $nuevoStock = $stockActual + $cantidad;

        $costoInventarioProducto = (float) $this->productoLib->getCostoInventarioProducto($item->id);
        $nuevoCostoInventarioProducto = $costoInventarioProducto + $costoTotal;

        $costoInventarioTotal = (float) $this->productoLib->getCostoInventarioTotal();
        $nuevoCostoInventarioTotal = $costoInventarioTotal + $costoTotal;

        $costoPromedioActual = (float) $this->productoLib->getCostoPromedio($item->id);
        $costoUltimoActual = (float) $this->productoLib->getCostoUltimo($item->id);

        $costoPromedio = $nuevoStock > 0 ? $nuevoCostoInventarioProducto / $nuevoStock : $costoPromedioActual;
        $costoUltimoProducto = $costoUnitario > 0 ? $costoUnitario : $costoUltimoActual;

        $formData = [
            'fk_producto' => $item->id,
            'kar_kardex' => $cantidad,
            'kar_kardex_total' => $nuevoStock,
            'kar_costo_promedio' => $costoPromedio,
            'kar_costo_ultimo' => $costoUltimoProducto,
            'kar_total_costo' => abs($costoTotal),
            'kar_documento_id' => $notaCreditoId,
            'kar_codigo_transaccion' => $tipoTransaccion ?? $this->tipoTransaccionKardex,
            'kar_fecha' => $fecha,
            'kar_hora' => $hora,
            'kar_costoinventario_producto' => $nuevoCostoInventarioProducto,
            'kar_costoinventario_total' => $nuevoCostoInventarioTotal,
            'fk_bodega' => $bodegaId,
            'fk_lote' => $loteId,
            'fk_user_id' => $this->user->id,
        ];

        $kardexId = (int) $this->ccm->guardar($formData, 'cc_kardex');

        $this->productoLib->updateCostosProducto($item->id, $nuevoStock, $costoPromedio, $costoUltimoProducto, $nuevoCostoInventarioProducto);
        $this->productoLib->actualizarCostoInventarioTotal($nuevoCostoInventarioTotal);

        return [
            'kardexId' => $kardexId,
            'costoPromedio' => $costoPromedio,
            'costoUltimo' => $costoUltimoProducto,
        ];
    }

    private function actualizarKardexBodega(int $notaCreditoId, object $item, ?int $loteId, string $fecha, string $hora, int $bodegaId, array $costos, ?string $tipoTransaccion = null): int {

        $stockBodega = (float) $this->stockBodegaLib->getStockBodega($bodegaId, $item->id);
        $nuevoStockBodega = $stockBodega + (float) $item->qty;

        $formData = [
            'fk_producto' => $item->id,
            'fk_bodega' => $bodegaId,
            'karb_kardex' => $item->qty,
            'karb_kardex_total' => $nuevoStockBodega,
            'karb_costo_promedio' => $costos['costoPromedio'],
            'karb_costo_ultimo' => $costos['costoUltimo'],
            'karb_documento_id' => $notaCreditoId,
            'karb_codigo_transaccion' => $tipoTransaccion ?? $this->tipoTransaccionKardex,
            'karb_fecha' => $fecha,
            'karb_hora' => $hora,
            'fk_lote' => $loteId,
            'fk_user_id' => $this->user->id,
        ];

        $kardexBodegaId = (int) $this->ccm->guardar($formData, 'cc_kardex_bodega');

        $this->stockBodegaLib->actualizarStockBodega($bodegaId, $item->id, $nuevoStockBodega);

        return $kardexBodegaId;
    }

    private function actualizarKardexBodegaLote(int $notaCreditoId, object $item, int $loteId, string $fecha, string $hora, int $bodegaId, array $costos, ?string $tipoTransaccion = null): int {

        $stockBodegaLote = (float) $this->stockBodegaLib->getStockBodegaLote($bodegaId, $item->id, $loteId);
        $nuevoStockBodegaLote = $stockBodegaLote + (float) $item->qty;

        $formData = [
            'fk_producto' => $item->id,
            'fk_bodega' => $bodegaId,
            'fk_lote' => $loteId,
            'karbl_kardex' => $item->qty,
            'karbl_kardex_total' => $nuevoStockBodegaLote,
            'karbl_costo_promedio' => $costos['costoPromedio'],
            'karbl_costo_ultimo' => $costos['costoUltimo'],
            'karbl_documento_id' => $notaCreditoId,
            'karbl_codigo_transaccion' => $tipoTransaccion ?? $this->tipoTransaccionKardex,
            'karbl_fecha' => $fecha,
            'karbl_hora' => $hora,
            'fk_user_id' => $this->user->id,
        ];

        $kardexLoteId = (int) $this->ccm->guardar($formData, 'cc_kardex_bodega_lote');

        $this->stockBodegaLib->actualizarStockBodegaLote($bodegaId, $item->id, $loteId, $nuevoStockBodegaLote);

        return $kardexLoteId;
    }

    private function resumirBasesImpuesto(array $bases): array {

        $tarifaCero = 0;
        $tarifaIva = 0;

        foreach ($bases as $base) {
            if ((float) ($base->porcentaje ?? 0) > 0) {
                $tarifaIva += (float) ($base->subtotal_neto ?? 0);
            } else {
                $tarifaCero += (float) ($base->subtotal_neto ?? 0);
            }
        }

        return [
            'tarifa_cero' => $tarifaCero,
            'tarifa_iva' => $tarifaIva,
        ];
    }
}
