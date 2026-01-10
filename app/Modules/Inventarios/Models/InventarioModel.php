<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Inventarios\Models;

/**
 * Description of InventarioModel
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 5 ene 2026
 * @time 12:23:57 p.m.
 */
class InventarioModel extends \CodeIgniter\Model {

    //put your code here

    public function getReservaProductos(int $bodegaId) {
        $builder = $this->db->table("cc_reserva_inventario");
        $builder->select("fk_producto, SUM(res_cantidad)res_cantidad ");
        $builder->where('res_estado', "ACTIVA");

        if (!empty($bodegaId)) {
            $builder->where('fk_bodega', $bodegaId);
        }

        $builder->groupBy('fk_producto');

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function getInventarioGeneral($filtros) {


        $builder = $this->db->table('cc_productos tb1');
        $builder->select("tb1.id,
                            tb1.prod_codigo,
                            tb1.prod_codigobarras,
                            tb1.prod_nombre,
                            tb1.prod_costopromedio,
                            tb1.prod_costoultimo,
                            tb1.prod_existenciaminima,
                            tb1.prod_existenciamaxima,
                            tb1.prod_ivaporcentage,
                            tb4.sgr_nombre,
                            tb5.gr_nombre,
                            tb6.mrc_nombre,
                            MAX(tb3.bod_nombre)bod_nombre,
                            MAX(tb3.id)bodegaId,
                            SUM(tb2.stb_stock) AS stb_stock");

        $builder->join("cc_stock_bodega tb2 ", "tb2.fk_producto = tb1.id");
        $builder->join("cc_bodegas tb3 ", "tb3.id = tb2.fk_bodega");
        $builder->join("cc_subgrupos tb4 ", "tb4.id = tb1.fk_subgrupo", "left");
        $builder->join("cc_grupos tb5 ", "tb5.id = tb4.fk_grupo", "left");
        $builder->join("cc_marcas tb6 ", "tb6.id = tb1.fk_marca", "left");
        $builder->join("cc_producto_impuestotarifa tb7", "tb7.fk_producto = tb1.id ");

        // Mapeo de filtros a columnas de BD
        if (!empty($filtros['invBodega'])) {
            $builder->where('tb2.fk_bodega', $filtros['invBodega']);
        }
        if (isset($filtros['invStock'])) {
            if ($filtros['invStock'] === '1') {
                $builder->where('tb2.stb_stock >', 0);
            } else {
                $builder->where('tb2.stb_stock <=', 0);
            }
        }
        if (!empty($filtros['invGrupo'])) {
            $builder->where('tb5.id', $filtros['invGrupo']);
        }
        if (!empty($filtros['invIva'])) {
            $builder->where('tb7.fk_impuestotarifa', $filtros['invIva']);
        }
        if (!empty($filtros['invProductoId'])) {
            $builder->where('tb1.id', $filtros['invProductoId']);
        }
        if (!empty($filtros['invSubgrupo'])) {
            $builder->where('tb4.id', $filtros['invSubgrupo']);
        }

        $builder->orderBy('tb1.prod_nombre', 'ASC');

        $builder->groupBy("tb1.id");

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function viewStockBodega($productoId) {

        $builder = $this->db->table('cc_stock_bodega tb1');
        $builder->select('MAX(tb1.stb_stock)stb_stock,
                            tb2.bod_nombre,
                            COALESCE(ROUND(SUM( tb3.res_cantidad ),2),0) res_cantidad');
        $builder->join('cc_bodegas tb2', 'tb2.id = tb1.fk_bodega');
        $builder->join('cc_reserva_inventario tb3', 'tb3.fk_producto = tb1.fk_producto AND tb3.res_estado = "ACTIVA" and tb3.fk_bodega = tb1.fk_bodega','left');
        $builder->where('tb1.fk_producto ', $productoId);
        $builder->groupBy('tb1.fk_bodega');
        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }
}
