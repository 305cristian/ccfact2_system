<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Models;

/**
 * Description of ProductosModel
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 12 jun 2024
 * @time 2:19:52 p.m.
 */
class ProductosModel extends \CodeIgniter\Model {

    public function getProductos($whereQuery) {
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

        if ($whereQuery) {
            foreach ($whereQuery as $key => $val) {
                $builder->where($key, $val);
            }
        }

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function searchProductos($params) {

        $unwantedArray = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
            'ñ' => 'n', 'Ñ' => 'N',
        ];
        $newStringData = strtr($params->dataSerach, $unwantedArray);

        $builder = $this->db->table('cc_productos tb1');
        $builder->select("tb1.prod_nombre prodNombre, tb1.id prodCode, tb1.prod_codigo prodCode2, CONCAT(tb1.id,'/',tb1.prod_codigo)codigos");

        if ($params->val == "name") {
            $builder->like('LOWER(tb1.prod_nombre)', strtolower($newStringData));
        } else {
            $builder->where("tb1.id",`'`.$params->dataSerach.`'`);
            $builder->orWhere("tb1.prod_codigo",`'`.$params->dataSerach.`'`);
        }
        $builder->limit(15);

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }
}
