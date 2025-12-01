<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\AjustesSalida\Libraries;

/**
 * Description of SalidasLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 27 nov 2025
 * @time 12:23:01 p.m.
 */

namespace Modules\AjustesSalida\Libraries;

use Modules\Comun\Libraries\ProductoLib;
use Modules\Comun\Libraries\StockBodegaLib;
use Modules\Comun\Libraries\ReservasLib;

class SalidasLib {

    protected $ccm;
    protected $user;
    protected $tipotransaccionCod = '38'; // AJUSTE SALIDA
    protected $productLib;
    protected $stockBodLib;
    protected $reservasLib;

    public function __construct() {

        //IMPORTAMOS SERVICIOS
        $this->ccm = service('ccModel');
        $this->user = service('userSecion');

        //IMPORTAMOS LIBRERIAS
        $this->productLib = new ProductoLib();
        $this->stockBodLib = new StockBodegaLib();
        $this->reservasLib = new ReservasLib();
    }

    public function saveAjuste($cartData, $dataPostAjuste) {
        $esBorrador = ($dataPostAjuste->ajesEstado == 1);

        $secuencial = $this->ccm->getData('cc_ajuste_salida', null, 'ajes_secuencial', ['ajes_secuencial' => 'DESC'], 1);

        $datos = [
            'ajes_secuencial' => (isset($secuencial) ? $secuencial->ajes_secuencial + 1 : 1),
            'ajes_fecha' => $dataPostAjuste->ajesFecha,
            'ajes_observaciones' => $dataPostAjuste->ajesObservaciones,
            'ajes_estado' => $dataPostAjuste->ajesEstado,
            'ajes_fecha_anulacion' => null,
            'ajes_motivo_anulacion' => null,
            'fk_user_anulacion' => null,
            'fk_motivo_ajuste' => $dataPostAjuste->ajesMotivo,
            'fk_bodega' => $dataPostAjuste->ajesBodega,
            'fk_user_id' => $this->user->id,
            'ajes_fecha_aprobacion' => $esBorrador ? null : date('Y-m-d H:i:s'),
            'fk_user_id_aprueba' => $esBorrador ? null : $this->user->id,
            'fk_centro_costo' => $dataPostAjuste->ajesCentrocosto,
            'fk_cliente' => !empty($dataPostAjuste->ajesCliente) ? $dataPostAjuste->ajesCliente : 1,
            'iva_porcentaje' => getSettings('IVA'),
            'ajes_total_items' => $cartData->totalItems,
            'ajes_total' => $cartData->totalCart,
            'ajes_subtotal_bienes' => $cartData->totalBienes,
            'ajes_subtotal_servicios' => $cartData->totalServicios,
            'ajes_totalcartiva' => $cartData->totalCartIva,
            'ajes_totaliva' => $cartData->totalIva,
            'ajes_tarifacero' => $cartData->tarifCero,
            'ajes_tarifacero_neto' => $cartData->tarifCeroNeto,
            'ajes_tarifaiva' => $cartData->tarifIva,
            'ajes_tarifaiva_neto' => $cartData->tarifIvaNeto,
            'ajes_items_duplicados' => $dataPostAjuste->ajesPermitirDuplicados,
            'ajes_tipo' => $dataPostAjuste->ajesTipo,
            'fk_servicio' => $dataPostAjuste->ajesServicio,
        ];

        $save = $this->ccm->guardar($datos, 'cc_ajuste_salida');

        return $save;
    }

    public function saveAjusteDetalle($ajusteId, $val, $lote) {
        $datos = [
            'fk_ajuste_salida' => $ajusteId,
            'fk_producto' => $val->id,
            'fk_lote' => $lote,
            'ajsd_itemcantidad' => $val->qty,
            'ajsd_itemcosto' => $val->price,
            'ajsd_itemcostoxcantidad' => $val->total,
            'ajsd_observacion' => null,
            'ajsd_estado' => 1,
        ];

        return $this->ccm->guardar($datos, 'cc_ajuste_salida_det');
    }

