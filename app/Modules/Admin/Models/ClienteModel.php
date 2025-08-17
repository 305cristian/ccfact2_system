<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Models;

/**
 * Description of ClienteModel
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 15 ago 2025
 * @time 3:12:18 p.m.
 */
class ClienteModel extends \CodeIgniter\Model {

    //put your code here

    public function getClientes($ciruc) {
        $builder = $this->db->table('cc_clientes tb1');
        $builder->select('tb1.*,
                            tb2.doc_nombre,
                            tb3.prr_nombre,
                            tb4.id id_canton,
                            tb4.ctn_nombre,
                            tb5.id id_provincia,
                            tb5.prv_nombre');

        $builder->join('cc_tipo_documento tb2', 'tb1.fk_tipo_documento = tb2.id', 'left');
        $builder->join('cc_parroquia tb3', 'tb1.fk_parroquia = tb3.id', 'left');
        $builder->join('cc_canton tb4', 'tb3.fk_canton = tb4.id', 'left');
        $builder->join('cc_provincia tb5', 'tb4.fk_provincia = tb5.id', 'left');

        if (!empty($ciruc)) {
            $builder->where('tb1.clie_dni', $ciruc);
        }

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function searchClientes($dataSearch) {
        $builder = $this->db->table('cc_clientes tb1');
        $builder->select('tb1.*,
                            tb2.doc_nombre,
                            tb3.id id_parroquia,
                            tb3.prr_nombre,
                            tb4.id id_canton,
                            tb4.ctn_nombre,
                            tb5.id id_provincia,
                            tb5.prv_nombre');

        $builder->join('cc_tipo_documento tb2', 'tb1.fk_tipo_documento = tb2.id', 'left');
        $builder->join('cc_parroquia tb3', 'tb1.fk_parroquia = tb3.id', 'left');
        $builder->join('cc_canton tb4', 'tb3.fk_canton = tb4.id', 'left');
        $builder->join('cc_provincia tb5', 'tb4.fk_provincia = tb5.id', 'left');

        $builder->like('tb1.clie_razon_social', $dataSearch);

        $builder->limit(10);

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }
}
