<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Ventas\Libraries;

use Modules\Comun\Libraries\ProductoLib;
use Modules\Comun\Libraries\StockBodegaLib;

/**
 * Description of VentasLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 9 ago 2026
 * @time 1:11:59 p.m.
 */
class VentasLib {

    protected $ccm;
    protected $user;
    protected ProductoLib $productoLib;
    protected StockBodegaLib $stockBodegaLib;
    protected string $tipoTransaccion = '01';

    public function __construct() {
        $this->ccm = service('ccModel');
        $this->user = service('userSecion');
        $this->productoLib = new ProductoLib();
        $this->stockBodegaLib = new StockBodegaLib();
    }

    public function guardarVenta(object $cartData, object $dataPostVenta): int {

        $venta = $dataPostVenta->venta;
        $pago = $dataPostVenta->pago ?? null;
        $esArchivado = $venta->venEstado === 'ARCHIVADO';
        $formasPagoAts = $dataPostVenta->ats->formasPago ?? [];
        $formaPagoAtsPrincipal = $esArchivado && !empty($formasPagoAts) ? (string) $formasPagoAts[0] : null;
        $ultimo = $this->ccm->getData('cc_ventas', ['fk_proyecto' => getProyectoId()], 'ven_secuencial', ['ven_secuencial' => 'DESC'], 1);
        $secuencial = $ultimo ? (int) $ultimo->ven_secuencial + 1 : 1;
        $numeroComprobante = str_pad(trim((string) $venta->venNumeroComprobante), 9, '0', STR_PAD_LEFT);

        $datos = [
            'ven_secuencial' => $secuencial,
            'fk_cliente' => (int) $venta->venCliente,
            'fk_proyecto' => getProyectoId(),
            'fk_punto_venta' => (int) $venta->venPuntoEmision,
            'ven_numero_establecimiento' => trim((string) $venta->venNumeroEstablecimiento),
            'ven_numero_emision' => trim((string) $venta->venNumeroEmision),
            'ven_numero_comprobante' => $numeroComprobante,
            'ven_autorizacion_sri' => $venta->venAutorizacionSri ?? null,
            'ven_fecha_vencimiento_autorizacion' => $venta->venFechaVenceAutorizacion ?? null,
            'ven_tipo_comprobante_cod' => (string) $venta->venTipoComprobante,
            'ven_fecha_emision' => $venta->venFechaEmision,
            'fk_bodega' => (int) $venta->venBodega,
            'fk_centro_costo' => (int) $venta->venCentroCosto,
            'fk_tipo_venta' => (int) $venta->venTipoVenta,
            'ven_subtotal_bruto' => $cartData->totalSubtotalBruto,
            'ven_descuento_items' => $cartData->totalDescuentoItems,
            'ven_descuento_global' => $cartData->totalDescuentoGlobal,
            'ven_descuento_valor' => round((float) $cartData->totalDescuentoItems + (float) $cartData->totalDescuentoGlobal, 6),
            'ven_subtotal_neto' => $cartData->totalSubtotalNeto,
            'ven_totaliva' => $cartData->totalIva,
            'ven_totalice' => $cartData->totalIce,
            'ven_totalirbpnr' => $cartData->totalIrbpnr,
            'ven_recargo' => $cartData->totalRecargo,
            'ven_servicios_adicionales' => $cartData->totalServiciosAdc,
            'ven_total' => $cartData->totalGeneral,
            'ven_tarifacero_bruto' => $cartData->tarifCeroBruto,
            'ven_tarifacero_neto' => $cartData->tarifCeroNeto,
            'ven_tarifaiva_bruto' => $cartData->tarifIvaBruto,
            'ven_tarifaiva_neto' => $cartData->tarifIvaNeto,
            'ven_base_iva' => $cartData->baseIva,
            'ven_total_excento_impuestos' => $cartData->tarifExcentoNeto,
            'ven_total_no_objeto_impuestos' => $cartData->tarifNoObjetoNeto,
            'cod_forma_pago' => $formaPagoAtsPrincipal,
            'ven_tipo_pago' => $esArchivado ? ($pago->tipoPago ?? null) : null,
            'ven_dias_credito' => $esArchivado && ($pago->tipoPago ?? '') === 'CREDITO' ? ($pago->diasCredito ?? null) : null,
            'ven_num_cuotas' => $esArchivado && ($pago->tipoPago ?? '') === 'CREDITO' ? count($dataPostVenta->cuotas ?? []) : null,
            'ven_items_duplicados' => !empty($venta->venPermitirDuplicados) ? 'true' : 'false',
            'ven_estado' => $venta->venEstado,
            'ven_observacion' => $venta->venObservacion ?? null,
            'fk_user' => $this->user->id,
            'ven_fecha_archivada' => $venta->venEstado === 'ARCHIVADO' ? date('Y-m-d H:i:s') : null,
        ];

        return (int) $this->ccm->guardar($datos, 'cc_ventas');
    }

