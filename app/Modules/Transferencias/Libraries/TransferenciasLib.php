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

        return $this->ccm->actualizar('cc_transferencia_bodega', $datos, $tranferenciaId);
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

    public function registrarReservas($transferenciaId, $cartData, $dataPostTrb): array {

        try {

            //Limpia reservas previas de este ajuste
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
}
