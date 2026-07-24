<?php

namespace Modules\NotasCredito\Models;
use CodeIgniter\Model;

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of NotaCreditoModel
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 15 jul 2026
 * @time 3:01:55 p.m.
 */
class NotaCreditoModel extends Model{
    //put your code here

    public function getCompraBaseNotaCredito(int $compraId): ?object {

        $builder = $this->db->table("cc_compras compra");
        $builder->select(
                "compra.*, "
                . "proveedor.prov_ruc, proveedor.prov_razon_social, proveedor.prov_direccion, proveedor.prov_telefono, proveedor.prov_email, "
                . "bodega.bod_nombre, centro.cc_nombre, tipoComprobante.comp_nombre AS comprobante_nombre, "
                . "sustento.sus_nombre, tipoCompra.tc_nombre"
        );
        $builder->join("cc_proveedores proveedor", "proveedor.id = compra.fk_proveedor");
        $builder->join("cc_bodegas bodega", "bodega.id = compra.fk_bodega", "left");
        $builder->join("cc_centroscosto centro", "centro.id = compra.fk_centro_costo", "left");
        $builder->join("cc_tipos_comprobante tipoComprobante", "tipoComprobante.comp_codigo = compra.comp_tipo_comprobante_cod", "left");
        $builder->join("cc_sustentos sustento", "sustento.sus_codigo = compra.cod_sustento", "left");
        $builder->join("cc_tipo_compra tipoCompra", "tipoCompra.id = compra.fk_tipo_compra", "left");
        $builder->where("compra.id", $compraId);
        $builder->where("compra.comp_estado", "ARCHIVADO");
        $builder->whereIn("compra.comp_tipo_comprobante_cod", ["01", "02", "03"]);

        $compra = $builder->get()->getRow();

        if (!$compra) {
            return null;
        }

        $builderDetalle = $this->db->table("cc_compras_det detalle");
        $builderDetalle->select(
                "detalle.*, "
                . "producto.prod_codigo, producto.prod_nombre, producto.prod_ctrllote, producto.prod_isservicio, "
                . "tarifa.impt_detalle AS impuesto_detalle, "
                . "COALESCE(lote.lot_lote, detalle.compd_lote) AS lote, "
                . "COALESCE(lote.lot_fecha_elaboracion, detalle.compd_fecha_elaboracion) AS fecha_elaboracion, "
                . "COALESCE(lote.lot_fecha_caducidad, detalle.compd_fecha_caducidad) AS fecha_caducidad, "
                . "COALESCE(SUM(CASE WHEN ndc.comp_estado != 'ANULADA' THEN ndcDetalle.compd_cantidad ELSE 0 END), 0) AS cantidad_usada_ndc"
        );
        $builderDetalle->join("cc_productos producto", "producto.id = detalle.fk_producto");
        $builderDetalle->join("cc_impuesto_tarifa tarifa", "tarifa.id = detalle.fk_impuesto_tarifa", "left");
        $builderDetalle->join("cc_lotes lote", "lote.id = detalle.fk_lote", "left");
        $builderDetalle->join("cc_compras_det ndcDetalle", "ndcDetalle.fk_compra_det_relacionada = detalle.id AND ndcDetalle.compd_estado = 1", "left");
        $builderDetalle->join("cc_compras ndc", "ndc.id = ndcDetalle.fk_compra AND ndc.fk_compra_relacionada = detalle.fk_compra", "left");
        $builderDetalle->where("detalle.fk_compra", $compraId);
        $builderDetalle->where("detalle.compd_estado", 1);
        $builderDetalle->groupBy("detalle.id");
        $builderDetalle->orderBy("detalle.id", "ASC");

        $compra->detalle = $builderDetalle->get()->getResult();

        foreach ($compra->detalle as $detalle) {
            $detalle->cantidad_disponible_ndc = max(0, (float) $detalle->compd_cantidad - (float) $detalle->cantidad_usada_ndc);
        }

        return $compra;
    }
}
