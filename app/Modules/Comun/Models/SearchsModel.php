<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of SearchsModel
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 9 oct 2025
 * @time 12:58:19 p.m.
 */

namespace Modules\Comun\Models;

class SearchsModel extends \CodeIgniter\Model {

    //put your code here

    public function searchProveedores($dataSearch) {
        $builder = $this->db->table('cc_proveedores tb1');
        $builder->select('tb1.id, tb1.prov_nombres, tb1.prov_apellidos, tb1.prov_razon_social, tb1.prov_ruc, CONCAT(tb1.prov_ruc," : ",tb1.prov_razon_social)proveedor ');
        $builder->like('tb1.prov_razon_social', $dataSearch);
        $builder->orLike('tb1.prov_ruc', $dataSearch);

        $builder->limit(10);

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
        $builder->select("tb1.prod_nombre, tb1.id, tb1.prod_codigo, CONCAT(tb1.id,' / ',tb1.prod_codigo)codigos");

        $builder->like('LOWER(tb1.prod_nombre)', strtolower($newStringData));

        $builder->limit(15);

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function searchProductoCode($codProd) {
        $builder = $this->db->table('cc_productos tb1');
        $builder->select("tb1.prod_nombre, tb1.id, tb1.prod_codigo, CONCAT(tb1.id,' / ',tb1.prod_codigo)codigos");
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
