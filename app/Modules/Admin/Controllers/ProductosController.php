<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of ProductosController
 *
  /**
 * @author CRISTIAN PAZ
 * @date 15 abr. 2024
 * @time 11:42:41
 */
use Modules\Admin\Models\ProductosModel;

class ProductosController extends \App\Controllers\BaseController {

    protected $dirViewModule;
    protected $prodModel;

    public function __construct() {
        $this->dirViewModule = 'Modules\Admin\Views';
        $this->prodModel = new ProductosModel();
    }

    public function index() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $data2['listaUnidadesMedida'] = $this->ccm->getData('cc_unidades_medida', ['um_estado' => 1], '*');
        $data2['listaMarcas'] = $this->ccm->getData('cc_marcas', ['mrc_estado' => 1], '*');
        $data2['listaTipoProducto'] = $this->ccm->getData('cc_tipo_producto', ['tp_estado' => 1], '*');
        $data2['listaImpuestosTarifa'] = $this->ccm->getData('cc_impuesto_tarifa', ['fk_impuesto' => 1], '*');
        $data2['listaImpuestosICE'] = $this->ccm->getData('cc_impuesto_tarifa', ['fk_impuesto' => 2], '*');
        $data2['listaSubgrupos'] = $this->ccm->getData('cc_subgrupos', ['sgr_estado' => 1], '*');
        $data2['listaGrupos'] = $this->ccm->getData('cc_grupos', ['gr_estado' => 1], '*');
        $data2['listaCtaContable'] = $this->ccm->getData('cc_cuenta_contabledet', ['ctad_estado' => 1], 'ctad_codigo, CONCAT(ctad_codigo," ",ctad_nombre_cuenta)cuentadet');
        $data2['listaTiposPvp'] = $this->ccm->getData('cc_tipo_precios', ['tpc_estado' => 1], "*");

        $valAutocodigo = $this->ccm->getData('cc_autocodigo', $where = null, 'cod', null, 1);
        $autocodigo = str_pad(($valAutocodigo->cod + 1), 6, 0, STR_PAD_LEFT);
        $data2['autocodigo'] = getSettings("ABREVIATURA_AUTO_COD") . $autocodigo;
        $data2['user'] = $this->user;
        $send['view'] = view($this->dirViewModule . '\productos\viewProductos', $data2);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function consultarAutoCodigo() {
        $valAutocodigo = $this->ccm->getData('cc_autocodigo', $where = null, 'cod', $order = null, 1);
        $codigo = str_pad(($valAutocodigo->cod + 1), 6, 0, STR_PAD_LEFT);
        $autocodigo = getSettings("ABREVIATURA_AUTO_COD") . $codigo;
        return $this->response->setJSON($autocodigo);
    }

    public function getProductos() {

        $data = json_decode(file_get_contents("php://input"));

        $whereQuery = [];

        if (!empty($data->idProd)) {
            $whereQuery['tb1.id'] = $data->idProd;
        }

        if (!empty($data->codProd)) {
            $whereQuery['tb1.id'] = $data->codProd;
        }

        if (!empty($data->grupo) && $data->grupo != "-1") {
            $whereQuery['tb5.id'] = $data->grupo;
        }

        if ($data->stock != "-1") {
            if ($data->stock == 1) {
                $whereQuery['tb1.prod_stockactual >'] = 1;
            } else {
                $whereQuery['tb1.prod_stockactual <'] = 0;
            }
        }
        if ($data->estado != "-1") {
            if ($data->estado == 1) {
                $whereQuery['tb1.prod_estado'] = 1;
            } else {
                $whereQuery['tb1.prod_estado'] = 0;
            }
        }
        if ($data->impuesto != "-1") {
            if ($data->impuesto == 2) {
                $whereQuery['tb7.fk_impuestotarifa'] = 2;
            } else {
                $whereQuery['tb7.fk_impuestotarifa'] = 1;
            }
        }

        $response = $this->prodModel->getProductos($whereQuery);
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }

    public function getPreciosProducto($idProducto) {
        $response = $this->ccm->getData('cc_producto_precios', ['fk_producto' => $idProducto], "fk_tipo_precio, pp_valor");
        return $this->response->setJSON($response);
    }

