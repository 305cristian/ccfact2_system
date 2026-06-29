<?php

namespace Modules\Compras\Controllers;

use App\Controllers\BaseController;
use Modules\Comun\Models\ProductoModel;
use Modules\Comun\Models\SearchsModel;
use Modules\Compras\Libraries\ComprasCartLib;
use Modules\Comun\Libraries\CuentasConfigLib;

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of IndexController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 3 may 2026
 * @time 3:40:05 p.m.
 */
class IndexController extends BaseController {

    protected string $dirViewModule;
    protected ComprasCartLib  $comprasCart;
    protected ProductoModel $prodModel;
    protected SearchsModel $searchModel;
    protected CuentasConfigLib $cuentasConfigLib;

    public function __construct() {

        $this->dirViewModule = 'Modules\Compras\Views';
        $this->comprasCart = new ComprasCartLib();
        $this->prodModel = new ProductoModel();
        $this->searchModel = new SearchsModel();

        //IMPORTAMOS LIBRERIAS
        $this->cuentasConfigLib = new CuentasConfigLib();
    }

    /**
     * Función para mostrar el dashboard principal del módulo de compras
     * @param int $moduloId El identificador del módulo a mostrar en el dashboard
    */

    public function index(int $moduloId) {
        $this->user->validateSession();
        $data['moduloId'] = $moduloId;
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewDashboard', $data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function nuevaCompra() {
        $view = $this->parametrosIndex();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($view);
        } else {
            return view($this->dirTemplate . '\dashboard', $view);
        }
    }

    /**
     * Función para cargar la vista de edición de compra con los datos de la compra especificada
     * @param int $compraId El identificador único de la compra a editar
    */
    public function nuevaCompraEdit(int $compraId) {
        $view = $this->parametrosIndex($compraId);
        return view($this->dirTemplate . '\dashboard', $view);
    }

    public function parametrosIndex($compraId = null) {

        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);

        $data['listaTiposCompra'] = $this->ccm->getData('cc_tipo_compra', ['tc_estado' => 1], 'id, tc_nombre');
        $data['listaFormasPago'] = $this->ccm->getData('cc_formas_pago', ['fp_estado' => 1], 'cod, fp_nombre');
        $data['listaFormasPagoSRI'] = $this->ccm->getData('cc_formas_pago_sri', ['fp_estado' => 1], 'codigo, fp_nombre_sri');
        $data['listaTiposComprobantes'] = $this->ccm->getData('cc_tipos_comprobante', ['comp_estado' => 1], 'comp_codigo, comp_nombre, id');
        $data['listaCuentasContables'] = $this->ccm->getData('cc_cuenta_contabledet', ['ctad_estado' => 1], 'ctad_codigo, ctad_nombre_cuenta, CONCAT_WS(" ",ctad_codigo,ctad_nombre_cuenta)cuenta ');
        $data['listaImpuestosTarifa'] = $this->ccm->getData('cc_impuesto_tarifa', ['fk_impuesto' => 1, 'impt_estado' => 'ACTIVO'], '*');
        $data['listaSustentos'] = $this->ccm->getData('cc_sustentos', ['sus_estado' => 1], 'sus_codigo, sus_nombre');
        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaCentroCostos'] = $this->ccm->getData('cc_centroscosto', ['cc_estado' => 1], 'id, cc_nombre');
        $data['listaRetenciones'] = $this->ccm->getData('cc_retencion_sri', ['ret_estado' => 1], 'id, ret_codigo, ret_nombre, ret_porcentaje, ret_impuesto, ret_impuesto_detalle, CONCAT_WS(" - ",ret_codigo,ret_nombre,ret_porcentaje)retencionName');

        $bodegaMainUsuario = bodegaMain($this->user->id);

        $data['bodegaId'] = $this->session->get('bodegaIdComp') ? $this->session->get('bodegaIdComp') : $bodegaMainUsuario;

        $data['permitirDuplicados'] = getSettings('PERMITIR_ITEMS_DUPLICADOS');
        $data['dataCompra'] = null;
        $data['dataProveedor'] = null;

        if (!empty($compraId)) {
            $data['dataCompra'] = $this->ccm->getData('cc_compras', ['id' => $compraId], '*', null, 1);
            $data['dataProveedor'] = $this->searchModel->searchProveedorById($data['dataCompra']->fk_proveedor);
        }

        $send['view'] = view($this->dirViewModule . '\viewNewCompra', $data);

        return $send;
    }
    

    /**
     * Función para generar una respuesta JSON con un formato estándar para las operaciones del módulo de compras
     * @param string $status El estado de la operación (e.g., 'success', 'error', 'warning')
     * @param string $mensaje Un mensaje descriptivo sobre el resultado de la operación
     * @param mixed $data (Opcional) Datos adicionales relacionados con la operación, como detalles del producto o información de la compra
     * @return JSON Respuesta formateada con el estado, mensaje y datos proporcionados
    */
    public function responseSetJSON( string $status, string $mensaje, mixed $data = null) {
        return $this->response->setJSON([
                    'status' => $status,
                    'msg' => $mensaje,
                    'data' => $data,
        ]);
    }


    /**
     * Función para agregar un producto al carrito de compras, validando la información del producto y calculando los impuestos correspondientes
     * @return JSON Respuesta con el estado de la operación, mensaje descriptivo y detalles del producto agregado al carrito
     * El método recibe los datos del producto a través de una solicitud POST en formato JSON, valida la información, obtiene los detalles del producto y sus impuestos, y luego agrega el producto al carrito utilizando la biblioteca ComprasCartLib. La respuesta JSON incluye el estado de la operación ('success' o 'warning'), un mensaje descriptivo y los detalles del producto agregado al carrito.
    */
    public function insertProduct() {
        $dataPost = json_decode(file_get_contents('php://input'));

        $idProd = $dataPost->id ?? null;
        $cantidad = $dataPost->qty ?? 1;
        $permitirDuplicados = $dataPost->permitirDuplicados ?? false;

        if (empty($idProd) || !is_numeric($idProd) || $idProd <= 0) {
            return $this->responseSetJSON('warning', 'No puede agregarse un producto nulo.');
        }

        $dataProducto = $this->searchModel->searchProductoData($idProd);
        if (!$dataProducto) {

            return $this->responseSetJSON('warning', 'Producto no encontrado o inactivo.');
        }


        $tarifas = $this->prodModel->getImpuestoTarifa($dataProducto->id);
        $ivaPorcent = 0;
        $icePorcent = 0;
        $porcentajeSelect = 0;
        $detallePorcentajeSelect = "";
        if ($tarifas) {
            foreach ($tarifas ?? [] as $tarifa) {
                if (!isset($tarifa->impt_porcentage)) {
                    continue;
                }
                switch ((int) $tarifa->fk_impuesto) {

                    case 1: // IVA
                        $ivaPorcent = (float) $tarifa->impt_porcentage;
                        $porcentajeSelect = (int) $tarifa->id;
                        $codigoPorcentajeSelect = $tarifa->impt_codigo;
                        $detallePorcentajeSelect = $tarifa->impt_detalle;
                        break;

                    case 2: // ICE
                        $icePorcent = (float) $tarifa->impt_porcentage;
                        break;
                }
            }
        }

        $centroCostoData = $this->ccm->getData('cc_centroscosto', ['cc_estado' => 1], 'id', null, 1);
        if (!$centroCostoData) {
            return $this->responseSetJSON('warning', 'Debe existir al menos un centro de costos en el proyecto');
        }

        $item = [
            'id' => (int) $dataProducto->id,
            'qty' => (float) $cantidad,
            'codigo' => $dataProducto->prod_codigo,
            'name' => $dataProducto->prod_nombre,
            'unidadMedida' => $dataProducto->um_nombre_corto,
            'price' => (float) ($dataProducto->prod_costoultimo ?? 0),
            'ivaPorcent' => $ivaPorcent,
            'icePorcent' => $icePorcent,
            'impuestoSelect' => $porcentajeSelect,
            'codigoImpuestoSelect' => $codigoPorcentajeSelect,
            'detalleImpuestoSelect' => $detallePorcentajeSelect,
            //Descuentos
            'tipoDescuento' => 'VALOR',
            'discountPercent' => 0,
            'discountValue' => 0,
            'tieneLote' => $dataProducto->prod_ctrllote,
            'permitirDuplicados' => $permitirDuplicados,
            'lote' => null,
            'fechaElaboracion' => null,
            'fechaCaducidad' => null,
            'servicio' => $dataProducto->prod_isservicio,
            'irbpnrUnitario' => $dataProducto->prod_tiene_irbpnr === '1' ? (float) getImpuestoIrbpnr() : 0,
            'centroCosto' => $centroCostoData->id,
            'ctaContableProducto' => null,
            'codigoImport' => null,
            'isNewProduct' => 0
        ];

        if (!empty($dataProducto->fk_cuentacontablecompras)) {
            $item['ctaContableProducto'] = $dataProducto->fk_cuentacontablecompras;
        } else {
            $codigoCuenta = null;
            if ($dataProducto->prod_isservicio === 0 && $dataProducto->fk_tipoproducto === 1) {// SI ES UN PRODUCTO NORMAL COMPRA Y VENTA Y NO TIENE DEFINIDO CUENTA CONTABLE
                $codigoCuenta = $ivaPorcent > 0 ? '011' : '010';
            } else if ($dataProducto->prod_isservicio === 1 && $dataProducto->fk_tipoproducto === 3) {// SI ES UN PRODUCTO TIPO SERVICIO Y NO TIENE DEFINIDO CUENTA CONTABLE
                $codigoCuenta = '014';
            }
            if ($codigoCuenta) {
                $item['ctaContableProducto'] = $this->cuentasConfigLib->obtenerSettingCuentaContable($codigoCuenta);
            }
        }

        $this->comprasCart->insert($item);

        return $this->responseSetJSON('success', 'Producto agregado al carrito', $item);
    }


    /**
     * Función para mostrar los detalles del carrito de compras, incluyendo los productos agregados, totales y cálculos de impuestos
     * @return JSON Respuesta con los detalles del carrito de compras, como los productos agregados, totales, impuestos y otros cálculos relacionados
     * El método obtiene el contenido actual del carrito de compras utilizando la biblioteca ComprasCartLib, calcula los totales, impuestos y otros valores relacionados, y luego devuelve una respuesta JSON con toda esta información para ser utilizada en la interfaz de usuario del módulo de compras.
     * La respuesta JSON incluye detalles como el contenido del carrito, total de artículos, subtotales brutos y netos, totales de impuestos (IVA, ICE, IRBPNR), total general, totales específicos para bienes y servicios, tarifas cero, descuentos, recargos, bases imponibles y otros cálculos relevantes para la gestión de compras.
     * La función también maneja el caso en el que el carrito esté vacío, devolviendo un valor nulo para el contenido del carrito en la respuesta JSON.
     * Esta función es esencial para mantener actualizada la información del carrito de compras en la interfaz de usuario, permitiendo a los usuarios ver los detalles de los productos que han agregado, así como los cálculos relacionados con su compra antes de finalizarla.
     * Es importante destacar que esta función se espera que sea llamada a través de una solicitud AJAX desde la interfaz de usuario del módulo de compras, para actualizar dinámicamente la información del carrito sin necesidad de recargar la página completa. La respuesta JSON proporcionada por esta función debe ser manejada adecuadamente en el frontend para reflejar los cambios en el carrito de compras y proporcionar una experiencia de usuario fluida y eficiente.
     * En resumen, esta función es responsable de proporcionar una visión detallada y actualizada del carrito de compras, incluyendo los productos agregados, totales, impuestos y otros cálculos relevantes, a través de una respuesta JSON que puede ser utilizada para actualizar la interfaz de usuario del módulo de compras de manera dinámica.
    */
    public function showDetailCart() {
        $cartContent = array_values($this->comprasCart->getContent() ?? []);

        $dataCart = [
            'cartContent' => $cartContent ? array_reverse($cartContent) : null,
            'totalArticles' => $this->comprasCart->totalArticles(),
            'totalSubtotalBruto' => $this->comprasCart->totalSubtotalBruto(),
            'totalSubtotalNeto' => $this->comprasCart->totalSubtotalNeto(),
            'totalIva' => $this->comprasCart->totalIva(),
            'totalIce' => $this->comprasCart->totalIce(),
            'totalIrbpnr' => $this->comprasCart->totalIrbpnr(),
            'totalGeneral' => $this->comprasCart->totalGeneral(),
            'totalBienesNeto' => $this->comprasCart->totalBienesNeto(),
            'totalBienesBruto' => $this->comprasCart->totalBienesBruto(),
            'totalServiciosNeto' => $this->comprasCart->totalServiciosNeto(),
            'totalServiciosBruto' => $this->comprasCart->totalServiciosBruto(),
            'tarifCeroBruto' => $this->comprasCart->tarifCeroBruto(),
            'tarifCeroNeto' => $this->comprasCart->tarifCeroNeto(),
            'tarifIvaBruto' => $this->comprasCart->tarifIvaBruto(),
            'tarifIvaNeto' => $this->comprasCart->tarifIvaNeto(),
            'totalDescuentoItems' => $this->comprasCart->totalDescuentoItems(),
            'totalDescuentoGlobal' => $this->comprasCart->totalDescuentoGlobal(),
            'totalServiciosAdc' => $this->comprasCart->totalServiciosAdc(),
            'totalRecargo' => $this->comprasCart->totalRecargo(),
            'tarifExcentoNeto' => $this->comprasCart->totalExcentoIva(),
            'tarifNoObjetoNeto' => $this->comprasCart->totalnoObjetoImpuestos(),
            'baseIva' => $this->comprasCart->totalBaseIva(),
            'baseRenta' => $this->comprasCart->totalBaseRenta(),
            'ivaBienes' => $this->comprasCart->totalIvaBienes(),
            'ivaServicios' => $this->comprasCart->totalIvaServicios(),
            'basesImpuesto' => $this->comprasCart->getImpuestos(),
        ];

        return $this->response->setJSON($dataCart);
    }


    /**
     * Función para actualizar la información de un producto específico en el carrito de compras, incluyendo cantidad, precio, impuestos y descuentos
     * @return JSON Respuesta con el estado de la operación, mensaje descriptivo y detalles del producto actualizado en el carrito
     * El método recibe los datos actualizados del producto a través de una solicitud POST en formato JSON, valida la información proporcionada, obtiene los detalles del producto y sus impuestos, calcula los descuentos aplicables y luego actualiza el producto en el carrito utilizando la biblioteca ComprasCartLib. La respuesta JSON incluye el estado de la operación ('success' o 'error'), un mensaje descriptivo sobre el resultado de la actualización y los detalles del producto actualizado en el carrito.
     * La función maneja casos de validación para asegurarse de que se proporciona un identificador de producto válido, una cantidad mayor a cero y un precio válido. Además, calcula los descuentos basados en el tipo de descuento (valor o porcentaje) y actualiza el producto en el carrito con toda la información relevante, incluyendo impuestos, descuentos, detalles del producto y otros atributos relacionados.
     * Esta función es esencial para permitir a los usuarios modificar la información de los productos que han
     * agregado al carrito de compras, asegurando que los cálculos de impuestos y descuentos se actualicen correctamente en función de los cambios realizados. La respuesta JSON proporcionada por esta función debe ser manejada adecuadamente en el frontend para reflejar los cambios en el carrito de compras y proporcionar una experiencia de usuario fluida y eficiente.
     * En resumen, esta función es responsable de actualizar la información de un producto específico en el carrito de compras, incluyendo cantidad, precio, impuestos y descuentos, a través de una solicitud POST con datos en formato JSON, y devuelve una respuesta JSON con el resultado de la operación y los detalles del producto actualizado en el carrito.
     * 
    */
    public function updateProduct() {
        $dataPost = json_decode(file_get_contents('php://input'));

        $rowId = $dataPost->rowid ?? null;
        $idProd = (int) $dataPost->id ?? null;
        $cantidad = (float) $dataPost->qty ?? null;
        $precio = (float) $dataPost->price ?? null;

        if (empty($rowId)) {
            return $this->responseSetJSON('warning', 'No se ha especificado el producto a actualizar.');
        }

        if (empty($idProd) || !is_numeric($idProd) || $idProd <= 0) {
            return $this->responseSetJSON('warning', 'No puede actualizarse un producto con código inválido.');
        }

        if (!is_numeric($cantidad) || $cantidad <= 0) {
            return $this->responseSetJSON('warning', 'La cantidad debe ser mayor a cero.');
        }

        if (!is_numeric($precio) || $precio <= 0) {
            return $this->responseSetJSON('warning', 'El precio debe ser un valor válido y mayor a cero.');
        }

        $impuestoSelect = $dataPost->impuestoSelect;
        $impuesto = $this->ccm->getData('cc_impuesto_tarifa', ['id' => $impuestoSelect], 'impt_porcentage, impt_codigo, impt_detalle', null, 1);

        //CALCULO DESCUENTO UNITARIO
        $descuento = (float) ($dataPost->descuento ?? 0);
        $discountValue = 0;
        $discountPercent = 0;

        if ($dataPost->tipoDescuento === 'VALOR') {
            $discountValue = $descuento;
            if ($precio > 0) {
                $discountPercent = ($discountValue / $precio) * 100;
            }
        } else { // PORCENTAJE
            $discountPercent = $descuento;
            $discountValue = ($precio * $discountPercent) / 100;
        }

        $item = [
            'id' => $idProd,
            'qty' => $cantidad,
            'price' => $precio,
            'codigo' => $dataPost->codigo ?? null,
            'name' => $dataPost->name ?? null,
            'unidadMedida' => $dataPost->unidadMedida ?? null,
            'ivaPorcent' => (float) $impuesto->impt_porcentage ?? 0,
            'icePorcent' => $dataPost->icePorcent ?? 0,
            'impuestoSelect' => $impuestoSelect ?? null,
            'codigoImpuestoSelect' => (int) $impuesto->impt_codigo,
            'detalleImpuestoSelect' => $impuesto->impt_detalle,
            'tipoDescuento' => $dataPost->tipoDescuento ?? null,
            'discountPercent' => $discountPercent,
            'discountValue' => $discountValue,
            'descuento' => $descuento,
            'permitirDuplicados' => $dataPost->permitirDuplicados ?? false,
            'tieneLote' => $dataPost->tieneLote ?? null,
            'lote' => $dataPost->lote ?? null,
            'fechaElaboracion' => $dataPost->fechaElaboracion ?? null,
            'fechaCaducidad' => $dataPost->fechaCaducidad ?? null,
            'servicio' => $dataPost->servicio ?? null,
            'irbpnrUnitario' => $dataPost->irbpnrUnitario ?? 0,
            'centroCosto' => $dataPost->centroCosto ?? null,
            'ctaContableProducto' => $dataPost->ctaContableProducto ?? null,
            'codigoImport' => null,
            'isNewProduct' => 0,
            'rowid' => $rowId
        ];

        try {
            $this->comprasCart->update($item, $rowId);
            return $this->responseSetJSON('success', 'Producto actualizado', $rowId);
        } catch (\Throwable $ex) {
            return $this->responseSetJSON('error', 'No se pudo actualizar el producto: ' . $ex->getMessage(), $rowId);
        }
    }
    
    /**
     * Función para actualizar los valores globales del carrito de compras, como descuentos globales, recargos y servicios adicionales
     * @return JSON Respuesta con el estado de la operación y mensaje descriptivo sobre el resultado de la actualización de los valores globales del carrito de compras
     * El método recibe los valores globales a través de una solicitud POST en formato JSON,
    */
    public function updateValoresGlobales() {
        $dataPost = json_decode(file_get_contents('php://input'));

        $descuentoGlobal = (float) ($dataPost->descuentoGlobal ?? 0);
        $recargo = (float) ($dataPost->recargo ?? 0);
        $serviciosAdc = (float) ($dataPost->serviciosAdc ?? 0);

        try {
            $this->comprasCart->updateValoresGlobales($descuentoGlobal, $recargo, $serviciosAdc);
            return $this->responseSetJSON('success', 'Valores globales actualizados');
        } catch (\Throwable $e) {
            return $this->responseSetJSON('error', 'Error al actualizar los datos globales' . $e->getMessage());
        }
    }


    /**
     * @param string $rowId El identificador único del producto en el carrito a eliminar
      @return JSON Respuesta con el estado de la operación y mensaje correspondiente
    */
    public function deleteProduct($rowId) {
        try {
            $this->comprasCart->removeItem($rowId);
            return $this->responseSetJSON('success', 'Producto eliminado del carrito', $rowId);
        } catch (\Throwable $ex) {
            return $this->responseSetJSON('error', 'No se pudo eliminar el producto del carrito: ' . $ex->getMessage(), $rowId);
        }
    }
    

    /**
     * Función para cancelar el proceso de compra actual, eliminando todos los productos del carrito y restableciendo los valores globales
     * @return JSON Respuesta con el estado de la operación y mensaje descriptivo sobre el resultado de la cancelación del proceso de compra
     * El método destruye el contenido del carrito de compras utilizando la biblioteca ComprasCartLib, eliminando todos los productos agregados y restableciendo cualquier valor global asociado al proceso de compra. La respuesta JSON indica si la operación fue exitosa o si ocurrió un error durante el proceso de cancelación, proporcionando un mensaje descriptivo sobre el resultado de la operación. Esta función es esencial para permitir a los usuarios cancelar el proceso de compra en cualquier momento, asegurando que toda la información relacionada con el carrito de compras se elimine correctamente y se restablezca el estado del proceso para futuras compras.
     * Es importante destacar que esta función se espera que sea llamada a través de una solicitud AJAX desde la interfaz de usuario del módulo de compras, para permitir a los usuarios cancelar el proceso de compra sin necesidad de recargar la página completa. La respuesta JSON proporcionada por esta función debe ser manejada adecuadamente en el frontend para reflejar la cancelación del proceso de compra y proporcionar una experiencia de usuario fluida y eficiente.
     * En resumen, esta función es responsable de cancelar el proceso de compra actual, eliminando todos los productos del carrito y restableciendo los valores globales, a través de una solicitud AJAX, y devuelve una respuesta JSON con el resultado de la operación.
    */
    public function cancelarCompra() {
        try {
            $this->comprasCart->destroy();
            return $this->responseSetJSON('success', 'Proceso cancelado exitosamente');
        } catch (\Throwable $ex) {
            return $this->responseSetJSON('error', 'Ha ocurrido un error al tratar de cancelar el proceso: ' . $ex->getMessage());
        }
    }

    /**
     * @param int $bodegaId_ Identificador unico de la bodega
     * @return JSON Respuesta con el estado de la operación, mensaje descriptivo y el identificador de la bodega seleccionada
     * La función recibe el identificador de la bodega a través de una solicitud POST, valida el identificador, actualiza la sesión con la bodega seleccionada y devuelve una respuesta JSON indicando el resultado de la operación. Esta función es esencial para permitir a los usuarios seleccionar una bodega específica para sus operaciones de compra, asegurando que la información de la bodega se almacene correctamente en la sesión y se refleje en las operaciones relacionadas con el proceso de compra.
     * Es importante destacar que esta función se espera que sea llamada a través de una solicitud AJAX desde la interfaz de usuario del módulo de compras, para permitir a los usuarios cambiar la bodega seleccionada sin necesidad de recargar la página completa. La respuesta JSON proporcionada por esta función debe ser manejada adecuadamente en el frontend para reflejar el cambio de bodega y proporcionar una experiencia de usuario fluida y eficiente.
     * En resumen, esta función es responsable de cambiar la bodega seleccionada para el proceso de compra, actualizando la sesión con el nuevo identificador de bodega y devolviendo una respuesta JSON con el resultado de la operación.
     */
    public function changeBodega($bodegaId_) {
        $bodegaId = (int) $bodegaId_;
        $this->session->set('bodegaIdComp', $bodegaId);
        return $this->responseSetJSON('success', 'Bodega seleccionada correctamente', $bodegaId);
    }
}
