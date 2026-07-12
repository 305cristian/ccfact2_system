<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of ComprasModel
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 5 jul 2026
 * @time 10:44:57 a.m.
 */

namespace Modules\Compras\Models;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

class ComprasModel extends Model {

    public function getDataDetalle(int $compraId): object|false {

        $builder = $this->db->table('cc_compras compra');

        $builder->select(
                'compra.*,'
                . ' proveedor.prov_ruc,'
                . ' proveedor.prov_razon_social,'
                . ' proveedor.prov_direccion,'
                . ' proveedor.prov_telefono,'
                . ' bodega.bod_nombre,'
                . ' centro.cc_nombre,'
                . ' tipoCompra.tc_nombre,'
                . ' tipoComprobante.comp_nombre AS comprobante_nombre,'
                . ' sustento.sus_nombre,'
                . ' formaPago.fp_nombre,'
                . ' CONCAT(usuario.emp_nombre, " ", usuario.emp_apellido) AS user_create,'
                . ' CONCAT(usuarioAnulacion.emp_nombre, " ", usuarioAnulacion.emp_apellido) AS usuario_anulacion'
        );

        $builder->join('cc_proveedores proveedor', 'proveedor.id = compra.fk_proveedor');
        $builder->join('cc_bodegas bodega', 'bodega.id = compra.fk_bodega', 'left');
        $builder->join('cc_centroscosto centro', 'centro.id = compra.fk_centro_costo', 'left');
        $builder->join('cc_tipo_compra tipoCompra', 'tipoCompra.id = compra.fk_tipo_compra', 'left');
        $builder->join('cc_tipos_comprobante tipoComprobante', 'tipoComprobante.comp_codigo = compra.comp_tipo_comprobante_cod', 'left');
        $builder->join('cc_sustentos sustento', 'sustento.sus_codigo = compra.cod_sustento', 'left');
        $builder->join('cc_formas_pago formaPago', 'formaPago.cod = compra.cod_forma_pago', 'left');
        $builder->join('cc_empleados usuario', 'usuario.id = compra.fk_user', 'left');
        $builder->join('cc_empleados usuarioAnulacion', 'usuarioAnulacion.id = compra.fk_user_anulacion', 'left');
        $builder->where('compra.id', $compraId);

        $compra = $builder->get()->getRow();

        if (!$compra) {
            return false;
        }

        $builderDetalle = $this->db->table('cc_compras_det detalle');

        $builderDetalle->select(
                'detalle.*,'
                . ' producto.prod_codigo,'
                . ' producto.prod_nombre,'
                . ' producto.prod_ctrllote,'
                . ' producto.prod_isservicio,'
                . ' unidad.um_nombre_corto,'
                . ' tarifa.impt_detalle AS impuesto_detalle,'
                . ' COALESCE(lote.lot_lote, detalle.compd_lote) AS lote,'
                . ' COALESCE(lote.lot_fecha_elaboracion, detalle.compd_fecha_elaboracion) AS fecha_elaboracion,'
                . ' COALESCE(lote.lot_fecha_caducidad, detalle.compd_fecha_caducidad) AS fecha_caducidad'
        );

        $builderDetalle->join('cc_productos producto', 'producto.id = detalle.fk_producto');
        $builderDetalle->join('cc_unidades_medida unidad', 'unidad.id = producto.fk_unidadmedida', 'left');
        $builderDetalle->join('cc_impuesto_tarifa tarifa', 'tarifa.id = detalle.fk_impuesto_tarifa', 'left');
        $builderDetalle->join('cc_lotes lote', 'lote.id = detalle.fk_lote', 'left');
        $builderDetalle->where('detalle.fk_compra', $compraId);
        $builderDetalle->where('detalle.compd_estado', 1);
        $builderDetalle->orderBy('detalle.id', 'ASC');

        $compra->detalle = $builderDetalle->get()->getResult();

        $compra->basesImpuestos = $this->db
                ->table('cc_compras_bases_impuesto')
                ->where('fk_compra', $compraId)
                ->where('estado', 1)
                ->orderBy('imp_porcentaje', 'ASC')
                ->get()
                ->getResult();

        $compra->formasPagoAts = $this->obtenerFormasPagoAts($compraId);
        $compra->retencion = $this->obtenerRetencionCompra($compra);
        $compra->cuentaPorPagar = $this->obtenerCuentaPorPagar($compraId);
        $compra->asientoContable = $this->obtenerAsientoContable($compraId);

        return $compra;
    }

