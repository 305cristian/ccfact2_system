<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\AjustesSalida\Controllers;

/**
 * Description of GestionController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 29 nov 2025
 * @time 3:20:29 p.m.
 */
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Modules\AjustesSalida\Models\SalidasModel;

class GestionController extends \App\Controllers\BaseController {

    //put your code here

    protected $dirViewModule;
    protected $salidasModel;

    public function __construct() {

        $this->dirViewModule = 'Modules\AjustesSalida\Views';
        $this->salidasModel = new SalidasModel();
    }

    public function index() {
        $this->user->validateSession();

        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);

        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaMotivos'] = $this->ccm->getData('cc_motivos_ajuste', ['mot_estado' => 1, 'mot_tipo !=' => 'AJUSTES'], 'id, mot_nombre, CONCAT(mot_nombre, " ( ", mot_tipo," )") motivo');
        $data['listaCentroCostos'] = $this->ccm->getData('cc_centroscosto', ['cc_estado' => 1], 'id, cc_nombre');
        $data['listaServicios'] = $this->ccm->getData('cc_servicios', ['serv_estado' => 1], 'id, serv_nombre');

        $bodegaMainUsuario = $this->ccm->getValue('cc_bodegas', $this->user->id, 'id', 'id');
        $data['bodegaId'] = $this->session->get('bodegaIdAjs') ?? $bodegaMainUsuario;

        $send['view'] = view($this->dirViewModule . '\viewGestionAjuste', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
    }

    public function searchAjustes() {
        $dataPost = json_decode(file_get_contents('php://input'));

        $filtros = [
            'ajesSecuencial' => $dataPost->ajssSecuencial ?? null,
            'ajesBodega' => $dataPost->ajesBodega ?? null,
            'ajesMotivo' => $dataPost->ajesMotivo ?? null,
            'ajesCentrocosto' => $dataPost->ajesCentrocosto ?? null,
            'ajesEstado' => $dataPost->ajesEstado ?? null,
            'ajesFechas' => $dataPost->ajesFechas ?? null
        ];

        $data = $this->salidasModel->searchAjustes($filtros);

        return $data ? $this->response->setJSON(['status' => 'success', 'data' => $data]) : $this->response->setJSON(['status' => 'warning', 'data' => []]);
    }

    public function getDataDetalle($ajusteId) {

        $empresa = enterprice();
        $ajusteData = $this->salidasModel->getDataDetalle($ajusteId);

        $data = [
            'ajuste' => $ajusteData,
            'empresa' => $empresa,
        ];

        $view = view('\Modules\AjustesSalida\Views\reportes\viewDetalleReport', $data);
        return $this->response->setJSON($view);
    }

    public function generarPDF($ajusteId) {
        $empresa = enterprice();
        $ajusteData = $this->salidasModel->getDataDetalle($ajusteId);

        $data = [
            'ajuste' => $ajusteData,
            'empresa' => $empresa,
        ];

        $view = view('\Modules\AjustesSalida\Views\reportes\viewDetalleReport', $data);

        // Cargar CSS de Bootstrap (desde tu carpeta local)
        $bootstrapPath = FCPATH . 'resources/css/stylesMpdf.css';
        $bootstrapCSS = file_get_contents($bootstrapPath);

        // Configurar mPDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'margin_right' => 10,
            'default_font_size' => 8,
            'default_font' => 'dejavusans',
        ]);

        // Configuraciones adicionales para mejor renderizado
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        // Inyectar CSS y contenido HTML
        $mpdf->WriteHTML($bootstrapCSS, HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($view, HTMLParserMode::HTML_BODY);
        $mpdf->SetHTMLFooter('<div class="text-center small">Página {PAGENO} de {nbpg}</div>');

        // Nombre del archivo
        $fileName = "Ajuste_Salida_{$ajusteData->ajes_secuencial}.pdf";

        if ($this->request->getGet('download')) {
//          return $mpdf->Output($fileName, 'D'); PARA DESCARGA DIRECTA
            return $this->response
                            ->setHeader('Content-Type', 'application/pdf')
                            ->setHeader('Content-Disposition', 'inline; filename="' . $fileName . '"')
                            ->setBody($mpdf->Output($fileName, 'D')); // (D,I,S) (Download,Inline, devuelve contenido binario)
        } else {
            $directory = WRITEPATH . 'uploads/pdfs/ajustesSalida/';
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

        $dataPost = json_decode(file_get_contents("php://input"));

        $para = $dataPost->para;
        $cc = $dataPost->cc;
        $asunto = $dataPost->asunto;
        $mensaje = $dataPost->mensaje;
        $ajusteId = $dataPost->idAjuste;

        if (!$para || !$asunto) {
            return $this->responseSetJSON('warning', '⚠️ Debe completar los campos obligatorios (Para y Asunto)');
        }

        // Convertir cadenas separadas por coma a arrays + eliminar espacios y vacíos
        $paraArray = array_filter(array_map('trim', explode(',', $para)));
        $ccArray = array_filter(array_map('trim', explode(',', $cc)));

        $email = \Config\Services::email();
        $email->clear(true);
        $email->setFrom('no-reply@ccomputers.com', 'CCFACT - Sistema ERP');
        $email->setTo($paraArray);
        if (!empty($ccArray)) {
            $email->setCC($ccArray);
        }
        $email->setSubject($asunto);

        // PARA MENSAJES HTML:
        // $email->setMailType('html');
        //$mensajeHTML = nl2br(htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'));

        $email->setMailType('text');
        $email->setMessage($mensaje);

        // Generar PDF
        $pdfData = $this->generarPDF($ajusteId);
        log_message('debug', 'PDF Data: ' . print_r($pdfData, true));

        if (isset($pdfData['path']) && file_exists($pdfData['path'])) {
            $email->attach($pdfData['path']);
        } else {
            return $this->responseSetJSON('warning', 'No se pudo generar el PDF del ajuste.');
        }

        if ($email->send()) {
            if (file_exists($pdfData['path'])) {
                unlink($pdfData['path']);
            }
            return $this->responseSetJSON('success', 'Correo enviado exitosamente');
        } else {
            return $this->responseSetJSON('warning', 'Error al enviar el email, Verifique configuración SMTP: ' . $email->printDebugger());
        }
    }
}
