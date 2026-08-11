<?php

namespace Modules\Ventas\Models;

use CodeIgniter\Model;

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of VentasModel
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 9 ago 2026
 * @time 8:30:05 a.m.
 */
class VentasModel extends Model {
    //put your code here

    public function getPreciosProducto(int $productoId): array {

        $builder = $this->db->table("cc_producto_precios tb1");
        $builder->select("tb1.fk_tipo_precio AS id, tb2.tpc_nombre AS nombre, tb2.tpc_descripcion AS descripcion, tb1.pp_valor AS valor");
        $builder->join("cc_tipo_precios tb2", "tb2.id = tb1.fk_tipo_precio");
        $builder->where([
            "tb1.fk_producto" => $productoId,
            "tb2.tpc_estado" => 1,
        ]);
        $builder->where("tb1.pp_valor >", 0);
        $builder->orderBy("tb1.fk_tipo_precio", "ASC");

        return $builder->get()->getResult();
    }

    public function getPuntosEmisionUsuario(int $empleadoId, array $codigosComprobante): array {

        $builder = $this->db->table("cc_puntos_venta tb1");
        $builder->select("
            tb1.id,
            tb1.fk_comprobante,
            tb1.pv_establecimiento,
            tb1.pv_emision,
            tb1.pv_auth_sri,
            tb1.pv_fecha_vence_auth,
            tb1.pv_sec_inicial,
            tb1.pv_sec_actual,
            tb1.pv_sec_final,
            tb1.pv_is_electronica,
            tb1.pv_fk_bodega,
            tb3.bod_nombre,
            CONCAT(tb1.pv_establecimiento, '-', tb1.pv_emision, ' / Sec. ', LPAD(tb1.pv_sec_actual, 9, '0'), ' / ', tb3.bod_nombre) AS punto_label
        ", false);
        $builder->join("cc_puntoventa_empleado tb2", "tb2.fk_punto_venta = tb1.id");
        $builder->join("cc_bodegas tb3", "tb3.id = tb1.pv_fk_bodega");
        $builder->where([
            "tb1.fk_proyecto" => getProyectoId(),
            "tb1.pv_estado" => "1",
            "tb2.fk_empleado" => $empleadoId,
        ]);

        if ($codigosComprobante) {
            $builder->whereIn("tb1.fk_comprobante", $codigosComprobante);
        }

        $builder->orderBy("tb1.fk_comprobante", "ASC");
        $builder->orderBy("tb1.pv_establecimiento", "ASC");
        $builder->orderBy("tb1.pv_emision", "ASC");

        return $builder->get()->getResult();
    }

    public function getDashboardResumen(array $filtros): object {

        $builder = $this->db->table("cc_ventas venta");
        $builder->select("
            COUNT(*) AS total_documentos,
            SUM(CASE WHEN venta.ven_estado = 'ARCHIVADO' THEN venta.ven_total ELSE 0 END) AS total_ventas,
            SUM(CASE WHEN venta.ven_estado = 'ARCHIVADO' THEN venta.ven_totaliva ELSE 0 END) AS total_iva,
            SUM(CASE WHEN venta.ven_estado = 'BORRADOR' THEN 1 ELSE 0 END) AS total_borradores,
            SUM(CASE WHEN venta.ven_estado = 'ARCHIVADO' THEN 1 ELSE 0 END) AS total_archivadas,
            SUM(CASE WHEN venta.ven_estado IN ('ANULADA_EN_PENDIENTE', 'ANULADA_EN_ARCHIVADA') THEN 1 ELSE 0 END) AS total_anuladas
        ", false);
        $this->aplicarFiltrosDashboard($builder, $filtros);

        return $builder->get()->getRow() ?? (object) [
            'total_documentos' => 0,
            'total_ventas' => 0,
            'total_iva' => 0,
            'total_borradores' => 0,
            'total_archivadas' => 0,
            'total_anuladas' => 0,
        ];
    }

    public function getDashboardEstados(array $filtros): array {

        $builder = $this->db->table("cc_ventas venta");
        $builder->select("venta.ven_estado AS estado, COUNT(*) AS total");
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $builder->groupBy("venta.ven_estado");
        $builder->orderBy("venta.ven_estado", "ASC");

        return $builder->get()->getResult();
    }

    public function getDashboardComprobantes(array $filtros): array {

        $builder = $this->db->table("cc_ventas venta");
        $builder->select("venta.ven_tipo_comprobante_cod AS codigo, comprobante.comp_nombre AS nombre, COUNT(*) AS total, SUM(venta.ven_total) AS valor");
        $builder->join("cc_tipos_comprobante comprobante", "comprobante.comp_codigo = venta.ven_tipo_comprobante_cod", "left");
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $builder->where("venta.ven_estado", "ARCHIVADO");
        $builder->groupBy("venta.ven_tipo_comprobante_cod, comprobante.comp_nombre");
        $builder->orderBy("valor", "DESC");

        return $builder->get()->getResult();
    }

    public function getDashboardTopClientes(array $filtros): array {

        $builder = $this->db->table("cc_ventas venta");
        $builder->select("cliente.id, cliente.clie_razon_social, cliente.clie_dni, COUNT(*) AS documentos, SUM(venta.ven_total) AS valor");
        $builder->join("cc_clientes cliente", "cliente.id = venta.fk_cliente");
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $builder->where("venta.ven_estado", "ARCHIVADO");
        $builder->groupBy("cliente.id, cliente.clie_razon_social, cliente.clie_dni");
        $builder->orderBy("valor", "DESC");
        $builder->limit(10);

        return $builder->get()->getResult();
    }

    public function getDashboardTendenciaMensual(array $filtros): array {

        $builder = $this->db->table("cc_ventas venta");
        $builder->select("DATE_FORMAT(venta.ven_fecha_emision, '%Y-%m') AS periodo, SUM(venta.ven_total) AS valor, COUNT(*) AS documentos", false);
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $builder->where("venta.ven_estado", "ARCHIVADO");
        $builder->groupBy("DATE_FORMAT(venta.ven_fecha_emision, '%Y-%m')", false);
        $builder->orderBy("periodo", "ASC");

        return $builder->get()->getResult();
    }

    public function getDashboardBodegas(array $filtros): array {

        $builder = $this->db->table("cc_ventas venta");
        $builder->select("bodega.bod_nombre, COUNT(*) AS documentos, SUM(venta.ven_total) AS valor");
        $builder->join("cc_bodegas bodega", "bodega.id = venta.fk_bodega", "left");
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $builder->where("venta.ven_estado", "ARCHIVADO");
        $builder->groupBy("bodega.id, bodega.bod_nombre");
        $builder->orderBy("valor", "DESC");

        return $builder->get()->getResult();
    }

    private function aplicarFiltrosDashboard($builder, array $filtros): void {

        $builder->where("venta.fk_proyecto", getProyectoId());

        if (!empty($filtros['fechaDesde'])) {
            $builder->where("venta.ven_fecha_emision >=", $filtros['fechaDesde']);
        }

        if (!empty($filtros['fechaHasta'])) {
            $builder->where("venta.ven_fecha_emision <=", $filtros['fechaHasta']);
        }

        if (!empty($filtros['clienteId'])) {
            $builder->where("venta.fk_cliente", (int) $filtros['clienteId']);
        }

        if (!empty($filtros['bodegaId'])) {
            $builder->where("venta.fk_bodega", (int) $filtros['bodegaId']);
        }

        if (!empty($filtros['centroCostoId'])) {
            $builder->where("venta.fk_centro_costo", (int) $filtros['centroCostoId']);
        }

        if (!empty($filtros['tipoComprobante'])) {
            $builder->where("venta.ven_tipo_comprobante_cod", $filtros['tipoComprobante']);
        }
    }
}
