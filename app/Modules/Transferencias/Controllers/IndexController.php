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
 * @date 4 dic 2025
 * @time 11:34:03 p.m.
 */
/**
 * NOTA:
 * Los precios e IVA en transferencias son SOLO informativos.
 * No afectan inventario, kardex ni contabilidad.
 * */

namespace Modules\Transferencias\Controllers;

use Modules\Transferencias\Libraries\TransferenciasCartLib;
use Modules\Transferencias\Libraries\TransferenciasLib;
use Modules\Comun\Models\BodegasUserModel;
use Modules\Comun\Models\SearchsModel;
use Modules\Comun\Libraries\StockBodegaLib;
use Modules\Comun\Models\LotesStockModel;
use Modules\Comun\Libraries\ReservasLib;
use Modules\Comun\Models\ProductoModel;
use Modules\Transferencias\Models\TransferenciasModel;

class IndexController extends \App\Controllers\BaseController {

    //put your code here

    protected $transaccionCod = '17';
    protected $dirViewModule;
    protected $transferenciaCart;
    protected $uersBodModel;
    protected $searchModel;
    protected $stockBodLib;
    protected $lotesStkModel;
    protected $reservasLib;
    protected $prodModel;
    protected $transfLib;
    protected $transferModel;

    public function __construct() {
        $this->dirViewModule = 'Modules\Transferencias\Views';

        //IMPORTACION DE MODELOS
        $this->uersBodModeldel = new BodegasUserModel();
        $this->searchModel = new SearchsModel();
        $this->lotesStkModel = new LotesStockModel();
        $this->prodModel = new ProductoModel();
        $this->transferModel = new TransferenciasModel();

        //IMPORTACION DE LIBRERIAS
        $this->transferenciaCart = new TransferenciasCartLib();
        $this->stockBodLib = new StockBodegaLib();
        $this->reservasLib = new ReservasLib();
        $this->transfLib = new TransferenciasLib();
    }

