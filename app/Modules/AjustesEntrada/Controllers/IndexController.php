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
use Modules\Comun\Models\ProductoModel;
use Modules\AjustesEntrada\Libraries\EntradasCartLib;
use Modules\AjustesEntrada\Models\EntradasModel;

class IndexController extends \App\Controllers\BaseController {

    //put your code here
    protected $dirViewModule;
    protected $antradasModel;
    protected $prodModel;
    protected $ajenCart;

    public function __construct() {
        $this->dirViewModule = 'Modules\AjustesEntrada\Views';

        //IMPORTACION DE MODELOS
        $this->antradasModel = new EntradasModel();
        $this->prodModel = new ProductoModel();

        //IMPORTACION DE LIBRERIAS
        $this->ajenCart = new EntradasCartLib();
    }

    public function index() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);

        $data['listaSustentos'] = $this->ccm->getData('cc_sustentos', ['sus_estado' => 1], 'sus_codigo, sus_nombre');
        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaMotivos'] = $this->ccm->getData('cc_motivos_ajuste', ['mot_estado' => 1, 'mot_tipo' => "AJUSTES"], 'id, mot_nombre');
        $data['listaCentroCostos'] = $this->ccm->getData('cc_centroscosto', ['cc_estado' => 1], 'id, cc_nombre');

        $bodegaMainUsuario = $this->ccm->getValue('cc_bodegas', $this->user->id, 'id', 'id');

        $data['bodegaId'] = $this->session->get('bodegaIdAje') ? $this->session->get('bodegaIdAje') : $bodegaMainUsuario;
        $send['view'] = view($this->dirViewModule . '\viewNewAjuste', $data);
        $send['user'] = $this->user;
        $send['ccm'] = $this->ccm;
        return view($this->dirTemplate . '\dashboard', $send);
    }

    public function insertProduct() {

        $dataPost = json_decode(file_get_contents("php://input"));

        $idProd = $dataPost->id;
        $cantidad = $dataPost->qty;
        $permitirDuplicados = $dataPost->permitirDuplicados;
        $idBodega = $dataPost->bodega;

        if ($idProd <= '0' or $idProd == null) {
            $msg['status'] = "warning";
            $msg['msg'] = "No pueden haber productos con código 0 o NULO ";
            return $this->response->setJSON($msg);
        }

        $dataProducto = $this->antradasModel->searchProductoData($idProd);
        if (!$dataProducto) {
            $msg['status'] = "warning";
            $msg['msg'] = "No se ha encontrado el producto con el codigo: " . $idProd;
            return $this->response->setJSON($msg);
        }

        $dataStockBodega = $this->ccm->getData('cc_stock_bodega', ['fk_producto' => $idProd, 'fk_bodega' => $idBodega], 'stk_stock');
        $stockBodega = $dataStockBodega ? $dataStockBodega->stk_stock : $dataProducto->prod_stockactual;

        $impuestos = $this->prodModel->getImpuestoTarifa($dataProducto->id);
        $tarifaIva = isset($impuestos[0]->impt_porcentage) ? $impuestos[0]->impt_porcentage : 0;
        $tarifaIce = isset($impuestos[1]->impt_porcentage) ? $impuestos[1]->impt_porcentage : 0;

        $item = [
            "id" => (int) $dataProducto->id,
            "qty" => (float) $cantidad,
            "codigo" => $dataProducto->prod_codigo,
            "name" => $dataProducto->prod_nombre,
            "unidadMedida" => $dataProducto->um_nombre_corto,
            "price" => (float) $dataProducto->prod_costopromedio,
            "stock" => $dataProducto->prod_stockactual,
            "stockBodega" => $stockBodega,
            "ivaPorcent" => $tarifaIva,
            "icePorcent" => $tarifaIce,
            "tieneLote" => $dataProducto->prod_ctrllote,
            "permitirDuplicados" => $permitirDuplicados,
        ];
        $item['servicio'] = $dataProducto->prod_isservicio;
        $this->ajenCart->insert($item);

        $msg['status'] = "success";
        $msg['msg'] = "Producto agregado al carrito";
        return $this->response->setJSON($msg);
    }

    public function updateProduct() {
        $dataPost = json_decode(file_get_contents("php://input"));

        $idProd = $dataPost->id;
        $cantidad = $dataPost->qty;
        $permitirDuplicados = $dataPost->permitirDuplicados;

        if ($idProd <= '0' or $idProd == null) {
            $msg['status'] = "warning";
            $msg['msg'] = "No pueden haber productos con código 0 o NULO ";
            return $this->response->setJSON($msg);
        }

        $item = [
            "id" => (int) $idProd,
            "qty" => (float) $cantidad,
            "codigo" => $dataPost->codigo,
            "name" => $dataPost->name,
            "unidadMedida" => null,
            "price" => (float) $dataPost->price,
            "stock" => $dataPost->stock,
            "stockBodega" => 0,
            "ivaPorcent" => $dataPost->ivaPorcent,
            "icePorcent" => $dataPost->icePorcent,
            "tieneLote" => $dataPost->tieneLote,
            "permitirDuplicados" => $permitirDuplicados,
            "lote" => $dataPost->lote ?? null,
            "fechaElaboracion" => $dataPost->fechaElaboracion ?? null,
            "fechaCaducidad" => $dataPost->fechaCaducidad ?? null,
        ];
        $item['servicio'] = $dataPost->servicio;

        $this->ajenCart->update($item);
        $msg['status'] = "success";
        $msg['msg'] = "Producto actualizado";
        return $this->response->setJSON($msg);
    }

    public function showDetailCart() {
        $cartContent = $this->ajenCart->getContent();
        $data['cartContent'] = $cartContent ? array_reverse($cartContent) : null;
        $data['totalArticles'] = $this->ajenCart->totalArticles();
        $data['totalItems'] = $cartContent ? count($cartContent) : null;
        $data['totalCart'] = $this->ajenCart->totalCart();
        $data['totalIva'] = $this->ajenCart->totalIva();
        $data['totalBienes'] = $this->ajenCart->totalBienes();
        $data['totalServicios'] = $this->ajenCart->totalServicios();
        $data['totalCartIva'] = $this->ajenCart->totalCartIva();
        return $this->response->setJSON($data);
    }

    public function deleteProduct($rowId) {
        $this->ajenCart->removeItem($rowId);
    }

    public function changeBodega($bodegaId) {
        $this->session->set('bodegaIdAje', $bodegaId);
        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => 'Bodega seleccionada correctamente',
                    'bodegaId' => $bodegaId
        ]);
    }

    public function saveAjuste() {
        return $this->response->setJSON(['status' => "success", 'msg' => "Ajuste registrado exitosamente"]);
    }
}
