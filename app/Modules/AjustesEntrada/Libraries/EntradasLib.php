<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\AjustesEntrada\Libraries;

/**
 * Description of EntradasLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 17 oct 2025
 * @time 5:19:30 p.m.
 */
use Modules\Comun\Libraries\ProductoLib;
use Modules\Comun\Libraries\StockBodegaLib;

class EntradasLib {

    protected $ccm;
    protected $user;
    protected $tipotransaccionCod = '39';
    protected $productLib;
    protected $stockBodLib;

    public function __construct() {

        //Import Servicios
        $this->ccm = service('ccModel');
        $this->user = service('userSecion');

        //Import Librerias
        $this->productLib = new ProductoLib();
        $this->stockBodLib = new StockBodegaLib();
    }

    public function saveAjuste($cartData, $dataAjuste) {

        $esPendiente = ($dataAjuste->ajenEstado == 1);
        $secuencial = $this->ccm->getData('cc_ajuste_entrada', null, 'ajen_secuencial', ['ajen_secuencial' => 'DESC'], 1);

        $datos = [
            'ajen_secuencial' => (isset($secuencial) ? $secuencial->ajen_secuencial + 1 : 1),
            'ajen_fecha' => $dataAjuste->ajenFecha,
            'ajen_observaciones' => $dataAjuste->ajenObservaciones,
            'ajen_estado' => $dataAjuste->ajenEstado,
            'ajen_tipo' => $dataAjuste->ajenTipo,
            'ajen_fecha_anulacion' => null,
            'ajen_motivo_anulacion' => null,
            'fk_user_anulacion' => null,
            'fk_motivo_ajuste' => $dataAjuste->ajenMotivo,
            'fk_bodega' => $dataAjuste->ajenBodega,
            'fk_user_id' => $this->user->id,
            'ajen_fecha_aprobacion' => $esPendiente ? null : date('Y-m-d H:i:s'),
            'fk_user_id_aprueba' => $esPendiente ? null : $this->user->id,
            'fk_proveedor' => $dataAjuste->ajenProveedor,
            'fk_centro_costo' => $dataAjuste->ajenCentrocosto,
            'codigo_sustento' => $dataAjuste->ajenSustento,
            'iva_porcentaje' => getSettings('IVA'),
            'ajen_total_items' => $cartData->totalItems,
            'ajen_total' => $cartData->totalCart,
            'ajen_subtotal_bienes' => $cartData->totalBienes,
            'ajen_subtotal_servicios' => $cartData->totalServicios,
            'ajen_totalcartiva' => $cartData->totalCartIva,
            'ajen_totaliva' => $cartData->totalIva,
            'ajen_tarifacero' => $cartData->tarifCero,
            'ajen_tarifaiva' => $cartData->tarifIva,
            'ajen_tarifacero_neto' => $cartData->tarifCeroNeto,
            'ajen_tarifaiva_neto' => $cartData->tarifIvaNeto,
        ];

        $save = $this->ccm->guardar($datos, 'cc_ajuste_entrada');

        return $save;
    }

    public function saveAjusteDetalle($ajusteId, $val, $lote) {

        $datos = [
            'fk_ajuste_entrada' => $ajusteId,
            'fk_producto' => $val->id,
            'fk_lote' => $lote,
            'ajend_itemcantidad' => $val->qty,
            'ajend_itemcosto' => $val->price,
            'ajend_itemcostoxcantidad' => $val->total,
            'ajend_observacion' => null,
            'ajend_estado' => 1,
        ];

        $saveDetalle = $this->ccm->guardar($datos, 'cc_ajuste_entrada_det');

        return $saveDetalle;
    }

    /**
     * Actualiza todo el kardex (general, bodega y lote)
     * 
     * @param int $ajusteId ID del documento (ajuste, compra, venta, etc)
     * @param array $producto Array con datos del producto ['id', 'qty', 'price', 'total']
     * @param int|null $loteId ID del lote (null si no maneja lotes)
     * @param array $dataAjuste (fecha, estado, bodega, etc)
     * @return bool
     */
    public function updateKardex($ajusteId, $producto, $loteId, $dataAjuste) {
        try {
            $fecha = $dataAjuste->ajenFecha ?? date('Y-m-d');
            $hora = date('H:i:s');
            $bodegaId = $dataAjuste->ajenBodega;

            // 1. Actualizar kardex general
            $kardex = $this->actualizarKardexGeneral($producto, $ajusteId, $loteId, $fecha, $hora, $bodegaId);
            if (!$kardex['kardexId']) {
                return [
                    'status' => 'error',
                    'msg' => 'Error al actualizar kardex general.',
                ];
            }

            // 2. Actualizar kardex por bodega
            $kardexBodegaOk = $this->actualizarKardexBodega($producto, $ajusteId, $loteId, $fecha, $hora, $bodegaId, $kardex);
            if (!$kardexBodegaOk) {
                return [
                    'status' => 'error',
                    'msg' => 'Error al actualizar kardex por bodega',
                ];
            }

            // 3. Si maneja lotes, actualizar kardex por lote
            if ($loteId) {
                $kardexLoteOk = $this->actualizarKardexBodegaLote($producto, $ajusteId, $loteId, $fecha, $hora, $bodegaId, $kardex);
                if (!$kardexLoteOk) {
                    return [
                        'status' => 'error',
                        'msg' => 'Error al actualizar kardex por lote',
                    ];
                }
            }

            return ['status' => 'success'];
        } catch (\Throwable $e) {
            throw new \Exception('Error al generar kardex: ' . $e->getMessage() . $e->getTraceAsString());
        }
    }

