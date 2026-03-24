<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Inventarios\Models;

/**
 * Description of KardexModel
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 10 mar 2026
 * @time 5:47:38 p.m.
 */
class KardexModel extends \CodeIgniter\Model {

    //put your code here

    public function getKardexProducto(array $filtros): array {

        $tabla = $filtros['kardBodega'] ? 'cc_kardex_bodega tb1' : 'cc_kardex tb1';
        $abrev = $filtros['kardBodega'] ? 'karb' : 'kar';

        $builder = $this->db->table($tabla);

        $builder->select(' tb1.' . $abrev . '_fecha as kardex_fecha,
                            tb1.' . $abrev . '_hora,
                            tb1.' . $abrev . '_kardex,
                            tb1.' . $abrev . '_kardex_total AS kardex_total,
                            tb1.' . $abrev . '_costo_promedio AS kardex_costo_promedio,
                            tb1.' . $abrev . '_costo_ultimo AS kardex_costo_ultimo,
                            tb1.' . $abrev . '_documento_id AS kardex_documento_id,
                            tb1.fk_bodega,
                            tb2.id AS producto_id,
                            tb2.prod_nombre,
                            tb3.tr_codigo,
                            tb3.tr_nombre,
                            tb4.id AS empleado_id,
                            CONCAT_WS(" ", tb4.emp_nombre, tb4.emp_apellido) AS empleado,
                            tb5.lot_lote,
                            tb5.lot_fecha_elaboracion,
                            tb5.lot_fecha_caducidad,
                            tb6.bod_nombre,
                            
                            CASE
                                WHEN tb3.tr_codigo IN ("38","40","42")THEN CONCAT_WS(" ",cli.clie_nombres,cli.clie_apellidos)
                                WHEN tb3.tr_codigo IN ("39","41") THEN CONCAT_WS(" ",prov.prov_nombres,prov.prov_apellidos)
                                -- WHEN tb3.tr_codigo IN ("01","01") THEN CONCAT_WS(" ",cli.clie_nombres,cli.clie_apellidos)
                                -- WHEN tb3.tr_codigo IN ("02","02") THEN CONCAT_WS(" ",prov.prov_nombres,prov.prov_apellidos)
                                ELSE NULL
                            END AS prov_clie_nombre,
  
                            CASE
                                WHEN tb3.tr_codigo IN ("38","40","42") THEN ajs.ajes_secuencial
                                WHEN tb3.tr_codigo IN ("17","44") THEN trb.trb_secuencial
                                WHEN tb3.tr_codigo IN ("39","41") THEN aje.ajen_secuencial
                                -- WHEN tb3.tr_codigo IN ("01","01") THEN vent.vent_secuencial
                                -- WHEN tb3.tr_codigo IN ("02","02") THEN comp.comp_secuencial
                                ELSE NULL
                            END AS num_documento');
        
        $builder->select('CASE WHEN tb1.' . $abrev . '_kardex > 0 THEN tb1.' . $abrev . '_kardex ELSE 0 END AS entrada,
                            CASE WHEN tb1.' . $abrev . '_kardex < 0 THEN tb1.' . $abrev . '_kardex * -1 ELSE 0 END AS salida
                        ', false);

        // JOINs obligatorios
        $builder->join('cc_productos tb2', 'tb2.id = tb1.fk_producto');
        $builder->join('cc_transacciones tb3', 'tb3.tr_codigo = tb1.' . $abrev . '_codigo_transaccion');
        $builder->join('cc_empleados tb4', 'tb4.id = tb1.fk_user_id');
        $builder->join('cc_bodegas tb6', 'tb6.id = tb1.fk_bodega');

        // LEFT JOINs opcionales
        $builder->join('cc_lotes tb5', 'tb5.id = tb1.fk_lote', 'left');
        $builder->join('cc_ajuste_salida ajs', 'ajs.id = tb1.' . $abrev . '_documento_id AND tb3.tr_codigo = "38"', 'left');
        $builder->join('cc_ajuste_entrada aje', 'aje.id = tb1.' . $abrev . '_documento_id AND tb3.tr_codigo = "39"', 'left');
        $builder->join('cc_transferencia_bodega trb', 'trb.id = tb1.' . $abrev . '_documento_id AND tb3.tr_codigo = "17"', 'left');

        //JOINS especiales para obtener el nombre del cliente proveedor

        $caseProv = " CASE WHEN tb3.tr_codigo IN ('39','41') THEN aje.fk_proveedor ELSE NULL END";
        $builder->join('cc_proveedores prov', "prov.id =  $caseProv ", 'left');

        $caseClie = " CASE WHEN tb3.tr_codigo IN ('38','40','42') THEN ajs.fk_cliente ELSE NULL END";
        $builder->join('cc_clientes cli', "cli.id = $caseClie ", 'left');

//        DE AQUI EN ADELANTE TOCA CARGAR LOS JOINS DE VENTAS Y COMPRAS
        // WHERE
        if (!empty($filtros['kardProductoId'])) {
            $builder->where('tb2.id', $filtros['kardProductoId']);
        }
        if (!empty($filtros['kardBodega'])) {
            $builder->where('tb6.id', $filtros['kardBodega']);
        }

        // Verificar si viene el filtro de fechas
        if (!empty($filtros['rangoFechas'])) {
            $rangoFechas = explode(' a ', $filtros['rangoFechas']);
            $fDesde = trim($rangoFechas[0]);
            $fHasta = isset($rangoFechas[1]) ? trim($rangoFechas[1]) : trim($rangoFechas[0]);
            $builder->where(['tb1.' . $abrev . '_fecha <= ' => $fHasta, 'tb1.' . $abrev . '_fecha >= ' => $fDesde]);
        }

        $builder->where('tb1.' . $abrev . '_estado >', 0);

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return [];
        }
    }

    public function getMiniKardexProducto(array $filtros): array {

        $builder = $this->db->table('cc_kardex tb1');

        $builder->select(' tb1.kar_fecha AS fecha,
                            tb1.kar_kardex,
                            tb1.kar_kardex_total AS saldo,
                            tb1.kar_costo_promedio,
                            tb1.kar_costo_ultimo,
                            tb1.fk_bodega,
                            tb3.tr_codigo,
                            tb3.tr_nombre AS movimiento,
                            tb6.bod_nombre,                        
                           ');
        $builder->select('
                            CASE WHEN tb1.kar_kardex > 0 THEN tb1.kar_kardex ELSE 0 END AS entrada,
                            CASE WHEN tb1.kar_kardex < 0 THEN tb1.kar_kardex * -1 ELSE 0 END AS salida
                        ', false);

        // JOINs obligatorios
        $builder->join('cc_productos tb2', 'tb2.id = tb1.fk_producto');
        $builder->join('cc_transacciones tb3', 'tb3.tr_codigo = tb1.kar_codigo_transaccion');
        $builder->join('cc_bodegas tb6', 'tb6.id = tb1.fk_bodega');

        // WHERE
        if (!empty($filtros['productoId'])) {
            $builder->where('tb2.id', $filtros['productoId']);
        }
        if (!empty($filtros['kardBodega'])) {
            $builder->where('tb6.id', $filtros['kardBodega']);
        }

        // Verificar si viene el filtro de fechas
        if (!empty($filtros['fecha'])) {
            $builder->where('tb1.kar_fecha >= ', $filtros['fecha']);
        }

        $builder->where('tb1.kar_estado >', 0);

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return [];
        }
    }
}
