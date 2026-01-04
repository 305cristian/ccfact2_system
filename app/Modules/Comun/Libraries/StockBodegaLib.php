<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Comun\Libraries;

/**
 * Description of StockBodegaLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 19 oct 2025
 * @time 8:36:13 a.m.
 */
class StockBodegaLib {

    protected $ccm;

    public function __construct() {
        $this->ccm = service('ccModel');
    }

    /**
     * Obtiene el stock en bodega
     */
    public function getStockBodega($bodegaId, $productoId) {
        $whereData = [
            'fk_bodega' => $bodegaId,
            'fk_producto' => $productoId
        ];
        $stock = $this->ccm->getData('cc_stock_bodega', $whereData, 'stb_stock', null, 1);
        return $stock ? (float) $stock->stb_stock : 0;
    }

    /**
     * Actualiza el stock en bodega
     */
    public function actualizarStockBodega($bodegaId, $productoId, $nuevoStock) {
        $whereData = [
            'fk_bodega' => $bodegaId,
            'fk_producto' => $productoId
        ];
        $existe = $this->ccm->getData('cc_stock_bodega', $whereData);

        if ($existe) {
            $datos = [
                'stb_stock' => $nuevoStock,
            ];
            $whereData = [
                'fk_bodega' => $bodegaId,
                'fk_producto' => $productoId
            ];
            return $this->ccm->actualizar('cc_stock_bodega', $datos, $whereData);
        } else {
            $datos = [
                'fk_bodega' => $bodegaId,
                'fk_producto' => $productoId,
                'stb_stock' => $nuevoStock,
            ];
            return $this->ccm->guardar($datos, 'cc_stock_bodega');
        }
    }

    /**
     * Obtiene el stock de un producto en una bodega por lote
     */
    public function getStockBodegaLote($bodegaId, $productoId, $loteId) {
        $whereData = [
            'fk_bodega' => $bodegaId,
            'fk_producto' => $productoId,
            'fk_lote' => $loteId
        ];
        $stock = $this->ccm->getData('cc_stock_bodega_lote', $whereData, 'stbl_stock', null, 1);

        return $stock ? (float) $stock->stbl_stock : 0;
    }

    /**
     * Actualiza el stock en bodega por lote
     */
    public function actualizarStockBodegaLote($bodegaId, $productoId, $loteId, $nuevoStock) {
        $whereData = [
            'fk_bodega' => $bodegaId,
            'fk_producto' => $productoId,
            'fk_lote' => $loteId
        ];
        $existe = $this->ccm->getData('cc_stock_bodega_lote', $whereData);

        if ($existe) {
            $datos = [
                'stbl_stock' => $nuevoStock,
            ];
            $whereData = [
                'fk_bodega' => $bodegaId,
                'fk_producto' => $productoId,
                'fk_lote' => $loteId
            ];
            return $this->ccm->actualizar('cc_stock_bodega_lote', $datos, $whereData);
        } else {
            $datos = [
                'fk_bodega' => $bodegaId,
                'fk_producto' => $productoId,
                'fk_lote' => $loteId,
                'stbl_stock' => $nuevoStock,
            ];
            return $this->ccm->guardar($datos, 'cc_stock_bodega_lote');
        }
    }

    public function validarStockDisponible(int $productoId, int $bodegaId, float $cantidadSolicitada, ?string $codTransaccion=null, ?int $documentoId = null, ?int $idLoteProducto = null): array {

        // 1) STOCK EN BODEGA
        $tabla = $idLoteProducto ? 'cc_stock_bodega_lote' : 'cc_stock_bodega';
        $whereDataStock = ['fk_producto' => $productoId, 'fk_bodega' => $bodegaId];
        if ($idLoteProducto) {
            $whereDataStock['fk_lote'] = $idLoteProducto;
        }
        $campoStock = $idLoteProducto ? 'stbl_stock' : 'stb_stock';

        $rowStock = $this->ccm->getData($tabla, $whereDataStock, $campoStock, null, 1);
        $stockBodega = $rowStock ? (float) $rowStock->$campoStock : 0;

        // 2) STOCK RESERVADO
        $whereDataReserva = ['tb1.fk_producto' => $productoId, 'tb1.fk_bodega' => $bodegaId, 'tb1.res_estado' => 'ACTIVA'];
        $whereNotReserva=null;
        if ($documentoId) {
            $whereNotReserva = "NOT (tb1.res_codigo_transaccion = {$codTransaccion} AND tb1.res_documento_id = {$documentoId})";
        }
        if ($idLoteProducto) {
            $whereDataReserva['tb1.fk_lote'] = $idLoteProducto;
        }
        $rowReserva = $this->ccm->getData("cc_reserva_inventario tb1", $whereDataReserva, "COALESCE(SUM(tb1.res_cantidad),0) AS reservado", null, 1,null, $whereNotReserva);
        $reservado = $rowReserva ? (float) $rowReserva->reservado : 0;

        // 3) DISPONIBLE REAL
        $disponible = $stockBodega - $reservado;
        
        if ($cantidadSolicitada > $disponible) {
            return [
                'status' => 'warning',
                'msg' =>
                "Stock insuficiente para realizar la salida.<br>" .
                "Stock bodega: <b>{$stockBodega}</b><br>" .
                "Reservado: <b>{$reservado}</b><br>" .
                "Disponible: <b>{$disponible}</b><br>" .
                "Solicitado: <b>{$cantidadSolicitada}</b>"
            ];
        }

        return ['status' => 'success', 'dataStockDisponible' => $disponible, 'dataReservado' => $reservado];
    }
}
