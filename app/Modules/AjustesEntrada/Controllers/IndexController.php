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

use Modules\Comun\Models\ProductoModel;
use Modules\AjustesEntrada\Libraries\EntradasCartLib;
use Modules\AjustesEntrada\Libraries\EntradasLib;
use Modules\AjustesEntrada\Libraries\EntradasAsientosLib;
use Modules\AjustesEntrada\Models\EntradasModel;
use Modules\Comun\Models\SearchsModel;

class IndexController extends \App\Controllers\BaseController {

    //put your code here
    protected $dirViewModule;
    protected $entradasModel;
    protected $entradasLib;
    protected $entradasAsientoLib;
    protected $prodModel;
    protected $ajenCart;
    protected $searchModel;

    public function __construct() {
        $this->dirViewModule = 'Modules\AjustesEntrada\Views';

        //IMPORTACION DE MODELOS
        $this->entradasModel = new EntradasModel();
        $this->prodModel = new ProductoModel();
        $this->searchModel = new SearchsModel();

        //IMPORTACION DE LIBRERIAS
        $this->ajenCart = new EntradasCartLib();
        $this->entradasLib = new EntradasLib();
        $this->entradasAsientoLib = new EntradasAsientosLib();
    }

    public function index() {
        $view = $this->parametrosIndex();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($view);
        } else {
            return view($this->dirTemplate . '\dashboard', $view);
        }
    }

    public function indexEdit($idAjuste) {
        $view = $this->parametrosIndex($idAjuste);
        return view($this->dirTemplate . '\dashboard', $view);
    }

    public function parametrosIndex($idAjuste = null) {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);

        $data['listaSustentos'] = $this->ccm->getData('cc_sustentos', ['sus_estado' => 1], 'sus_codigo, sus_nombre');
        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaMotivos'] = $this->ccm->getData('cc_motivos_ajuste', ['mot_estado' => 1, 'mot_tipo' => "AJUSTES"], 'id, mot_nombre');
        $data['listaCentroCostos'] = $this->ccm->getData('cc_centroscosto', ['cc_estado' => 1], 'id, cc_nombre');

        $bodegaMainUsuario = $this->ccm->getValue('cc_bodegas', $this->user->id, 'id', 'id');

        $data['bodegaId'] = $this->session->get('bodegaIdAje') ? $this->session->get('bodegaIdAje') : $bodegaMainUsuario;

        $data['permitirDuplicados'] = getSettings('PERMITIR_ITEMS_DUPLICADOS');
        $data['dataAjuste'] = null;
        $data['dataProveedor'] = null;

        if (!empty($idAjuste)) {
            $data['dataAjuste'] = $this->ccm->getData('cc_ajuste_entrada', ['id' => $idAjuste], '*', null, 1);
            $data['dataProveedor'] = $this->searchModel->searchProveedorById($data['dataAjuste']->fk_proveedor);
        }

        $send['view'] = view($this->dirViewModule . '\viewNewAjuste', $data);

        return $send;
    }

    public function responseSetJSON($status, $mensaje, $data = null) {
        return $this->response->setJSON([
                    'status' => $status,
                    'msg' => $mensaje,
                    'data' => $data,
        ]);
    }

    public function loadAjusteEdit($idAjuste) {

        //CARGAMOS LOS DATOS AL CART
        $respuesta = $this->loadDataAjusteCart($idAjuste);

        return $this->response->setJSON([
                    'status' => $respuesta['status'] === 'success' ? 'success' : 'error',
                    'msg' => $respuesta['status'] === 'success' ? 'ok' : $respuesta['msg'],
                    'redirect' => site_url('ajustesentrada/indexEdit/' . $idAjuste)
        ]);
    }

    public function loadDataAjusteCart($idAjuste) {

        $this->ajenCart->destroy();

        $dataAjuste = $this->entradasModel->getDataDetalle($idAjuste);

        if ($dataAjuste->ajen_estado === '1') {

            foreach ($dataAjuste->detalle as $valDet) {

                $dataProducto = $this->entradasModel->searchProductoData($valDet->fk_producto);

                $dataStockBodega = $this->ccm->getData('cc_stock_bodega', ['fk_producto' => $valDet->fk_producto, 'fk_bodega' => $dataAjuste->id_bodega], 'stb_stock', null, 1);
                $stockBodega = $dataStockBodega ? $dataStockBodega->stb_stock : $dataProducto->prod_stockactual;

                $impuestos = $this->prodModel->getImpuestoTarifa($valDet->fk_producto);
                $tarifaIva = isset($impuestos[0]->impt_porcentage) ? $impuestos[0]->impt_porcentage : 0;
                $tarifaIce = isset($impuestos[1]->impt_porcentage) ? $impuestos[1]->impt_porcentage : 0;

                $item = [
                    "id" => (int) $dataProducto->id,
                    "qty" => (float) $valDet->ajend_itemcantidad,
                    "codigo" => $dataProducto->prod_codigo,
                    "name" => $dataProducto->prod_nombre,
                    "unidadMedida" => $dataProducto->um_nombre_corto,
                    "price" => (float) $valDet->ajend_itemcosto,
                    "stock" => $dataProducto->prod_stockactual,
                    "stockBodega" => $stockBodega,
                    "ivaPorcent" => $tarifaIva,
                    "icePorcent" => $tarifaIce,
                    "tieneLote" => $dataProducto->prod_ctrllote,
                    "permitirDuplicados" => $dataAjuste->ajen_items_duplicados,
                    "lote" => $valDet->lot_lote,
                    "fechaElaboracion" => $valDet->lot_fecha_elaboracion,
                    "fechaCaducidad" => $valDet->lot_fecha_caducidad,
                ];
                $item['servicio'] = $dataProducto->prod_isservicio;
                $this->ajenCart->insert($item);
            }
            return['status' => 'success', 'msg' => ''];
        } else {
            return['status' => 'error', 'msg' => 'Este ajuste de entrada ya se encuentra archivado o anulado previamente'];
        }
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

        $dataProducto = $this->entradasModel->searchProductoData($idProd);
        if (!$dataProducto) {
            $msg['status'] = "warning";
            $msg['msg'] = "No se ha encontrado el producto con el codigo: " . $idProd . '(Posiblemente este desactivado o aun no esta registrado )';
            return $this->response->setJSON($msg);
        }

        $dataStockBodega = $this->ccm->getData('cc_stock_bodega', ['fk_producto' => $idProd, 'fk_bodega' => $idBodega], 'stb_stock', null, 1);
        $stockBodega = $dataStockBodega ? $dataStockBodega->stb_stock : $dataProducto->prod_stockactual;

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
            "lote" => null,
            "fechaElaboracion" => null,
            "fechaCaducidad" => null,
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
            "unidadMedida" => $dataPost->unidadMedida,
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

        $rowidRand = $dataPost->rowid; //Parte clave para los items duplicados en el cart
        $this->ajenCart->update($item, $rowidRand);

        $msg['status'] = "success";
        $msg['msg'] = "Producto actualizado";
        return $this->response->setJSON($msg);
    }

    public function showDetailCart($key = 0) {
        $cartContent = $this->ajenCart->getContent();
        $dataCart['cartContent'] = $cartContent ? array_reverse($cartContent) : null;
        $dataCart['totalArticles'] = $this->ajenCart->totalArticles();
        $dataCart['totalItems'] = $cartContent ? count($cartContent) : null;
        $dataCart['totalCart'] = $this->ajenCart->totalCart();
        $dataCart['totalIva'] = $this->ajenCart->totalIva();
        $dataCart['totalBienes'] = $this->ajenCart->totalBienes();
        $dataCart['totalServicios'] = $this->ajenCart->totalServicios();
        $dataCart['totalCartIva'] = $this->ajenCart->totalCartIva();
        $dataCart['tarifCero'] = $this->ajenCart->tarifCero();
        $dataCart['tarifIva'] = $this->ajenCart->tarifIva();
        $dataCart['tarifCeroNeto'] = $this->ajenCart->tarifCeroNeto();
        $dataCart['tarifIvaNeto'] = $this->ajenCart->tarifIvaNeto();

        if ($key === 0) {
            return $this->response->setJSON($dataCart);
        } else {
            return json_decode(json_encode($dataCart));
        }
    }

    public function deleteProduct($rowId) {
        $this->ajenCart->removeItem($rowId);
    }

    public function cancelarAjuste() {
        $this->ajenCart->destroy();
    }

    public function changeBodega($bodegaId) {
        $this->session->set('bodegaIdAje', $bodegaId);
        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => 'Bodega seleccionada correctamente',
                    'bodegaId' => $bodegaId
        ]);
    }

    public function updateAjuste() {

        $dataPostAjuste = json_decode(json_encode($this->request->getPost()));

        // Validamos campos antes de procesar
        $statusValidation = $this->validarCampos($dataPostAjuste);
        if ($statusValidation['status']) {
            return $this->responseSetJSON("warning", $statusValidation['msg']);
        }

        $ajusteId = $dataPostAjuste->ajusteId;

        try {
            $cartData = $this->showDetailCart(1);
            $this->db->transBegin();

            $update = $this->entradasLib->updateAjuste($cartData, $dataPostAjuste, $ajusteId);
            if (!$update) {//UPDATE DEVUELVE TRUE SI TODO SE COMPLETO CORRECTAMENTE
                $this->db->transRollback();
                $this->responseSetJSON('error', 'Ha ocurrido un error al actualizar el Ajuste');
            }

            //Eliminamos todo el detalle anterior para registar el detalle actualizado.
            $this->ccm->eliminar('cc_ajuste_entrada_det', ['fk_ajuste_entrada' => $ajusteId]);

            foreach ($cartData->cartContent as $val) {

                // Validación de control de lotes
                $lote = null;
                if ($val->tieneLote === '1') {
                    if ((empty($val->lote) || empty($val->fechaElaboracion) || empty($val->fechaCaducidad))) {
                        $this->db->transRollback();
                        return $this->responseSetJSON(
                                        'warning',
                                        'El producto ' . $val->name . ' maneja control de lotes<br> Por favor revise el LOTE y sus respectivas FECHAS',
                        );
                    }

                    $existeLote = $this->ccm->getData('cc_lotes', ['lot_lote' => $val->lote, 'fk_producto' => $val->id], '*', null, 1);
                    if ($existeLote) {
                        $lote = $existeLote->id;
                    } else {
                        $lote = $this->saveLote($ajusteId, $val);
                    }
                }
                $ajusteIdDet = $this->entradasLib->saveAjusteDetalle($ajusteId, $val, $lote);

                if (!$ajusteIdDet) {
                    $this->db->transRollback();
                    return $this->responseSetJSON('error', 'Ha ocurrido un error al registrar el producto ' . $val->name . ' en el detalle del ajuste');
                }

                // Actualizamos el kardex solo si el ajuste está aprobado y no es servicio
                if ($dataPostAjuste->ajenEstado === '2' && $val->servicio === '0') {

                    $kardexOk = $this->entradasLib->updateKardex($ajusteId, $val, $lote, $dataPostAjuste);
                    if ($kardexOk['status'] !== 'success') {
                        $this->db->transRollback();
                        return $this->responseSetJSON($kardexOk['status'], $kardexOk['msg']);
                    }
                }
            }

            //SI TODO MARCHO BIEN REALIZO EL COMMIT
            $secuencail = $this->ccm->getValueWhere('cc_ajuste_entrada', ['id' => $ajusteId], 'ajen_secuencial');
            $this->db->transCommit();
            $this->ajenCart->destroy();
            $this->logs->logSuccess('Ajuste Actualizado exitosamente ID: ' . $ajusteId);
            log_message('info', "[Ajuste Entrada] Ajuste actualizado exitosamente ID: , DocID: {$ajusteId}");
            return $this->responseSetJSON("success", "Ajuste #" . $secuencail . " actualizado exitosamente");
        } catch (Exception $exc) {

            $this->db->transRollback();
            $this->logs->logError('Ha ocurrido un error al actualizar el Ajuste');
            return $this->responseSetJSON('error', '<br>Error al tratar de actualizar el Ajuste <br> ' . $exc->getMessage());
        }


        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => 'Ajuste actualizado exitosamente ' . $ajusteId,
        ]);
    }

    public function saveAjuste() {

        $dataPostAjuste = json_decode(json_encode($this->request->getPost()));

        // Validamos campos antes de procesar
        $statusValidation = $this->validarCampos($dataPostAjuste);
        if ($statusValidation['status']) {
            return $this->responseSetJSON("warning", $statusValidation['msg']);
        }


        try {
            $cartData = $this->showDetailCart(1);
            $this->db->transBegin();

            $ajusteId = $this->entradasLib->saveAjuste($cartData, $dataPostAjuste);

            if (!$ajusteId) {
                $this->db->transRollback();
                $this->responseSetJSON('error', 'Ha ocurrido un error al registrar el Ajuste');
            }

            foreach ($cartData->cartContent as $val) {

                // Validación de control de lotes
                $lote = null;
                if ($val->tieneLote === '1') {
                    if ((empty($val->lote) || empty($val->fechaElaboracion) || empty($val->fechaCaducidad))) {
                        $this->db->transRollback();
                        return $this->responseSetJSON(
                                        'warning',
                                        'El producto ' . $val->name . ' maneja control de lotes<br> Por favor revise el LOTE y sus respectivas FECHAS',
                        );
                    }

                    $existeLote = $this->ccm->getData('cc_lotes', ['lot_lote' => $val->lote, 'fk_producto' => $val->id], '*', null, 1);
                    if ($existeLote) {
                        $lote = $existeLote->id;
                    } else {
                        $lote = $this->saveLote($ajusteId, $val);
                    }
                }
                $ajusteIdDet = $this->entradasLib->saveAjusteDetalle($ajusteId, $val, $lote);

                if (!$ajusteIdDet) {
                    $this->db->transRollback();
                    return $this->responseSetJSON('error', 'Ha ocurrido un error al registrar el producto ' . $val->name . ' en el detalle del ajuste');
                }

                // Actualizamos el kardex solo si el ajuste está aprobado y no es servicio
                if ($dataPostAjuste->ajenEstado === '2' && $val->servicio === '0') {

                    $kardexOk = $this->entradasLib->updateKardex($ajusteId, $val, $lote, $dataPostAjuste);
                    if ($kardexOk['status'] !== 'success') {
                        $this->db->transRollback();
                        return $this->responseSetJSON($kardexOk['status'], $kardexOk['msg']);
                    }
                }
            }

            if ($dataPostAjuste->ajenEstado === '2') {
                $responseAsiento = $this->entradasAsientoLib->generarAsiento($ajusteId);
                if ($responseAsiento['status'] !== 'success') {
                    $this->db->transRollback();
                    return $this->responseSetJSON($responseAsiento['status'], $responseAsiento['msg']);
                }
            }

            //SI TODO MARCHO BIEN REALIZO EL COMMIT
            $secuencail = $this->ccm->getValueWhere('cc_ajuste_entrada', ['id' => $ajusteId], 'ajen_secuencial');
            $this->db->transCommit();
            $this->ajenCart->destroy();
            $this->logs->logSuccess('Ajuste registrado exitosamente ID: ' . $ajusteId);
            log_message('info', "[Ajuste Entrada] Ajuste registrado exitosamente ID: , DocID: {$ajusteId}");

            return $this->responseSetJSON("success", "Ajuste #" . $secuencail . " registrado exitosamente");
        } catch (\Throwable $exc) {

            $this->db->transRollback();
            $this->logs->logError('Ha ocurrido un error al registrar el Ajuste');
            return $this->responseSetJSON('error', '<br>Error al tratar de crear el Ajuste <br> ' . $exc->getMessage() . $exc->getTraceAsString());
        }
    }

    public function validarCampos($data) {

        $campos = [
            'ajenFecha' => 'Debe seleccionar una fecha',
            'ajenSustento' => 'Debe seleccionar un sustento',
            'ajenBodega' => 'Debe seleccionar una bodega',
            'ajenCentrocosto' => 'Debe seleccionar un centro de costos',
            'ajenMotivo' => 'Debe seleccionar un motivo de ajuste',
            'ajenEstado' => 'Debe seleccionar un estado',
            'ajenProveedor' => 'Debe seleccionar un proveedor',
        ];

        // Validar campos genéricos
        foreach ($campos as $campo => $mensaje) {
            if (empty($data->$campo)) {
                return [
                    'status' => true,
                    'msg' => $mensaje
                ];
            }
        }

        // Si todo está correcto
        return ['status' => false];
    }

    public function saveLote($ajusteId, $producto) {
        $dataLote = [
            'lot_lote' => $producto->lote,
            'lot_fecha_elaboracion' => $producto->fechaElaboracion,
            'lot_fecha_caducidad' => $producto->fechaCaducidad,
            'lot_documento_id' => $ajusteId,
            'fk_producto' => $producto->id,
        ];
        $lote = $this->ccm->guardar($dataLote, 'cc_lotes');
        return $lote;
    }
}
