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
                                -- WHEN tb3.tr_codigo IN ("01","01") THEN ven.ven_secuencial
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

        $builder->where('tb2.id', $filtros['kardProductoId']);

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

    public function getKardexGeneral(array $filtros, string $movimiento, ?int $start = null, ?int $length = null, ?string $search = null, ?string $orderBy = null, ?string $orderDir = null): array {

        $builder = $this->buildBaseQuery($filtros, $movimiento);

        // =========================
        // TOTAL SIN FILTROS DE SEARCH
        // =========================
        $builderTotal = clone $builder;
        $total = $builderTotal->countAllResults();

        // =========================
        // SEARCH (OPCIONAL)
        // =========================
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('p.prod_nombre', $search)
                    ->orLike('p.prod_codigo', $search)
                    ->orLike('mot.mot_nombre', $search)
                    ->groupEnd();
        }

        // =========================
        // TOTAL FILTRADO 
        // =========================
        $builderFiltered = clone $builder;
        $filtered = $builderFiltered->countAllResults();

        // =========================
        // ORDEN
        // =========================
        if (!empty($orderBy)) {
            $builder->orderBy($orderBy, $orderDir);
        }


        // =========================
        // PAGINACIÓN
        // =========================
        if ($start !== null && $length !== null) {
            $builder->limit($length, $start);
        }


        $data = $builder->get()->getResult();

        return [
            'data' => $data,
            'total' => $total,
            'filtered' => $filtered
        ];
    }

    private function buildBaseQuery($filtros, $movimiento) {

        $builder = $this->db->table('cc_kardex k');
        // =========================
        // SELECT BASE
        // =========================
        $builder->select("
            k.kar_fecha AS fecha_movimiento,
            p.prod_codigo,
            p.prod_nombre,
            g.gr_nombre,
            sg.sgr_nombre,
            b.bod_nombre,
            l.lot_lote,
            l.lot_fecha_caducidad,
            ABS(k.kar_kardex) AS cantidad,
            k.kar_costo_promedio,
            (ABS(k.kar_kardex) * ROUND(k.kar_costo_promedio, 2)) AS total_promedio,
            k.kar_costo_ultimo,
            (ABS(k.kar_kardex) * ROUND(k.kar_costo_ultimo, 2)) AS total_ultimo,
            k.kar_codigo_transaccion,
            k.kar_documento_id,
            tr.tr_nombre AS transaccion,
            CONCAT(emp.emp_nombre,' ', emp.emp_apellido) userId
        ");

        // =========================
        // JOINS BASE
        // =========================
        $builder->join('cc_productos p', 'p.id = k.fk_producto');

        $builder->join('cc_subgrupos sg', 'sg.id = p.fk_subgrupo', 'left');

        $builder->join('cc_grupos g', 'g.id = sg.fk_grupo', 'left');

        $builder->join('cc_bodegas b', 'b.id = k.fk_bodega');

        $builder->join('cc_lotes l', 'l.id = k.fk_lote', 'left');

        $builder->join('cc_empleados emp', 'emp.id = k.fk_user_id');

        $builder->join('cc_transacciones tr', 'tr.id = k.kar_codigo_transaccion');

        // =========================
        // FILTROS
        // =========================

        if ($filtros['productoId']) {
            $builder->where('k.fk_producto', $filtros['productoId']);
        }

        if ($filtros['bodegaId']) {
            $builder->where('k.fk_bodega', $filtros['bodegaId']);
        }

        if ($filtros['grupoId']) {
            $builder->where('g.id', $filtros['grupoId']);
        }

        // Verificar si viene el filtro de fechas

        if (!empty($filtros['rangoFechasKardex'])) {
            $rangoFechas = explode('a', $filtros['rangoFechasKardex']);
            $fDesde = trim($rangoFechas[0]);
            $fHasta = isset($rangoFechas[1]) ? trim($rangoFechas[1]) : trim($rangoFechas[0]);
            $builder->where(['k.kar_fecha <= ' => $fHasta, 'k.kar_fecha >= ' => $fDesde]);
        }

        if (!empty($filtros['rangoFechasEmision'])) {
            $rangoFechasEmi = explode('a', $filtros['rangoFechasEmision']);
            $fDesdeEmi = trim($rangoFechasEmi[0]);
            $fHastaEmi = isset($rangoFechasEmi[1]) ? trim($rangoFechasEmi[1]) : trim($rangoFechasEmi[0]);
        }

        // =========================
        // MOVIMIENTOS
        // =========================
        switch ($movimiento) {

            case 'COMPRAS':
                $builder->whereIn('k.kar_codigo_transaccion', ['02']);

                $builder->select("c.comp_numero_factura AS documento, CONCAT(prov.nombres,' ',prov.apellidos) AS proveedor_cliente, c.comp_fecha_emision AS fecha_emision");

                $builder->join('cc_compras c', 'c.id = k.kar_documento_id');
                $builder->join('cc_proveedores prov', 'prov.id = c.cmp_proveedor_id');

                if (!empty($fDesdeEmi) && !empty($fHastaEmi)) {
                    $builder->where(['c.comp_fecha_emision <= ' => $fHastaEmi, 'c.comp_fecha_emision >= ' => $fDesdeEmi]);
                }

                $builder->where('c.cmp_estado', 2); //SOLO ARCHIVADAS

                break;

            case 'VENTAS':
                $builder->whereIn('k.kar_codigo_transaccion', ['01']);

                $builder->select("v.ven_secuencial AS documento, CONCAT(cli.nombres, ' ', cli.apellidos) AS proveedor_cliente, v.ven_fecha_emision AS fecha_emision");

                $builder->join('cc_ventas v', 'v.id = k.kar_documento_id');
                $builder->join('cc_clientes cli', 'cli.id = v.ven_cliente_id');

                if (!empty($fDesdeEmi) && !empty($fHastaEmi)) {
                    $builder->where(['v.ven_fecha_emision <= ' => $fHastaEmi, 'v.ven_fecha_emision >= ' => $fDesdeEmi]);
                }

                $builder->where('v.ven_estado', 2); //SOLO ARCHIVADAS

                break;

            case 'AJUSTES_DE_ENTRADA':
                $builder->whereIn('k.kar_codigo_transaccion', ['39']);

                $builder->select("aje.ajen_secuencial AS documento ,mot.mot_nombre AS motivo, mot.id motivoId, aje.ajen_fecha AS fecha_emision");

                $builder->join('cc_ajuste_entrada aje', 'aje.id = k.kar_documento_id');
                $builder->join('cc_motivos_ajuste mot', 'mot.id = aje.fk_motivo_ajuste');

                if (!empty($fDesdeEmi) && !empty($fHastaEmi)) {
                    $builder->where(['aje.ajen_fecha <= ' => $fHastaEmi, 'aje.ajen_fecha >= ' => $fDesdeEmi]);
                }

                $builder->where('aje.ajen_estado', 2); //SOLO ARCHIVADAS
                break;

            case 'AJUSTES_DE_SALIDA':
                $builder->whereIn('k.kar_codigo_transaccion', ['38']);

                $builder->select("ajs.ajes_secuencial AS documento ,mot.mot_nombre AS motivo, mot.id motivoId, ajs.ajes_fecha AS fecha_emision");

                $builder->join('cc_ajuste_salida ajs', 'ajs.id = k.kar_documento_id');
                $builder->join('cc_motivos_ajuste mot', 'mot.id = ajs.fk_motivo_ajuste');

                if (!empty($fDesdeEmi) && !empty($fHastaEmi)) {
                    $builder->where(['ajs.ajes_fecha <= ' => $fHastaEmi, 'ajs.ajes_fecha >= ' => $fDesdeEmi]);
                }
                $builder->where('ajs.ajes_estado', 2); //SOLO ARCHIVADAS
                break;

            case 'TRANSFERENCIAS':
                $builder->whereIn('k.kar_codigo_transaccion', ['17']);

                $builder->select("t.trb_secuencial AS documento, t.trb_fecha AS fecha_emision");

                $builder->join('cc_transferencia_bodega t', 't.id = k.kar_documento_id');

                if ($filtros['tipoTransferencia'] === 'ENTRADA') {
                    $builder->where('k.kar_kardex >', 0);
                }

                if ($filtros['tipoTransferencia'] === 'SALIDA') {
                    $builder->where('k.kar_kardex <', 0);
                }

                if (!empty($fDesdeEmi) && !empty($fHastaEmi)) {
                    $builder->where(['t.trb_fecha <= ' => $fHastaEmi, 't.trb_fecha >= ' => $fDesdeEmi]);
                }

                $builder->where('t.trb_estado', 3); //SOLO CONFIRMADAS

                break;
        }
        $builder->orderBy('k.kar_fecha', 'DESC');

        return $builder;
    }

    public function getKardexLotes(array $filtros): array {

        $abrev = $filtros['kardBodega'] ? 'karbl' : 'karbl';

        $builder = $this->db->table('cc_kardex_bodega_lote tb1');

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
                                WHEN tb3.tr_codigo IN ("38","40","42") THEN ajs.ajes_secuencial
                                WHEN tb3.tr_codigo IN ("17","44") THEN trb.trb_secuencial
                                WHEN tb3.tr_codigo IN ("39","41") THEN aje.ajen_secuencial
                                -- WHEN tb3.tr_codigo IN ("01","01") THEN ven.ven_secuencial
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
        //DE AQUI EN ADELANTE TOCA CARGAR LOS JOINS DE VENTAS Y COMPRAS
        
        // WHERE
        $builder->where('tb2.id', $filtros['kardProductoId']);
        $builder->where('tb1.fk_lote', $filtros['kardLoteId']);

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
}
