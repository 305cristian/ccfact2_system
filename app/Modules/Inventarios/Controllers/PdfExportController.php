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

use App\Controllers\BaseController;
use Modules\Comun\Libraries\PdfExportLib;
use Modules\Inventarios\Models\CaducidadModel;
use Modules\Inventarios\Models\InventarioModel;
use function view;

class PdfExportController extends BaseController {

    protected $invModel;
    protected $caducModel;
    protected $pdfExport;
    protected $dirViewModule;

    public function __construct() {

        //MODELOS
        $this->invModel = new InventarioModel();
        $this->caducModel = new CaducidadModel();

        //LIBRERIAS
        $this->pdfExport = new PdfExportLib();

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
            'filename' => 'Inventario_Por_Lotes_' . date('Ymd_His') . '.pdf',
            'orientation' => 'L' // Landscape (opcional)
        ]);
    }

    public function exportInventarioConsolidadoPdf() {
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
            'invSubgrupo' => $dataPost->invSubgrupo ?? null
        ];

        $reservas = $this->invModel->getReservasConsolidado((int) $dataPost->invBodega);
        $reservasProducto = [];

        foreach ($reservas ?? [] as $val) {
            $lote = ($val->fk_lote !== null) ? $val->fk_lote : "";
            $reservasProducto[$val->fk_producto . '|' . $lote] = (float) $val->res_cantidad;
        }

        $dataInv = $this->invModel->getInventarioConsolidado($filtros, null, null, $searchValue, $orderBy, $orderDir);
        foreach ($dataInv as $val) {
            $lote = ($val->fk_lote !== null) ? $val->fk_lote : "";
            $val->reservaProducto = $reservasProducto[$val->id . '|' . $lote] ?? 0;
        }
        $data['data'] = $dataInv;
        $data['bodgaSelect'] = $dataPost->invBodega;

        $html = view($this->dirViewModule . '\Existencias\consolidado\viewPdfReport', $data);

        $this->pdfExport->export([
            'title' => 'REPORTE DE INVENTARIO CONSOLIDADO',
            'html' => $html,
            'filename' => 'Inventario_Consolidado_' . date('Ymd_His') . '.pdf',
            'orientation' => 'L' // Landscape (opcional)
        ]);
    }

    public function exportPdfCaducidad() {
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
            'caducFechas' => $dataPost->caducFechas ?? null,
            'invSubgrupo' => $dataPost->invSubgrupo ?? null
        ];

        $reservas = $this->caducModel->getReservaLotesProductos((int) $dataPost->invBodega);
        $reservasProducto = [];

        foreach ($reservas ?? [] as $val) {
            $reservasProducto[$val->fk_producto . '|' . $val->fk_lote] = $val->res_cantidad;
        }

        $dataInv = $this->caducModel->getProductosCaducidad($filtros, null, null, $searchValue, $orderBy, $orderDir);
        foreach ($dataInv as $val) {
            $val->reservaProducto = $reservasProducto[$val->id . '|' . $val->fk_lote] ?? 0;
            $val->stockDisponible = $val->stbl_stock - $val->reservaProducto;
        }
        $data['data'] = $dataInv;
        $data['bodgaSelect'] = $dataPost->invBodega;

        $html = view($this->dirViewModule . '\Existencias\caducidad\viewPdfReport', $data);

        $this->pdfExport->export([
            'title' => 'REPORTE DE CONTROL DE CADUCIDAD',
            'html' => $html,
            'filename' => 'Inventario_Control_Caducidad_' . date('Ymd_His') . '.pdf',
            'orientation' => 'L' // Landscape (opcional)
        ]);
    }
}
