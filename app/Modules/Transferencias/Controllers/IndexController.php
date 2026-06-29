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
use PhpOffice\PhpSpreadsheet\IOFactory;

class IndexController extends \App\Controllers\BaseController {

    //put your code here

    protected $transaccionCod = '17';
    protected string $dirViewModule;
    protected TransferenciasCartLib $transferenciaCart;
    protected BodegasUserModel $uersBodModel;
    protected SearchsModel $searchModel;
    protected StockBodegaLib $stockBodLib;
    protected LotesStockModel $lotesStkModel;
    protected ReservasLib $reservasLib;
    protected ProductoModel $prodModel;
    protected TransferenciasLib $transfLib;
    protected TransferenciasModel $transferModel;

    public function __construct() {
        $this->dirViewModule = 'Modules\Transferencias\Views';

        //IMPORTACION DE MODELOS
        $this->uersBodModel = new BodegasUserModel();
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

    /**
     * @param int $transferenciaId Identificador único del documento
    */
    public function indexEdit(int $transferenciaId) {
        $view = $this->parametrosIndex($transferenciaId);
        return view($this->dirTemplate . '\dashboard', $view);
    }

    /**
     * @param int|null $transferenciaId Identificador único de la transferencia
     */
    public function parametrosIndex(int $transferenciaId = null) {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);

        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaCentroCostos'] = $this->ccm->getData('cc_centroscosto', ['cc_estado' => 1], 'id, cc_nombre');

        $bodegaMainUsuario = bodegaMain($this->user->id);

        $data['bodegaId'] = $this->session->get('bodegaIdTrb') ? $this->session->get('bodegaIdTrb') : $bodegaMainUsuario;

        $data['permitirDuplicados'] = getSettings('PERMITIR_ITEMS_DUPLICADOS');
        $data['dataTransferencia'] = null;

        if (!empty($transferenciaId)) {
            $data['dataTransferencia'] = $this->ccm->getData('cc_transferencia_bodega', ['id' => $transferenciaId], '*', null, 1);
        }

        $send['view'] = view($this->dirViewModule . '\viewNewTransferencia', $data);

        return $send;
    }

    /**
     * @param string $status Estado de la respuesta (success, warning, error)
     * @param string $mensaje Mensaje descriptivo sobre el resultado de la operación
     * @param mixed|null $data Datos adicionales relacionados con la respuesta (opcional)
    */
    public function responseSetJSON(string $status, string $mensaje, mixed $data = null) {
        return $this->response->setJSON([
                    'status' => $status,
                    'msg' => $mensaje,
                    'data' => $data,
        ]);
    }

