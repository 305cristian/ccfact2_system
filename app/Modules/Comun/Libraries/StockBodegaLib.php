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
            return $this->ccm->actualizar('cc_stock_bodega',$datos, $whereData);
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
            return $this->ccm->actualizar($datos, $whereData, 'cc_stock_bodega_lote');
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
}
