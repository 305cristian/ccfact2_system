<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Comun\Models;

/**
 * Description of LotesStockModel
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 28 nov 2025
 * @time 10:02:11 a.m.
 */
class LotesStockModel extends \CodeIgniter\Model {

    //put your code here

    public function getLotesStock($idProducto, $idBodega) {
        $builder = $this->db->table('cc_stock_bodega_lote tb1');

        $builder->select("tb2.lot_lote AS lote,
                            tb2.lot_fecha_elaboracion AS fechaElaboracion,
                            tb2.lot_fecha_caducidad AS fechaCaducidad,
                            tb1.stbl_stock,
                            tb1.fk_lote ");
        $builder->join("cc_lotes tb2", "tb2.id = tb1.fk_lote");
        $builder->where(["tb1.fk_producto" => $idProducto, "tb1.fk_bodega" => $idBodega, "tb1.stbl_stock >" => 0]);
        
        $builder->orderBy(" tb2.lot_fecha_caducidad","asc");

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }
}
