<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Models;

/**
 * Description of ProveedorModel
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 17 ago 2025
 * @time 10:40:46 a.m.
 */
class ProveedorModel extends \CodeIgniter\Model {

    //put your code here

    public function getProveedores($ciruc) {
        $builder = $this->db->table('cc_proveedores tb1');
        $builder->select('tb1.*,
                            CONCAT(tb7.ctad_codigo," ",tb7.ctad_nombre_cuenta) cta_contable,
                            tb8.sec_nombre,
                            tb9.an_nombre,
                            tb2.doc_nombre,
                            tb3.prr_nombre,
                            tb4.id id_canton,
                            tb4.ctn_nombre,
                            tb5.id id_provincia,
                            tb5.prv_nombre,
                            tb6.tps_descripcion');

        $builder->join('cc_tipo_documento tb2', 'tb1.fk_tipo_documento = tb2.id', 'left');
        $builder->join('cc_parroquia tb3', 'tb1.fk_parroquia = tb3.id', 'left');
        $builder->join('cc_canton tb4', 'tb3.fk_canton = tb4.id', 'left');
        $builder->join('cc_provincia tb5', 'tb4.fk_provincia = tb5.id', 'left');
        $builder->join('cc_tipo_sujetos tb6', 'tb1.fk_tipo_sujeto = tb6.id', 'left');
        $builder->join('cc_cuenta_contabledet tb7', 'tb1.fk_codigo_cuenta_contable = tb7.ctad_codigo', 'left');
        $builder->join('cc_sectores tb8', 'tb1.fk_sector = tb8.id', 'left');
        $builder->join('cc_anillo tb9', 'tb8.fk_anillo = tb9.id', 'left');

        if (!empty($ciruc)) {
            $builder->where('tb1.prov_ruc', $ciruc);
        }

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function searchProveedores($dataSearch) {
        $builder = $this->db->table('cc_proveedores tb1');
        $builder->select('tb1.prov_nombres, tb1.prov_apellidos, tb1.prov_razon_social, tb1.prov_ruc');
        $builder->like('tb1.prov_razon_social', $dataSearch);

        $builder->limit(10);

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function getBancos($dataSearch) {
        $builder = $this->db->table('cc_bancos_list tb1');
        $builder->select('tb1.banc_nombre, tb1.id');
        $builder->like('tb1.banc_nombre', $dataSearch);

        $builder->limit(10);

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function getRetenciones($dataSearch) {
        $builder = $this->db->table('cc_retencion_sri tb1');
        $builder->select('tb1.ret_codigo, tb1.ret_nombre, CONCAT(tb1.ret_codigo," ",tb1.ret_nombre)retencion , tb1.id');
        $builder->groupStart();
        $builder->like('tb1.ret_codigo', $dataSearch);
        $builder->orLike('tb1.ret_nombre', $dataSearch);
        $builder->groupEnd();

        $builder->limit(10);

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function getDataCuentas($idProveedor) {
        $builder = $this->db->table('cc_proveedor_banco tb1');
        $builder->select('tb2.banc_nombre, tb2.id, tb1.numero_cuenta, tb3.id tipo_cuenta');
        $builder->join('cc_bancos_list tb2', 'tb2.id = tb1.fk_banco');
        $builder->join('cc_banco_tipo_cuenta tb3', 'tb3.id = tb1.fk_tipo_cuenta');
        $builder->where('tb1.fk_proveedor', $idProveedor);

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }

    public function getDataRetenciones($idProveedor) {
        $builder = $this->db->table('cc_proveedor_retencion tb1');
        $builder->select('tb2.ret_codigo, tb2.ret_nombre, CONCAT(tb2.ret_codigo," ",tb2.ret_nombre)retencion , tb2.id');
        $builder->join('cc_retencion_sri tb2', 'tb2.id = tb1.fk_retencion');
        $builder->where('tb1.fk_proveedor', $idProveedor);

        $response = $builder->get();

        if ($response->getNumRows() > 0) {
            return $response->getResult();
        } else {
            return false;
        }
    }
}
