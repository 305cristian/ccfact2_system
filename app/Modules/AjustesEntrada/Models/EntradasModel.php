<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of EntradasModel
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 13 oct 2025
 * @time 9:13:04 a.m.
 */

namespace Modules\AjustesEntrada\Models;

use CodeIgniter\Database\BaseBuilder;

class EntradasModel extends \CodeIgniter\Model {

//    public function searchProductoData($codProd) {
//        $builder = $this->db->table('cc_productos tb1');
//        $builder->select("tb1.id,"
//                . " tb1.prod_nombre,"
//                . " tb1.prod_codigo,"
//                . " tb1.prod_costopromedio,"
//                . " tb1.prod_isservicio,"
//                . " tb1.prod_stockactual,"
//                . " tb1.prod_ctrllote, tb2.um_nombre_corto");
//        $builder->join('cc_unidades_medida tb2', 'tb2.id = tb1.fk_unidadmedida');
//        if (ctype_digit($codProd)) {
//            // Busca por ID O por cualquier código de barras
//            $builder->groupStart();
//            $builder->where('tb1.id', $codProd);
//            $builder->orWhere('tb1.prod_codigo', $codProd);
//            $builder->orWhere('tb1.prod_codigobarras', $codProd);
//            $builder->orWhere('tb1.prod_codigobarras2', $codProd);
//            $builder->orWhere('tb1.prod_codigobarras3', $codProd);
//            $builder->groupEnd();
//        } else {
//            // Busca solo por códigos (no puede ser ID porque tiene letras)
//            $builder->groupStart();
//            $builder->where('tb1.prod_codigo', $codProd);
//            $builder->orWhere('tb1.prod_codigobarras', $codProd);
//            $builder->orWhere('tb1.prod_codigobarras2', $codProd);
//            $builder->orWhere('tb1.prod_codigobarras3', $codProd);
//            $builder->groupEnd();
//        }
//        $builder->where('tb1.prod_estado', 1);
//        $builder->limit(1);
//
//        $response = $builder->get();
//
//        if ($response->getNumRows() > 0) {
//            return $response->getRow();
//        } else {
//            return false;
//        }
//    }

//    public function searchProductoDataById($idProd) {
//        $builder = $this->db->table('cc_productos tb1');
//        $builder->select("tb1.id,"
//                . " tb1.prod_nombre,"
//                . " tb1.prod_codigo,"
//                . " tb1.prod_costopromedio,"
//                . " tb1.prod_isservicio,"
//                . " tb1.prod_stockactual,"
//                . " tb1.prod_ctrllote, tb2.um_nombre_corto");
//        $builder->join('cc_unidades_medida tb2', 'tb2.id = tb1.fk_unidadmedida');
//
//        $builder->where('tb1.id', $idProd);
//
//        $builder->where('tb1.prod_estado', 1);
//        $builder->limit(1);
//
//        $response = $builder->get();
//
//        if ($response->getNumRows() > 0) {
//            return $response->getRow();
//        } else {
//            return false;
//        }
//    }