    public function index() {
        $view = $this->parametrosIndex();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($view);
        } else {
            return view($this->dirTemplate . '\dashboard', $view);
        }
    }

    public function indexEdit($transferenciaId) {
        $view = $this->parametrosIndex($transferenciaId);
        return view($this->dirTemplate . '\dashboard', $view);
    }

    public function parametrosIndex($transferenciaId = null) {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);

        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaCentroCostos'] = $this->ccm->getData('cc_centroscosto', ['cc_estado' => 1], 'id, cc_nombre');

        $bodegaMainUsuario = $this->ccm->getValue('cc_bodegas', $this->user->id, 'id', 'id');

        $data['bodegaId'] = $this->session->get('bodegaIdTrb') ? $this->session->get('bodegaIdTrb') : $bodegaMainUsuario;

        $data['permitirDuplicados'] = getSettings('PERMITIR_ITEMS_DUPLICADOS');
        $data['dataTransferencia'] = null;

        if (!empty($transferenciaId)) {
            $data['dataTransferencia'] = $this->ccm->getData('cc_transferencia_bodega', ['id' => $transferenciaId], '*', null, 1);
        }

        $send['view'] = view($this->dirViewModule . '\viewNewTransferencia', $data);

        return $send;
    }

    public function responseSetJSON($status, $mensaje, $data = null) {
        return $this->response->setJSON([
                    'status' => $status,
                    'msg' => $mensaje,
                    'data' => $data,
        ]);
    }

    /**
     * Insertar producto al cart de TRANSFERENCIAS
     * Aquí se debe validar stock disponible considerando reservas (en SalidasLib)
     */
    public function insertProduct() {
        $dataPost = json_decode(file_get_contents("php://input"));

        $idProd = $dataPost->id;
        $cantidad = $dataPost->qty;
        $permitirDuplicados = $dataPost->permitirDuplicados;
        $idBodega = $dataPost->bodega;

        if ($idProd <= '0' || $idProd == null) {
            return $this->responseSetJSON("warning", "No pueden haber productos con código 0 o NULO");
        }

        $dataProducto = $this->searchModel->searchProductoData($idProd);
        if (!$dataProducto) {
            return $this->responseSetJSON("warning", "No se ha encontrado el producto con el código: {$idProd} (Posiblemente está desactivado o aún no está registrado)");
        }

        // Validar stock disponible (stock real - reservas)
        $validarStock = $this->stockBodLib->validarStockDisponible($dataProducto->id, $idBodega, $cantidad);
        if ($validarStock['status'] !== 'success') {
            return $this->responseSetJSON($validarStock['status'], $validarStock['msg']);
        }


        //Obtenemos los lotes con su stock en caso de que el producto maneje lotes 
        $lotesStock = [];
        if ($dataProducto->prod_ctrllote === '1') {
            $lotesStock = $this->lotesStkModel->getLotesStock($dataProducto->id, $idBodega);
            foreach ($lotesStock as $val) {
                $reservas = $this->reservasLib->getReservasProductoLote($dataProducto->id, $idBodega, $val->fk_lote);
                $val->stockLote = $val->stbl_stock - $reservas['reserva'];
            }
        }


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
            "stock" => number_format($dataProducto->prod_stockactual, 2),
            "stockBodega" => number_format($validarStock['dataStockDisponible'], 2),
            "ivaPorcent" => $tarifaIva,
            "icePorcent" => $tarifaIce,
            "permitirDuplicados" => $permitirDuplicados,
            "tieneLote" => $dataProducto->prod_ctrllote,
            "lotes" => $lotesStock,
            "idLote" => isset($lotesStock[0]->fk_lote) ? $lotesStock[0]->fk_lote : null,
            "servicio" => $dataProducto->prod_isservicio,
            "idBodega" => $idBodega
        ];

        $this->transferenciaCart->insert($item);

        return $this->responseSetJSON("success", "Producto agregado al carrito");
    }

    public function updateProduct() {
        $this->user->validateSession();
        $dataPost = json_decode(file_get_contents("php://input"));

        $idProd = $dataPost->id;
        $cantidad = $dataPost->qty;
        $idBodega = $dataPost->idBodega;
        $permitirDuplicados = $dataPost->permitirDuplicados;

        if ($idProd <= '0' || $idProd == null) {
            return $this->responseSetJSON("warning", "No pueden haber productos con código 0 o NULO");
        }

        // Validar stock disponible considerando reservas, para la nueva cantidad
        // Validar stock disponible (stock real - reservas)
        $idTransferencia = empty($dataPost->transferId) ? null : $dataPost->transferId; //Cuando el updateProduct se ejecutado desde una actualización de una transferenca en borrrador hacemos uso del ID de la transferencia
        $idLote = ($dataPost->tieneLote === '1') ? $dataPost->idLote : null;
        $validarStock = $this->stockBodLib->validarStockDisponible($idProd, $idBodega, $cantidad, $this->transaccionCod, $idTransferencia, $idLote);

        if ($validarStock['status'] !== 'success') {
            return $this->responseSetJSON($validarStock['status'], $validarStock['msg']);
        }

        //Obtenemos el stock por bodega
        $dataStockBodega = $this->ccm->getData('cc_stock_bodega', ['fk_producto' => $idProd, 'fk_bodega' => $idBodega], 'stb_stock', null, 1);
        $stockBodega = $dataStockBodega ? $dataStockBodega->stb_stock - $validarStock['dataReservado'] : 0;

        $item = [
            "id" => (int) $idProd,
            "qty" => (float) $cantidad,
            "codigo" => $dataPost->codigo,
            "name" => $dataPost->name,
            "unidadMedida" => $dataPost->unidadMedida,
            "price" => (float) $dataPost->price,
            "stock" => number_format($dataPost->stock, 2),
            "stockBodega" => number_format($stockBodega, 2),
            "ivaPorcent" => $dataPost->ivaPorcent,
            "icePorcent" => $dataPost->icePorcent,
            "permitirDuplicados" => $permitirDuplicados,
            "tieneLote" => $dataPost->tieneLote,
            "lotes" => $dataPost->lotes,
            "idLote" => $dataPost->idLote,
            "servicio" => $dataPost->servicio,
            "idBodega" => $idBodega
        ];

        $rowidRand = $dataPost->rowid;
        $this->transferenciaCart->update($item, $rowidRand);

        return $this->responseSetJSON('success', 'Producto actualizado');
    }

    public function showDetailCart($key = 0) {

        $cartContent = $this->transferenciaCart->getContent();
        $dataCart['cartContent'] = $cartContent ? array_reverse($cartContent) : null;
        $dataCart['totalArticles'] = $this->transferenciaCart->totalArticles();
        $dataCart['totalItems'] = $cartContent ? count($cartContent) : null;
        $dataCart['totalCart'] = $this->transferenciaCart->totalCart();
        $dataCart['totalIva'] = $this->transferenciaCart->totalIva();
        $dataCart['totalCartIva'] = $this->transferenciaCart->totalCartIva();
//        $dataCart['tarifCero'] = $this->transferenciaCart->tarifCero();
//        $dataCart['tarifIva'] = $this->transferenciaCart->tarifIva();
//        $dataCart['tarifCeroNeto'] = $this->transferenciaCart->tarifCeroNeto();
//        $dataCart['tarifIvaNeto'] = $this->transferenciaCart->tarifIvaNeto();
        if ($key === 0) {
            return $this->response->setJSON($dataCart);
        } else {
            return json_decode(json_encode($dataCart));
        }
    }

    public function saveTransferencia() {
        $dataPostTrb = json_decode(json_encode($this->request->getPost()));

        // Validar campos
        $statusValidation = $this->validarCampos($dataPostTrb);
        if ($statusValidation['status']) {
            return $this->responseSetJSON("warning", $statusValidation['msg']);
        }

        try {

            $cartData = $this->showDetailCart(1);

            $this->db->transBegin();

            $transferenciaId = $this->transfLib->saveTransferencia($cartData, $dataPostTrb);
            if (!$transferenciaId) {
                $this->db->transRollback();
                return $this->responseSetJSON("error", 'Ha ocurrido un error al registrar la transferencia');
            }

            foreach ($cartData->cartContent as $val) {

                //Valido que si maneja control de lotes el producto venga con un lote seleccionado
                if ($val->tieneLote === '1' && empty($val->idLote)) {
                    $this->db->transRollback();
                    return $this->responseSetJSON("warning", "El producto {$val->name} tiene control de lotes, seleccione uno por favor ");
                }

                //Validamos que sea una cantidad valida
                if ($val->qty <= 0) {
                    $this->db->transRollback();
                    return $this->responseSetJSON("warning", "No pueden haber productos con cantidad menor o igual a 0<br> para el producto {$val->name}");
                }

                //Validadmos que sea un precio valido
                if ($val->price <= 0) {
                    $this->db->transRollback();
                    return $this->responseSetJSON("warning", "No pueden haber productos con precio menor o igual a 0<br> para el producto {$val->name}");
                }

                // Validar stock disponible considerando reservas, para la nueva cantidad
                // Validar stock disponible (stock real - reservas)
                $idLote = ($val->tieneLote === '1') ? $val->idLote : null;
                $validarStock = $this->stockBodLib->validarStockDisponible($val->id, $dataPostTrb->trbBodegaOrigen, $val->qty, null, null, $idLote);

                if ($validarStock['status'] !== 'success') {
                    $this->db->transRollback();
                    return $this->responseSetJSON($validarStock['status'], $validarStock['msg']);
                }

                $ajusteDetId = $this->transfLib->saveTransferenciaDetalle($transferenciaId, $val, $idLote);
                if (!$ajusteDetId) {
                    $this->db->transRollback();
                    return $this->responseSetJSON("error", "Ha ocurrido un error al registrar el producto { $val->name} en el detalle de la transferencia");
                }
            }

            // Manejo de reservas según estado (EN ESTADO BORRADOR Y POR CONFIRMAR GENERAMOS RESERVAS)
            if (($dataPostTrb->trbEstado === '1' || $dataPostTrb->trbEstado === '2' ) && $val->servicio === '0') {
                $reservas = $this->transfLib->registrarReservas($transferenciaId, $cartData, $dataPostTrb);
                if ($reservas['status'] !== 'success') {
                    $this->db->transRollback();
                    return $this->responseSetJSON($reservas['status'], $reservas['msg']);
                }
            }

            $secuencial = $this->ccm->getValueWhere('cc_transferencia_bodega', ['id' => $transferenciaId], 'trb_secuencial');

            $this->db->transCommit();
            $this->transferenciaCart->destroy();
            $this->logs->logSuccess('Transferencia de productos registrado exitosamente ID: ' . $transferenciaId);
            log_message('info', "[Transferencia de productos] Transferencia registrado exitosamente, DocID: {$transferenciaId}");

            $dataResponse = ['id' => $transferenciaId, 'trb_secuencial' => $secuencial];

            if ($dataPostTrb->trbEstado === '2') {
                return $this->responseSetJSON(
                                "success",
                                "<h5>TRansferencia #" . $secuencial . " registrado exitosamente</h5>",
                                $dataResponse
                );
            } else {
                return $this->responseSetJSON(
                                "success",
                                "<span class='text-warning'>Transferencia #" . $secuencial . " registrado exitosamente<br>REGISTRADO COMO BORRADOR<br></span>",
                                $dataResponse
                );
            }
        } catch (Exception $exc) {
            $this->db->transRollback();
            $this->logs->logError('Ha ocurrido un error al registrar la transferencia de productos');
            return $this->responseSetJSON(
                            'error',
                            '<br>Error al tratar de crear la transferencia de productos <br> ' . $exc->getMessage() . $exc->getTraceAsString()
            );
        }
    }

    public function updateTransferencia() {
        $dataPostTrb = json_decode(json_encode($this->request->getPost()));

        // Validar campos
        $statusValidation = $this->validarCampos($dataPostTrb);
        if ($statusValidation['status']) {
            return $this->responseSetJSON("warning", $statusValidation['msg']);
        }

        $transferenciaId = $dataPostTrb->transferenciaId;

        try {

            $cartData = $this->showDetailCart(1);

            $this->db->transBegin();

            $update = $this->transfLib->updateTransferencia($cartData, $dataPostTrb, $transferenciaId);
            if (!$update) {
                $this->db->transRollback();
                return $this->responseSetJSON("error", 'Ha ocurrido un error al registrar la transferencia');
            }

            // Eliminamos detalle anterior
            $this->ccm->eliminar('cc_transferencia_bodega_det', ['fk_transferencia_bodega' => $transferenciaId]);

            foreach ($cartData->cartContent as $val) {

                //Valido que si maneja control de lotes el producto venga con un lote seleccionado
                if ($val->tieneLote === '1' && empty($val->idLote)) {
                    $this->db->transRollback();
                    return $this->responseSetJSON("warning", "El producto {$val->name} tiene control de lotes, seleccione uno por favor ");
                }

                //Validamos que sea una cantidad valida
                if ($val->qty <= 0) {
                    $this->db->transRollback();
                    return $this->responseSetJSON("warning", "No pueden haber productos con cantidad menor o igual a 0<br> para el producto {$val->name}");
                }

                //Validadmos que sea un precio valido
                if ($val->price <= 0) {
                    $this->db->transRollback();
                    return $this->responseSetJSON("warning", "No pueden haber productos con precio menor o igual a 0<br> para el producto {$val->name}");
                }

                // Validar stock disponible considerando reservas, para la nueva cantidad
                // Validar stock disponible (stock real - reservas)
                $idLote = ($val->tieneLote === '1') ? $val->idLote : null;
                $validarStock = $this->stockBodLib->validarStockDisponible($val->id, $dataPostTrb->trbBodegaOrigen, $val->qty, null, null, $idLote);

                if ($validarStock['status'] !== 'success') {
                    $this->db->transRollback();
                    return $this->responseSetJSON($validarStock['status'], $validarStock['msg']);
                }

                $ajusteDetId = $this->transfLib->saveTransferenciaDetalle($transferenciaId, $val, $idLote);
                if (!$ajusteDetId) {
                    $this->db->transRollback();
                    return $this->responseSetJSON("error", "Ha ocurrido un error al registrar el producto { $val->name} en el detalle de la transferencia");
                }
            }

            // Manejo de reservas según estado (EN ESTADO BORRADOR Y POR CONFIRMAR GENERAMOS RESERVAS)
            if (($dataPostTrb->trbEstado === '1' || $dataPostTrb->trbEstado === '2' ) && $val->servicio === '0') {
                $reservas = $this->transfLib->registrarReservas($transferenciaId, $cartData, $dataPostTrb);
                if ($reservas['status'] !== 'success') {
                    $this->db->transRollback();
                    return $this->responseSetJSON($reservas['status'], $reservas['msg']);
                }
            }

            $secuencial = $this->ccm->getValueWhere('cc_transferencia_bodega', ['id' => $transferenciaId], 'trb_secuencial');

            $this->db->transCommit();
            $this->transferenciaCart->destroy();
            $this->logs->logSuccess('Transferencia de productos actualizada exitosamente ID: ' . $transferenciaId);
            log_message('info', "[Transferencia de productos] Transferencia actualizada exitosamente, DocID: {$transferenciaId}");

            $dataResponse = ['id' => $transferenciaId, 'trb_secuencial' => $secuencial];

            if ($dataPostTrb->trbEstado === '2') {
                return $this->responseSetJSON(
                                "success",
                                "<h5>TRansferencia #" . $secuencial . " actualizada exitosamente</h5>",
                                $dataResponse
                );
            } else {
                return $this->responseSetJSON(
                                "success",
                                "<span class='text-warning'>Transferencia #" . $secuencial . " actualizada exitosamente<br>REGISTRADO COMO BORRADOR<br></span>",
                                $dataResponse
                );
            }
        } catch (Exception $exc) {
            $this->db->transRollback();
            $this->logs->logError('Ha ocurrido un error al actualizar la transferencia de productos');
            return $this->responseSetJSON(
                            'error',
                            '<br>Error al tratar de actualizar la transferencia de productos <br> ' . $exc->getMessage() . $exc->getTraceAsString()
            );
        }
    }

    public function loadTransferenciaEdit($transferenciaId) {
        $respuesta = $this->loadDataTransferenciaCart($transferenciaId);

        return $this->response->setJSON([
                    'status' => $respuesta['status'] === 'success' ? 'success' : 'error',
                    'msg' => $respuesta['status'] === 'success' ? 'ok' : $respuesta['msg'],
                    'redirect' => site_url('transferencias/indexEdit/' . $transferenciaId)
        ]);
    }

    public function loadDataTransferenciaCart($ajusteId, $isClone = false) {
        $this->transferenciaCart->destroy();

        $dataTransferencia = $this->transferModel->getDataDetalle($ajusteId);
        $idBodega = $dataTransferencia->id_bodega_origen;

        // Solo se puede editar si está en borrador
        if ($isClone === false && $dataTransferencia->trb_estado !== '1') {
            return ['status' => 'error', 'msg' => 'Esta transferencia ya se encuentra confirmada o anulado previamente'];
        }

        foreach ($dataTransferencia->detalle as $valDet) {
            $dataProducto = $this->searchModel->searchProductoData($valDet->fk_producto);
            $idProd = $valDet->fk_producto;

            $dataStockBodega = $this->ccm->getData('cc_stock_bodega', ['fk_producto' => $idProd, 'fk_bodega' => $idBodega], 'stb_stock', null, 1);
            $stockBodega = $dataStockBodega ? $dataStockBodega->stb_stock : 0;

            //Obtenemos los lotes con su stock en caso de que el producto maneje lotes 
            $lotesStock = [];
            if ($dataProducto->prod_ctrllote === '1') {
                $lotesStock = $this->lotesStkModel->getLotesStock($idProd, $idBodega);

                foreach ($lotesStock as $val) {
                    if ($isClone) {
                        $reservas = $this->reservasLib->getReservasProductoLote($idProd, $idBodega, $val->fk_lote);
                    } else {
                        $reservas = $this->reservasLib->getReservasProductoLote($idProd, $idBodega, $val->fk_lote, $this->transaccionCod, $ajusteId);
                    }
                    $val->stockLote = $val->stbl_stock - $reservas['reserva'];
                }
            }

            $impuestos = $this->prodModel->getImpuestoTarifa($valDet->fk_producto);
            $tarifaIva = isset($impuestos[0]->impt_porcentage) ? $impuestos[0]->impt_porcentage : 0;
            $tarifaIce = isset($impuestos[1]->impt_porcentage) ? $impuestos[1]->impt_porcentage : 0;

            $item = [
                "id" => (int) $dataProducto->id,
                "qty" => (float) $valDet->trbd_itemcantidad,
                "codigo" => $dataProducto->prod_codigo,
                "name" => $dataProducto->prod_nombre,
                "unidadMedida" => $dataProducto->um_nombre_corto,
                "price" => (float) $valDet->trbd_itemcosto,
                "stock" => number_format($dataProducto->prod_stockactual, 2),
                "stockBodega" => number_format($stockBodega, 2),
                "ivaPorcent" => $tarifaIva,
                "icePorcent" => $tarifaIce,
                "permitirDuplicados" => $dataTransferencia->trb_items_duplicados,
                "tieneLote" => $dataProducto->prod_ctrllote,
                "lotes" => $lotesStock,
                "idLote" => $valDet->fk_lote,
                "servicio" => $dataProducto->prod_isservicio,
                "idBodega" => $idBodega
            ];

            $this->transferenciaCart->insert($item);
        }

        return ['status' => 'success', 'msg' => ''];
    }

    public function validarCampos($data) {
        $campos = [
            'trbFecha' => 'Debe seleccionar una fecha',
            'trbBodegaOrigen' => 'No hay bodega de origen seleccionado',
            'trbBodegaDestino' => 'No hay bodega de destino seleccionado',
            'trbUsuarioDestino' => 'Debe seleccionar un usuario para que confirme la transferencia',
            'trbCentroCosto' => 'Debe seleccionar un centro de costos',
            'trbEstado' => 'Debe seleccionar un estado',
        ];

        foreach ($campos as $campo => $mensaje) {
            if (empty($data->$campo)) {
                return [
                    'status' => true,
                    'msg' => $mensaje
                ];
            }
        }

        if ($data->trbBodegaOrigen == $data->trbBodegaDestino) {
            return [
                'status' => true,
                'msg' => "La bodega de origen y destino no pueden ser la misma"
            ];
        }

        return ['status' => false];
    }