    public function updateAjuste($cartData, $dataPostAjuste, $ajusteId) {
        $esBorrador = ($dataPostAjuste->ajesEstado == 1);

        $datos = [
            'ajes_fecha' => $dataPostAjuste->ajesFecha,
            'ajes_observaciones' => $dataPostAjuste->ajesObservaciones,
            'ajes_estado' => $dataPostAjuste->ajesEstado,
            'ajes_fecha_anulacion' => null,
            'ajes_motivo_anulacion' => null,
            'fk_user_anulacion' => null,
            'fk_motivo_ajuste' => $dataPostAjuste->ajesMotivo,
            'fk_bodega' => $dataPostAjuste->ajesBodega,
            'fk_user_id' => $this->user->id,
            'ajes_fecha_aprobacion' => $esBorrador ? null : date('Y-m-d H:i:s'),
            'fk_user_id_aprueba' => $esBorrador ? null : $this->user->id,
            'fk_centro_costo' => $dataPostAjuste->ajesCentrocosto,
            'fk_cliente' => isset($dataPostAjuste->ajesCliente) ? $dataPostAjuste->ajesCliente : 1,
            'iva_porcentaje' => getSettings('IVA'),
            'ajes_total_items' => $cartData->totalItems,
            'ajes_total' => $cartData->totalCart,
            'ajes_subtotal_bienes' => $cartData->totalBienes,
            'ajes_subtotal_servicios' => $cartData->totalServicios,
            'ajes_totalcartiva' => $cartData->totalCartIva,
            'ajes_totaliva' => $cartData->totalIva,
            'ajes_tarifacero' => $cartData->tarifCero,
            'ajes_tarifacero_neto' => $cartData->tarifCeroNeto,
            'ajes_tarifaiva' => $cartData->tarifIva,
            'ajes_tarifaiva_neto' => $cartData->tarifIvaNeto,
            'ajes_items_duplicados' => $dataPostAjuste->ajesPermitirDuplicados,
            'ajes_tipo' => $dataPostAjuste->ajesTipo,
            'fk_servicio' => $dataPostAjuste->ajesServicio,
        ];

        return $this->ccm->actualizar('cc_ajuste_salida', $datos, ['id' => $ajusteId]);
    }

    /**
     * Actualiza kardex restando stock (salida)
     */
    public function updateKardex($ajusteId, $producto, $loteId, $dataPostAjuste) {
        $fecha = $dataPostAjuste->ajesFecha ?? date('Y-m-d');
        $hora = date('H:i:s');
        $bodegaId = $dataPostAjuste->ajesBodega;

        // 1. Kardex general (resta)
        $kardex = $this->actualizarKardexGeneral($producto, $ajusteId, $loteId, $fecha, $hora, $bodegaId);
        if (!$kardex['kardexId']) {
            return ['status' => 'error', 'msg' => 'Error al actualizar kardex general (salida).'];
        }

        // 2. Kardex bodega
        $okBod = $this->actualizarKardexBodega($producto, $ajusteId, $loteId, $fecha, $hora, $bodegaId, $kardex);
        if (!$okBod) {
            return ['status' => 'error', 'msg' => 'Error al actualizar kardex bodega (salida).'];
        }

        // 3. Kardex bodega lote
        if ($loteId) {
            $okLot = $this->actualizarKardexBodegaLote($producto, $ajusteId, $loteId, $fecha, $hora, $bodegaId, $kardex);
            if (!$okLot) {
                return ['status' => 'error', 'msg' => 'Error al actualizar kardex lote (salida).'];
            }
        }

        return ['status' => 'success'];
    }

