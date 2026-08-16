<?php

namespace Modules\Ventas\Controllers;

use App\Controllers\BaseController;
use Modules\Ventas\Models\VentasModel;
use Modules\Comun\Models\ProductoModel;
use Modules\Comun\Models\SearchsModel;
use Modules\Comun\Models\LotesStockModel;
use Modules\Comun\Libraries\StockBodegaLib;
use Modules\Comun\Libraries\ReservasLib;
use Modules\Ventas\Libraries\VentasCartLib;
use Modules\Ventas\Libraries\VentasLib;
use Modules\Ventas\Libraries\VentasFinanzasLib;
use Modules\Ventas\Libraries\VentasAsientosLib;

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of IndexController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 9 ago 2026
 * @time 8:25:42 a.m.
 */
class IndexController extends BaseController {

    //put your code here
    //put your code here
    protected $dirViewModule;
    protected VentasModel $ventasModel;
    protected ProductoModel $prodModel;
    protected SearchsModel $searchModel;
    protected LotesStockModel $lotesStkModel;
    protected StockBodegaLib $stockBodLib;
    protected ReservasLib $reservasLib;
    protected VentasCartLib $ventasCart;
    protected VentasLib $ventasLib;
    protected VentasFinanzasLib $ventasFinanzasLib;
    protected VentasAsientosLib $ventasAsientosLib;
    protected string $transaccionCod = '01'; // VENTA

    public function __construct() {
        $this->dirViewModule = 'Modules\Ventas\Views';
        $this->ventasModel = new VentasModel();
        $this->prodModel = new ProductoModel();
        $this->searchModel = new SearchsModel();
        $this->lotesStkModel = new LotesStockModel();
        $this->stockBodLib = new StockBodegaLib();
        $this->reservasLib = new ReservasLib();
        $this->ventasCart = new VentasCartLib();
        $this->ventasLib = new VentasLib();
        $this->ventasFinanzasLib = new VentasFinanzasLib();
        $this->ventasAsientosLib = new VentasAsientosLib();
    }

