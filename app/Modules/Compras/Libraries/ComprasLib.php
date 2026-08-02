<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Compras\Libraries;

use Modules\Comun\Libraries\ProductoLib;
use Modules\Comun\Libraries\StockBodegaLib;
use Modules\Compras\Models\ComprasModel;

/**
 * Description of ComprasLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 5 jul 2026
 * @time 10:23:49 a.m.
 */
class ComprasLib {

    protected $ccm;
    protected $user;
    protected ComprasModel $comprasModel;
    protected ProductoLib $productoLib;
    protected StockBodegaLib $stockBodegaLib;
    protected string $tipoTransaccion = '02';

    public function __construct() {
        $this->ccm = service('ccModel');
        $this->user = service('userSecion');
        $this->comprasModel = new ComprasModel();
        $this->productoLib = new ProductoLib();
        $this->stockBodegaLib = new StockBodegaLib();
    }

    public function guardarCompra(object $cartData, object $dataPostCompra): int {

        $compra = $dataPostCompra->compra;
        $esArchivado = $compra->compEstado === 'ARCHIVADO';
        $retencion = $dataPostCompra->retencion ?? null;

        $aplicaRetencion = $esArchivado && $retencion && !empty($retencion->aplica) && empty($retencion->noSujeto);

        $ultimo = $this->ccm->getData('cc_compras', ['fk_proyecto' => getProyectoId()], 'comp_secuencial', ['comp_secuencial' => 'DESC'], 1);

        $secuencial = $ultimo ? (int) $ultimo->comp_secuencial + 1 : 1;

        $numeroComprobante = str_pad(trim((string) $compra->compNumeroComprobante), 9, '0', STR_PAD_LEFT);

        $datos = [
            'comp_secuencial' => $secuencial,
            'fk_proveedor' => $compra->compProveedor,
            'comp_numero_comprobante' => $numeroComprobante,
            'comp_numero_establecimiento' => $compra->compNumeroEstablecimiento,
            'comp_numero_emision' => $compra->compNumeroEmision,
            'comp_autorizacion_sri' => $compra->compAutSRI,
            'comp_fecha_vencimiento_autorizacion' => $compra->compFechaCaducidad,
            'comp_tipo_comprobante_cod' => $compra->compTipoComprobante,
            'comp_fecha_emision' => $compra->compFechaEmision,
            'fk_bodega' => $compra->compBodega,
            'fk_centro_costo' => $compra->compCentroCosto,
            'fk_tipo_compra' => $compra->compTipoCompra,
            'cod_sustento' => $compra->compSustento,
            'comp_es_gasto' => (int) $compra->compEsGasto,
            'comp_es_activo_fijo' => $this->tieneActivoFijo($cartData->cartContent ?? []),
            'comp_subtotal_bruto' => $cartData->totalSubtotalBruto,
            'comp_descuento_items' => $cartData->totalDescuentoItems,
            'comp_descuento_global' => $cartData->totalDescuentoGlobal,
            'comp_descuento_valor' => round($cartData->totalDescuentoItems + $cartData->totalDescuentoGlobal, 6),
            'comp_subtotal_neto' => $cartData->totalSubtotalNeto,
            'comp_totaliva' => $cartData->totalIva,
            'comp_totalice' => $cartData->totalIce,
            'comp_recargo' => $cartData->totalRecargo,
            'comp_servicios_adicionales' => $cartData->totalServiciosAdc,
            'comp_total' => $cartData->totalGeneral,
            'comp_tarifacero_bruto' => $cartData->tarifCeroBruto,
            'comp_tarifacero_neto' => $cartData->tarifCeroNeto,
            'comp_tarifaiva_bruto' => $cartData->tarifIvaBruto,
            'comp_tarifaiva_neto' => $cartData->tarifIvaNeto,
            'comp_subtotal_bienes_bruto' => $cartData->totalBienesBruto,
            'comp_subtotal_bienes_neto' => $cartData->totalBienesNeto,
            'comp_subtotal_servicios_bruto' => $cartData->totalServiciosBruto,
            'comp_subtotal_servicios_neto' => $cartData->totalServiciosNeto,
            'comp_base_iva' => $cartData->baseIva,
            'comp_aplica_retencion' => (int) $aplicaRetencion,
            'fk_retencion' => null,
            'comp_asume_retencion' => $aplicaRetencion ? $retencion->asumir : null,
            'cod_forma_pago' => $esArchivado ? ($compra->compFormaPago ?? null) : null,
            'comp_tipo_pago' => $esArchivado ? ($compra->compTipoPago ?? null) : null,
            'comp_dias_credito' => $esArchivado ? ($compra->compDiasCredito ?? null) : null,
            'comp_num_cuotas' => $esArchivado ? ($compra->compCuotas ?? null) : null,
            'comp_items_duplicados' => !empty($compra->compPermitirDuplicados) ? 'true' : 'false',
            'comp_estado' => $compra->compEstado,
            'fk_orden_compra' => $compra->compODC ?? null,
            'fk_user' => $this->user->id,
            'comp_observacion' => $compra->compObservaciones ?? null,
            'tipo_costo' => $compra->compTipoCosto,
            'comp_pago_residente' => $dataPostCompra->ats->residente ?? null,
            'fk_proyecto' => getProyectoId(),
            'comp_fecha_archivada' => $esArchivado ? date('Y-m-d H:i:s') : null,
            'comp_total_excento_impuestos' => $cartData->tarifExcentoNeto,
            'comp_total_no_objeto_impuestos' => $cartData->tarifNoObjetoNeto,
            'comp_totalirbpnr' => $cartData->totalIrbpnr,
        ];

        return (int) $this->ccm->guardar($datos, 'cc_compras');
    }

