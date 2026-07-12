<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Compras\Controllers;

use App\Controllers\BaseController;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Modules\Compras\Models\ComprasModel;

/**
 * Description of GestionController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 3 may 2026
 * @time 4:43:44 p.m.
 */
class GestionController extends BaseController {

    protected string $dirViewModule;
    protected ComprasModel $comprasModel;

    public function __construct() {

        $this->dirViewModule = 'Modules\Compras\Views';
        $this->comprasModel = new ComprasModel();
    }

    public function index() {
        $this->user->validateSession();
        $data['title'] = "Listar Compras";
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);

        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaCentroCostos'] = $this->ccm->getData('cc_centroscosto', ['cc_estado' => 1], 'id, cc_nombre');
        $data['listaTiposComprobantes'] = $this->ccm->getData('cc_tipos_comprobante', ['comp_estado' => 1], 'id, comp_codigo, comp_nombre');
        $data['listaTiposCompra'] = $this->ccm->getData('cc_tipo_compra', ['tc_estado' => 1], 'id, tc_nombre');
        $data['listaSustentos'] = $this->ccm->getData('cc_sustentos', ['sus_estado' => 1], 'sus_codigo, sus_nombre');

        $bodegaMainUsuario = bodegaMain($this->user->id);

        $data['bodegaId'] = $this->session->get('bodegaIdComp') ?? $bodegaMainUsuario;

        $send['view'] = view($this->dirViewModule . '\viewGestionCompras', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
    }

    public function searchCompras() {

        $this->user->validateSession();
        $dataPost = json_decode(file_get_contents('php://input')) ?? (object) [];

        $filtros = [
            'compFechasEmision' => $dataPost->compFechasEmision ?? null,
            'compFechasArchivado' => $dataPost->compFechasArchivado ?? null,
            'compSecuencial' => $dataPost->compSecuencial ?? null,
            'compComprobante' => $dataPost->compComprobante ?? null,
            'compProveedor' => $dataPost->compProveedor ?? null,
            'compBodega' => $dataPost->compBodega ?? null,
            'compCentroCosto' => $dataPost->compCentroCosto ?? null,
            'compTipoComprobante' => $dataPost->compTipoComprobante ?? null,
            'compTipoCosto' => $dataPost->compTipoCosto ?? null,
            'compEstado' => $dataPost->compEstado ?? null,
        ];

        $compras = $this->comprasModel->searchCompras($filtros);

        return $this->response->setJSON([
                    'status' => $compras ? 'success' : 'warning',
                    'data' => $compras,
        ]);
    }

