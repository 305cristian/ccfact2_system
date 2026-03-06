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
use App\Controllers\BaseController;
use Modules\Comun\Libraries\ExcelExportLib;
use Modules\Inventarios\Models\CaducidadModel;
use Modules\Inventarios\Models\InventarioModel;

class ExcelExportController extends BaseController {

    protected $invModel;
    protected $caducModel;
    protected $xlsxExport;

    public function __construct() {
        //MODELOS
        $this->invModel = new InventarioModel();
        $this->caducModel = new CaducidadModel();
        
        //LIBRERIAS
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
                $item->um_nombre_corto,
                $item->stb_stock,
                $reserva,
                $item->stb_stock - $reserva,
                $item->prod_ivaporcentage === '0.00' ? 'SIN IVA' : 'IVA',
                $item->prod_ctrllote === '1' ? 'SI' : 'NO',
                $dataPost->invBodega ? $item->bod_nombre : "ALL SELECT",
                $item->prod_costopromedio,
                $item->prod_costoultimo,
                $item->gr_nombre,
                $item->sgr_nombre
            ];
        }


        $this->xlsxExport->export([
            'title' => 'REPORTE DE INVENTARIO GENERAL',
            'headers' => [
                'CÓDIGO', 'BARCODE', 'PRODUCTO', 'PRES.', 'STOCK',
                'RESERVA', 'STOCK DISPONIBLE', 'IVA', 'CTRL LOTE',
                'BODEGA', 'COSTO PROMEDIO', 'COSTO ÚLTIMO',
                'GRUPO', 'SUBGRUPO'
            ],
            'data' => $rows,
            'filename' => 'Inventario_General_' . date('Ymd_His') . '.xlsx',
            'lastColumn' => 'N'
        ]);
    }

    public function exportInventarioLotesExcel() {

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
        //Indexamos reservas
        $reservasProducto = [];

        foreach ($reservas ?? [] as $val) {
            $reservasProducto[$val->fk_producto . '|' . $val->fk_lote] = $val->res_cantidad;
        }

        $data = $this->invModel->getInventarioLotes($filtros, null, null, $searchValue, $orderBy, $orderDir);
        $rows = [];
        foreach ($data as $item) {
            $reserva = $reservasProducto[$item->id . '|' . $item->fk_lote] ?? 0;

            $rows[] = [
                $item->prod_codigo,
                $item->prod_codigobarras,
                $item->prod_nombre,
                $item->um_nombre_corto,
                $item->lot_lote,
                $item->lot_fecha_caducidad,
                $item->stbl_stock,
                $reserva,
                $item->stbl_stock - $reserva,
                $item->prod_ivaporcentage === '0.00' ? 'SIN IVA' : 'IVA',
                $dataPost->invBodega ? $item->bod_nombre : "ALL SELECT",
                $item->prod_costopromedio,
                $item->prod_costoultimo,
                $item->gr_nombre,
                $item->sgr_nombre
            ];
        }


        $this->xlsxExport->export([
            'title' => 'REPORTE DE INVENTARIO GENERAL',
            'headers' => [
                'CÓDIGO', 'BARCODE', 'PRODUCTO', 'PRES.', 'LOTE', 'F. CADUCIDAD', 'STOCK',
                'RESERVA', 'STOCK DISPONIBLE', 'IVA',
                'BODEGA', 'COSTO PROMEDIO', 'COSTO ÚLTIMO',
                'GRUPO', 'SUBGRUPO'
            ],
            'data' => $rows,
            'filename' => 'Inventario_Por_Lotes_' . date('Ymd_His') . '.xlsx',
            'lastColumn' => 'O'
        ]);
    }

    public function exportInventarioConsolidadoExcel() {
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
        //Indexamos reservas
        $reservasProducto = [];

        foreach ($reservas ?? [] as $val) {
            $lote = ($val->fk_lote !== null) ? $val->fk_lote : "";
            $reservasProducto[$val->fk_producto . '|' . $lote] = (float) $val->res_cantidad;
        }

        $data = $this->invModel->getInventarioConsolidado($filtros, null, null, $searchValue, $orderBy, $orderDir);
        $rows = [];
        foreach ($data as $item) {
            $lote = ($item->fk_lote !== null) ? $item->fk_lote : "";
            $reserva = $reservasProducto[$item->id . '|' . $lote] ?? 0;

            $rows[] = [
                $item->prod_codigo,
                $item->prod_codigobarras,
                $item->prod_nombre,
                $item->um_nombre_corto,
                $item->lot_lote ? $item->lot_lote : "--",
                $item->lot_fecha_caducidad ? $item->lot_fecha_caducidad : "--",
                $item->stock,
                $reserva,
                $item->stock - $reserva,
                $item->prod_ivaporcentage === '0.00' ? 'SIN IVA' : 'IVA',
                $dataPost->invBodega ? $item->bod_nombre : "ALL SELECT",
                $item->prod_costopromedio,
                $item->prod_costoultimo,
                $item->gr_nombre,
                $item->sgr_nombre
            ];
        }


        $this->xlsxExport->export([
            'title' => 'REPORTE DE INVENTARIO CONSOLIDADO',
            'headers' => [
                'CÓDIGO', 'BARCODE', 'PRODUCTO', 'PRES.', 'LOTE', 'F. CADUCIDAD', 'STOCK',
                'RESERVA', 'STOCK DISPONIBLE', 'IVA',
                'BODEGA', 'COSTO PROMEDIO', 'COSTO ÚLTIMO',
                'GRUPO', 'SUBGRUPO'
            ],
            'data' => $rows,
            'filename' => 'Inventario_Consolidado_' . date('Ymd_His') . '.xlsx',
            'lastColumn' => 'O'
        ]);
    }

    public function exportExcelCaducidad() {

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

        //Indexamos reservas
        $reservasProducto = [];

        foreach ($reservas ?? [] as $val) {
            $reservasProducto[$val->fk_producto . '|' . $val->fk_lote] = $val->res_cantidad;
        }

        $data = $this->caducModel->getProductosCaducidad($filtros, null, null, $searchValue, $orderBy, $orderDir);
        $rows = [];
        foreach ($data as $item) {
            $reserva = $reservasProducto[$item->id . '|' . $item->fk_lote] ?? 0;

            $rows[] = [
                $item->prod_codigo,
                $item->prod_nombre,
                $item->um_nombre_corto,
                $item->lot_lote,
                $item->lot_fecha_caducidad,
                $item->stbl_stock - $reserva,
                $item->prod_ivaporcentage === '0.00' ? 'SIN IVA' : 'IVA',
                $dataPost->invBodega ? $item->bod_nombre : "ALL SELECT",
                $item->prod_costopromedio,
                $item->prod_costoultimo,
                $item->gr_nombre,
                $item->sgr_nombre
            ];
        }


        $this->xlsxExport->export([
            'title' => 'REPORTE DE CONTROL DE CADUCIDAD',
            'headers' => [
                'CÓDIGO', 'PRODUCTO', 'PRES.', 'LOTE', 'F. CADUCIDAD', 'STOCK DISPONIBLE', 'IVA',
                'BODEGA', 'COSTO PROMEDIO', 'COSTO ÚLTIMO',
                'GRUPO', 'SUBGRUPO'
            ],
            'data' => $rows,
            'filename' => 'Inventario_Control_Caducidad_' . date('Ymd_His') . '.xlsx',
            'lastColumn' => 'L'
        ]);
    }
}
