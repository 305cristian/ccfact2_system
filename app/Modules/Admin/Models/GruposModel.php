<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Models;

/**
 * Description of GruposModel
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 11 jun 2024
 * @time 11:52:17 p.m.
 */
class GruposModel extends \CodeIgniter\Model {

    public function getSubgrupos() {
        $builder = $this->db->table('cc_subgrupos tb1');
        $builder->select('tb1.*, tb2.gr_nombre');
        $builder->join('cc_grupos tb2', 'tb2.id = tb1.fk_grupo');
        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }
}
