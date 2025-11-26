<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\AjustesEntrada\Controllers;

/**
 * Description of AjusteInicialController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 22 oct 2025
 * @time 4:46:30 p.m.
 */
use Modules\AjustesEntrada\Models\EntradasModel;
use Modules\AjustesEntrada\Libraries\EntradasCartLib;
use Modules\AjustesEntrada\Libraries\EntradasLib;
use Modules\AjustesEntrada\Libraries\EntradasAsientosLib;
use Modules\Comun\Models\ProductoModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AjusteInicialController extends \App\Controllers\BaseController {

    //put your code here
    protected $dirViewModule;
    protected $entradasModel;
    protected $ajenCart;
    protected $entradasLib;
    protected $prodModel;
    protected $entradasAsientoLib;

    public function __construct() {

        $this->dirViewModule = 'Modules\AjustesEntrada\Views';

        //IMPORT MODELS
        $this->entradasModel = new EntradasModel();
        $this->prodModel = new ProductoModel();

        //IMPORT LIBRERIAS
        $this->ajenCart = new EntradasCartLib();
        $this->entradasLib = new EntradasLib();
        $this->entradasAsientoLib = new EntradasAsientosLib();
    }

    public function index() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);

        $data['listaSustentos'] = $this->ccm->getData('cc_sustentos', ['sus_estado' => 1], 'sus_codigo, sus_nombre');
        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaMotivos'] = $this->ccm->getData('cc_motivos_ajuste', ['mot_estado' => 1, 'mot_tipo' => "AJUSTES"], 'id, mot_nombre');
        $data['listaCentroCostos'] = $this->ccm->getData('cc_centroscosto', ['cc_estado' => 1], 'id, cc_nombre');
        $data['listaTipoProducto'] = $this->ccm->getData('cc_tipo_producto', ['tp_estado' => 1], 'id, tp_nombre');
        $data['listaImpuestosIva'] = $this->ccm->getData('cc_impuesto_tarifa', ['fk_impuesto' => 1], 'id, impt_porcentage, impt_detalle');

        $bodegaMainUsuario = $this->ccm->getValue('cc_bodegas', $this->user->id, 'id', 'id');

        $data['bodegaId'] = $this->session->get('bodegaIdAje') ? $this->session->get('bodegaIdAje') : $bodegaMainUsuario;
        $send['view'] = view($this->dirViewModule . '\viewAjusteInicial', $data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function loadAjusteInicial() {

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

        $file = $this->request->getFile('ajenFile');

        if (!$file || !$file->isValid()) {
            return $this->responseSetJSON('error', 'Debe seleccionar un archivo Excel válido.');
        }

        try {

            // Leemos el Excel
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();
            $filas = $sheet->toArray(null, true, true, true);

            $errores = [];
            $importados = 0;

            // Limpiar carrito antes de comenzar
            $this->ajenCart->destroy();

            //INICIAMOS LA TRANSACCIÓN
            $this->db->transBegin();

            foreach ($filas as $i => $row) {
                if ($i === 1) {//Es el header o cabecera del excel
                    continue;
                }

                $prodCodigo = trim($row['A'] ?? '');
                $prodNombre = trim($row['B'] ?? '');
                $precioSinIva = (float) ($row['C'] ?? 0);
                $cantidad = (float) ($row['D'] ?? 0);
                $grupoNombre = mb_strtoupper(trim($row['E'] ?? ''), 'UTF-8');
                $subgrupoNombre = mb_strtoupper(trim($row['F'] ?? ''), 'UTF-8');
                $marcaNombre = mb_strtoupper(trim($row['G'] ?? ''), 'UTF-8');
                $unidadNombre = mb_strtoupper(trim($row['H'] ?? ''), 'UTF-8');
                $prodCodigoBarras = trim($row['I'] ?? '');
                $prodCodigoBarras2 = trim($row['J'] ?? '');
                $prodCodigoBarras3 = trim($row['K'] ?? '');
                $lote = trim($row['L'] ?? '');
                $fechaElab = trim($row['M'] ?? '');
                $fechaCaduc = trim($row['N'] ?? '');
                $precioA = (float) ($row['O'] ?? 0);
                $prodCtaCompras = ($row['P'] ?? '');
                $prodCtaVentas = ($row['Q'] ?? '');

                if ($prodCodigo === '') {
                    $errores[] = "Fila {$i}: El código del producto está vacío.";
                    continue;
                }

                if ($prodNombre === '') {
                    $errores[] = "Fila {$i}: El nombre del producto está vacío (código {$prodCodigo}).";
                    continue;
                }
                if ($grupoNombre === '') {
                    $errores[] = "Fila {$i}: El nombre del grupo está vacío (código {$prodCodigo}).";
                    continue;
                }

                if ($cantidad <= 0) {
                    $errores[] = "Fila {$i}: El stock debe ser mayor a cero para el código {$prodCodigo}.";
                    continue;
                }

                if ($precioSinIva <= 0) {
                    $errores[] = "Fila {$i}: El precio sin IVA no puede ser negativo no igual a 0 (código {$prodCodigo}).";
                    continue;
                }

                if ($precioA <= 0 || empty($precioA)) {
                    $errores[] = "Fila {$i}: El precio PA no puede ser menor o igual a 0, o no puede estar vacio para el producto ( código{$prodCodigo}).";
                    continue;
                }

                // Manejo de lotes (si escribe algo en lote, se asume que el producto controla lote)
                $controlaLote = !empty($lote);

                // Si controla lotes, validar fechas
                if ($controlaLote) {
                    if (empty($fechaElab) || empty($fechaCaduc)) {
                        $errores[] = "Fila {$i}: El producto {$prodCodigo} maneja lote, debe registrar Fecha Elaboración y Fecha Caducidad.";
                        continue;
                    }

                    // Convertimos a fechas
                    try {

                        $fechaElabTimestamp = strtotime($fechaElab);
                        $fechaCaducTimestamp = strtotime($fechaCaduc);

                        // Validar fechas válidas
                        if ($fechaElabTimestamp === false || $fechaCaducTimestamp === false) {
                            $errores[] = "Fila {$i}: Fechas inválidas para el producto {$prodCodigo}.";
                            continue;
                        }

                        // Validar que caducidad >= elaboración
                        if ($fechaCaducTimestamp < $fechaElabTimestamp) {
                            $errores[] = "Fila {$i}: La fecha de caducidad no puede ser menor a la de elaboración para el producto {$prodCodigo}.";
                            continue;
                        }

                        // Convertir a formato final
                        $fechaElab = date('Y-m-d', $fechaElabTimestamp);
                        $fechaCaduc = date('Y-m-d', $fechaCaducTimestamp);
                    } catch (\Throwable $e) {
                        $errores[] = "Fila {$i}: Formato de fecha inválido para el producto {$prodCodigo}.";
                        continue;
                    }
                } else {
                    $lote = null;
                    $fechaElab = null;
                    $fechaCaduc = null;
                }

                // ===================================================
                // 1) CREAR O BUSCAR GRUPO / SUBGRUPO / MARCA / UNIDAD
                // ===================================================
                $grupoId = $this->saveGrupo($grupoNombre);
                $subgrupoId = $this->saveSubGrupo($subgrupoNombre, $grupoId);
                $marcaId = $this->saveMarca($marcaNombre);
                $unidadId = $this->saveUnidadMedida($unidadNombre);

                $tipoProdId = $dataPostAjuste->ajenTipoProducto;
                $esServicio = 0;
                if ($tipoProdId === '3') {
                    $esServicio = 1;
                }

                $prodIvaPorcentajeId = $dataPostAjuste->ajenImpuestoIva;
                $prodIvaPorcentaje = $this->ccm->getValue('cc_impuesto_tarifa', $prodIvaPorcentajeId, "impt_porcentage", "id");

                //Obtenemos cuentas contables genéricas por código
                $cuentaGenericaIva0 = $this->ccm->getValue('cc_cuenta_contabledet', '1.01.04.01.02', "ctad_codigo", "ctad_codigo");
                $cuentaGenericaIva = $this->ccm->getValue('cc_cuenta_contabledet', '1.01.04.01.02', "ctad_codigo", "ctad_codigo");

                //Si una de las columnas de cuentas contables del excel viene vacia, validamos la existencia de la cuenta generica
                if (empty($prodCtaVentas) || empty($prodCtaCompras)) {
                    if ($cuentaGenericaIva0 === -1 || $cuentaGenericaIva === -1) {
                        $errores[] = "Fila {$i}: No se encontro una de las cuentas contables genericas (1.01.04.01.50) para el producto {$prodCodigo}.";
                        continue;
                    }
                }

                switch ($prodIvaPorcentajeId) {
                    case '1': // IVA 0%
                        $cuentaComprasGenerico = $cuentaGenericaIva0;
                        $cuentaVentasGenerico = $cuentaGenericaIva0;
                        break;

                    case '2': // IVA 15%
                        $cuentaComprasGenerico = $cuentaGenericaIva;
                        $cuentaVentasGenerico = $cuentaGenericaIva;
                        break;

                    default:
                        $cuentaComprasGenerico = null;
                        $cuentaVentasGenerico = null;
                        break;
                }

                $datos = [
                    'prod_fechacreacion' => date('Y-m-d'),
                    'prod_nombre' => mb_strtoupper(trim($prodNombre), 'UTF-8'),
                    'prod_codigo' => trim($prodCodigo),
                    'prod_codigobarras' => trim($prodCodigoBarras),
                    'prod_codigobarras2' => trim($prodCodigoBarras2),
                    'prod_codigobarras3' => trim($prodCodigoBarras3),
                    'prod_existenciaminima' => 5,
                    'prod_existenciamaxima' => 10,
                    'prod_venta' => 1,
                    'prod_compra' => 1,
                    'prod_isservicio' => $esServicio,
                    'prod_isgasto' => 0,
                    'fk_unidadmedida' => $unidadId,
                    'fk_subgrupo' => $subgrupoId,
                    'fk_marca' => $marcaId,
                    'fk_tipoproducto' => $tipoProdId,
                    'prod_ivaporcentage' => $prodIvaPorcentaje,
                    'prod_iceporcentage' => null,
                    'prod_tiene_ice' => 0,
                    'prod_ispromo' => 0,
                    'fk_cuentacontableventas' => $prodCtaVentas ? $prodCtaVentas : $cuentaVentasGenerico,
                    'fk_cuentacontablecompras' => $prodCtaCompras ? $prodCtaCompras : $cuentaComprasGenerico,
                    'prod_estado' => 1,
                    'prod_issuperproducto' => 0,
                    'prod_ctrllote' => $controlaLote ? 1 : 0,
                    'prod_facturar_ennegativo' => 0,
                    'prod_facturar_precio_inferiorcosto' => 0,
                ];

                //=====================
                //CREAMOS EL PRODUCTO
                //=====================
                $productoData = $this->createAndUpdateProducto($datos, $lote);
                $productoId = $productoData['data'];

                if ($productoData['status'] === 'warning') {
                    $errores[] = "Fila {$i}: {$productoData['msg']} {$prodCodigo}.";
                    continue;
                }
                if (empty($productoId)) {
                    $errores[] = "Fila {$i}: No se pudo crear/actualizar el producto {$prodCodigo}.";
                    continue;
                }

                //=====================
                //REGISTRAMOS LOS TIPOS DE PRECIO DEL PRODUCTO PA, PB
                //=====================
                $tipoPrecioData = $this->createTiposPrecio($productoId, $precioA);

                if ($tipoPrecioData['status'] === 'warning') {
                    $errores[] = "Fila {$i}: {$tipoPrecioData['msg']} {$prodCodigo}.";
                    continue;
                }

                //=====================
                //REGISTRAMOS EL IMPUESTO DEL PRODUCTO (IVA)
                //=====================
                $this->createImpuestoIvaProducto($productoId, $prodIvaPorcentajeId);

                $producto = $this->entradasModel->searchProductoDataById($productoId);

                if (empty($producto)) {
                    $errores[] = "Fila {$i}: No se encontró el producto recien creado {$prodCodigo}.";
                    continue;
                }

                $impuestos = $this->prodModel->getImpuestoTarifa($producto->id);
                $tarifaIva = isset($impuestos[0]->impt_porcentage) ? $impuestos[0]->impt_porcentage : 0;
                $tarifaIce = isset($impuestos[1]->impt_porcentage) ? $impuestos[1]->impt_porcentage : 0;

                $item = [
                    "id" => (int) $producto->id,
                    "qty" => (float) $cantidad,
                    "codigo" => $producto->prod_codigo,
                    "name" => $producto->prod_nombre,
                    "unidadMedida" => $producto->um_nombre_corto,
                    "price" => (float) $precioSinIva,
                    "stock" => 0,
                    "stockBodega" => 0,
                    "ivaPorcent" => $tarifaIva,
                    "icePorcent" => $tarifaIce,
                    "tieneLote" => $producto->prod_ctrllote,
                    "permitirDuplicados" => 1,
                    "lote" => $lote,
                    "fechaElaboracion" => $fechaElab,
                    "fechaCaducidad" => $fechaCaduc,
                    "servicio" => $producto->prod_isservicio,
                ];

                $this->ajenCart->insert($item);
                $importados++;
            }

            if ($importados === 0) {//ESTO SE EJECUTA SOLO SI NO HAY NINGUN PRODUCTO REGISTRADO
                $msg = 'No se importaron productos válidos.';
                $msg .= "<span class='fw-semibold text-danger'><br><br><strong>Errores encontrados:</strong><br>" . implode('<br>', $errores) . '</span>';
                $this->db->transRollback();
                return $this->responseSetJSON('warning', $msg);
            }

            if (!empty($errores)) {
                $msg = "Importación completada: {$importados} producto(s) agregado(s).";
                $msg .= "<span class='fw-semibold text-danger'><br><br><strong>Errores encontrados:</strong><br>" . implode('<br>', $errores) . '</span>';

                $dataResp = [
                    'totalImportados' => $importados,
                    'errores' => $errores
                ];
                $this->db->transRollback();
                return $this->responseSetJSON('error', $msg, $dataResp);
            }
//            else {
//                return $this->responseSetJSON('success', 'exito');
//            }
            //=================================
            //REGISTRAMOS EL AJUSTE INICIAL
            //=================================
            $ajusteData = $this->crearAjusteInicial($dataPostAjuste);

            if ($ajusteData['status'] !== 'success') {
                $this->db->transRollback();
                return $this->responseSetJSON("error", "<h5> Ha ocurrido un error interno al registrar el ajuste inicial<br> {$ajusteData['msg']}</h5>");
            }
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return $this->responseSetJSON('error', 'Error interno en transacción al registra el ajuste inicial');
            }

            //SI TODO MARCHO BIEN REALIZO EL COMMIT
            $secuencail = $this->ccm->getValueWhere('cc_ajuste_entrada', ['id' => $ajusteData['id']], 'ajen_secuencial');
            $this->db->transCommit();
            $this->ajenCart->destroy();
            $this->logs->logSuccess('Ajuste registrado exitosamente ID: ' . $ajusteData['id']);
            log_message('info', "[Ajuste Entrada] Ajuste registrado exitosamente, DocID: {$ajusteData['id']}");

            $dataResponse = ['id' => $ajusteData['id'], 'ajen_secuencial' => $secuencail];

            return $this->responseSetJSON("success", "<h5> Ajuste inicial #{$secuencail} registrado exitosamente <br>Documento excel cargado exitosamente</h5>", $dataResponse);
        } catch (Exception $exc) {
            $this->db->transRollback();
            $this->logs->logError('Ha ocurrido un error al registrar el Ajuste');
            log_message('error', "[Ajuste Entrada] Error al registrar");
            return $this->responseSetJSON('error', '<br>Error al tratar de crear el Ajuste <br> ' . $exc->getMessage() . $exc->getTraceAsString());
        }
    }

    public function crearAjusteInicial($dataPost) {

        $cartData = $this->showDetailCart(1);

        $dataPostAjuste = (object) [
                    'ajenFecha' => $dataPost->ajenFecha,
                    'ajenObservaciones' => $dataPost->ajenObservacion,
                    'ajenEstado' => 2,
                    'ajenTipo' => 'AJUSTE_INICIAL',
                    'ajenMotivo' => $dataPost->ajenMotivo,
                    'ajenBodega' => $dataPost->ajenBodega,
                    'ajenSustento' => $dataPost->ajenSustento,
                    'ajenProveedor' => $dataPost->ajenProveedor,
                    'ajenCentrocosto' => $dataPost->ajenCentrocosto,
                    'ajenPermitirDuplicados' => 1,
        ];

        $ajusteId = $this->entradasLib->saveAjuste($cartData, $dataPostAjuste);
        $estadoAjuste = $this->ccm->getValue('cc_ajuste_entrada', ['id' => $ajusteId], 'ajen_estado');

        if (!$ajusteId) {
            $this->db->transRollback();
            return ['status' => 'error', 'msg' => 'No se pudo crear el ajuste'];
        }

        foreach ($cartData->cartContent as $val) {

            // Validación de control de lotes
            $lote = null;
            if ($val->tieneLote === '1') {
                if ((empty($val->lote) || empty($val->fechaElaboracion) || empty($val->fechaCaducidad))) {
                    $this->db->transRollback();
                    return [
                        'status' => 'warning',
                        'msg' => 'El producto ' . $val->name . ' maneja control de lotes<br> Por favor revise el LOTE y sus respectivas FECHAS',
                    ];
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
                return ['status' => 'error', 'msg' => 'Ha ocurrido un error al registrar el producto ' . $val->name . ' en el detalle del ajuste'];
            }

            // Actualizamos el kardex solo si el ajuste está aprobado y no es servicio
            if ($estadoAjuste === '2' && $val->servicio === '0') {

                $kardexOk = $this->entradasLib->updateKardex($ajusteId, $val, $lote, $dataPostAjuste);
                if ($kardexOk['status'] !== 'success') {
                    $this->db->transRollback();
                    return ['status' => $kardexOk['status'], 'msg' => $kardexOk['msg']];
                }
            }
        }

        if ($estadoAjuste === '2') {
            $responseAsiento = $this->entradasAsientoLib->generarAsiento($ajusteId);
            if ($responseAsiento['status'] !== 'success') {
                $this->db->transRollback();
                return ['status' => $responseAsiento['status'], 'msg' => $responseAsiento['msg']];
            }
        }

        return ['status' => 'success', 'msg' => 'EXITO', 'id' => $ajusteId];
    }

    public function createAndUpdateProducto($datos, $lote) {

        $existeProducto = $this->ccm->getData('cc_productos', ['prod_nombre' => $datos['prod_nombre']], 'id, prod_nombre, prod_stockactual, prod_ctrllote', null, 1);

        if ($existeProducto) {
            if (!empty($lote)) {
                if ($existeProducto->prod_stockactual > 0 && $existeProducto->prod_ctrllote !== '1') {
                    $msg = "El producto debe de estar en stock 0 en todas las bodegas para poder activar el control de lotes para el producto ";
                    return ['status' => 'warning', 'data' => '', 'msg' => $msg];
                }
                $this->ccm->actualizar('cc_productos', ['prod_ctrllote' => 1], ['id' => $existeProducto->id]);
            }
            return ['status' => 'success', 'data' => $existeProducto->id];
        } else {
            $prodSave = $this->ccm->guardar($datos, 'cc_productos');
            return ['status' => 'success', 'data' => $prodSave];
        }
    }

    public function createTiposPrecio($productoId, $precioA) {

        $tipoPrecios = $this->ccm->getData('cc_tipo_precios', ['tpc_estado' => 1]);

        if ($precioA) {
            foreach ($tipoPrecios as $val) {
                if ($val->tpc_nombre === "pA" || $val->tpc_nombre === "PA") {
                    $datos = [
                        "fk_tipo_precio" => $val->id,
                        "fk_producto" => $productoId,
                        "pp_valor" => $precioA,
                    ];

                    $existe = $this->ccm->getData('cc_producto_precios', ['fk_producto' => $productoId, 'fk_tipo_precio' => $val->id]);
                    if ($existe) {
                        $this->ccm->actualizar('cc_producto_precios', $datos, ['fk_producto' => $productoId, 'fk_tipo_precio' => $val->id]);
                    } else {
                        $this->ccm->guardar($datos, "cc_producto_precios");
                    }
                }
            }
            return ['status' => 'success'];
        } else {
            return ['status' => 'warning', 'msg' => 'El precio pA es requerido para el producto '];
        }
    }

    public function createImpuestoIvaProducto($productoId, $prodIvaPorcentajeId) {

        $existeImpuestoInProducto = $this->ccm->getData('cc_producto_impuestotarifa', ['fk_producto' => $productoId]); //SI YA EXISTE UN IMPUESTO REGISTRADO EN EL PRODUCTO YA NO HACEMOS NADA LO DEJAMOS TAL CUAL  ESTA

        if (empty($existeImpuestoInProducto)) {
            $impuesto = $this->ccm->getValue('cc_impuesto_tarifa', $prodIvaPorcentajeId, "fk_impuesto", "id");
            $datosImpuestoTarifa = [
                "fk_producto" => $productoId,
                "fk_impuestotarifa" => $prodIvaPorcentajeId,
                "fk_impuesto" => $impuesto,
            ];
            $this->ccm->guardar($datosImpuestoTarifa, 'cc_producto_impuestotarifa');
        }
    }

    public function validarCampos($data) {

        $campos = [
            'ajenFecha' => 'Debe seleccionar una fecha',
            'ajenSustento' => 'Debe seleccionar un sustento',
            'ajenTipoProducto' => 'Debe seleccionar un tipo de producto',
            'ajenImpuestoIva' => 'Debe seleccionar un impuesto',
            'ajenBodega' => 'Debe seleccionar una bodega',
            'ajenCentrocosto' => 'Debe seleccionar un centro de costos',
            'ajenMotivo' => 'Debe seleccionar un motivo de ajuste',
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

    public function responseSetJSON($status, $mensaje, $data = null) {
        return $this->response->setJSON([
                    'status' => $status,
                    'msg' => $mensaje,
                    'data' => $data,
        ]);
    }

    public function saveGrupo($valorNombre) {

        $existe = $this->ccm->getData('cc_grupos', ['gr_nombre' => $valorNombre], 'id', null, 1);
        if ($existe) {
            return $existe->id;
        }
        $datosInsert = [
            'gr_nombre' => strtoupper($valorNombre) ?: 'SIN GRUPO',
            'gr_descripcion' => 'PRODUCTOS EN GRUPO ' . strtoupper($valorNombre) ?: 'SIN GRUPO',
            'gr_estado' => 1,
            'gr_fecha_creacion' => date('Y-m-d')
        ];
        return $this->ccm->guardar($datosInsert, 'cc_grupos');
    }

    public function saveSubGrupo($valorNombre, $grupoId) {
        $existe = $this->ccm->getData('cc_subgrupos', ['sgr_nombre' => $valorNombre], 'id', null, 1);
        if ($existe) {
            return $existe->id;
        }
        $datosInsert = [
            'sgr_nombre' => strtoupper($valorNombre) ?: 'SIN SUBGRUPO',
            'sgr_detalle' => 'PRODUCTOS EN SUBGRUPO ' . strtoupper($valorNombre) ?: 'SIN SUBGRUPO',
            'fk_grupo' => $grupoId,
            'sgr_estado' => 1,
            'sgr_fecha_creacion' => date('Y-m-d')
        ];
        return $this->ccm->guardar($datosInsert, 'cc_subgrupos');
    }

    public function saveMarca($valorNombre) {
        $existe = $this->ccm->getData('cc_marcas', ['mrc_nombre' => $valorNombre], 'id', null, 1);
        if ($existe) {
            return $existe->id;
        }
        $datosInsert = [
            'mrc_nombre' => strtoupper($valorNombre) ?: 'SIN MARCA',
            'mrc_estado' => 1,
            'mrc_fecha_creacion' => date('Y-m-d')
        ];

        return $this->ccm->guardar($datosInsert, 'cc_marcas');
    }

    public function saveUnidadMedida($valorNombre) {
        $existe = $this->ccm->getData('cc_unidades_medida', ['um_nombre' => $valorNombre], 'id', null, 1);
        if ($existe) {
            return $existe->id;
        }
        $datosInsert = [
            'um_nombre' => $valorNombre ?: 'UNIDAD',
            'um_nombre_corto' => $valorNombre ? mb_substr($valorNombre, 0, 3) : 'UND',
            'um_estado' => 1,
            'um_fecha_creacion' => date('Y-m-d')
        ];
        return $this->ccm->guardar($datosInsert, 'cc_unidades_medida');
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
