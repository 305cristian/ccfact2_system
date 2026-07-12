<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Compras\Libraries;

use Modules\Compras\Models\ComprasModel;
use Modules\Comun\Libraries\CuentasConfigLib;
use Modules\Comun\Models\ProductoModel;
use Modules\Comun\Models\SearchsModel;
use RuntimeException;
use SimpleXMLElement;
use stdClass;
use function getImpuestoIrbpnr;
use function getSettings;
use function service;

/**
 * Description of ComprasSriImportLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 10 jul 2026
 * @time 6:37:20 p.m.
 */
class ComprasSriImportLib {

    protected $ccm;
    protected ComprasCartLib $comprasCart;
    protected ProductoModel $prodModel;
    protected SearchsModel $searchModel;
    protected CuentasConfigLib $cuentasConfigLib;
    protected ComprasModel $compModel;

    public function __construct() {
        $this->ccm = service('ccModel');
        $this->comprasCart = new ComprasCartLib();
        $this->prodModel = new ProductoModel();
        $this->searchModel = new SearchsModel();
        $this->cuentasConfigLib = new CuentasConfigLib();
        $this->compModel = new ComprasModel;
    }

    public function importarDesdeXml(string $xmlContent, mixed $permitirDuplicados, mixed $centroCostoId): array {
        $autorizacion = $this->extraerAutorizacionSri($xmlContent);
        $xmlData = $this->processXmlDownload($autorizacion);

        return $this->procesarXmlSriCompra($xmlData, $permitirDuplicados, $centroCostoId);
    }

    public function importarDesdeClaveAcceso(string $claveAcceso, mixed $permitirDuplicados, mixed $centroCostoId): array {
        $xmlDataSri = $this->obtenerXmlSriPorClaveAcceso($claveAcceso);
        $xmlData = $this->processXmlDownload($xmlDataSri);

        return $this->procesarXmlSriCompra($xmlData, $permitirDuplicados, $centroCostoId);
    }

    public function obtenerXmlSriPorClaveAcceso(string $claveAcceso): array {
        $url = (string) (getSettings('SRI_URL_AUTORIZACION') ?: 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl');

        $xmlRequest = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ec="http://ec.gob.sri.ws.autorizacion">'
                . '<soapenv:Header/>'
                . '<soapenv:Body>'
                . '<ec:autorizacionComprobante>'
                . '<claveAccesoComprobante>' . $claveAcceso . '</claveAccesoComprobante>'
                . '</ec:autorizacionComprobante>'
                . '</soapenv:Body>'
                . '</soapenv:Envelope>';

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
//                CURLOPT_FOLLOWLOCATION => true,
//                CURLOPT_MAXREDIRS => 5,
            CURLOPT_POSTFIELDS => $xmlRequest,
            CURLOPT_HTTPHEADER => [
                'Content-Type: text/xml;charset=UTF-8',
                'Content-Length: ' . strlen($xmlRequest),
                'SOAPAction: ""'
            ],
            CURLOPT_SSL_VERIFYPEER => false, // En producción debería ser true
            CURLOPT_TIMEOUT => 30
        ];
        $curl = curl_init();

        curl_setopt_array($curl, $options);

        $respuesta = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            throw new RuntimeException('Error de conexion con SRI: ' . $error);
        }

        if ((int) $httpCode !== 200 || !$respuesta) {
            throw new RuntimeException('El SRI respondio HTTP ' . $httpCode . '.');
        }
        // Procesar la respuesta SOAP
        $xmlString = $this->extraerXmlRespuesta($respuesta);

        if (empty($xmlString)) {
            throw new RuntimeException('No se pudo obtener el comprobante electrónico ' . $httpCode . '.');
        }

