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
}