    public function actualizarKardexGeneral($producto, $ajusteId, $loteId, $fecha, $hora, $bodegaId) {

        // Obtengo stock actual del producto
        $stockActual = $this->productLib->getStockProducto($producto->id);
        $nuevoStock = $stockActual + $producto->qty;

        // Obtengo costo de inventario del producto
        $costoInvProducto = $this->productLib->getCostoInventarioProducto($producto->id);
        $nuevoCostoInvProducto = $costoInvProducto + $producto->total;

        // Obtengo costo de inventario total (empresa)
        $costoInvTotal = $this->productLib->getCostoInventarioTotal();
        $nuevoCostoInvTotal = $costoInvTotal + $producto->total;

        // Calcular costo promedio
        $costoPromedio = $nuevoStock > 0 ? ($nuevoCostoInvProducto / $nuevoStock) : 0;

        // Insertar registro en kardex
        $dataKardex = [
            'fk_producto' => $producto->id,
            'kar_kardex' => $producto->qty,
            'kar_kardex_total' => $nuevoStock,
            'kar_costo_promedio' => $costoPromedio,
            'kar_costo_ultimo' => $producto->price,
            'kar_total_costo' => $producto->total,
            'kar_documento_id' => $ajusteId,
            'kar_codigo_transaccion' => $this->tipotransaccionCod,
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
            // Actualizar producto
            $this->productLib->updateCostosProducto($producto->id, $nuevoStock, $costoPromedio, $producto->price, $nuevoCostoInvProducto);

            // Actualizar costo inventario total
            $this->productLib->actualizarCostoInventarioTotal($nuevoCostoInvTotal);
        }

        $responseKardex = [
            'kardexId' => $kardexId,
            'costoPromedio' => $costoPromedio,
            'costoUltimo' => $producto->price,
        ];
        return $responseKardex;
    }

    public function actualizarKardexBodega($producto, $ajusteId, $loteId, $fecha, $hora, $bodegaId, $kardexCostos) {
        // Obtener stock actual en bodega
        $stockBodega = $this->stockBodLib->getStockBodega($bodegaId, $producto->id);
        $nuevoStockBodega = $stockBodega + $producto->qty;

        // Insertar registro en kardex_bodega
        $dataKardexBodega = [
            'fk_producto' => $producto->id,
            'fk_bodega' => $bodegaId,
            'karb_kardex' => $producto->qty,
            'karb_kardex_total' => $nuevoStockBodega,
            'karb_costo_promedio' => $kardexCostos['costoPromedio'],
            'karb_costo_ultimo' => $kardexCostos['costoUltimo'],
            'karb_documento_id' => $ajusteId,
            'karb_codigo_transaccion' => $this->tipotransaccionCod,
            'karb_fecha' => $fecha,
            'karb_hora' => $hora,
            'fk_lote' => $loteId,
            'fk_user_id' => $this->user->id,
        ];

        $kardexBodegaId = $this->ccm->guardar($dataKardexBodega, 'cc_kardex_bodega');

        if ($kardexBodegaId) {
            // Actualizamos o creamos registro de stock por bodega
            $this->stockBodLib->actualizarStockBodega($bodegaId, $producto->id, $nuevoStockBodega);
        }

        return $kardexBodegaId;
    }

    public function actualizarKardexBodegaLote($producto, $ajusteId, $loteId, $fecha, $hora, $bodegaId, $kardexCostos) {
        // Obtener stock actual en bodega por lote
        $stockBodegaLote = $this->stockBodLib->getStockBodegaLote($bodegaId, $producto->id, $loteId);
        $nuevoStockBodegaLote = $stockBodegaLote + $producto->qty;

        // Insertar registro en kardex_bodega_lote
        $dataKardexLote = [
            'fk_producto' => $producto->id,
            'fk_bodega' => $bodegaId,
            'fk_lote' => $loteId,
            'karbl_kardex' => $producto->qty,
            'karbl_kardex_total' => $nuevoStockBodegaLote,
            'karbl_costo_promedio' => $kardexCostos['costoPromedio'],
            'karbl_costo_ultimo' => $kardexCostos['costoUltimo'],
            'karbl_documento_id' => $ajusteId,
            'karbl_codigo_transaccion' => $this->tipotransaccionCod,
            'karbl_fecha' => $fecha,
            'karbl_hora' => $hora,
            'fk_lote' => $loteId,
            'fk_user_id' => $this->user->id,
        ];

        $kardexLoteId = $this->ccm->guardar($dataKardexLote, 'cc_kardex_bodega_lote');

        if ($kardexLoteId) {
            // Actualizar o crear registro de stock por bodega y lote
            $this->stockBodLib->actualizarStockBodegaLote($bodegaId, $producto->id, $loteId, $nuevoStockBodegaLote);
        }

        return $kardexLoteId;
    }
}