    /**
     * Insertar producto al cart de TRANSFERENCIAS
     * Aquí se debe validar stock disponible considerando reservas
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
        $stockBodega = $dataStockBodega ? $dataStockBodega->stb_stock : 0;
        $whereReserva = ['tb1.fk_producto' => $idProd, 'tb1.fk_bodega' => $idBodega, 'tb1.res_estado' => 'ACTIVA'];
        $whereNotReserva = null;
        if ($idTransferencia) {
            $whereNotReserva = "NOT (tb1.res_codigo_transaccion = {$this->transaccionCod} AND tb1.res_documento_id = {$idTransferencia})";
        }
        $rowReserva = $this->ccm->getData("cc_reserva_inventario tb1", $whereReserva, "COALESCE(SUM(tb1.res_cantidad),0) AS reservado", null, 1, null, $whereNotReserva);
        $stockBodegaDisponible = $stockBodega - $rowReserva->reservado;

        $item = [
            "id" => (int) $idProd,
            "qty" => (float) $cantidad,
            "codigo" => $dataPost->codigo,
            "name" => $dataPost->name,
            "unidadMedida" => $dataPost->unidadMedida,
            "price" => (float) $dataPost->price,
            "stock" => number_format($dataPost->stock, 2),
            "stockBodega" => number_format($stockBodegaDisponible, 2),
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
    

    /**
     * Mostrar detalle del cart de transferencias
     * Si se envía el parámetro $key con valor 1, la función devuelve un objeto con los datos del cart en lugar de una respuesta JSON. Esto es útil para reutilizar la lógica de obtención de datos del cart en otras funciones dentro del controlador, como al guardar la transferencia, sin necesidad de realizar una llamada AJAX adicional. 
     * @param int $key Parámetro opcional para determinar el formato de la respuesta (0 para JSON, 1 para objeto)   
    */
    public function showDetailCart(int $key = 0) {

        $cartContent = $this->transferenciaCart->getContent();
        $dataCart['cartContent'] = $cartContent ? array_reverse($cartContent) : null;
        $dataCart['totalArticles'] = $this->transferenciaCart->totalArticles();
        $dataCart['totalItems'] = $cartContent ? count($cartContent) : null;
        $dataCart['totalCart'] = $this->transferenciaCart->totalCart();
        $dataCart['totalIva'] = $this->transferenciaCart->totalIva();
        $dataCart['totalCartIva'] = $this->transferenciaCart->totalCartIva();

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
        } catch (\Exception $exc) {
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
                $validarStock = $this->stockBodLib->validarStockDisponible($val->id, $dataPostTrb->trbBodegaOrigen, $val->qty, $this->transaccionCod, $transferenciaId, $idLote);

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
        } catch (\Exception $exc) {
            $this->db->transRollback();
            $this->logs->logError('Ha ocurrido un error al actualizar la transferencia de productos');
            return $this->responseSetJSON(
                            'error',
                            '<br>Error al tratar de actualizar la transferencia de productos <br> ' . $exc->getMessage() . $exc->getTraceAsString()
            );
        }
    }


    /**
     * @param int $transferenciaId Identificador único de la transferencia a cargar en el cart para edición
     * Esta función se encarga de cargar los productos de una transferencia existente en el carrito de transferencias, permitiendo así la edición de la transferencia. Se realiza una validación para asegurar que la transferencia esté en estado borrador o rechazada antes de cargar los productos, ya que solo en
    */
    public function loadTransferenciaEdit(int $transferenciaId) {
        $respuesta = $this->loadDataTransferenciaCart($transferenciaId);

        return $this->response->setJSON([
                    'status' => $respuesta['status'] === 'success' ? 'success' : 'error',
                    'msg' => $respuesta['status'] === 'success' ? 'ok' : $respuesta['msg'],
                    'redirect' => site_url('transferencias/indexEdit/' . $transferenciaId)
        ]);
    }


    /**
     * @param int $transferenciaId Identificador único de la transferencia a cargar en el cart para clonación
     * Esta función se encarga de cargar los productos de una transferencia existente en el carrito de transferencias para crear una nueva transferencia basada en la existente. A diferencia de la función de edición, esta función no realiza la validación del estado de la transferencia, permitiendo cargar los productos independientemente del estado en que se encuentre la transferencia original. Esto es útil para casos donde se desea replicar una transferencia anterior sin importar su estado, facilitando la creación de transferencias similares de manera rápida.    
     * @param bool $isClone Parámetro que indica si la carga es para clonación (true) o edición (false), afectando las validaciones de estado de la transferencia. En clonación se omiten las validaciones de estado, mientras que en edición se requiere que la transferencia esté en estado borrador o rechazada. 
    */
    public function loadDataTransferenciaCart(int $transferenciaId, bool $isClone = false) {
        $this->transferenciaCart->destroy();

        $dataTransferencia = $this->transferModel->getDataDetalle($transferenciaId);
        $idBodega = $dataTransferencia->id_bodega_origen;

        // Solo se puede editar si está en borrador o esta rechazada
        if ($isClone === false && !in_array($dataTransferencia->trb_estado, ['0', '1'])) {
            return ['status' => 'error', 'msg' => 'Esta transferencia ya se encuentra confirmada o anulado previamente'];
        }

        foreach ($dataTransferencia->detalle as $valDet) {
            $dataProducto = $this->searchModel->searchProductoData($valDet->fk_producto);
            $idProd = $valDet->fk_producto;

            //Obtenemos el stock del producto
            $dataStockBodega = $this->ccm->getData('cc_stock_bodega', ['fk_producto' => $idProd, 'fk_bodega' => $idBodega], 'stb_stock', null, 1);
            $stockBodega = $dataStockBodega ? $dataStockBodega->stb_stock : 0;
            $whereReserva = ['tb1.fk_producto' => $idProd, 'tb1.fk_bodega' => $idBodega, 'tb1.res_estado' => 'ACTIVA'];
            $whereNotReserva = "NOT (tb1.res_codigo_transaccion = {$this->transaccionCod} AND tb1.res_documento_id = {$transferenciaId})";
            $rowReserva = $this->ccm->getData("cc_reserva_inventario tb1", $whereReserva, "COALESCE(SUM(tb1.res_cantidad),0) AS reservado", null, 1, null, $whereNotReserva);
            $stockBodegaDisponible = $stockBodega - $rowReserva->reservado;

            if ($stockBodegaDisponible <= 0) {
                return ['status' => 'error', 'msg' => "No se ha encontrado stock para el producto de código {$valDet->fk_producto}"];
            }

            //Obtenemos los lotes con su stock en caso de que el producto maneje lotes 
            $lotesStock = [];
            if ($dataProducto->prod_ctrllote === '1') {
                $lotesStock = $this->lotesStkModel->getLotesStock($idProd, $idBodega);

                foreach ($lotesStock as $val) {
                    if ($isClone) {
                        $reservas = $this->reservasLib->getReservasProductoLote($idProd, $idBodega, $val->fk_lote);
                    } else {
                        $reservas = $this->reservasLib->getReservasProductoLote($idProd, $idBodega, $val->fk_lote, $this->transaccionCod, $transferenciaId);
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
                "stockBodega" => number_format($stockBodegaDisponible, 2),
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

    /**
     * @param object $data Objeto que contiene los datos de la transferencia a validar
     * Esta función se encarga de validar los campos necesarios para registrar o actualizar una transferencia. Se verifica que los campos obligatorios estén presentes y que la bodega de origen y destino no sean
    */
    public function validarCampos(object $data) {
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


    /**
     * @param int $transferenciaId Identificador único de la transferencia a clonar en el cart
     * Esta función se encarga de cargar los productos de una transferencia existente en el carrito de transferencias para crear una nueva transferencia basada en la existente. A diferencia de la función de edición, esta función no realiza la validación del estado de la transferencia, permitiendo cargar los productos independientemente del estado en que se encuentre la transferencia original. Esto es útil para casos donde se desea replicar una transferencia anterior sin importar su estado, facilitando la creación de transferencias similares de manera rápida.
     * @return JSON Respuesta con el estado de la operación y la URL de redirección a la página de creación de transferencia con los datos cargados en el cart. El estado será 'success' si la carga se realizó correctamente, o 'error' si ocurrió algún problema durante la carga de los datos al cart. La URL de redirección llevará al usuario a la página de
    */
    public function clonarTransferencia(int $transferenciaId) {
        $respuesta = $this->loadDataTransferenciaCart($transferenciaId, true);

        return $this->response->setJSON([
                    'status' => $respuesta['status'] === 'success' ? 'success' : 'error',
                    'redirect' => site_url('transferencias/nuevaTransferencia')
        ]);
    }

    public function rechazarTransferencia() {

        $dataPost = json_decode(file_get_contents("php://input"));

        // ===============================
        // 1. Obtenemos la transferencia
        // ===============================
        $transfer = $this->ccm->getData('cc_transferencia_bodega', ['id' => $dataPost->transferenciaId], '*', null, 1);

        if (!$transfer) {
            return $this->responseSetJSON('error', 'Transferencia no encontrada');
        }

        // ===============================
        // 2. Validamos por estado
        // ===============================
        if ((int) $transfer->trb_estado === 0) {
            return $this->responseSetJSON('error', 'La Transferencia ya se encuentra rechazada');
        }
        if ((int) $transfer->trb_estado !== 2) {
            return $this->responseSetJSON('warning', 'Solo se pueden rechazar transferencias en estado POR CONFIRMAR');
        }

        try {
            //Liberamos reservas
            $this->reservasLib->liberarReservasDocumento($this->transaccionCod, $dataPost->transferenciaId);

            //Cambiamos de estado
            $dataUpdate = [
                'trb_estado' => 0, // RECHAZADA
                'trb_motivo_anulacion' => $dataPost->motivo,
            ];
            $this->ccm->actualizar('cc_transferencia_bodega', $dataUpdate, ['id' => $dataPost->transferenciaId]);
            return $this->responseSetJSON('success', '⚠️ Transferencia rechazada y enviada a corrección');
        } catch (\Exception $exc) {
            log_message('error', 'Error rechazarTransferencia: ' . $exc->getMessage());
            return $this->responseSetJSON('error', 'Error al rechazar la transferencia');
        }
    }

    /**
     * @param int $transferenciaId Identificador único de la transferencia a confirmar
     * Esta función se encarga de confirmar una transferencia de productos, realizando las siguientes acciones:
     * 1. Validar que la transferencia esté en estado "POR CONFIRMAR" antes de proceder con la confirmación.
     * 2. Validar que existan productos en el detalle de la transferencia para poder confirmar.
     * 3. Crear los movimientos de inventario correspondientes en el kardex para la bodega de origen (salida) y la bodega de destino (entrada), actualizando las cantidades y costos según corresponda.
     * 4. Eliminar las reservas de inventario asociadas a la transferencia, ya que al confirmar la transferencia los productos se consideran efectivamente transferidos y las reservas ya no son necesarias.
     * 5. Actualizar el estado de la transferencia a "CONFIRMADA", registrando la fecha de confirmación y el usuario que realizó la confirmación para mantener un registro histórico de las acciones realizadas sobre la transferencia. 
     * En caso de que la transferencia no esté en el estado correcto, no existan productos en el detalle, o ocurra algún error durante el proceso de confirmación, se devolverá una respuesta JSON con el estado correspondiente (warning o error) y un mensaje descriptivo del problema. Si la confirmación se realiza exitosamente, se devolverá una respuesta JSON con el estado "success" y un mensaje indicando que la transferencia ha sido confirmada correctamente, junto con la actualización de los movimientos de inventario en el kardex.   
     * 
    */
    public function confirmarTransferencia(int $transferenciaId) {
        $this->user->validateSession();

        try {
            $this->db->transBegin();

            $transfer = $this->ccm->getData('cc_transferencia_bodega', ['id' => $transferenciaId], '*', null, 1);

            if (!$transfer || $transfer->trb_estado != 2) {
                return $this->responseSetJSON('warning', 'La transferencia no está en estado POR CONFIRMAR');
            }

            $detalle = $this->ccm->getData('cc_transferencia_bodega_det', ['fk_transferencia_bodega' => $transferenciaId, 'trbd_estado' => 1]);

            if (!$detalle) {
                return $this->responseSetJSON('warning', 'No existen productos');
            }

            // =============================
            // CREACIÓN DE KARDEX
            // =============================
            foreach ($detalle as $item) {

                //Data KARDEX
                $prodSalida = (object) [
                            'id' => $item->fk_producto,
                            'qty' => $item->trbd_itemcantidad,
                            'price' => $item->trbd_itemcosto,
                            'total' => $item->trbd_itemcostoxcantidad,
                            'servicio' => 0,
                            'tieneLote' => $item->fk_lote ? 1 : 0
                ];

                // Kardex SALIDA (origen)
                $this->transfLib->updateKardex($transferenciaId, $prodSalida, $item->fk_lote, $transfer, true);

                // Kardex ENTRADA (destino)
                $this->transfLib->updateKardex($transferenciaId, $prodSalida, $item->fk_lote, $transfer, false);
            }

            // =============================
            // CONSUMO DE RESERVAS Y CAMBIO DE ESTADO
            // =============================
            $this->reservasLib->eliminarReservas($transferenciaId, $this->transaccionCod);

            // Actualizar estado
            $whereData = [
                'trb_estado' => 3,
                'trb_fecha_confirmacion' => date('Y-m-d H:i:s'),
                'fk_user_confirma' => $this->user->id
            ];
            $this->ccm->actualizar('cc_transferencia_bodega', $whereData, ['id' => $transferenciaId]);

            $this->db->transCommit();
            $this->logs->logSuccess('Transferencia confirmada exitosamente ID: ' . $transferenciaId);
            log_message('info', "[Transferencia de productos] Transferencia confirmada exitosamente, DocID: {$transferenciaId}");
            return $this->responseSetJSON('success', 'Transferencia confirmada exitosamente <br> Los productos de inventario se han actualizado ');
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return $this->responseSetJSON('error', $e->getMessage());
        }
    }

    public function anularTransferencia() {

        $this->user->validateSession();
        $dataPost = json_decode(file_get_contents("php://input"));

        try {
            $transferenciaId = $dataPost->transferenciaId;
            $motivoAnulacion = $dataPost->motivo;
            if (empty($transferenciaId)) {
                return $this->responseSetJSON('warning', 'ID de ajuste inválido');
            }

            if (empty($motivoAnulacion)) {
                return $this->responseSetJSON('warning', 'Debe especificar un motivo de anulación');
            }

            $this->db->transBegin();

            $response = $this->transfLib->anularTransferencia($transferenciaId, $motivoAnulacion);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return $this->responseSetJSON('error', "Error inesperado al anular la transferencia");
            }
            if ($response['status'] !== 'success') {
                $this->db->transRollback();
                return $this->responseSetJSON($response['status'], $response['msg']);
            }

            $this->db->transCommit();
            $this->logs->logSuccess('Transferencia anulada exitosamente ID: ' . $transferenciaId);
            log_message('info', "[Transferencia de productos] Transferencia anulada exitosamente, DocID: {$transferenciaId}");
            return $this->responseSetJSON('success', 'Transferencia anulada exitosamente');
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('info', "[Transferencia de productos] Error al anular Transferencia");
            return $this->responseSetJSON('error', $e->getMessage());
        }
    }

    /**
     * @param int $bodegaId Identificador único de la bodega a seleccionar para la transferencia
     * Esta función se encarga de cambiar la bodega seleccionada en la sesión para la transferencia de productos. Al recibir el identificador de la bodega, se actualiza la variable de sesión 'bodegaIdTrb' con el nuevo valor, lo que permite que las operaciones posteriores relacionadas con la transferencia utilicen la bodega seleccionada. La función devuelve una respuesta JSON indicando el estado de la operación, un mensaje de confirmación y el identificador de la bodega seleccionada. Esto es útil para mantener la bodega seleccionada de manera persistente durante el proceso de creación
     * o edición de una transferencia, asegurando que las acciones realizadas se asocien correctamente con la bodega elegida por el usuario.
    */
    public function changeBodega(int $bodegaId) {
        $this->session->set('bodegaIdTrb', $bodegaId);
        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => 'Bodega seleccionada correctamente',
                    'bodegaId' => $bodegaId
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


//                VALIDAR LOTE DE PRODUCTO QUE VIENE EN EXCEL 
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

                $this->transferenciaCart->insert($item);
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

    /**
     * @param string $rowId Identificador único del producto en el carrito de transferencias a eliminar
     * Esta función se encarga de eliminar un producto específico del carrito de transferencias utilizando su identificador único (rowId). Al recibir el rowId, se llama al método removeItem del carrito de transferencias para eliminar el producto correspondiente. Esto permite a los usuarios gestionar los productos incluidos en
    */
    public function deleteProduct(string $rowId) {
        $this->transferenciaCart->removeItem($rowId);
    }

    public function cancelarTransferencia() {
        $this->transferenciaCart->destroy();
    }

    /**
     * @param int $bodegaId Identificador único de la bodega de destino seleccionada para la transferencia
     * Esta función se encarga de cargar los usuarios asociados a la bodega de destino seleccionada para la transferencia de productos. Al recibir el identificador de la bodega, se consulta el modelo de usuarios por bodega para obtener la lista de usuarios vinculados a esa bodega. La función devuelve una respuesta JSON con el estado de la operación, un mensaje descriptivo y los datos de
    */
    public function loadUsersConfirm(int $bodegaId) {
        $response = $this->uersBodModel->getUsuariosByBodega($bodegaId);
        return $this->response->setJSON([
                    'status' => $response ? 'success' : 'warning',
                    'msg' => $response ? '' : 'No se han encontrado usuarios en la bodega de destino seleccionada',
                    'data' => $response
        ]);
    }
}