    public function actualizarCompra(int $compraId, object $cartData, object $dataPostCompra): bool {

        $compra = $dataPostCompra->compra;
        $esArchivado = $compra->compEstado === 'ARCHIVADO';
        $retencion = $dataPostCompra->retencion ?? null;

        $aplicaRetencion = $esArchivado && $retencion && !empty($retencion->aplica) && empty($retencion->noSujeto);
        $numeroComprobante = str_pad(trim((string) $compra->compNumeroComprobante), 9, '0', STR_PAD_LEFT);

        $datos = [
            'fk_proveedor' => $compra->compProveedor,
            'comp_numero_comprobante' => $numeroComprobante,
            'comp_numero_establecimiento' => $compra->compNumeroEstablecimiento,
            'comp_numero_emision' => $compra->compNumeroEmision,
            'comp_autorizacion_sri' => $compra->compAutSRI,
            'comp_fecha_vencimiento_autorizacion' => $compra->compFechaCaducidad,
            'comp_tipo_comprobante_cod' => $compra->compTipoComprobante,
            'comp_fecha_emision' => $compra->compFechaEmision,
            'fk_bodega' => $compra->compBodega,
            'fk_centro_costo' => $compra->compCentroCosto,
            'fk_tipo_compra' => $compra->compTipoCompra,
            'cod_sustento' => $compra->compSustento,
            'comp_es_gasto' => (int) $compra->compEsGasto,
            'comp_es_activo_fijo' => $this->tieneActivoFijo($cartData->cartContent ?? []),
            'comp_subtotal_bruto' => $cartData->totalSubtotalBruto,
            'comp_descuento_items' => $cartData->totalDescuentoItems,
            'comp_descuento_global' => $cartData->totalDescuentoGlobal,
            'comp_descuento_valor' => round($cartData->totalDescuentoItems + $cartData->totalDescuentoGlobal, 6),
            'comp_subtotal_neto' => $cartData->totalSubtotalNeto,
            'comp_totaliva' => $cartData->totalIva,
            'comp_totalice' => $cartData->totalIce,
            'comp_totalirbpnr' => $cartData->totalIrbpnr,
            'comp_recargo' => $cartData->totalRecargo,
            'comp_servicios_adicionales' => $cartData->totalServiciosAdc,
            'comp_total' => $cartData->totalGeneral,
            'comp_tarifacero_bruto' => $cartData->tarifCeroBruto,
            'comp_tarifacero_neto' => $cartData->tarifCeroNeto,
            'comp_tarifaiva_bruto' => $cartData->tarifIvaBruto,
            'comp_tarifaiva_neto' => $cartData->tarifIvaNeto,
            'comp_subtotal_bienes_bruto' => $cartData->totalBienesBruto,
            'comp_subtotal_bienes_neto' => $cartData->totalBienesNeto,
            'comp_subtotal_servicios_bruto' => $cartData->totalServiciosBruto,
            'comp_subtotal_servicios_neto' => $cartData->totalServiciosNeto,
            'comp_base_iva' => $cartData->baseIva,
            'comp_total_excento_impuestos' => $cartData->tarifExcentoNeto,
            'comp_total_no_objeto_impuestos' => $cartData->tarifNoObjetoNeto,
            'comp_aplica_retencion' => (int) $aplicaRetencion,
            'fk_retencion' => null,
            'comp_asume_retencion' => $aplicaRetencion ? $retencion->asumir : null,
            'cod_forma_pago' => $esArchivado ? ($compra->compFormaPago ?? null) : null,
            'comp_tipo_pago' => $esArchivado ? ($compra->compTipoPago ?? null) : null,
            'comp_dias_credito' => $esArchivado ? ($compra->compDiasCredito ?? null) : null,
            'comp_num_cuotas' => $esArchivado ? ($compra->compCuotas ?? null) : null,
            'comp_items_duplicados' => !empty($compra->compPermitirDuplicados) ? 'true' : 'false',
            'comp_estado' => $compra->compEstado,
            'fk_orden_compra' => $compra->compODC ?? null,
            'fk_user' => $this->user->id,
            'comp_observacion' => $compra->compObservaciones ?? null,
            'tipo_costo' => $compra->compTipoCosto,
            'comp_pago_residente' => $dataPostCompra->ats->residente ?? null,
            'fk_proyecto' => getProyectoId(),
            'comp_fecha_archivada' => $esArchivado ? date('Y-m-d H:i:s') : null,
        ];

        return (bool) $this->ccm->actualizar('cc_compras', $datos, ['id' => $compraId, 'fk_proyecto' => getProyectoId(), 'comp_estado' => 'BORRADOR',]);
    }

