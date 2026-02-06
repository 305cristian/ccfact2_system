<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Inventarios\Controllers;

/**
 * Description of ExcelExportController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 27 ene 2026
 * @time 3:23:15 p.m.
 */
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Modules\Inventarios\Models\InventarioModel;
use Modules\Comun\Libraries\ExcelExportLib;

class ExcelExportController extends \App\Controllers\BaseController {

    protected $invModel;
    protected $xlsxExport;

    public function __construct() {
        $this->invModel = new InventarioModel();
        $this->xlsxExport = new ExcelExportLib();
    }

    public function exportInventarioGeneralExcel() {

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
        //Indexamos reservas
        $reservasProducto = [];

        foreach ($reservas ?? [] as $val) {
            $reservasProducto[$val->fk_producto] = $val->res_cantidad;
        }

        $data = $this->invModel->getInventarioGeneral($filtros, null, null, $searchValue, $orderBy, $orderDir);
        $rows = [];
        foreach ($data as $item) {
            $reserva = $reservasProducto[$item->id] ?? 0;

            $rows[] = [
                $item->prod_codigo,
                $item->prod_codigobarras,
                $item->prod_nombre,
                $item->stb_stock,
                $reserva,
                $item->stb_stock - $reserva,
                $item->prod_ivaporcentage === '0.00' ? 'SIN IVA' : 'IVA',
                $item->prod_ctrllote === '1' ? 'SI' : 'NO',
                $item->bod_nombre,
                $item->prod_costopromedio,
                $item->prod_costoultimo,
                $item->gr_nombre,
                $item->sgr_nombre
            ];
        }


        $this->xlsxExport->export([
            'title' => 'REPORTE DE INVENTARIO GENERAL',
            'headers' => [
                'CÓDIGO', 'BARCODE', 'PRODUCTO', 'STOCK',
                'RESERVA', 'STOCK DISPONIBLE', 'IVA', 'CTRL LOTE',
                'BODEGA', 'COSTO PROMEDIO', 'COSTO ÚLTIMO',
                'GRUPO', 'SUBGRUPO'
            ],
            'data' => $rows,
            'filename' => 'Inventario_General_' . date('Ymd_His') . '.xlsx',
            'lastColumn' => 'M'
        ]);
    }

    public function exportInventarioLotesExcel() {
        
    }
}
