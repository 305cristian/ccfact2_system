<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Inventarios\Controllers;

use App\Controllers\BaseController;
use Modules\Inventarios\Models\KardexModel;

/**
 * Description of KardexProductoController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 10 mar 2026
 * @time 4:19:41 p.m.
 */
class KardexProductoController extends BaseController {

    protected $karModel;

    public function __construct() {

        $this->karModel = new KardexModel();
    }

    public function getKardexProducto() {
        $dataPost = json_decode(file_get_contents('php://input'));

        $filtros = [
            'kardBodega' => $dataPost->kardBodega ?? null,
            'kardProductoId' => $dataPost->kardProductoId ?? null,
            'rangoFechas' => $dataPost->rangoFechas ?? null
        ];

        $respuesta = $this->karModel->getKardexProducto($filtros);

        return $this->response->setJSON([
                    'status' => !empty($respuesta) ? 'success' : 'warning',
                    'data' => $respuesta,
        ]);
    }
    
    public function getMiniKardexProducto() {
        $dataPost = json_decode(file_get_contents('php://input'));

        $filtros = [
            'kardBodega' => $dataPost->kardBodega ?? null,
            'productoId' => $dataPost->productoId ?? null,
            'fecha' => date('Y-m-d', strtotime('-30 days'))
        ];

        $respuesta = $this->karModel->getMiniKardexProducto($filtros);

        return $this->response->setJSON([
                    'status' => !empty($respuesta) ? 'success' : 'warning',
                    'data' => $respuesta,
        ]);
    }
    
    
}