    public function anularCompraBorrador(int $compraId, string $motivoAnulacion): bool {
        $datos = [
            'comp_estado' => 'ANULADA_EN_PENDIENTE',
            'comp_fecha_anulacion' => date('Y-m-d H:i:s'),
            'comp_motivo_anulacion' => trim($motivoAnulacion),
            'fk_user_anulacion' => $this->user->id,
        ];

        return (bool) $this->ccm->actualizar('cc_compras', $datos, ['id' => $compraId, 'fk_proyecto' => getProyectoId(), 'comp_estado' => 'BORRADOR']);
    }

    public function anularCompraArchivada(int $compraId, string $motivoAnulacion): bool {
        $datos = [
            'comp_estado' => 'ANULADA_EN_ARCHIVADA',
            'comp_fecha_anulacion' => date('Y-m-d H:i:s'),
            'comp_motivo_anulacion' => trim($motivoAnulacion),
            'fk_user_anulacion' => $this->user->id,
        ];

        return (bool) $this->ccm->actualizar('cc_compras', $datos, ['id' => $compraId, 'fk_proyecto' => getProyectoId(), 'comp_estado' => 'ARCHIVADO']);
    }

    public function obtenerOCrearLote(int $compraId, object $item): ?int {
        if ((int) ($item->tieneLote ?? 0) !== 1) {
            return null;
        }

        if (empty($item->lote) || empty($item->fechaElaboracion) || empty($item->fechaCaducidad)) {
            throw new \InvalidArgumentException("Debe completar los campos lote y fechas para el producto {$item->name}.");
        }

        $lote = $this->ccm->getData('cc_lotes', ['lot_lote' => trim($item->lote), 'fk_producto' => (int) $item->id,], '*', null, 1);

        if ($lote) {
            return (int) $lote->id;
        }

        $formData = [
            'lot_lote' => trim($item->lote),
            'lot_fecha_elaboracion' => $item->fechaElaboracion,
            'lot_fecha_caducidad' => $item->fechaCaducidad,
            'lot_documento_id' => $compraId,
            'fk_producto' => $item->id,
        ];
        return (int) $this->ccm->guardar($formData, 'cc_lotes');
    }

