<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Comun\Controllers;

/**
 * Description of SearchsController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 9 oct 2025
 * @time 12:56:51 p.m.
 */
use Modules\Comun\Models\SearchsModel;

class SearchsController extends \App\Controllers\BaseController {

    //put your code here
    protected $searchModel;

    public function __construct() {
        $this->searchModel = new SearchsModel();
    }

    public function searchProveedor() {

        $data = json_decode(file_get_contents("php://input"));
        if ($data->dataSerach) {
            $response = $this->searchModel->searchProveedores($data->dataSerach);
            if ($response) {
                return $this->response->setJSON($response);
            }
        }
        return $this->response->setJSON(false);
    }

    public function searchProductos() {

        $data = json_decode(file_get_contents("php://input"));

        if ($data->dataSerach) {
            $response = $this->searchModel->searchProductos($data);
            if ($response) {
                return $this->response->setJSON($response);
            }
        }
        return $this->response->setJSON(false);
    }

    public function searchProductoCode($codProd) {
        if ($codProd) {
            $response = $this->searchModel->searchProductoCode($codProd);
            if ($response) {
                return $this->response->setJSON(['status' => 'success']);
            }
        }
        return $this->response->setJSON(false);
    }
}