    public function nuevaVenta() {

        $view = $this->parametrosIndex();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($view);
        } else {
            return view($this->dirTemplate . '\dashboard', $view);
        }
    }

    public function indexEdit(int $ventaId) {
        $view = $this->parametrosIndex($ventaId);
        return view($this->dirTemplate . '\dashboard', $view);
    }

    public function parametrosIndex($ventaId = null) {

        $this->user->validateSession();

        $data['title'] = empty($ventaId) ? "Nueva Venta" : "Actualizar Venta";
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['listaClientes'] = $this->ccm->getData('cc_clientes', ['clie_estado' => 1], 'id, clie_dni, clie_razon_social, clie_direccion, clie_telefono, clie_email, clie_dias_credito, clie_cupo_credito');
        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaCentroCostos'] = $this->ccm->getData('cc_centroscosto', ['cc_estado' => 1], 'id, cc_nombre, cc_facturacion_elect');
        $data['listaTiposComprobantes'] = array_values(array_filter(
                        $this->ccm->getData('cc_tipos_comprobante', ['comp_estado' => 1], 'id, comp_codigo, comp_nombre'),
                        static fn($comprobante) => in_array((string) $comprobante->comp_codigo, ['01', '02'], true)
        ));
        $data['listaPuntosEmision'] = $this->ventasModel->getPuntosEmisionUsuario((int) $this->user->id, ['01', '02']);

        if (empty($data['listaPuntosEmision'])) {
            $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
            $send['view'] = $this->viewSinPuntosEmision();

            return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
        }

        $data['bodegaMainUsuario'] = $this->user->bodega_main ?? bodegaMain($this->user->id);
        $data['listaTipoVenta'] = $this->ccm->getData('cc_tipo_venta', ['tv_estado' => 1], 'id, tv_nombre, tv_codigo');
        $data['listaFormasPago'] = $this->ccm->getData('cc_formas_pago', ['fp_estado' => 1], 'cod, fp_nombre');
        $data['listaFormasPagoSri'] = $this->ccm->getData('cc_formas_pago_sri', ['fp_estado' => 1], 'codigo, fp_nombre_sri');
        $data['listaCuentasContables'] = $this->ccm->getData('cc_cuenta_contabledet', ['ctad_estado' => 1], 'ctad_codigo, ctad_nombre_cuenta, CONCAT_WS(" ",ctad_codigo,ctad_nombre_cuenta)cuenta ');
        $data['listaBancos'] = $this->ccm->getData('cc_bancos_list', ['banc_estado' => 1], 'id codigo, banc_nombre nombre, banc_tipo');
        $data['permitirCambioPrecio'] = $this->user->validatePermisos('permitir_cambio_precio', $this->user->id);
        $data['dataVenta'] = null;
        $data['dataCliente'] = null;

        if (!empty($ventaId)) {
            $data['dataVenta'] = $this->ccm->getData('cc_ventas', ['id' => $ventaId, 'fk_proyecto' => getProyectoId()], '*', null, 1);

            if (!$data['dataVenta']) {
                $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
                $send['view'] = '<div class="alert alert-warning m-3">No se encontro la venta solicitada.</div>';
                return $send;
            }

            if ($data['dataVenta']->ven_estado !== 'BORRADOR') {
                $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
                $send['view'] = '<div class="alert alert-warning m-3">Solo se pueden modificar ventas en BORRADOR.</div>';
                return $send;
            }

            $data['dataCliente'] = $this->ccm->getData('cc_clientes', ['id' => (int) $data['dataVenta']->fk_cliente], '*', null, 1);
        }

        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewNewVenta', $data);

        return $send;
    }

    public function loadVentaEdit(int $ventaId) {

        $respuesta = $this->loadDataVentaCart($ventaId);

        return $this->response->setJSON([
                    'status' => $respuesta['status'],
                    'msg' => $respuesta['status'] === 'success' ? 'ok' : $respuesta['msg'],
                    'redirect' => $respuesta['status'] === 'success' ? site_url('ventas/indexEdit/' . $ventaId) : null,
        ]);
    }

    private function loadDataVentaCart(int $ventaId): array {

        $venta = $this->ventasModel->getDataDetalle($ventaId);

        if (!$venta) {
            return [
                'status' => 'error',
                'msg' => 'No se encontro la venta solicitada.',
            ];
        }

        if ($venta->ven_estado !== 'BORRADOR') {
            return [
                'status' => 'error',
                'msg' => 'Solo se pueden modificar ventas en BORRADOR.',
            ];
        }

        $this->ventasCart->destroy();

        try {
            foreach ($venta->detalle as $detalle) {
                $producto = $this->searchModel->searchProductoData((string) $detalle->fk_producto);
                $listaPrecios = $producto ? $this->getListaPreciosVentaProducto($producto) : [];
                $precioSeleccionado = $this->getPrecioSeleccionadoVentaPorValor($listaPrecios, (float) $detalle->vend_precio_bruto);
                $stock = $this->getStockProductoBodega((int) $detalle->fk_producto, (int) $detalle->fk_bodega);
                $lotesStock = [];

                if ((int) $detalle->prod_ctrllote === 1) {
                    $lotesStock = $this->lotesStkModel->getLotesStock((int) $detalle->fk_producto, (int) $detalle->fk_bodega) ?: [];

                    foreach ($lotesStock as $lote) {
                        $reservas = $this->reservasLib->getReservasProductoLote((int) $detalle->fk_producto, (int) $detalle->fk_bodega, (int) $lote->fk_lote);
                        $lote->stockLote = $lote->stbl_stock - $reservas['reserva'];
                    }
                }

                $item = [
                    'id' => (int) $detalle->fk_producto,
                    'qty' => (float) $detalle->vend_cantidad,
                    'codigo' => $detalle->prod_codigo,
                    'name' => $detalle->prod_nombre,
                    'unidadMedida' => $detalle->um_nombre_corto,
                    'price' => (float) $detalle->vend_precio_bruto,
                    'listaPrecios' => $listaPrecios,
                    'precioSeleccionado' => $precioSeleccionado,
                    'stock' => (float) $stock,
                    'ivaPorcent' => (float) $detalle->vend_impt_porcentaje,
                    'icePorcent' => 0,
                    'impuestoSelect' => (int) $detalle->fk_impuesto_tarifa,
                    'codigoImpuestoSelect' => (int) $detalle->vend_impt_codigo,
                    'detalleImpuestoSelect' => $detalle->impuesto_detalle,
                    'tipoDescuento' => (float) $detalle->vend_descuento_porcentaje > 0 ? 'PORCENTAJE' : 'VALOR',
                    'descuento' => (float) $detalle->vend_descuento_porcentaje > 0 ? (float) $detalle->vend_descuento_porcentaje : (float) $detalle->vend_descuento_valor,
                    'discountPercent' => (float) $detalle->vend_descuento_porcentaje,
                    'discountValue' => (float) $detalle->vend_descuento_valor,
                    'tieneLote' => (int) $detalle->prod_ctrllote,
                    'lotes' => $lotesStock,
                    'idLote' => !empty($detalle->fk_lote) ? (int) $detalle->fk_lote : null,
                    'idBodega' => (int) $detalle->fk_bodega,
                    'permitirDuplicados' => $venta->ven_items_duplicados,
                    'servicio' => (int) $detalle->prod_isservicio,
                    'irbpnrUnitario' => 0,
                    'ctaContableProducto' => $detalle->vend_cta_venta,
                ];

                $this->ventasCart->insert($item);
            }

            return ['status' => 'success', 'msg' => ''];
        } catch (\Throwable $e) {
            $this->ventasCart->destroy();

            return [
                'status' => 'error',
                'msg' => 'No se pudo cargar el borrador: ' . $e->getMessage(),
            ];
        }
    }

    private function viewSinPuntosEmision(): string {

        return '
            <div class="container-fluid">
                <div class="card card-system card-outline">
                    <div class="card-header">
                        <h6 class="mb-0 text-system fw-bold">
                            <i class="far fa-cash-register"></i> VENTAS / Nueva Venta
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-0">
                            <h5 class="mb-2"><i class="fas fa-exclamation-triangle me-2"></i>No tiene puntos de venta asignados</h5>
                            <p class="mb-0">Solicite al administrador que le asigne un punto de emisión activo para poder registrar ventas.</p>
                        </div>
                    </div>
                </div>
            </div>';
    }

    public function insertProduct() {

        $dataPost = json_decode(file_get_contents('php://input'));

        $idProd = $dataPost->id ?? null;
        $cantidad = $dataPost->qty ?? 1;
        $permitirDuplicados = $dataPost->permitirDuplicados ?? false;
        $bodegaId = $dataPost->bodegaId ?? null;

        if (empty($idProd)) {
            return $this->responseSetJSON('warning', 'No puede agregarse un producto nulo.');
        }

        if (empty($bodegaId)) {
            return $this->responseSetJSON('warning', 'Seleccione una bodega antes de agregar productos.');
        }

        $dataProducto = $this->searchModel->searchProductoData($idProd);

        if (!$dataProducto) {
            return $this->responseSetJSON('warning', 'Producto no encontrado o inactivo.');
        }

        $listaPrecios = $this->getListaPreciosVentaProducto($dataProducto);
        $precioSeleccionado = $this->getPrecioSeleccionadoVenta($listaPrecios);

        if (!$precioSeleccionado) {
            return $this->responseSetJSON('warning', 'El producto ' . $dataProducto->prod_nombre . ' no tiene PVP ni costo disponible para vender.');
        }

        $stock = $this->getStockProductoBodega((int) $dataProducto->id, (int) $bodegaId);

        if ((int) $dataProducto->prod_isservicio === 0 && (float) $stock <= 0) {
            return $this->responseSetJSON('warning', 'El producto ' . $dataProducto->prod_nombre . ' no tiene stock disponible en la bodega seleccionada.');
        }

        $lotesStock = [];
        if ((int) $dataProducto->prod_ctrllote === 1) {
            $lotesStock = $this->lotesStkModel->getLotesStock((int) $dataProducto->id, (int) $bodegaId);

            if (!$lotesStock) {
                return $this->responseSetJSON('warning', 'El producto ' . $dataProducto->prod_nombre . ' maneja lotes, pero no tiene lotes disponibles en la bodega seleccionada.');
            }

            foreach ($lotesStock as $lote) {
                $reservas = $this->reservasLib->getReservasProductoLote((int) $dataProducto->id, (int) $bodegaId, (int) $lote->fk_lote);
                $lote->stockLote = $lote->stbl_stock - $reservas['reserva'];
            }
        }

        $tarifas = $this->prodModel->getImpuestoTarifa((int) $dataProducto->id);
        $ivaPorcent = 0;
        $icePorcent = 0;
        $porcentajeSelect = 0;
        $codigoPorcentajeSelect = 0;
        $detallePorcentajeSelect = '';

        if ($tarifas) {
            foreach ($tarifas as $tarifa) {
                if (!isset($tarifa->impt_porcentage)) {
                    continue;
                }

                switch ((int) $tarifa->fk_impuesto) {
                    case 1:
                        $ivaPorcent = (float) $tarifa->impt_porcentage;
                        $porcentajeSelect = (int) $tarifa->id;
                        $codigoPorcentajeSelect = (int) $tarifa->impt_codigo;
                        $detallePorcentajeSelect = $tarifa->impt_detalle;
                        break;

                    case 2:
                        $icePorcent = (float) $tarifa->impt_porcentage;
                        break;
                }
            }
        }

        $item = [
            'id' => (int) $dataProducto->id,
            'qty' => (float) $cantidad,
            'codigo' => $dataProducto->prod_codigo,
            'name' => $dataProducto->prod_nombre,
            'unidadMedida' => $dataProducto->um_nombre_corto,
            'price' => (float) $precioSeleccionado->valor,
            'listaPrecios' => $listaPrecios,
            'precioSeleccionado' => $precioSeleccionado,
            'stock' => (float) $stock,
            'ivaPorcent' => $ivaPorcent,
            'icePorcent' => $icePorcent,
            'impuestoSelect' => $porcentajeSelect,
            'codigoImpuestoSelect' => $codigoPorcentajeSelect,
            'detalleImpuestoSelect' => $detallePorcentajeSelect,
            'tipoDescuento' => 'VALOR',
            'descuento' => 0,
            'discountPercent' => 0,
            'discountValue' => 0,
            'tieneLote' => $dataProducto->prod_ctrllote,
            'lotes' => $lotesStock,
            'idLote' => isset($lotesStock[0]->fk_lote) ? (int) $lotesStock[0]->fk_lote : null,
            'idBodega' => (int) $bodegaId,
            'permitirDuplicados' => $permitirDuplicados,
            'servicio' => $dataProducto->prod_isservicio,
            'irbpnrUnitario' => $dataProducto->prod_tiene_irbpnr === '1' ? (float) getImpuestoIrbpnr() : 0,
            'ctaContableProducto' => $dataProducto->fk_cuentacontableventas ?? null,
        ];

        try {
            $this->ventasCart->insert($item);
            return $this->responseSetJSON('success', 'Producto agregado correctamente.');
        } catch (\Throwable $e) {
            return $this->responseSetJSON('warning', $e->getMessage());
        }
    }

    public function showDetailCart(int $key = 0) {
        
        $cartContent = array_reverse(array_values($this->ventasCart->getContent() ?? []));

        $respuesta = [
            'cartContent' => $cartContent,
            'totalArticles' => $this->ventasCart->totalArticles(),
            'totalItems' => count($cartContent),
            'totalSubtotalBruto' => $this->ventasCart->totalSubtotalBruto(),
            'totalBienes' => $this->ventasCart->totalBienesNeto(),
            'totalServicios' => $this->ventasCart->totalServiciosNeto(),
            'totalBienesBruto' => $this->ventasCart->totalBienesBruto(),
            'totalServiciosBruto' => $this->ventasCart->totalServiciosBruto(),
            'tarifCeroBruto' => $this->ventasCart->tarifCeroBruto(),
            'tarifCeroNeto' => $this->ventasCart->tarifCeroNeto(),
            'tarifIvaBruto' => $this->ventasCart->tarifIvaBruto(),
            'tarifIvaNeto' => $this->ventasCart->tarifIvaNeto(),
            'tarifNoObjetoNeto' => $this->ventasCart->totalnoObjetoImpuestos(),
            'tarifExcentoNeto' => $this->ventasCart->totalExcentoIva(),
            'baseIva' => $this->ventasCart->totalBaseIva(),
            'totalIva' => $this->ventasCart->totalIva(),
            'totalIce' => $this->ventasCart->totalIce(),
            'totalIrbpnr' => $this->ventasCart->totalIrbpnr(),
            'totalDescuentoGlobal' => $this->ventasCart->totalDescuentoGlobal(),
            'totalDescuentoItems' => $this->ventasCart->totalDescuentoItems(),
            'totalSubtotalNeto' => $this->ventasCart->totalSubtotalNeto(),
            'totalGeneral' => $this->ventasCart->totalGeneral(),
            'totalRecargo' => $this->ventasCart->totalRecargo(),
            'totalServiciosAdc' => $this->ventasCart->totalServiciosAdc(),
            'basesImpuesto' => $this->ventasCart->getImpuestos(),
        ];

        return $key ? $respuesta : $this->response->setJSON($respuesta);
    }

    public function updateProduct() {

        $dataPost = json_decode(file_get_contents('php://input'));

        if (!$dataPost || empty($dataPost->rowid)) {
            return $this->responseSetJSON('warning', 'No se recibio el item a actualizar.');
        }

        if ((float) ($dataPost->qty ?? 0) <= 0) {
            return $this->responseSetJSON('warning', 'La cantidad debe ser mayor a cero.');
        }

        $rowId = (string) $dataPost->rowid;
        $cartContent = $this->ventasCart->getContent() ?? [];

        if (!isset($cartContent[$rowId])) {
            return $this->responseSetJSON('warning', 'El item que intenta actualizar ya no existe en el carrito.');
        }

        $itemCartActual = $cartContent[$rowId];

        if ((int) ($dataPost->id ?? 0) !== (int) ($itemCartActual['id'] ?? 0)) {
            return $this->responseSetJSON('warning', 'No se puede cambiar el producto de un item ya cargado.');
        }

        if ((int) ($dataPost->idBodega ?? 0) !== (int) ($itemCartActual['idBodega'] ?? 0)) {
            return $this->responseSetJSON('warning', 'No se puede cambiar la bodega de un item ya cargado.');
        }

        $dataProducto = $this->searchModel->searchProductoData((string)$dataPost->id);

        if (!$dataProducto) {
            return $this->responseSetJSON('warning', 'Producto no encontrado o inactivo.');
        }

        $listaPrecios = $this->getListaPreciosVentaProducto($dataProducto);
        $permiteCambioPrecio = $this->user->validatePermisos('permitir_cambio_precio', $this->user->id);
        $precioSeleccionado = $permiteCambioPrecio ? $this->validarPrecioSeleccionadoItem($dataPost, $listaPrecios) : ($itemCartActual['precioSeleccionado'] ?? null);

        if (!$precioSeleccionado) {
            return $this->responseSetJSON('warning', 'Seleccione un precio válido para el producto.');
        }

        $bodegaId = (int) ($dataPost->idBodega ?? 0);
        $idLote = ((int) $dataProducto->prod_ctrllote === 1) ? (int) ($dataPost->idLote ?? 0) : null;

        if ($bodegaId <= 0) {
            return $this->responseSetJSON('warning', 'Seleccione una bodega valida para actualizar el producto.');
        }

        if ((int) $dataProducto->prod_ctrllote === 1 && empty($idLote)) {
            return $this->responseSetJSON('warning', 'El producto ' . $dataProducto->prod_nombre . ' tiene control de lotes, seleccione uno por favor.');
        }

        if ((int) $dataProducto->prod_isservicio === 0) {
            $ventaId = empty($dataPost->ventaId) ? null : $dataPost->ventaId; //Cuando el updateProduct se ejecutado desde una actualización de una venta en borrrador hacemos uso del ID del ajuste

            $validarStock = $this->stockBodLib->validarStockDisponible((int) $dataProducto->id, $bodegaId, (float) ($dataPost->qty ?? 0), $this->transaccionCod, $ventaId, $idLote);

            if ($validarStock['status'] !== 'success') {
                return $this->responseSetJSON($validarStock['status'], $validarStock['msg']);
            }
        }

        $precioVenta = (float) (is_array($precioSeleccionado) ? $precioSeleccionado['valor'] : $precioSeleccionado->valor);

        if ($precioVenta <= 0) {
            return $this->responseSetJSON('warning', 'El precio de venta debe ser mayor a cero.');
        }

        $descuento = (float) ($dataPost->descuento ?? 0);
        $discountValue = 0;
        $discountPercent = 0;
        $tipoDescuento = $dataPost->tipoDescuento ?? 'VALOR';

        if ($descuento < 0) {
            return $this->responseSetJSON('warning', 'El descuento no puede ser negativo.');
        }

        if ($tipoDescuento === 'VALOR') {
            if ($descuento > $precioVenta) {
                return $this->responseSetJSON('warning', 'El descuento no puede superar el precio unitario del producto.');
            }

            $discountValue = $descuento;

            if ($precioVenta > 0) {
                $discountPercent = ($discountValue / $precioVenta) * 100;
            }
        } else {
            if ($descuento > 100) {
                return $this->responseSetJSON('warning', 'El descuento porcentual no puede superar el 100%.');
            }

            $discountPercent = $descuento;
            $discountValue = ($precioVenta * $discountPercent) / 100;
        }

        $item = [
            'id' => (int) $dataProducto->id,
            'qty' => (float) $dataPost->qty,
            'codigo' => $itemCartActual['codigo'] ?? $dataProducto->prod_codigo,
            'name' => $itemCartActual['name'] ?? $dataProducto->prod_nombre,
            'unidadMedida' => $itemCartActual['unidadMedida'] ?? $dataProducto->um_nombre_corto,
            'price' => $precioVenta,
            'listaPrecios' => $listaPrecios,
            'precioSeleccionado' => $precioSeleccionado,
            'stock' => $itemCartActual['stock'] ?? 0,
            'ivaPorcent' => $itemCartActual['ivaPorcent'] ?? 0,
            'icePorcent' => $itemCartActual['icePorcent'] ?? 0,
            'impuestoSelect' => $itemCartActual['impuestoSelect'] ?? 0,
            'codigoImpuestoSelect' => $itemCartActual['codigoImpuestoSelect'] ?? 0,
            'detalleImpuestoSelect' => $itemCartActual['detalleImpuestoSelect'] ?? '',
            'tipoDescuento' => $tipoDescuento,
            'descuento' => $descuento,
            'discountPercent' => $discountPercent,
            'discountValue' => $discountValue,
            'tieneLote' => $itemCartActual['tieneLote'] ?? $dataProducto->prod_ctrllote,
            'lotes' => $itemCartActual['lotes'] ?? [],
            'idLote' => $idLote,
            'idBodega' => $bodegaId,
            'permitirDuplicados' => $itemCartActual['permitirDuplicados'] ?? false,
            'servicio' => $itemCartActual['servicio'] ?? $dataProducto->prod_isservicio,
            'irbpnrUnitario' => $itemCartActual['irbpnrUnitario'] ?? 0,
            'ctaContableProducto' => $itemCartActual['ctaContableProducto'] ?? ($dataProducto->fk_cuentacontableventas ?? null),
        ];

        try {
            $this->ventasCart->update($item, $rowId);
            return $this->responseSetJSON('success', 'Item actualizado correctamente.');
        } catch (\Throwable $e) {
            return $this->responseSetJSON('warning', $e->getMessage());
        }
    }

    public function deleteProduct($rowId) {

        try {
            $this->ventasCart->removeItem((string) $rowId);
            return $this->responseSetJSON('success', 'Producto eliminado correctamente.');
        } catch (\Throwable $e) {
            return $this->responseSetJSON('warning', $e->getMessage());
        }
    }

    public function saveVenta() {

        $json = $this->request->getPost('data');

        if (!is_string($json) || trim($json) === '') {
            return $this->responseSetJSON('warning', 'No se recibieron datos validos para procesar la venta.');
        }

        try {
            $dataPostVenta = json_decode($json, false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return $this->responseSetJSON('warning', 'Los datos de la venta no tienen un formato valido.');
        }

        $cartData = json_decode(json_encode($this->showDetailCart(1)));
        $validacion = $this->validarCamposVenta($dataPostVenta, $cartData);

        if ($validacion['status']) {
            return $this->responseSetJSON('warning', $validacion['msg']);
        }

        $venta = $dataPostVenta->venta;

        $this->db->transBegin();

        try {
            $ventaId = $this->ventasLib->guardarVenta($cartData, $dataPostVenta);

            foreach ($cartData->cartContent as $item) {
                $this->ventasLib->guardarDetalleVenta($ventaId, $item, (int) $venta->venBodega, (int) $venta->venCentroCosto);

                if ($venta->venEstado === 'ARCHIVADO') {
                    $this->ventasLib->generarKardexItemVenta($ventaId, $item, (int) $venta->venBodega, (string) $venta->venFechaEmision);
                }
            }

            $this->ventasLib->guardarBasesImpuesto($ventaId, $dataPostVenta->basesImpuestos ?? []);

            if ($venta->venEstado === 'ARCHIVADO') {
                $this->ventasLib->guardarFormasPagoAts($ventaId, $dataPostVenta->ats ?? (object) []);

                $cxcId = $this->ventasFinanzasLib->crearCuentaPorCobrar($ventaId);

                if (($dataPostVenta->pago->tipoPago ?? '') === 'CONTADO') {
                    $cobroId = $this->ventasFinanzasLib->guardarCobroContado($cxcId, $dataPostVenta->pago ?? (object) []);
                } else {
                    $this->ventasFinanzasLib->guardarCuotas($cxcId, $dataPostVenta->cuotas ?? []);
                }

                $this->ventasAsientosLib->generarAsientosVenta($ventaId);

                if (($dataPostVenta->pago->tipoPago ?? '') === 'CONTADO') {
                    $this->ventasAsientosLib->generarAsientoCobroContado((int) $cobroId);
                }

                $this->ventasFinanzasLib->incrementarSecuencialPuntoVenta((int) $venta->venPuntoEmision);
            }

            $secuencial = $this->ccm->getValueWhere('cc_ventas', ['id' => $ventaId, 'fk_proyecto' => getProyectoId()], 'ven_secuencial');

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return $this->responseSetJSON('error', 'No se pudo registrar la venta.');
            }

            $this->db->transCommit();
            $this->ventasCart->destroy();

            $mensaje = $venta->venEstado === 'ARCHIVADO' ? 'Venta registrada correctamente.' : 'Venta guardada como borrador.';

            return $this->responseSetJSON('success', $mensaje, ['id' => $ventaId, 'ven_secuencial' => $secuencial]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return $this->responseSetJSON('error', 'Error al registrar la venta: ' . $e->getMessage());
        }
    }

    public function updateVenta() {

        $json = $this->request->getPost('data');

        if (!is_string($json) || trim($json) === '') {
            return $this->responseSetJSON('warning', 'No se recibieron datos validos para actualizar la venta.');
        }

        try {
            $dataPostVenta = json_decode($json, false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return $this->responseSetJSON('warning', 'Los datos de la venta no tienen un formato valido.');
        }

        $ventaId = (int) ($dataPostVenta->idVenta ?? 0);

        if ($ventaId <= 0) {
            return $this->responseSetJSON('warning', 'No se recibio la venta que se desea actualizar.');
        }

        $cartData = json_decode(json_encode($this->showDetailCart(1)));
        $validacion = $this->validarCamposVenta($dataPostVenta, $cartData, $ventaId);

        if ($validacion['status']) {
            return $this->responseSetJSON('warning', $validacion['msg']);
        }

        $ventaActual = $this->ccm->getData('cc_ventas', ['id' => $ventaId, 'fk_proyecto' => getProyectoId()], 'id, ven_secuencial, ven_estado', null, 1);

        if (!$ventaActual) {
            return $this->responseSetJSON('warning', 'La venta no se encuentra registrada.');
        }

        if ($ventaActual->ven_estado !== 'BORRADOR') {
            return $this->responseSetJSON('warning', 'Solo se pueden actualizar ventas en estado borrador.');
        }

        $venta = $dataPostVenta->venta;

        $this->db->transBegin();

        try {
            $actualizado = $this->ventasLib->actualizarVenta($ventaId, $cartData, $dataPostVenta);

            if (!$actualizado) {
                throw new \RuntimeException('No se pudo actualizar la cabecera de la venta.');
            }

            $this->ccm->eliminar('cc_ventas_det', ['fk_venta' => $ventaId, 'fk_proyecto' => getProyectoId()]);
            $this->ccm->eliminar('cc_ventas_bases_impuesto', ['fk_venta' => $ventaId, 'fk_proyecto' => getProyectoId()]);

            foreach ($cartData->cartContent as $item) {
                $this->ventasLib->guardarDetalleVenta($ventaId, $item, (int) $venta->venBodega, (int) $venta->venCentroCosto);

                if ($venta->venEstado === 'ARCHIVADO') {
                    $this->ventasLib->generarKardexItemVenta($ventaId, $item, (int) $venta->venBodega, (string) $venta->venFechaEmision);
                }
            }

            $this->ventasLib->guardarBasesImpuesto($ventaId, $dataPostVenta->basesImpuestos ?? []);

            if ($venta->venEstado === 'ARCHIVADO') {
                $this->ventasLib->guardarFormasPagoAts($ventaId, $dataPostVenta->ats ?? (object) []);

                $cxcId = $this->ventasFinanzasLib->crearCuentaPorCobrar($ventaId);

                if (($dataPostVenta->pago->tipoPago ?? '') === 'CONTADO') {
                    $cobroId = $this->ventasFinanzasLib->guardarCobroContado($cxcId, $dataPostVenta->pago ?? (object) []);
                } else {
                    $this->ventasFinanzasLib->guardarCuotas($cxcId, $dataPostVenta->cuotas ?? []);
                }

                $this->ventasAsientosLib->generarAsientosVenta($ventaId);

                if (($dataPostVenta->pago->tipoPago ?? '') === 'CONTADO') {
                    $this->ventasAsientosLib->generarAsientoCobroContado((int) $cobroId);
                }

                $this->ventasFinanzasLib->incrementarSecuencialPuntoVenta((int) $venta->venPuntoEmision);
            }

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('La transaccion de actualizacion fallo.');
            }

            $this->db->transCommit();
            $this->ventasCart->destroy();

            $mensaje = $venta->venEstado === 'ARCHIVADO' ? 'Venta registrada correctamente.' : 'Borrador de venta actualizado correctamente.';

            return $this->responseSetJSON('success', $mensaje, [
                        'id' => $ventaId,
                        'ven_secuencial' => $ventaActual->ven_secuencial,
                        'redirect' => site_url('ventas/gestionVentas'),
            ]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return $this->responseSetJSON('error', 'Error al actualizar la venta: ' . $e->getMessage());
        }
    }

    private function validarCamposVenta(object $dataPostVenta, object $cartData, ?int $ventaIdExcluir = null): array {

        if (empty($dataPostVenta->venta)) {
            return [
                'status' => true,
                'msg' => 'No se recibio la cabecera de la venta.',
            ];
        }

        $venta = $dataPostVenta->venta;
        $campos = [
            'venTipoComprobante' => 'Tipo de comprobante',
            'venPuntoEmision' => 'Punto de emision',
            'venNumeroEstablecimiento' => 'Establecimiento',
            'venNumeroEmision' => 'Punto de emision del comprobante',
            'venNumeroComprobante' => 'Numero de comprobante',
            'venFechaEmision' => 'Fecha de emision',
            'venCliente' => 'Cliente',
            'venBodega' => 'Bodega',
            'venCentroCosto' => 'Centro de costo',
            'venTipoVenta' => 'Tipo de venta',
            'venEstado' => 'Estado',
        ];

        foreach ($campos as $campo => $nombre) {
            if (!property_exists($venta, $campo) || $venta->$campo === null || (is_string($venta->$campo) && trim($venta->$campo) === '')) {
                return [
                    'status' => true,
                    'msg' => "El campo {$nombre} es obligatorio.",
                ];
            }
        }

        if (!in_array((string) $venta->venTipoComprobante, ['01', '02'], true)) {
            return [
                'status' => true,
                'msg' => 'El tipo de comprobante seleccionado no esta permitido para ventas.',
            ];
        }

        if (!in_array((string) $venta->venEstado, ['BORRADOR', 'ARCHIVADO'], true)) {
            return [
                'status' => true,
                'msg' => 'El estado de la venta no es valido.',
            ];
        }

        $tipoVenta = $this->ccm->getData('cc_tipo_venta', ['id' => (int) $venta->venTipoVenta, 'tv_estado' => 1], 'id', null, 1);

        if (!$tipoVenta) {
            return [
                'status' => true,
                'msg' => 'El tipo de venta seleccionado no es valido o se encuentra inactivo.',
            ];
        }

        if (empty($cartData->cartContent)) {
            return [
                'status' => true,
                'msg' => 'Debe agregar al menos un item para guardar la venta.',
            ];
        }

        $requiereFormaPagoAts = $venta->venEstado === 'ARCHIVADO' && (float) $cartData->totalGeneral >= (float) getSettings('VALOR_MAXIMO_ANEXO_ATS_SRI');

        if ($requiereFormaPagoAts) {
            $formasPagoAts = (array) ($dataPostVenta->ats->formasPago ?? []);

            if (empty($formasPagoAts)) {
                return [
                    'status' => true,
                    'msg' => 'Debe seleccionar al menos una forma de pago ATS.',
                ];
            }
        }

        if ($venta->venEstado === 'ARCHIVADO') {
            $validacionPago = $this->validarDatosPagoVenta($dataPostVenta->pago ?? (object) [], (float) $cartData->totalGeneral);

            if ($validacionPago['status']) {
                return $validacionPago;
            }
        }

        $puntoEmision = $this->validarPuntoEmisionVenta($venta);

        if ($puntoEmision['status']) {
            return $puntoEmision;
        }

        $numeroComprobante = str_pad(trim((string) $venta->venNumeroComprobante), 9, '0', STR_PAD_LEFT);
        $ventaDuplicada = $this->ccm->getData('cc_ventas', [
            'fk_proyecto' => getProyectoId(),
            'ven_tipo_comprobante_cod' => (string) $venta->venTipoComprobante,
            'ven_numero_establecimiento' => trim((string) $venta->venNumeroEstablecimiento),
            'ven_numero_emision' => trim((string) $venta->venNumeroEmision),
            'ven_numero_comprobante' => $numeroComprobante,
        ], 'id, ven_secuencial, ven_estado', null, 1);

        if ($ventaDuplicada && (int) $ventaDuplicada->id !== (int) $ventaIdExcluir) {
            return [
                'status' => true,
                'msg' => 'Ya existe una venta registrada con el mismo tipo y numero de comprobante. Venta #' . str_pad((string) $ventaDuplicada->ven_secuencial, 5, '0', STR_PAD_LEFT) . ' (' . $ventaDuplicada->ven_estado . ').',
            ];
        }

        foreach ($cartData->cartContent as $item) {
            $nombreProducto = $item->name ?? 'producto';

            if ((float) ($item->qty ?? 0) <= 0) {
                return [
                    'status' => true,
                    'msg' => "Cantidad invalida para {$nombreProducto}, la cantidad debe ser mayor a 0.",
                ];
            }

            if ((float) ($item->price ?? 0) <= 0) {
                return [
                    'status' => true,
                    'msg' => "Precio invalido para {$nombreProducto}, el precio debe ser mayor a 0.",
                ];
            }

            if ((int) ($item->idBodega ?? 0) !== (int) $venta->venBodega) {
                return [
                    'status' => true,
                    'msg' => "El producto {$nombreProducto} pertenece a una bodega distinta a la del punto de emision.",
                ];
            }

            $idLote = null;
            if ((int) ($item->tieneLote ?? 0) === 1) {
                $idLote = (int) ($item->idLote ?? 0);

                if ($idLote <= 0) {
                    return [
                        'status' => true,
                        'msg' => "El producto {$nombreProducto} maneja lotes, seleccione uno antes de guardar.",
                    ];
                }
            }

            if ((int) ($item->servicio ?? 0) === 0) {
                $validarStock = $this->stockBodLib->validarStockDisponible((int) $item->id, (int) $venta->venBodega, (float) $item->qty, $this->transaccionCod, $ventaIdExcluir, $idLote);

                if ($validarStock['status'] !== 'success') {
                    return [
                        'status' => true,
                        'msg' => $validarStock['msg'],
                    ];
                }
            }
        }

        return [
            'status' => false,
            'msg' => '',
        ];
    }

    private function validarDatosPagoVenta(object $pago, float $totalVenta): array {

        if (empty($pago->tipoPago) || !in_array((string) $pago->tipoPago, ['CONTADO', 'CREDITO'], true)) {
            return [
                'status' => true,
                'msg' => 'Debe seleccionar un tipo de pago valido.',
            ];
        }

        if ((string) $pago->tipoPago === 'CONTADO') {
            if (empty($pago->formaPago)) {
                return [
                    'status' => true,
                    'msg' => 'Debe seleccionar el metodo de pago.',
                ];
            }

            if (empty($pago->cuentaContable)) {
                return [
                    'status' => true,
                    'msg' => 'Debe seleccionar la cuenta contable del cobro.',
                ];
            }

            if ((float) ($pago->valorRecibido ?? 0) < $totalVenta) {
                return [
                    'status' => true,
                    'msg' => 'El valor recibido no puede ser menor al total de la venta.',
                ];
            }

            if (empty($pago->fechaCobro)) {
                return [
                    'status' => true,
                    'msg' => 'Debe seleccionar la fecha del cobro.',
                ];
            }

            $campos = [];
            $formaPago = (string) $pago->formaPago;

            if ($formaPago === '01') {
                $campos = ['nota' => 'Debe ingresar una nota para el cobro en efectivo.'];
            } elseif ($formaPago === '02') {
                $campos = [
                    'banco' => 'Debe seleccionar el banco de la transferencia.',
                    'numeroTransferencia' => 'Debe ingresar el numero de transferencia.',
                    'fechaTransferencia' => 'Debe seleccionar la fecha de transferencia.',
                    'nota' => 'Debe ingresar una nota para la transferencia.',
                ];
            } elseif ($formaPago === '03') {
                $campos = [
                    'banco' => 'Debe seleccionar el banco del cheque.',
                    'numeroCheque' => 'Debe ingresar el numero de cheque.',
                    'fechaCheque' => 'Debe seleccionar la fecha del cheque.',
                ];
            } elseif ($formaPago === '04') {
                $campos = [
                    'marcaTarjeta' => 'Debe seleccionar la marca de la tarjeta.',
                    'loteTarjeta' => 'Debe ingresar el lote de la tarjeta.',
                    'autorizacionTarjeta' => 'Debe ingresar la autorizacion de la tarjeta.',
                    'ultimosDigitos' => 'Debe ingresar los ultimos cuatro digitos de la tarjeta.',
                    'fechaVoucher' => 'Debe seleccionar la fecha del voucher.',
                    'nota' => 'Debe ingresar una nota para el cobro con tarjeta.',
                ];
            }

            foreach ($campos as $campo => $mensaje) {
                $valor = $pago->$campo ?? null;

                if ($valor === null || (is_string($valor) && trim($valor) === '')) {
                    return [
                        'status' => true,
                        'msg' => $mensaje,
                    ];
                }
            }

            if ($formaPago === '04' && !preg_match('/^\d{4}$/', (string) ($pago->ultimosDigitos ?? ''))) {
                return [
                    'status' => true,
                    'msg' => 'Ingrese exactamente cuatro numeros para los ultimos digitos de la tarjeta.',
                ];
            }
        }

        return [
            'status' => false,
            'msg' => '',
        ];
    }

    private function validarPuntoEmisionVenta(object $venta): array {

        $puntos = $this->ventasModel->getPuntosEmisionUsuario((int) $this->user->id, [(string) $venta->venTipoComprobante]);
        $puntoSeleccionado = null;

        foreach ($puntos as $punto) {
            if ((int) $punto->id === (int) $venta->venPuntoEmision) {
                $puntoSeleccionado = $punto;
                break;
            }
        }

        if (!$puntoSeleccionado) {
            return [
                'status' => true,
                'msg' => 'Su usuario no tiene asignado el punto de emision seleccionado para este comprobante.',
            ];
        }

        if ((int) $puntoSeleccionado->pv_sec_actual > (int) $puntoSeleccionado->pv_sec_final) {
            return [
                'status' => true,
                'msg' => 'El punto de emision seleccionado ya no tiene secuenciales disponibles.',
            ];
        }

        if ((int) $puntoSeleccionado->pv_fk_bodega !== (int) $venta->venBodega) {
            return [
                'status' => true,
                'msg' => 'La bodega de la venta no corresponde al punto de emision seleccionado.',
            ];
        }

        if ((string) $puntoSeleccionado->pv_establecimiento !== trim((string) $venta->venNumeroEstablecimiento) || (string) $puntoSeleccionado->pv_emision !== trim((string) $venta->venNumeroEmision)) {
            return [
                'status' => true,
                'msg' => 'El establecimiento o punto de emision no corresponde al punto seleccionado.',
            ];
        }

        $numeroComprobante = str_pad(trim((string) $venta->venNumeroComprobante), 9, '0', STR_PAD_LEFT);
        $secuencialActual = str_pad((string) $puntoSeleccionado->pv_sec_actual, 9, '0', STR_PAD_LEFT);

        if ($numeroComprobante !== $secuencialActual) {
            return [
                'status' => true,
                'msg' => 'El numero de comprobante no corresponde al secuencial actual del punto de emision.',
            ];
        }

        return [
            'status' => false,
            'msg' => '',
        ];
    }

    public function cancelarVenta() {

        $this->ventasCart->destroy();
        return $this->responseSetJSON('success', 'Venta cancelada correctamente.');
    }

    public function anularVenta() {

        $this->user->validateSession();

        $data = $this->request->getJSON();

        if (!is_object($data)) {
            return $this->responseSetJSON('warning', 'No se recibieron datos para anular la venta.');
        }

        $ventaId = (int) ($data->ventaId ?? 0);
        $motivoAnulacion = trim((string) ($data->motivoAnulacion ?? ''));

        if ($ventaId <= 0) {
            return $this->responseSetJSON('warning', 'El identificador de la venta no es valido.');
        }

        if ($motivoAnulacion === '') {
            return $this->responseSetJSON('warning', 'Debe especificar el motivo de la anulacion.');
        }

        $venta = $this->ccm->getData('cc_ventas', ['id' => $ventaId, 'fk_proyecto' => getProyectoId()], 'id, ven_secuencial, ven_estado', null, 1);

        if (!$venta) {
            return $this->responseSetJSON('warning', 'La venta no se encuentra registrada.');
        }

        if (!in_array($venta->ven_estado, ['BORRADOR', 'ARCHIVADO'], true)) {
            return $this->responseSetJSON('warning', 'La venta ya no se encuentra en un estado que permita anular.');
        }

        if ($venta->ven_estado === 'ARCHIVADO' && !getPeriodoContable(date('Y-m-d'))) {
            return $this->responseSetJSON(
                            'error',
                            '<h5>Revise el periodo de cierre</h5><h6>No se encontro un periodo contable habil para la fecha de anulacion.</h6>'
            );
        }

        $this->db->transBegin();

        try {
            if ($venta->ven_estado === 'BORRADOR') {
                $estadoAnulado = 'ANULADA_EN_PENDIENTE';
                $anulado = $this->ventasLib->anularVentaBorrador($ventaId, $motivoAnulacion);
            } else {
                $estadoAnulado = 'ANULADA_EN_ARCHIVADA';

                $this->ventasFinanzasLib->anularCuentaPorCobrarVenta($ventaId);
                $this->ventasLib->revertirKardexVenta($ventaId);
                $this->ventasAsientosLib->anularAsientosVenta($ventaId);

                $anulado = $this->ventasLib->anularVentaArchivada($ventaId, $motivoAnulacion);
            }

            if (!$anulado || $this->db->transStatus() === false) {
                throw new \RuntimeException('No se pudo actualizar el estado de la venta, no se completo el proceso de anulacion.');
            }

            $this->db->transCommit();

            return $this->responseSetJSON('success', 'Venta #' . str_pad((string) $venta->ven_secuencial, 5, '0', STR_PAD_LEFT) . ' anulada correctamente.', [
                        'id' => $ventaId,
                        'estado' => $estadoAnulado,
            ]);
        } catch (\Throwable $e) {
            $this->db->transRollback();

            return $this->responseSetJSON('error', 'Error al anular la venta: ' . $e->getMessage());
        }
    }

    private function getStockProductoBodega(int $productoId, int $bodegaId): float {

        $stock = $this->ccm->getValueWhere('cc_stock_bodega', ['fk_producto' => $productoId, 'fk_bodega' => $bodegaId], 'stb_stock');
        return (float) ($stock ?? 0);
    }

    private function getListaPreciosVentaProducto(object $dataProducto): array {

        $precios = $this->ventasModel->getPreciosProducto((int) $dataProducto->id);

        if ($precios) {
            return array_map(static function ($precio) {
                return [
            'id' => (int) $precio->id,
            'nombre' => $precio->nombre,
            'descripcion' => $precio->descripcion,
            'valor' => (float) $precio->valor,
            'origen' => 'PVP',
            'label' => $precio->nombre . ' - $' . number_format((float) $precio->valor, 4),
                ];
            }, $precios);
        }

        $costoUltimo = (float) ($dataProducto->prod_costoultimo ?? 0);
        if ($costoUltimo > 0) {
            return [[
            'id' => 0,
            'nombre' => 'Costo último',
            'descripcion' => 'Precio tomado desde costo último por falta de PVP',
            'valor' => $costoUltimo,
            'origen' => 'COSTO_ULTIMO',
            'label' => 'Costo último - $' . number_format($costoUltimo, 4),
            ]];
        }

        $costoPromedio = (float) ($dataProducto->prod_costopromedio ?? 0);
        if ($costoPromedio > 0) {
            return [[
            'id' => -1,
            'nombre' => 'Costo promedio',
            'descripcion' => 'Precio tomado desde costo promedio por falta de PVP',
            'valor' => $costoPromedio,
            'origen' => 'COSTO_PROMEDIO',
            'label' => 'Costo promedio - $' . number_format($costoPromedio, 4),
            ]];
        }

        return [];
    }

    private function getPrecioSeleccionadoVenta(array $listaPrecios): ?object {

        if (!$listaPrecios) {
            return null;
        }

        foreach ($listaPrecios as $precio) {
            if ((int) $precio['id'] === 1) {
                return (object) $precio;
            }
        }

        return (object) $listaPrecios[0];
    }

    private function getPrecioSeleccionadoVentaPorValor(array $listaPrecios, float $precioVenta): ?object {

        foreach ($listaPrecios as $precio) {
            if (abs((float) ($precio['valor'] ?? 0) - $precioVenta) < 0.0001) {
                return (object) $precio;
            }
        }

        return $this->getPrecioSeleccionadoVenta($listaPrecios);
    }

    private function validarPrecioSeleccionadoItem(object|array $item, array $listaPrecios): ?array {

        $precioSeleccionado = is_array($item) ? ($item['precioSeleccionado'] ?? null) : ($item->precioSeleccionado ?? null);
        $precioId = is_array($precioSeleccionado) ? (int) ($precioSeleccionado['id'] ?? 0) : (int) ($precioSeleccionado->id ?? 0);
        $origen = is_array($precioSeleccionado) ? ($precioSeleccionado['origen'] ?? '') : ($precioSeleccionado->origen ?? '');

        foreach ($listaPrecios as $precio) {
            $precio = (array) $precio;

            if ((int) ($precio['id'] ?? 0) === $precioId && (string) ($precio['origen'] ?? '') === (string) $origen) {
                return $precio;
            }
        }

        return null;
    }

    public function responseSetJSON(string $status, string $mensaje, mixed $data = null) {
        return $this->response->setJSON([
                    'status' => $status,
                    'msg' => $mensaje,
                    'data' => $data,
        ]);
    }
}