    public function guardarDetalleCompra(int $compraId, object $item, int $bodegaId, string $sustento, ?int $loteId): int {

        $precioConIce = (float) $item->priceNeto + (float) $item->iceValUnit;
        $totalPrecioConIce = (float) $item->subtotalNeto + (float) $item->iceValTotal;

        $datos = [
            'fk_compra' => $compraId,
            'fk_proyecto' => getProyectoId(),
            'fk_producto' => $item->id,
            'fk_bodega' => $bodegaId,
            'compd_cantidad' => $item->qty,
            'compd_precio_bruto' => $item->price,
            'compd_descuento_valor' => $item->discountValue,
            'compd_descuento_porcentaje' => $item->discountPercent,
            'compd_precio_neto' => $item->priceNeto,
            'compd_total_neto' => $item->subtotalNeto,
            'compd_ice_porcentaje' => $item->icePorcent,
            'compd_ice_valor' => $item->iceValUnit,
            'compd_total_ice_valor' => $item->iceValTotal,
            'compd_precio_con_ice' => $precioConIce,
            'compd_total_precio_con_ice' => $totalPrecioConIce,
            'fk_impuesto_tarifa' => $item->impuestoSelect,
            'compd_impt_codigo' => $item->codigoImpuestoSelect,
            'compd_valor_iva' => $item->ivaValTotal,
            'compd_impt_porcentaje' => $item->ivaPorcent,
            'compd_iva_valor' => $item->ivaValUnit,
            'compd_total_iva_valor' => $item->ivaValTotal,
            'compd_precio_con_iva' => $item->priceIva,
            'compd_total_precio_con_iva' => $item->totalPriceIva,
            'compd_base_iva' => $item->itemBaseIvaUnit,
            'compd_total_base_iva' => $item->itemBaseIvaTotal,
            'compd_irbpnr' => $item->irbpnrUnitario,
            'compd_irbpnr_total' => $item->irbpnr_total,
            'compd_total' => $item->total,
            'compd_cta_entrada' => $item->ctaContableProducto,
            'compd_cod_sustento' => $sustento,
            'compd_centro_costo' => $item->centroCosto,
            'fk_lote' => $loteId,
            'compd_lote' => $loteId ? $item->lote : null,
            'compd_fecha_caducidad' => $loteId ? $item->fechaCaducidad : null,
            'compd_fecha_elaboracion' => $loteId ? $item->fechaElaboracion : null,
            'compd_impuesto_seleccionado' => $item->impuestoSelect,
            'compd_estado' => 1,
        ];

        return (int) $this->ccm->guardar($datos, 'cc_compras_det');
    }

    public function guardarProductoProveedor(int $proveedorId, object $item): void {
        $codigoProveedor = trim((string) ($item->codigoImport ?? ''));

        if ($proveedorId <= 0 || $codigoProveedor === '') {
            return;
        }

        if ((int) ($item->isNewProduct ?? 0) !== 1 || (int) ($item->productoTemporal ?? 0) === 1) {
            return;
        }

        $relacion = $this->ccm->getData('cc_producto_proveedor', ['fk_proveedor' => $proveedorId, 'codigo_proveedor' => $codigoProveedor,], 'id, fk_producto', null, 1);

        if ($relacion) {
            if ((int) $relacion->fk_producto !== (int) $item->id) {
                $this->ccm->actualizar('cc_producto_proveedor', ['fk_producto' => (int) $item->id], ['id' => (int) $relacion->id]);
            }
            return;
        }

        $formData = [
            'fk_producto' => (int) $item->id,
            'fk_proveedor' => $proveedorId,
            'codigo_proveedor' => $codigoProveedor,
        ];
        $this->ccm->guardar($formData, 'cc_producto_proveedor');
    }

