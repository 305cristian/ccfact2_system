<?php

namespace Modules\Compras\Controllers;

use App\Controllers\BaseController;
use Modules\Compras\Libraries\ComprasSriImportLib;
use Throwable;

class SriImportController extends BaseController {

    protected ComprasSriImportLib $sriImportLib;

    public function __construct() {
        $this->sriImportLib = new ComprasSriImportLib();
    }

    public function responseSetJSON(string $status, string $mensaje, mixed $data = null) {
        return $this->response->setJSON([
                    'status' => $status,
                    'msg' => $mensaje,
                    'data' => $data,
        ]);
    }

    public function importarXmlSri() {
        try {
            $file = $this->request->getFile('file');
            $permitirDuplicados = $this->request->getPost('permitirDuplicados');
            $centroCostoId = $this->request->getPost('centroCostoId');

            if (!$file || !$file->isValid()) {
                return $this->responseSetJSON('error', 'Debe seleccionar un archivo XML valido.');
            }

            $xmlContent = file_get_contents($file->getTempName());
            if (!$xmlContent) {
                return $this->responseSetJSON('error', 'Error al leer el XML de la factura.');
            }

            $dataFactura = $this->sriImportLib->importarDesdeXml($xmlContent, $permitirDuplicados, $centroCostoId);

            return $this->responseSetJSON('success', $dataFactura['msg'], $dataFactura);
        } catch (Throwable $e) {
            return $this->responseSetJSON('error', 'Error al procesar XML: ' . $e->getMessage());
        }
    }

    public function consultarAutorizacionSri() {
        try {
            $dataPost = json_decode(file_get_contents('php://input'));
            $claveAcceso = trim((string) ($dataPost->claveAcceso ?? ''));
            $permitirDuplicados = $dataPost->permitirDuplicados ?? false;
            $centroCostoId = $dataPost->centroCostoId ?? null;

            if ($claveAcceso === '') {
                return $this->responseSetJSON('warning', 'Debe ingresar la clave de acceso.');
            }

            if (!preg_match('/^\d{49}$/', $claveAcceso)) {
                return $this->responseSetJSON('warning', 'La clave de acceso debe tener 49 digitos numericos.');
            }

            $dataFactura = $this->sriImportLib->importarDesdeClaveAcceso($claveAcceso, $permitirDuplicados, $centroCostoId);

            return $this->responseSetJSON('success', $dataFactura['msg'], $dataFactura);
        } catch (Throwable $e) {
            return $this->responseSetJSON('error', 'Error al consultar SRI: ' . $e->getMessage());
        }
    }

    public function reemplazarProductoImportado() {
        try {
            $dataPost = json_decode(file_get_contents('php://input'));
            $rowId = trim((string) ($dataPost->rowid ?? ''));
            $codigoProducto = trim((string) ($dataPost->codigoProducto ?? ''));

            if ($rowId === '' || $codigoProducto === '') {
                return $this->responseSetJSON('warning', 'Debe indicar el item y el producto del sistema.');
            }

            $this->sriImportLib->reemplazarProductoImportado($rowId, $codigoProducto);

            return $this->responseSetJSON('success', 'Producto vinculado correctamente.');
        } catch (Throwable $e) {
            return $this->responseSetJSON('error', 'No se pudo vincular el producto: ' . $e->getMessage());
        }
    }
}