    public function searchCompras(array $filtros): array {

        $builder = $this->db->table('cc_compras compra');
        $builder->select(
                'compra.*,'
                . ' proveedor.prov_razon_social AS proveedor,'
                . ' proveedor.prov_ruc,'
                . ' bodega.bod_nombre AS bodega,'
                . ' centro.cc_nombre AS centro_costo,'
                . ' tipoCompra.tc_nombre AS tipo_compra,'
                . ' tipoComprobante.comp_nombre AS tipo_comprobante,'
                . ' CONCAT(usuario.emp_nombre, " ", usuario.emp_apellido) AS usuario_registra'
        );
        $builder->join('cc_proveedores proveedor', 'proveedor.id = compra.fk_proveedor');
        $builder->join('cc_bodegas bodega', 'bodega.id = compra.fk_bodega', 'left');
        $builder->join('cc_centroscosto centro', 'centro.id = compra.fk_centro_costo', 'left');
        $builder->join('cc_tipo_compra tipoCompra', 'tipoCompra.id = compra.fk_tipo_compra', 'left');
        $builder->join('cc_tipos_comprobante tipoComprobante', 'tipoComprobante.comp_codigo = compra.comp_tipo_comprobante_cod', 'left');
        $builder->join('cc_empleados usuario', 'usuario.id = compra.fk_user', 'left');

        $campos = [
            'compSecuencial' => 'compra.comp_secuencial',
            'compProveedor' => 'compra.fk_proveedor',
            'compBodega' => 'compra.fk_bodega',
            'compCentroCosto' => 'compra.fk_centro_costo',
            'compTipoComprobante' => 'compra.comp_tipo_comprobante_cod',
            'compTipoCosto' => 'compra.tipo_costo',
            'compEstado' => 'compra.comp_estado',
        ];

        foreach ($campos as $filtro => $campo) {
            if (isset($filtros[$filtro]) && $filtros[$filtro] !== '') {
                $builder->where($campo, $filtros[$filtro]);
            }
        }

        if (!empty($filtros['compComprobante'])) {
            $comprobante = trim((string) $filtros['compComprobante']);
            $builder->like('compra.comp_numero_comprobante', $comprobante);
        }

        $this->aplicarRangoFechas($builder, $filtros['compFechasEmision'] ?? null, 'compra.comp_fecha_emision');

        $this->aplicarRangoFechas($builder, $filtros['compFechasArchivado'] ?? null, 'compra.comp_fecha_archivada', true);

        $builder->orderBy('compra.comp_fecha_emision', 'DESC');
        $builder->orderBy('compra.comp_secuencial', 'DESC');

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return [];
        }
    }

    public function contadoresCompras(?string $fechasEmision, ?string $fechasArchivado): array {
        $builder = $this->db->table('cc_compras compra');
        $builder->select('compra.comp_estado, COUNT(*) AS total');

        $this->aplicarRangoFechas($builder, $fechasEmision, 'compra.comp_fecha_emision');
        $this->aplicarRangoFechas($builder, $fechasArchivado, 'compra.comp_fecha_archivada', true);

        $builder->groupBy('compra.comp_estado');
        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return [];
        }
    }

    public function existeActivoFijo(array $productoIds): bool {
        if (!$productoIds) {
            return false;
        }

        return $this->db->table('cc_productos')
                        ->whereIn('id', $productoIds)
                        ->where('fk_tipoproducto', 2)
                        ->countAllResults() > 0;
    }

    public function obtenerPuntoEmisionRetencionUsuario(int $empleadoId): ?object {
        $builder = $this->db->table("cc_puntos_venta tb1");
        $builder->select("tb1.id, tb1.pv_establecimiento, tb1.pv_emision, tb1.pv_auth_sri, tb1.pv_fecha_vence_auth, tb1.pv_sec_inicial, tb1.pv_sec_actual, tb1.pv_sec_final, tb1.pv_is_electronica");
        $builder->join("cc_puntoventa_empleado tb2", "tb2.fk_punto_venta = tb1.id");
        $builder->where(["tb1.fk_comprobante" => "07", "tb1.pv_estado" => "1", "tb2.fk_empleado" => $empleadoId]);
        $builder->orderBy("tb1.id", "ASC");
        return $builder->get()->getRow();
    }

    public function obtenerPuntoEmisionRetencion(string $establecimiento, string $emision): ?object {
        $builder = $this->db->table("cc_puntos_venta");
        $builder->where(["fk_comprobante" => "07", "pv_establecimiento" => $establecimiento, "pv_emision" => $emision, "pv_estado" => "1"]);
        return $builder->get()->getRow();
    }

    public function usuarioPuedeEmitirEnPuntoRetencion(int $puntoEmisionId, int $empleadoId): bool {
        $builder = $this->db->table("cc_puntoventa_empleado");
        $builder->where(["fk_punto_venta" => $puntoEmisionId, "fk_empleado" => $empleadoId]);
        return $builder->countAllResults() > 0;
    }

    private function obtenerFormasPagoAts(int $compraId): array {

        return $this->db->table('cc_compras_ats_formas_pago compraAts')
                        ->select('compraAts.fk_forma_pago_ats AS codigo, forma.fp_nombre_sri AS nombre')
                        ->join('cc_formas_pago_sri forma', 'forma.codigo = compraAts.fk_forma_pago_ats')
                        ->where('compraAts.fk_compra', $compraId)
                        ->orderBy('compraAts.id', 'ASC')
                        ->get()
                        ->getResult();
    }

    private function obtenerRetencionCompra(object $compra): ?object {

        if (empty($compra->fk_retencion)) {
            return null;
        }

        $retencion = $this->db->table('cc_retencion retencion')
                ->select('retencion.*, CONCAT(usuario.emp_nombre, " ", usuario.emp_apellido) AS usuario_registra')
                ->join('cc_empleados usuario', 'usuario.id = retencion.fk_user', 'left')
                ->where('retencion.id', $compra->fk_retencion)
                ->where('retencion.ret_estado', 1)
                ->get()
                ->getRow();

        if (!$retencion) {
            return null;
        }

        $retencion->detalle = $this->db->table('cc_retencion_det detalle')
                ->select('detalle.*, sri.ret_nombre, sri.ret_impuesto_detalle')
                ->join('cc_retencion_sri sri', 'sri.id = detalle.fk_sri_retencion', 'left')
                ->where('detalle.fk_retencion', $retencion->id)
                ->orderBy('detalle.id', 'ASC')
                ->get()
                ->getResult();

        return $retencion;
    }

    private function obtenerCuentaPorPagar(int $compraId): ?object {

        $cxp = $this->db->table('cc_cxp cxp')
                ->select('cxp.*, CONCAT(usuario.emp_nombre, " ", usuario.emp_apellido) AS usuario_registra')
                ->join('cc_empleados usuario', 'usuario.id = cxp.fk_user', 'left')
                ->where('cxp.fk_compra', $compraId)
                ->orderBy('cxp.id', 'DESC')
                ->get()
                ->getRow();

        if (!$cxp) {
            return null;
        }

        $cxp->cuotas = $this->db->table('cc_cxp_cuotas cuota')
                ->where('cuota.fk_cxp', $cxp->id)
                ->orderBy('cuota.cxpc_numero', 'ASC')
                ->get()
                ->getResult();

        $cxp->pagos = $this->db->table('cc_pagos_det aplicacion')
                ->select('pago.*,'
                        . ' aplicacion.pgd_valor AS valor_aplicado,'
                        . ' cuota.cxpc_numero AS numero_cuota,'
                        . ' forma.fp_nombre AS forma_pago,'
                        . ' banco.banc_nombre AS banco,'
                        . ' cuenta.ctad_nombre_cuenta AS cuenta_contable'
                )
                ->join('cc_pagos pago', 'pago.id = aplicacion.fk_pago')
                ->join('cc_cxp_cuotas cuota', 'cuota.id = aplicacion.fk_cuota', 'left')
                ->join('cc_formas_pago forma', 'forma.cod = pago.fk_forma_pago', 'left')
                ->join('cc_bancos_list banco', 'banco.id = pago.fk_banco', 'left')
                ->join('cc_cuenta_contabledet cuenta', 'cuenta.ctad_codigo = pago.fk_cuenta_contable', 'left')
                ->where('aplicacion.fk_cxp', $cxp->id)
                ->orderBy('pago.pg_fecha', 'ASC')
                ->orderBy('pago.id', 'ASC')
                ->get()
                ->getResult();

        return $cxp;
    }

    private function obtenerAsientoContable(int $compraId): ?object {

        $asiento = $this->db->table('cc_asiento_contable asiento')
                ->select('asiento.*, CONCAT(usuario.emp_nombre, " ", usuario.emp_apellido) AS usuario_registra')
                ->join('cc_empleados usuario', 'usuario.id = asiento.fk_user_id', 'left')
                ->where('asiento.ac_codigo_transaccion', '02')
                ->where('asiento.ac_documento_id', $compraId)
                ->orderBy('asiento.id', 'DESC')
                ->get()
                ->getRow();

        if (!$asiento) {
            return null;
        }

        $asiento->detalle = $this->db
                ->table('cc_asiento_contable_det detalle')
                ->select('detalle.*, cuenta.ctad_nombre_cuenta AS cuenta_contable, centro.cc_nombre AS centro_costo')
                ->join('cc_cuenta_contabledet cuenta', 'cuenta.ctad_codigo = detalle.codigo_cuenta_contable', 'left')
                ->join('cc_centroscosto centro', 'centro.id = detalle.fk_centro_costos', 'left')
                ->where('detalle.fk_asiento_contable', $asiento->id)
                ->orderBy('detalle.id', 'ASC')
                ->get()
                ->getResult();

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

        return $asiento;
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

    public function getRelacionProducto(int $proveedorId, string $codigoProveedor): ?object {

        $builder = $this->db->table("cc_producto_proveedor tb1");
        $builder->select("fk_producto");
        $builder->join("cc_productos tb2", "tb2.id = tb1.fk_producto");
        $builder->where(['fk_proveedor' => $proveedorId, "codigo_proveedor" => $codigoProveedor, "tb2.prod_estado" => 1]);
        return $builder->get()->getRow();
    }
}
