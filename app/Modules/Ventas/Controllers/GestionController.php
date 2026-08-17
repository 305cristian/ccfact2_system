<?php

namespace Modules\Ventas\Controllers;

use App\Controllers\BaseController;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Modules\Ventas\Models\VentasModel;

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of GestionController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 9 ago 2026
 * @time 8:26:14 a.m.
 */
class GestionController extends BaseController {

    //put your code here
    protected $dirViewModule;
    protected VentasModel $ventasModel;

    public function __construct() {
        $this->dirViewModule = 'Modules\Ventas\Views';
        $this->ventasModel = new VentasModel();
    }

    public function index() {

        $this->user->validateSession();

        $data['title'] = "Listar Ventas";
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaCentroCostos'] = $this->ccm->getData('cc_centroscosto', ['cc_estado' => 1], 'id, cc_nombre');
        $tiposComprobantes = $this->ccm->getData('cc_tipos_comprobante', ['comp_estado' => 1], 'id, comp_codigo, comp_nombre');
        $data['listaTiposComprobantes'] = array_values(array_filter($tiposComprobantes, static fn($comprobante) => in_array((string) $comprobante->comp_codigo, ['01', '02'], true)));

        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewGestionVentas', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
    }

    public function searchVentas() {

        $this->user->validateSession();
        $dataPost = json_decode(file_get_contents('php://input')) ?? (object) [];

        $filtros = [
            'venFechasEmision' => $dataPost->venFechasEmision ?? null,
            'venFechasArchivado' => $dataPost->venFechasArchivado ?? null,
            'venSecuencial' => $dataPost->venSecuencial ?? null,
            'venComprobante' => $dataPost->venComprobante ?? null,
            'venCliente' => $dataPost->venCliente ?? null,
            'venBodega' => $dataPost->venBodega ?? null,
            'venCentroCosto' => $dataPost->venCentroCosto ?? null,
            'venTipoComprobante' => $dataPost->venTipoComprobante ?? null,
            'venTipoVenta' => $dataPost->venTipoVenta ?? null,
            'venEstado' => $dataPost->venEstado ?? null,
        ];

        $ventas = $this->ventasModel->searchVentas($filtros);

        return $this->response->setJSON([
                    'status' => $ventas ? 'success' : 'warning',
                    'data' => $ventas,
        ]);
    }

    public function contadoresVentas() {

        $this->user->validateSession();
        $dataPost = json_decode(file_get_contents('php://input')) ?? (object) [];
        $contadores = $this->ventasModel->contadoresVentas($dataPost->venFechasEmision ?? null, $dataPost->venFechasArchivado ?? null);
        $data = [
            'BORRADOR' => 0,
            'ARCHIVADO' => 0,
            'ANULADA_EN_PENDIENTE' => 0,
            'ANULADA_EN_ARCHIVADA' => 0,
        ];

        foreach ($contadores as $contador) {
            $data[$contador->ven_estado] = (int) $contador->total;
        }

        return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $data,
        ]);
    }

    public function getDataDetalle(int $ventaId) {

        $this->user->validateSession();

        $venta = $this->ventasModel->getDataDetalle($ventaId);

        if (!$venta) {
            return $this->response
                            ->setStatusCode(404)
                            ->setJSON([
                                'status' => 'warning',
                                'message' => 'No se encontro la venta solicitada.',
            ]);
        }

        $view = view('\Modules\Ventas\Views\reportes\viewDetalleReport', [
            'venta' => $venta,
            'empresa' => enterprice(),
        ]);

        return $this->response->setJSON($view);
    }

    public function getAsientoContable(int $ventaId) {

        $this->user->validateSession();

        $venta = $this->ventasModel->getDataDetalle($ventaId);

        if (!$venta) {
            return $this->response->setJSON(['status' => 'warning', 'msg' => 'No se encontro la venta solicitada.']);
        }

        $asientos = $this->ventasModel->getAsientosContablesVenta($ventaId);

        if (empty($asientos)) {
            return $this->response->setJSON(['status' => 'warning', 'msg' => 'La venta no tiene asientos contables registrados.']);
        }

        $view = view('\Modules\Ventas\Views\reportes\viewAsientoContable', ['venta' => $venta, 'asientos' => $asientos,]);

        return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $view,
        ]);
    }

    public function generarPDF(int $ventaId) {

        $this->user->validateSession();

        $venta = $this->ventasModel->getDataDetalle($ventaId);

        if (!$venta) {
            return $this->response
                            ->setStatusCode(404)
                            ->setJSON([
                                'status' => 'warning',
                                'message' => 'No se encontro la venta solicitada.',
            ]);
        }
        $send = [
            'venta' => $venta,
            'empresa' => enterprice(),
        ];

        $view = view('\Modules\Ventas\Views\reportes\viewDetalleReport', $send);

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
        $mpdf->SetHTMLFooter('<div style="text-align:center; font-size:8pt;">Pagina {PAGENO} de {nbpg}</div>');

        $nombreArchivo = 'venta_' . str_pad((string) $venta->ven_secuencial, 5, '0', STR_PAD_LEFT) . '.pdf';

        if ($this->request->getGet('download')) {
            return $this->response
                            ->setHeader('Content-Type', 'application/pdf')
                            ->setHeader('Content-Disposition', 'attachment; filename="' . $nombreArchivo . '"')
                            ->setBody($mpdf->Output('', 'S'));
        } else {
            $directorio = WRITEPATH . 'uploads/pdfs/ventas/';
            $pdfPath = $directorio . $nombreArchivo;

            if (!is_dir($directorio)) {
                mkdir($directorio, 0755, true);
            }

            file_put_contents($pdfPath, $mpdf->Output($nombreArchivo, 'S'));

            return [
                'success' => true,
                'path' => $pdfPath,
                'fileName' => $nombreArchivo,
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
        $ventaId = (int) ($dataPost->idVenta ?? 0);

        if ($ventaId <= 0) {
            return $this->responseSetJSON('warning', 'No se recibio la venta para enviar por email.');
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

        $pdfData = $this->generarPDF($ventaId);

        if (!is_array($pdfData) || empty($pdfData['path']) || !file_exists($pdfData['path'])) {
            return $this->responseSetJSON('warning', 'No se pudo generar el PDF de la venta.');
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
}
