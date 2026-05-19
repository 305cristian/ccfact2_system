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

    public function getReservaProductos(int $bodegaId): array {
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
            return [];
        }
    }

    public function getInventarioGeneral(array $filtros, ?int $start = null, ?int $length = null, ?string $search = null, ?string $orderBy = null, ?string $orderDir = null): array {


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
                            tb1.prod_ctrllote,
                            tb1.prod_imagen,
                            tb8.um_nombre,
                            tb8.um_nombre_corto,
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
        $builder->join("cc_impuesto_tarifa tb9", "tb9.id = tb7.fk_impuestotarifa ");
        $builder->join("cc_unidades_medida tb8", "tb8.id = tb1.fk_unidadmedida", "left");

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
            $builder->where('tb9.fk_impuesto', 1);
            if ($filtros['invIva'] == 2) {
                $builder->where('tb9.impt_report_iva', 2);
            } else {
                $builder->where('tb9.impt_report_iva', 1);
            }
        }
        if (!empty($filtros['invProductoId'])) {
            $builder->where('tb1.id', $filtros['invProductoId']);
        }
        if (!empty($filtros['invSubgrupo'])) {
            $builder->where('tb4.id', $filtros['invSubgrupo']);
        }

        if (!empty($search)) {//Esto funciona cuando usas la opcion de searh en la tabla (solo especifico 3 campos)
            $builder->groupStart();
            $builder->like('tb1.prod_nombre', $search);
            $builder->orLike('tb1.prod_codigo', $search);
            $builder->orLike('tb1.prod_codigobarras', $search);
            $builder->groupEnd();
        }

        if (!empty($orderBy)) {
            $builder->orderBy($orderBy, $orderDir);
        }

        $builder->groupBy("tb1.id");

        if (isset($start) && $start !== null) {
            $builder->limit($length, $start);
        }

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return [];
        }
    }

    public function countFilteredProducts(array $filtros, ?string $search = null): int {
        $builder = $this->db->table('cc_productos tb1');
        $builder->select('tb1.id');

        $builder->join('cc_stock_bodega tb2', 'tb2.fk_producto = tb1.id');
        $builder->join('cc_bodegas tb3', 'tb3.id = tb2.fk_bodega');
        $builder->join('cc_subgrupos tb4', 'tb4.id = tb1.fk_subgrupo', 'left');
        $builder->join('cc_grupos tb5', 'tb5.id = tb4.fk_grupo', 'left');
        $builder->join('cc_marcas tb6', 'tb6.id = tb1.fk_marca', 'left');
        $builder->join('cc_producto_impuestotarifa tb7', 'tb7.fk_producto = tb1.id');
        $builder->join("cc_impuesto_tarifa tb9", "tb9.id = tb7.fk_impuestotarifa ");

        // Filtros
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
            $builder->where('tb9.fk_impuesto', 1);
            if ($filtros['invIva'] == 2) {
                $builder->where('tb9.impt_report_iva', 2);
            } else {
                $builder->where('tb9.impt_report_iva', 1);
            }
        }

        if (!empty($filtros['invProductoId'])) {
            $builder->where('tb1.id', $filtros['invProductoId']);
        }

        if (!empty($filtros['invSubgrupo'])) {
            $builder->where('tb4.id', $filtros['invSubgrupo']);
        }

        // Search
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('tb1.prod_nombre', $search)
                    ->orLike('tb1.prod_codigo', $search)
                    ->orLike('tb1.prod_codigobarras', $search)
                    ->groupEnd();
        }

        $builder->groupBy('tb1.id');

        return $builder->countAllResults();
    }

    public function getStockBodega($productoId) {

        $builder = $this->db->table('cc_stock_bodega tb1');
        $builder->select('MAX(tb1.stb_stock)stb_stock,
                            tb2.bod_nombre,
                            COALESCE(ROUND(SUM( tb3.res_cantidad ),2),0) res_cantidad');
        $builder->join('cc_bodegas tb2', 'tb2.id = tb1.fk_bodega');
        $builder->join('cc_reserva_inventario tb3', 'tb3.fk_producto = tb1.fk_producto AND tb3.res_estado = "ACTIVA" and tb3.fk_bodega = tb1.fk_bodega', 'left');
        $builder->where('tb1.fk_producto ', $productoId);
        $builder->groupBy('tb1.fk_bodega');
        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function getStockBodegaLote($productoId, $fkLote) {

        $builder = $this->db->table('cc_stock_bodega_lote tb1');
        $builder->select('MAX(tb1.stbl_stock)stbl_stock,
                            tb1.fk_lote,
                            tb4.lot_lote,
                            tb2.bod_nombre,
                            COALESCE(ROUND(SUM( tb3.res_cantidad ),2),0) res_cantidad');
        $builder->join('cc_bodegas tb2', 'tb2.id = tb1.fk_bodega');
        $builder->join('cc_reserva_inventario tb3', 'tb3.fk_producto = tb1.fk_producto AND tb3.fk_lote =tb1.fk_lote AND tb3.res_estado = "ACTIVA" and tb3.fk_bodega = tb1.fk_bodega', 'left');
        $builder->join("cc_lotes tb4", "tb4.id = tb1.fk_lote");
        $builder->where(['tb1.fk_producto ' => $productoId, 'tb1.fk_lote' => $fkLote]);
        $builder->groupBy('tb1.fk_bodega');
        $builder->groupBy('tb1.fk_lote');
        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function getReservaLotesProductos(int $bodegaId): array {
        $builder = $this->db->table("cc_reserva_inventario");
        $builder->select("fk_producto, fk_lote, SUM(res_cantidad)res_cantidad ");
        $builder->where('res_estado', "ACTIVA");
        $builder->where('fk_lote IS NOT NULL');

        if (!empty($bodegaId)) {
            $builder->where('fk_bodega', $bodegaId);
        }

        $builder->groupBy('fk_producto');
        $builder->groupBy('fk_lote');

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return [];
        }
    }

    public function getInventarioLotes(array $filtros, ?int $start = null, ?int $length = null, ?string $search = null, ?string $orderBy = null, ?string $orderDir = null): array {


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
                            tb9.um_nombre,
                            tb9.um_nombre_corto,
                            tb2.fk_lote,
                            tb8.lot_lote,
                            tb8.lot_fecha_elaboracion,
                            tb8.lot_fecha_caducidad,
                            tb4.sgr_nombre,
                            tb5.gr_nombre,
                            tb6.mrc_nombre,
                            MAX(tb3.bod_nombre)bod_nombre,
                            MAX(tb3.id)bodegaId,
                            SUM(tb2.stbl_stock) AS stbl_stock");

        $builder->join("cc_stock_bodega_lote tb2 ", "tb2.fk_producto = tb1.id");
        $builder->join("cc_bodegas tb3 ", "tb3.id = tb2.fk_bodega");
        $builder->join("cc_subgrupos tb4 ", "tb4.id = tb1.fk_subgrupo", "left");
        $builder->join("cc_grupos tb5 ", "tb5.id = tb4.fk_grupo", "left");
        $builder->join("cc_marcas tb6 ", "tb6.id = tb1.fk_marca", "left");
        $builder->join("cc_producto_impuestotarifa tb7", "tb7.fk_producto = tb1.id ");
        $builder->join("cc_lotes tb8", "tb8.id = tb2.fk_lote");
        $builder->join("cc_unidades_medida tb9", "tb9.id = tb1.fk_unidadmedida", "left");
        $builder->join("cc_impuesto_tarifa tb10", "tb10.id = tb7.fk_impuestotarifa ");

        // Mapeo de filtros a columnas de BD
        if (!empty($filtros['invBodega'])) {
            $builder->where('tb2.fk_bodega', $filtros['invBodega']);
        }
        if (isset($filtros['invStock'])) {
            if ($filtros['invStock'] === '1') {
                $builder->where('tb2.stbl_stock >', 0);
            } else {
                $builder->where('tb2.stbl_stock <=', 0);
            }
        }
        if (!empty($filtros['invGrupo'])) {
            $builder->where('tb5.id', $filtros['invGrupo']);
        }
        if (!empty($filtros['invIva'])) {
            $builder->where('tb10.fk_impuesto', 1);
            if ($filtros['invIva'] == 2) {
                $builder->where('tb10.impt_report_iva', 2);
            } else {
                $builder->where('tb10.impt_report_iva', 1);
            }
        }
        if (!empty($filtros['invProductoId'])) {
            $builder->where('tb1.id', $filtros['invProductoId']);
        }
        if (!empty($filtros['invSubgrupo'])) {
            $builder->where('tb4.id', $filtros['invSubgrupo']);
        }

        if (!empty($search)) {//Esto funciona cuando usas la opcion de searh en la tabla (solo especifico 3 campos)
            $builder->groupStart();
            $builder->like('tb1.prod_nombre', $search);
            $builder->orLike('tb1.prod_codigo', $search);
            $builder->orLike('tb1.prod_codigobarras', $search);
            $builder->groupEnd();
        }

        if (!empty($orderBy)) {
            $builder->orderBy($orderBy, $orderDir);
        }

        $builder->groupBy("tb1.id, tb2.fk_lote");

        if (isset($start) && $start !== null) {
            $builder->limit($length, $start);
        }

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return [];
        }
    }

    public function countFilteredProductsLotes(array $filtros, ?string $search = null): int {
        $builder = $this->db->table('cc_productos tb1');
        $builder->select('tb1.id');

        $builder->join('cc_stock_bodega_lote tb2', 'tb2.fk_producto = tb1.id');
        $builder->join('cc_bodegas tb3', 'tb3.id = tb2.fk_bodega');
        $builder->join('cc_subgrupos tb4', 'tb4.id = tb1.fk_subgrupo', 'left');
        $builder->join('cc_grupos tb5', 'tb5.id = tb4.fk_grupo', 'left');
        $builder->join('cc_marcas tb6', 'tb6.id = tb1.fk_marca', 'left');
        $builder->join('cc_producto_impuestotarifa tb7', 'tb7.fk_producto = tb1.id');
        $builder->join("cc_lotes tb8", "tb8.id = tb2.fk_lote");
        $builder->join("cc_impuesto_tarifa tb10", "tb10.id = tb7.fk_impuestotarifa ");

        // Filtros
        if (!empty($filtros['invBodega'])) {
            $builder->where('tb2.fk_bodega', $filtros['invBodega']);
        }

        if (isset($filtros['invStock'])) {
            if ($filtros['invStock'] === '1') {
                $builder->where('tb2.stbl_stock >', 0);
            } else {
                $builder->where('tb2.stbl_stock <=', 0);
            }
        }

        if (!empty($filtros['invGrupo'])) {
            $builder->where('tb5.id', $filtros['invGrupo']);
        }

        if (!empty($filtros['invIva'])) {
            $builder->where('tb10.fk_impuesto', 1);
            if ($filtros['invIva'] == 2) {
                $builder->where('tb10.impt_report_iva', 2);
            } else {
                $builder->where('tb10.impt_report_iva', 1);
            }
        }

        if (!empty($filtros['invProductoId'])) {
            $builder->where('tb1.id', $filtros['invProductoId']);
        }

        if (!empty($filtros['invSubgrupo'])) {
            $builder->where('tb4.id', $filtros['invSubgrupo']);
        }

        // Search
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('tb1.prod_nombre', $search)
                    ->orLike('tb1.prod_codigo', $search)
                    ->orLike('tb1.prod_codigobarras', $search)
                    ->groupEnd();
        }

        $builder->groupBy('tb1.id');
        $builder->groupBy('tb2.fk_lote');

        return $builder->countAllResults();
    }

    public function getReservaLote($productoId, $fkLote, $fkBodega) {

        $builder = $this->db->table("cc_reserva_inventario tb1");

        $builder->select("tb3.bod_nombre,
                            tb2.tr_nombre,
                            tb1.res_documento_id,
                            tb1.res_codigo_transaccion,
                            ROUND(tb1.res_cantidad,2)res_cantidad ");
        $builder->join("cc_transacciones tb2", "tb2.tr_codigo = tb1.res_codigo_transaccion");
        $builder->join("cc_bodegas tb3", "tb3.id = tb1.fk_bodega");

        $builder->where(["tb1.fk_producto" => $productoId, "tb1.fk_lote" => $fkLote, "tb1.res_estado" => "ACTIVA"]);

        if ($fkBodega) {
            $builder->where("tb1.fk_bodega", $fkBodega);
        }

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return [];
        }
    }

    public function getReserva($productoId, $fkBodega) {

        $builder = $this->db->table("cc_reserva_inventario tb1");

        $builder->select("tb3.bod_nombre,
                            tb2.tr_nombre,
                            tb1.res_documento_id,
                            tb1.res_codigo_transaccion,
                            ROUND(tb1.res_cantidad,2)res_cantidad ");
        $builder->join("cc_transacciones tb2", "tb2.tr_codigo = tb1.res_codigo_transaccion");
        $builder->join("cc_bodegas tb3", "tb3.id = tb1.fk_bodega");

        $builder->where(["tb1.fk_producto" => $productoId, "tb1.res_estado" => "ACTIVA"]);

        if ($fkBodega) {
            $builder->where("tb1.fk_bodega", $fkBodega);
        }

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return [];
        }
    }

    public function getInventarioConsolidado(array $filtros, ?int $start = null, ?int $length = null, ?string $search = null, ?string $orderBy = null, ?string $orderDir = null): array {
        /*
          ==========================================================
          PRODUCTOS SIN LOTE
          ==========================================================
         */

        $builder1 = $this->db->table('cc_productos tb1');
        $builder1->select("
                            tb1.id,
                            tb1.prod_codigo,
                            tb1.prod_codigobarras,
                            tb1.prod_nombre,
                            tb1.prod_costopromedio,
                            tb1.prod_costoultimo,
                            tb1.prod_existenciaminima,
                            tb1.prod_existenciamaxima,
                            tb1.prod_ivaporcentage,
                            tb1.prod_ctrllote,
                            tb8.um_nombre,
                            tb8.um_nombre_corto,
                            NULL AS fk_lote,
                            NULL AS lot_lote,
                            NULL AS lot_fecha_elaboracion,
                            NULL AS lot_fecha_caducidad,
                            tb4.sgr_nombre,
                            tb5.gr_nombre,
                            tb6.mrc_nombre,
                            MAX(tb3.bod_nombre) AS bod_nombre,
                            MAX(tb3.id) AS bodegaId,
                            SUM(tb2.stb_stock) AS stock
                        ");
        $builder1->join("cc_stock_bodega tb2", "tb2.fk_producto = tb1.id");
        $builder1->join("cc_bodegas tb3", "tb3.id = tb2.fk_bodega");
        $builder1->join("cc_subgrupos tb4", "tb4.id = tb1.fk_subgrupo", "left");
        $builder1->join("cc_grupos tb5", "tb5.id = tb4.fk_grupo", "left");
        $builder1->join("cc_marcas tb6", "tb6.id = tb1.fk_marca", "left");
        $builder1->join("cc_producto_impuestotarifa tb7", "tb7.fk_producto = tb1.id");
        $builder1->join("cc_unidades_medida tb8", "tb8.id = tb1.fk_unidadmedida", "left");
        $builder1->join("cc_impuesto_tarifa tb9", "tb9.id = tb7.fk_impuestotarifa ");

        $builder1->where("tb1.prod_ctrllote", 0);

        // Mapeo de filtros a columnas de BD
        if (!empty($filtros['invBodega'])) {
            $builder1->where('tb2.fk_bodega', $filtros['invBodega']);
        }
        if (isset($filtros['invStock'])) {
            if ($filtros['invStock'] === '1') {
                $builder1->where('tb2.stb_stock >', 0);
            } else {
                $builder1->where('tb2.stb_stock <=', 0);
            }
        }
        if (!empty($filtros['invGrupo'])) {
            $builder1->where('tb5.id', $filtros['invGrupo']);
        }
        if (!empty($filtros['invIva'])) {
            $builder1->where('tb9.fk_impuesto', 1);
            if ($filtros['invIva'] == 2) {
                $builder1->where('tb9.impt_report_iva', 2);
            } else {
                $builder1->where('tb9.impt_report_iva', 1);
            }
        }
        if (!empty($filtros['invProductoId'])) {
            $builder1->where('tb1.id', $filtros['invProductoId']);
        }
        if (!empty($filtros['invSubgrupo'])) {
            $builder1->where('tb4.id', $filtros['invSubgrupo']);
        }

        if (!empty($search)) {//Esto funciona cuando usas la opcion de searh en la tabla (solo especifico 3 campos)
            $builder1->groupStart();
            $builder1->like('tb1.prod_nombre', $search);
            $builder1->orLike('tb1.prod_codigo', $search);
            $builder1->orLike('tb1.prod_codigobarras', $search);
            $builder1->groupEnd();
        }

        $builder1->groupBy("tb1.id");

        /*
          ==========================================================
          PRODUCTOS CON LOTE
          ==========================================================
         */

        $builder2 = $this->db->table('cc_productos tb1');
        $builder2->select("
                    tb1.id,
                    tb1.prod_codigo,
                    tb1.prod_codigobarras,
                    tb1.prod_nombre,
                    tb1.prod_costopromedio,
                    tb1.prod_costoultimo,
                    tb1.prod_existenciaminima,
                    tb1.prod_existenciamaxima,
                    tb1.prod_ivaporcentage,
                    tb1.prod_ctrllote,
                    tb9.um_nombre,
                    tb9.um_nombre_corto,
                    tb2.fk_lote,
                    tb8.lot_lote,
                    tb8.lot_fecha_elaboracion,
                    tb8.lot_fecha_caducidad,
                    tb4.sgr_nombre,
                    tb5.gr_nombre,
                    tb6.mrc_nombre,
                    MAX(tb3.bod_nombre) AS bod_nombre,
                    MAX(tb3.id) AS bodegaId,
                    SUM(tb2.stbl_stock) AS stock
                ");
        $builder2->join("cc_stock_bodega_lote tb2", "tb2.fk_producto = tb1.id");
        $builder2->join("cc_bodegas tb3", "tb3.id = tb2.fk_bodega");
        $builder2->join("cc_subgrupos tb4", "tb4.id = tb1.fk_subgrupo", "left");
        $builder2->join("cc_grupos tb5", "tb5.id = tb4.fk_grupo", "left");
        $builder2->join("cc_marcas tb6", "tb6.id = tb1.fk_marca", "left");
        $builder2->join("cc_producto_impuestotarifa tb7", "tb7.fk_producto = tb1.id");
        $builder2->join("cc_lotes tb8", "tb8.id = tb2.fk_lote");
        $builder2->join("cc_unidades_medida tb9", "tb9.id = tb1.fk_unidadmedida", "left");
        $builder2->join("cc_impuesto_tarifa tb10", "tb9.id = tb7.fk_impuestotarifa ");

        $builder2->where("tb1.prod_ctrllote", 1);

        if (!empty($filtros['invBodega'])) {
            $builder2->where('tb2.fk_bodega', $filtros['invBodega']);
        }
        if (isset($filtros['invStock'])) {
            if ($filtros['invStock'] === '1') {
                $builder2->where('tb2.stbl_stock >', 0);
            } else {
                $builder2->where('tb2.stbl_stock <=', 0);
            }
        }
        if (!empty($filtros['invGrupo'])) {
            $builder2->where('tb5.id', $filtros['invGrupo']);
        }
        if (!empty($filtros['invIva'])) {
            $builder2->where('tb10.fk_impuesto', 1);
            if ($filtros['invIva'] == 2) {
                $builder2->where('tb10.impt_report_iva', 2);
            } else {
                $builder2->where('tb10.impt_report_iva', 1);
            }
        }
        if (!empty($filtros['invProductoId'])) {
            $builder2->where('tb1.id', $filtros['invProductoId']);
        }
        if (!empty($filtros['invSubgrupo'])) {
            $builder2->where('tb4.id', $filtros['invSubgrupo']);
        }

        if (!empty($search)) {//Esto funciona cuando usas la opcion de searh en la tabla (solo especifico 3 campos)
            $builder2->groupStart();
            $builder2->like('tb1.prod_nombre', $search);
            $builder2->orLike('tb1.prod_codigo', $search);
            $builder2->orLike('tb1.prod_codigobarras', $search);
            $builder2->groupEnd();
        }
        $builder2->groupBy("tb1.id, tb2.fk_lote");

        /*
          UNION SE LAS 2 CONSULTAS
         */
        $sql1 = $builder1->getCompiledSelect();
        $sql2 = $builder2->getCompiledSelect();

        $unionSql = "($sql1) UNION ALL ($sql2)";

        $orderByq = $orderBy ?: 'prod_nombre';
        $orderDirq = strtolower($orderDir) === 'desc' ? 'DESC' : 'ASC';

        $unionSql .= " ORDER BY {$orderByq} {$orderDirq}";

        if ($start !== null) {
            $unionSql .= " LIMIT " . (int) $length . " OFFSET " . (int) $start;
        }

        $query = $this->db->query($unionSql);

        return $query->getResult();
    }

    public function countProductosAllConsolidado(array $filtros): int {
        $sql = $this->buildCountInventarioConsolidadoSQL($filtros);

        $row = $this->db->query($sql)->getRow();

        return (int) ($row->total ?? 0);
    }

    public function countFilteredProductsConsolidado(array $filtros, ?string $search): int {

        $sql = $this->buildCountInventarioConsolidadoSQL($filtros, $search !== '' ? $search : null);

        $row = $this->db->query($sql)->getRow();

        return (int) ($row->total ?? 0);
    }

    public function getReservasConsolidado(int $bodegaId): array {
        $builder = $this->db->table("cc_reserva_inventario");
        $builder->select("fk_producto, fk_lote, SUM(res_cantidad)res_cantidad ");
        $builder->where('res_estado', "ACTIVA");

        if (!empty($bodegaId)) {
            $builder->where('fk_bodega', $bodegaId);
        }

        $builder->groupBy('fk_producto');
        $builder->groupBy('fk_lote');

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return [];
        }
    }

    private function buildCountInventarioConsolidadoSQL(array $filtros, ?string $search = null): string {
        /*
          ==========================================================
          SIN LOTE (1 fila por producto)
          ==========================================================
         */
        $b1 = $this->db->table('cc_productos tb1');
        $b1->select("tb1.id AS row_id");

        $b1->join("cc_stock_bodega tb2", "tb2.fk_producto = tb1.id");
        $b1->join("cc_bodegas tb3", "tb3.id = tb2.fk_bodega");
        $b1->join("cc_subgrupos tb4", "tb4.id = tb1.fk_subgrupo", "left");
        $b1->join("cc_grupos tb5", "tb5.id = tb4.fk_grupo", "left");
        $b1->join("cc_marcas tb6", "tb6.id = tb1.fk_marca", "left");
        $b1->join("cc_producto_impuestotarifa tb7", "tb7.fk_producto = tb1.id");
        $b1->join("cc_impuesto_tarifa tb9", "tb9.id = tb7.fk_impuestotarifa ");

        $b1->where("tb1.prod_ctrllote", 0);

        // Filtros
        if (!empty($filtros['invBodega'])) {
            $b1->where('tb2.fk_bodega', $filtros['invBodega']);
        }

        if (isset($filtros['invStock'])) {
            $filtros['invStock'] === '1' ? $b1->where('tb2.stb_stock >', 0) : $b1->where('tb2.stb_stock <=', 0);
        }

        if (!empty($filtros['invGrupo'])) {
            $b1->where('tb5.id', $filtros['invGrupo']);
        }

        if (!empty($filtros['invIva'])) {
            $b1->where('tb9.fk_impuesto', 1);
            if ($filtros['invIva'] == 2) {
                $b1->where('tb9.impt_report_iva', 2);
            } else {
                $b1->where('tb9.impt_report_iva', 1);
            }
        }

        if (!empty($filtros['invProductoId'])) {
            $b1->where('tb1.id', $filtros['invProductoId']);
        }

        if (!empty($filtros['invSubgrupo'])) {
            $b1->where('tb4.id', $filtros['invSubgrupo']);
        }

        // Búsqueda
        if (!empty($search)) {
            $b1->groupStart()
                    ->like('tb1.prod_nombre', $search)
                    ->orLike('tb1.prod_codigo', $search)
                    ->orLike('tb1.prod_codigobarras', $search)
                    ->groupEnd();
        }

        $b1->groupBy("tb1.id");

        /*
          ==========================================================
          CON LOTE (1 fila por producto+lote)
          ==========================================================
         */
        $b2 = $this->db->table('cc_productos tb1');

        // row_id único por producto+lote para que cuente filas reales
        $b2->select("CONCAT(tb1.id,'|',tb2.fk_lote) AS row_id");

        $b2->join("cc_stock_bodega_lote tb2", "tb2.fk_producto = tb1.id");
        $b2->join("cc_bodegas tb3", "tb3.id = tb2.fk_bodega");
        $b2->join("cc_subgrupos tb4", "tb4.id = tb1.fk_subgrupo", "left");
        $b2->join("cc_grupos tb5", "tb5.id = tb4.fk_grupo", "left");
        $b2->join("cc_marcas tb6", "tb6.id = tb1.fk_marca", "left");
        $b2->join("cc_producto_impuestotarifa tb7", "tb7.fk_producto = tb1.id");
        $b2->join("cc_lotes tb8", "tb8.id = tb2.fk_lote");
        $b2->join("cc_impuesto_tarifa tb9", "tb9.id = tb7.fk_impuestotarifa ");

        $b2->where("tb1.prod_ctrllote", 1);

        // Filtros
        if (!empty($filtros['invBodega'])) {
            $b2->where('tb2.fk_bodega', $filtros['invBodega']);
        }

        if (isset($filtros['invStock'])) {
            $filtros['invStock'] === '1' ? $b2->where('tb2.stbl_stock >', 0) : $b2->where('tb2.stbl_stock <=', 0);
        }

        if (!empty($filtros['invGrupo'])) {
            $b2->where('tb5.id', $filtros['invGrupo']);
        }

        if (!empty($filtros['invIva'])) {
            $b2->where('tb9.fk_impuesto', 1);
            if ($filtros['invIva'] == 2) {
                $b2->where('tb9.impt_report_iva', 2);
            } else {
                $b2->where('tb9.impt_report_iva', 1);
            }
        }

        if (!empty($filtros['invProductoId'])) {
            $b2->where('tb1.id', $filtros['invProductoId']);
        }

        if (!empty($filtros['invSubgrupo'])) {
            $b2->where('tb4.id', $filtros['invSubgrupo']);
        }

        // Búsqueda
        if (!empty($search)) {
            $b2->groupStart()
                    ->like('tb1.prod_nombre', $search)
                    ->orLike('tb1.prod_codigo', $search)
                    ->orLike('tb1.prod_codigobarras', $search)
                    ->groupEnd();
        }

        $b2->groupBy("tb1.id, tb2.fk_lote");

        /*
          ==========================================================
          UNION + COUNT
          ==========================================================
         */
        $sql1 = $b1->getCompiledSelect();
        $sql2 = $b2->getCompiledSelect();

        return "SELECT COUNT(*) AS total FROM ( ($sql1) UNION ALL ($sql2) ) X";
    }

    public function getDataProducto($productoId): array {
        $builder = $this->db->table('cc_productos tb1');
        $builder->select('tb1.*,'
                . ' tb2.mrc_nombre,'
                . ' tb3.um_nombre,'
                . ' tb3.um_nombre_corto,'
                . ' tb4.sgr_nombre,'
                . ' tb5.id id_grupo,'
                . ' tb5.gr_nombre,'
                . ' tb6.tp_nombre,'
                . ' tb7.fk_impuestotarifa idImpuesto,'
                . ' tb8.fk_impuestotarifa idImpuestoIce,'
                . ' tb9.pp_valor');
        $builder->join("cc_marcas tb2", "tb2.id = tb1.fk_marca", "left");
        $builder->join("cc_unidades_medida tb3", "tb3.id = tb1.fk_unidadmedida");
        $builder->join("cc_subgrupos tb4", "tb4.id = tb1.fk_subgrupo");
        $builder->join("cc_grupos tb5", "tb5.id = tb4.fk_grupo");
        $builder->join("cc_tipo_producto tb6", "tb6.id = tb1.fk_tipoproducto");
        $builder->join("cc_producto_impuestotarifa tb7", "tb7.fk_producto = tb1.id AND tb7.fk_impuesto = 1");
        $builder->join("cc_producto_impuestotarifa tb8", "tb8.fk_producto = tb1.id AND tb8.fk_impuesto = 2", "left");
        $builder->join("cc_producto_precios tb9", "tb9.fk_producto = tb1.id AND tb9.fk_tipo_precio = 1");

        $builder->where("tb1.id", $productoId);

        $response = $builder->get();

        return $response->getNumRows() > 0 ? $response->getRowArray() : [];
    }
}