    public function guardarBasesImpuesto(int $compraId, array $basesImpuesto): int {

        $basesIva = array_values(array_filter($basesImpuesto, static fn($base) => (float) ($base->porcentaje ?? 0) > 0));

        if (!$basesIva) {
            return 0;
        }

        $registrosGuardados = 0;

        foreach ($basesIva as $base) {
            $tarifaId = (int) ($base->impuestoTarifaId ?? $base->impuesto_tarifa_id ?? 0);

            if ($tarifaId <= 0) {
                $tarifaId = (int) $this->ccm->getValueWhere('cc_impuesto_tarifa', ['impt_codigo' => (string) $base->codigo, 'impt_porcentage' => (float) $base->porcentaje, 'fk_impuesto' => 1,], 'id');
            }

            if (!$tarifaId) {
                throw new \RuntimeException("No se encontró la tarifa de impuesto " . "{$base->codigo} - {$base->porcentaje}%.");
            }

            $formData = [
                'fk_compra' => $compraId,
                'fk_proyecto' => getProyectoId(),
                'fk_impuesto_tarifa' => (int) $tarifaId,
                'imp_codigo' => $base->codigo,
                'imp_detalle' => $base->detalle,
                'imp_porcentaje' => $base->porcentaje,
                'subtotal_bruto' => $base->subtotal_bruto,
                'subtotal_neto' => $base->subtotal_neto,
                'iva_valor' => $base->iva,
                'tipo_impuesto' => 'IVA',
                'estado' => 1,
            ];

            $this->ccm->guardar($formData, 'cc_compras_bases_impuesto');
            $registrosGuardados++;
        }

        return $registrosGuardados;
    }

    public function guardarFormasPagoAts(int $compraId, object $ats): int {

        $formasPago = array_values(array_unique(array_filter((array) ($ats->formasPago ?? []))));

        if (!$formasPago) {
            return 0;
        }

        $registrosGuardados = 0;

        foreach ($formasPago as $codigo) {
            $formaPagoExiste = $this->ccm->getValueWhere('cc_formas_pago_sri', ['codigo' => $codigo, 'fp_estado' => 1,], 'codigo');

            if (!$formaPagoExiste) {
                throw new \RuntimeException("La forma de pago ATS {$codigo} no es válida.");
            }

            $formData = [
                'fk_compra' => $compraId,
                'fk_proyecto' => getProyectoId(),
                'fk_forma_pago_ats' => $codigo,
            ];

            $this->ccm->guardar($formData, 'cc_compras_ats_formas_pago');
            $registrosGuardados++;
        }

        return $registrosGuardados;
    }

    public function generarKardex(int $compraId, object $item, ?int $loteId, object $compra): void {

        try {
            if ((int) ($item->servicio ?? 0) === 1) {
                return;
            }

            $fecha = $compra->compFechaEmision;
            $hora = date('H:i:s');
            $bodegaId = (int) $compra->compBodega;

            $costos = $this->actualizarKardexGeneral($compraId, $item, $loteId, $fecha, $hora, $bodegaId);

            $this->actualizarKardexBodega($compraId, $item, $loteId, $fecha, $hora, $bodegaId, $costos);

            if ($loteId) {
                $this->actualizarKardexBodegaLote($compraId, $item, $loteId, $fecha, $hora, $bodegaId, $costos);
            }
        } catch (\Throwable $e) {
            throw new \RuntimeException("Error al generar kardex para {$item->name}: " . $e->getMessage(), 0, $e);
        }
    }