    public function actualizarVenta(int $ventaId, object $cartData, object $dataPostVenta): bool {

        $venta = $dataPostVenta->venta;
        $pago = $dataPostVenta->pago ?? null;
        $esArchivado = $venta->venEstado === 'ARCHIVADO';
        $formasPagoAts = $dataPostVenta->ats->formasPago ?? [];
        $formaPagoAtsPrincipal = $esArchivado && !empty($formasPagoAts) ? (string) $formasPagoAts[0] : null;
        $numeroComprobante = str_pad(trim((string) $venta->venNumeroComprobante), 9, '0', STR_PAD_LEFT);

        $datos = [
            'fk_cliente' => (int) $venta->venCliente,
            'fk_punto_venta' => (int) $venta->venPuntoEmision,
            'ven_numero_establecimiento' => trim((string) $venta->venNumeroEstablecimiento),
            'ven_numero_emision' => trim((string) $venta->venNumeroEmision),
            'ven_numero_comprobante' => $numeroComprobante,
            'ven_autorizacion_sri' => $venta->venAutorizacionSri ?? null,
            'ven_fecha_vencimiento_autorizacion' => $venta->venFechaVenceAutorizacion ?? null,
            'ven_tipo_comprobante_cod' => (string) $venta->venTipoComprobante,
            'ven_fecha_emision' => $venta->venFechaEmision,
            'fk_bodega' => (int) $venta->venBodega,
            'fk_centro_costo' => (int) $venta->venCentroCosto,
            'fk_tipo_venta' => (int) $venta->venTipoVenta,
            'ven_subtotal_bruto' => $cartData->totalSubtotalBruto,
            'ven_descuento_items' => $cartData->totalDescuentoItems,
            'ven_descuento_global' => $cartData->totalDescuentoGlobal,
            'ven_descuento_valor' => round((float) $cartData->totalDescuentoItems + (float) $cartData->totalDescuentoGlobal, 6),
            'ven_subtotal_neto' => $cartData->totalSubtotalNeto,
            'ven_totaliva' => $cartData->totalIva,
            'ven_totalice' => $cartData->totalIce,
            'ven_totalirbpnr' => $cartData->totalIrbpnr,
            'ven_recargo' => $cartData->totalRecargo,
            'ven_servicios_adicionales' => $cartData->totalServiciosAdc,
            'ven_total' => $cartData->totalGeneral,
            'ven_tarifacero_bruto' => $cartData->tarifCeroBruto,
            'ven_tarifacero_neto' => $cartData->tarifCeroNeto,
            'ven_tarifaiva_bruto' => $cartData->tarifIvaBruto,
            'ven_tarifaiva_neto' => $cartData->tarifIvaNeto,
            'ven_base_iva' => $cartData->baseIva,
            'ven_total_excento_impuestos' => $cartData->tarifExcentoNeto,
            'ven_total_no_objeto_impuestos' => $cartData->tarifNoObjetoNeto,
            'cod_forma_pago' => $formaPagoAtsPrincipal,
            'ven_tipo_pago' => $esArchivado ? ($pago->tipoPago ?? null) : null,
            'ven_dias_credito' => $esArchivado && ($pago->tipoPago ?? '') === 'CREDITO' ? ($pago->diasCredito ?? null) : null,
            'ven_num_cuotas' => $esArchivado && ($pago->tipoPago ?? '') === 'CREDITO' ? count($dataPostVenta->cuotas ?? []) : null,
            'ven_items_duplicados' => !empty($venta->venPermitirDuplicados) ? 'true' : 'false',
            'ven_estado' => $venta->venEstado,
            'ven_observacion' => $venta->venObservacion ?? null,
            'ven_fecha_archivada' => $venta->venEstado === 'ARCHIVADO' ? date('Y-m-d H:i:s') : null,
        ];

        return (bool) $this->ccm->actualizar('cc_ventas', $datos, ['id' => $ventaId, 'fk_proyecto' => getProyectoId(), 'ven_estado' => 'BORRADOR']);
    }

