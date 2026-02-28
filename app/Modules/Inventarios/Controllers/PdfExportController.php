<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Inventarios\Controllers;

/**
 * Description of PdfExportController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 27 ene 2026
 * @time 9:14:57 p.m.
 */
use Modules\Comun\Libraries\PdfExportLib;
use Modules\Inventarios\Models\InventarioModel;

class PdfExportController extends \App\Controllers\BaseController {

    protected $invModel;
    protected $pdfExport;
    protected $dirViewModule;

    public function __construct() {
        $this->pdfExport = new PdfExportLib();
        $this->invModel = new InventarioModel();
        $this->dirViewModule = 'Modules\Inventarios\Views';
    }

    public function exportInventarioGeneralPdf() {
        $dataPost = json_decode(file_get_contents('php://input'));
        $searchValue = (string) ($dataPost->search ?? '');
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
        $reservasProducto = [];

        foreach ($reservas ?? [] as $val) {
            $reservasProducto[$val->fk_producto] = $val->res_cantidad;
        }

        $dataInv = $this->invModel->getInventarioGeneral($filtros, null, null, $searchValue, $orderBy, $orderDir);
        foreach ($dataInv as $val) {
            $val->reservaProducto = $reservasProducto[$val->id] ?? 0;
        }
        $data['data'] = $dataInv;
        $data['bodgaSelect'] = $dataPost->invBodega;

        $html = view($this->dirViewModule . '\Existencias\general\viewPdfReport', $data);

        $this->pdfExport->export([
            'title' => 'REPORTE DE INVENTARIO GENERAL',
            'html' => $html,
            'filename' => 'Inventario_General_' . date('Ymd_His') . '.pdf',
            'orientation' => 'L' // Landscape (opcional)
        ]);
    }

    public function exportInventarioLotesPdf() {
        $dataPost = json_decode(file_get_contents('php://input'));
        $searchValue = (string) ($dataPost->search ?? '');
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
        $reservasProducto = [];

        foreach ($reservas ?? [] as $val) {
            $reservasProducto[$val->fk_producto . '|' . $val->fk_lote] = $val->res_cantidad;
        }

        $dataInv = $this->invModel->getInventarioLotes($filtros, null, null, $searchValue, $orderBy, $orderDir);
        foreach ($dataInv as $val) {
            $val->reservaProducto = $reservasProducto[$val->id . '|' . $val->fk_lote] ?? 0;
        }
        $data['data'] = $dataInv;
        $data['bodgaSelect'] = $dataPost->invBodega;

        $html = view($this->dirViewModule . '\Existencias\lotes\viewPdfReport', $data);

        $this->pdfExport->export([
            'title' => 'REPORTE DE INVENTARIO POR LOTES',
            'html' => $html,
            'filename' => 'Inventario_General_' . date('Ymd_His') . '.pdf',
            'orientation' => 'L' // Landscape (opcional)
        ]);
    }
}
