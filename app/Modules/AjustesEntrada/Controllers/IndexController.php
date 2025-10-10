<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of IndexController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 8 oct 2025
 * @time 4:45:51 p.m.
 */

namespace Modules\AjustesEntrada\Controllers;

use Modules\Comun\Models\SearchsModel;

class IndexController extends \App\Controllers\BaseController {

    //put your code here
    protected $dirViewModule;
    protected $searchModel;

    public function __construct() {
        $this->dirViewModule = 'Modules\AjustesEntrada\Views';

        $this->searchModel = new SearchsModel();
    }

    public function index() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);

        $data['listaSustentos'] = $this->ccm->getData('cc_sustentos', ['sus_estado' => 1]);
        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1]);
        $data['listaMotivos'] = $this->ccm->getData('cc_motivos_ajuste', ['mot_estado' => 1, 'mot_tipo' => "AJUSTES"]);
        $data['listaCentroCostos'] = $this->ccm->getData('cc_centroscosto', ['cc_estado' => 1]);

        $send['view'] = view($this->dirViewModule . '\viewNewAjuste', $data);
        $send['user'] = $this->user;
        $send['ccm'] = $this->ccm;
        return view($this->dirTemplate . '\dashboard', $send);
    }

    public function insertProduct() {
        
        $dataPost= json_decode(file_get_contents("php://input"));

        $existe = $this->searchProductoCode($dataPost->id);
        if (!$existe) {
            $msg['status'] = "warning";
            $msg['msg'] = "No se ha encontrado el producto con el codigo: " . $dataPost->id;
            return $this->response->setJSON($msg);
        }

        $msg['status'] = "success";
        $msg['msg'] = "Producto agregado al carrito";
        return $this->response->setJSON($msg);
    }

//    public function showDetailCart() {
//        $data['total_articles'] = $this->depachoseppcart->total_articles();
//        $data['total_items'] = $this->countData($this->depachoseppcart->get_content());
//        $data['total_cart'] = $this->depachoseppcart->total_cart();
//        $data['despachos'] = $this->arrayReverse($this->depachoseppcart->get_content());
//        echo json_encode($data);
//    }

    public function saveAjuste() {
        return $this->response->setJSON(['status' => "success", 'msg' => "Ajuste registrado exitosamente"]);
    }

    public function searchProductoCode($codProd) {
        if ($codProd) {
            $response = $this->searchModel->searchProductoCode($codProd);
            return $response ? $response : false;
        }
    }
}