    public function guardarDetalleVenta(int $ventaId, object $item, int $bodegaId, ?int $centroCostoId): int {

        $lote = $this->obtenerLoteSeleccionado($item);
        $producto = $this->ccm->getData('cc_productos', ['id' => (int) $item->id], 'id, prod_isservicio, prod_costopromedio, prod_costoultimo, fk_cuentacontablecompras, fk_cuentacontableventas', null, 1);
        $costoUnitario = 0;

        if ($producto && (int) ($producto->prod_isservicio ?? 0) === 0) {
            $costoUnitario = (float) ($producto->prod_costopromedio ?: $producto->prod_costoultimo ?: 0);
        }

        $datos = [
            'fk_venta' => $ventaId,
            'fk_proyecto' => getProyectoId(),
            'fk_producto' => (int) $item->id,
            'fk_bodega' => $bodegaId,
            'fk_lote' => $lote ? (int) $lote->fk_lote : null,
            'vend_cantidad' => $item->qty,
            'vend_precio_bruto' => $item->price,
            'vend_descuento_valor' => $item->discountValue,
            'vend_descuento_porcentaje' => $item->discountPercent,
            'vend_precio_neto' => $item->priceNeto,
            'vend_total_neto' => $item->subtotalNeto,
            'fk_impuesto_tarifa' => $item->impuestoSelect,
            'vend_impt_codigo' => $item->codigoImpuestoSelect,
            'vend_impt_porcentaje' => $item->ivaPorcent,
            'vend_iva_valor' => $item->ivaValUnit,
            'vend_total_iva_valor' => $item->ivaValTotal,
            'vend_total' => $item->total,
            'vend_costo_unitario' => $costoUnitario,
            'vend_costo_total' => round($costoUnitario * (float) $item->qty, 6),
            'vend_cta_venta' => $item->ctaContableProducto ?? ($producto->fk_cuentacontableventas ?? null),
            'vend_cta_inventario' => $producto->fk_cuentacontablecompras ?? null,
            'vend_cta_costo' => null,
            'vend_centro_costo' => $item->centroCosto ?? $centroCostoId,
            'vend_lote' => $lote->lote ?? null,
            'vend_fecha_elaboracion' => $lote->fechaElaboracion ?? null,
            'vend_fecha_caducidad' => $lote->fechaCaducidad ?? null,
            'vend_estado' => 1,
        ];

        return (int) $this->ccm->guardar($datos, 'cc_ventas_det');
    }

    public function guardarBasesImpuesto(int $ventaId, array $basesImpuesto): int {

        if (!$basesImpuesto) {
            return 0;
        }

        $registrosGuardados = 0;

        foreach ($basesImpuesto as $base) {
            $tarifaId = (int) ($base->impuestoTarifaId ?? $base->impuesto_tarifa_id ?? 0);

            if ($tarifaId <= 0 && (float) ($base->porcentaje ?? 0) > 0) {
                $tarifaId = (int) $this->ccm->getValueWhere('cc_impuesto_tarifa', ['impt_codigo' => (string) $base->codigo, 'impt_porcentage' => (float) $base->porcentaje, 'fk_impuesto' => 1], 'id');
            }

            $formData = [
                'fk_venta' => $ventaId,
                'fk_proyecto' => getProyectoId(),
                'fk_impuesto_tarifa' => $tarifaId > 0 ? $tarifaId : null,
                'imp_codigo' => $base->codigo,
                'imp_detalle' => $base->detalle,
                'imp_porcentaje' => $base->porcentaje,
                'imp_subtotal_bruto' => $base->subtotal_bruto,
                'imp_subtotal_neto' => $base->subtotal_neto,
                'imp_valor' => $base->iva,
            ];

            $this->ccm->guardar($formData, 'cc_ventas_bases_impuesto');
            $registrosGuardados++;
        }

        return $registrosGuardados;
    }

