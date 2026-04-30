<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Inventarios\Controllers;

use App\Controllers\BaseController;
use Modules\Inventarios\Models\KardexModel;

/**
 * Description of KardexLotesController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 29 abr 2026
 * @time 6:18:55 p.m.
 */
class KardexLotesController extends BaseController {

    protected $karModel;

    public function __construct() {

        $this->karModel = new KardexModel();
    }

    public function getKardexLote() {
        $dataPost = json_decode(file_get_contents('php://input'));

        if (empty($dataPost->kardProductoId)) {
            return $this->response->setJSON([
                        'status' => 'error',
                        'msg' => "Ingrese un producto para generar el kardex",
            ]);
        }
        if (empty($dataPost->kardLoteId)) {
            return $this->response->setJSON([
                        'status' => 'error',
                        'msg' => "Debe seleccionar un lote para poder consultar el kardex",
            ]);
        }

        $filtros = [
            'kardBodega' => $dataPost->kardBodega ?? null,
            'kardProductoId' => $dataPost->kardProductoId,
            'kardLoteId' => $dataPost->kardLoteId,
            'rangoFechas' => $dataPost->rangoFechas ?? null
        ];

        $respuesta = $this->karModel->getKardexLotes($filtros);

        return $this->response->setJSON([
                    'status' => !empty($respuesta) ? 'success' : 'warning',
                    'data' => $respuesta,
        ]);
    }
}
