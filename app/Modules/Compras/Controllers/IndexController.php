<?php

namespace Modules\Compras\Controllers;

use App\Controllers\BaseController;
use Modules\Comun\Models\ProductoModel;
use Modules\Comun\Models\SearchsModel;
use Modules\Compras\Libraries\ComprasCartLib;
use Modules\Compras\Libraries\ComprasLib;
use Modules\Compras\Libraries\ComprasFinanzasLib;
use Modules\Compras\Libraries\ComprasAsientosLib;
use Modules\Comun\Libraries\CuentasConfigLib;
use Modules\Compras\Models\ComprasModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
    protected ComprasCartLib $comprasCart;
    protected ComprasLib $comprasLib;
    protected ComprasFinanzasLib $comprasFinanzasLib;
    protected ComprasAsientosLib $comprasAsientosLib;
    protected ProductoModel $prodModel;
    protected SearchsModel $searchModel;
    protected CuentasConfigLib $cuentasConfigLib;
    protected ComprasModel $comprasModel;

    public function __construct() {

        $this->dirViewModule = 'Modules\Compras\Views';
        $this->comprasCart = new ComprasCartLib();
        $this->comprasLib = new ComprasLib();
        $this->comprasFinanzasLib = new ComprasFinanzasLib();
        $this->comprasAsientosLib = new ComprasAsientosLib();
        $this->prodModel = new ProductoModel();
        $this->searchModel = new SearchsModel();
        $this->comprasModel = new ComprasModel();

        //IMPORTAMOS LIBRERIAS
        $this->cuentasConfigLib = new CuentasConfigLib();
    }

    /**
     * Función para mostrar el dashboard principal del módulo de compras
     * @param int $moduloId El identificador del módulo a mostrar en el dashboard
     */
    public function index(int $moduloId) {
        $this->user->validateSession();
        $data['title'] = "Compras";
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

    public function indexEdit(int $compraId) {
        $view = $this->parametrosIndex($compraId);
        return view($this->dirTemplate . '\dashboard', $view);
    }

    public function loadCompraEdit(int $compraId) {

        $respuesta = $this->loadDataCompraCart($compraId);

        return $this->response->setJSON([
                    'status' => $respuesta['status'],
                    'msg' => $respuesta['status'] === 'success' ? 'ok' : $respuesta['msg'],
                    'redirect' => $respuesta['status'] === 'success' ? site_url('compras/indexEdit/' . $compraId) : null,
        ]);
    }

    public function clonarCompra(int $compraId) {

        $respuesta = $this->loadDataCompraCart($compraId, true);

        if ($respuesta['status'] !== 'success') {
            return $this->response->setJSON([
                        'status' => 'error',
                        'msg' => $respuesta['msg'],
                        'redirect' => null,
            ]);
        }

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => 'Compra clonada correctamente.',
                    'redirect' => site_url('compras/nuevaCompra'),
        ]);
    }

    private function loadDataCompraCart(int $compraId, bool $isClone = false): array {

        $compra = $this->comprasModel->getDataDetalle($compraId);

        if (!$compra) {
            return [
                'status' => 'error',
                'msg' => 'No se encontró la compra solicitada.',
            ];
        }

        if (!$isClone && $compra->comp_estado !== 'BORRADOR') {
            return [
                'status' => 'error',
                'msg' => 'Solo se pueden modificar compras en BORRADOR.',
            ];
        }

        $this->comprasCart->destroy();

        try {
            foreach ($compra->detalle as $detalle) {
                $item = [
                    'id' => (int) $detalle->fk_producto,
                    'qty' => (float) $detalle->compd_cantidad,
                    'codigo' => $detalle->prod_codigo,
                    'name' => $detalle->prod_nombre,
                    'unidadMedida' => $detalle->um_nombre_corto,
                    'price' => (float) $detalle->compd_precio_bruto,
                    'tipoDescuento' => (float) $detalle->compd_descuento_porcentaje > 0 ? 'PORCENTAJE' : 'VALOR',
                    'discountPercent' => (float) $detalle->compd_descuento_porcentaje,
                    'discountValue' => (float) $detalle->compd_descuento_valor,
                    'ivaPorcent' => (float) $detalle->compd_impt_porcentaje,
                    'icePorcent' => (float) $detalle->compd_ice_porcentaje,
                    'irbpnrUnitario' => (float) $detalle->compd_irbpnr,
                    'impuestoSelect' => (int) $detalle->fk_impuesto_tarifa,
                    'codigoImpuestoSelect' => $detalle->compd_impt_codigo,
                    'detalleImpuestoSelect' => $detalle->impuesto_detalle,
                    'tieneLote' => (int) $detalle->prod_ctrllote,
                    'lote' => $detalle->lote,
                    'fechaElaboracion' => $detalle->fecha_elaboracion,
                    'fechaCaducidad' => $detalle->fecha_caducidad,
                    'servicio' => (int) $detalle->prod_isservicio,
                    'permitirDuplicados' => $compra->comp_items_duplicados,
                    'centroCosto' => $detalle->compd_centro_costo,
                    'ctaContableProducto' => $detalle->compd_cta_entrada,
                    'codigoImport' => null,
                    'isNewProduct' => 0,
                    'productoTemporal' => 0,
                    'codigoProductoReemplazo' => null,
                ];

                $this->comprasCart->insert($item);
            }

            $this->comprasCart->updateValoresGlobales(
                    (float) $compra->comp_descuento_global,
                    (float) $compra->comp_recargo,
                    (float) $compra->comp_servicios_adicionales
            );

            return ['status' => 'success', 'msg' => ''];
        } catch (\Throwable $e) {
            $this->comprasCart->destroy();

            return [
                'status' => 'error',
                'msg' => 'No se pudo cargar el borrador: ' . $e->getMessage(),
            ];
        }
    }

    public function parametrosIndex($compraId = null) {

        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);

        $data['title'] = empty($compraId) ? "Nueva Compra" : "Actualizar Compra";
        $data['listaTiposCompra'] = $this->ccm->getData('cc_tipo_compra', ['tc_estado' => 1], 'id, tc_nombre, tc_codigo');
        $data['listaFormasPago'] = $this->ccm->getData('cc_formas_pago', ['fp_estado' => 1], 'cod, fp_nombre');
        $data['listaFormasPagoSRI'] = $this->ccm->getData('cc_formas_pago_sri', ['fp_estado' => 1], 'codigo, fp_nombre_sri');
        $tiposComprobantes = $this->ccm->getData('cc_tipos_comprobante', ['comp_estado' => 1], 'comp_codigo, comp_nombre, id');
        $data['listaTiposComprobantes'] = array_values(array_filter($tiposComprobantes, static fn($comprobante) => in_array((string) $comprobante->comp_codigo, ['01', '02', '03'], true)));
        $data['listaCuentasContables'] = $this->ccm->getData('cc_cuenta_contabledet', ['ctad_estado' => 1], 'ctad_codigo, ctad_nombre_cuenta, CONCAT_WS(" ",ctad_codigo,ctad_nombre_cuenta)cuenta ');

        $permiteIvaHistorico = $this->user->validatePermisos('usar_iva_historico_compra', $this->user->id);
        $whereTarifasIva = ['fk_impuesto' => 1];

        if (!$permiteIvaHistorico) {
            $whereTarifasIva['impt_estado'] = 'ACTIVO';
        }

        $data['permiteIvaHistorico'] = $permiteIvaHistorico;
        $data['listaImpuestosTarifa'] = $this->ccm->getData('cc_impuesto_tarifa', $whereTarifasIva, '*', ['impt_estado' => 'ASC', 'impt_porcentage' => 'ASC']);

        $data['listaSustentos'] = $this->ccm->getData('cc_sustentos', ['sus_estado' => 1], 'sus_codigo, sus_nombre');
        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaCentroCostos'] = $this->ccm->getData('cc_centroscosto', ['cc_estado' => 1], 'id, cc_nombre');
        $data['listaRetenciones'] = $this->ccm->getData('cc_retencion_sri', ['ret_estado' => 1], 'id, ret_codigo, ret_nombre, ret_porcentaje, ret_impuesto, ret_impuesto_detalle, CONCAT_WS(" - ",ret_codigo,ret_nombre,ret_porcentaje)retencionName');
        $data['listaBancos'] = $this->ccm->getData('cc_bancos_list', ['banc_estado' => 1], 'id codigo, banc_nombre nombre, banc_tipo');
        $data['puntoEmisionRetencion'] = $this->comprasModel->obtenerPuntoEmisionRetencionUsuario((int) $this->user->id);
        $data['puntoEmisionLiquidacionCompra'] = $this->comprasModel->obtenerPuntoEmisionUsuario((int) $this->user->id, '03');

        $bodegaMainUsuario = bodegaMain($this->user->id);

        $data['bodegaId'] = $this->session->get('bodegaIdComp') ? $this->session->get('bodegaIdComp') : $bodegaMainUsuario;

        $data['permitirDuplicados'] = getSettings('PERMITIR_ITEMS_DUPLICADOS');
        $data['dataCompra'] = null;
        $data['dataProveedor'] = null;

        if (!empty($compraId)) {
            $data['dataCompra'] = $this->ccm->getData('cc_compras', ['id' => $compraId, 'fk_proyecto' => getProyectoId()], '*', null, 1);
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
    public function responseSetJSON(string $status, string $mensaje, mixed $data = null) {
        return $this->response->setJSON([
                    'status' => $status,
                    'msg' => $mensaje,
                    'data' => $data,
        ]);
    }

    private function obtenerCodigoCuentaCompraPorProducto(object $producto, ?int $impuestoTarifaId, float $ivaPorcent): ?string {
        if ((int) $producto->prod_isservicio === 1 && (int) $producto->fk_tipoproducto === 3) {
            return '014';
        }

        if ((int) $producto->prod_isservicio !== 0 || (int) $producto->fk_tipoproducto !== 1) {
            return null;
        }

        if ($ivaPorcent <= 0) {
            return '010';
        }

        $grupo = null;

        if ($impuestoTarifaId) {
            $grupo = $this->ccm->getValueWhere('cc_impuesto_tarifa', ['id' => $impuestoTarifaId, 'fk_impuesto' => 1], 'impt_grupo');
        }

        if ($grupo === 'ESPECIAL') {
            return '022';
        }

        return '011';
    }

    private function obtenerCuentaHistoricaItemCompra(?int $impuestoTarifaId, string $fechaEmision): ?string {
        if (!$impuestoTarifaId || trim($fechaEmision) === '') {
            return null;
        }

        $tarifa = $this->ccm->getData('cc_impuesto_tarifa', ['id' => $impuestoTarifaId, 'fk_impuesto' => 1], 'id, impt_estado, impt_fecha_inicio_vigencia, impt_fecha_fin_vigencia', null, 1);

        if (!$tarifa || !$this->esTarifaHistoricaParaFecha($tarifa, $fechaEmision)) {
            return null;
        }

        $whereData = [
            'fk_impuesto_tarifa' => $impuestoTarifaId,
            'tipo_movimiento' => 'COMPRA',
            'tipo_cuenta' => 'INVENTARIO',
            'estado' => 1,
        ];
        return $this->ccm->getValueWhere('cc_impuesto_tarifa_cuenta_contable', $whereData, 'fk_cuentacontable_det');
    }

    private function esTarifaHistoricaParaFecha(object $tarifa, string $fechaEmision): bool {
        if (($tarifa->impt_estado ?? '') !== 'HISTORIAL') {
            return false;
        }

        $fechaInicio = $tarifa->impt_fecha_inicio_vigencia ?? null;
        $fechaFin = $tarifa->impt_fecha_fin_vigencia ?? null;

        if ($fechaInicio && $fechaInicio !== '0000-00-00' && $fechaInicio > $fechaEmision) {
            return false;
        }

        if ($fechaFin && $fechaFin !== '0000-00-00' && $fechaFin < $fechaEmision) {
            return false;
        }

        return true;
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
        $fechaEmision = $dataPost->fechaEmision ?? date('Y-m-d');

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

        $tarifaVigenteFecha = null;

        if ($porcentajeSelect) {
            $tarifaVigenteFecha = $this->comprasModel->obtenerTarifaIvaVigentePorFecha((int) $porcentajeSelect, (string) $fechaEmision);
        }

        if (!$tarifaVigenteFecha && $porcentajeSelect) {
            return $this->responseSetJSON('warning', 'No existe una tarifa IVA vigente para la fecha de emisión ' . $fechaEmision . '. Revise la configuración de impuestos.');
        }

        if ($tarifaVigenteFecha && (int) $tarifaVigenteFecha->id !== (int) $porcentajeSelect && !$this->user->validatePermisos('usar_iva_historico_compra', $this->user->id)) {
            return $this->responseSetJSON('warning', 'La fecha de emisión requiere una tarifa histórica de IVA, pero su usuario no tiene habilitado el permiso para usar IVA histórico en compras.');
        }

        if ($tarifaVigenteFecha && (int) $tarifaVigenteFecha->id !== (int) $porcentajeSelect) {
            $ivaPorcent = (float) $tarifaVigenteFecha->impt_porcentage;
            $porcentajeSelect = (int) $tarifaVigenteFecha->id;
            $codigoPorcentajeSelect = $tarifaVigenteFecha->impt_codigo;
            $detallePorcentajeSelect = $tarifaVigenteFecha->impt_detalle;
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
            'isNewProduct' => 0,
            'productoTemporal' => 0,
            'codigoProductoReemplazo' => null,
        ];

        $cuentaHistorica = $this->obtenerCuentaHistoricaItemCompra($porcentajeSelect, (string) $fechaEmision);

        if ($cuentaHistorica) {
            $item['ctaContableProducto'] = $cuentaHistorica;
        } elseif (($tarifaVigenteFecha->impt_estado ?? '') === 'HISTORIAL') {
            return $this->responseSetJSON('warning', 'La tarifa IVA historica seleccionada no tiene configurada la cuenta contable de inventario para compras.');
        } elseif (!empty($dataProducto->fk_cuentacontablecompras)) {
            $item['ctaContableProducto'] = $dataProducto->fk_cuentacontablecompras;
        } else {
            $codigoCuenta = $this->obtenerCodigoCuentaCompraPorProducto($dataProducto, $porcentajeSelect, $ivaPorcent);
            if ($codigoCuenta) {
                $item['ctaContableProducto'] = $this->cuentasConfigLib->obtenerSettingCuentaContable($codigoCuenta);
            }
        }

        $this->comprasCart->insert($item);

        return $this->responseSetJSON('success', 'Producto agregado al carrito', $item);
    }

    public function importarExcel() {
        try {
            $file = $this->request->getFile('file');
            $permitirDuplicados = $this->request->getPost('permitirDuplicados');
            $centroCostoId = $this->request->getPost('centroCostoId');
            $fechaEmision = $this->request->getPost('fechaEmision') ?: date('Y-m-d');

            if (!$file || !$file->isValid()) {
                return $this->responseSetJSON('error', 'Debe seleccionar un archivo Excel valido.');
            }

            $centroCostoDefault = !empty($centroCostoId) ? (int) $centroCostoId : null;

            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();
            $registros = $sheet->toArray(null, true, true, true);

            $importados = 0;
            $errores = [];

            foreach ($registros as $i => $row) {
                if ($i === 1) {
                    continue;
                }

                $codigo = trim((string) ($row['A'] ?? ''));
                $cantidad = (float) ($row['B'] ?? 0);
                $precio = (float) ($row['C'] ?? 0);
                $descuento = (float) ($row['D'] ?? 0);
                $lote = trim((string) ($row['E'] ?? ''));
                $fechaElab = trim((string) ($row['F'] ?? ''));
                $fechaCaduc = trim((string) ($row['G'] ?? ''));

                if ($codigo === '') {
                    $errores[] = "Fila {$i}: el codigo esta vacio.";
                    continue;
                }

                if ($cantidad <= 0) {
                    $errores[] = "Fila {$i}: la cantidad debe ser mayor a cero.";
                    continue;
                }

                if ($precio <= 0) {
                    $errores[] = "Fila {$i}: el costo debe ser mayor a cero.";
                    continue;
                }

                if ($descuento < 0) {
                    $errores[] = "Fila {$i}: el descuento no puede ser negativo.";
                    continue;
                }

                $idProd = $this->ccm->getValueWhere('cc_productos', ['prod_codigo' => $codigo, 'prod_estado' => 1], 'id');
                if (!$idProd) {
                    $errores[] = "Fila {$i}: el producto con codigo '{$codigo}' no existe o esta desactivado.";
                    continue;
                }

                $dataProducto = $this->searchModel->searchProductoData($idProd);
                if (!$dataProducto) {
                    $errores[] = "Fila {$i}: no se pudo obtener informacion del producto '{$codigo}'.";
                    continue;
                }

                if ((string) $dataProducto->prod_ctrllote === '1') {
                    if ($lote === '') {
                        $errores[] = "Fila {$i}: el producto '{$codigo}' requiere numero de lote.";
                        continue;
                    }

                    if ($fechaElab === '' || $fechaCaduc === '') {
                        $errores[] = "Fila {$i}: el producto '{$codigo}' requiere fecha de elaboracion y caducidad.";
                        continue;
                    }

                    $fechaElab = date('Y-m-d', strtotime($fechaElab));
                    $fechaCaduc = date('Y-m-d', strtotime($fechaCaduc));

                    if ($fechaElab === '1970-01-01' || $fechaCaduc === '1970-01-01') {
                        $errores[] = "Fila {$i}: formato de fecha invalido para '{$codigo}'.";
                        continue;
                    }
                } else {
                    $lote = null;
                    $fechaElab = null;
                    $fechaCaduc = null;
                }

                $ivaPorcent = 0;
                $icePorcent = 0;
                $impuestoSelect = null;
                $codigoPorcentajeSelect = null;
                $detallePorcentajeSelect = '';

                $tarifas = $this->prodModel->getImpuestoTarifa((int) $dataProducto->id);
                foreach ($tarifas ?: [] as $tarifa) {
                    if ((int) $tarifa->fk_impuesto === 1) {
                        $ivaPorcent = (float) $tarifa->impt_porcentage;
                        $impuestoSelect = (int) $tarifa->id;
                        $codigoPorcentajeSelect = $tarifa->impt_codigo;
                        $detallePorcentajeSelect = $tarifa->impt_detalle;
                    }

                    if ((int) $tarifa->fk_impuesto === 2) {
                        $icePorcent = (float) $tarifa->impt_porcentage;
                    }
                }

                $tarifaVigenteFecha = null;

                if ($impuestoSelect) {
                    $tarifaVigenteFecha = $this->comprasModel->obtenerTarifaIvaVigentePorFecha((int) $impuestoSelect, (string) $fechaEmision);
                }

                if (!$tarifaVigenteFecha && $impuestoSelect) {
                    $errores[] = "Fila {$i}: no existe una tarifa IVA vigente para la fecha de emision {$fechaEmision}.";
                    continue;
                }

                if ($tarifaVigenteFecha && (int) $tarifaVigenteFecha->id !== (int) $impuestoSelect && !$this->user->validatePermisos('usar_iva_historico_compra', $this->user->id)) {
                    $errores[] = "Fila {$i}: la fecha de emision requiere una tarifa historica de IVA, pero su usuario no tiene habilitado el permiso para usar IVA historico en compras.";
                    continue;
                }

                if ($tarifaVigenteFecha && (int) $tarifaVigenteFecha->id !== (int) $impuestoSelect) {
                    $ivaPorcent = (float) $tarifaVigenteFecha->impt_porcentage;
                    $impuestoSelect = (int) $tarifaVigenteFecha->id;
                    $codigoPorcentajeSelect = $tarifaVigenteFecha->impt_codigo;
                    $detallePorcentajeSelect = $tarifaVigenteFecha->impt_detalle;
                }

                $discountValue = $descuento;
                $discountPercent = $precio > 0 ? ($discountValue / $precio) * 100 : 0;

                $item = [
                    'id' => (int) $dataProducto->id,
                    'qty' => $cantidad,
                    'codigo' => $dataProducto->prod_codigo,
                    'name' => $dataProducto->prod_nombre,
                    'unidadMedida' => $dataProducto->um_nombre_corto,
                    'price' => $precio,
                    'ivaPorcent' => $ivaPorcent,
                    'icePorcent' => $icePorcent,
                    'impuestoSelect' => $impuestoSelect,
                    'codigoImpuestoSelect' => $codigoPorcentajeSelect,
                    'detalleImpuestoSelect' => $detallePorcentajeSelect,
                    'tipoDescuento' => 'VALOR',
                    'discountPercent' => $discountPercent,
                    'discountValue' => $discountValue,
                    'descuento' => $descuento,
                    'tieneLote' => $dataProducto->prod_ctrllote,
                    'permitirDuplicados' => $permitirDuplicados,
                    'lote' => $lote,
                    'fechaElaboracion' => $fechaElab,
                    'fechaCaducidad' => $fechaCaduc,
                    'servicio' => $dataProducto->prod_isservicio,
                    'irbpnrUnitario' => $dataProducto->prod_tiene_irbpnr === '1' ? (float) getImpuestoIrbpnr() : 0,
                    'centroCosto' => $centroCostoDefault,
                    'ctaContableProducto' => $dataProducto->fk_cuentacontablecompras ?: null,
                    'codigoImport' => null,
                    'isNewProduct' => 0,
                    'productoTemporal' => 0,
                    'codigoProductoReemplazo' => null,
                ];

                $cuentaHistorica = $this->obtenerCuentaHistoricaItemCompra($impuestoSelect, (string) $fechaEmision);

                if ($cuentaHistorica) {
                    $item['ctaContableProducto'] = $cuentaHistorica;
                } elseif (($tarifaVigenteFecha->impt_estado ?? '') === 'HISTORIAL') {
                    $errores[] = "Fila {$i}: la tarifa IVA historica seleccionada no tiene configurada la cuenta contable de inventario para compras.";
                    continue;
                } elseif (empty($item['ctaContableProducto'])) {
                    $codigoCuenta = $this->obtenerCodigoCuentaCompraPorProducto($dataProducto, $impuestoSelect, $ivaPorcent);

                    if ($codigoCuenta) {
                        $item['ctaContableProducto'] = $this->cuentasConfigLib->obtenerSettingCuentaContable($codigoCuenta);
                    }
                }

                $this->comprasCart->insert($item);
                $importados++;
            }

            if ($importados === 0) {
                $msg = 'No se importaron productos validos.';
                if ($errores) {
                    $msg .= "<span class='fw-semibold text-danger'><br><br><strong>Errores encontrados:</strong><br>" . implode('<br>', $errores) . '</span>';
                }
                return $this->responseSetJSON('warning', $msg);
            }

            $msg = "Importacion completada: {$importados} producto(s) agregado(s).";
            if ($errores) {
                $msg .= "<span class='fw-semibold text-danger'><br><br><strong>Errores encontrados:</strong><br>" . implode('<br>', $errores) . '</span>';
            }

            return $this->responseSetJSON('success', $msg, [
                        'totalImportados' => $importados,
                        'errores' => $errores,
            ]);
        } catch (\Throwable $e) {
            return $this->responseSetJSON('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
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
    public function showDetailCart(int $key = 0) {
        $cartContent = array_values($this->comprasCart->getContent() ?? []);

        $dataCart = [
            'cartContent' => $cartContent ? array_reverse($cartContent) : null,
            'totalArticles' => $this->comprasCart->totalArticles(),
            'totalItems' => count($cartContent),
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

        if ($key === 1) {
            return json_decode(json_encode($dataCart));
        }

        return $this->response->setJSON($dataCart);
    }

    public function validarCambioFechaEmisionCart() {
        $this->user->validateSession();

        $dataPost = json_decode(file_get_contents('php://input'));
        $fechaEmision = (string) ($dataPost->fechaEmision ?? '');

        if (trim($fechaEmision) === '') {
            return $this->responseSetJSON('warning', 'Debe seleccionar la fecha de emision.');
        }
        $whereData = [
            'fk_impuesto' => 1,
            'impt_estado' => 'ACTIVO',
            'impt_predeterminado' => 1,
        ];
        $tarifaActual = $this->ccm->getData('cc_impuesto_tarifa', $whereData, 'id, impt_porcentage, impt_detalle, impt_fecha_inicio_vigencia, impt_fecha_fin_vigencia', null, 1);

        if (!$tarifaActual) {
            return $this->responseSetJSON('warning', 'No existe una tarifa IVA actual predeterminada. Revise la configuracion de impuestos.');
        }

        $fechaInicio = $tarifaActual->impt_fecha_inicio_vigencia ?? null;
        $fechaFin = $tarifaActual->impt_fecha_fin_vigencia ?? null;
        $fechaDentroIvaActual = true;

        if ($fechaInicio && $fechaInicio !== '0000-00-00' && $fechaInicio > $fechaEmision) {
            $fechaDentroIvaActual = false;
        }

        if ($fechaFin && $fechaFin !== '0000-00-00' && $fechaFin < $fechaEmision) {
            $fechaDentroIvaActual = false;
        }

        if (!$fechaDentroIvaActual) {
            return $this->responseSetJSON(
                            'success',
                            'La nueva fecha de emision esta fuera de la vigencia del IVA actual. Para evitar inconsistencias debe limpiar el carrito.',
                            ['requiereLimpiar' => true]
            );
        }

        return $this->responseSetJSON('success', 'La fecha de emision esta dentro de la vigencia del IVA actual.', ['requiereLimpiar' => false]);
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
        $impuesto = $this->ccm->getData('cc_impuesto_tarifa', ['id' => $impuestoSelect], 'impt_porcentage, impt_codigo, impt_detalle, impt_estado', null, 1);

        if (!$impuesto) {
            return $this->responseSetJSON('warning', 'La tarifa de IVA seleccionada no existe.');
        }

        if (($impuesto->impt_estado ?? '') === 'HISTORIAL' && !$this->user->validatePermisos('usar_iva_historico_compra', $this->user->id)) {
            return $this->responseSetJSON('warning', 'Su usuario no tiene habilitado el permiso para usar IVA histórico en compras.');
        }

        $fechaEmision = $dataPost->fechaEmision ?? date('Y-m-d');
        $tarifaVigenteFecha = $this->comprasModel->obtenerTarifaIvaVigentePorFecha((int) $impuestoSelect, (string) $fechaEmision);

        if (!$tarifaVigenteFecha) {
            return $this->responseSetJSON('warning', 'No existe una tarifa IVA vigente para la fecha de emision ' . $fechaEmision . '. Revise la configuracion de impuestos.');
        }

        if ((int) $tarifaVigenteFecha->id !== (int) $impuestoSelect && !$this->user->validatePermisos('usar_iva_historico_compra', $this->user->id)) {
            return $this->responseSetJSON('warning', 'La fecha de emision requiere una tarifa historica de IVA, pero su usuario no tiene habilitado el permiso para usar IVA historico en compras.');
        }

        if ((int) $tarifaVigenteFecha->id !== (int) $impuestoSelect) {
            $impuestoSelect = (int) $tarifaVigenteFecha->id;
            $impuesto = $tarifaVigenteFecha;
        }

        //CALCULO DESCUENTO UNITARIO
        $descuento = (float) ($dataPost->descuento ?? 0);
        $discountValue = 0;
        $discountPercent = 0;

        if ($dataPost->tipoDescuento === 'VALOR') {
            if ($descuento > $precio) {
                return $this->responseSetJSON('warning', 'El descuento no puede superar el precio unitario del producto.');
            }
            $discountValue = $descuento;
            if ($precio > 0) {
                $discountPercent = ($discountValue / $precio) * 100;
            }
        } else { // PORCENTAJE
            if ($descuento > 100) {
                return $this->responseSetJSON('warning', 'El descuento porcentual no puede superar el 100%.');
            }

            $discountPercent = $descuento;
            $discountValue = ($precio * $discountPercent) / 100;
        }

        $lote = $dataPost->lote ?? null;
        $fechaElaboracion = $dataPost->fechaElaboracion ?? null;
        $fechaCaducidad = $dataPost->fechaCaducidad ?? null;

        if (!empty($dataPost->actualizarLote) && trim((string) $lote) !== '' && (int) ($dataPost->tieneLote ?? 0) === 1) {
            $dataLote = $this->ccm->getData('cc_lotes', ['lot_lote' => trim((string) $lote), 'fk_producto' => $idProd], 'lot_fecha_elaboracion, lot_fecha_caducidad', null, 1);

            if ($dataLote) {
                $fechaElaboracion = $dataLote->lot_fecha_elaboracion;
                $fechaCaducidad = $dataLote->lot_fecha_caducidad;
            }
        }

        $ctaContableProducto = $dataPost->ctaContableProducto ?? null;
        $cuentaHistorica = $this->obtenerCuentaHistoricaItemCompra((int) $impuestoSelect, (string) $fechaEmision);

        if ($cuentaHistorica) {
            $ctaContableProducto = $cuentaHistorica;
        } elseif (($tarifaVigenteFecha->impt_estado ?? '') === 'HISTORIAL') {
            return $this->responseSetJSON('warning', 'La tarifa IVA historica seleccionada no tiene configurada la cuenta contable de inventario para compras.');
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
            'lote' => $lote,
            'fechaElaboracion' => $fechaElaboracion,
            'fechaCaducidad' => $fechaCaducidad,
            'servicio' => $dataPost->servicio ?? null,
            'irbpnrUnitario' => $dataPost->irbpnrUnitario ?? 0,
            'centroCosto' => $dataPost->centroCosto ?? null,
            'ctaContableProducto' => $ctaContableProducto,
            'codigoImport' => $dataPost->codigoImport ?? null,
            'isNewProduct' => (int) ($dataPost->isNewProduct ?? 0),
            'productoTemporal' => (int) ($dataPost->productoTemporal ?? 0),
            'codigoProductoReemplazo' => $dataPost->codigoProductoReemplazo ?? null,
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

    public function anularCompra() {
        $data = $this->request->getJSON();

        if (!is_object($data)) {
            return $this->responseSetJSON('warning', 'No se recibieron datos para anular la compra.');
        }

        $compraId = (int) ($data->compraId ?? 0);
        $motivoAnulacion = trim((string) ($data->motivoAnulacion ?? ''));

        if ($compraId <= 0) {
            return $this->responseSetJSON('warning', 'El identificador de la compra no es válido.');
        }

        if ($motivoAnulacion === '') {
            return $this->responseSetJSON('warning', 'Debe especificar el motivo de la anulación.');
        }

        $compra = $this->ccm->getData('cc_compras', ['id' => $compraId, 'fk_proyecto' => getProyectoId()], 'id, comp_secuencial, comp_estado', null, 1);

        if (!$compra) {
            return $this->responseSetJSON('warning', 'La compra no se encuentra registrada.');
        }

        if (!in_array($compra->comp_estado, ['BORRADOR', 'ARCHIVADO'], true)) {
            return $this->responseSetJSON('warning', 'La compra ya no se encuentra en un estado que permita anular.');
        }

        if ($compra->comp_estado === 'ARCHIVADO' && !getPeriodoContable(date('Y-m-d'))) {
            return $this->responseSetJSON(
                            'error',
                            '<h5>Revise el periodo de cierre</h5><h6>No se encontro un periodo contable habil para la fecha de anulacion.</h6>'
            );
        }

        $this->db->transBegin();

        try {
            if ($compra->comp_estado === 'BORRADOR') {
                $estadoAnulado = 'ANULADA_EN_PENDIENTE';
                $anulado = $this->comprasLib->anularCompraBorrador($compraId, $motivoAnulacion);
            } else {
                $estadoAnulado = 'ANULADA_EN_ARCHIVADA';

                $this->comprasFinanzasLib->validarSinPagosActivosCredito($compraId); //Valido que la compra no tenga pagos aplicados
                $this->comprasFinanzasLib->validarRetencionAnulableCompra($compraId); //Primero valido que la retencion no este autorizada por el SRI
                $this->comprasLib->revertirKardexCompra($compraId);
                $this->comprasFinanzasLib->anularRetencionCompra($compraId);
                $this->comprasFinanzasLib->anularPagosCompra($compraId);
                $this->comprasFinanzasLib->anularCuentaPorPagarCompra($compraId);
                $this->comprasAsientosLib->anularAsientoCompra($compraId);

                $anulado = $this->comprasLib->anularCompraArchivada($compraId, $motivoAnulacion);
            }

            if (!$anulado || $this->db->transStatus() === false) {
                throw new \RuntimeException('No se pudo actualizar el estado de la compra, no se completo el proceso de anulación');
            }

            $this->db->transCommit();

            $datosSend = [
                'id' => $compraId,
                'estado' => $estadoAnulado,
            ];
            return $this->responseSetJSON('success', "Compra #{$compra->comp_secuencial} anulada correctamente.", $datosSend);
        } catch (\Throwable $e) {
            $this->db->transRollback();

            return $this->responseSetJSON('error', 'Error al anular la compra: ' . $e->getMessage()
            );
        }
    }

    public function updateCompra() {
        $json = $this->request->getPost('data');

        if (!is_string($json) || trim($json) === '') {
            return $this->responseSetJSON('warning', 'No se recibieron datos válidos para actualizar la compra.');
        }



        try {
            $dataPostCompra = json_decode($json, false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return $this->responseSetJSON('warning', 'Los datos de la compra no tienen un formato válido.');
        }

        $compraId = (int) ($dataPostCompra->idCompra ?? 0);

        if ($compraId <= 0) {
            return $this->responseSetJSON('warning', 'No se recibió la compra que se desea actualizar.');
        }

        $cartData = $this->showDetailCart(1);
        $validacion = $this->validarCamposCompra($dataPostCompra, $cartData, $compraId);

        if ($validacion['status']) {
            return $this->responseSetJSON('warning', $validacion['msg']);
        }

        $compraActual = $this->ccm->getData('cc_compras', ['id' => $compraId, 'fk_proyecto' => getProyectoId()], 'id, comp_secuencial, comp_estado', null, 1);

        if (!$compraActual) {
            return $this->responseSetJSON('warning', 'La compra no se encuentra registrada.');
        }

        if ($compraActual->comp_estado !== 'BORRADOR') {
            return $this->responseSetJSON('warning', 'Solo se pueden actualizar compras en estado borrador.');
        }

        $compra = $dataPostCompra->compra;
        $esArchivado = $compra->compEstado === 'ARCHIVADO';

        if ($esArchivado && !getPeriodoContable(date('Y-m-d'))) {
            return $this->responseSetJSON(
                            'error',
                            '<h5>Revise el período de cierre</h5><h6>No se encontró un período contable hábil para la fecha de emisión.</h6>'
            );
        }

        $this->db->transBegin();

        try {
            $actualizado = $this->comprasLib->actualizarCompra($compraId, $cartData, $dataPostCompra);

            if (!$actualizado) {
                throw new \RuntimeException('No se pudo actualizar la cabecera de la compra.');
            }

            $this->ccm->eliminar('cc_compras_det', ['fk_compra' => $compraId, 'fk_proyecto' => getProyectoId()]);

            $this->ccm->eliminar('cc_compras_bases_impuesto', ['fk_compra' => $compraId, 'fk_proyecto' => getProyectoId()]);

            foreach ($cartData->cartContent as $item) {
                $loteId = $this->comprasLib->obtenerOCrearLote($compraId, $item);

                $this->comprasLib->guardarDetalleCompra($compraId, $item, (int) $compra->compBodega, (string) $compra->compSustento, $loteId);

                if ((int) ($item->isNewProduct ?? 0) === 1 && (int) ($item->productoTemporal ?? 0) !== 1) {
                    $this->comprasLib->guardarProductoProveedor((int) $compra->compProveedor, $item);
                }

                if ($esArchivado) {
                    $this->comprasLib->generarKardex($compraId, $item, $loteId, $compra);
                }
            }

            $this->comprasLib->guardarBasesImpuesto($compraId, $dataPostCompra->basesImpuestos ?? []);

            if ($esArchivado) {
                $this->comprasLib->guardarFormasPagoAts($compraId, $dataPostCompra->ats ?? (object) []);

                $this->comprasFinanzasLib->guardarRetencion($compraId, $dataPostCompra->retencion ?? (object) []);

                $cxpId = $this->comprasFinanzasLib->crearCuentaPorPagar($compraId);

                if ($compra->compTipoPago === 'CONTADO') {
                    $this->comprasFinanzasLib->guardarPagoContado($cxpId, $dataPostCompra->pago ?? (object) []);
                }

                if ($compra->compTipoPago === 'CREDITO') {
                    $this->comprasFinanzasLib->guardarCuotas($cxpId, $dataPostCompra->cuotas ?? []);
                }

                $this->comprasAsientosLib->generarAsiento($compraId);

                $this->actualizarSecuencialLiquidacionCompra($compra);
            }

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('La transacción de actualización falló.');
            }

            $this->db->transCommit();
            $this->comprasCart->destroy();

            $dataResponse = ['id' => $compraId, 'comp_secuencial' => $compraActual->comp_secuencial];

            $mensaje = $esArchivado ? 'Compra actualizada y archivada correctamente.' : 'Borrador de compra actualizado correctamente.';

            return $this->responseSetJSON('success', $mensaje, $dataResponse
            );
        } catch (\Throwable $e) {
            $this->db->transRollback();

            return $this->responseSetJSON('error', 'Error al actualizar la compra: ' . $e->getMessage());
        }
    }

    /**
     * Recibe y valida los datos comunes antes de registrar la compra.
     */
    public function saveCompra() {

        $json = $this->request->getPost('data');

        if (!is_string($json) || trim($json) === '') {
            return $this->responseSetJSON('warning', 'No se recibieron datos validos para procesar la compra.');
        }


        try {
            $dataPostCompra = json_decode($json, false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return $this->responseSetJSON('warning', 'Los datos de la compra no tienen un formato válido.');
        }

        $cartData = $this->showDetailCart(1);
        $validacion = $this->validarCamposCompra($dataPostCompra, $cartData);

        if ($validacion['status']) {
            return $this->responseSetJSON('warning', $validacion['msg']);
        }

        $compra = $dataPostCompra->compra;
        $esArchivado = $compra->compEstado === 'ARCHIVADO';

        if ($esArchivado && !getPeriodoContable(date('Y-m-d'))) {
            return $this->responseSetJSON('error', '<h5>Revise el período de cierre</h5><h6>No se encontró un período contable hábil para la fecha de emisión.</h6>');
        }

        $this->db->transBegin();

        try {
            $compraId = $this->comprasLib->guardarCompra($cartData, $dataPostCompra);

            foreach ($cartData->cartContent as $item) {
                $loteId = $this->comprasLib->obtenerOCrearLote($compraId, $item);

                $this->comprasLib->guardarDetalleCompra($compraId, $item, (int) $compra->compBodega, (string) $compra->compSustento, $loteId);

                if ((int) ($item->isNewProduct ?? 0) === 1 && (int) ($item->productoTemporal ?? 0) !== 1) {
                    $this->comprasLib->guardarProductoProveedor((int) $compra->compProveedor, $item);
                }

                if ($esArchivado) {
                    $this->comprasLib->generarKardex($compraId, $item, $loteId, $compra);
                }
            }

            $this->comprasLib->guardarBasesImpuesto($compraId, $dataPostCompra->basesImpuestos ?? []);

            if ($esArchivado) {

                $this->comprasLib->guardarFormasPagoAts($compraId, $dataPostCompra->ats ?? (object) []);

                $this->comprasFinanzasLib->guardarRetencion($compraId, $dataPostCompra->retencion ?? (object) []);

                $cxpId = $this->comprasFinanzasLib->crearCuentaPorPagar($compraId);

                if ($compra->compTipoPago === 'CONTADO') {
                    $this->comprasFinanzasLib->guardarPagoContado($cxpId, $dataPostCompra->pago ?? (object) []);
                }

                if ($compra->compTipoPago === 'CREDITO') {
                    $this->comprasFinanzasLib->guardarCuotas($cxpId, $dataPostCompra->cuotas ?? []);
                }

                $this->comprasAsientosLib->generarAsiento($compraId);

                $this->actualizarSecuencialLiquidacionCompra($compra);
            }

            $secuencial = $this->ccm->getValueWhere('cc_compras', ['id' => $compraId, 'fk_proyecto' => getProyectoId()], 'comp_secuencial');

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return $this->responseSetJSON('error', 'No se pudo registrar la compra.');
            }

            $this->db->transCommit();

            $this->comprasCart->destroy();

            $dataResponse = ['id' => $compraId, 'comp_secuencial' => $secuencial];

            return $this->responseSetJSON('success', $esArchivado ? 'Compra registrada correctamente.' : 'Compra guardada como borrador.', $dataResponse);
        } catch (\Throwable $e) {
            $this->db->transRollback();

            return $this->responseSetJSON('error', 'Error al registrar la compra: ' . $e->getMessage());
        }
    }

    private function validarCamposCompra(object $dataPostCompra, object $cartData, ?int $compraIdExcluir = null): array {
        if (empty($dataPostCompra->compra)) {
            return [
                'status' => true,
                'msg' => 'No se recibió la cabecera de la compra.',
            ];
        }

        $compra = $dataPostCompra->compra;
        $campos = [
            'compFechaEmision' => 'Fecha de emisión',
            'compTipoComprobante' => 'Tipo de comprobante',
            'compNumeroComprobante' => 'Número de comprobante',
            'compNumeroEstablecimiento' => 'Establecimiento',
            'compNumeroEmision' => 'Punto de emisión',
            'compFechaCaducidad' => 'Fecha de caducidad',
            'compAutSRI' => 'Autorización SRI',
            'compProveedor' => 'Proveedor',
            'compBodega' => 'Bodega',
            'compSustento' => 'Sustento',
            'compCentroCosto' => 'Centro de costos',
            'compTipoCompra' => 'Tipo de compra',
            'compTipoCosto' => 'Tipo de costo',
            'compEstado' => 'Estado',
        ];

        foreach ($campos as $campo => $nombre) {
            if (!property_exists($compra, $campo) || $compra->$campo === null || (is_string($compra->$campo) && trim($compra->$campo) === '')) {
                return [
                    'status' => true,
                    'msg' => "El campo {$nombre} es obligatorio.",
                ];
            }
        }

        if (!in_array($compra->compEstado, ['BORRADOR', 'ARCHIVADO'], true)) {
            return [
                'status' => true,
                'msg' => 'El estado de la compra no es válido.',
            ];
        }

        if (!in_array((string) $compra->compTipoComprobante, ['01', '02', '03'], true)) {
            return [
                'status' => true,
                'msg' => 'El tipo de comprobante seleccionado no esta permitido para este proceso de compra.',
            ];
        }

        $numeroComprobante = str_pad(trim((string) $compra->compNumeroComprobante), 9, '0', STR_PAD_LEFT);
        $compraDuplicada = $this->comprasModel->getCompraDuplicadaProveedor(
                (int) $compra->compProveedor,
                (string) $compra->compTipoComprobante,
                trim((string) $compra->compNumeroEstablecimiento),
                trim((string) $compra->compNumeroEmision),
                $numeroComprobante,
                $compraIdExcluir
        );

        if ($compraDuplicada) {
            $proyectoDuplicado = trim((string) ($compraDuplicada->proy_codigo ?? '') . ' - ' . (string) ($compraDuplicada->proy_nombre ?? ''));
            return [
                'status' => true,
                'msg' => 'Ya existe una compra registrada para este proveedor con el mismo tipo y numero de comprobante.<br> Compra #' . str_pad((string) $compraDuplicada->comp_secuencial, 5, '0', STR_PAD_LEFT) . ' (' . $compraDuplicada->comp_estado . ') en el proyecto ' . $proyectoDuplicado . '.',
            ];
        }

        if ((string) $compra->compTipoComprobante === '03') {
            $puntoEmision = $this->comprasModel->obtenerPuntoEmisionUsuario((int) $this->user->id, '03');

            if (!$puntoEmision) {
                return [
                    'status' => true,
                    'msg' => 'Su usuario no esta registrado en un punto de emisión para liquidación de compra.',
                ];
            }

            if ((int) $puntoEmision->pv_sec_actual > (int) $puntoEmision->pv_sec_final) {
                return [
                    'status' => true,
                    'msg' => 'El punto de emisión para liquidación de compra ya no tiene secuenciales disponibles.',
                ];
            }

            if (
                    (string) $compra->compNumeroEstablecimiento !== (string) $puntoEmision->pv_establecimiento ||
                    (string) $compra->compNumeroEmision !== (string) $puntoEmision->pv_emision ||
                    (string) $compra->compNumeroComprobante !== str_pad((string) $puntoEmision->pv_sec_actual, 9, '0', STR_PAD_LEFT)
            ) {
                return [
                    'status' => true,
                    'msg' => 'Los datos del comprobante no coinciden con el punto de emisión asignado para liquidación de compra.',
                ];
            }
        }

        if (empty($cartData->cartContent)) {
            return [
                'status' => true,
                'msg' => 'Debe agregar al menos un producto o servicio.',
            ];
        }

        $requiereFormaPagoAts = $compra->compEstado === 'ARCHIVADO' && (float) $cartData->totalGeneral >= (float) getSettings('VALOR_MAXIMO_ANEXO_ATS_SRI');

        if ($requiereFormaPagoAts) {
            $formasPagoAts = (array) ($dataPostCompra->ats->formasPago ?? []);

            if (empty($formasPagoAts)) {
                return [
                    'status' => true,
                    'msg' => 'Debe seleccionar al menos una forma de pago ATS.',
                ];
            }
        }

        $permiteIvaHistorico = $this->user->validatePermisos('usar_iva_historico_compra', $this->user->id);

        foreach ($cartData->cartContent as $item) {
            $nombreProducto = $item->name ?? 'producto';

            if (!$permiteIvaHistorico) {
                $estadoTarifaIva = $this->ccm->getValueWhere('cc_impuesto_tarifa', ['id' => (int) ($item->impuestoSelect ?? 0), 'fk_impuesto' => 1], 'impt_estado');

                if ($estadoTarifaIva === 'HISTORIAL') {
                    return [
                        'status' => true,
                        'msg' => "El producto {$nombreProducto} usa una tarifa histórica de IVA. Su usuario no tiene habilitado el permiso para guardar compras con IVA histórico.",
                    ];
                }
            }

            if ((int) ($item->productoTemporal ?? 0) === 1) {
                return [
                    'status' => true,
                    'msg' => "El producto {$nombreProducto} fue importado como temporal. Debe vincularlo con un producto del sistema antes de guardar.",
                ];
            }

            if ((float) ($item->qty ?? 0) <= 0) {
                return [
                    'status' => true,
                    'msg' => "Cantidad inválida para {$nombreProducto}, la cantidad debe ser mayor a 0 .",
                ];
            }

            if ((float) ($item->price ?? 0) <= 0) {
                return [
                    'status' => true,
                    'msg' => "Precio inválido para {$nombreProducto}, el precio debe ser mayor a 0.",
                ];
            }
        }

        return ['status' => false, 'msg' => ''];
    }

    private function actualizarSecuencialLiquidacionCompra(object $compra): void {

        if ((string) ($compra->compTipoComprobante ?? '') !== '03') {
            return;
        }

        $puntoEmision = $this->comprasModel->obtenerPuntoEmisionUsuario((int) $this->user->id, '03');

        if (!$puntoEmision) {
            throw new \RuntimeException('Su usuario no esta registrado en un punto de emisión para liquidación de compra.');
        }

        $secuencialActual = (int) $puntoEmision->pv_sec_actual;

        if ($secuencialActual > (int) $puntoEmision->pv_sec_final) {
            throw new \RuntimeException('El punto de emisión para liquidación de compra ya no tiene secuenciales disponibles.');
        }

        if (
                (string) $compra->compNumeroEstablecimiento !== (string) $puntoEmision->pv_establecimiento ||
                (string) $compra->compNumeroEmision !== (string) $puntoEmision->pv_emision ||
                (string) $compra->compNumeroComprobante !== str_pad((string) $secuencialActual, 9, '0', STR_PAD_LEFT)
        ) {
            throw new \RuntimeException('Los datos del comprobante no coinciden con el punto de emisión asignado para liquidación de compra.');
        }

        $this->ccm->actualizar('cc_puntos_venta', ['pv_sec_actual' => $secuencialActual + 1], ['id' => $puntoEmision->id, 'fk_proyecto' => getProyectoId()]);
    }

    /**
     * Actualiza la bodega seleccionada para el proceso de compra.
     * @param int $bodegaId_ Identificador unico de la bodega
     * @return JSON Respuesta con el estado de la operación, mensaje descriptivo y el identificador de la bodega seleccionada
     */
    public function changeBodega(int $bodegaId_) {
        $bodegaId = (int) $bodegaId_;
        $this->session->set('bodegaIdComp', $bodegaId);
        return $this->responseSetJSON('success', 'Bodega seleccionada correctamente', $bodegaId);
    }
}