    public function guardarFormasPagoAts(int $ventaId, object $ats): int {

        $formasPago = array_values(array_unique(array_filter((array) ($ats->formasPago ?? []))));

        if (!$formasPago) {
            return 0;
        }

        $registrosGuardados = 0;

        foreach ($formasPago as $codigo) {
            $formaPagoExiste = $this->ccm->getValueWhere('cc_formas_pago_sri', ['codigo' => $codigo, 'fp_estado' => 1], 'codigo');

            if (!$formaPagoExiste) {
                throw new \RuntimeException("La forma de pago ATS {$codigo} no es valida.");
            }

            $formData = [
                'fk_venta' => $ventaId,
                'fk_proyecto' => getProyectoId(),
                'fk_forma_pago_ats' => $codigo,
            ];

            $this->ccm->guardar($formData, 'cc_ventas_ats_formas_pago');
            $registrosGuardados++;
        }

        return $registrosGuardados;
    }

    public function anularVentaBorrador(int $ventaId, string $motivoAnulacion): bool {

        $this->ccm->actualizar('cc_ventas_det', ['vend_estado' => 0], [
            'fk_venta' => $ventaId,
            'fk_proyecto' => getProyectoId(),
        ]);

        return $this->anularVenta($ventaId, 'ANULADA_EN_PENDIENTE', $motivoAnulacion);
    }

    public function anularVentaArchivada(int $ventaId, string $motivoAnulacion): bool {

        $this->ccm->actualizar('cc_ventas_det', ['vend_estado' => 0], [
            'fk_venta' => $ventaId,
            'fk_proyecto' => getProyectoId(),
        ]);

        return $this->anularVenta($ventaId, 'ANULADA_EN_ARCHIVADA', $motivoAnulacion);
    }

    public function revertirKardexVenta(int $ventaId): void {

        $venta = $this->ccm->getData('cc_ventas', ['id' => $ventaId, 'fk_proyecto' => getProyectoId()], 'id, ven_estado', null, 1);

        if (!$venta || $venta->ven_estado !== 'ARCHIVADO') {
            throw new \RuntimeException('Solo una venta archivada puede revertir kardex.');
        }

        $kardexAnulacion = $this->ccm->getData('cc_kardex', ['kar_documento_id' => $ventaId, 'kar_codigo_transaccion' => '08', 'fk_proyecto' => getProyectoId()], 'id', null, 1);

        if ($kardexAnulacion) {
            throw new \RuntimeException('La venta ya tiene kardex de anulacion registrado.');
        }

        $detalle = $this->ccm->getData('cc_ventas_det', ['fk_venta' => $ventaId, 'fk_proyecto' => getProyectoId(), 'vend_estado' => 1], '*');

        if (!$detalle) {
            throw new \RuntimeException('La venta no tiene detalle para revertir kardex.');
        }

        $fecha = date('Y-m-d');
        $hora = date('H:i:s');

        foreach ($detalle as $item) {
            $producto = $this->ccm->getData('cc_productos', ['id' => (int) $item->fk_producto], 'id, prod_nombre, prod_isservicio, prod_costopromedio, prod_costoultimo', null, 1);

            if (!$producto || (int) ($producto->prod_isservicio ?? 0) === 1) {
                continue;
            }

            $this->revertirKardexItemVenta($ventaId, $item, $producto, $fecha, $hora);
        }
    }

