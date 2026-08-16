<?php

namespace Modules\Ventas\Models;

use CodeIgniter\Database\BaseBuilder;
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

    public function searchVentas(array $filtros): array {

        $builder = $this->db->table('cc_ventas venta');
        $builder->select(
                'venta.*,'
                . ' cliente.clie_razon_social AS cliente,'
                . ' cliente.clie_dni,'
                . ' cliente.clie_email,'
                . ' bodega.bod_nombre AS bodega,'
                . ' centro.cc_nombre AS centro_costo,'
                . ' tipoVenta.tv_nombre AS tipo_venta,'
                . ' tipoVenta.tv_codigo AS tipo_venta_codigo,'
                . ' tipoComprobante.comp_nombre AS tipo_comprobante,'
                . ' CONCAT(usuario.emp_nombre, " ", usuario.emp_apellido) AS usuario_registra'
        );
        $builder->join('cc_clientes cliente', 'cliente.id = venta.fk_cliente');
        $builder->join('cc_bodegas bodega', 'bodega.id = venta.fk_bodega', 'left');
        $builder->join('cc_centroscosto centro', 'centro.id = venta.fk_centro_costo', 'left');
        $builder->join('cc_tipo_venta tipoVenta', 'tipoVenta.id = venta.fk_tipo_venta', 'left');
        $builder->join('cc_tipos_comprobante tipoComprobante', 'tipoComprobante.comp_codigo = venta.ven_tipo_comprobante_cod', 'left');
        $builder->join('cc_empleados usuario', 'usuario.id = venta.fk_user', 'left');
        $builder->where('venta.fk_proyecto', getProyectoId());

        $campos = [
            'venSecuencial' => 'venta.ven_secuencial',
            'venCliente' => 'venta.fk_cliente',
            'venBodega' => 'venta.fk_bodega',
            'venCentroCosto' => 'venta.fk_centro_costo',
            'venTipoComprobante' => 'venta.ven_tipo_comprobante_cod',
            'venTipoVenta' => 'venta.fk_tipo_venta',
            'venEstado' => 'venta.ven_estado',
        ];

        foreach ($campos as $filtro => $campo) {
            if (isset($filtros[$filtro]) && $filtros[$filtro] !== '') {
                $builder->where($campo, $filtros[$filtro]);
            }
        }

        if (!empty($filtros['venComprobante'])) {
            $builder->like('venta.ven_numero_comprobante', trim((string) $filtros['venComprobante']));
        }

        $this->aplicarRangoFechas($builder, $filtros['venFechasEmision'] ?? null, 'venta.ven_fecha_emision');
        $this->aplicarRangoFechas($builder, $filtros['venFechasArchivado'] ?? null, 'venta.ven_fecha_archivada', true);

        $builder->orderBy('venta.ven_fecha_emision', 'DESC');
        $builder->orderBy('venta.ven_secuencial', 'DESC');

        return $builder->get()->getResult();
    }

    public function contadoresVentas(?string $fechasEmision, ?string $fechasArchivado): array {

        $builder = $this->db->table('cc_ventas venta');
        $builder->select('venta.ven_estado, COUNT(*) AS total');
        $builder->where('venta.fk_proyecto', getProyectoId());

        $this->aplicarRangoFechas($builder, $fechasEmision, 'venta.ven_fecha_emision');
        $this->aplicarRangoFechas($builder, $fechasArchivado, 'venta.ven_fecha_archivada', true);

        $builder->groupBy('venta.ven_estado');

        return $builder->get()->getResult();
    }

    public function getDataDetalle(int $ventaId): object|false {

        $builder = $this->db->table('cc_ventas venta');
        $builder->select(
                'venta.*,'
                . ' cliente.clie_dni,'
                . ' cliente.clie_razon_social,'
                . ' cliente.clie_direccion,'
                . ' cliente.clie_telefono,'
                . ' cliente.clie_email,'
                . ' bodega.bod_nombre,'
                . ' centro.cc_nombre,'
                . ' tipoVenta.tv_nombre,'
                . ' tipoVenta.tv_codigo,'
                . ' tipoComprobante.comp_nombre AS comprobante_nombre,'
                . ' CONCAT(usuario.emp_nombre, " ", usuario.emp_apellido) AS user_create,'
                . ' CONCAT(usuarioAnulacion.emp_nombre, " ", usuarioAnulacion.emp_apellido) AS usuario_anulacion'
        );
        $builder->join('cc_clientes cliente', 'cliente.id = venta.fk_cliente');
        $builder->join('cc_bodegas bodega', 'bodega.id = venta.fk_bodega', 'left');
        $builder->join('cc_centroscosto centro', 'centro.id = venta.fk_centro_costo', 'left');
        $builder->join('cc_tipo_venta tipoVenta', 'tipoVenta.id = venta.fk_tipo_venta', 'left');
        $builder->join('cc_tipos_comprobante tipoComprobante', 'tipoComprobante.comp_codigo = venta.ven_tipo_comprobante_cod', 'left');
        $builder->join('cc_empleados usuario', 'usuario.id = venta.fk_user', 'left');
        $builder->join('cc_empleados usuarioAnulacion', 'usuarioAnulacion.id = venta.fk_user_anulacion', 'left');
        $builder->where('venta.id', $ventaId);
        $builder->where('venta.fk_proyecto', getProyectoId());

        $venta = $builder->get()->getRow();

        if (!$venta) {
            return false;
        }

        $builderDetalle = $this->db->table('cc_ventas_det detalle');
        $builderDetalle->select(
                'detalle.*,'
                . ' producto.prod_codigo,'
                . ' producto.prod_nombre,'
                . ' producto.prod_ctrllote,'
                . ' producto.prod_isservicio,'
                . ' unidad.um_nombre_corto,'
                . ' tarifa.impt_detalle AS impuesto_detalle,'
                . ' COALESCE(lote.lot_lote, detalle.vend_lote) AS lote,'
                . ' COALESCE(lote.lot_fecha_elaboracion, detalle.vend_fecha_elaboracion) AS fecha_elaboracion,'
                . ' COALESCE(lote.lot_fecha_caducidad, detalle.vend_fecha_caducidad) AS fecha_caducidad'
        );
        $builderDetalle->join('cc_productos producto', 'producto.id = detalle.fk_producto');
        $builderDetalle->join('cc_unidades_medida unidad', 'unidad.id = producto.fk_unidadmedida', 'left');
        $builderDetalle->join('cc_impuesto_tarifa tarifa', 'tarifa.id = detalle.fk_impuesto_tarifa', 'left');
        $builderDetalle->join('cc_lotes lote', 'lote.id = detalle.fk_lote', 'left');
        $builderDetalle->where('detalle.fk_venta', $ventaId);
        $builderDetalle->where('detalle.fk_proyecto', getProyectoId());
        $builderDetalle->where('detalle.vend_estado', 1);
        $builderDetalle->orderBy('detalle.id', 'ASC');

        $venta->detalle = $builderDetalle->get()->getResult();

        $venta->basesImpuestos = $this->db
                ->table('cc_ventas_bases_impuesto')
                ->where('fk_venta', $ventaId)
                ->where('fk_proyecto', getProyectoId())
                ->orderBy('imp_porcentaje', 'ASC')
                ->get()
                ->getResult();

        return $venta;
    }

    public function getAsientosContablesVenta(int $ventaId): array {

        $codigosDocumentoVenta = ['01', '22'];
        $cobrosIds = $this->getCobrosIdsVenta($ventaId);

        $builder = $this->db->table('cc_asiento_contable asiento');
        $builder->select('asiento.*, CONCAT(usuario.emp_nombre, " ", usuario.emp_apellido) AS usuario_registra');
        $builder->join('cc_empleados usuario', 'usuario.id = asiento.fk_user_id', 'left');
        $builder->where('asiento.fk_proyecto', getProyectoId());
        $builder->groupStart();
        $builder->groupStart();
        $builder->whereIn('asiento.ac_codigo_transaccion', $codigosDocumentoVenta);
        $builder->where('asiento.ac_documento_id', $ventaId);
        $builder->groupEnd();

        if ($cobrosIds) {
            $builder->orGroupStart();
            $builder->where('asiento.ac_codigo_transaccion', '05');
            $builder->whereIn('asiento.ac_documento_id', $cobrosIds);
            $builder->groupEnd();
        }

        $builder->groupEnd();
        $builder->where('asiento.ac_estado', 1);
        $builder->orderBy('asiento.ac_codigo_transaccion', 'ASC');
        $builder->orderBy('asiento.id', 'ASC');

        $asientos = $builder->get()->getResult();

        foreach ($asientos as $asiento) {
            $asiento->detalle = $this->getDetalleAsiento((int) $asiento->id);
            $asiento->totalDebe = 0.0;
            $asiento->totalHaber = 0.0;

            foreach ($asiento->detalle as $detalle) {
                if ($detalle->acd_tipo === 'DEBE') {
                    $asiento->totalDebe += (float) $detalle->acd_valor;
                }

                if ($detalle->acd_tipo === 'HABER') {
                    $asiento->totalHaber += (float) $detalle->acd_valor;
                }
            }
        }

        return $asientos;
    }

    private function getCobrosIdsVenta(int $ventaId): array {

        $cxc = $this->db->table('cc_cxc')
                ->select('id')
                ->where('fk_venta', $ventaId)
                ->where('fk_proyecto', getProyectoId())
                ->get()
                ->getRow();

        if (!$cxc) {
            return [];
        }

        $detalles = $this->db->table('cc_cobros_det')
                ->select('fk_cobro')
                ->where('fk_cxc', (int) $cxc->id)
                ->where('fk_proyecto', getProyectoId())
                ->groupBy('fk_cobro')
                ->get()
                ->getResult();

        return array_map(static fn($detalle) => (int) $detalle->fk_cobro, $detalles);
    }

    private function getDetalleAsiento(int $asientoId): array {

        return $this->db
                        ->table('cc_asiento_contable_det detalle')
                        ->select('detalle.*, cuenta.ctad_nombre_cuenta AS cuenta_contable, centro.cc_nombre AS centro_costo')
                        ->join('cc_cuenta_contabledet cuenta', 'cuenta.ctad_codigo = detalle.codigo_cuenta_contable', 'left')
                        ->join('cc_centroscosto centro', 'centro.id = detalle.fk_centro_costos', 'left')
                        ->where('detalle.fk_asiento_contable', $asientoId)
                        ->where('detalle.fk_proyecto', getProyectoId())
                        ->orderBy('detalle.id', 'ASC')
                        ->get()
                        ->getResult();
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

    private function aplicarRangoFechas(BaseBuilder $builder, ?string $rango, string $campo, bool $incluyeHora = false): void {

        if (!$rango) {
            return;
        }

        $fechas = explode(' a ', $rango);
        $desde = trim($fechas[0]);
        $hasta = trim($fechas[1] ?? $fechas[0]);

        if ($incluyeHora) {
            $desde .= ' 00:00:00';
            $hasta .= ' 23:59:59';
        }

        $builder->where($campo . ' >=', $desde);
        $builder->where($campo . ' <=', $hasta);
    }
}
