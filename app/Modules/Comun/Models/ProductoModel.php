<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Comun\Models;

/**
 * Description of ProductoModel
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 10 oct 2025
 * @time 7:45:43 p.m.
 */
class ProductoModel extends \CodeIgniter\Model {

    /**
     * Función para obtener las tarifas de impuestos asociadas a un producto específico
     * @param int $prodId El identificador único del producto
     * @return array|false Un array con las tarifas de impuestos si se encuentran, o
     * false si no se encuentran tarifas para el producto
    */
    function getImpuestoTarifa(int $prodId):array|false {

        $builder = $this->db->table('cc_producto_impuestotarifa tb1');
        $builder->select('tb2.id, tb2.impt_porcentage, tb2.impt_codigo, tb2.impt_detalle, tb1.fk_impuesto');
        $builder->join('cc_impuesto_tarifa tb2', 'tb2.id = tb1.fk_impuestotarifa');
        $builder->where('tb1.fk_producto', $prodId);
        $builder->whereIn('tb1.fk_impuesto', [1, 2]); //1=>ID DE IMPIESTO IVA, 2=>ID DE IMPIESTO ICE
        $builder->orderBy('tb1.fk_impuesto', 'acs');

        $respuesta = $builder->get();

        if ($respuesta->getNumRows() > 0) {
            return $respuesta->getResult();
        } else {
            return false;
        }
    }
}
