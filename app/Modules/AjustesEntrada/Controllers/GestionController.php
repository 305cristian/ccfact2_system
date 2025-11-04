<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\AjustesEntrada\Controllers;

/**
 * Description of GestionController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 22 oct 2025
 * @time 2:57:09 p.m.
 */
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Modules\AjustesEntrada\Models\EntradasModel;

class GestionController extends \App\Controllers\BaseController {

    protected $dirViewModule;
    protected $entadasModel;

    public function __construct() {

        $this->dirViewModule = 'Modules\AjustesEntrada\Views';

        //IMPORT MODELS
        $this->entadasModel = new EntradasModel();
    }

    public function index() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);

        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaMotivos'] = $this->ccm->getData('cc_motivos_ajuste', ['mot_estado' => 1, 'mot_tipo' => "AJUSTES"], 'id, mot_nombre');
        $data['listaCentroCostos'] = $this->ccm->getData('cc_centroscosto', ['cc_estado' => 1], 'id, cc_nombre');

        $bodegaMainUsuario = $this->ccm->getValue('cc_bodegas', $this->user->id, 'id', 'id');

        $data['bodegaId'] = $this->session->get('bodegaIdAje') ? $this->session->get('bodegaIdAje') : $bodegaMainUsuario;
        $send['view'] = view($this->dirViewModule . '\viewGesionarAjuste', $data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function getAjustes() {

        $response = $this->entadasModel->getAjustes();

        if ($response) {
            return $this->response->setJSON($response);
        }

        return $this->response->setJSON(false);
    }

    public function getDataDetalle($idAjuste) {

        $empresa = enterprice();
        $ajusteData = $this->entadasModel->getDataDetalle($idAjuste);

        $data = [
            'ajuste' => $ajusteData,
            'empresa' => $empresa,
        ];

        $view = view('\Modules\AjustesEntrada\Views\reportes\viewDetalleReport', $data);
        return $this->response->setJSON($view);
    }

    public function generarPDF($idAjuste) {
        $empresa = enterprice();
        $ajusteData = $this->entadasModel->getDataDetalle($idAjuste);

        $data = [
            'ajuste' => $ajusteData,
            'empresa' => $empresa,
        ];

        $view = view('\Modules\AjustesEntrada\Views\reportes\viewDetalleReport', $data);

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
        $fileName = "Ajuste_Entrada_{$ajusteData->ajen_secuencial}.pdf";

        if ($this->request->getGet('download')) {
//          return $mpdf->Output($fileName, 'D'); PARA DESCARGA DIRECTA
            return $this->response
                            ->setHeader('Content-Type', 'application/pdf')
                            ->setHeader('Content-Disposition', 'inline; filename="' . $fileName . '"')
                            ->setBody($mpdf->Output($fileName, 'D')); // (D,I,S) (Download,Inline, devuelve contenido binario)
        } else {
            $pdfPath = WRITEPATH . 'uploads/pdfs/' . $fileName;

            if (!is_dir(WRITEPATH . 'uploads/pdfs')) {
                mkdir(WRITEPATH . 'uploads/pdfs', 0755, true);
            }
            file_put_contents($pdfPath, $mpdf->Output($fileName, 'S'));

            return $this->response->setJSON([
                        'success' => true,
                        'path' => $pdfPath,
                        'fileName' => $fileName
            ]);
        }
    }

    public function enviarPorEmail($idAjuste) {
        $empresa = enterprice();
        // Generar PDF
        $pdfData = $this->generarPDF($idAjuste);

        // Configurar email
        $email = \Config\Services::email();
        $email->setFrom('noreply@ccomputers.com', $empresa->epr_nombre_comercial);
        $email->setTo('pcris.994@gmail.com');
        $email->setSubject('Ajuste de Entrada #' . $pdfData['secuencial']);
        $email->setMessage('Adjunto encontrará el ajuste de entrada.');
        $email->attach($pdfData['path']);

        if ($email->send()) {
            // Opcional: eliminar el archivo temporal
            // unlink($pdfData->path);

            return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Email enviado exitosamente'
            ]);
        } else {
            return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Error al enviar email: ' . $email->printDebugger()
            ]);
        }
    }

   
}
