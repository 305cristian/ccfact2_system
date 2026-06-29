<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Transferencias\Controllers;

/**
 * Description of GestionController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 4 dic 2025
 * @time 11:51:25 p.m.
 */
use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;
use Modules\Transferencias\Models\TransferenciasModel;

class GestionController extends \App\Controllers\BaseController {

    //put your code here
    protected $dirViewModule;
    protected $transferModel;

    public function __construct() {
        $this->dirViewModule = 'Modules\Transferencias\Views';

        //IMPORT MODELS
        $this->transferModel = new TransferenciasModel();
    }

    public function index() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);

        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaUsuarios'] = $this->ccm->getData('cc_empleados', ['emp_estado' => 1], 'id, CONCAT(emp_nombre, " ", emp_apellido) empleado');

        $bodegaMainUsuario = bodegaMain($this->user->id);
        $data['userSession'] = $this->user->id;
        $data['rootUser'] = $this->user->root;
        $data['bodegaId'] = $this->session->get('bodegaIdAje') ? $this->session->get('bodegaIdAje') : $bodegaMainUsuario;
        $send['view'] = view($this->dirViewModule . '\viewGestionTransferencias', $data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }
    
     /**
     * Función para generar una respuesta JSON con un formato estándar para las operaciones del módulo de ajustes de salida
     * @param string $status El estado de la operación (e.g., 'success', 'error', 'warning')
     * @param string $mensaje Un mensaje descriptivo sobre el resultado de la operación
     * @param mixed $data (Opcional) Datos adicionales relacionados con la operación, como detalles del ajuste o información relevante para el frontend
     * @return JSON Respuesta formateada con el estado, mensaje y datos proporcionados, que puede ser utilizada por el frontend
    */
    public function responseSetJSON(string $status, string $mensaje, mixed $data = null) {
        return $this->response->setJSON([
                    'status' => $status,
                    'msg' => $mensaje,
                    'data' => $data,
        ]);
    }

    public function searchTransferencias() {
        $this->user->validateSession();
        $dataPost = json_decode(file_get_contents('php://input'));

        $filtros = [
            'trbSecuencial' => $dataPost->trbSecuencial ?? null,
            'trbBodegaOrigen' => $dataPost->trbBodegaOrigen ?? null,
            'trbBodegaDestino' => $dataPost->trbBodegaDestino ?? null,
            'trbEstado' => $dataPost->trbEstado,
            'trbFechas' => $dataPost->trbFechas ?? null,
            'trbUsuarioConfirmar' => $dataPost->trbUsuarioConfirmar ?? null
        ];

        $data = $this->transferModel->searchTransferencias($filtros);

        return $data ? $this->response->setJSON(['status' => 'success', 'data' => $data]) : $this->response->setJSON(['status' => 'warning', 'data' => []]);
    }

    public function contadoresTransferencias() {
        $this->user->validateSession();
        $dataPost = json_decode(file_get_contents('php://input'));

        if (empty($dataPost->trbFechas)) {
            return $this->response->setJSON(['status' => 'warning', 'msg' => 'No hay fecha seleccionada']);
        }

        // Inicializamos todos los estados en 0 (UX limpio)
        $contadores = [
            1 => 0, // BORRADOR
            2 => 0, // POR CONFIRMAR
            3 => 0, // CONFIRMADA
            0 => 0, // RECHAZADAS
            -1 => 0  // ANULADA
        ];

        $response = $this->transferModel->contadoresTransferencias($dataPost->trbFechas);

        if ($response) {
            foreach ($response as $row) {
                $estado = (int) $row->trb_estado;
                if (array_key_exists($estado, $contadores)) {
                    $contadores[$estado] = (int) $row->total;
                }
            }
        }
        return $this->response->setJSON([
                    'status' => $response ? 'success' : 'warning',
                    'data' => $response ? $contadores : []
        ]);
    }

    public function getDataDetalle($transferenciaId) {


        $empresa = enterprice();
        $ajusteData = $this->transferModel->getDataDetalle($transferenciaId);

        $data = [
            'transferencia' => $ajusteData,
            'empresa' => $empresa,
        ];

        $view = view('\Modules\Transferencias\Views\reportes\viewDetalleReport', $data);
        return $this->response->setJSON($view);
    }

    public function generarPDF($transferenciaId) {
        $empresa = enterprice();
        $transferenciaData = $this->transferModel->getDataDetalle($transferenciaId);

        $data = [
            'transferencia' => $transferenciaData,
            'empresa' => $empresa,
        ];

        $view = view('\Modules\Transferencias\Views\reportes\viewDetalleReport', $data);

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
        $fileName = "Transferencia_{$transferenciaData->trb_secuencial}.pdf";

        if ($this->request->getGet('download')) {
//          return $mpdf->Output($fileName, 'D'); PARA DESCARGA DIRECTA
            return $this->response
                            ->setHeader('Content-Type', 'application/pdf')
                            ->setHeader('Content-Disposition', 'inline; filename="' . $fileName . '"')
                            ->setBody($mpdf->Output($fileName, 'D')); // (D,I,S) (Download,Inline, devuelve contenido binario)
        } else {
            $directory = WRITEPATH . 'uploads/pdfs/transferencias/';
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
        $transferenciaId = $dataPost->idTransferencia;

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
        $pdfData = $this->generarPDF($transferenciaId);
        log_message('debug', 'PDF Data: ' . print_r($pdfData, true));

        if (isset($pdfData['path']) && file_exists($pdfData['path'])) {
            $email->attach($pdfData['path']);
        } else {
            return $this->responseSetJSON('warning', 'No se pudo generar el PDF de la transferencia.');
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
