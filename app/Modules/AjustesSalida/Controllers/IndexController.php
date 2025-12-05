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
 * @date 27 nov 2025
 * @time 10:21:19 a.m.
 */

namespace Modules\AjustesSalida\Controllers;

use Modules\Comun\Models\ProductoModel;
use Modules\Comun\Models\SearchsModel;
use Modules\Comun\Models\LotesStockModel;
use Modules\Comun\Libraries\StockBodegaLib;
use Modules\Comun\Libraries\ReservasLib;
use Modules\AjustesSalida\Libraries\SalidasCartLib;
use Modules\AjustesSalida\Libraries\SalidasLib;
use Modules\AjustesSalida\Libraries\SalidasAsientosLib;
use Modules\AjustesSalida\Models\SalidasModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class IndexController extends \App\Controllers\BaseController {

    //put your code here
    protected $dirViewModule;
    protected $salidasModel;
    protected $salidasLib;
    protected $salidasAsientoLib;
    protected $prodModel;
    protected $ajesCart;
    protected $searchModel;
    protected $stockBodLib;
    protected $lotesStkModel;
    protected $reservasLib;

    public function __construct() {

        $this->dirViewModule = 'Modules\AjustesSalida\Views';

        //IMPORTACION DE MODELOS
        $this->salidasModel = new SalidasModel();
        $this->prodModel = new ProductoModel();
        $this->searchModel = new SearchsModel();
        $this->lotesStkModel = new LotesStockModel();

        //IMPORTACION DE LIBRERIAS
        $this->ajesCart = new SalidasCartLib();
        $this->stockBodLib = new StockBodegaLib();
        $this->salidasLib = new SalidasLib();
        $this->salidasAsientoLib = new SalidasAsientosLib();
        $this->reservasLib = new ReservasLib();
    }

    public function index() {
        $view = $this->parametrosIndex();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($view);
        } else {
            return view($this->dirTemplate . '\dashboard', $view);
        }
    }

    public function indexEdit($ajusteId) {
        $view = $this->parametrosIndex($ajusteId);
        return view($this->dirTemplate . '\dashboard', $view);
    }

    public function parametrosIndex($ajusteId = null) {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);

        // Para salidas no usamos sustento ni proveedor (normalmente)
        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaMotivos'] = $this->ccm->getData('cc_motivos_ajuste', ['mot_estado' => 1, 'mot_tipo !=' => 'AJUSTES'], 'id, mot_nombre, CONCAT(mot_nombre, " ( ", mot_tipo," )") motivo');
        $data['listaCentroCostos'] = $this->ccm->getData('cc_centroscosto', ['cc_estado' => 1], 'id, cc_nombre');
        $data['listaServicios'] = $this->ccm->getData('cc_servicios', ['serv_estado' => 1], 'id, serv_nombre');

        $bodegaMainUsuario = $this->ccm->getValue('cc_bodegas', $this->user->id, 'id', 'id');

        // Usamos una clave de sesión independiente a la de entradas
        $data['bodegaId'] = $this->session->get('bodegaIdAjs') ? $this->session->get('bodegaIdAjs') : $bodegaMainUsuario;

        $data['permitirDuplicados'] = getSettings('PERMITIR_ITEMS_DUPLICADOS');
        $data['dataAjuste'] = null;
        $data['dataCliente'] = null;

        if (!empty($ajusteId)) {
            $data['dataAjuste'] = $this->ccm->getData('cc_ajuste_salida', ['id' => $ajusteId], '*', null, 1);
            $data['dataCliente'] = $this->searchModel->searchClientesById($data['dataAjuste']->fk_cliente);
        }

        $send['view'] = view($this->dirViewModule . '\viewNewAjuste', $data);

        return $send;
    }

    // Helper respuesta JSON estándar
    public function responseSetJSON($status, $mensaje, $data = null) {
        return $this->response->setJSON([
                    'status' => $status,
                    'msg' => $mensaje,
                    'data' => $data,
        ]);
    }

    /**
     * Carga un ajuste de SALIDA al cart para edición
     */
    public function loadAjusteEdit($ajusteId) {
        $respuesta = $this->loadDataAjusteCart($ajusteId);

        return $this->response->setJSON([
                    'status' => $respuesta['status'] === 'success' ? 'success' : 'error',
                    'msg' => $respuesta['status'] === 'success' ? 'ok' : $respuesta['msg'],
                    'redirect' => site_url('ajustessalida/indexEdit/' . $ajusteId)
        ]);
    }

    public function loadDataAjusteCart($ajusteId, $isClone = false) {
        $this->ajesCart->destroy();

        $dataAjuste = $this->salidasModel->getDataDetalle($ajusteId);
        $idBodega = $dataAjuste->id_bodega;
        // Solo se puede editar/ clonar si está en borrador
        if ($isClone === false && $dataAjuste->ajes_estado !== '1') {
            return ['status' => 'error', 'msg' => 'Este ajuste de salida ya se encuentra archivado o anulado previamente'];
        }

        foreach ($dataAjuste->detalle as $valDet) {
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
                        $reservas = $this->reservasLib->getReservasProductoLote($idProd, $idBodega, $val->fk_lote, '38', $ajusteId);
                    }
                    $val->stockLote = $val->stbl_stock - $reservas['reserva'];
                }
            }

            $impuestos = $this->prodModel->getImpuestoTarifa($valDet->fk_producto);
            $tarifaIva = isset($impuestos[0]->impt_porcentage) ? $impuestos[0]->impt_porcentage : 0;
            $tarifaIce = isset($impuestos[1]->impt_porcentage) ? $impuestos[1]->impt_porcentage : 0;

            $item = [
                "id" => (int) $dataProducto->id,
                "qty" => (float) $valDet->ajsd_itemcantidad,
                "codigo" => $dataProducto->prod_codigo,
                "name" => $dataProducto->prod_nombre,
                "unidadMedida" => $dataProducto->um_nombre_corto,
                "price" => (float) $valDet->ajsd_itemcosto,
                "stock" => number_format($dataProducto->prod_stockactual, 2),
                "stockBodega" => number_format($stockBodega, 2),
                "ivaPorcent" => $tarifaIva,
                "icePorcent" => $tarifaIce,
                "permitirDuplicados" => $dataAjuste->ajes_items_duplicados,
                "tieneLote" => $dataProducto->prod_ctrllote,
                "lotes" => $lotesStock,
                "idLote" => $valDet->fk_lote,
                "servicio" => $dataProducto->prod_isservicio,
                "idBodega" => $idBodega
            ];

            $this->ajesCart->insert($item);
        }

        return ['status' => 'success', 'msg' => ''];
    }

    /**
     * Insertar producto al cart de SALIDA
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
        $validarStock = $this->stockBodLib->validarStockDisponible($idProd, $idBodega, $cantidad);
        if ($validarStock['status'] !== 'success') {
            return $this->responseSetJSON($validarStock['status'], $validarStock['msg']);
        }


        //Obtenemos los lotes con su stock en caso de que el producto maneje lotes 
        $lotesStock = [];
        if ($dataProducto->prod_ctrllote === '1') {
            $lotesStock = $this->lotesStkModel->getLotesStock($idProd, $idBodega);
            foreach ($lotesStock as $val) {
                $reservas = $this->reservasLib->getReservasProductoLote($idProd, $idBodega, $val->fk_lote);
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

        $this->ajesCart->insert($item);

        return $this->responseSetJSON("success", "Producto agregado al carrito");
    }

    public function updateProduct() {
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
        $idAjuste = empty($dataPost->ajusteId) ? null : $dataPost->ajusteId; //Cuando el updateProduct se ejecutado desde una actualización de una salida en borrrador hacemos uso del ID del ajuste
        $idLote = ($dataPost->tieneLote === '1') ? $dataPost->idLote : null;
        $validarStock = $this->stockBodLib->validarStockDisponible($idProd, $idBodega, $cantidad, '38', $idAjuste, $idLote);

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
        $this->ajesCart->update($item, $rowidRand);

        return $this->responseSetJSON('success', 'Producto actualizado');
    }

    public function showDetailCart($key = 0) {
        $cartContent = $this->ajesCart->getContent();
        $dataCart['cartContent'] = $cartContent ? array_reverse($cartContent) : null;
        $dataCart['totalArticles'] = $this->ajesCart->totalArticles();
        $dataCart['totalItems'] = $cartContent ? count($cartContent) : null;
        $dataCart['totalCart'] = $this->ajesCart->totalCart();
        $dataCart['totalIva'] = $this->ajesCart->totalIva();
        $dataCart['totalBienes'] = $this->ajesCart->totalBienes();
        $dataCart['totalServicios'] = $this->ajesCart->totalServicios();
        $dataCart['totalCartIva'] = $this->ajesCart->totalCartIva();
        $dataCart['tarifCero'] = $this->ajesCart->tarifCero();
        $dataCart['tarifIva'] = $this->ajesCart->tarifIva();
        $dataCart['tarifCeroNeto'] = $this->ajesCart->tarifCeroNeto();
        $dataCart['tarifIvaNeto'] = $this->ajesCart->tarifIvaNeto();

        if ($key === 0) {
            return $this->response->setJSON($dataCart);
        } else {
            return json_decode(json_encode($dataCart));
        }
    }

    public function deleteProduct($rowId) {
        $this->ajesCart->removeItem($rowId);
    }

    public function cancelarAjuste() {
        $this->ajesCart->destroy();
    }

    public function changeBodega($bodegaId) {
        $this->session->set('bodegaIdAjs', $bodegaId);
        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => 'Bodega seleccionada correctamente',
                    'bodegaId' => $bodegaId
        ]);
    }

    public function updateAjuste() {
        $dataPostAjuste = json_decode(json_encode($this->request->getPost()));

        // Validar campos
        $statusValidation = $this->validarCampos($dataPostAjuste);
        if ($statusValidation['status']) {
            return $this->responseSetJSON("warning", $statusValidation['msg']);
        }

        $ajusteId = $dataPostAjuste->ajusteId;

        try {
            $cartData = $this->showDetailCart(1);

            $this->db->transBegin();

            // Actualizar cabecera
            $update = $this->salidasLib->updateAjuste($cartData, $dataPostAjuste, $ajusteId);
            if (!$update) {
                $this->db->transRollback();
                return $this->responseSetJSON('error', 'Ha ocurrido un error al actualizar el Ajuste de Salida');
            }

            // Eliminamos detalle anterior
            $this->ccm->eliminar('cc_ajuste_salida_det', ['fk_ajuste_salida' => $ajusteId]);

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
                $validarStock = $this->stockBodLib->validarStockDisponible($val->id, $dataPostAjuste->ajesBodega, $val->qty, '38', $ajusteId, $idLote);
                if ($validarStock['status'] !== 'success') {
                    $this->db->transRollback();
                    return $this->responseSetJSON($validarStock['status'], $validarStock['msg']);
                }

                $ajusteDetId = $this->salidasLib->saveAjusteDetalle($ajusteId, $val, $idLote);
                if (!$ajusteDetId) {
                    $this->db->transRollback();
                    return $this->responseSetJSON('error', 'Ha ocurrido un error al registrar el producto ' . $val->name . ' en el detalle del ajuste');
                }

                // Si el ajuste está ARCHIVADO (2) y no es servicio => descontamos stock
                if ($dataPostAjuste->ajesEstado === '2' && $val->servicio === '0') {
                    $kardexOk = $this->salidasLib->updateKardex($ajusteId, $val, $idLote, $dataPostAjuste);
                    if ($kardexOk['status'] !== 'success') {
                        $this->db->transRollback();
                        return $this->responseSetJSON($kardexOk['status'], $kardexOk['msg']);
                    }
                }
            }

            // Manejo de reservas según estado
            if ($dataPostAjuste->ajesEstado === '1' && $val->servicio === '0') {
                $reservas = $this->salidasLib->registrarReservas($ajusteId, $cartData, $dataPostAjuste);
                if ($reservas['status'] !== 'success') {
                    $this->db->transRollback();
                    return $this->responseSetJSON($reservas['status'], $reservas['msg']);
                }
            } elseif ($dataPostAjuste->ajesEstado === '2') {
                // Si pasa a archivado, eliminamos reservas (si existían)
                $this->reservasLib->eliminarReservas($ajusteId, '38');

                // Generamos asiento si aplica
                $responseAsiento = $this->salidasAsientoLib->generarAsiento($ajusteId);
                if ($responseAsiento['status'] !== 'success') {
                    $this->db->transRollback();
                    return $this->responseSetJSON($responseAsiento['status'], $responseAsiento['msg']);
                }
            }

            $secuencial = $this->ccm->getValueWhere('cc_ajuste_salida', ['id' => $ajusteId], 'ajes_secuencial');

            $this->db->transCommit();
            $this->ajesCart->destroy();

            $this->logs->logSuccess('Ajuste de Salida Actualizado exitosamente ID: ' . $ajusteId);
            log_message('info', "[Ajuste Salida] Ajuste actualizado exitosamente, DocID: {$ajusteId}");

            $dataResponse = ['id' => $ajusteId, 'ajes_secuencial' => $secuencial];

            if ($dataPostAjuste->ajesEstado === '2') {
                return $this->responseSetJSON(
                                "success",
                                "<h5>Ajuste de Salida #" . $secuencial . " registrado exitosamente</h5>",
                                $dataResponse
                );
            } else {
                return $this->responseSetJSON(
                                "success",
                                "<span class='text-warning'>Ajuste de Salida #" . $secuencial . " registrado exitosamente<br>REGISTRADO COMO BORRADOR<br></span>",
                                $dataResponse
                );
            }
        } catch (\Throwable $exc) {
            $this->db->transRollback();
            $this->logs->logError('Ha ocurrido un error al actualizar el Ajuste de Salida');
            return $this->responseSetJSON(
                            'error',
                            '<br>Error al tratar de actualizar el Ajuste de Salida <br> ' . $exc->getMessage()
            );
        }
    }

    public function saveAjuste() {
        $dataPostAjuste = json_decode(json_encode($this->request->getPost()));

        // Obtener índice (periodo contable)
        $periodoContable = getPeriodoContable($dataPostAjuste->ajesFecha);
        if (!$periodoContable) {
            return $this->responseSetJSON(
                            "error",
                            '<h5>Revise el periodo de cierre</h5><br> <h6>Al parecer no se ha encontrado un periodo contable habil para la fecha dada</h6>'
            );
        }

        // Validar campos
        $statusValidation = $this->validarCampos($dataPostAjuste);
        if ($statusValidation['status']) {
            return $this->responseSetJSON("warning", $statusValidation['msg']);
        }

        try {
            $cartData = $this->showDetailCart(1);

            $this->db->transBegin();

            $ajusteId = $this->salidasLib->saveAjuste($cartData, $dataPostAjuste);
            if (!$ajusteId) {
                $this->db->transRollback();
                return $this->responseSetJSON("error", 'Ha ocurrido un error al registrar el Ajuste de Salida');
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
                $validarStock = $this->stockBodLib->validarStockDisponible($val->id, $dataPostAjuste->ajesBodega, $val->qty, null, null, $idLote);

                if ($validarStock['status'] !== 'success') {
                    $this->db->transRollback();
                    return $this->responseSetJSON($validarStock['status'], $validarStock['msg']);
                }


                $ajusteDetId = $this->salidasLib->saveAjusteDetalle($ajusteId, $val, $idLote);
                if (!$ajusteDetId) {
                    $this->db->transRollback();
                    return $this->responseSetJSON("error", "Ha ocurrido un error al registrar el producto { $val->name} en el detalle del ajuste");
                }

                // Si va directo a ARCHIVADO (2) y no es servicio => descontamos stock
                if ($dataPostAjuste->ajesEstado === '2' && $val->servicio === '0') {
                    $kardexOk = $this->salidasLib->updateKardex($ajusteId, $val, $idLote, $dataPostAjuste);
                    if ($kardexOk['status'] !== 'success') {
                        $this->db->transRollback();
                        return $this->responseSetJSON($kardexOk['status'], $kardexOk['msg']);
                    }
                }
            }

            // Manejo de reservas según estado
            if ($dataPostAjuste->ajesEstado === '1' && $val->servicio === '0') {
                $reservas = $this->salidasLib->registrarReservas($ajusteId, $cartData, $dataPostAjuste);
                if ($reservas['status'] !== 'success') {
                    $this->db->transRollback();
                    return $this->responseSetJSON($reservas['status'], $reservas['msg']);
                }
            } elseif ($dataPostAjuste->ajesEstado === '2') {
                // Generar asiento contable
                $responseAsiento = $this->salidasAsientoLib->generarAsiento($ajusteId);
                if ($responseAsiento['status'] !== 'success') {
                    $this->db->transRollback();
                    return $this->responseSetJSON($responseAsiento['status'], $responseAsiento['msg']);
                }
            }

            $secuencial = $this->ccm->getValueWhere('cc_ajuste_salida', ['id' => $ajusteId], 'ajes_secuencial');

            $this->db->transCommit();
            $this->ajesCart->destroy();
            $this->logs->logSuccess('Ajuste de Salida registrado exitosamente ID: ' . $ajusteId);
            log_message('info', "[Ajuste Salida] Ajuste registrado exitosamente, DocID: {$ajusteId}");

            $dataResponse = ['id' => $ajusteId, 'ajes_secuencial' => $secuencial];

            if ($dataPostAjuste->ajesEstado === '2') {
                return $this->responseSetJSON(
                                "success",
                                "<h5>Ajuste de Salida #" . $secuencial . " registrado exitosamente</h5>",
                                $dataResponse
                );
            } else {
                return $this->responseSetJSON(
                                "success",
                                "<span class='text-warning'>Ajuste de Salida #" . $secuencial . " registrado exitosamente<br>REGISTRADO COMO BORRADOR<br></span>",
                                $dataResponse
                );
            }
        } catch (\Throwable $exc) {
            $this->db->transRollback();
            $this->logs->logError('Ha ocurrido un error al registrar el Ajuste de Salida');
            return $this->responseSetJSON(
                            'error',
                            '<br>Error al tratar de crear el Ajuste de Salida <br> ' . $exc->getMessage() . $exc->getTraceAsString()
            );
        }
    }

    public function validarCampos($data) {
        $campos = [
            'ajesFecha' => 'Debe seleccionar una fecha',
            'ajesBodega' => 'Debe seleccionar una bodega',
            'ajesCentrocosto' => 'Debe seleccionar un centro de costos',
            'ajesMotivo' => 'Debe seleccionar un motivo de ajuste',
            'ajesEstado' => 'Debe seleccionar un estado',
            'ajesServicio' => 'Debe seleccionar un servicio',
            'ajesTipo' => 'Debe seleccionar un tipo de ajuste',
        ];

        foreach ($campos as $campo => $mensaje) {
            if (empty($data->$campo)) {
                return [
                    'status' => true,
                    'msg' => $mensaje
                ];
            }
        }

        return ['status' => false];
    }

    public function anularAjuste() {
        $this->user->validateSession();

        $data = json_decode(file_get_contents('php://input'));
        $ajusteId = $data->ajusteId;
        $motivoAnulacion = $data->motivoAnulacion;

        try {
            if (empty($ajusteId)) {
                return $this->responseSetJSON('warning', 'ID de ajuste inválido');
            }
            if (empty($motivoAnulacion)) {
                return $this->responseSetJSON('warning', 'Debe especificar un motivo de anulación');
            }

            $this->db->transBegin();

            $response = $this->salidasLib->anularAjuste($ajusteId, $motivoAnulacion);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
            }
            if ($response['status'] !== 'success') {
                $this->db->transRollback();
                return $this->responseSetJSON($response['status'], $response['msg']);
            }

            $this->db->transCommit();
            $this->logs->logSuccess("[Ajuste Salida] Anulado exitosamente ID: {$ajusteId}");
            log_message('info', "[Ajuste Salida] Anulado exitosamente ID:{$ajusteId}");
            return $this->responseSetJSON('success', $response['msg']);
        } catch (\Throwable $exc) {
            $this->logs->logError('Excepción al anular ajuste de salida: ' . $exc->getMessage());
            return $this->responseSetJSON('error', 'Error interno: ' . $exc->getMessage() . $exc->getTraceAsString());
        }
    }

    public function clonarAjuste($ajusteId) {
        $respuesta = $this->loadDataAjusteCart($ajusteId, true);

        return $this->response->setJSON([
                    'status' => $respuesta['status'] === 'success' ? 'success' : 'error',
                    'redirect' => site_url('ajustessalida/nuevoAjuste')
        ]);
    }

    public function importarExcel() {
        try {
            $file = $this->request->getFile('file');
            $bodegaId = $this->request->getPost('bodegaId');
            $permitirDuplicados = $this->request->getPost('permitirDuplicados');

            if (!$file || !$file->isValid()) {
                return $this->responseSetJSON('error', 'Debe seleccionar un archivo Excel válido.');
            }

            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();
            $registros = $sheet->toArray(null, true, true, true); //(valor de celdas vacias, calcular formulas, formato date, returnCellRef), el cuarto parametro es true para que las columas sean manejadas mediante su letra, si fuera false serian manejadas mediante sus indices 0,1,2,etc

            $importados = 0;
            $errores = [];
            $fila = 1;

            foreach ($registros as $i => $row) {
                $fila++;
                if ($i === 1) {
                    continue; // cabecera
                }

                $codigo = trim($row['A'] ?? '');
                $cantidad = (float) ($row['B'] ?? 0);
                $lote = trim($row['C'] ?? '');

                if (empty($codigo)) {
                    $errores[] = "Fila {$i}: el código está vacío.";
                    continue;
                }
                if ($cantidad <= 0) {
                    $errores[] = "Fila {$i}: la cantidad debe ser mayor a cero.";
                    continue;
                }

                $idProd = $this->ccm->getValueWhere('cc_productos', ['prod_codigo' => $codigo, 'prod_estado' => 1], 'id');
                if (!$idProd) {
                    $errores[] = "Fila {$i}: el producto con código '{$codigo}' no existe o está desactivado.";
                    continue;
                }

                $producto = $this->searchModel->searchProductoData($idProd);

                // Validar stock disponible (stock - reservas)
                $validarStock = $this->stockBodLib->validarStockDisponible($producto->id, $bodegaId, $cantidad);
                if ($validarStock['status'] !== 'success') {
                    $errores[] = "Fila {$i}: {$validarStock['msg']}";
                    continue;
                }


//                VALIDAR LOTE DE PRODUCTO QUE VIENE EN EXCL ... continuara...
//                //Obtenemos los lotes con su stock en caso de que el producto maneje lotes 
                $lotesStock = [];
                if ($producto->prod_ctrllote === '1') {

                    $existeLote = $this->ccm->getValueWhere('cc_lotes', ['lot_lote' => $lote, 'fk_producto' => $idProd], 'id');
                    if (!$existeLote) {
                        $errores[] = "Fila {$i}: No existe el lote {$lote} para el productod código {$codigo}";
                        continue;
                    }

                    $lotesStock = $this->lotesStkModel->getLotesStock($idProd, $bodegaId);
                    foreach ($lotesStock as $val) {
                        $reservas = $this->reservasLib->getReservasProductoLote($idProd, $bodegaId, $val->fk_lote);
                        $val->stockLote = $val->stbl_stock - $reservas['reserva'];
                    }
                }

                $dataStockBodega = $this->ccm->getData('cc_stock_bodega', ['fk_producto' => $producto->id, 'fk_bodega' => $bodegaId], 'stb_stock', null, 1);
                $stockBodega = $dataStockBodega ? $dataStockBodega->stb_stock : 0;

                $impuestos = $this->prodModel->getImpuestoTarifa($producto->id);
                $tarifaIva = isset($impuestos[0]->impt_porcentage) ? $impuestos[0]->impt_porcentage : 0;
                $tarifaIce = isset($impuestos[1]->impt_porcentage) ? $impuestos[1]->impt_porcentage : 0;

                $item = [
                    "id" => (int) $producto->id,
                    "qty" => $cantidad,
                    "codigo" => $producto->prod_codigo,
                    "name" => $producto->prod_nombre,
                    "unidadMedida" => $producto->um_nombre_corto,
                    "price" => (float) $producto->prod_costopromedio,
                    "stock" => number_format($producto->prod_stockactual, 2),
                    "stockBodega" => number_format($stockBodega, 2),
                    "ivaPorcent" => $tarifaIva,
                    "icePorcent" => $tarifaIce,
                    "permitirDuplicados" => $permitirDuplicados,
                    "tieneLote" => $producto->prod_ctrllote,
                    "lotes" => $lotesStock,
                    "idLote" => isset($existeLote) ? $existeLote : null,
                    "servicio" => $producto->prod_isservicio,
                    "idBodega" => $bodegaId
                ];

                $this->ajesCart->insert($item);
                $importados++;
            }

            if ($importados === 0) {
                $msg = 'No se importaron productos válidos.';
                $msg .= "<span class='fw-semibold text-danger'><br><br><strong>Errores encontrados:</strong><br>" . implode('<br>', $errores) . '</span>';
                return $this->responseSetJSON('warning', $msg);
            }

            $msg = "Importación completada: {$importados} producto(s) agregado(s).";
            if (!empty($errores)) {
                $msg .= "<span class='fw-semibold text-danger'><br><br><strong>Errores encontrados:</strong><br>" . implode('<br>', $errores) . '</span>';
            }

            $dataResponse = [
                'totalImportados' => $importados,
                'errores' => $errores
            ];
            return $this->responseSetJSON('success', $msg, $dataResponse);
        } catch (\Throwable $exec) {
            return $this->responseSetJSON('error', 'Error al procesar el archivo: ' . $exec->getMessage() . $exec->getTraceAsString());
        }
    }
}