    public function revertirKardexCompra(int $compraId): void {
        $compra = $this->ccm->getData('cc_compras', ['id' => $compraId, 'fk_proyecto' => getProyectoId()], 'id, comp_estado', null, 1);

        if (!$compra || $compra->comp_estado !== 'ARCHIVADO') {
            throw new \RuntimeException('Solo una compra archivada puede revertir kardex.');
        }

        $kardexAnulacion = $this->ccm->getData('cc_kardex', ['kar_documento_id' => $compraId, 'kar_codigo_transaccion' => '09'], 'id', null, 1);

        if ($kardexAnulacion) {
            throw new \RuntimeException('La compra ya tiene kardex de anulacion registrado.');
        }

        $detalle = $this->ccm->getData('cc_compras_det', ['fk_compra' => $compraId, 'fk_proyecto' => getProyectoId(), 'compd_estado' => 1], '*');

        if (!$detalle) {
            throw new \RuntimeException('La compra no tiene detalle para revertir kardex.');
        }

        $fecha = date('Y-m-d');
        $hora = date('H:i:s');

        try {
            $this->tipoTransaccion = '09';

            foreach ($detalle as $item) {
                $producto = $this->ccm->getData('cc_productos', ['id' => $item->fk_producto], 'id, prod_isservicio', null, 1);

                if ($producto && (int) $producto->prod_isservicio === 1) {
                    continue;
                }

                $loteId = !empty($item->fk_lote) ? (int) $item->fk_lote : null;
                $bodegaId = (int) $item->fk_bodega;

                $validarStock = $this->stockBodegaLib->validarStockDisponible((int) $item->fk_producto, $bodegaId, abs((float) $item->compd_cantidad), null, null, $loteId);

                if ($validarStock['status'] !== 'success') {
                    throw new \RuntimeException("No se puede anular la compra porque el producto {$item->fk_producto} no tiene stock suficiente.<br>{$validarStock['msg']}");
                }

                // Armamos el producto para el movimiento inverso
                $itemKardex = (object) [
                            'id' => (int) $item->fk_producto,
                            'name' => 'Producto ' . $item->fk_producto,
                            'qty' => -abs((float) $item->compd_cantidad),
                            'priceNeto' => (float) $item->compd_precio_neto,
                            'subtotalNeto' => -abs((float) $item->compd_total_neto),
                ];

                $costos = $this->actualizarKardexGeneral($compraId, $itemKardex, $loteId, $fecha, $hora, $bodegaId);

                $this->actualizarKardexBodega($compraId, $itemKardex, $loteId, $fecha, $hora, $bodegaId, $costos);

                if ($loteId) {
                    $this->actualizarKardexBodegaLote($compraId, $itemKardex, $loteId, $fecha, $hora, $bodegaId, $costos);
                }
            }
        } catch (\Throwable $e) {
            throw new \RuntimeException('Error al revertir kardex de la compra: ' . $e->getMessage(), 0, $e);
        } finally {
            $this->tipoTransaccion = '02';
        }
    }

    private function actualizarKardexGeneral(int $compraId, object $item, ?int $loteId, string $fecha, string $hora, int $bodegaId): array {

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
            'kar_documento_id' => $compraId,
            'kar_codigo_transaccion' => $this->tipoTransaccion,
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

    private function actualizarKardexBodega(int $compraId, object $item, ?int $loteId, string $fecha, string $hora, int $bodegaId, array $costos): int {
        $stockBodega = (float) $this->stockBodegaLib->getStockBodega($bodegaId, $item->id);
        $nuevoStockBodega = $stockBodega + (float) $item->qty;

        $formData = [
            'fk_producto' => $item->id,
            'fk_bodega' => $bodegaId,
            'karb_kardex' => $item->qty,
            'karb_kardex_total' => $nuevoStockBodega,
            'karb_costo_promedio' => $costos['costoPromedio'],
            'karb_costo_ultimo' => $costos['costoUltimo'],
            'karb_documento_id' => $compraId,
            'karb_codigo_transaccion' => $this->tipoTransaccion,
            'karb_fecha' => $fecha,
            'karb_hora' => $hora,
            'fk_lote' => $loteId,
            'fk_user_id' => $this->user->id,
        ];

        $kardexBodegaId = (int) $this->ccm->guardar($formData, 'cc_kardex_bodega');

        $this->stockBodegaLib->actualizarStockBodega($bodegaId, $item->id, $nuevoStockBodega);

        return $kardexBodegaId;
    }

    private function actualizarKardexBodegaLote(int $compraId, object $item, int $loteId, string $fecha, string $hora, int $bodegaId, array $costos): int {

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
            'karbl_documento_id' => $compraId,
            'karbl_codigo_transaccion' => $this->tipoTransaccion,
            'karbl_fecha' => $fecha,
            'karbl_hora' => $hora,
            'fk_user_id' => $this->user->id,
        ];

        $kardexLoteId = (int) $this->ccm->guardar($formData, 'cc_kardex_bodega_lote');

        $this->stockBodegaLib->actualizarStockBodegaLote($bodegaId, $item->id, $loteId, $nuevoStockBodegaLote
        );

        return $kardexLoteId;
    }

    private function tieneActivoFijo(array $detalle): int {
        $productoIds = array_values(array_unique(array_filter(array_map(
                                        static fn($item) => (int) ($item->id ?? 0),
                                        $detalle
        ))));

        if (!$productoIds) {
            return 0;
        }

        return $this->comprasModel->existeActivoFijo($productoIds) ? 1 : 0;
    }
}
