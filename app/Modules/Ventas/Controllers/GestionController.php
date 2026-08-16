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

        $view = view('\Modules\Ventas\Views\reportes\viewAsientoContable', ['venta' => $venta,'asientos' => $asientos,]);

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

        $view = view('\Modules\Ventas\Views\reportes\viewDetalleReport', [
            'venta' => $venta,
            'empresa' => enterprice(),
        ]);

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
        }

        return $this->response
                        ->setHeader('Content-Type', 'application/pdf')
                        ->setBody($mpdf->Output('', 'S'));
    }
}
