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
class AjusteInicialController extends \App\Controllers\BaseController {

    //put your code here
    protected $dirViewModule;

    public function __construct() {

        $this->dirViewModule = 'Modules\AjustesEntrada\Views';
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

        $file = $this->request->getFile('file');

        if (!$file || !$file->isValid()) {
            return $this->responseSetJSON('error', 'Debe seleccionar un archivo Excel válido.');
        }

        try {

            // Convertir fecha a Y-m-d
            $fechaAjuste = date('Y-m-d', strtotime($dataPostAjuste->ajenFecha));

            // Leemos el Excel
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();
            $filas = $sheet->toArray(null, true, true, true);

            $errores = [];
            $importados = 0;

            foreach ($filas as $i => $row) {
                if ($i === 1) {//Es el header
                    continue;
                }

                $prodCodigo = trim($row['A'] ?? '');
                $prodNombre = trim($row['B'] ?? '');
                $precioSinIva = (float) ($row['C'] ?? 0);
                $stock = (float) ($row['D'] ?? 0);
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
                $prodCtaCompras = (float) ($row['P'] ?? '');
                $prodCtaVentas = (float) ($row['Q'] ?? '');

                if ($codigo === '') {
                    $errores[] = "Fila {$i}: El código del producto está vacío.";
                    continue;
                }

                if ($nombre === '') {
                    $errores[] = "Fila {$i}: El nombre del producto está vacío (código {$codigo}).";
                    continue;
                }
                if ($grupoNombre === '') {
                    $errores[] = "Fila {$i}: El nombre del grupo está vacío (código {$codigo}).";
                    continue;
                }

                if ($stock <= 0) {
                    $errores[] = "Fila {$i}: El stock debe ser mayor a cero para el código {$codigo}.";
                    continue;
                }

                if ($precioSinIva < 0) {
                    $errores[] = "Fila {$i}: El precio sin IVA no puede ser negativo ({$codigo}).";
                    continue;
                }

                // Manejo de lotes (si escribe algo en lote, se asume que el producto controla lote)
                $controlaLote = !empty($lote);

                // Si controla lotes, validar fechas
                if ($controlaLote) {
                    if (empty($fechaElab) || empty($fechaCaduc)) {
                        $errores[] = "Fila {$i}: El producto {$codigo} maneja lote, debe registrar Fecha Elaboración y Fecha Caducidad.";
                        continue;
                    }

                    // Convertimos a fechas
                    try {
                        $fechaElabTimestamp = strtotime($fechaElab);
                        $fechaCaducTimestamp = strtotime($fechaCaduc);

                        // Validar fechas válidas
                        if ($fechaElabTimestamp === false || $fechaCaducTimestamp === false) {
                            $errores[] = "Fila {$i}: Fechas inválidas para el producto {$codigo}.";
                            continue;
                        }

                        // Validar que caducidad >= elaboración
                        if ($fechaCaducTimestamp < $fechaElabTimestamp) {
                            $errores[] = "Fila {$i}: La fecha de caducidad no puede ser menor a la de elaboración para el producto {$codigo}.";
                            continue;
                        }

                        // Convertir a formato final
                        $fechaElab = date('Y-m-d', $fechaElabTimestamp);
                        $fechaCaduc = date('Y-m-d', $fechaCaducTimestamp);
                    } catch (\Throwable $e) {
                        $errores[] = "Fila {$i}: Formato de fecha inválido para el producto {$codigo}.";
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
                    if ($cuentaGenericaIva0 === '-1' || $cuentaGenericaIva === '-1') {
                        $errores[] = "Fila {$i}: No se encontro una de las cuentas contables genericas para el producto {$codigo}.";
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


                //=====================
                //CREAMOS EL PRODUCTO
                //=====================

                $datos = [
                    'prod_fechacreacion' => date('Y-m-d'),
                    'prod_nombre' => mb_strtoupper(trim($prodNombre), 'UTF-8'),
                    'prod_codigo' => trim($prodCodigo),
                    'prod_codigobarras' => trim($prodCodigoBarras),
                    'prod_codigobarras2' => trim($prodCodigoBarras2),
                    'prod_codigobarras3' => trim($prodCodigoBarras3),
                    'prod_existenciaminima' => 1,
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

                $productoId = $this->createAndUpdateProducto($datos, $lote);

                if (!$productoId) {
                    $errores[] = "Fila {$i}: No se pudo crear/actualizar el producto {$codigo}.";
                    continue;
                }

                // Para el carrito necesitamos datos como unidad corta, etc.
                $dataProd = $this->ccm->getData('cc_productos', ['id' => $productoId], '*', null, 1);
                if (!$dataProd) {
                    $errores[] = "Fila {$i}: No se encontró el producto recien creado {$codigo}.";
                    continue;
                }
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    public function createAndUpdateProducto($datos, $lote) {

        $existeProducto = $this->ccm->getData('cc_productos', ['prod_nombre' => $datos['prod_nombre'], 'id, prod_nombre, prod_stockactual, prod_ctrllote', null, 1]);
        if ($existeProducto) {

            if (!empty($lote)) {
                if ($existeProducto->prod_stockactual > 0 && $existeProducto->prod_ctrllote !== '1') {
                    echo "Fila 0: El producto debe de estar en stock 0 en todas las bodegas para poder activar el control de lotes para el producto {$datos['prod_codigo']}.";
                }
                $this->ccm->actualizar('cc_productos', ['prod_ctrllote' => 1], ['id' => $existeProducto->id]);
            }
            return $existeProducto->id;
        } else {
            $prodSave = $this->ccm->guardar($datos, 'cc_productos');
            return $prodSave;
        }
        
        //CONTINUAREMOS CON LOS TIPOS DE PRECIOS PA, PB, Y EL REGISTRO DEL IMPUESTOTARIFA
    }

    public function validarCampos($data) {

        $campos = [
            'ajenFecha' => 'Debe seleccionar una fecha',
            'ajenSustento' => 'Debe seleccionar un sustento',
            'ajenTipoProducto' => 'Debe seleccionar un tipo de producto',
            'ajenTipoImpuesto' => 'Debe seleccionar un impuesto',
            'ajenBodega' => 'Debe seleccionar una bodega',
            'ajenCentrocosto' => 'Debe seleccionar un centro de costos',
            'ajenMotivo' => 'Debe seleccionar un motivo de ajuste',
            'ajenEstado' => 'Debe seleccionar un estado',
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
}
