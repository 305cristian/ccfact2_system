<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of SalidasModel
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 27 nov 2025
 * @time 10:23:15 a.m.
 */

namespace Modules\AjustesSalida\Models;

use CodeIgniter\Database\BaseBuilder;

class SalidasModel extends \CodeIgniter\Model {

    public function searchAjustes($filtros) {
        $builder = $this->db->table('cc_ajuste_salida tb1');
        $builder->select('tb1.*,'
                . ' tb2.bod_nombre,'
                . ' tb6.serv_nombre,'
                . 'tb3.clie_razon_social,'
                . 'CONCAT(tb4.emp_nombre," ", tb4.emp_apellido) user_create,'
                . 'tb5.cc_nombre');
        $builder->join('cc_bodegas tb2', 'tb2.id =tb1.fk_bodega');
        $builder->join('cc_clientes tb3', 'tb3.id =tb1.fk_cliente');
        $builder->join('cc_empleados tb4', 'tb4.id =tb1.fk_user_id');
        $builder->join('cc_centroscosto tb5', 'tb5.id =tb1.fk_centro_costo');
        $builder->join('cc_servicios tb6', 'tb6.id =tb1.fk_servicio');

        // Mapeo de filtros a columnas de BD
        $camposBD = [
            'ajesSecuencial' => 'ajes_secuencial',
            'ajesBodega' => 'fk_bodega',
            'ajesMotivo' => 'fk_motivo_ajuste',
            'ajesCentrocosto' => 'fk_centro_costo',
            'ajesEstado' => 'ajes_estado',
            'ajesTipo' => 'ajes_tipo'
        ];

        // Aplicar filtros dinámicamente
        foreach ($camposBD as $filtro => $columnaBD) {
            if (!empty($filtros[$filtro])) {
                $builder->where($columnaBD, $filtros[$filtro]);
            }
        }

        // Verificar si viene el filtro de fechas
        if (!empty($filtros['ajesFechas'])) {
            $rangoFechas = explode(' a ', $filtros['ajesFechas']);
            $fDesde = trim($rangoFechas[0]);
            $fHasta = isset($rangoFechas[1]) ? trim($rangoFechas[1]) : trim($rangoFechas[0]);
            $builder->where(['ajes_fecha <=' => $fHasta, 'ajes_fecha >= ' => $fDesde]);
        }


        $builder->orderBy('ajes_fecha', 'ASC');
        $builder->orderBy('ajes_secuencial', 'ASC');

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function getDataDetalle($idAjuste) {
        $builder = $this->db->table('cc_ajuste_salida tb1');
        $builder->select('tb1.ajes_secuencial, tb1.ajes_fecha, tb1.ajes_estado, tb1.ajes_observaciones, tb1.ajes_items_duplicados,'
                . ' tb7.serv_nombre,'
                . ' tb2.id id_bodega,'
                . ' tb2.bod_nombre,'
                . ' tb3.clie_dni,'
                . ' tb3.clie_razon_social,'
                . ' CONCAT(tb4.emp_nombre," ", tb4.emp_apellido) user_create,'
                . ' tb5.cc_nombre, tb6.mot_nombre');
        $builder->join('cc_bodegas tb2', 'tb2.id = tb1.fk_bodega');
        $builder->join('cc_clientes tb3', 'tb3.id = tb1.fk_cliente');
        $builder->join('cc_empleados tb4', 'tb4.id = tb1.fk_user_id');
        $builder->join('cc_centroscosto tb5', 'tb5.id = tb1.fk_centro_costo');
        $builder->join('cc_motivos_ajuste tb6', 'tb6.id = tb1.fk_motivo_ajuste');
        $builder->join('cc_servicios tb7', 'tb7.id = tb1.fk_servicio');
        $builder->where('tb1.id', $idAjuste);

        $ajuste = $builder->get()->getRow();

        if ($ajuste) {
            // Obtener detalle
            $builderDet = $this->db->table('cc_ajuste_salida_det tb3');
            $builderDet->select('tb4.prod_codigo, tb4.prod_nombre,'
                    . ' tb3.fk_producto,'
                    . ' tb3.fk_lote,'
                    . ' tb3.ajsd_itemcantidad,'
                    . ' tb3.ajsd_itemcosto,'
                    . ' tb3.ajsd_itemcostoxcantidad,'
                    . ' tb5.lot_lote,'
                    . ' tb5.lot_fecha_elaboracion,'
                    . ' tb5.lot_fecha_caducidad');
            $builderDet->join('cc_productos tb4', 'tb4.id = tb3.fk_producto');
            $builderDet->join('cc_lotes tb5', 'tb5.id = tb3.fk_lote', 'left');
            $builderDet->where('tb3.fk_ajuste_salida', $idAjuste);

            $ajuste->detalle = $builderDet->get()->getResult();

            return $ajuste;
        } else {
            return false;
        }
    }

    public function getDashboardResumen(array $filtros): object {

        $builder = $this->db->table("cc_ajuste_salida ajuste");
        $builder->select("
            COUNT(*) AS total_ajustes,
            SUM(CASE WHEN ajuste.ajes_estado = 2 THEN 1 ELSE 0 END) AS total_archivados,
            SUM(CASE WHEN ajuste.ajes_estado = 1 THEN 1 ELSE 0 END) AS total_borradores,
            SUM(CASE WHEN ajuste.ajes_estado = -1 THEN 1 ELSE 0 END) AS total_anulados,
            SUM(CASE WHEN ajuste.ajes_estado = 2 THEN ajuste.ajes_total ELSE 0 END) AS total_valor
        ", false);
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $ajustes = $builder->get()->getRow();

        $builderItems = $this->db->table("cc_ajuste_salida_det detalle");
        $builderItems->select("SUM(detalle.ajsd_itemcantidad) AS total_items", false);
        $builderItems->join("cc_ajuste_salida ajuste", "ajuste.id = detalle.fk_ajuste_salida");
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

        $builder = $this->db->table("cc_ajuste_salida ajuste");
        $builder->select("ajuste.ajes_estado AS estado, COUNT(*) AS total");
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $builder->groupBy("ajuste.ajes_estado");
        $builder->orderBy("total", "DESC");
        return $builder->get()->getResult();
    }

    public function getDashboardMotivos(array $filtros): array {

        $builder = $this->db->table("cc_ajuste_salida ajuste");
        $builder->select("motivo.mot_nombre AS motivo, COUNT(*) AS total, SUM(ajuste.ajes_total) AS valor");
        $builder->join("cc_motivos_ajuste motivo", "motivo.id = ajuste.fk_motivo_ajuste", "left");
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $builder->where("ajuste.ajes_estado", 2);
        $builder->groupBy("motivo.id, motivo.mot_nombre");
        $builder->orderBy("valor", "DESC");
        return $builder->get()->getResult();
    }

    public function getDashboardBodegas(array $filtros): array {

        $builder = $this->db->table("cc_ajuste_salida ajuste");
        $builder->select("bodega.bod_nombre AS bodega, COUNT(*) AS total, SUM(ajuste.ajes_total) AS valor");
        $builder->join("cc_bodegas bodega", "bodega.id = ajuste.fk_bodega", "left");
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $builder->where("ajuste.ajes_estado", 2);
        $builder->groupBy("bodega.id, bodega.bod_nombre");
        $builder->orderBy("valor", "DESC");
        return $builder->get()->getResult();
    }

    public function getDashboardTendenciaMensual(array $filtros): array {

        $builder = $this->db->table("cc_ajuste_salida ajuste");
        $builder->select("DATE_FORMAT(ajuste.ajes_fecha, '%Y-%m') AS periodo, SUM(ajuste.ajes_total) AS valor, COUNT(*) AS total");
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $builder->where("ajuste.ajes_estado", 2);
        $builder->groupBy("DATE_FORMAT(ajuste.ajes_fecha, '%Y-%m')");
        $builder->orderBy("periodo", "ASC");
        return $builder->get()->getResult();
    }

    public function getDashboardCentrosCosto(array $filtros): array {

        $builder = $this->db->table("cc_ajuste_salida ajuste");
        $builder->select("centro.cc_nombre AS centro_costo, COUNT(*) AS total, SUM(ajuste.ajes_total) AS valor");
        $builder->join("cc_centroscosto centro", "centro.id = ajuste.fk_centro_costo", "left");
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $builder->where("ajuste.ajes_estado", 2);
        $builder->groupBy("centro.id, centro.cc_nombre");
        $builder->orderBy("valor", "DESC");
        return $builder->get()->getResult();
    }

    public function getDashboardServicios(array $filtros): array {

        $builder = $this->db->table("cc_ajuste_salida ajuste");
        $builder->select("servicio.serv_nombre AS servicio, COUNT(*) AS total, SUM(ajuste.ajes_total) AS valor");
        $builder->join("cc_servicios servicio", "servicio.id = ajuste.fk_servicio", "left");
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $builder->where("ajuste.ajes_estado", 2);
        $builder->groupBy("servicio.id, servicio.serv_nombre");
        $builder->orderBy("valor", "DESC");
        return $builder->get()->getResult();
    }

    private function aplicarFiltrosDashboard(BaseBuilder $builder, array $filtros): void {

        if (!empty($filtros["fechaDesde"])) {
            $builder->where("ajuste.ajes_fecha >=", $filtros["fechaDesde"]);
        }

        if (!empty($filtros["fechaHasta"])) {
            $builder->where("ajuste.ajes_fecha <=", $filtros["fechaHasta"]);
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
    }
}
