<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Inventarios\Models;

/**
 * Description of HistoricoModel
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 7 mar 2026
 * @time 4:41:55 p.m.
 */
class HistoricoModel extends \CodeIgniter\Model {

    //put your code here

    public function getInventarioHistorico(array $filtros, ?int $start = null, ?int $length = null, ?string $search = null, ?string $orderBy = null, ?string $orderDir = null): array {

        $tabla = $filtros['invBodega'] ? 'cc_kardex_bodega tb1' : 'cc_kardex tb1';
        $nomenclatura = $filtros['invBodega'] ? 'karb' : 'kar';

        $builder = $this->db->table($tabla);

        $builder->select(" tb1.{$nomenclatura}_kardex_total AS kardexStock,        
                            tb1.{$nomenclatura}_costo_promedio AS costoPromedio,
                            ROUND( tb1.{$nomenclatura}_costo_promedio * tb1.{$nomenclatura}_kardex_total, 2 ) total_cst_promedio,
                            tb1.{$nomenclatura}_costo_ultimo AS costoUltimo,
                            ROUND( tb1.{$nomenclatura}_costo_ultimo * tb1.{$nomenclatura}_kardex_total, 2 ) total_cst_ultimo, ");

        //SECCION DE JOINS
        $this->joinBuilderQuery($builder); //RESTO DE LOS JOINS Y SELECTS
        $builder->join("cc_transacciones tb10", "tb10.tr_codigo = tb1.{$nomenclatura}_codigo_transaccion");
        $filtros['invBodega'] ?
                        $builder->join("( SELECT MAX( id ) AS id FROM cc_kardex_bodega WHERE karb_fecha <= '{$filtros['fechaCorte']}' AND fk_bodega = {$filtros['invBodega']} AND karb_estado = 1 GROUP BY fk_producto) tblkardex ", "tblkardex.id = tb1.id ") :
                        $builder->join("( SELECT MAX( id ) AS id FROM cc_kardex WHERE kar_fecha <= '{$filtros['fechaCorte']}' AND kar_estado = 1 GROUP BY fk_producto) tblkardex ", "tblkardex.id = tb1.id ");
        //CIERRO SECCION JOINS
        // Mapeo de filtros a columnas de BD
        if (isset($filtros['kardStock'])) {
            if ($filtros['kardStock'] === '1') {
                $builder->where("tb1.{$nomenclatura}_kardex_total >", 0);
            } else {
                $builder->where("tb1.{$nomenclatura}_kardex_total <=", 0);
            }
        }
        if (!empty($filtros['invGrupo'])) {
            $builder->where('tb6.id', $filtros['invGrupo']);
        }
        if (!empty($filtros['invIva'])) {
            $builder->where('tb11.fk_impuestotarifa', $filtros['invIva']);
        }

        if (!empty($filtros['invSubgrupo'])) {
            $builder->where('tb5.id', $filtros['invSubgrupo']);
        }

        $builder->where("tb1.{$nomenclatura}_estado", 1);

        if (!empty($search)) {//Esto funciona cuando usas la opcion de searh en la tabla (solo especifico 3 campos)
            $builder->groupStart();
            $builder->like('tb2.prod_nombre', $search);
            $builder->orLike('tb2.prod_codigo', $search);
            $builder->orLike('tb2.prod_codigobarras', $search);
            $builder->groupEnd();
        }

        $builder->groupBy("tb1.id");

        if (!empty($orderBy)) {
            $builder->orderBy($orderBy, $orderDir);
        }

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

        $tabla = $filtros['invBodega'] ? 'cc_kardex_bodega tb1' : 'cc_kardex tb1';
        $nomenclatura = $filtros['invBodega'] ? 'karb' : 'kar';

        $builder = $this->db->table($tabla);

        //SECCION DE JOINS
        $this->joinBuilderQuery($builder); //RESTO DE LOS JOINS Y SELECTS

        $builder->join("cc_transacciones tb10", "tb10.tr_codigo = tb1.{$nomenclatura}_codigo_transaccion");
        $filtros['invBodega'] ?
                        $builder->join("( SELECT MAX( id ) AS id FROM cc_kardex_bodega WHERE karb_fecha <= '{$filtros['fechaCorte']}' AND fk_bodega = {$filtros['invBodega']} AND karb_estado = 1 GROUP BY fk_producto) tblkardex ", "tblkardex.id = tb1.id ") :
                        $builder->join("( SELECT MAX( id ) AS id FROM cc_kardex WHERE kar_fecha <= '{$filtros['fechaCorte']}' AND kar_estado = 1 GROUP BY fk_producto) tblkardex ", "tblkardex.id = tb1.id ");
        //CIERRO SECCION JOINS
        // Mapeo de filtros a columnas de BD
        if (isset($filtros['kardStock'])) {
            if ($filtros['kardStock'] === '1') {
                $builder->where("tb1.{$nomenclatura}_kardex_total >", 0);
            } else {
                $builder->where("tb1.{$nomenclatura}_kardex_total <=", 0);
            }
        }
        if (!empty($filtros['invGrupo'])) {
            $builder->where('tb6.id', $filtros['invGrupo']);
        }
        if (!empty($filtros['invIva'])) {
            $builder->where('tb11.fk_impuestotarifa', $filtros['invIva']);
        }

        if (!empty($filtros['invSubgrupo'])) {
            $builder->where('tb5.id', $filtros['invSubgrupo']);
        }

        $builder->where("tb1.{$nomenclatura}_estado", 1);

        if (!empty($search)) {//Esto funciona cuando usas la opcion de searh en la tabla (solo especifico 3 campos)
            $builder->groupStart();
            $builder->like('tb2.prod_nombre', $search);
            $builder->orLike('tb2.prod_codigo', $search);
            $builder->orLike('tb2.prod_codigobarras', $search);
            $builder->groupEnd();
        }
        $builder->groupBy("tb1.id");

        return $builder->countAllResults();
    }

    public function joinBuilderQuery($builder) {

        $builder->select("tb1.id AS id_kardex, tb2.prod_codigo,
                            tb2.id AS id_producto,
                            tb2.prod_nombre,
                            tb8.um_nombre_corto,
                            tb4.bod_nombre,
                            tb6.gr_nombre,
                            tb5.sgr_nombre,
                            tb7.mrc_nombre,
                            tb2.prod_ivaporcentage,
                            tb9.ctad_codigo,
                            tb10.tr_nombre");

        $builder->join("cc_productos tb2", "tb2.id = tb1.fk_producto");
        $builder->join("cc_bodegas tb4", "tb4.id = tb1.fk_bodega");
        $builder->join("cc_subgrupos tb5", "tb5.id = tb2.fk_subgrupo", "left");
        $builder->join("cc_grupos tb6", "tb6.id = tb5.fk_grupo", "left");
        $builder->join("cc_marcas tb7", "tb7.id = tb2.fk_marca", "left");
        $builder->join("cc_unidades_medida tb8", "tb8.id = tb2.fk_unidadmedida", "left");
        $builder->join("cc_cuenta_contabledet tb9", "tb9.ctad_codigo = tb2.fk_cuentacontablecompras", "left");
        $builder->join("cc_producto_impuestotarifa tb11", "tb11.fk_producto = tb1.fk_producto", "left");
    }
}
