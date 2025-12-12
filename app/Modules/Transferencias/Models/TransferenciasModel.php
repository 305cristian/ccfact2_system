<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Transferencias\Models;

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
        $builder->join('cc_empleados tb5', 'tb5.id =tb1.fk_user_confirma');
        $builder->join('cc_centroscosto tb6', 'tb6.id =tb1.fk_centro_costo');

        // Mapeo de filtros a columnas de BD
        $camposBD = [
            'trbSecuencial' => 'trb_secuencial',
            'trbBodegaOrigen' => 'fk_bodega_origen',
            'trbBodegaDestino' => 'fk_bodega_destino',
            'trbEstado' => 'trb_estado',
            'trbUsuarioConfirmar' => 'fk_user_confirma'
        ];

        // Aplicar filtros dinámicamente
        foreach ($camposBD as $filtro => $columnaBD) {
            if (!empty($filtros[$filtro])) {
                $builder->where($columnaBD, $filtros[$filtro]);
            }
        }

        // Verificar si viene el filtro de fechas
        if (!empty($filtros['trbFechas'])) {
            $rangoFechas = explode(' a ', $filtros['trbFechas']);
            $fDesde = trim($rangoFechas[0]);
            $fHasta = isset($rangoFechas[1]) ? trim($rangoFechas[1]) : trim($rangoFechas[0]);
            $builder->where(['trb_fecha <=' => $fHasta, 'trb_fecha >= ' => $fDesde]);
        }


        $builder->orderBy('trb_fecha', 'ASC');
        $builder->orderBy('trb_secuencial', 'ASC');

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
        $builder->join('cc_empleados tb5', 'tb5.id = tb1.fk_user_confirma');
        $builder->join('cc_centroscosto tb6', 'tb6.id = tb1.fk_centro_costo');
        $builder->where('tb1.id', $transferenciaId);

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
}
