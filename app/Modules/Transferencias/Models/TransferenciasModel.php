<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Transferencias\Models;

use CodeIgniter\Database\BaseBuilder;

/**
 * Description of TransferenciasModel
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 9 dic 2025
 * @time 8:29:25 p.m.
 */
class TransferenciasModel extends \CodeIgniter\Model {

    public function searchTransferencias($filtros) {
        $builder = $this->db->table('cc_transferencia_bodega tb1');
        $builder->select('tb1.*,'
                . ' tb2.bod_nombre AS bodega_origen,'
                . ' tb3.bod_nombre AS bodega_destino,'
                . 'CONCAT(tb4.emp_nombre," ", tb4.emp_apellido) user_crea,'
                . 'CONCAT(tb5.emp_nombre," ", tb5.emp_apellido) user_confirma,'
                . 'tb6.cc_nombre');
        $builder->join('cc_bodegas tb2', 'tb2.id =tb1.fk_bodega_origen');
        $builder->join('cc_bodegas tb3', 'tb3.id =tb1.fk_bodega_destino');
        $builder->join('cc_empleados tb4', 'tb4.id =tb1.fk_user_crea');
        $builder->join('cc_empleados tb5', 'tb5.id =tb1.fk_user_confirma','left');
        $builder->join('cc_centroscosto tb6', 'tb6.id =tb1.fk_centro_costo');
        $builder->where('tb1.fk_proyecto', getProyectoId());

        // Mapeo de filtros a columnas de BD
        $camposBD = [
            'trbSecuencial' => 'tb1.trb_secuencial',
            'trbBodegaOrigen' => 'tb1.fk_bodega_origen',
            'trbBodegaDestino' => 'tb1.fk_bodega_destino',
            'trbEstado' => 'tb1.trb_estado',
            'trbUsuarioConfirmar' => 'tb1.fk_user_confirma'
        ];

        // Aplicar filtros dinámicamente
        foreach ($camposBD as $filtro => $columnaBD) {
            if (isset($filtros[$filtro]) && $filtros[$filtro] !== '') {
                $builder->where($columnaBD, $filtros[$filtro]);
            }
        }

        // Verificar si viene el filtro de fechas
        if (!empty($filtros['trbFechas'])) {
            $rangoFechas = explode(' a ', $filtros['trbFechas']);
            $fDesde = trim($rangoFechas[0]);
            $fHasta = isset($rangoFechas[1]) ? trim($rangoFechas[1]) : trim($rangoFechas[0]);
            $builder->where(['tb1.trb_fecha <=' => $fHasta, 'tb1.trb_fecha >= ' => $fDesde]);
        }


        $builder->orderBy('tb1.trb_fecha', 'ASC');
        $builder->orderBy('tb1.trb_secuencial', 'ASC');

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function getDataDetalle($transferenciaId) {

        $builder = $this->db->table('cc_transferencia_bodega tb1');
        $builder->select('tb1.trb_secuencial, tb1.trb_fecha, tb1.trb_estado, tb1.trb_observaciones, tb1.trb_items_duplicados,'
                . ' tb2.id id_bodega_origen,'
                . ' tb2.bod_nombre bod_nombre_origen,'
                . ' tb3.id id_bodega_destino,'
                . ' tb3.bod_nombre bod_nombre_destino,'
                . ' CONCAT(tb4.emp_nombre," ", tb4.emp_apellido) user_create,'
                . ' CONCAT(tb5.emp_nombre," ", tb5.emp_apellido) user_confirm,'
                . ' tb6.cc_nombre');
        $builder->join('cc_bodegas tb2', 'tb2.id = tb1.fk_bodega_origen');
        $builder->join('cc_bodegas tb3', 'tb3.id = tb1.fk_bodega_destino');
        $builder->join('cc_empleados tb4', 'tb4.id = tb1.fk_user_crea');
        $builder->join('cc_empleados tb5', 'tb5.id = tb1.fk_user_confirma','left');
        $builder->join('cc_centroscosto tb6', 'tb6.id = tb1.fk_centro_costo');
        $builder->where('tb1.id', $transferenciaId);
        $builder->where('tb1.fk_proyecto', getProyectoId());

        $ajuste = $builder->get()->getRow();

        if ($ajuste) {
            // Obtener detalle
            $builderDet = $this->db->table('cc_transferencia_bodega_det tb3');
            $builderDet->select('tb4.prod_codigo, tb4.prod_nombre,'
                    . ' tb3.fk_producto,'
                    . ' tb3.fk_lote,'
                    . ' tb3.trbd_itemcantidad,'
                    . ' tb3.trbd_itemcosto,'
                    . ' tb3.trbd_itemcostoxcantidad,'
                    . ' tb5.lot_lote,'
                    . ' tb5.lot_fecha_elaboracion,'
                    . ' tb5.lot_fecha_caducidad');
            $builderDet->join('cc_productos tb4', 'tb4.id = tb3.fk_producto');
            $builderDet->join('cc_lotes tb5', 'tb5.id = tb3.fk_lote', 'left');
            $builderDet->where('tb3.fk_transferencia_bodega', $transferenciaId);

            $ajuste->detalle = $builderDet->get()->getResult();

            return $ajuste;
        } else {
            return false;
        }
    }

    public function contadoresTransferencias($trbFechas) {



        $builder = $this->db->table('cc_transferencia_bodega tb1');
        $builder->select('tb1.trb_estado, COUNT(*) AS total');
        $builder->where('tb1.fk_proyecto', getProyectoId());

        if ($trbFechas) {
            $rangoFechas = explode(' a ', $trbFechas);
            $fDesde = trim($rangoFechas[0]);
            $fHasta = isset($rangoFechas[1]) ? trim($rangoFechas[1]) : trim($rangoFechas[0]);
            $builder->where(['trb_fecha <=' => $fHasta, 'trb_fecha >= ' => $fDesde]);
        }

        $builder->groupBy('tb1.trb_estado');

        $response = $builder->get();
        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function getDashboardResumen(array $filtros): object {

        $builder = $this->db->table("cc_transferencia_bodega transferencia");
        $builder->select("
            COUNT(*) AS total_transferencias,
            SUM(CASE WHEN transferencia.trb_estado = 3 THEN 1 ELSE 0 END) AS total_confirmadas,
            SUM(CASE WHEN transferencia.trb_estado = 2 THEN 1 ELSE 0 END) AS total_por_confirmar,
            SUM(CASE WHEN transferencia.trb_estado = 1 THEN 1 ELSE 0 END) AS total_borradores,
            SUM(CASE WHEN transferencia.trb_estado = 0 THEN 1 ELSE 0 END) AS total_rechazadas,
            SUM(CASE WHEN transferencia.trb_estado = -1 THEN 1 ELSE 0 END) AS total_anuladas,
            SUM(CASE WHEN transferencia.trb_estado = 3 THEN transferencia.trb_total ELSE 0 END) AS total_valor
        ", false);
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $transferencias = $builder->get()->getRow();

        $builderItems = $this->db->table("cc_transferencia_bodega_det detalle");
        $builderItems->select("SUM(detalle.trbd_itemcantidad) AS total_items", false);
        $builderItems->join("cc_transferencia_bodega transferencia", "transferencia.id = detalle.fk_transferencia_bodega");
        $this->aplicarFiltrosDashboard($builderItems, $filtros);
        $items = $builderItems->get()->getRow();

        return (object) [
            "total_transferencias" => (int) ($transferencias->total_transferencias ?? 0),
            "total_confirmadas" => (int) ($transferencias->total_confirmadas ?? 0),
            "total_por_confirmar" => (int) ($transferencias->total_por_confirmar ?? 0),
            "total_borradores" => (int) ($transferencias->total_borradores ?? 0),
            "total_rechazadas" => (int) ($transferencias->total_rechazadas ?? 0),
            "total_anuladas" => (int) ($transferencias->total_anuladas ?? 0),
            "total_valor" => (float) ($transferencias->total_valor ?? 0),
            "total_items" => (float) ($items->total_items ?? 0),
        ];
    }

    public function getDashboardEstados(array $filtros): array {

        $builder = $this->db->table("cc_transferencia_bodega transferencia");
        $builder->select("transferencia.trb_estado AS estado, COUNT(*) AS total");
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $builder->groupBy("transferencia.trb_estado");
        $builder->orderBy("total", "DESC");
        return $builder->get()->getResult();
    }

    public function getDashboardBodegasOrigen(array $filtros): array {

        $builder = $this->db->table("cc_transferencia_bodega transferencia");
        $builder->select("bodega.bod_nombre AS bodega, COUNT(*) AS total, SUM(transferencia.trb_total) AS valor");
        $builder->join("cc_bodegas bodega", "bodega.id = transferencia.fk_bodega_origen", "left");
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $builder->where("transferencia.trb_estado", 3);
        $builder->groupBy("bodega.id, bodega.bod_nombre");
        $builder->orderBy("valor", "DESC");
        return $builder->get()->getResult();
    }

    public function getDashboardBodegasDestino(array $filtros): array {

        $builder = $this->db->table("cc_transferencia_bodega transferencia");
        $builder->select("bodega.bod_nombre AS bodega, COUNT(*) AS total, SUM(transferencia.trb_total) AS valor");
        $builder->join("cc_bodegas bodega", "bodega.id = transferencia.fk_bodega_destino", "left");
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $builder->where("transferencia.trb_estado", 3);
        $builder->groupBy("bodega.id, bodega.bod_nombre");
        $builder->orderBy("valor", "DESC");
        return $builder->get()->getResult();
    }

    public function getDashboardTendenciaMensual(array $filtros): array {

        $builder = $this->db->table("cc_transferencia_bodega transferencia");
        $builder->select("DATE_FORMAT(transferencia.trb_fecha, '%Y-%m') AS periodo, SUM(transferencia.trb_total) AS valor, COUNT(*) AS total");
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $builder->where("transferencia.trb_estado", 3);
        $builder->groupBy("DATE_FORMAT(transferencia.trb_fecha, '%Y-%m')");
        $builder->orderBy("periodo", "ASC");
        return $builder->get()->getResult();
    }

    public function getDashboardUsuariosConfirmacion(array $filtros): array {

        $builder = $this->db->table("cc_transferencia_bodega transferencia");
        $builder->select("CONCAT(usuario.emp_nombre, ' ', usuario.emp_apellido) AS usuario, COUNT(*) AS total, SUM(transferencia.trb_total) AS valor");
        $builder->join("cc_empleados usuario", "usuario.id = transferencia.fk_user_confirma", "left");
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $builder->where("transferencia.trb_estado", 3);
        $builder->groupBy("usuario.id, usuario.emp_nombre, usuario.emp_apellido");
        $builder->orderBy("total", "DESC");
        return $builder->get()->getResult();
    }

    public function getDashboardRutas(array $filtros): array {

        $builder = $this->db->table("cc_transferencia_bodega transferencia");
        $builder->select("CONCAT(origen.bod_nombre, ' -> ', destino.bod_nombre) AS ruta, COUNT(*) AS total, SUM(transferencia.trb_total) AS valor");
        $builder->join("cc_bodegas origen", "origen.id = transferencia.fk_bodega_origen", "left");
        $builder->join("cc_bodegas destino", "destino.id = transferencia.fk_bodega_destino", "left");
        $this->aplicarFiltrosDashboard($builder, $filtros);
        $builder->where("transferencia.trb_estado", 3);
        $builder->groupBy("transferencia.fk_bodega_origen, transferencia.fk_bodega_destino, origen.bod_nombre, destino.bod_nombre");
        $builder->orderBy("valor", "DESC");
        $builder->limit(8);
        return $builder->get()->getResult();
    }

    private function aplicarFiltrosDashboard(BaseBuilder $builder, array $filtros): void {
        $builder->where("transferencia.fk_proyecto", getProyectoId());

        if (!empty($filtros["fechaDesde"])) {
            $builder->where("transferencia.trb_fecha >=", $filtros["fechaDesde"]);
        }

        if (!empty($filtros["fechaHasta"])) {
            $builder->where("transferencia.trb_fecha <=", $filtros["fechaHasta"]);
        }

        if (!empty($filtros["bodegaOrigenId"])) {
            $builder->where("transferencia.fk_bodega_origen", $filtros["bodegaOrigenId"]);
        }

        if (!empty($filtros["bodegaDestinoId"])) {
            $builder->where("transferencia.fk_bodega_destino", $filtros["bodegaDestinoId"]);
        }

        if (!empty($filtros["usuarioConfirmarId"])) {
            $builder->where("transferencia.fk_user_confirma", $filtros["usuarioConfirmarId"]);
        }
    }
}