    public function searchAjustes($filtros) {
        $builder = $this->db->table('cc_ajuste_entrada tb1');
        $builder->select('tb1.*,'
                . ' tb2.bod_nombre,'
                . 'tb3.prov_razon_social,'
                . 'CONCAT(tb4.emp_nombre," ", tb4.emp_apellido) user_create,'
                . 'tb5.cc_nombre');
        $builder->join('cc_bodegas tb2', 'tb2.id =tb1.fk_bodega');
        $builder->join('cc_proveedores tb3', 'tb3.id =tb1.fk_proveedor');
        $builder->join('cc_empleados tb4', 'tb4.id =tb1.fk_user_id');
        $builder->join('cc_centroscosto tb5', 'tb5.id =tb1.fk_centro_costo');

        // Mapeo de filtros a columnas de BD
        $camposBD = [
            'ajenSecuencial' => 'ajen_secuencial',
            'ajenBodega' => 'fk_bodega',
            'ajenMotivo' => 'fk_motivo_ajuste',
            'ajenCentrocosto' => 'fk_centro_costo',
            'ajenEstado' => 'ajen_estado',
            'ajenTipo' => 'ajen_tipo'
        ];

        // Aplicar filtros dinámicamente
        foreach ($camposBD as $filtro => $columnaBD) {
            if (!empty($filtros[$filtro])) {
                $builder->where($columnaBD, $filtros[$filtro]);
            }
        }

        // Verificar si viene el filtro de fechas
        if (!empty($filtros['ajenFechas'])) {
            $rangoFechas = explode(' a ', $filtros['ajenFechas']);
            $fDesde = trim($rangoFechas[0]);
            $fHasta = isset($rangoFechas[1]) ? trim($rangoFechas[1]) : trim($rangoFechas[0]);
            $builder->where(['ajen_fecha <=' => $fHasta, 'ajen_fecha >= ' => $fDesde]);
        }


        $builder->orderBy('ajen_fecha', 'ASC');
        $builder->orderBy('ajen_secuencial', 'ASC');

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function getDataDetalle($idAjuste) {
        $builder = $this->db->table('cc_ajuste_entrada tb1');
        $builder->select('tb1.ajen_secuencial, tb1.ajen_fecha, tb1.ajen_estado, tb1.ajen_observaciones, tb1.ajen_items_duplicados,'
                . ' tb2.id id_bodega,'
                . ' tb2.bod_nombre,'
                . ' tb3.prov_ruc,'
                . ' tb3.prov_razon_social,'
                . ' CONCAT(tb4.emp_nombre," ", tb4.emp_apellido) user_create,'
                . ' tb5.cc_nombre, tb6.mot_nombre');
        $builder->join('cc_bodegas tb2', 'tb2.id = tb1.fk_bodega');
        $builder->join('cc_proveedores tb3', 'tb3.id = tb1.fk_proveedor');
        $builder->join('cc_empleados tb4', 'tb4.id = tb1.fk_user_id');
        $builder->join('cc_centroscosto tb5', 'tb5.id = tb1.fk_centro_costo');
        $builder->join('cc_motivos_ajuste tb6', 'tb6.id = tb1.fk_motivo_ajuste');
        $builder->where('tb1.id', $idAjuste);

        $ajuste = $builder->get()->getRow();

        if ($ajuste) {
            // Obtener detalle
            $builderDet = $this->db->table('cc_ajuste_entrada_det tb3');
            $builderDet->select('tb4.prod_codigo, tb4.prod_nombre,'
                    . ' tb3.fk_producto,'
                    . ' tb3.ajend_itemcantidad,'
                    . ' tb3.ajend_itemcosto,'
                    . ' tb3.ajend_itemcostoxcantidad,'
                    . ' tb5.lot_lote,'
                    . ' tb5.lot_fecha_elaboracion,'
                    . ' tb5.lot_fecha_caducidad');
            $builderDet->join('cc_productos tb4', 'tb4.id = tb3.fk_producto');
            $builderDet->join('cc_lotes tb5', 'tb5.id = tb3.fk_lote', 'left');
            $builderDet->where('tb3.fk_ajuste_entrada', $idAjuste);

            $ajuste->detalle = $builderDet->get()->getResult();

            return $ajuste;
        } else {
            return false;
        }
    }

    public function getDashboardResumen(array $filtros): object {

        $builder = $this->db->table("cc_ajuste_entrada ajuste");
        $builder->select("
            COUNT(*) AS total_ajustes,
            SUM(CASE WHEN ajuste.ajen_estado = 2 THEN 1 ELSE 0 END) AS total_archivados,
            SUM(CASE WHEN ajuste.ajen_estado = 1 THEN 1 ELSE 0 END) AS total_borradores,
            SUM(CASE WHEN ajuste.ajen_estado = -1 THEN 1 ELSE 0 END) AS total_anulados,
            SUM(CASE WHEN ajuste.ajen_estado = 2 THEN ajuste.ajen_total ELSE 0 END) AS total_valor
        ", false);
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $ajustes = $builder->get()->getRow();

        $builderItems = $this->db->table("cc_ajuste_entrada_det detalle");
        $builderItems->select("SUM(detalle.ajend_itemcantidad) AS total_items", false);
        $builderItems->join("cc_ajuste_entrada ajuste", "ajuste.id = detalle.fk_ajuste_entrada");
        $this->aplicarFiltrosDashboard($builderItems, $filtros);
        $items = $builderItems->get()->getRow();

        return (object) [
            "total_ajustes" => (int) ($ajustes->total_ajustes ?? 0),
            "total_archivados" => (int) ($ajustes->total_archivados ?? 0),
            "total_borradores" => (int) ($ajustes->total_borradores ?? 0),
            "total_anulados" => (int) ($ajustes->total_anulados ?? 0),
            "total_valor" => (float) ($ajustes->total_valor ?? 0),
            "total_items" => (float) ($items->total_items ?? 0),
        ];
    }

    public function getDashboardEstados(array $filtros): array {

        $builder = $this->db->table("cc_ajuste_entrada ajuste");
        $builder->select("ajuste.ajen_estado AS estado, COUNT(*) AS total");
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $builder->groupBy("ajuste.ajen_estado");
        $builder->orderBy("total", "DESC");
        return $builder->get()->getResult();
    }

    public function getDashboardMotivos(array $filtros): array {

        $builder = $this->db->table("cc_ajuste_entrada ajuste");
        $builder->select("motivo.mot_nombre AS motivo, COUNT(*) AS total, SUM(ajuste.ajen_total) AS valor");
        $builder->join("cc_motivos_ajuste motivo", "motivo.id = ajuste.fk_motivo_ajuste", "left");
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $builder->where("ajuste.ajen_estado", 2);
        $builder->groupBy("motivo.id, motivo.mot_nombre");
        $builder->orderBy("valor", "DESC");
        return $builder->get()->getResult();
    }

    public function getDashboardBodegas(array $filtros): array {

        $builder = $this->db->table("cc_ajuste_entrada ajuste");
        $builder->select("bodega.bod_nombre AS bodega, COUNT(*) AS total, SUM(ajuste.ajen_total) AS valor");
        $builder->join("cc_bodegas bodega", "bodega.id = ajuste.fk_bodega", "left");
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $builder->where("ajuste.ajen_estado", 2);
        $builder->groupBy("bodega.id, bodega.bod_nombre");
        $builder->orderBy("valor", "DESC");
        return $builder->get()->getResult();
    }

    public function getDashboardTendenciaMensual(array $filtros): array {

        $builder = $this->db->table("cc_ajuste_entrada ajuste");
        $builder->select("DATE_FORMAT(ajuste.ajen_fecha, '%Y-%m') AS periodo, SUM(ajuste.ajen_total) AS valor, COUNT(*) AS total");
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $builder->where("ajuste.ajen_estado", 2);
        $builder->groupBy("DATE_FORMAT(ajuste.ajen_fecha, '%Y-%m')");
        $builder->orderBy("periodo", "ASC");
        return $builder->get()->getResult();
    }

    public function getDashboardCentrosCosto(array $filtros): array {

        $builder = $this->db->table("cc_ajuste_entrada ajuste");
        $builder->select("centro.cc_nombre AS centro_costo, COUNT(*) AS total, SUM(ajuste.ajen_total) AS valor");
        $builder->join("cc_centroscosto centro", "centro.id = ajuste.fk_centro_costo", "left");
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $builder->where("ajuste.ajen_estado", 2);
        $builder->groupBy("centro.id, centro.cc_nombre");
        $builder->orderBy("valor", "DESC");
        return $builder->get()->getResult();
    }

    private function aplicarFiltrosDashboard(BaseBuilder $builder, array $filtros): void {

        if (!empty($filtros["fechaDesde"])) {
            $builder->where("ajuste.ajen_fecha >=", $filtros["fechaDesde"]);
        }

        if (!empty($filtros["fechaHasta"])) {
            $builder->where("ajuste.ajen_fecha <=", $filtros["fechaHasta"]);
        }

        if (!empty($filtros["bodegaId"])) {
            $builder->where("ajuste.fk_bodega", $filtros["bodegaId"]);
        }

        if (!empty($filtros["motivoId"])) {
            $builder->where("ajuste.fk_motivo_ajuste", $filtros["motivoId"]);
        }

        if (!empty($filtros["centroCostoId"])) {
            $builder->where("ajuste.fk_centro_costo", $filtros["centroCostoId"]);
        }

        if (!empty($filtros["estado"])) {
            $builder->where("ajuste.ajen_estado", $filtros["estado"]);
        }

        if (!empty($filtros["tipo"])) {
            $builder->where("ajuste.ajen_tipo", $filtros["tipo"]);
        }
    }
}