    public function saveProducto() {
        $prodNombre = $this->request->getPost('prodNombre');
        $prodCodigo = $this->request->getPost('prodCodigo');
        $prodCodigoBarras = $this->request->getPost('prodCodigoBarras');
        $prodCodigoBarras2 = $this->request->getPost('prodCodigoBarras2');
        $prodCodigoBarras3 = $this->request->getPost('prodCodigoBarras3');
        $prodExistenciaMinima = $this->request->getPost('prodExistenciaMinima');
        $prodExistenciaMaxima = $this->request->getPost('prodExistenciaMaxima');
        $prodVenta = $this->request->getPost('prodVenta'); //BOOLEAN
        $prodCompra = $this->request->getPost('prodCompra'); //BOOLEAN
        $prodIsServicio = $this->request->getPost('prodIsServicio'); //BOOLEAN
        $prodIsGasto = $this->request->getPost('prodIsGasto'); //BOOLEAN
        $prodValorMedida = $this->request->getPost('prodValorMedida');
        $prodUnidadMedida = $this->request->getPost('prodUnidadMedida');
        $prodSubgrupo = $this->request->getPost('prodSubgrupo');
        $prodMarca = $this->request->getPost('prodMarca');
        $prodTipoProducto = $this->request->getPost('prodTipoProducto');

        $prodIvaPorcentajeId = $this->request->getPost('prodIvaPorcentajeId');
        $prodIvaPorcentaje = $this->ccm->getValue('cc_impuesto_tarifa', $prodIvaPorcentajeId, "impt_porcentage", "id");

        $prodIcePorcentajeId = $this->request->getPost('prodIcePorcentajeId');
        $prodIcePorcentaje = $this->ccm->getValue('cc_impuesto_tarifa', $prodIcePorcentajeId, "impt_porcentage", "id");
        $prodTieneICE = $this->request->getPost('prodTieneICE');

        $prodIsPromo = $this->request->getPost('prodIsPromo'); //BOOLEAN
        $prodPvpPromo = $this->request->getPost('prodPvpPromo');
        $prodEspecificaciones = $this->request->getPost('prodEspecificaciones');
        $prodCtaCompras = $this->request->getPost('prodCtaCompras');
        $prodCtaVentas = $this->request->getPost('prodCtaVentas');
        $prodIsSuperProducto = $this->request->getPost('prodIsSuperProducto'); //BOOLEAN
        $prodCtrlLote = $this->request->getPost('prodCtrlLote'); //BOOLEAN
        $prodFacturarEnNegativo = $this->request->getPost('prodFacturarEnNegativo'); //BOOLEAN
        $prodFacturarPrecioInferiorCosto = $this->request->getPost('prodFacturarPrecioInferiorCosto'); //BOOLEAN
        $prodImagen = $this->request->getPost('prodImagen');
        $prodEstado = $this->request->getPost('prodEstado'); //BOOLEAN

        $this->validation->setRules([
            'prodNombre' => ['label' => 'Nombre  Producto', 'rules' => 'trim|required'],
            'prodCodigo' => ['label' => 'Código  Producto', 'rules' => 'trim|required'],
            'prodUnidadMedida' => ['label' => 'Unidad de medida  Producto', 'rules' => 'trim|required'],
            'grupo' => ['label' => 'Grupo  Producto', 'rules' => 'trim|required'],
            'prodSubgrupo' => ['label' => 'Subgrupo  Producto', 'rules' => 'trim|required'],
            'prodTipoProducto' => ['label' => 'Tipo de Producto', 'rules' => 'trim|required'],
            'prodIvaPorcentajeId' => ['label' => 'Tipo de Impuesto de Producto', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_productos', ['prod_nombre' => trim($prodNombre)]);
            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un producto registrado con el nombre ' . $prodNombre . '</h5>';
                return $this->response->setJson($response);
            }
            $existeCod = $this->ccm->getData('cc_productos', ['prod_codigo' => trim($prodCodigo)]);
            if (count($existeCod) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un producto registrado con el código ' . $prodCodigo . '</h5>';
                return $this->response->setJson($response);
            }
            $datos = [
                'prod_fechacreacion' => date('Y-m-d'),
                'prod_nombre' => mb_strtoupper(trim($prodNombre), 'UTF-8'),
                'prod_codigo' => trim($prodCodigo),
                'prod_codigobarras' => trim($prodCodigoBarras),
                'prod_codigobarras2' => trim($prodCodigoBarras2),
                'prod_codigobarras3' => trim($prodCodigoBarras3),
                'prod_imagen' => $prodImagen,
                'prod_existenciaminima' => $prodExistenciaMinima,
                'prod_existenciamaxima' => $prodExistenciaMaxima,
                'prod_stockactual' => 0,
                'prod_costoalto' => 0,
                'prod_costopromedio' => 0,
                'prod_costoultimo' => 0,
                'prod_venta' => $prodVenta == "true" ? 1 : 0,
                'prod_compra' => $prodCompra == "true" ? 1 : 0,
                'prod_isservicio' => $prodIsServicio == "true" ? 1 : 0,
                'prod_isgasto' => $prodIsGasto == "true" ? 1 : 0,
                'prod_valormedida' => $prodValorMedida,
                'fk_unidadmedida' => $prodUnidadMedida,
                'fk_subgrupo' => $prodSubgrupo,
                'fk_marca' => $prodMarca ? $prodMarca : NULL,
                'fk_tipoproducto' => $prodTipoProducto,
                'prod_ivaporcentage' => $prodIvaPorcentaje,
                'prod_iceporcentage' => $prodIcePorcentaje,
                'prod_tiene_ice' => $prodTieneICE,
                'prod_ispromo' => $prodIsPromo == "true" ? 1 : 0,
                'prod_pvppromo' => $prodPvpPromo,
                'prod_especificaciones' => $prodEspecificaciones,
                'fk_cuentacontableventas' => $prodCtaVentas ? $prodCtaVentas : NULL,
                'fk_cuentacontablecompras' => $prodCtaCompras ? $prodCtaCompras : NULL,
                'prod_estado' => $prodEstado == "true" ? 1 : 0,
                'prod_issuperproducto' => $prodIsSuperProducto == "true" ? 1 : 0,
                'prod_ctrllote' => $prodCtrlLote == "true" ? 1 : 0,
                'prod_facturar_ennegativo' => $prodFacturarEnNegativo == "true" ? 1 : 0,
                'prod_facturar_precio_inferiorcosto' => $prodFacturarPrecioInferiorCosto == "true" ? 1 : 0,
            ];

            $this->db->transBegin();
            $prodSave = $this->ccm->guardar($datos, 'cc_productos');

            $tipoPrecioId = $this->request->getPost("tipoPrecioId");
            $tipoPrecioVal = $this->request->getPost("tipoPrecioVal");

            $tipoPrecios = explode(",", $tipoPrecioId);
            $preciosVal = explode(",", $tipoPrecioVal);

            //Recorro todos los tipos de precio que envio desde el front
            //Si el campo de precio tiene un valor registro el tipo de precio con el producto y su costo
            //Si no trae valor simplemente no lo registro salvo que sea tipo de precio pA, el sistema te notificara que
            //es un campo requerido, si es pB no pasa nada.

            foreach ($tipoPrecios as $index => $val) {
                if (!empty($preciosVal[$index])) {
                    $datos = [
                        "fk_tipo_precio" => $val,
                        "fk_producto" => $prodSave,
                        "pp_valor" => $preciosVal[$index],
                    ];
                    $this->ccm->guardar($datos, "cc_producto_precios");
                } else {
                    //Voy a consultar si el precio es pA para notificar al usuario que este es un campo requerido
                    $pvp = $this->ccm->getData("cc_tipo_precios", ["id" => $val]);
                    if ($pvp[0]->tpc_nombre == "pA" || $pvp[0]->tpc_nombre == "PA") {
                        $response['status'] = 'warning';
                        $response['msg'] = '<h5>El precio pA es requerido</h5>';
                        return $this->response->setJson($response);
                    }
                }
            }
            $impuesto = $this->ccm->getValue('cc_impuesto_tarifa', $prodIvaPorcentajeId, "fk_impuesto", "id");
            $datosImpuestoTarifa = [
                "fk_producto" => $prodSave,
                "fk_impuestotarifa" => $prodIvaPorcentajeId,
                "fk_impuesto" => $impuesto,
            ];
            $this->ccm->guardar($datosImpuestoTarifa, 'cc_producto_impuestotarifa');

            if ($prodTieneICE) {
                $impuesto = $this->ccm->getValue('cc_impuesto_tarifa', $prodIcePorcentajeId, "fk_impuesto", "id");
                $datosImpuestoTarifaIce = [
                    "fk_producto" => $prodSave,
                    "fk_impuestotarifa" => $prodIcePorcentajeId,
                    "fk_impuesto" => $impuesto,
                ];
                $this->ccm->guardar($datosImpuestoTarifaIce, 'cc_producto_impuestotarifa');
            }

            $codigo = $this->ccm->getData('cc_autocodigo', $where = null, 'cod', $order = null, 1);
            $this->ccm->actualizar("cc_autocodigo", ['cod' => $codigo->cod + 1], $where = null);
            if ($this->db->transStatus == false) {
                $response['status'] = 'error';
                $response['msg'] = '<h5>Ha ocurrido un error al tratar de crear el producto ' . $prodNombre . '</h5>';
                $this->db->transRollback();
            } else {
                $this->logs->logSuccess('SE HA CREADO UN PRODUCTO CON EL ID ' . $prodSave);
                $response['status'] = 'success';
                $response['msg'] = '<h5>Producto Registrado Exitosamente <br><hr> ' . $prodSave . ' : ' . mb_strtoupper(trim($prodNombre)) . '</h5>';
                $this->db->transCommit();
            }
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'prodNombre' => $this->validation->getError('prodNombre'),
                'prodCodigo' => $this->validation->getError('prodCodigo'),
                'prodUnidadMedida' => $this->validation->getError('prodUnidadMedida'),
                'grupo' => $this->validation->getError('grupo'),
                'prodSubgrupo' => $this->validation->getError('prodSubgrupo'),
                'prodTipoProducto' => $this->validation->getError('prodTipoProducto'),
                'prodIvaPorcentaje' => $this->validation->getError('prodIvaPorcentaje'),
            ];
        }
        return $this->response->setJson($response);
    }

