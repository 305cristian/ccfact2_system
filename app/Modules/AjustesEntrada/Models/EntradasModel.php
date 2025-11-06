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

class EntradasModel extends \CodeIgniter\Model {

    public function searchProductoData($codProd) {
        $builder = $this->db->table('cc_productos tb1');
        $builder->select("tb1.id,"
                . " tb1.prod_nombre,"
                . " tb1.prod_codigo,"
                . " tb1.prod_costopromedio,"
                . " tb1.prod_isservicio,"
                . " tb1.prod_stockactual,"
                . " tb1.prod_ctrllote, tb2.um_nombre_corto");
        $builder->join('cc_unidades_medida tb2', 'tb2.id = tb1.fk_unidadmedida');
        if (ctype_digit($codProd)) {
            // Busca por ID O por cualquier código de barras
            $builder->groupStart();
            $builder->where('tb1.id', $codProd);
            $builder->orWhere('tb1.prod_codigo', $codProd);
            $builder->orWhere('tb1.prod_codigobarras', $codProd);
            $builder->orWhere('tb1.prod_codigobarras2', $codProd);
            $builder->orWhere('tb1.prod_codigobarras3', $codProd);
            $builder->groupEnd();
        } else {
            // Busca solo por códigos (no puede ser ID porque tiene letras)
            $builder->groupStart();
            $builder->where('tb1.prod_codigo', $codProd);
            $builder->orWhere('tb1.prod_codigobarras', $codProd);
            $builder->orWhere('tb1.prod_codigobarras2', $codProd);
            $builder->orWhere('tb1.prod_codigobarras3', $codProd);
            $builder->groupEnd();
        }
        $builder->where('tb1.prod_estado', 1);
        $builder->limit(1);

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getRow();
        } else {
            return false;
        }
    }

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
            'ajenEstado' => 'ajen_estado'
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
}
