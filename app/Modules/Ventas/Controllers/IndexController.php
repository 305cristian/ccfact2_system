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
    }

    public function nuevaVenta() {

        $this->user->validateSession();

        $data['title'] = "Nueva Venta";
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
        $data['listaFormasPagoSri'] = $this->ccm->getData('cc_formas_pago_sri', ['fp_estado' => 1], 'codigo, fp_nombre_sri');
        $data['permitirCambioPrecio'] = $this->user->validatePermisos('permitir_cambio_precio', $this->user->id);
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewNewVenta', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
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

        $cartContent = array_values($this->ventasCart->getContent() ?? []);

        $respuesta = [
            'cartContent' => $cartContent,
            'totalArticles' => $this->ventasCart->totalArticles(),
            'totalItems' => count($cartContent),
            'totalSubtotalBruto' => $this->ventasCart->totalSubtotalBruto(),
            'totalBienes' => $this->ventasCart->totalBienesNeto(),
            'totalServicios' => $this->ventasCart->totalServiciosNeto(),
            'tarifCeroNeto' => $this->ventasCart->tarifCeroNeto(),
            'tarifIvaNeto' => $this->ventasCart->tarifIvaNeto(),
            'tarifNoObjetoNeto' => $this->ventasCart->totalnoObjetoImpuestos(),
            'tarifExcentoNeto' => $this->ventasCart->totalExcentoIva(),
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

    public function cancelarVenta() {

        $this->ventasCart->destroy();
        return $this->responseSetJSON('success', 'Venta cancelada correctamente.');
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
