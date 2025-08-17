<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Cc_model
 * @author Cristian R. Paz
 * @Date 1 feb. 2023
 * @Time 13:10:28
 */

namespace App\Models;

class CcModel extends \CodeIgniter\Model {

    public function guardar($form_data, $table_name) {
        $builder = $this->db->table($table_name);
        $builder->insert($form_data);
        if ($this->db->affectedRows() > 0) {
            return $this->db->insertID();
        } else {
            $error = $this->db->error();
            throw new \Exception('Error al insertar en la tabla ' . $table_name . ': ' . $error['message']);
        }
    }

    function eliminar($table_name, $where = null) {
        $builder = $this->db->table($table_name);
        if ($where) {
            foreach ($where as $col => $val) {
                $builder->where($col, $val);
            }
        } else {
            $builder->where('id >', '0');
        }

        $builder->delete($where);
        return $this->db->affectedRows();
    }

    function actualizar($table_name, $data_set, $where_data) {
        $builder = $this->db->table($table_name);
        $builder->update($data_set, $where_data);
//        var_dump($builder);
        if ($this->db->affectedRows() > 0) {
            return $this->db->affectedRows();
        } else {
            $error = $this->db->error();
            if ($error['message']) {
                throw new \Exception('Error al actualizar en la tabla ' . $table_name . ': ' . $error['message']);
            } else {
                return $this->db->affectedRows();
            }
        }
    }

    function getData($table_name, $where_data = null, $fields = '', $order_by = null, $rows_num = 0, $group_by = null) {

        $builder = $this->db->table($table_name);

        if ($where_data) {
            foreach ($where_data as $key => $value) {
                $builder->where($key, $value);
            }
        }

        if (!empty($group_by)) {
            $builder->groupBy($group_by);
        }

        if (!empty($fields)) {
            $builder->select($fields, FALSE);
        } else {
            $builder->select('*');
        }

        if ($order_by) {
            foreach ($order_by as $order => $tipo) {
                $builder->orderBy($order, $tipo);
            }
        }
//        var_dump($builder);

        $query = $builder->get();

        if ($rows_num == 1) {
            $builder->limit($rows_num);
            return $query->getRow();
        } elseif ($rows_num > 1) {
            $builder->limit($rows_num);
            return $query->getResult();
        } elseif ($rows_num == 0) {
            return $query->getResult();
        }
    }

    function get($table_name, $where_data = null, $or_where=null, $fields = '', $order_by = null, $rows_num = 0, $group_by = null) {

        $builder = $this->db->table($table_name);

        if ($where_data) {
            foreach ($where_data as $key => $value) {
                $builder->where($key, $value);
            }
        }

        if ($or_where) {
            foreach ($or_where as $key => $value) {
                $builder->orWhere($key, $value);
            }
        }

        if (!empty($group_by)) {
            $builder->groupBy($group_by);
        }

        if (!empty($fields)) {
            $builder->select($fields, FALSE);
        } else {
            $builder->select('*');
        }

        if ($order_by) {
            foreach ($order_by as $order => $tipo) {
                $builder->orderBy($order, $tipo);
            }
        }
//        var_dump($builder);

        $query = $builder->get();

        if ($rows_num == 1) {
            $builder->limit($rows_num);
            return $query->getRow();
        } elseif ($rows_num > 1) {
            $builder->limit($rows_num);
            return $query->getResult();
        } elseif ($rows_num == 0) {
            return $query->getResult();
        }
    }

    function getJoin($table_name, $where_data, $join_cluase, $fields = '', $rows_num = 0, $order_by = null, $group_by = null) {
        $builder = $this->db->table($table_name);

        if ($where_data) {
            foreach ($where_data as $key => $value) {
                $builder->where($key, $value);
            }
        }

        if (!empty($group_by)) {
            $builder->groupBy($group_by);
        }

        if (!empty($fields)) {
            $builder->select($fields, FALSE);
        } else {
            $builder->select('*');
        }

        foreach ($join_cluase as $join) {
            if (!empty($join['type'])) {
                $builder->join($join['table'], $join['condition'], $join['type']);
            } else {
                $builder->join($join['table'], $join['condition']);
            }
        }

        if ($order_by) {
            foreach ($order_by as $order => $tipo) {
                $builder->orderBy($order, $tipo);
            }
        }

        if ($rows_num == 1) {

            $builder->limit($rows_num);
            $query = $builder->get();
            return $query->getRow();
        } else {
            $query = $builder->get();
            return $query->getResult();
        }
    }

    function getValue($table_name, $id, $val, $id_column_name = 'id', $alias_v = null, $empty_v = -1) {

        $builder = $this->db->table($table_name);

        $builder->where($id_column_name, $id);
        $builder->select($val, FALSE);

        $query = $builder->get();

        if ($query->getRow()) {
            if ($alias_v != null) {
                return $query->getRow()->{$alias_v};
            } else {
                return $query->getRow()->$val;
            }
        } else {
            return $empty_v;
        }
    }
}
