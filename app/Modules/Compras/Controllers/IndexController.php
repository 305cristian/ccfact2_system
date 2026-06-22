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

    protected $dirViewModule;
    protected $comprasCart;
    protected $prodModel;
    protected $searchModel;
    protected $cuentasConfigLib;

    public function __construct() {

        $this->dirViewModule = 'Modules\Compras\Views';
        $this->comprasCart = new ComprasCartLib();
        $this->prodModel = new ProductoModel();
        $this->searchModel = new SearchsModel();

        //IMPORTAMOS LIBRERIAS
        $this->cuentasConfigLib = new CuentasConfigLib();
    }

    public function index($moduloId) {
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

    public function nuevaCompraEdit($compraId) {
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

    public function responseSetJSON($status, $mensaje, $data = null) {
        return $this->response->setJSON([
                    'status' => $status,
                    'msg' => $mensaje,
                    'data' => $data,
        ]);
    }

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

    public function deleteProduct($rowId) {
        try {
            $this->comprasCart->removeItem($rowId);
            return $this->responseSetJSON('success', 'Producto eliminado del carrito', $rowId);
        } catch (\Throwable $ex) {
            return $this->responseSetJSON('error', 'No se pudo eliminar el producto del carrito: ' . $ex->getMessage(), $rowId);
        }
    }

    public function cancelarCompra() {
        try {
            $this->comprasCart->destroy();
            return $this->responseSetJSON('success', 'Proceso cancelado exitosamente');
        } catch (\Throwable $ex) {
            return $this->responseSetJSON('error', 'Ha ocurrido un error al tratar de cancelar el proceso: ' . $ex->getMessage());
        }
    }

    public function changeBodega($bodegaId) {
        $this->session->set('bodegaIdComp', $bodegaId);
        return $this->responseSetJSON('success', 'Bodega seleccionada correctamente', $bodegaId);
    }
}
