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
use PhpOffice\PhpSpreadsheet\IOFactory;

class IndexController extends \App\Controllers\BaseController {

    //put your code here
    protected string $dirViewModule;
    protected EntradasModel $entradasModel;
    protected EntradasLib $entradasLib;
    protected EntradasAsientosLib $entradasAsientoLib;
    protected ProductoModel $prodModel;
    protected EntradasCartLib $ajenCart;
    protected SearchsModel $searchModel;

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

    public function indexEdit($justeId) {
        $view = $this->parametrosIndex($justeId);
        return view($this->dirTemplate . '\dashboard', $view);
    }

    public function parametrosIndex($justeId = null) {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $data['title']="Entradas";
        $data['listaSustentos'] = $this->ccm->getData('cc_sustentos', ['sus_estado' => 1], 'sus_codigo, sus_nombre');
        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaMotivos'] = $this->ccm->getData('cc_motivos_ajuste', ['mot_estado' => 1, 'mot_tipo !=' => "DESPACHOS"], 'id, mot_nombre');
        $data['listaCentroCostos'] = $this->ccm->getData('cc_centroscosto', ['cc_estado' => 1], 'id, cc_nombre');

        $bodegaMainUsuario = bodegaMain($this->user->id);

        $data['bodegaId'] = $this->session->get('bodegaIdAje') ? $this->session->get('bodegaIdAje') : $bodegaMainUsuario;

        $data['permitirDuplicados'] = getSettings('PERMITIR_ITEMS_DUPLICADOS');
        $data['dataAjuste'] = null;
        $data['dataProveedor'] = null;

        if (!empty($justeId)) {
            $data['dataAjuste'] = $this->ccm->getData('cc_ajuste_entrada', ['id' => $justeId, 'fk_proyecto' => getProyectoId()], '*', null, 1);
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

    public function loadAjusteEdit($justeId) {

        //CARGAMOS LOS DATOS AL CART
        $respuesta = $this->loadDataAjusteCart($justeId);

        return $this->response->setJSON([
                    'status' => $respuesta['status'] === 'success' ? 'success' : 'error',
                    'msg' => $respuesta['status'] === 'success' ? 'ok' : $respuesta['msg'],
                    'redirect' => $respuesta['status'] === 'success' ? site_url('ajustesentrada/indexEdit/' . $justeId) : null
        ]);
    }

    public function loadDataAjusteCart($justeId, $isClone = false) {

        $this->ajenCart->destroy();

        $dataAjuste = $this->entradasModel->getDataDetalle($justeId);

        if ($isClone === false && $dataAjuste->ajen_estado !== '1') {
            return['status' => 'error', 'msg' => 'Este ajuste de entrada ya se encuentra archivado o anulado previamente'];
        }

        foreach ($dataAjuste->detalle as $valDet) {

            $dataProducto = $this->searchModel->searchProductoData($valDet->fk_producto);

            $dataStockBodega = $this->ccm->getData('cc_stock_bodega', ['fk_producto' => $valDet->fk_producto, 'fk_bodega' => $dataAjuste->id_bodega], 'stb_stock', null, 1);
            $stockBodega = $dataStockBodega ? $dataStockBodega->stb_stock : 0;

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
    }

    public function insertProduct() {

        $dataPost = json_decode(file_get_contents("php://input"));

        $idProd = $dataPost->id;
        $cantidad = $dataPost->qty;
        $permitirDuplicados = $dataPost->permitirDuplicados;
        $idBodega = $dataPost->bodega;
        $tipoAjuste = $dataPost->tipoAjuste;

        if ($idProd <= '0' or $idProd == null) {
            $msg['status'] = "warning";
            $msg['msg'] = "No pueden haber productos con código 0 o NULO ";
            return $this->response->setJSON($msg);
        }

        $dataProducto = $this->searchModel->searchProductoData($idProd);
        if (!$dataProducto) {
            $msg['status'] = "warning";
            $msg['msg'] = "No se ha encontrado el producto con el codigo: " . $idProd . '(Posiblemente este desactivado o aun no esta registrado )';
            return $this->response->setJSON($msg);
        }

        $dataStockBodega = $this->ccm->getData('cc_stock_bodega', ['fk_producto' => $idProd, 'fk_bodega' => $idBodega], 'stb_stock', null, 1);
        $stockBodega = $dataStockBodega ? $dataStockBodega->stb_stock : 0;

        $impuestos = $this->prodModel->getImpuestoTarifa($dataProducto->id);
        $tarifaIva = isset($impuestos[0]->impt_porcentage) ? $impuestos[0]->impt_porcentage : 0;
        $tarifaIce = isset($impuestos[1]->impt_porcentage) ? $impuestos[1]->impt_porcentage : 0;

        $precio = $tipoAjuste === 'AJUSTE_NORMAL' ? $dataProducto->prod_costopromedio : $dataProducto->prod_costoultimo;

//        $irbprnValor = $dataProducto->prod_tiene_irbpnr === '1' ? getImpuestoIrbpnr() : null;

        $item = [
            "id" => (int) $dataProducto->id,
            "qty" => (float) $cantidad,
            "codigo" => $dataProducto->prod_codigo,
            "name" => $dataProducto->prod_nombre,
            "unidadMedida" => $dataProducto->um_nombre_corto,
            "price" => (float) $precio,
            "stock" => number_format($dataProducto->prod_stockactual, 2),
            "stockBodega" => number_format($stockBodega, 2),
            "ivaPorcent" => $tarifaIva,
            "icePorcent" => $tarifaIce,
//            "irbprnValor" => $irbprnValor,
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

        if ($dataPost->tieneLote === '1') {
            $existeLote = $this->ccm->getData('cc_lotes', ['lot_lote' => $dataPost->lote, 'fk_producto' => $dataPost->id], '*', null, 1);
            //Si el lote en el producto especificado existe, obtengo sus respectivas fechas
            $lote = $existeLote ? $existeLote->lot_lote : $dataPost->lote;
            $fechaElab = $existeLote ? $existeLote->lot_fecha_elaboracion : $dataPost->fechaElaboracion;
            $fechaCaduc = $existeLote ? $existeLote->lot_fecha_caducidad : $dataPost->fechaCaducidad;
        } else {
            $lote = null;
            $fechaElab = null;
            $fechaCaduc = null;
        }

        $dataStockBodega = $this->ccm->getData('cc_stock_bodega', ['fk_producto' => $idProd, 'fk_bodega' => $dataPost->idBodega], 'stb_stock', null, 1);
        $stockBodega = $dataStockBodega ? $dataStockBodega->stb_stock : 0;

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
            "tieneLote" => $dataPost->tieneLote,
            "permitirDuplicados" => $permitirDuplicados,
            "lote" => $lote,
            "fechaElaboracion" => $fechaElab,
            "fechaCaducidad" => $fechaCaduc,
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

    /**
     * Función para eliminar un producto del carrito de ajustes de entrada, utilizando el identificador único del producto en el carrito (rowId)
     * @param string $rowId El identificador único del producto en el carrito que se desea eliminar
     * El método recibe el identificador del producto en el carrito a través de una solicitud POST, valida el identificador
     */
    public function deleteProduct(string $rowId) {
        $this->ajenCart->removeItem($rowId);
    }

    public function cancelarAjuste() {
        $this->ajenCart->destroy();
    }

    /**
     * Función para cambiar la bodega seleccionada para el proceso de ajuste de entrada, actualizando la sesión con el nuevo identificador de bodega y devolviendo una respuesta JSON con el resultado de la operación
     * @param int $bodegaId El identificador único de la bodega que se desea seleccionar para el proceso de ajuste de entrada
     * @return JSON Respuesta con el estado de la operación, mensaje descriptivo y el identificador de la bodega seleccionada
     * El método recibe el identificador de la bodega a través de una solicitud POST, valida el identificador, actualiza la sesión con la bodega seleccionada y devuelve una respuesta JSON indicando el resultado
     */
    public function changeBodega(int $bodegaId) {
        $this->session->set('bodegaIdAje', $bodegaId);
        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => 'Bodega seleccionada correctamente',
                    'bodegaId' => $bodegaId
        ]);
    }

    /**
     * Función para actualizar un ajuste de entrada existente, procesando los datos del ajuste y su detalle, validando la información, actualizando el ajuste en la base de datos, actualizando el kardex si el ajuste está aprobado, generando el asiento contable correspondiente y devolviendo una respuesta JSON con el resultado de la operación
     * @return JSON Respuesta con el estado de la operación y mensaje descriptivo sobre el resultado de la actualización del ajuste de entrada
     * El método recibe los datos del ajuste a través de una solicitud POST en formato JSON, valida la información, procesa el detalle del ajuste, actualiza el ajuste en la base de datos, actualiza el kardex si el ajuste está aprobado, genera el asiento contable correspondiente y devuelve una respuesta JSON indicando el resultado de la operación. Esta función es esencial para permitir a los usuarios actualizar un ajuste de entrada existente, asegurando que toda la información relacionada con el ajuste se procese correctamente y se refleje en las operaciones relacionadas, como el kardex y la contabilidad, proporcionando una experiencia de usuario fluida y eficiente en la gestión de ajustes de entrada.
     * Es importante destacar que esta función se espera que sea llamada a través de una solicitud AJAX desde la interfaz de usuario del módulo de ajustes de entrada, para permitir a los usuarios actualizar un ajuste de entrada sin necesidad de recargar la página completa. La respuesta JSON proporcionada por esta función debe ser manejada adecuadamente en el frontend para reflejar el resultado de la actualización del ajuste de entrada y proporcionar una experiencia de usuario fluida y eficiente.
     * En resumen, esta función es responsable de actualizar un ajuste de entrada existente, procesando los datos del ajuste y su detalle, valid
     */
    public function updateAjuste() {

        $dataPostAjuste = json_decode(json_encode($this->request->getPost()));

        // Obtener índice (periodo contable)
        $periodoContable = getPeriodoContable($dataPostAjuste->ajenFecha);
        if (!$periodoContable) {
            return $this->responseSetJSON("error", '<h5>Revise el periodo de cierre</h5><br> <h6>Al parecer no se ha encontrado un periodo contable habil para la fecha dada</h6>');
        }

        // Validamos campos antes de procesar
        $statusValidation = $this->validarCampos($dataPostAjuste);
        if ($statusValidation['status']) {
            return $this->responseSetJSON("warning", $statusValidation['msg']);
        }

        $ajusteId = $dataPostAjuste->ajusteId;

        $estadoAjuste = (int) $this->ccm->getValueWhere('cc_ajuste_entrada', ['id' => $ajusteId, 'fk_proyecto' => getProyectoId()], 'ajen_estado');

        if ($estadoAjuste === 2) {
            return $this->responseSetJSON("warning", "Este ajuste ya ha sido aprobado previamente, Imposible actualizarlo");
        }
        if ($estadoAjuste === -1) {
            return $this->responseSetJSON("warning", "Este ajuste ya ha sido anulado previamente, Imposible actualizarlo");
        }

        try {
            $cartData = $this->showDetailCart(1);
            $this->db->transBegin();

            $update = $this->entradasLib->updateAjuste($cartData, $dataPostAjuste, $ajusteId);
            if (!$update) {//UPDATE DEVUELVE TRUE SI TODO SE COMPLETO CORRECTAMENTE
                $this->db->transRollback();
                return $this->responseSetJSON('error', 'Ha ocurrido un error al actualizar el Ajuste');
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

                if ($val->qty <= 0) {
                    $this->db->transRollback();
                    return $this->responseSetJSON('warning', "No pueden haber productos con cantidad menor o igual a 0<br> para el producto {$val->name}");
                }
                if ($val->price <= 0) {
                    $this->db->transRollback();
                    return $this->responseSetJSON('warning', "No pueden haber productos con precio menor o igual a 0<br> para el producto {$val->name}");
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
            $secuencail = $this->ccm->getValueWhere('cc_ajuste_entrada', ['id' => $ajusteId, 'fk_proyecto' => getProyectoId()], 'ajen_secuencial');
            $this->db->transCommit();
            $this->ajenCart->destroy();
            $this->logs->logSuccess('Ajuste Actualizado exitosamente ID: ' . $ajusteId);
            log_message('info', "[Ajuste Entrada] Ajuste actualizado exitosamente ID: , DocID: {$ajusteId}");

            $dataResponse = ['id' => $ajusteId, 'ajen_secuencial' => $secuencail];
            if ($dataPostAjuste->ajenEstado === '2') {
                return $this->responseSetJSON("success", "<h5>Ajuste #" . $secuencail . " registrado exitosamente</h5>", $dataResponse);
            } else {
                return $this->responseSetJSON("success", "<span class='text-warning'>Ajuste #" . $secuencail . " registrado exitosamente<br>REGISTRADO COMO BORRADOR<br></span>", $dataResponse);
            }
        } catch (\Exception $exc) {

            $this->db->transRollback();
            $this->logs->logError('Ha ocurrido un error al actualizar el Ajuste');
            return $this->responseSetJSON('error', '<br>Error al tratar de actualizar el Ajuste <br> ' . $exc->getMessage());
        }


        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => 'Ajuste actualizado exitosamente ' . $ajusteId,
        ]);
    }

    /**
     * Función para guardar un nuevo ajuste de entrada, procesando los datos del ajuste y su detalle, validando la información, registrando el ajuste en la base de datos, actualizando el kardex si el ajuste está aprobado, generando el asiento contable correspondiente y devolviendo una respuesta JSON con el resultado de la operación
     * @return JSON Respuesta con el estado de la operación y mensaje descriptivo sobre el resultado del registro del nuevo ajuste de entrada
     * El método recibe los datos del nuevo ajuste a través de una solicitud POST en formato JSON, valida la información, procesa el detalle del ajuste, registra el ajuste en la base de datos, actualiza el kardex si el ajuste está aprobado, genera el asiento contable correspondiente y devuelve una respuesta JSON indicando el resultado de la operación. Esta función esencial para permitir a los usuarios registrar un nuevo ajuste de entrada, asegurando que toda la información relacionada con el ajuste se procese correctamente y se refleje en las operaciones relacionadas, como el kardex y la contabilidad, proporcionando una experiencia de usuario fluida y eficiente en la gestión de ajustes de entrada.
     * Es importante destacar que esta función se espera que sea llamada a través de una solicitud AJAX desde la interfaz de usuario del módulo de ajustes de entrada, para permitir a los usuarios registrar un nuevo ajuste de entrada sin necesidad de recargar la página completa. La respuesta JSON proporcionada por esta función debe ser manejada adecuadamente en el frontend para reflejar el resultado del registro del nuevo ajuste de entrada y proporcionar una experiencia de usuario fluida y eficiente.    
     * En resumen, esta función es responsable de guardar un nuevo ajuste de entrada, procesando los datos del ajuste y su detalle, validando la información, registrando el ajuste en la base de datos, actualizando el kardex si el ajuste está aprobado, generando el asiento contable correspondiente y devolviendo una respuesta JSON con el resultado de la operación, permitiendo a los usuarios gestionar eficientemente sus ajustes de entrada.
     */
    public function saveAjuste() {

        $dataPostAjuste = json_decode(json_encode($this->request->getPost()));

        // Obtener índice (periodo contable)
        $periodoContable = getPeriodoContable($dataPostAjuste->ajenFecha);
        if (!$periodoContable) {
            return $this->responseSetJSON("error", '<h5>Revise el periodo de cierre</h5><br> <h6>Al parecer no se ha encontrado un periodo contable habil para la fecha dada</h6>');
        }


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
                return $this->responseSetJSON('error', 'Ha ocurrido un error al registrar el Ajuste');
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
                if ($val->qty <= 0) {
                    $this->db->transRollback();
                    return $this->responseSetJSON('warning', "No pueden haber productos con cantidad menor o igual a 0<br> para el producto {$val->name}");
                }
                if ($val->price <= 0) {
                    $this->db->transRollback();
                    return $this->responseSetJSON('warning', "No pueden haber productos con precio menor o igual a 0<br> para el producto {$val->name}");
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
            $secuencail = $this->ccm->getValueWhere('cc_ajuste_entrada', ['id' => $ajusteId, 'fk_proyecto' => getProyectoId()], 'ajen_secuencial');
            $this->db->transCommit();
            $this->ajenCart->destroy();
            $this->logs->logSuccess('Ajuste registrado exitosamente ID: ' . $ajusteId);
            log_message('info', "[Ajuste Entrada] Ajuste registrado exitosamente ID: , DocID: {$ajusteId}");

            $dataResponse = ['id' => $ajusteId, 'ajen_secuencial' => $secuencail];
            if ($dataPostAjuste->ajenEstado === '2') {
                return $this->responseSetJSON("success", "<h5>Ajuste #" . $secuencail . " registrado exitosamente</h5>", $dataResponse);
            } else {
                return $this->responseSetJSON("success", "<span class='text-warning'>Ajuste #" . $secuencail . " registrado exitosamente<br>REGISTRADO COMO BORRADOR<br> </span>", $dataResponse);
            }
        } catch (\Throwable $exc) {

            $this->db->transRollback();
            $this->logs->logError('Ha ocurrido un error al registrar el Ajuste');
            return $this->responseSetJSON('error', '<br>Error al tratar de crear el Ajuste <br> ' . $exc->getMessage() . $exc->getTraceAsString());
        }
    }

    /**
     * Función para validar los campos requeridos antes de procesar el registro o actualización de un ajuste de entrada, verificando que todos los campos necesarios estén presentes y devolviendo una respuesta con el estado de la validación y un mensaje descriptivo en caso de que falte algún campo requerido
     * @param object $data Objeto con los datos del ajuste de entrada a validar, incluyendo campos como fecha, sustento, bodega, centro de costos, motivo, estado, proveedor y tipo de ajuste
     * @return array Respuesta con el estado de la validación (
     */
    public function validarCampos(object $data): array {

        $campos = [
            'ajenFecha' => 'Debe seleccionar una fecha',
            'ajenSustento' => 'Debe seleccionar un sustento',
            'ajenBodega' => 'Debe seleccionar una bodega',
            'ajenCentrocosto' => 'Debe seleccionar un centro de costos',
            'ajenMotivo' => 'Debe seleccionar un motivo de ajuste',
            'ajenEstado' => 'Debe seleccionar un estado',
            'ajenProveedor' => 'Debe seleccionar un proveedor',
            'ajenTipo' => 'Debe seleccionar un tipo de ajuste',
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

    /**
     * Función para guardar un nuevo lote en la base de datos, asociándolo a un ajuste de entrada y un producto específico, y devolviendo el ID del lote registrado
     * @param int $ajusteId El identificador del ajuste de entrada al que se desea asociar el lote
     * @param object $producto Objeto con los datos del producto para el cual se desea registrar el lote, incluyendo información como el número de lote, fecha de elaboración y fecha de caducidad
     * @return int ID del lote registrado en la base de datos, o false en caso de que ocurra un error durante el registro del lote  
     */
    public function saveLote(int $ajusteId, object $producto): int {
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

    /**
     * Función para anular un ajuste de entrada existente, validando la información proporcionada, ejecutando la anulación en la base de datos y devolviendo una respuesta JSON con el resultado de la operación
     * @return JSON Respuesta con el estado de la operación y mensaje descriptivo sobre el resultado de la anulación del ajuste de entrada
     * El método recibe los datos necesarios para anular un ajuste de entrada a través de una solicitud POST en formato JSON, valida la información, ejecuta la anulación en la base de datos utilizando la librería correspondiente y devuelve una respuesta JSON indicando el resultado de la operación. Esta función es esencial para permitir a los usuarios anular un ajuste de entrada existente, asegurando que toda la información relacionada con el ajuste se procese correctamente durante la anulación y proporcionando una experiencia de usuario fluida y eficiente en la gestión de ajustes de entrada.
     * Es importante destacar que esta función se espera que sea llamada a través de una solicitud AJAX desde la interfaz de usuario del módulo de ajustes de entrada, para permitir a los usuarios anular un ajuste de entrada sin necesidad de recargar la página completa. La respuesta JSON proporcionada por esta función debe ser manejada adecuadamente en el frontend para reflejar el resultado de la anulación del ajuste de entrada y proporcionar una experiencia de usuario fluida y eficiente.    
     * En resumen, esta función es responsable de anular un ajuste de entrada existente, validando la información proporcionada, ejecutando la anulación en la base de datos utilizando la librería correspondiente y devolviendo una respuesta JSON
     */
    public function anularAjuste() {

        //Validamos que la secion este activa
        $this->user->validateSession();

        $data = json_decode(file_get_contents('php://input'));

        $ajusteId = $data->ajusteId;
        $motivoAnulacion = $data->motivoAnulacion;
        $tipoAjuste = $data->tipoAjuste;

        try {

            // Validamos que el ID sea válido
            if (empty($ajusteId)) {
                return $this->responseSetJSON('warning', 'ID de ajuste inválido');
            }
            // Validamos que especifique un motivo de anulación
            if (empty($motivoAnulacion)) {
                return $this->responseSetJSON('warning', 'Debe especificar un motivo de anulación');
            }


            $this->db->transBegin();

            // Ejecutamos la anulación en la librería
            $response = $this->entradasLib->anularAjuste($ajusteId, $motivoAnulacion, $tipoAjuste);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
            }
            if ($response['status'] !== 'success') {
                $this->db->transRollback();
                return $this->responseSetJSON($response['status'], $response['msg']);
            }

            $this->db->transCommit();
            $this->logs->logSuccess("[Ajuste Entrada] Anulado exitosamente ID: {$ajusteId}");
            return $this->responseSetJSON('success', $response['msg']);
        } catch (\Exception $exc) {
            $this->logs->logError('Excepción al anular ajuste: ' . $exc->getMessage());
            return $this->responseSetJSON('error', 'Error interno: ' . $exc->getMessage());
        }
    }

    /**
     * Función para clonar un ajuste de entrada existente, cargando los datos del ajuste original en el carrito de ajustes de entrada y devolviendo una respuesta JSON con el resultado de la operación y la URL de redirección para editar el nuevo ajuste clonado
     * @param int $ajusteId El identificador del ajuste de entrada que se desea clonar
     * @return JSON Respuesta con el estado de la operación y la URL de redirección para editar el nuevo ajuste clonado
     * El método recibe el identificador del ajuste de entrada a clonar a través de una solicitud POST, carga los datos del ajuste original en el carrito de ajustes de entrada utilizando una función específica, y devuelve una respuesta JSON indicando el resultado de
     */
    public function clonarAjuste(int $ajusteId) {
        //CARGAMOS LOS DATOS AL CART
        $respuesta = $this->loadDataAjusteCart($ajusteId, true);

        return $this->response->setJSON([
                    'status' => $respuesta['status'] === 'success' ? 'success' : 'error',
                    'redirect' => site_url('ajustesentrada/nuevoAjuste')
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

            // Leer archivo Excel
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();
            $registros = $sheet->toArray(null, true, true, true); //(valor de celdas vacias, calcular formulas, formato date, returnCellRef), el cuarto parametro es true para que las columas sean manejadas mediante su letra, si fuera false serian manejadas mediante sus indices 0,1,2,etc

            $importados = 0;
            $errores = [];
            $fila = 1;

            foreach ($registros as $i => $row) {
                $fila++;
                if ($i === 1) {
                    continue; // Saltamos cabecera
                }
                $codigo = trim($row['A'] ?? '');
                $cantidad = (float) ($row['B'] ?? 0);
                $lote = trim($row['C'] ?? '');
                $fechaElab = trim($row['D'] ?? '');
                $fechaCaduc = trim($row['E'] ?? '');

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
                    $errores[] = "Fila {$i}: el producto con código '{$codigo}' no existe o esta desactivado.";
                    continue;
                }
                $producto = $this->searchModel->searchProductoData($idProd);

                //SI EL PRODUCTO TIENE CONTROL DE LOTES CONTROLAMOS LOS LOTES
                if ($producto->prod_ctrllote === '1') {

                    if (empty($lote)) {
                        $errores[] = "Fila {$i}: el producto '{$codigo}' requiere un número de lote.";
                        continue;
                    }

                    if (empty($fechaElab) || empty($fechaCaduc)) {
                        $errores[] = "Fila {$i}: el producto '{$codigo}' requiere fecha de elaboración y caducidad.";
                        continue;
                    }

                    // Convertir fechas
                    try {
                        $fechaElab = date('Y-m-d', strtotime($fechaElab));
                        $fechaCaduc = date('Y-m-d', strtotime($fechaCaduc));
                    } catch (\Throwable $e) {
                        $errores[] = "Fila {$i}: formato de fecha inválido para '{$codigo}'.";
                        continue;
                    }
                    $existeLote = $this->ccm->getData('cc_lotes', ['lot_lote' => $lote, 'fk_producto' => $producto->id], '*', null, 1);
                    //Si el lote en el producto especificado existe, obtengo sus respectivas fechas
                    if ($existeLote) {
                        $lote = $existeLote->lot_lote;
                        $fechaElab = $existeLote->lot_fecha_elaboracion;
                        $fechaCaduc = $existeLote->lot_fecha_caducidad;
                    }
                } else {
                    // Productos que no manejan lotes
                    $lote = null;
                    $fechaElab = null;
                    $fechaCaduc = null;
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
                    "stock" => $producto->prod_stockactual,
                    "stockBodega" => $stockBodega,
                    "ivaPorcent" => $tarifaIva,
                    "icePorcent" => $tarifaIce,
                    "tieneLote" => $producto->prod_ctrllote,
                    "permitirDuplicados" => $permitirDuplicados,
                    "lote" => $lote,
                    "fechaElaboracion" => $fechaElab,
                    "fechaCaducidad" => $fechaCaduc,
                    "servicio" => $producto->prod_isservicio,
                ];

                $this->ajenCart->insert($item);
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