    public function generarKardexItemVenta(int $ventaId, object $item, int $bodegaId, string $fecha): void {

        $producto = $this->ccm->getData('cc_productos', ['id' => (int) $item->id], 'id, prod_nombre, prod_isservicio, prod_costopromedio, prod_costoultimo', null, 1);

        if (!$producto || (int) ($producto->prod_isservicio ?? 0) === 1) {
            return;
        }

        $loteId = !empty($item->idLote) ? (int) $item->idLote : null;
        $hora = date('H:i:s');
        $costos = $this->actualizarKardexGeneral($ventaId, $item, $producto, $loteId, $fecha, $hora, $bodegaId);

        if (!$costos['kardexId']) {
            throw new \RuntimeException("No se pudo registrar el kardex general del producto {$producto->prod_nombre}.");
        }

        $kardexBodegaId = $this->actualizarKardexBodega($ventaId, $item, $producto, $loteId, $fecha, $hora, $bodegaId, $costos);

        if (!$kardexBodegaId) {
            throw new \RuntimeException("No se pudo registrar el kardex por bodega del producto {$producto->prod_nombre}.");
        }

        if ($loteId) {
            $kardexLoteId = $this->actualizarKardexBodegaLote($ventaId, $item, $producto, $loteId, $fecha, $hora, $bodegaId, $costos);

            if (!$kardexLoteId) {
                throw new \RuntimeException("No se pudo registrar el kardex por lote del producto {$producto->prod_nombre}.");
            }
        }
    }

    private function actualizarKardexGeneral(int $ventaId, object $item, object $producto, ?int $loteId, string $fecha, string $hora, int $bodegaId): array {

        $cantidad = (float) $item->qty;
        $costoPromedio = (float) ($producto->prod_costopromedio ?: $producto->prod_costoultimo ?: 0);
        $costoUltimo = (float) ($producto->prod_costoultimo ?: $costoPromedio);
        $totalCosto = round($costoPromedio * $cantidad, 6);
        $stockActual = $this->productoLib->getStockProducto((int) $item->id);
        $nuevoStock = $stockActual - $cantidad;
        $costoInvProducto = $this->productoLib->getCostoInventarioProducto((int) $item->id);
        $nuevoCostoInvProducto = $costoInvProducto - $totalCosto;
        $costoInvTotal = $this->productoLib->getCostoInventarioTotal();
        $nuevoCostoInvTotal = $costoInvTotal - $totalCosto;

        if ($nuevoStock < -0.0001) {
            throw new \RuntimeException("Stock insuficiente para vender el producto {$producto->prod_nombre}: stock actual {$stockActual}, solicitado {$cantidad}");
        }

        if ($nuevoCostoInvProducto < 0 && abs($nuevoCostoInvProducto) <= 0.0001) {
            $nuevoCostoInvProducto = 0;
        }

        if ($nuevoCostoInvTotal < 0 && abs($nuevoCostoInvTotal) <= 0.0001) {
            $nuevoCostoInvTotal = 0;
        }

        $dataKardex = [
            'fk_producto' => (int) $item->id,
            'fk_proyecto' => getProyectoId(),
            'kar_kardex' => -$cantidad,
            'kar_kardex_total' => $nuevoStock,
            'kar_costo_promedio' => $costoPromedio,
            'kar_costo_ultimo' => $costoUltimo,
            'kar_total_costo' => abs($totalCosto),
            'kar_documento_id' => $ventaId,
            'kar_codigo_transaccion' => $this->tipoTransaccion,
            'kar_fecha' => $fecha,
            'kar_hora' => $hora,
            'kar_costoinventario_producto' => $nuevoCostoInvProducto,
            'kar_costoinventario_total' => $nuevoCostoInvTotal,
            'fk_bodega' => $bodegaId,
            'fk_lote' => $loteId,
            'fk_user_id' => $this->user->id,
        ];

        $kardexId = $this->ccm->guardar($dataKardex, 'cc_kardex');

        if ($kardexId) {
            $this->ccm->actualizar('cc_productos', [
                'prod_stockactual' => $nuevoStock,
                'prod_costoinventario' => $nuevoStock <= 0 ? 0 : $nuevoCostoInvProducto,
            ], ['id' => (int) $item->id]);

            $this->productoLib->actualizarCostoInventarioTotal($nuevoCostoInvTotal);
        }

        return [
            'kardexId' => $kardexId,
            'costoPromedio' => $costoPromedio,
            'costoUltimo' => $costoUltimo,
        ];
    }

