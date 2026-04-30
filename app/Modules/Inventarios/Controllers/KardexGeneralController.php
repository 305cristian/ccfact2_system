<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Inventarios\Controllers;

use App\Controllers\BaseController;
use Modules\Inventarios\Models\KardexModel;

/**
 * Description of KardexGeneralController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 29 abr 2026
 * @time 9:31:05 a.m.
 */
class KardexGeneralController extends BaseController {

    protected $karModel;

    public function __construct() {

        $this->karModel = new KardexModel();
    }

    // Helper respuesta JSON estándar
    public function responseSetJSON($status, $mensaje, $data = null) {
        return $this->response->setJSON([
                    'status' => $status,
                    'msg' => $mensaje,
                    'data' => $data,
        ]);
    }

    public function getKardexGeneral() {

        $dataPost = json_decode(file_get_contents('php://input'));

        $draw = $dataPost->draw ?? 1;
        $start = (int) ($dataPost->start ?? 0);
        $length = (int) ($dataPost->length ?? 10);
        $searchValue = $dataPost->search ?? '';

        $orderData = $dataPost->order ?? [];
        $orderBy = $orderData[0]->column ?? 'k.kar_fecha';
        $orderDir = $orderData[0]->dir ?? 'DESC';

        $movimiento = $dataPost->movimiento ?? null;

        if (empty($movimiento)) {
            return $this->responseSetJSON('warning', 'Debe seleccionar un tipo de transacción (AJUSTE DE ENTRADA,AJUSTE DE SALIDA, TRANSFERENCIAS, etc)');
        }

        if ($movimiento === 'TRANSFERENCIAS' && empty($dataPost->tipoTransferencia)) {
            return $this->responseSetJSON('warning', 'Debe seleccionar un tipo de movimiento (ENTRADAS, SALIDAS)');
        }

        $filtros = [
            'productoId' => $dataPost->kardProductoId ?? null,
            'bodegaId' => $dataPost->kardBodega ?? null,
            'grupoId' => $dataPost->kardGrupo ?? null,
            'tipoTransferencia' => $dataPost->tipoTransferencia,
            'rangoFechasKardex' => $dataPost->rangoFechasKardex ?? null,
            'rangoFechasEmision' => $dataPost->rangoFechasEmision ?? null
        ];

        $respuesta = $this->karModel->getKardexGeneral($filtros, $movimiento, $start, $length, $searchValue, $orderBy, $orderDir);

        // =========================
        // NORMALIZAMOS
        // =========================
        $data = array_map(function ($row) {

            return [
        'fecha_movimiento' => $row->fecha_movimiento,
        'fecha_emision' => $row->fecha_emision,
        'codigo' => $row->prod_codigo,
        'producto' => $row->prod_nombre,
        'grupo' => $row->gr_nombre,
        'subgrupo' => $row->sgr_nombre,
        'bodega' => $row->bod_nombre,
        'lote' => $row->lot_lote,
        'fecha_caducidad' => $row->lot_fecha_caducidad,
        'cantidad' => number_format($row->cantidad, 2),
        'costo_promedio' => $row->kar_costo_promedio,
        'total_promedio' => $row->total_promedio,
        'costo_ultimo' => $row->kar_costo_ultimo,
        'total_ultimo' => $row->total_ultimo,
        'documento' => $row->documento ?? null,
        'transaccion' => $row->transaccion ?? null,
        'motivo' => $row->motivo ?? null,
        'proveedor_cliente' => $row->proveedor_cliente ?? null,
        'kar_documento_id' => $row->kar_documento_id,
        'transaccion_cod' => $row->kar_codigo_transaccion,
            ];
        }, $respuesta['data']);

        return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $data,
                    'draw' => intval($draw),
                    'recordsTotal' => $respuesta['total'],
                    'recordsFiltered' => $respuesta['filtered']
        ]);
    }
}
