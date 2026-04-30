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
use Modules\Inventarios\Models\HistoricoModel;
use Modules\Inventarios\Models\InventarioModel;
use Modules\Inventarios\Models\KardexModel;

class ExcelExportController extends BaseController {

    protected $invModel;
    protected $caducModel;
    protected $hisModel;
    protected $xlsxExport;
    protected $karModel;

    public function __construct() {
        //MODELOS
        $this->invModel = new InventarioModel();
        $this->caducModel = new CaducidadModel();
        $this->hisModel = new HistoricoModel();
        $this->karModel = new KardexModel();

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

    public function exportExcelHistorico() {
        $dataPost = json_decode(file_get_contents('php://input'));

        $searchValue = (string) $dataPost->search ?? '';
        $orderData = $dataPost->order ?? [];
        $orderBy = ($orderData[0]->column ?? '') ?: 'prod_nombre';
        $orderDir = ($orderData[0]->dir ?? '') ?: 'asc';

        $filtros = [
            'invBodega' => $dataPost->invBodega ?? null,
            'kardStock' => $dataPost->invStock !== '-1' ? $dataPost->invStock : null,
            'invGrupo' => $dataPost->invGrupo ?? null,
            'invIva' => $dataPost->invIva !== '-1' ? $dataPost->invIva : null,
            'fechaCorte' => $dataPost->fechaCorte ?? date('Y-m-d'),
            'invSubgrupo' => $dataPost->invSubgrupo ?? null
        ];

        $data = $this->hisModel->getInventarioHistorico($filtros, null, null, $searchValue, $orderBy, $orderDir);

        $rows = [];
        foreach ($data as $item) {

            $rows[] = [
                $item->prod_codigo,
                $item->prod_nombre,
                $item->um_nombre_corto,
                $item->kardexStock,
                $item->prod_ivaporcentage === '0.00' ? 'SIN IVA' : 'IVA',
                $dataPost->invBodega ? $item->bod_nombre : "ALL SELECT",
                $item->costoPromedio,
                $item->total_cst_promedio,
                $item->costoUltimo,
                $item->total_cst_ultimo,
                $item->gr_nombre,
                $item->sgr_nombre,
                $item->tr_nombre
            ];
        }


        $this->xlsxExport->export([
            'title' => 'REPORTE DE INVENTARIO HISTÓRICO',
            'headers' => [
                'CÓDIGO', 'PRODUCTO', 'PRES.', 'STOCK', 'IVA', 'BODEGA',
                'COSTO PROMEDIO', 'TOT. COSTO PROM.', 'COSTO ÚLTIMO', 'TOT. COSTO ULT.',
                'GRUPO', 'SUBGRUPO', 'MOVIMIENTO'
            ],
            'data' => $rows,
            'filename' => 'Inventario_Histórico_' . date('Ymd_His') . '.xlsx',
            'lastColumn' => 'M'
        ]);
    }

    public function exportExcelKardexGeneral() {

        $dataPost = json_decode(file_get_contents('php://input'));

        $searchValue = $dataPost->search ?? '';
        $orderData = $dataPost->order ?? [];
        $orderBy = $orderData[0]->column ?? 'k.kar_fecha';
        $orderDir = $orderData[0]->dir ?? 'DESC';

        $movimiento = $dataPost->movimiento ?? null;

        $filtros = [
            'productoId' => $dataPost->kardProductoId ?? null,
            'bodegaId' => $dataPost->kardBodega ?? null,
            'grupoId' => $dataPost->kardGrupo ?? null,
            'tipoTransferencia' => $dataPost->tipoTransferencia,
            'rangoFechasKardex' => $dataPost->rangoFechasKardex ?? null,
            'rangoFechasEmision' => $dataPost->rangoFechasEmision ?? null
        ];

        $mostrarDocumento = in_array($movimiento, ['COMPRAS', 'VENTAS']);
        $mostrarMotivo = in_array($movimiento, ['AJUSTES_DE_ENTRADA', 'AJUSTES_DE_SALIDA']);
        $mostrarProvClie = in_array($movimiento, ['COMPRAS', 'VENTAS']);

        $data = $this->karModel->getKardexGeneral($filtros, $movimiento, null, null, $searchValue, $orderBy, $orderDir);

        $rows = [];
        foreach ($data['data'] as $item) {
            $row = [
                $item->fecha_movimiento,
                $item->fecha_emision,
                $item->prod_codigo,
                $item->prod_nombre,
                $item->gr_nombre,
                $item->sgr_nombre,
                $item->bod_nombre,
                $item->lot_lote ?? 'N/A',
                $item->lot_fecha_caducidad ?? 'N/A',
                number_format($item->cantidad, 2),
                $item->kar_costo_promedio,
                $item->total_promedio,
                $item->kar_costo_ultimo,
                $item->total_ultimo,
                $item->transaccion ?? '-'
            ];

            if ($mostrarDocumento) {
                $row[] = $item->documento ?? '-';
            }

            if ($mostrarMotivo) {
                $row[] = $item->motivo ?? '-';
            }

            if ($mostrarProvClie) {
                $row[] = $item->proveedor_cliente ?? '-';
            }
            $rows[] = $row;
        }

        $headers = [
            'FECHA MOV.',
            'FECHA EMISIÓN',
            'CÓDIGO',
            'PRODUCTO',
            'GRUPO',
            'SUBGRUPO',
            'BODEGA',
            'LOTE',
            'F. CADUC.',
            'CANTIDAD',
            'C. PROMEDIO',
            'TOTAL PROM.',
            'C. ÚLTIMO',
            'TOTAL ÚLTIMO',
            'TRANSACCIÓN'
        ];

        if ($mostrarDocumento) {
            $headers[] = 'NUM DOCUMENTO';
        }

        if ($mostrarMotivo) {
            $headers[] = 'MOTIVO';
        }

        if ($mostrarProvClie) {
            $headers[] = 'PROV/CLI';
        }

        $title = match ($movimiento) {
            'COMPRAS' => 'REPORTE DE COMPRAS',
            'VENTAS' => 'REPORTE DE VENTAS',
            'TRANSFERENCIAS' => 'REPORTE DE TRANSFERENCIAS',
            'AJUSTES_DE_ENTRADA' => 'REPORTE AJUSTES ENTRADA',
            'AJUSTES_DE_SALIDA' => 'REPORTE AJUSTES SALIDA',
            default => 'REPORTE KARDEX GENERAL'
        };

        $this->xlsxExport->export([
            'title' => $title,
            'headers' => $headers,
            'data' => $rows,
            'filename' => 'Kardex_General_' . date('Ymd_His') . '.xlsx',
            'lastColumn' => chr(64 + count($headers)) // A=1, B=2, ...
        ]);
    }
}