    public function updateEdicionRapida() {

        $this->user->validateSession();
        $dataPost = json_decode(file_get_contents('php://input')) ?? (object) [];

        $compraId = (int) ($dataPost->compraId ?? 0);

        if ($compraId <= 0) {
            return $this->responseSetJSON('warning', 'No se recibió la compra que se desea actualizar.');
        }

        $compra = $this->ccm->getData('cc_compras', ['id' => $compraId], '*', null, 1);

        if (!$compra) {
            return $this->responseSetJSON('warning', 'La compra no se encuentra registrada.');
        }

        if ($compra->comp_estado !== 'ARCHIVADO') {
            return $this->responseSetJSON('warning', 'La edición rápida solo está disponible para compras archivadas.');
        }

        $validacion = $this->validarEdicionRapida($dataPost);

        if ($validacion !== '') {
            return $this->responseSetJSON('warning', $validacion);
        }

        $establecimiento = str_pad(trim((string) $dataPost->compNumeroEstablecimiento), 3, '0', STR_PAD_LEFT);
        $emision = str_pad(trim((string) $dataPost->compNumeroEmision), 3, '0', STR_PAD_LEFT);
        $numeroComprobante = str_pad(trim((string) $dataPost->compNumeroComprobante), 9, '0', STR_PAD_LEFT);
        $numeroDocumento = "{$establecimiento}-{$emision}-{$numeroComprobante}";

        $datosCompra = [
            'comp_numero_establecimiento' => $establecimiento,
            'comp_numero_emision' => $emision,
            'comp_numero_comprobante' => $numeroComprobante,
            'comp_autorizacion_sri' => trim((string) $dataPost->compAutSRI),
            'comp_fecha_emision' => $dataPost->compFechaEmision,
            'comp_fecha_vencimiento_autorizacion' => $dataPost->compFechaCaducidad,
            'cod_sustento' => trim((string) $dataPost->compSustento),
            'fk_tipo_compra' => (int) $dataPost->compTipoCompra,
            'tipo_costo' => trim((string) $dataPost->compTipoCosto),
            'fk_orden_compra' => !empty($dataPost->compODC) ? (int) $dataPost->compODC : null,
            'comp_observacion' => trim((string) ($dataPost->compObservaciones ?? '')),
        ];

        $this->db->transBegin();

        try {
            $this->ccm->actualizar('cc_compras', $datosCompra, ['id' => $compraId, 'comp_estado' => 'ARCHIVADO']);

            $this->ccm->actualizar('cc_cxp', ['cxp_numero_documento' => $numeroComprobante], ['fk_compra' => $compraId]);

            $this->ccm->actualizar('cc_asiento_contable', ['ac_detalle' => 'COMPRA NRO. ' . $numeroDocumento], ['ac_codigo_transaccion' => '02', 'ac_documento_id' => $compraId, 'ac_estado' => 1]);

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('No se pudo completar la actualización.');
            }

            $this->db->transCommit();

            return $this->responseSetJSON('success', 'Compra actualizada correctamente.');
        } catch (\Throwable $e) {
            $this->db->transRollback();

            return $this->responseSetJSON('error', 'Error al actualizar la compra: ' . $e->getMessage());
        }
    }

    public function getCentrosCostosCompra(int $compraId) {

        $this->user->validateSession();

        $compra = $this->ccm->getData('cc_compras', ['id' => $compraId], 'id, comp_estado, fk_centro_costo, comp_secuencial', null, 1);

        if (!$compra) {
            return $this->responseSetJSON('warning', 'La compra no se encuentra registrada.');
        }

        if ($compra->comp_estado !== 'ARCHIVADO') {
            return $this->responseSetJSON('warning', 'Solo se pueden editar centros de costo de compras archivadas.');
        }

        return $this->responseSetJSON('success', 'ok', [
                    'compra' => $compra,
                    'detalle' => $this->comprasModel->getDetalleCentrosCostos($compraId),
        ]);
    }

    public function updateCentrosCostosCompra() {

        $this->user->validateSession();
        $dataPost = json_decode(file_get_contents('php://input')) ?? (object) [];

        $compraId = (int) ($dataPost->compraId ?? 0);
        $centroCostoId = (int) ($dataPost->centroCostoId ?? 0);
        $detalles = $dataPost->detalles ?? [];

        if ($compraId <= 0) {
            return $this->responseSetJSON('warning', 'No se recibió la compra que se desea actualizar.');
        }

        if ($centroCostoId <= 0) {
            return $this->responseSetJSON('warning', 'Debe seleccionar el centro de costo global.');
        }

        if (empty($detalles) || !is_array($detalles)) {
            return $this->responseSetJSON('warning', 'Debe existir al menos un detalle para actualizar.');
        }

        $compra = $this->ccm->getData('cc_compras', ['id' => $compraId], 'id, comp_estado', null, 1);

        if (!$compra) {
            return $this->responseSetJSON('warning', 'La compra no se encuentra registrada.');
        }

        if ($compra->comp_estado !== 'ARCHIVADO') {
            return $this->responseSetJSON('warning', 'Solo se pueden editar centros de costo de compras archivadas.');
        }

        $detallesActuales = $this->ccm->getData('cc_compras_det', ['fk_compra' => $compraId, 'compd_estado' => 1], 'id');
        $detalleIds = array_map(static fn($detalle) => (int) $detalle->id, $detallesActuales ?? []);

        foreach ($detalles as $detalle) {
            $detalleId = (int) ($detalle->id ?? 0);
            $detalleCentroCostoId = (int) ($detalle->centroCostoId ?? 0);

            if (!in_array($detalleId, $detalleIds, true)) {
                return $this->responseSetJSON('warning', 'Existe un detalle que no pertenece a la compra.');
            }

            if ($detalleCentroCostoId <= 0) {
                return $this->responseSetJSON('warning', 'Todos los detalles deben tener centro de costo.');
            }
        }

        $this->db->transBegin();

        try {
            $this->ccm->actualizar('cc_compras', ['fk_centro_costo' => $centroCostoId], ['id' => $compraId, 'comp_estado' => 'ARCHIVADO']);

            foreach ($detalles as $detalle) {
                $this->ccm->actualizar('cc_compras_det', ['compd_centro_costo' => (int) $detalle->centroCostoId], ['id' => (int) $detalle->id, 'fk_compra' => $compraId, 'compd_estado' => 1]);
            }

            $asientoId = $this->ccm->getValueWhere('cc_asiento_contable', ['ac_codigo_transaccion' => '02', 'ac_documento_id' => $compraId, 'ac_estado' => 1], 'id');

            if ($asientoId) {
                $this->ccm->actualizar('cc_asiento_contable_det', ['fk_centro_costos' => $centroCostoId], ['fk_asiento_contable' => $asientoId]);
            }

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('No se pudo completar la actualización.');
            }

            $this->db->transCommit();

            return $this->responseSetJSON('success', 'Centros de costo actualizados correctamente.');
        } catch (\Throwable $e) {
            $this->db->transRollback();

            return $this->responseSetJSON('error', 'Error al actualizar centros de costo: ' . $e->getMessage());
        }
    }

    public function getLotesCompra(int $compraId) {

        $this->user->validateSession();

        $compra = $this->ccm->getData('cc_compras', ['id' => $compraId], 'id, comp_estado, comp_secuencial', null, 1);

        if (!$compra) {
            return $this->responseSetJSON('warning', 'La compra no se encuentra registrada.');
        }

        if ($compra->comp_estado !== 'ARCHIVADO') {
            return $this->responseSetJSON('warning', 'Solo se pueden editar lotes de compras archivadas.');
        }

        return $this->responseSetJSON('success', 'ok', [
                    'compra' => $compra,
                    'detalle' => $this->comprasModel->getDetalleLotesCompra($compraId),
        ]);
    }

    public function updateLotesCompra() {

        $this->user->validateSession();
        $dataPost = json_decode(file_get_contents('php://input')) ?? (object) [];

        $compraId = (int) ($dataPost->compraId ?? 0);
        $detalles = $dataPost->detalles ?? [];

        if ($compraId <= 0) {
            return $this->responseSetJSON('warning', 'No se recibió la compra que se desea actualizar.');
        }

        if (empty($detalles) || !is_array($detalles)) {
            return $this->responseSetJSON('warning', 'Debe existir al menos un lote para actualizar.');
        }

        $compra = $this->ccm->getData('cc_compras', ['id' => $compraId], 'id, comp_estado', null, 1);

        if (!$compra) {
            return $this->responseSetJSON('warning', 'La compra no se encuentra registrada.');
        }

        if ($compra->comp_estado !== 'ARCHIVADO') {
            return $this->responseSetJSON('warning', 'Solo se pueden editar lotes de compras archivadas.');
        }

        $detallesActuales = $this->comprasModel->getDetalleLotesCompra($compraId);
        $detallesPorId = [];

        foreach ($detallesActuales as $detalleActual) {
            $detallesPorId[(int) $detalleActual->id] = $detalleActual;
        }

        $datosPorLote = [];

        foreach ($detalles as $detalle) {
            $detalleId = (int) ($detalle->id ?? 0);
            $lote = trim((string) ($detalle->lote ?? ''));
            $fechaElaboracion = trim((string) ($detalle->fechaElaboracion ?? ''));
            $fechaCaducidad = trim((string) ($detalle->fechaCaducidad ?? ''));

            if (!isset($detallesPorId[$detalleId])) {
                return $this->responseSetJSON('warning', 'Existe un detalle que no pertenece a la compra o no controla lote.');
            }

            if (empty($detallesPorId[$detalleId]->fk_lote)) {
                return $this->responseSetJSON('warning', 'Existe un detalle sin lote vinculado. No se puede corregir desde esta opción.');
            }

            if ($lote === '' || $fechaElaboracion === '' || $fechaCaducidad === '') {
                return $this->responseSetJSON('warning', 'Debe completar lote, fecha de elaboración y fecha de caducidad en todos los ítems.');
            }

            if ($fechaElaboracion > $fechaCaducidad) {
                return $this->responseSetJSON('warning', "La fecha de elaboración no puede ser mayor a la caducidad en el lote {$lote}.");
            }

            $loteIdActual = (int) $detallesPorId[$detalleId]->fk_lote;
            $firmaLote = "{$lote}|{$fechaElaboracion}|{$fechaCaducidad}";

            if (isset($datosPorLote[$loteIdActual]) && $datosPorLote[$loteIdActual] !== $firmaLote) {
                return $this->responseSetJSON('warning', "El mismo lote {$detallesPorId[$detalleId]->lot_lote} aparece con datos diferentes.");
            }

            $datosPorLote[$loteIdActual] = $firmaLote;

            $loteExistente = $this->ccm->getData('cc_lotes', ['lot_lote' => $lote, 'fk_producto' => (int) $detallesPorId[$detalleId]->fk_producto], 'id', null, 1);

            if ($loteExistente && (int) $loteExistente->id !== (int) $detallesPorId[$detalleId]->fk_lote) {
                return $this->responseSetJSON('warning', "El lote {$lote} ya existe para este producto. Esta opción no reasigna movimientos de kardex a otro lote.");
            }
        }

        $this->db->transBegin();

        try {
            foreach ($detalles as $detalle) {
                $detalleActual = $detallesPorId[(int) $detalle->id];
                $lote = trim((string) $detalle->lote);
                $fechaElaboracion = trim((string) $detalle->fechaElaboracion);
                $fechaCaducidad = trim((string) $detalle->fechaCaducidad);

                $dataSet = [
                    'lot_lote' => $lote,
                    'lot_fecha_elaboracion' => $fechaElaboracion,
                    'lot_fecha_caducidad' => $fechaCaducidad,
                ];
                $this->ccm->actualizar('cc_lotes', $dataSet, ['id' => (int) $detalleActual->fk_lote]);

                $dataSet2 = [
                    'compd_lote' => $lote,
                    'compd_fecha_elaboracion' => $fechaElaboracion,
                    'compd_fecha_caducidad' => $fechaCaducidad,
                ];
                $this->ccm->actualizar('cc_compras_det', $dataSet2, ['id' => (int) $detalle->id, 'fk_compra' => $compraId, 'compd_estado' => 1]);
            }

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('No se pudo completar la actualización.');
            }

            $this->db->transCommit();

            return $this->responseSetJSON('success', 'Lotes actualizados correctamente.');
        } catch (\Throwable $e) {
            $this->db->transRollback();

            return $this->responseSetJSON('error', 'Error al actualizar lotes: ' . $e->getMessage());
        }
    }

    public function contadoresCompras() {

        $this->user->validateSession();

        $dataPost = json_decode(file_get_contents('php://input')) ?? (object) [];

        $contadores = [
            'BORRADOR' => 0,
            'ARCHIVADO' => 0,
            'ANULADA' => 0,
            'ANULADA_EN_PENDIENTE' => 0,
            'ANULADA_EN_ARCHIVADA' => 0,
        ];

        $response = $this->comprasModel->contadoresCompras($dataPost->compFechasEmision ?? null, $dataPost->compFechasArchivado ?? null);

        foreach ($response as $row) {
            if (array_key_exists($row->comp_estado, $contadores)) {
                $contadores[$row->comp_estado] = (int) $row->total;
            }
        }

        return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $contadores,
        ]);
    }

    public function getDataDetalle(int $compraId) {

        $this->user->validateSession();

        $compra = $this->comprasModel->getDataDetalle($compraId);

        if (!$compra) {
            return $this->response
                            ->setStatusCode(404)
                            ->setJSON([
                                'status' => 'warning',
                                'message' => 'No se encontró la compra solicitada.',
            ]);
        }
        $send = [
            'compra' => $compra,
            'empresa' => enterprice(),
        ];
        $view = view('\Modules\Compras\Views\reportes\viewDetalleReport', $send);

        return $this->response->setJSON($view);
    }

    public function getAsientoContable(int $compraId) {

        $this->user->validateSession();

        $compra = $this->comprasModel->getDataDetalle($compraId);

        if (!$compra) {
            return $this->response->setJSON(['status' => 'warning', 'msg' => 'No se encontró la compra solicitada.']);
        }

        if (empty($compra->asientoContable)) {
            return $this->response->setJSON(['status' => 'warning', 'msg' => 'La compra no tiene asiento contable registrado.',]);
        }

        $view = view('\Modules\Compras\Views\reportes\viewAsientoContable', ['asiento' => $compra->asientoContable,]);

        return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $view,
        ]);
    }

    public function generarPDF(int $compraId) {

        $this->user->validateSession();

        $compra = $this->comprasModel->getDataDetalle($compraId);

        if (!$compra) {
            return $this->response
                            ->setStatusCode(404)
                            ->setJSON([
                                'status' => 'warning',
                                'message' => 'No se encontró la compra solicitada.',
            ]);
        }
        $send = [
            'compra' => $compra,
            'empresa' => enterprice(),
        ];
        $view = view('\Modules\Compras\Views\reportes\viewDetalleReport', $send);

        $cssPath = FCPATH . 'resources/css/stylesMpdf.css';
        $css = is_file($cssPath) ? file_get_contents($cssPath) : '';

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'margin_top' => 10,
            'margin_bottom' => 12,
            'margin_left' => 8,
            'margin_right' => 8,
            'default_font_size' => 8,
            'default_font' => 'dejavusans',
        ]);

        $mpdf->SetDisplayMode('fullpage');
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        if ($css !== '') {
            $mpdf->WriteHTML($css, HTMLParserMode::HEADER_CSS);
        }

        $mpdf->WriteHTML($view, HTMLParserMode::HTML_BODY);
        $mpdf->SetHTMLFooter('<div style="text-align:center; font-size:8pt;">'
                . 'Página {PAGENO} de {nbpg}'
                . '</div>'
        );

        $fileName = 'Compra_' . str_pad($compra->comp_secuencial, 5, '0', STR_PAD_LEFT) . '.pdf';

        if ($this->request->getGet('download')) {
            return $this->response
                            ->setHeader('Content-Type', 'application/pdf')
                            ->setHeader('Content-Disposition', 'inline; filename="' . $fileName . '"')
                            ->setBody($mpdf->Output($fileName, 'D'));
        } else {
            $directory = WRITEPATH . 'uploads/pdfs/compras/';
            $pdfPath = $directory . $fileName;

            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            file_put_contents($pdfPath, $mpdf->Output($fileName, 'S'));

            return [
                'success' => true,
                'path' => $pdfPath,
                'fileName' => $fileName
            ];
        }
    }

    public function sendEmailReport() {

        $this->user->validateSession();
        $dataPost = json_decode(file_get_contents("php://input")) ?? (object) [];

        $para = trim((string) ($dataPost->para ?? ''));
        $cc = trim((string) ($dataPost->cc ?? ''));
        $asunto = trim((string) ($dataPost->asunto ?? ''));
        $mensaje = (string) ($dataPost->mensaje ?? '');
        $compraId = (int) ($dataPost->idCompra ?? 0);

        if ($compraId <= 0) {
            return $this->responseSetJSON('warning', 'No se recibio la compra para enviar por email.');
        }

        if ($para === '' || $asunto === '') {
            return $this->responseSetJSON('warning', 'Debe completar los campos obligatorios (Para y Asunto).');
        }

        $paraArray = array_filter(array_map('trim', explode(',', $para)));
        $ccArray = array_filter(array_map('trim', explode(',', $cc)));

        if (empty($paraArray)) {
            return $this->responseSetJSON('warning', 'Debe ingresar al menos un correo de destino.');
        }

        foreach (array_merge($paraArray, $ccArray) as $correo) {
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                return $this->responseSetJSON('warning', "El correo {$correo} no es valido.");
            }
        }

        $email = \Config\Services::email();
        $email->clear(true);
        $email->setFrom('no-reply@ccomputers.com', 'CCFACT - Sistema ERP');
        $email->setTo($paraArray);

        if (!empty($ccArray)) {
            $email->setCC($ccArray);
        }

        $email->setSubject($asunto);
        $email->setMailType('text');
        $email->setMessage($mensaje);

        $pdfData = $this->generarPDF($compraId);

        if (!is_array($pdfData) || empty($pdfData['path']) || !file_exists($pdfData['path'])) {
            return $this->responseSetJSON('warning', 'No se pudo generar el PDF de la compra.');
        }

        $email->attach($pdfData['path']);

        if ($email->send()) {
            if (file_exists($pdfData['path'])) {
                unlink($pdfData['path']);
            }

            return $this->responseSetJSON('success', 'Correo enviado exitosamente.');
        }

        if (file_exists($pdfData['path'])) {
            unlink($pdfData['path']);
        }

        return $this->responseSetJSON('warning', 'Error al enviar el email, Verifique configuracion SMTP: ' . $email->printDebugger());
    }

    private function validarEdicionRapida(object $dataPost): string {

        $campos = [
            'compNumeroEstablecimiento' => 'Debe ingresar el punto de establecimiento.',
            'compNumeroEmision' => 'Debe ingresar el punto de emisión.',
            'compNumeroComprobante' => 'Debe ingresar el número de factura.',
            'compAutSRI' => 'Debe ingresar la autorización SRI.',
            'compFechaEmision' => 'Debe ingresar la fecha de emisión.',
            'compFechaCaducidad' => 'Debe ingresar la fecha de vencimiento de autorización.',
            'compSustento' => 'Debe seleccionar el sustento tributario.',
            'compTipoCompra' => 'Debe seleccionar el tipo de compra.',
            'compTipoCosto' => 'Debe seleccionar el tipo de costo.',
        ];

        foreach ($campos as $campo => $mensaje) {
            if (!isset($dataPost->{$campo}) || trim((string) $dataPost->{$campo}) === '') {
                return $mensaje;
            }
        }

        if (!preg_match('/^\d{1,3}$/', (string) $dataPost->compNumeroEstablecimiento)) {
            return 'El punto de establecimiento debe tener máximo 3 dígitos.';
        }

        if (!preg_match('/^\d{1,3}$/', (string) $dataPost->compNumeroEmision)) {
            return 'El punto de emisión debe tener máximo 3 dígitos.';
        }

        if (!preg_match('/^\d{1,9}$/', (string) $dataPost->compNumeroComprobante)) {
            return 'El número de factura debe tener máximo 9 dígitos.';
        }

        if (!in_array((string) $dataPost->compTipoCosto, ['DIRECTOS', 'INDIRECTOS'], true)) {
            return 'El tipo de costo seleccionado no es válido.';
        }

        return '';
    }

    public function responseSetJSON(string $status, string $mensaje, mixed $data = null) {
        return $this->response->setJSON([
                    'status' => $status,
                    'msg' => $mensaje,
                    'data' => $data,
        ]);
    }
}
