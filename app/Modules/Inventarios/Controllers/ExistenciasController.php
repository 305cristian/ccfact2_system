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

        $draw = $dataPost->draw;
        $start = (int) $dataPost->start;
        $length = (int) $dataPost->length;
        $searchValue = (string) $dataPost->search ?? '';

        $orderData = $dataPost->order ?? [];
        $orderBy = ($orderData[0]->column ?? '') ?: 'prod_nombre';
        $orderDir = ($orderData[0]->dir ?? '') ?: 'asc';

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

        foreach ($reservas ?? [] as $val) {
            $reservasProducto[$val->fk_producto] = $val->res_cantidad;
        }


        $data = $this->invModel->getInventarioGeneral($filtros, $start, $length, $searchValue, $orderBy, $orderDir);
        $countProductosAll = $this->invModel->countFilteredProducts($filtros);
        $countFilteredProducts = $this->invModel->countFilteredProducts($filtros, $searchValue);

        foreach ($data as $val) {
            $val->reservaProducto = $reservasProducto[$val->id] ?? 0;
        }

        return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $data,
                    'draw' => intval($draw),
                    'recordsTotal' => $countProductosAll,
                    'recordsFiltered' => $countFilteredProducts,
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
        $stockBodega = $this->invModel->getStockBodega($productoId);
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

    public function viewReserva() {

        $dataPost = json_decode(file_get_contents('php://input'));

        $productoId = $dataPost->id ?? null;
        $fkBodega = $dataPost->bodega ?? null;

        $producto = $this->ccm->getData('cc_productos', ['id' => $productoId], 'prod_nombre, prod_codigo', null, 1);

        $output = '<div class="container-fluid"><table class="table table-fixed table-condensed">';
        $output .= '<thead class="bg-warning">
            <tr>
                <th colspan="5" ><font color="white">' . $producto->prod_codigo . ' | ' . $producto->prod_nombre . '</th>
            </tr>
            <tr class="text-left">
                <th ><font color="white">Bodega</th>
                <th ><font color="white">Documento</th>
                <th ><font color="white"># Doc.</th>
                <th ><font color="white">Cantidad</th>
            </tr>
        </thead>
        <tbody>';
        $reservaLote = $this->invModel->getReserva($productoId, $fkBodega);
        foreach ($reservaLote as $val) {
            $output .= '<tr  class="text-left">';
            $output .= '<td>' . $val->bod_nombre . '</td>';
            $output .= '<td>' . $val->tr_nombre . '</td>';
            $output .= '<td>' . $this->getNumDocumento($val->res_documento_id, $val->res_codigo_transaccion) . '</td>';
            $output .= '<td>' . $val->res_cantidad . '</td>';
            $output .= '</tr>';
        }
        $output .= '</tbody></table></div>';
        echo $output;
    }

    public function getInventarioLotes() {
        $dataPost = json_decode(file_get_contents('php://input'));

        $draw = $dataPost->draw;
        $start = (int) $dataPost->start;
        $length = (int) $dataPost->length;
        $searchValue = (string) $dataPost->search ?? '';

        $orderData = $dataPost->order ?? [];
        $orderBy = ($orderData[0]->column ?? '') ?: 'prod_nombre';
        $orderDir = ($orderData[0]->dir ?? '') ?: 'asc';

        $filtros = [
            'invBodega' => $dataPost->invBodega ?? null,
            'invStock' => $dataPost->invStock !== '-1' ? $dataPost->invStock : null,
            'invGrupo' => $dataPost->invGrupo ?? null,
            'invIva' => $dataPost->invIva !== '-1' ? $dataPost->invIva : null,
            'invProductoId' => $dataPost->invProductoId ?? null,
            'invSubgrupo' => $dataPost->invSubgrupo ?? null
        ];

        $reservas = $this->invModel->getReservaLotesProductos((int) $dataPost->invBodega);

        //Indexamos reservas
        $reservasProducto = [];

        foreach ($reservas ?? [] as $val) {
            $reservasProducto[$val->fk_producto . '|' . $val->fk_lote] = $val->res_cantidad;
        }


        $data = $this->invModel->getInventarioLotes($filtros, $start, $length, $searchValue, $orderBy, $orderDir);
        $countProductosAllLotes = $this->invModel->countFilteredProductsLotes($filtros);
        $countFilteredProductsLotes = $this->invModel->countFilteredProductsLotes($filtros, $searchValue);
//
        foreach ($data as $val) {
            $val->reservaProducto = $reservasProducto[$val->id . '|' . $val->fk_lote] ?? 0;
        }

        return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $data,
                    'draw' => intval($draw),
                    'recordsTotal' => $countProductosAllLotes,
                    'recordsFiltered' => $countFilteredProductsLotes,
        ]);
    }

    public function viewStockBodegaLote($productoId, $fkLote) {

        $producto = $this->ccm->getData('cc_productos', ['id' => $productoId], 'prod_nombre, prod_codigo', null, 1);

        $output = '<div class="container-fluid"><table class="table table-fixed table-condensed">';
        $output .= '<thead class="bg-system">
            <tr>
                <th colspan="5" ><font color="white">' . $producto->prod_codigo . ' | ' . $producto->prod_nombre . '</th>
            </tr>
            <tr class="text-left">
                <th ><font color="white">Bodega</th>
                <th ><font color="white">Stock</th>
                <th ><font color="white">Lote</th>
                <th ><font color="white">Reserva</th>
                <th ><font color="white">Disponible</th>
            </tr>
        </thead>
        <tbody>';
        $stockBodegaLote = $this->invModel->getStockBodegaLote($productoId, $fkLote);
        foreach ($stockBodegaLote as $val) {
            $output .= '<tr  class="text-left">';
            $output .= '<td>' . $val->bod_nombre . '</td>';
            $output .= '<td>' . $val->stbl_stock . '</td>';
            $output .= '<td>' . $val->lot_lote . '</td>';
            $output .= '<td>' . $val->res_cantidad . '</td>';
            $output .= '<td>' . $val->stbl_stock - $val->res_cantidad . '</td>';
            $output .= '</tr>';
        }
        $output .= '</tbody></table></div>';
        echo $output;
    }

    public function viewReservaLote() {

        $dataPost = json_decode(file_get_contents('php://input'));

        $productoId = $dataPost->id ?? null;
        $fkLote = $dataPost->lote ?? null;
        $fkBodega = $dataPost->bodega ?? null;

        $producto = $this->ccm->getData('cc_productos', ['id' => $productoId], 'prod_nombre, prod_codigo', null, 1);

        $output = '<div class="container-fluid"><table class="table table-fixed table-condensed">';
        $output .= '<thead class="bg-warning">
            <tr>
                <th colspan="5" ><font color="white">' . $producto->prod_codigo . ' | ' . $producto->prod_nombre . '</th>
            </tr>
            <tr class="text-left">
                <th ><font color="white">Bodega</th>
                <th ><font color="white">Documento</th>
                <th ><font color="white"># Doc.</th>
                <th ><font color="white">Cantidad</th>
            </tr>
        </thead>
        <tbody>';
        $reservaLote = $this->invModel->getReservaLote($productoId, $fkLote, $fkBodega);
        foreach ($reservaLote as $val) {
            $output .= '<tr  class="text-left">';
            $output .= '<td>' . $val->bod_nombre . '</td>';
            $output .= '<td>' . $val->tr_nombre . '</td>';
            $output .= '<td>' . $this->getNumDocumento($val->res_documento_id, $val->res_codigo_transaccion) . '</td>';
            $output .= '<td>' . $val->res_cantidad . '</td>';
            $output .= '</tr>';
        }
        $output .= '</tbody></table></div>';
        echo $output;
    }

    public function getNumDocumento($documentoId, $codigoTransaccion) {

        switch ($codigoTransaccion) {
            case "01": // VENTAS
                $sec = $this->ccm->getValueWhere("cc_ventas", ["id" => $documentoId], "ven_secuencial");
                break;
            case "38": // AJUSTES DE SALIDA
                $sec = $this->ccm->getValueWhere("cc_ajuste_salida", ["id" => $documentoId], "ajes_secuencial");
                break;
            case "17": // TRANSFERENCIAS
                $sec = $this->ccm->getValueWhere("cc_transferencia_bodega", ["id" => $documentoId], "trb_secuencial");
                break;
            default:
                $sec = $documentoId;
        }

        return str_pad($sec, 5, "0", STR_PAD_LEFT);
    }

    public function getInventarioConsolidado() {
        $dataPost = json_decode(file_get_contents('php://input'));

        $draw = $dataPost->draw;
        $start = (int) $dataPost->start;
        $length = (int) $dataPost->length;
        $searchValue = (string) $dataPost->search ?? '';

        $orderData = $dataPost->order ?? [];
        $orderBy = ($orderData[0]->column ?? '') ?: 'prod_nombre';
        $orderDir = ($orderData[0]->dir ?? '') ?: 'asc';

        $filtros = [
            'invBodega' => $dataPost->invBodega ?? null,
            'invStock' => $dataPost->invStock !== '-1' ? $dataPost->invStock : null,
            'invGrupo' => $dataPost->invGrupo ?? null,
            'invIva' => $dataPost->invIva !== '-1' ? $dataPost->invIva : null,
            'invSubgrupo' => $dataPost->invSubgrupo ?? null
        ];

        $reservas = $this->invModel->getReservasConsolidado((int) $dataPost->invBodega);

        //Indexamos reservas
        $reservasProducto = [];

        foreach ($reservas ?? [] as $val) {
            $lote = ($val->fk_lote !== null) ? $val->fk_lote : "";
            $reservasProducto[$val->fk_producto . '|' . $lote] = (float) $val->res_cantidad;
        }


        $data = $this->invModel->getInventarioConsolidado($filtros, $start, $length, $searchValue, $orderBy, $orderDir);
        $countProductosAllLotes = $this->invModel->countProductosAllConsolidado($filtros);
        $countFilteredProductsLotes = $this->invModel->countFilteredProductsConsolidado($filtros, $searchValue);
//
        foreach ($data as $val) {
            $lote = ($val->fk_lote !== null) ? $val->fk_lote : "";
            $val->reservaProducto = $reservasProducto[$val->id . '|' . $lote] ?? 0;
        }

        return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $data,
                    'draw' => intval($draw),
                    'recordsTotal' => $countProductosAllLotes,
                    'recordsFiltered' => $countFilteredProductsLotes,
        ]);
    }

    public function getDataProducto($productoId) {

        $respuesta = $this->invModel->getDataProducto($productoId);

        return $this->response->setJSON(['data' => $respuesta,]);
    }
}