    private function actualizarKardexBodega(int $ventaId, object $item, object $producto, ?int $loteId, string $fecha, string $hora, int $bodegaId, array $costos): int {

        $cantidad = (float) $item->qty;
        $stockBodega = $this->stockBodegaLib->getStockBodega($bodegaId, (int) $item->id);
        $nuevoStockBodega = $stockBodega - $cantidad;

        if ($nuevoStockBodega < -0.0001) {
            throw new \RuntimeException("Stock insuficiente en bodega para vender el producto {$producto->prod_nombre}.");
        }

        $dataKardexBodega = [
            'fk_producto' => (int) $item->id,
            'fk_proyecto' => getProyectoId(),
            'fk_bodega' => $bodegaId,
            'karb_kardex' => -$cantidad,
            'karb_kardex_total' => $nuevoStockBodega,
            'karb_costo_promedio' => $costos['costoPromedio'],
            'karb_costo_ultimo' => $costos['costoUltimo'],
            'karb_documento_id' => $ventaId,
            'karb_codigo_transaccion' => $this->tipoTransaccion,
            'karb_fecha' => $fecha,
            'karb_hora' => $hora,
            'fk_lote' => $loteId,
            'fk_user_id' => $this->user->id,
        ];

        $kardexBodegaId = (int) $this->ccm->guardar($dataKardexBodega, 'cc_kardex_bodega');

        if ($kardexBodegaId) {
            $this->stockBodegaLib->actualizarStockBodega($bodegaId, (int) $item->id, $nuevoStockBodega);
        }

        return $kardexBodegaId;
    }

    private function actualizarKardexBodegaLote(int $ventaId, object $item, object $producto, int $loteId, string $fecha, string $hora, int $bodegaId, array $costos): int {

        $cantidad = (float) $item->qty;
        $stockBodegaLote = $this->stockBodegaLib->getStockBodegaLote($bodegaId, (int) $item->id, $loteId);
        $nuevoStockBodegaLote = $stockBodegaLote - $cantidad;

        if ($nuevoStockBodegaLote < -0.0001) {
            throw new \RuntimeException("Stock insuficiente en el lote seleccionado del producto {$producto->prod_nombre}.");
        }

        $dataKardexLote = [
            'fk_producto' => (int) $item->id,
            'fk_proyecto' => getProyectoId(),
            'fk_bodega' => $bodegaId,
            'fk_lote' => $loteId,
            'karbl_kardex' => -$cantidad,
            'karbl_kardex_total' => $nuevoStockBodegaLote,
            'karbl_costo_promedio' => $costos['costoPromedio'],
            'karbl_costo_ultimo' => $costos['costoUltimo'],
            'karbl_documento_id' => $ventaId,
            'karbl_codigo_transaccion' => $this->tipoTransaccion,
            'karbl_fecha' => $fecha,
            'karbl_hora' => $hora,
            'fk_user_id' => $this->user->id,
        ];

        $kardexLoteId = (int) $this->ccm->guardar($dataKardexLote, 'cc_kardex_bodega_lote');

        if ($kardexLoteId) {
            $this->stockBodegaLib->actualizarStockBodegaLote($bodegaId, (int) $item->id, $loteId, $nuevoStockBodegaLote);
        }

        return $kardexLoteId;
    }

    private function obtenerLoteSeleccionado(object $item): ?object {

        if ((int) ($item->tieneLote ?? 0) !== 1 || empty($item->idLote)) {
            return null;
        }

        foreach (($item->lotes ?? []) as $lote) {
            if ((int) ($lote->fk_lote ?? 0) === (int) $item->idLote) {
                return $lote;
            }
        }

        return null;
    }

    private function anularVenta(int $ventaId, string $estado, string $motivoAnulacion): bool {

        $datos = [
            'ven_estado' => $estado,
            'ven_fecha_anulacion' => date('Y-m-d H:i:s'),
            'ven_motivo_anulacion' => $motivoAnulacion,
            'fk_user_anulacion' => $this->user->id,
        ];

        return (bool) $this->ccm->actualizar('cc_ventas', $datos, [
            'id' => $ventaId,
            'fk_proyecto' => getProyectoId(),
        ]);
    }