    public function updateProducto() {
        $prodNombre = $this->request->getPost('prodNombre');
        $prodCodigo = $this->request->getPost('prodCodigo');
        $prodCodigoBarras = $this->request->getPost('prodCodigoBarras');
        $prodCodigoBarras2 = $this->request->getPost('prodCodigoBarras2');
        $prodCodigoBarras3 = $this->request->getPost('prodCodigoBarras3');
        $prodExistenciaMinima = $this->request->getPost('prodExistenciaMinima');
        $prodExistenciaMaxima = $this->request->getPost('prodExistenciaMaxima');
        $prodVenta = $this->request->getPost('prodVenta'); //BOOLEAN
        $prodCompra = $this->request->getPost('prodCompra'); //BOOLEAN
        $prodIsServicio = $this->request->getPost('prodIsServicio'); //BOOLEAN
        $prodIsGasto = $this->request->getPost('prodIsGasto'); //BOOLEAN
        $prodValorMedida = $this->request->getPost('prodValorMedida');
        $prodUnidadMedida = $this->request->getPost('prodUnidadMedida');
        $prodSubgrupo = $this->request->getPost('prodSubgrupo');
        $prodMarca = $this->request->getPost('prodMarca');
        $prodTipoProducto = $this->request->getPost('prodTipoProducto');

        $prodIvaPorcentajeId = $this->request->getPost('prodIvaPorcentajeId');
        $prodIvaPorcentaje = $this->ccm->getValue('cc_impuesto_tarifa', $prodIvaPorcentajeId, "impt_porcentage", "id");

        $prodIcePorcentajeId = $this->request->getPost('prodIcePorcentajeId');
        $prodIcePorcentaje = $this->ccm->getValue('cc_impuesto_tarifa', $prodIcePorcentajeId, "impt_porcentage", "id");
        $prodTieneICE = $this->request->getPost('prodTieneICE');

        $prodIsPromo = $this->request->getPost('prodIsPromo'); //BOOLEAN
        $prodPvpPromo = $this->request->getPost('prodPvpPromo');
        $prodEspecificaciones = $this->request->getPost('prodEspecificaciones');
        $prodCtaCompras = $this->request->getPost('prodCtaCompras');
        $prodCtaVentas = $this->request->getPost('prodCtaVentas');
        $prodIsSuperProducto = $this->request->getPost('prodIsSuperProducto'); //BOOLEAN
        $prodCtrlLote = $this->request->getPost('prodCtrlLote'); //BOOLEAN
        $prodFacturarEnNegativo = $this->request->getPost('prodFacturarEnNegativo'); //BOOLEAN
        $prodFacturarPrecioInferiorCosto = $this->request->getPost('prodFacturarPrecioInferiorCosto'); //BOOLEAN
        $prodImagen = $this->request->getPost('prodImagen');
        $prodEstado = $this->request->getPost('prodEstado'); //BOOLEAN

        $idProd = $this->request->getPost('idProd');
        $nameAux = $this->request->getPost('nameAux');
        $codeAux = $this->request->getPost('codeAux');

        $this->validation->setRules([
            'prodNombre' => ['label' => 'Nombre  Producto', 'rules' => 'trim|required'],
            'prodCodigo' => ['label' => 'Código  Producto', 'rules' => 'trim|required'],
            'prodUnidadMedida' => ['label' => 'Unidad de medida  Producto', 'rules' => 'trim|required'],
            'grupo' => ['label' => 'Grupo  Producto', 'rules' => 'trim|required'],
            'prodSubgrupo' => ['label' => 'Subgrupo  Producto', 'rules' => 'trim|required'],
            'prodTipoProducto' => ['label' => 'Tipo de Producto', 'rules' => 'trim|required'],
            'prodIvaPorcentajeId' => ['label' => 'Tipo de Impuesto de Producto', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_productos', ['prod_nombre' => trim($prodNombre)], '*', $orderBy = null, 1);

            if ($existe && $existe->prod_nombre != $nameAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un producto registrado con el nombre ' . $prodNombre . '</h5>';
                return $this->response->setJson($response);
            }
            $existeCod = $this->ccm->getData('cc_productos', ['prod_codigo' => trim($prodCodigo)], 'prod_codigo', $orderBy = null, 1);
            if ($existeCod && $existeCod->prod_codigo != $codeAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un producto registrado con el código ' . $prodCodigo . '</h5>';
                return $this->response->setJson($response);
            }
            $datos = [
                'prod_nombre' => mb_strtoupper(trim($prodNombre), 'UTF-8'),
                'prod_codigo' => trim($prodCodigo),
                'prod_codigobarras' => trim($prodCodigoBarras),
                'prod_codigobarras2' => trim($prodCodigoBarras2),
                'prod_codigobarras3' => trim($prodCodigoBarras3),
                'prod_imagen' => $prodImagen,
                'prod_existenciaminima' => $prodExistenciaMinima,
                'prod_existenciamaxima' => $prodExistenciaMaxima,
                'prod_stockactual' => 0,
                'prod_costoalto' => 0,
                'prod_costopromedio' => 0,
                'prod_costoultimo' => 0,
                'prod_venta' => $prodVenta == "true" ? 1 : 0,
                'prod_compra' => $prodCompra == "true" ? 1 : 0,
                'prod_isservicio' => $prodIsServicio == "true" ? 1 : 0,
                'prod_isgasto' => $prodIsGasto == "true" ? 1 : 0,
                'prod_valormedida' => $prodValorMedida,
                'fk_unidadmedida' => $prodUnidadMedida,
                'fk_subgrupo' => $prodSubgrupo,
                'fk_marca' => $prodMarca == "null" ? NULL : $prodMarca,
                'fk_tipoproducto' => $prodTipoProducto,
                'prod_ivaporcentage' => $prodIvaPorcentaje,
                'prod_iceporcentage' => $prodIcePorcentaje,
                'prod_tiene_ice' => $prodTieneICE,
                'prod_ispromo' => $prodIsPromo == "true" ? 1 : 0,
                'prod_pvppromo' => $prodPvpPromo,
                'prod_especificaciones' => $prodEspecificaciones,
                'fk_cuentacontableventas' => $prodCtaVentas == "null" ? NULL : $prodCtaVentas,
                'fk_cuentacontablecompras' => $prodCtaCompras == "null" ? NULL : $prodCtaCompras,
                'prod_estado' => $prodEstado == "true" ? 1 : 0,
                'prod_issuperproducto' => $prodIsSuperProducto == "true" ? 1 : 0,
                'prod_ctrllote' => $prodCtrlLote == "true" ? 1 : 0,
                'prod_facturar_ennegativo' => $prodFacturarEnNegativo == "true" ? 1 : 0,
                'prod_facturar_precio_inferiorcosto' => $prodFacturarPrecioInferiorCosto == "true" ? 1 : 0,
            ];

            $this->db->transBegin();
            $this->ccm->actualizar('cc_productos', $datos, ['id' => $idProd]);

            $tipoPrecioId = $this->request->getPost("tipoPrecioId");
            $tipoPrecioVal = $this->request->getPost("tipoPrecioVal");

            $tipoPrecios = explode(",", $tipoPrecioId);
            $preciosVal = explode(",", $tipoPrecioVal);

            //Primero elimino los precios existentes para actualizar con los nuevos
            //Recorro todos los tipos de precio que envio desde el front
            //Si el campo de precio tiene un valor registro el tipo de precio con el producto y su costo
            //Si no trae valor simplemente no lo registro salvo que sea tipo de precio pA, el sistema te notificara que
            //es un campo requerido, si es pB no pasa nada.

            $this->ccm->eliminar('cc_producto_precios', ['fk_producto' => $idProd]);
            foreach ($tipoPrecios as $index => $val) {
                if (!empty($preciosVal[$index])) {
                    $datos = [
                        "fk_tipo_precio" => $val,
                        "fk_producto" => $idProd,
                        "pp_valor" => $preciosVal[$index],
                    ];
                    $this->ccm->guardar($datos, "cc_producto_precios");
                } else {
                    //Voy a consultar si el precio es pA para notificar al usuario que este es un campo requerido
                    $pvp = $this->ccm->getData("cc_tipo_precios", ["id" => $val]);
                    if ($pvp[0]->tpc_nombre == "pA" || $pvp[0]->tpc_nombre == "PA") {
                        $response['status'] = 'warning';
                        $response['msg'] = '<h5>El precio pA es requerido</h5>';
                        return $this->response->setJson($response);
                    }
                }
            }

            $this->ccm->eliminar('cc_producto_impuestotarifa', ['fk_producto' => $idProd]);

            $impuesto = $this->ccm->getValue('cc_impuesto_tarifa', $prodIvaPorcentajeId, "fk_impuesto", "id");
            $datosImpuestoTarifa = [
                "fk_producto" => $idProd,
                "fk_impuestotarifa" => $prodIvaPorcentajeId,
                "fk_impuesto" => $impuesto,
            ];
            $this->ccm->guardar($datosImpuestoTarifa, 'cc_producto_impuestotarifa');

            if ($prodTieneICE) {
                $impuesto = $this->ccm->getValue('cc_impuesto_tarifa', $prodIcePorcentajeId, "fk_impuesto", "id");
                $datosImpuestoTarifaIce = [
                    "fk_producto" => $idProd,
                    "fk_impuestotarifa" => $prodIcePorcentajeId,
                    "fk_impuesto" => $impuesto,
                ];
                $this->ccm->guardar($datosImpuestoTarifaIce, 'cc_producto_impuestotarifa');
            }

            if ($this->db->transStatus == false) {
                $response['status'] = 'error';
                $response['msg'] = '<h5>Ha ocurrido un error al tratar de crear el producto ' . $prodNombre . '</h5>';
                $this->db->transRollback();
            } else {
                $this->logs->logSuccess('SE HA ACTUALIZADO EL PRODUCTO CON EL ID ' . $idProd);
                $response['status'] = 'success';
                $response['msg'] = '<h5>Producto Actualizado Exitosamente <br><hr> ' . $idProd . ' : ' . mb_strtoupper(trim($prodNombre)) . '</h5>';
                $this->db->transCommit();
            }
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'prodNombre' => $this->validation->getError('prodNombre'),
                'prodCodigo' => $this->validation->getError('prodCodigo'),
                'prodUnidadMedida' => $this->validation->getError('prodUnidadMedida'),
                'grupo' => $this->validation->getError('grupo'),
                'prodSubgrupo' => $this->validation->getError('prodSubgrupo'),
                'prodTipoProducto' => $this->validation->getError('prodTipoProducto'),
                'prodIvaPorcentaje' => $this->validation->getError('prodIvaPorcentaje'),
            ];
        }
        return $this->response->setJson($response);
    }

    public function searchProductos() {

        $data = json_decode(file_get_contents("php://input"));

        if ($data->dataSerach) {
            $response = $this->prodModel->searchProductos($data);
            if ($response) {
                return $this->response->setJSON($response);
            }
        }
        return $this->response->setJSON(false);
    }
}
