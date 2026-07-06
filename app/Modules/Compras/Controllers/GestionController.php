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

        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);

        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaCentroCostos'] = $this->ccm->getData('cc_centroscosto', ['cc_estado' => 1], 'id, cc_nombre');
        $data['listaTiposComprobantes'] = $this->ccm->getData('cc_tipos_comprobante', ['comp_estado' => 1], 'id, comp_codigo, comp_nombre');

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

        $nombre = 'Compra_' . str_pad($compra->comp_secuencial, 5, '0', STR_PAD_LEFT) . '.pdf';

        return $this->response
                        ->setHeader('Content-Type', 'application/pdf')
                        ->setHeader('Content-Disposition', 'inline; filename="' . $nombre . '"' )
                        ->setBody($mpdf->Output($nombre, 'S'));
    }
}