    private function revertirKardexItemVenta(int $ventaId, object $item, object $producto, string $fecha, string $hora): void {

        $productoId = (int) $item->fk_producto;
        $bodegaId = (int) $item->fk_bodega;
        $loteId = !empty($item->fk_lote) ? (int) $item->fk_lote : null;
        $cantidad = abs((float) $item->vend_cantidad);
        $costoTotal = abs((float) $item->vend_costo_total);
        $costoPromedio = (float) ($producto->prod_costopromedio ?: $item->vend_costo_unitario ?: 0);
        $costoUltimo = (float) ($producto->prod_costoultimo ?: $costoPromedio);
        $stockActual = $this->productoLib->getStockProducto($productoId);
        $nuevoStock = $stockActual + $cantidad;
        $costoInvProducto = $this->productoLib->getCostoInventarioProducto($productoId);
        $nuevoCostoInvProducto = $costoInvProducto + $costoTotal;
        $costoInvTotal = $this->productoLib->getCostoInventarioTotal();
        $nuevoCostoInvTotal = $costoInvTotal + $costoTotal;

        $this->ccm->guardar([
            'fk_producto' => $productoId,
            'fk_proyecto' => getProyectoId(),
            'kar_kardex' => $cantidad,
            'kar_kardex_total' => $nuevoStock,
            'kar_costo_promedio' => $costoPromedio,
            'kar_costo_ultimo' => $costoUltimo,
            'kar_total_costo' => $costoTotal,
            'kar_documento_id' => $ventaId,
            'kar_codigo_transaccion' => '08',
            'kar_fecha' => $fecha,
            'kar_hora' => $hora,
            'kar_costoinventario_producto' => $nuevoCostoInvProducto,
            'kar_costoinventario_total' => $nuevoCostoInvTotal,
            'fk_bodega' => $bodegaId,
            'fk_lote' => $loteId,
            'fk_user_id' => $this->user->id,
        ], 'cc_kardex');

        $this->ccm->actualizar('cc_productos', [
            'prod_stockactual' => $nuevoStock,
            'prod_costoinventario' => $nuevoCostoInvProducto,
        ], ['id' => $productoId]);

        $this->productoLib->actualizarCostoInventarioTotal($nuevoCostoInvTotal);

        $stockBodega = $this->stockBodegaLib->getStockBodega($bodegaId, $productoId);
        $nuevoStockBodega = $stockBodega + $cantidad;

        $this->ccm->guardar([
            'fk_producto' => $productoId,
            'fk_proyecto' => getProyectoId(),
            'fk_bodega' => $bodegaId,
            'karb_kardex' => $cantidad,
            'karb_kardex_total' => $nuevoStockBodega,
            'karb_costo_promedio' => $costoPromedio,
            'karb_costo_ultimo' => $costoUltimo,
            'karb_documento_id' => $ventaId,
            'karb_codigo_transaccion' => '08',
            'karb_fecha' => $fecha,
            'karb_hora' => $hora,
            'fk_lote' => $loteId,
            'fk_user_id' => $this->user->id,
        ], 'cc_kardex_bodega');

        $this->stockBodegaLib->actualizarStockBodega($bodegaId, $productoId, $nuevoStockBodega);

        if ($loteId) {
            $stockBodegaLote = $this->stockBodegaLib->getStockBodegaLote($bodegaId, $productoId, $loteId);
            $nuevoStockBodegaLote = $stockBodegaLote + $cantidad;

            $this->ccm->guardar([
                'fk_producto' => $productoId,
                'fk_proyecto' => getProyectoId(),
                'fk_bodega' => $bodegaId,
                'fk_lote' => $loteId,
                'karbl_kardex' => $cantidad,
                'karbl_kardex_total' => $nuevoStockBodegaLote,
                'karbl_costo_promedio' => $costoPromedio,
                'karbl_costo_ultimo' => $costoUltimo,
                'karbl_documento_id' => $ventaId,
                'karbl_codigo_transaccion' => '08',
                'karbl_fecha' => $fecha,
                'karbl_hora' => $hora,
                'fk_user_id' => $this->user->id,
            ], 'cc_kardex_bodega_lote');

            $this->stockBodegaLib->actualizarStockBodegaLote($bodegaId, $productoId, $loteId, $nuevoStockBodegaLote);
        }
    }
}
