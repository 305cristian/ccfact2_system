<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Comun\Libraries;

/**
 * Description of ProductoLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 18 oct 2025
 * @time 3:44:26 p.m.
 */
class ProductoLib {

    protected $ccm;

    public function __construct() {
        $this->ccm = service('ccModel');
    }

    /**
     * Obtiene el stock actual de un producto
     */
    public function getStockProducto($productoId) {
        $producto = $this->ccm->getData('cc_productos', ['id' => $productoId], 'prod_stockactual', null, 1);
        return $producto ? (float) $producto->prod_stockactual : 0;
    }

    /**
     * Obtiene el costo de inventario de un producto
     */
    public function getCostoInventarioProducto($productoId) {
        $producto = $this->ccm->getData('cc_productos', ['id' => $productoId], 'prod_costoinventario', null, 1);
        return $producto ? (float) $producto->prod_costoinventario : 0;
    }

    /**
     * Obtiene el costo de inventario total de la empresa
     */
    public function getCostoInventarioTotal() {
        $indice = $this->ccm->getData('cc_empresa_indice', ['ind_nombre' => 'COSTO_INVENTARIO'], 'ind_valor', null, 1);
        return $indice ? (float) $indice->ind_valor : 0;
    }

    /**
     * Actualiza los datos del producto (stock, costos)
     */
    public function updateCostosProducto($productoId, $nuevoStock_, $costoPromedio_, $costoUltimo_, $costoInventario_) {

        $nuevoStock = (float) $nuevoStock_;
        $costoPromedio = (float) $costoPromedio_;
        $costoUltimo = (float) $costoUltimo_;
        $costoInventario = (float) $costoInventario_;

        if ($nuevoStock > 0 && $costoPromedio <= 0) {
            throw new \RuntimeException("No se puede actualizar el producto {$productoId} con costo promedio 0 teniendo stock positivo.");
        }

        if ($nuevoStock > 0 && $costoUltimo <= 0) {
            throw new \RuntimeException("No se puede actualizar el producto {$productoId} con costo ultimo 0 teniendo stock positivo.");
        }

        if ($nuevoStock <= 0) {
            $costoInventario = 0;
        }

        $datos = [
            'prod_stockactual' => $nuevoStock,
            'prod_costopromedio' => $costoPromedio,
            'prod_costoultimo' => $costoUltimo,
            'prod_costoinventario' => $costoInventario,
        ];

        return $this->ccm->actualizar('cc_productos', $datos, ['id' => $productoId]);
    }

    /**
     * Actualiza el costo de inventario total
     */
    public function actualizarCostoInventarioTotal($nuevoCosto) {
        $existe = $this->ccm->getData('cc_empresa_indice', ['ind_nombre' => 'COSTO_INVENTARIO'], 'id', null, 1);

        if ($existe) {
            $datos = [
                'ind_valor' => $nuevoCosto,
                'ind_fecha_actualizacion' => date('Y-m-d H:i:s')
            ];
            return $this->ccm->actualizar('cc_empresa_indice', $datos, ['ind_nombre' => 'COSTO_INVENTARIO'],
            );
        } else {
            $datos = [
                'ind_nombre' => 'COSTO_INVENTARIO',
                'ind_valor' => $nuevoCosto,
                'ind_fecha_actualizacion' => date('Y-m-d H:i:s')
            ];
            return $this->ccm->guardar($datos, 'cc_empresa_indice');
        }
    }

    /**
     * Obtenemos el costo último del producto
     */
    public function getCostoUltimo($productoId) {
        $producto = $this->ccm->getData('cc_productos', ['id' => $productoId], 'prod_costoultimo', null, 1);
        return $producto ? (float) $producto->prod_costoultimo : 0;
    }
    
     /**
     * Obtenemos el costo promedio del producto
     */
    public function getCostoPromedio($productoId) {
        $producto = $this->ccm->getData('cc_productos', ['id' => $productoId], 'prod_costopromedio', null, 1);
        return $producto ? (float) $producto->prod_costopromedio : 0;
    }
}
