<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Transferencias\Libraries;

/**
 * Description of TransferenciasLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 9 dic 2025
 * @time 4:08:50 p.m.
 */
use Modules\Comun\Libraries\ProductoLib;
use Modules\Comun\Libraries\StockBodegaLib;
use Modules\Comun\Libraries\ReservasLib;

class TransferenciasLib {

    protected $ccm;
    protected $user;
    protected $tipotransaccionCod = '17'; // TRANSFERENCIA DE PRODUCTOS 
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

    public function saveTransferencia($cartData, $dataPostTrb) {

        $secuencial = $this->ccm->getData('cc_transferencia_bodega', null, 'trb_secuencial', ['trb_secuencial' => 'DESC'], 1);

        $datos = [
            'trb_secuencial' => (isset($secuencial) ? $secuencial->trb_secuencial + 1 : 1),
            'fk_bodega_origen' => $dataPostTrb->trbBodegaOrigen,
            'fk_bodega_destino' => $dataPostTrb->trbBodegaDestino,
            'fk_centro_costo' => $dataPostTrb->trbCentroCosto,
            'trb_estado' => $dataPostTrb->trbEstado,
            'trb_observaciones' => $dataPostTrb->trbObservaciones,
            'trb_fecha' => $dataPostTrb->trbFecha,
            'trb_totaliva' => $cartData->totalIva,
            'trb_totalcartiva' => $cartData->totalCartIva,
            'trb_total' => $cartData->totalCart,
            'trb_total_items' => $cartData->totalItems,
            'trb_items_duplicados' => $dataPostTrb->trbPermitirDuplicados,
            'fk_user_confirma' => $dataPostTrb->trbUsuarioDestino,
            'fk_user_crea' => $this->user->id,
        ];

        $save = $this->ccm->guardar($datos, 'cc_transferencia_bodega');

        return $save;
    }

    public function updateTransferencia($cartData, $dataPostTrb, $tranferenciaId) {


        $datos = [
            'fk_bodega_origen' => $dataPostTrb->trbBodegaOrigen,
            'fk_bodega_destino' => $dataPostTrb->trbBodegaDestino,
            'fk_centro_costo' => $dataPostTrb->trbCentroCosto,
            'trb_estado' => $dataPostTrb->trbEstado,
            'trb_observaciones' => $dataPostTrb->trbObservaciones,
            'trb_fecha' => $dataPostTrb->trbFecha,
            'trb_totaliva' => $cartData->totalIva,
            'trb_totalcartiva' => $cartData->totalCartIva,
            'trb_total' => $cartData->totalCart,
            'trb_total_items' => $cartData->totalItems,
            'trb_items_duplicados' => $dataPostTrb->trbPermitirDuplicados,
            'fk_user_confirma' => $dataPostTrb->trbUsuarioDestino,
            'fk_user_crea' => $this->user->id
        ];

        return $this->ccm->actualizar('cc_transferencia_bodega', $datos, ['id' => $tranferenciaId]);
    }

    public function saveTransferenciaDetalle($transferenciaId, $item, $idLote) {
        $datos = [
            'fk_transferencia_bodega' => $transferenciaId,
            'fk_producto' => $item->id,
            'fk_lote' => $idLote,
            'trbd_itemcantidad' => $item->qty,
            'trbd_itemcosto' => $item->price,
            'trbd_itemcostoxcantidad' => $item->total,
            'trbd_observaciones' => null,
            'trbd_estado' => 1
        ];

        return $this->ccm->guardar($datos, 'cc_transferencia_bodega_det');
    }

    /**
     * Actualiza kardex restando stock (salida)
     */
    public function updateKardex(int $transferenciaId, object $producto, ?int $loteId, object $dataTransfer, bool $isOrigen, ?bool $isAnulacion = false): array {

        $fecha = $dataTransfer->trb_fecha ?? date('Y-m-d');
        $hora = date('H:i:s');

        $bodegaId = $isOrigen ? $dataTransfer->fk_bodega_origen : $dataTransfer->fk_bodega_destino;

        // 1. Kardex general (resta si es origen, suma si es destino, si es anulación hace lo contrario)
        $kardex = $this->actualizarKardexGeneral($producto, $transferenciaId, $loteId, $fecha, $hora, $bodegaId, $isOrigen, $isAnulacion);
        if (!$kardex['kardexId']) {
            return ['status' => 'error', 'msg' => 'Error al actualizar kardex general (Transferencia).'];
        }

        // 2. Kardex bodega
        $okBod = $this->actualizarKardexBodega($producto, $transferenciaId, $loteId, $fecha, $hora, $bodegaId, $kardex, $isOrigen, $isAnulacion);
        if (!$okBod) {
            return ['status' => 'error', 'msg' => 'Error al actualizar kardex bodega (Transferencia).'];
        }

        // 3. Kardex bodega lote
        if ($loteId) {
            $okLot = $this->actualizarKardexBodegaLote($producto, $transferenciaId, $loteId, $fecha, $hora, $bodegaId, $kardex, $isOrigen, $isAnulacion);
            if (!$okLot) {
                return ['status' => 'error', 'msg' => 'Error al actualizar kardex lote (Transferencia).'];
            }
        }

        return ['status' => 'success'];
    }

