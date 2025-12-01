<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Comun\Libraries;

/**
 * Description of ReservasLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 29 nov 2025
 * @time 2:10:58 p.m.
 */
class ReservasLib {

    protected $ccm;
    protected $user;

    public function __construct() {
        $this->ccm = service('ccModel');
        $this->user = service('userSecion');
    }

    /**
     * Registra una línea de reserva (BORRADOR)
     */
    public function reservarLinea(string $transaccionCod, int $origenDocumentoId, int $bodegaId, int $productoId, ?int $loteId, float $cantidad): int {
        if ($cantidad <= 0) {
            return 0;
        }

        $data = [
            'res_codigo_transaccion' => $transaccionCod,
            'res_documento_id' => $origenDocumentoId,
            'fk_bodega' => $bodegaId,
            'fk_producto' => $productoId,
            'fk_lote' => $loteId,
            'res_cantidad' => $cantidad,
            'res_estado' => 'ACTIVA',
            'res_fecha' => date('Y-m-d H:i:s'),
            'fk_user_id' => $this->user->id
        ];

        return $this->ccm->guardar($data, 'cc_reserva_inventario');
    }

    public function liberarReservasDocumento(string $transaccionCod, int $origenDocumentoId): bool {
        $whereUpdate = ["res_codigo_transaccion" => $transaccionCod, "res_documento_id" => $origenDocumentoId, "res_estado" => "ACTIVA"];
        return $this->ccm->actualizar("cc_reserva_inventario", ['res_estado' => "LIBERADA"], $whereUpdate);
    }

    /**
     * Consume las reservas cuando el ajuste se ARCHIVA
     * (el stock ya se descuenta por kardex)
     */
    public function eliminarReservas(int $ajusteId, string $transaccionCod): void {
        $whereUpdate = ["res_codigo_transaccion" => $transaccionCod, "res_documento_id" => $ajusteId, "res_estado" => "ACTIVA"];
        $this->ccm->actualizar("cc_reserva_inventario", ['res_estado' => "CONSUMIDA"], $whereUpdate);
    }

//    /**
//     * Libera las reservas cuando el ajuste se ANULA
//     */
//    public function liberarReservas(int $ajusteId, string $transaccionCod): void {
//        $whereUpdate = ["res_codigo_transaccion" => $transaccionCod, "res_documento_id" => $ajusteId, "res_estado" => "ACTIVA"];
//        $this->ccm->actualizar("cc_reserva_inventario", ['res_estado' => "LIBERADA"], $whereUpdate);
//    }

    public function getReservasProductoLote(int $productoId, int $bodegaId, int $idLoteProducto, ?string $codTransaccion=null, ?int $idDocumento=null): array {
        // 1) STOCK RESERVADO
        $whereDataReserva = ['tb1.fk_producto' => $productoId, 'tb1.fk_bodega' => $bodegaId, 'tb1.res_estado' => 'ACTIVA','tb1.fk_lote'=>$idLoteProducto];
        
        if($idDocumento){
           $whereDataReserva['tb1.res_documento_id != ']= $idDocumento;
           $whereDataReserva['tb1.res_codigo_transaccion']= $codTransaccion;
        }

        $rowReserva = $this->ccm->getData("cc_reserva_inventario tb1", $whereDataReserva, "COALESCE(SUM(tb1.res_cantidad),0) AS reservado", null, 1);
        $reservado = $rowReserva ? (float) $rowReserva->reservado : 0;

        return ['reserva' => $reservado];
    }

    /**
     * Devuelve el total de stock reservado de un producto
     * (opcionalmente por bodega y lote)
     */
//    public function getStockReservado(
//            int $productoId,
//            ?int $bodegaId = null,
//            ?int $loteId = null,
//            ?int $origenIdExcluir = null
//    ): float {
//        $builder = $this->db->table('cc_reserva_inventario r');
//        $builder->selectSum('r.res_cantidad', 'reservado');
//        $builder->where('r.fk_producto', $productoId);
//        $builder->where('r.res_estado', 'ACTIVA');
//
//        if ($bodegaId) {
//            $builder->where('r.fk_bodega', $bodegaId);
//        }
//
//        if ($loteId !== null) {
//            $builder->where('r.fk_lote', $loteId);
//        }
//
//        // ✅ CLAVE para edición de borradores
//        if ($origenIdExcluir) {
//            $builder->where('r.res_origen_id !=', $origenIdExcluir);
//        }
//
//        $row = $builder->get()->getRow();
//        return $row && $row->reservado ? (float) $row->reservado : 0;
//    }

    /**
     * Valida stock disponible REAL
     * stock actual - reservas ACTIVAS
     */
//    public function validarStockDisponible(
//            int $productoId,
//            int $bodegaId,
//            float $cantidadSolicitada,
//            ?int $loteId = null,
//            ?int $origenIdExcluir = null
//    ): array {
//        // Stock real en bodega
//        if ($loteId) {
//            $stock = $this->ccm->getValueWhere(
//                    'cc_stock_bodega_lote',
//                    [
//                        'fk_producto' => $productoId,
//                        'fk_bodega' => $bodegaId,
//                        'fk_lote' => $loteId
//                    ],
//                    'stbl_stock'
//            );
//        } else {
//            $stock = $this->ccm->getValueWhere(
//                    'cc_stock_bodega',
//                    [
//                        'fk_producto' => $productoId,
//                        'fk_bodega' => $bodegaId
//                    ],
//                    'stb_stock'
//            );
//        }
//
//        $stock = $stock ? (float) $stock : 0;
//
//        // Stock reservado
//        $reservado = $this->getStockReservado(
//                $productoId,
//                $bodegaId,
//                $loteId,
//                $origenIdExcluir
//        );
//
//        $disponible = $stock - $reservado;
//
//        if ($cantidadSolicitada > $disponible) {
//            return [
//                'status' => 'warning',
//                'msg' => "Stock insuficiente. Disponible: {$disponible}"
//            ];
//        }
//
//        return [
//            'status' => 'success',
//            'disponible' => $disponible
//        ];
//    }
}