    public function actualizarKardexGeneral($producto, $ajusteId, $loteId, $fecha, $hora, $bodegaId) {

        // Obtengo stock actual del producto
        $stockActual = $this->productLib->getStockProducto($producto->id);
        $nuevoStock = $stockActual - $producto->qty;

        // Obtengo costo de inventario del producto
        $costoInvProducto = $this->productLib->getCostoInventarioProducto($producto->id);
        $nuevoCostoInvProducto = $costoInvProducto - $producto->total;

        // Obtengo costo de inventario total (empresa)
        $costoInvTotal = $this->productLib->getCostoInventarioTotal();
        $nuevoCostoInvTotal = $costoInvTotal - $producto->total;

//        if ($nuevoStock < 0) {
//            throw new \Exception("El producto {$producto->name} quedaría con stock negativo.");
//        }
        // Calcular costo promedio
        $costoPromedio = $nuevoStock > 0 ? ($nuevoCostoInvProducto / $nuevoStock) : 0;

        // Insertar registro en kardex
        $dataKardex = [
            'fk_producto' => $producto->id,
            'kar_kardex' => -$producto->qty,
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
            $this->productLib->updateCostosProducto(
                    $producto->id,
                    $nuevoStock,
                    $costoPromedio,
                    $producto->price,
                    $nuevoCostoInvProducto
            );
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
        $nuevoStockBodega = $stockBodega - $producto->qty;

//        if ($nuevoStockBodega < 0) {
//            throw new \Exception("Stock en bodega insuficiente para el producto {$producto->name}.");
//        }
        // Insertar registro en kardex_bodega
        $dataKardexBodega = [
            'fk_producto' => $producto->id,
            'fk_bodega' => $bodegaId,
            'karb_kardex' => -$producto->qty,
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
        $nuevoStockBodegaLote = $stockBodegaLote - $producto->qty;

//        if ($nuevoStockBodegaLote < 0) {
//            throw new \Exception("Stock por lote insuficiente para producto {$producto->name}.");
//        }
//        
        // Insertar registro en kardex_bodega_lote
        $dataKardexLote = [
            'fk_producto' => $producto->id,
            'fk_bodega' => $bodegaId,
            'fk_lote' => $loteId,
            'karbl_kardex' => -$producto->qty,
            'karbl_kardex_total' => $nuevoStockBodegaLote,
            'karbl_costo_promedio' => $kardexCostos['costoPromedio'],
            'karbl_costo_ultimo' => $kardexCostos['costoUltimo'],
            'karbl_documento_id' => $ajusteId,
            'karbl_codigo_transaccion' => $this->tipotransaccionCod,
            'karbl_fecha' => $fecha,
            'karbl_hora' => $hora,
            'fk_user_id' => $this->user->id,
        ];

        $kardexLoteId = $this->ccm->guardar($dataKardexLote, 'cc_kardex_bodega_lote');

        if ($kardexLoteId) {
            // Actualizar o crear registro de stock por bodega y lote
            $this->stockBodLib->actualizarStockBodegaLote($bodegaId, $producto->id, $loteId, $nuevoStockBodegaLote);
        }

        return $kardexLoteId;
    }

    /**
     * Se usa SOLO cuando el ajuste queda en BORRADOR
     * Genera las reservas reales en BORRADOR
     * NO toca stock ni kardex
     */
    public function registrarReservas($ajusteId, $cartData, $dataPostAjuste): array {

        try {

            //Limpia reservas previas de este ajuste
            $this->reservasLib->liberarReservasDocumento($this->tipotransaccionCod, $ajusteId);

            //Crea reservas nuevamente
            foreach ($cartData->cartContent as $val) {

                //Solo en caso de que se haya pasado un producto tipo servicio, lo ignoramos
                if ($val->servicio === '1') {
                    continue;
                }

                $reservaId = $this->reservasLib->reservarLinea(
                        $this->tipotransaccionCod, // origen
                        $ajusteId, // id documento
                        $dataPostAjuste->ajesBodega, // bodega
                        $val->id, // producto
                        $val->tieneLote === '1' ? $val->idLote : null,
                        $val->qty// cantidad
                );

                // VALIDAR RESULTADO
                if (!$reservaId) {
                    return [
                        'status' => 'error',
                        'msg' => "No se pudo reservar el producto {$val->name}"
                    ];
                }
            }

            return ['status' => 'success'];
            
        } catch (Exception $exc) {
            log_message('error', '[AJUSTE SALIDA][RESERVAS] ' . $exc->getMessage());
            return [
                'status' => 'error',
                'msg' => 'Error interno al registrar reservas'
            ];
        }
    }
}