    public function actualizarKardexGeneral($producto, $transferenciaId, $loteId, $fecha, $hora, $bodegaId, $isOrigen, $isAnulacion) {

        // ===============================
        // 1. Definimos el signo del movimiento
        // ===============================
        // Transferencia normal:
        //   Origen  -> -1
        //   Destino -> +1
        //
        // Anulación:
        //   Origen  -> +1
        //   Destino -> -1

        if ($isAnulacion) {
            $factor = $isOrigen ? 1 : -1;
        } else {
            $factor = $isOrigen ? -1 : 1;
        }
        // Obtengo stock actual del producto
        $stockActual = $this->productLib->getStockProducto($producto->id);
        $nuevoStock = $stockActual + ($factor * $producto->qty);

        // Obtengo costo de inventario del producto (LOS COSTOS DE MANTIENEN POR QUE EN TRANSFERNCIAS SOLO MOVEMOS STOCK)
        $costoInvProducto = $this->productLib->getCostoInventarioProducto($producto->id);
        $nuevoCostoInvProducto = $costoInvProducto;

        // Obtengo costo de inventario total (empresa)  (LOS COSTOS DE MANTIENEN POR QUE EN TRANSFERNCIAS SOLO MOVEMOS STOCK)
        $costoInvTotal = $this->productLib->getCostoInventarioTotal();
        $nuevoCostoInvTotal = $costoInvTotal;

//        if ($nuevoStock < 0) {
//            throw new \Exception("El producto {$producto->name} quedaría con stock negativo.");
//        }
//        
        // Calcular costo promedio (LOS COSTOS DE MANTIENEN POR QUE EN TRANSFERNCIAS SOLO MOVEMOS STOCK)
//        $costoPromedio = $nuevoStock > 0 ? ($costoInvProducto / $nuevoStock) : 0;
        //Obtengo el costo último
        $costoPromedio = $this->productLib->getCostoPromedio($producto->id);

        //Obtengo el costo último
        $costoUltimo = $this->productLib->getCostoUltimo($producto->id);

        // Insertar registro en kardex
        $dataKardex = [
            'fk_producto' => $producto->id,
            'kar_kardex' => $factor * $producto->qty,
            'kar_kardex_total' => $nuevoStock,
            'kar_costo_promedio' => $costoPromedio,
            'kar_costo_ultimo' => $costoUltimo,
            'kar_total_costo' => abs($producto->total), //SIEMPRE POSITIVO LOS COSTOS
            'kar_documento_id' => $transferenciaId,
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
                    $costoUltimo,
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

    public function actualizarKardexBodega($producto, $transferenciaId, $loteId, $fecha, $hora, $bodegaId, $kardexCostos, $isOrigen, $isAnulacion) {
        // ===============================
        // 1. Definimos el signo del movimiento
        // ===============================
        // Transferencia normal:
        //   Origen  -> -1
        //   Destino -> +1
        //
        // Anulación:
        //   Origen  -> +1
        //   Destino -> -1

        if ($isAnulacion) {
            $factor = $isOrigen ? 1 : -1;
        } else {
            $factor = $isOrigen ? -1 : 1;
        }

        // Obtener stock actual en bodega
        $stockBodega = $this->stockBodLib->getStockBodega($bodegaId, $producto->id);
        $nuevoStockBodega = $stockBodega + ($factor * $producto->qty);

//        if ($nuevoStockBodega < 0) {
//            throw new \Exception("Stock en bodega insuficiente para el producto {$producto->name}.");
//        }
        // Insertar registro en kardex_bodega
        $dataKardexBodega = [
            'fk_producto' => $producto->id,
            'fk_bodega' => $bodegaId,
            'karb_kardex' => $factor * $producto->qty,
            'karb_kardex_total' => $nuevoStockBodega,
            'karb_costo_promedio' => $kardexCostos['costoPromedio'],
            'karb_costo_ultimo' => $kardexCostos['costoUltimo'],
            'karb_documento_id' => $transferenciaId,
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

    public function actualizarKardexBodegaLote($producto, $transferenciaId, $loteId, $fecha, $hora, $bodegaId, $kardexCostos, $isOrigen, $isAnulacion) {

        // ===============================
        // 1. Definimos el signo del movimiento
        // ===============================
        // Transferencia normal:
        //   Origen  -> -1
        //   Destino -> +1
        //
        // Anulación:
        //   Origen  -> +1
        //   Destino -> -1

        if ($isAnulacion) {
            $factor = $isOrigen ? 1 : -1;
        } else {
            $factor = $isOrigen ? -1 : 1;
        }

        // Obtener stock actual en bodega por lote
        $stockBodegaLote = $this->stockBodLib->getStockBodegaLote($bodegaId, $producto->id, $loteId);
        $nuevoStockBodegaLote = $stockBodegaLote + ($factor * $producto->qty);

//        if ($nuevoStockBodegaLote < 0) {
//            throw new \Exception("Stock por lote insuficiente para producto {$producto->name}.");
//        }
//        
        // Insertar registro en kardex_bodega_lote
        $dataKardexLote = [
            'fk_producto' => $producto->id,
            'fk_bodega' => $bodegaId,
            'fk_lote' => $loteId,
            'karbl_kardex' => $factor * $producto->qty,
            'karbl_kardex_total' => $nuevoStockBodegaLote,
            'karbl_costo_promedio' => $kardexCostos['costoPromedio'],
            'karbl_costo_ultimo' => $kardexCostos['costoUltimo'],
            'karbl_documento_id' => $transferenciaId,
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

    public function registrarReservas($transferenciaId, $cartData, $dataPostTrb): array {

        try {

            //Limpia reservas previas de este documento
            $this->reservasLib->liberarReservasDocumento($this->tipotransaccionCod, $transferenciaId);

            //Crea reservas nuevamente
            foreach ($cartData->cartContent as $val) {

                //Solo en caso de que se haya pasado un producto tipo servicio, lo ignoramos
                if ($val->servicio === '1') {
                    continue;
                }

                $reservaId = $this->reservasLib->reservarLinea(
                        $this->tipotransaccionCod, // origen
                        $transferenciaId, // id documento
                        $dataPostTrb->trbBodegaOrigen, // bodega
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
            log_message('error', '[TRANSFERENCIA DE PRODUCTOS][RESERVAS] ' . $exc->getMessage());
            return [
                'status' => 'error',
                'msg' => 'Error interno al registrar reservas'
            ];
        }
    }

    public function anularTransferencia(int $transferenciaId, string $motivoAnulacion): array {


        $transfer = $this->ccm->getData('cc_transferencia_bodega', ['id' => $transferenciaId], '*', null, 1);

        if (!$transfer) {
            return ['status' => 'error', 'msg' => 'Transferencia no encontrada'];
        }

        if ($transfer->trb_estado == -1) {
            return ['status' => 'warning', 'msg' => 'La transferencia ya está anulada'];
        }

        // Si NO estaba confirmada → liberar reservas (unicamente libera reservas si esta en estado borador o por confirmar, no involucra kardex)
        if (in_array($transfer->trb_estado, [1, 2])) {
            $this->reservasLib->liberarReservasDocumento($this->tipotransaccionCod, $transferenciaId);
            $text = $transfer->trb_estado == 1 ? 'BORRADOR' : 'POR CONFIRMAR';

            $dataUpdate = [
                'trb_estado' => -1,
                'trb_motivo_anulacion' => $motivoAnulacion,
                'trb_fecha_anulacion' => date('Y-m-d H:i:s'),
                'fk_user_anula' => $this->user->id,
                'trb_observaciones' => "Transferencia anulada en estado {$text}"
            ];

            $this->ccm->actualizar('cc_transferencia_bodega', $dataUpdate, ['id' => $transferenciaId]);

            return [
                'status' => 'success',
                'msg' => "Transferencia en estado {$text} anulado exitosamente"
            ];
        }
        try {

            // Código de transacción para ANULACIÓN DE TRANSFERENCIA
            $this->tipotransaccionCod = '44';

            // Si ya estaba CONFIRMADA → kardex inverso
            $detalle = $this->ccm->getData('cc_transferencia_bodega_det', ['fk_transferencia_bodega' => $transferenciaId, 'trbd_estado' => 1]);

            foreach ($detalle as $item) {

                $producto = (object) [
                            'id' => $item->fk_producto,
                            'qty' => $item->trbd_itemcantidad,
                            'price' => $item->trbd_itemcosto,
                            'total' => $item->trbd_itemcostoxcantidad,
                            'servicio' => 0,
                            'tieneLote' => $item->fk_lote ? 1 : 0
                ];
                // Reverso ENTRADA-SALIDA
                // Kardex ANTES ERA SALIDA (origen) AHORA AL ANULAR ES INGRESO
                $this->updateKardex($transferenciaId, $producto, $item->fk_lote, $transfer,
                        true, //ES ORIGEN 
                        true// ES ANULACIÓN
                );

                // Kardex ANTES ERA ENTRADA (destino) AHORA AL ANULAR ES SALIDA
                $this->updateKardex($transferenciaId, $producto, $item->fk_lote, $transfer,
                        false, //ES ORIGEN
                        true// ES ANULACIÓN
                );
            }

            $dataUpdate = [
                'trb_estado' => -1,
                'trb_motivo_anulacion' => $motivoAnulacion,
                'trb_fecha_anulacion' => date('Y-m-d H:i:s'),
                'fk_user_anula' => $this->user->id
            ];
            $this->ccm->actualizar('cc_transferencia_bodega', $dataUpdate, ['id' => $transferenciaId]);

            return [
                'status' => 'success',
                'msg' => "Transferencia #{$transfer->trb_secuencial} anulado exitosamente."
            ];
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            return [
                'status' => 'error',
                'msg' => 'Error al anular la transferencia: ' . $exc->getMessage()
            ];
        } finally {
            $this->tipotransaccionCod = '17';
        }
    }
}