//    public function confirmarTransferencia() {
//        $this->user->validateSession();
//        $data = $this->request->getPost();
//
//        try {
//            $this->db->transBegin();
//
//            $trbId = $data['transferId'];
//
//            $transfer = $this->ccm->getData(
//                    'cc_transfer_bodega',
//                    ['id' => $trbId],
//                    '*',
//                    null,
//                    1
//            );
//
//            if (!$transfer || $transfer->trb_estado != 2) {
//                return $this->responseSetJSON(
//                                'warning',
//                                'La transferencia no está en estado POR CONFIRMAR'
//                );
//            }
//
//            $detalle = $this->ccm->getData(
//                    'cc_transfer_bodega_det',
//                    ['fk_transfer_bodega' => $trbId, 'trbd_estado' => 1]
//            );
//
//            if (!$detalle) {
//                return $this->responseSetJSON('warning', 'No existen productos');
//            }
//
//            // =============================
//            // CONSUMO DE RESERVAS + KARDEX
//            // =============================
//            foreach ($detalle as $item) {
//
//                // 1️⃣ Kardex SALIDA (origen)
//                $prodSalida = (object) [
//                            'id' => $item->fk_producto,
//                            'qty' => $item->trbd_itemcantidad,
//                            'price' => $item->trbd_itemcosto,
//                            'total' => $item->trbd_itemcostoxcantidad,
//                            'servicio' => 0,
//                            'tieneLote' => $item->fk_lote ? 1 : 0
//                ];
//
//                $this->salidasLib->updateKardexTransferencia(
//                        $trbId,
//                        $prodSalida,
//                        $item->fk_lote,
//                        $transfer->fk_bodega_origen
//                );
//
//                // 2️⃣ Kardex ENTRADA (destino)
//                $this->entradasLib->updateKardexTransferencia(
//                        $trbId,
//                        $prodSalida,
//                        $item->fk_lote,
//                        $transfer->fk_bodega_destino
//                );
//            }
//
//            // 🔁 Consumir reservas
//            $this->reservasLib->consumirReservasDocumento(
//                    'TRANSFERENCIA_BODEGA',
//                    $trbId
//            );
//
//            // ✅ Actualizar estado
//            $this->ccm->actualizar(
//                    'cc_transfer_bodega',
//                    [
//                        'trb_estado' => 3,
//                        'trb_fecha_confirmacion' => date('Y-m-d H:i:s'),
//                        'fk_user_confirma' => $this->user->id
//                    ],
//                    ['id' => $trbId]
//            );
//
//            $this->db->transCommit();
//
//            return $this->responseSetJSON(
//                            'success',
//                            'Transferencia confirmada correctamente'
//            );
//        } catch (\Throwable $e) {
//            $this->db->transRollback();
//            return $this->responseSetJSON('error', $e->getMessage());
//        }
//    }
//    public function anularTransferencia() {
//        $this->user->validateSession();
//        $data = $this->request->getPost();
//
//        try {
//            $trbId = $data['transferId'];
//            $motivo = $data['motivo'] ?? 'Anulación manual';
//
//            $this->db->transBegin();
//
//            $transfer = $this->ccm->getData(
//                    'cc_transfer_bodega',
//                    ['id' => $trbId],
//                    '*',
//                    null,
//                    1
//            );
//
//            if (!$transfer) {
//                return $this->responseSetJSON('error', 'Transferencia no encontrada');
//            }
//
//            if ($transfer->trb_estado == -1) {
//                return $this->responseSetJSON('warning', 'La transferencia ya está anulada');
//            }
//
//            // Si NO estaba confirmada → liberar reservas
//            if (in_array($transfer->trb_estado, [1, 2])) {
//                $this->reservasLib->liberarReservasDocumento(
//                        'TRANSFERENCIA_BODEGA',
//                        $trbId
//                );
//            }
//
//            // Si ya estaba CONFIRMADA → kardex inverso
//            if ($transfer->trb_estado == 3) {
//
//                $detalle = $this->ccm->getData(
//                        'cc_transfer_bodega_det',
//                        ['fk_transfer_bodega' => $trbId, 'trbd_estado' => 1]
//                );
//
//                foreach ($detalle as $item) {
//
//                    $prod = (object) [
//                                'id' => $item->fk_producto,
//                                'qty' => abs($item->trbd_itemcantidad),
//                                'price' => $item->trbd_itemcosto,
//                                'total' => abs($item->trbd_itemcostoxcantidad),
//                                'servicio' => 0,
//                                'tieneLote' => $item->fk_lote ? 1 : 0
//                    ];
//
//                    // Reverso ENTRADA-SALIDA
//                    $this->salidasLib->updateKardexTransferencia(
//                            $trbId,
//                            $prod,
//                            $item->fk_lote,
//                            $transfer->fk_bodega_destino
//                    );
//
//                    $this->entradasLib->updateKardexTransferencia(
//                            $trbId,
//                            $prod,
//                            $item->fk_lote,
//                            $transfer->fk_bodega_origen
//                    );
//                }
//            }
//
//            $this->ccm->actualizar(
//                    'cc_transfer_bodega',
//                    [
//                        'trb_estado' => -1,
//                        'trb_motivo_anulacion' => $motivo,
//                        'trb_fecha_anulacion' => date('Y-m-d H:i:s'),
//                        'fk_user_anula' => $this->user->id
//                    ],
//                    ['id' => $trbId]
//            );
//
//            $this->db->transCommit();
//
//            return $this->responseSetJSON(
//                            'success',
//                            'Transferencia anulada exitosamente'
//            );
//        } catch (\Throwable $e) {
//            $this->db->transRollback();
//            return $this->responseSetJSON('error', $e->getMessage());
//        }
//    }
    public function changeBodega($bodegaId) {
        $this->session->set('bodegaIdTrb', $bodegaId);
        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => 'Bodega seleccionada correctamente',
                    'bodegaId' => $bodegaId
        ]);
    }

    public function deleteProduct($rowId) {
        $this->transferenciaCart->removeItem($rowId);
    }

    public function cancelarTransferencia() {
        $this->transferenciaCart->destroy();
    }

    public function loadUsersConfirm($bodegaId) {
        $response = $this->uersBodModeldel->getUsuariosByBodega($bodegaId);
        return $this->response->setJSON([
                    'status' => $response ? 'success' : 'warning',
                    'msg' => $response ? '' : 'No se han encontrado usuarios en la bodega de destino seleccionada',
                    'data' => $response
        ]);
    }
}
