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

        $bodegaMainUsuario = bodegaMain($this->user->id);
        $data['bodegaId'] = $this->session->get('bodegaIdAjs') ?? $bodegaMainUsuario;

        $send['view'] = view($this->dirViewModule . '\viewGestionAjuste', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
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

    public function searchAjustes() {
        $this->user->validateSession();
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

    /**
     * Función para obtener los detalles de un ajuste de salida específico y generar una vista HTML con la información del ajuste
     * @param int $ajusteId El identificador único del ajuste de salida para el cual se desean obtener los detalles
     * @return JSON Respuesta con el contenido HTML generado a partir de los detalles del ajuste de salida, que puede ser utilizado por el frontend para mostrar la información del ajuste de salida de manera detallada
     * El método obtiene los detalles del ajuste de salida utilizando el modelo SalidasModel, prepara los datos necesarios para la vista, genera el contenido HTML a partir de la vista 'viewDetalleReport' y devuelve una respuesta JSON con el contenido HTML generado. Esta función es esencial para permitir a los usuarios visualizar la información detallada de un ajuste de salida específico, proporcionando una experiencia de usuario enriquecida y facilitando la comprensión de los detalles asociados al ajuste de salida seleccionado.
     * Es importante destacar que esta función se espera que sea llamada a través de una solicitud AJAX desde la interfaz de usuario del módulo de ajustes de salida, para permitir a los usuarios obtener los detalles del ajuste de salida sin necesidad de recargar la página completa. La respuesta JSON proporcionada por esta función debe ser manejada adecuadamente en el frontend para mostrar el contenido HTML generado y proporcionar una experiencia de usuario fluida y eficiente al visualizar los detalles del ajuste de salida.
     * En resumen, esta función es responsable de obtener los detalles de un ajuste de salida específico, generar una vista HTML con la información del ajuste de salida y devolver una respuesta JSON con el contenido HTML generado, para ser utilizado por el frontend en la visualización de los detalles del ajuste de salida seleccionado.
     * @throws \Exception Si ocurre un error al obtener los detalles del ajuste de salida o al generar la vista HTML, se lanzará una excepción con un mensaje descriptivo del error ocurrido, que puede ser
    */
    public function getDataDetalle(int $ajusteId) {

        $empresa = enterprice();
        $ajusteData = $this->salidasModel->getDataDetalle($ajusteId);

        $data = [
            'ajuste' => $ajusteData,
            'empresa' => $empresa,
        ];

        $view = view('\Modules\AjustesSalida\Views\reportes\viewDetalleReport', $data);
        return $this->response->setJSON($view);
    }

    /**
     * Función para generar un reporte en formato PDF de un ajuste de salida específico, utilizando la biblioteca mPDF para renderizar el contenido HTML y generar el archivo PDF
     * @param int $ajusteId El identificador único del ajuste de salida para el cual se desea generar el reporte en formato PDF
     * El método obtiene los detalles del ajuste de salida utilizando el modelo SalidasModel, prepara los datos necesarios para la vista, genera el contenido HTML a partir de la vista 'viewDetalleReport', carga el CSS de Bootstrap para el estilo del PDF, configura la biblioteca mPDF con las opciones necesarias para el formato del PDF, inyecta el contenido HTML y el CSS en mPDF, y finalmente genera el archivo PDF que puede ser descargado o visualizado en línea por el usuario, dependiendo de la configuración de la respuesta HTTP. Esta función es esencial para permitir a los usuarios generar un reporte en formato PDF de un ajuste de salida específico, proporcionando una forma conveniente de obtener un documento formal con la información detallada del ajuste de salida
    */
    public function generarPDF(int $ajusteId) {
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