        return $xmlString;
    }

    private function extraerXmlRespuesta($soap_response) {

        if (strpos($soap_response, '<soap:Envelope') === false) {
            throw new RuntimeException('El SRI devolvió una respuesta temporal inválida. Intente nuevamente.');
        }

        $xml = new SimpleXMLElement($soap_response);
        $xml->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
        $xml->registerXPathNamespace('ns2', 'http://ec.gob.sri.ws.autorizacion');

        // Extraer el nodo de respuesta
        $resultado = $xml->xpath('//soap:Body/ns2:autorizacionComprobanteResponse/RespuestaAutorizacionComprobante');

        if (empty($resultado)) {
            throw new RuntimeException('No se encontro la factura solicitada');
        }

        // Verificar si tiene autorizaciones
        if (!isset($resultado[0]->autorizaciones->autorizacion)) {
            throw new RuntimeException('El documento no tiene autorizaciones');
        }

        $autorizacion = $resultado[0]->autorizaciones->autorizacion;

        // Verificar el estado
        if ((string) $autorizacion->estado !== 'AUTORIZADO') {
            throw new RuntimeException('Comprobante no autorizado: ' . (string) $autorizacion->estado);
        }

        // Retornar el comprobante XML
        $dataFactura['comprobante'] = $autorizacion->comprobante;
        $dataFactura['autorizacion'] = $resultado[0]->autorizaciones->autorizacion;

        return $dataFactura;
    }

    private function processXmlDownload(array $xmlDataSri): object {
        try {
            $comprobante = new SimpleXMLElement((string) $xmlDataSri['comprobante']);

            $autorizacion = $xmlDataSri['autorizacion'] ?? (object) [
                        'numeroAutorizacion' => $xmlDataSri['numeroAutorizacion'] ?? '',
                        'fechaAutorizacion' => $xmlDataSri['fechaAutorizacion'] ?? null,
                        'ambiente' => null,
            ];

            $datosFactura = $this->extraerDatosFactura($comprobante, $autorizacion);
            $datosFactura->detalles = $this->extraerDetallesFactura($comprobante);

            return $datosFactura;
        } catch (\Throwable $e) {
            throw new RuntimeException('Error al procesar XML: ' . $e->getMessage(), 0, $e);
        }
    }

    private function procesarXmlSriCompra(object $xmlData, mixed $permitirDuplicados, mixed $centroCostoId): array {
        $centroCostoDefault = !empty($centroCostoId) ? (int) $centroCostoId : null;
        $errores = [];
        $importados = 0;
        $rucEmisor = (string) ($xmlData->rucEmisor ?? '');

        if ($rucEmisor === '') {
            throw new RuntimeException('El XML no contiene RUC del emisor.');
        }

        $proveedor = $this->ccm->getData(
                'cc_proveedores',
                ['prov_ruc' => $rucEmisor, 'prov_estado' => 1],
                'id, prov_email, prov_telefono, prov_direccion, prov_celular, prov_nombres, prov_apellidos, prov_razon_social, prov_ruc, prov_dias_credito, CONCAT(prov_ruc," : ",prov_razon_social) proveedor',
                null,
                1
        );

        foreach ($xmlData->detalles ?? [] as $i => $detalle) {
            $codigoPrincipal = trim((string) ($detalle->codigoPrincipal ?? ''));
            $codigoAuxiliar = trim((string) ($detalle->codigoAuxiliar ?? ''));
            $codigoBuscar = $codigoPrincipal ?: $codigoAuxiliar;

            if ($codigoBuscar === '') {
                $errores[] = 'Detalle ' . ($i + 1) . ': no tiene codigo principal ni auxiliar.';
                continue;
            }

            $impuestoIva = $this->extraerImpuestoXmlDetalle($detalle, '2');
            $impuestoIce = $this->extraerImpuestoXmlDetalle($detalle, '3');

            $codigoIva = $impuestoIva ? (string) $impuestoIva->codigoPorcentaje : null;
            $tarifaIva = $impuestoIva ? (float) $impuestoIva->tarifa : 0;

            $tarifaData = $this->ccm->getData('cc_impuesto_tarifa', [
                'fk_impuesto' => 1,
                'impt_codigo' => $codigoIva,
                'impt_porcentage' => $tarifaIva,
                    ], 'id, impt_codigo, impt_detalle, impt_porcentage', null, 1);

            if (!$tarifaData) {
                $errores[] = "Detalle " . ($i + 1) . ": no existe tarifa IVA codigo {$codigoIva} porcentaje {$tarifaIva}.";
                continue;
            }

            $dataProducto = $this->resolverProductoSistema($proveedor ? (int) $proveedor->id : null, $codigoBuscar);
            $productoTemporal = 0;

            if (!$dataProducto) {
                $dataProducto = $this->crearProductoTemporalCarrito($detalle, (int) $tarifaData->id, (float) $tarifaData->impt_porcentage);
                $productoTemporal = 1;
                $errores[] = "Detalle " . ($i + 1) . ": el producto '{$codigoBuscar}' no esta vinculado. Debe reemplazarlo con un producto del sistema antes de guardar.";
            }

            $precio = (float) ($detalle->precioUnitario ?? 0);
            $cantidad = (float) ($detalle->cantidad ?? 0);
            $descuentoLinea = (float) ($detalle->descuento ?? 0);
            $descuentoUnitario = $cantidad > 0 ? $descuentoLinea / $cantidad : 0;

            $item = [
                'id' => (int) $dataProducto->id,
                'qty' => $cantidad,
                'codigo' => $dataProducto->prod_codigo,
                'name' => $dataProducto->prod_nombre,
                'unidadMedida' => $dataProducto->um_nombre_corto,
                'price' => $precio,
                'ivaPorcent' => (float) $tarifaData->impt_porcentage,
                'icePorcent' => $impuestoIce ? (float) $impuestoIce->tarifa : 0,
                'impuestoSelect' => (int) $tarifaData->id,
                'codigoImpuestoSelect' => $tarifaData->impt_codigo,
                'detalleImpuestoSelect' => $tarifaData->impt_detalle,
                'tipoDescuento' => 'VALOR',
                'discountPercent' => $precio > 0 ? ($descuentoUnitario / $precio) * 100 : 0,
                'discountValue' => $descuentoUnitario,
                'descuento' => $descuentoUnitario,
                'tieneLote' => $dataProducto->prod_ctrllote,
                'permitirDuplicados' => $permitirDuplicados,
                'lote' => null,
                'fechaElaboracion' => null,
                'fechaCaducidad' => null,
                'servicio' => $dataProducto->prod_isservicio,
                'irbpnrUnitario' => $dataProducto->prod_tiene_irbpnr === '1' ? (float) getImpuestoIrbpnr() : 0,
                'centroCosto' => $centroCostoDefault,
                'ctaContableProducto' => $dataProducto->fk_cuentacontablecompras ?: null,
                'codigoImport' => $codigoBuscar,
                'isNewProduct' => $productoTemporal,
                'productoTemporal' => $productoTemporal,
                'codigoProductoReemplazo' => null,
            ];

            if (empty($item['ctaContableProducto'])) {
                $codigoCuenta = $this->obtenerCodigoCuentaCompraPorProducto($dataProducto, (int) $tarifaData->id, (float) $tarifaData->impt_porcentage);
                if ($codigoCuenta) {
                    $item['ctaContableProducto'] = $this->cuentasConfigLib->obtenerSettingCuentaContable($codigoCuenta);
                }
            }

            $this->comprasCart->insert($item);
            $importados++;
        }

        if ($importados === 0) {
            throw new RuntimeException('No se importaron items validos.<br>' . implode('<br>', $errores));
        }

        $fechaEmision_ = str_replace('/', '-', (string) ($xmlData->fechaEmision ?? ''));
        $fechaEmision = date('Y-m-d', strtotime($fechaEmision_));

        $msg = "Factura SRI importada: {$importados} item(s) agregado(s).";
        if ($errores) {
            $msg .= "<span class='fw-semibold text-danger'><br><br><strong>Observaciones:</strong><br>" . implode('<br>', $errores) . '</span>';
        }

        return [
            'msg' => $msg,
            'totalImportados' => $importados,
            'errores' => $errores,
            'proveedor' => $proveedor ?: null,
            'factura' => [
                'rucEmisor' => $rucEmisor,
                'razonSocial' => (string) ($xmlData->razonSocialEmisor ?? ''),
                'codDoc' => (string) ($xmlData->codDoc ?? ''),
                'estab' => (string) ($xmlData->estab ?? ''),
                'ptoEmi' => (string) ($xmlData->puntoEmision ?? ''),
                'secuencial' => (string) ($xmlData->secuencial ?? ''),
                'claveAcceso' => (string) ($xmlData->claveAcceso ?? ''),
                'numeroAutorizacion' => (string) ($xmlData->numeroAutorizacion ?? ''),
                'fechaAutorizacion' => $xmlData->fechaAutorizacion ?? null,
                'fechaEmision' => $fechaEmision,
                'importeTotal' => (float) ($xmlData->importeTotal ?? 0),
            ],
        ];
    }

    public function extraerDatosFactura($comprobante, $xmlAutorizacion): object {
        $infoTributaria = $comprobante->infoTributaria;
        $infoFactura = $comprobante->infoFactura;

        $datos = new stdClass();

        // Datos de autorización
        $datos->numeroAutorizacion = (string) $xmlAutorizacion->numeroAutorizacion;
        $datos->fechaAutorizacion = (string) $xmlAutorizacion->fechaAutorizacion;
        $datos->ambiente = (string) $xmlAutorizacion->ambiente;

        // Datos del emisor
        $datos->razonSocialEmisor = (string) $infoTributaria->razonSocial;
        $datos->nombreComercialEmisor = (string) $infoTributaria->nombreComercial;
        $datos->rucEmisor = (string) $infoTributaria->ruc;
        $datos->dirMatrizEmisor = (string) $infoTributaria->dirMatriz;
        $datos->claveAcceso = (string) $infoTributaria->claveAcceso;

        // Datos del documento
        $datos->codDoc = (string) $infoTributaria->codDoc;
        $datos->estab = (string) $infoTributaria->estab;
        $datos->puntoEmision = (string) $infoTributaria->ptoEmi;
        $datos->secuencial = (string) $infoTributaria->secuencial;
        $datos->numeroFactura = sprintf(
                '%s-%s-%s',
                $infoTributaria->estab,
                $infoTributaria->ptoEmi,
                $infoTributaria->secuencial
        );

        // Datos del comprador
        $datos->tipoIdentificacionComprador = (string) $infoFactura->tipoIdentificacionComprador;
        $datos->razonSocialComprador = (string) $infoFactura->razonSocialComprador;
        $datos->identificacionComprador = (string) $infoFactura->identificacionComprador;
        $datos->direccionComprador = (string) $infoFactura->direccionComprador;

        // Fechas
        $datos->fechaEmision = (string) $infoFactura->fechaEmision;

        // Totales
        $datos->totalSinImpuestos = (float) $infoFactura->totalSinImpuestos;
        $datos->totalDescuento = (float) $infoFactura->totalDescuento;
        $datos->importeTotal = (float) $infoFactura->importeTotal;
        $datos->moneda = (string) $infoFactura->moneda;

        // Pagos
        $datos->pagos = [];

        if (isset($infoFactura->pagos->pago)) {
            foreach ($infoFactura->pagos->pago as $pago) {
                $datos->pagos[] = (object) [
                            'formaPago' => (string) $pago->formaPago,
                            'total' => (float) $pago->total,
                            'plazo' => (string) $pago->plazo,
                            'unidadTiempo' => (string) $pago->unidadTiempo,
                ];
            }
        }

        // Impuestos
        $datos->impuestos = [];

        if (isset($infoFactura->totalConImpuestos->totalImpuesto)) {
            foreach ($infoFactura->totalConImpuestos->totalImpuesto as $impuesto) {
                $datos->impuestos[] = (object) [
                            'codigo' => (string) $impuesto->codigo,
                            'codigoPorcentaje' => (string) $impuesto->codigoPorcentaje,
                            'baseImponible' => (float) $impuesto->baseImponible,
                            'tarifa' => (float) $impuesto->tarifa,
                            'valor' => (float) $impuesto->valor,
                ];
            }
        }

        // Opcionales
        $datos->contribuyenteEspecial = isset($infoFactura->contribuyenteEspecial) ? (string) $infoFactura->contribuyenteEspecial : null;

        $datos->obligadoContabilidad = isset($infoFactura->obligadoContabilidad) ? (string) $infoFactura->obligadoContabilidad : null;

        $datos->propina = isset($infoFactura->propina) ? (float) $infoFactura->propina : 0.0;

        $datos->agenteRetencion = isset($infoTributaria->agenteRetencion) ? (string) $infoTributaria->agenteRetencion : null;

        return $datos;
    }

    public function extraerDetallesFactura($comprobante): array {
        $detalles = [];

        if (isset($comprobante->detalles->detalle)) {

            foreach ($comprobante->detalles->detalle as $detalle) {

                $item = new stdClass();

                $item->codigoPrincipal = (string) $detalle->codigoPrincipal;
                $item->descripcion = (string) $detalle->descripcion;
                $item->cantidad = (float) $detalle->cantidad;
                $item->precioUnitario = (float) $detalle->precioUnitario;
                $item->descuento = (float) $detalle->descuento;
                $item->precioTotalSinImpuesto = (float) $detalle->precioTotalSinImpuesto;

                // Código auxiliar (opcional)
                $item->codigoAuxiliar = isset($detalle->codigoAuxiliar) ? (string) $detalle->codigoAuxiliar : null;

                // Impuestos del ítem
                $item->impuestos = [];

                if (isset($detalle->impuestos->impuesto)) {

                    foreach ($detalle->impuestos->impuesto as $impuesto) {

                        $item->impuestos[] = (object) [
                                    'codigo' => (string) $impuesto->codigo,
                                    'codigoPorcentaje' => (string) $impuesto->codigoPorcentaje,
                                    'tarifa' => (float) $impuesto->tarifa,
                                    'baseImponible' => (float) $impuesto->baseImponible,
                                    'valor' => (float) $impuesto->valor,
                        ];
                    }
                }

                $detalles[] = $item;
            }
        }

        return $detalles;
    }

    public function extraerAutorizacionSri(string $xmlContent): array {
        $xml = new SimpleXMLElement($xmlContent);
        $autorizaciones = $xml->xpath('//*[local-name()="autorizacion"]');

        if ($autorizaciones) {
            $autorizacion = $autorizaciones[0];
        } else if (isset($xml->autorizaciones->autorizacion)) {
            $autorizacion = $xml->autorizaciones->autorizacion;
        } else if (isset($xml->estado) && isset($xml->comprobante)) {
            $autorizacion = $xml;
        } else if (isset($xml->infoTributaria)) {
            return [
                'comprobante' => $xmlContent,
                'numeroAutorizacion' => (string) $xml->infoTributaria->claveAcceso,
                'fechaAutorizacion' => null,
            ];
        } else {
            throw new RuntimeException('No se pudo reconocer el formato del XML.');
        }

        if ((string) $autorizacion->estado !== 'AUTORIZADO') {
            throw new RuntimeException('El documento no esta autorizado por el SRI. Estado: ' . (string) $autorizacion->estado);
        }

        return [
            'comprobante' => (string) $autorizacion->comprobante,
            'numeroAutorizacion' => (string) ($autorizacion->numeroAutorizacion ?? ''),
            'fechaAutorizacion' => (string) ($autorizacion->fechaAutorizacion ?? ''),
        ];
    }

    public function extraerImpuestoXmlDetalle(object $detalle, string $codigo) {
        foreach ($detalle->impuestos ?? [] as $impuesto) {
            if ((string) $impuesto->codigo === $codigo) {
                return $impuesto;
            }
        }

        return null;
    }

    public function resolverProductoSistema(?int $proveedorId, string $codigoPrincipal): mixed {
        if ($proveedorId) {
            $relacion = $this->compModel->getRelacionProducto($proveedorId, $codigoPrincipal);

            if ($relacion) {
                $producto = $this->searchModel->searchProductoData((string) $relacion->fk_producto);
                if ($producto) {
                    return $producto;
                }
            }
        }

        return $this->searchModel->searchProductoData($codigoPrincipal);
    }

    private function crearProductoTemporalCarrito(object $detalle, int $impuestoTarifaId, float $ivaPorcent): object {
        $codigoTemporal = $this->generarCodigoUnico();
        $descripcion = trim((string) ($detalle->descripcion ?? 'PRODUCTO IMPORTADO SRI'));

        $producto = new stdClass();
        $producto->id = (int) $codigoTemporal;
        $producto->prod_codigo = (string) $codigoTemporal;
        $producto->prod_nombre = $descripcion !== '' ? $descripcion : 'PRODUCTO IMPORTADO SRI';
        $producto->um_nombre_corto = '';
        $producto->prod_ctrllote = 0;
        $producto->prod_isservicio = 0;
        $producto->fk_tipoproducto = 1;
        $producto->prod_tiene_irbpnr = 0;
        $producto->fk_cuentacontablecompras = null;

        $codigoCuenta = $this->obtenerCodigoCuentaCompraPorProducto($producto, $impuestoTarifaId, $ivaPorcent);
        if ($codigoCuenta) {
            $producto->fk_cuentacontablecompras = $this->cuentasConfigLib->obtenerSettingCuentaContable($codigoCuenta);
        }

        return $producto;
    }

    public function reemplazarProductoImportado(string $rowId, string $codigoProducto): void {
        $items = $this->comprasCart->getContent() ?? [];

        if (!isset($items[$rowId])) {
            throw new RuntimeException('El item importado ya no existe en el carrito.');
        }

        $itemActual = $items[$rowId];
        $producto = $this->searchModel->searchProductoData($codigoProducto);

        if (!$producto) {
            throw new RuntimeException('Producto no encontrado o inactivo.');
        }

        $itemActual['id'] = (int) $producto->id;
        $itemActual['codigo'] = $producto->prod_codigo;
        $itemActual['name'] = $producto->prod_nombre;
        $itemActual['unidadMedida'] = $producto->um_nombre_corto;
        $itemActual['tieneLote'] = $producto->prod_ctrllote;
        $itemActual['servicio'] = $producto->prod_isservicio;
        $itemActual['irbpnrUnitario'] = $producto->prod_tiene_irbpnr === '1' ? (float) getImpuestoIrbpnr() : 0;
        $itemActual['productoTemporal'] = 0;
        $itemActual['isNewProduct'] = 1;
        $itemActual['codigoProductoReemplazo'] = null;

        if (!empty($producto->fk_cuentacontablecompras)) {
            $itemActual['ctaContableProducto'] = $producto->fk_cuentacontablecompras;
        } else {
            $codigoCuenta = $this->obtenerCodigoCuentaCompraPorProducto($producto, (int) ($itemActual['impuestoSelect'] ?? 0), (float) ($itemActual['ivaPorcent'] ?? 0));
            if ($codigoCuenta) {
                $itemActual['ctaContableProducto'] = $this->cuentasConfigLib->obtenerSettingCuentaContable($codigoCuenta);
            }
        }

        $this->comprasCart->update($itemActual, $rowId);
    }

    private function generarCodigoUnico(): string {
        return date('y') . mt_rand(100000, 999999);
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
}
