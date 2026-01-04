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
    public function eliminarReservas(int $documentoId, string $transaccionCod): void {
        $whereUpdate = ["res_codigo_transaccion" => $transaccionCod, "res_documento_id" => $documentoId, "res_estado" => "ACTIVA"];
        $this->ccm->actualizar("cc_reserva_inventario", ['res_estado' => "CONSUMIDA"], $whereUpdate);
    }


    public function getReservasProductoLote(int $productoId, int $bodegaId, int $idLoteProducto, ?string $codTransaccion = null, ?int $idDocumento = null): array {
        // 1) STOCK RESERVADO
        $whereDataReserva = ['tb1.fk_producto' => $productoId, 'tb1.fk_bodega' => $bodegaId, 'tb1.res_estado' => 'ACTIVA', 'tb1.fk_lote' => $idLoteProducto];
        
        $whereNotReserva=null;
        if ($idDocumento) {
            $whereNotReserva = "NOT (tb1.res_codigo_transaccion = {$codTransaccion} AND tb1.res_documento_id = {$idDocumento})";
        }

        $rowReserva = $this->ccm->getData("cc_reserva_inventario tb1", $whereDataReserva, "COALESCE(SUM(tb1.res_cantidad),0) AS reservado", null, 1, null, $whereNotReserva);
        $reservado = $rowReserva ? (float) $rowReserva->reservado : 0;

        return ['reserva' => $reservado];
    }

}
