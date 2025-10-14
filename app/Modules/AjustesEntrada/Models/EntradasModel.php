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
        $builder->join('cc_unidades_medida tb2','tb2.id = tb1.fk_unidadmedida');
        if (ctype_digit($codProd)) {
            $builder->where('tb1.id', $codProd);
        } else {
            $builder->where("CAST(tb1.id AS CHAR) =", $codProd);
        } $builder->orWhere("tb1.prod_codigo", `'` . $codProd . `'`);
        $builder->orWhere("tb1.prod_codigobarras", `'` . $codProd . `'`);
        $builder->orWhere("tb1.prod_codigobarras2", `'` . $codProd . `'`);
        $builder->orWhere("tb1.prod_codigobarras3", `'` . $codProd . `'`);

        $builder->limit(1);

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getRow();
        } else {
            return false;
        }
    }
}
