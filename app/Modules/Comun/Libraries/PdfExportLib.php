<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Comun\Libraries;

/**
 * Description of PdfExportLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 27 ene 2026
 * @time 9:13:40 p.m.
 */
use Mpdf\Mpdf;

class PdfExportLib {

    public function __construct() {
        
    }

    public function export(array $config) {
        $mpdf = new Mpdf([
            'format' => 'A4',
            'orientation' => $config['orientation'] ?? 'P',
            'margin_top' => 30,
            'margin_bottom' => 15,
            'margin_left' => 10,
            'margin_right' => 10
        ]);

        // Header
        $logoPath = FCPATH . 'uploads/img/enterprice/logo.png'; // ruta física

        $mpdf->SetHTMLHeader("
             <table width='100%' style='border-bottom:1px solid #000;'>
                <tr>
                    <!-- LOGO -->
                    <td width='15%' style='text-align:center; padding:5px 0;'>
                        <img src='{$logoPath}' style='height:40px;'>
                    </td>

                    <!-- TITULO -->
                    <td width='70%' style='text-align:center;'>
                        <div style='font-size:14px; font-weight:bold;'>
                            {$config['title']}
                        </div>
                        <div style='font-size:10px;'>
                            Generado el: " . date('d/m/Y H:i') . "
                        </div>
                    </td>

                    <!-- ESPACIO DERECHO -->
                    <td width='15%'></td>
                </tr>
            </table>
        ");

        // Footer
        $mpdf->SetHTMLFooter("
            <div style='text-align:center; font-size:9px;'>
                Página {PAGENO} de {nbpg}
            </div>
        ");

        // CSS
        $mpdf->WriteHTML($this->baseCss(), \Mpdf\HTMLParserMode::HEADER_CSS);

        // HTML
        $mpdf->WriteHTML($config['html'], \Mpdf\HTMLParserMode::HTML_BODY);

        // Output
        $mpdf->Output($config['filename'], 'D');
        exit;
    }

    private function baseCss() {
        return "
            body { font-family: sans-serif; font-size: 10px; }
            table.report { width: 100%; border-collapse: collapse; }
            table.report th {
                background: #A1A1A1;
                color: #ffffff;
                padding: 6px;
                border: 1px solid #000;
                font-size: 10px;
            }
            table.report td {
                padding: 5px;
                border: 1px solid #000;
                font-size: 9px;
            }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
        ";
    }
}
