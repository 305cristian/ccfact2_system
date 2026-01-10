<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Inventarios\Controllers;

/**
 * Description of ExistenciasController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 4 ene 2026
 * @time 1:23:31 a.m.
 */
use Modules\Inventarios\Models\InventarioModel;

class ExistenciasController extends \App\Controllers\BaseController {

    //put your code here
    protected $invModel;

    public function __construct() {
        $this->invModel = new InventarioModel();
    }

    public function getInventarioGeneral() {

        $dataPost = json_decode(file_get_contents('php://input'));

        $filtros = [
            'invBodega' => $dataPost->invBodega ?? null,
            'invStock' => $dataPost->invStock !== '-1' ? $dataPost->invStock : null,
            'invGrupo' => $dataPost->invGrupo ?? null,
            'invIva' => $dataPost->invIva !== '-1' ? $dataPost->invIva : null,
            'invProductoId' => $dataPost->invProductoId ?? null,
            'invSubgrupo' => $dataPost->invSubgrupo ?? null
        ];
        $reservas = $this->invModel->getReservaProductos((int) $dataPost->invBodega);

        //Indexamos reservas
        $reservasProducto = [];
        if ($reservas) {
            foreach ($reservas as $val) {
                $reservasProducto[$val->fk_producto] = $val->res_cantidad;
            }
        }

        $data = $this->invModel->getInventarioGeneral($filtros);

        foreach ($data as $val) {
            $val->reservaProducto = $reservasProducto[$val->id] ?? 0;
        }

        return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $data ? $data : []
        ]);
    }

    public function viewStockBodega($productoId) {
        $producto = $this->ccm->getData('cc_productos', ['id' => $productoId], 'prod_nombre, prod_codigo', null, 1);

        $output = '<div class="container-fluid"><table class="table table-fixed table-condensed">';
        $output .= '<thead class="bg-system">
            <tr>
                <th colspan="4" ><font color="white">' . $producto->prod_codigo . ' | ' . $producto->prod_nombre . '</th>
            </tr>
            <tr class="text-left">
                <th ><font color="white">Bodega</th>
                <th ><font color="white">Stock</th>
                <th ><font color="white">Reserva</th>
                <th ><font color="white">Disponible</th>
            </tr>
        </thead>
        <tbody>';
        $stockBodega = $this->invModel->viewStockBodega($productoId);
        foreach ($stockBodega as $val) {
            $output .= '<tr  class="text-left">';
            $output .= '<td>' . $val->bod_nombre . '</td>';
            $output .= '<td>' . $val->stb_stock . '</td>';
            $output .= '<td>' . $val->res_cantidad . '</td>';
            $output .= '<td>' . $val->stb_stock - $val->res_cantidad . '</td>';
            $output .= '</tr>';
        }
        $output .= '</tbody></table></div>';
        echo $output;
    }
}
