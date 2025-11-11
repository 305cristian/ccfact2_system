<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Comun\Controllers;

/**
 * Description of Index_controller
 * @author Cristian R. Paz
 * @Date 29 ene. 2023
 * @Time 17:22:52
 */
use \PhpOffice\PhpSpreadsheet\Spreadsheet;

class IndexController extends \App\Controllers\BaseController {

    public function __construct() {
        
    }

    public function index() {
//         $this->user->validateSession();
    }

    public function getProvincias() {
        $response = $this->ccm->getData('cc_provincia');
        if ($response) {
            return $this->response->setJSON($response);
        }
        return $this->response->setJSON(false);
    }

    public function getCantones() {
        $response = $this->ccm->getData('cc_canton');
        if ($response) {
            return $this->response->setJSON($response);
        }
        return $this->response->setJSON(false);
    }

    public function getParroquias() {
        $response = $this->ccm->getData('cc_parroquia');
        if ($response) {
            return $this->response->setJSON($response);
        }
        return $this->response->setJSON(false);
    }

    public function getCantonesByProvincia($idProvincia) {
        $response = $this->ccm->getData('cc_canton', ['fk_provincia' => $idProvincia]);
        if ($response) {
            return $this->response->setJSON($response);
        }
        return $this->response->setJSON(false);
    }

    public function getParroquiasByCanton($idCanton) {
        $response = $this->ccm->getData('cc_parroquia', ['fk_canton' => $idCanton]);
        if ($response) {
            return $this->response->setJSON($response);
        }
        return $this->response->setJSON(false);
    }

    public function generarExcel() {
        if ($this->request->getPost('dataHtml')) {
            $title = $this->request->getPost('titleDoc');
            $dataReport = $this->request->getPost('dataHtml');

            // Establecer la codificación de caracteres
            header("Content-type: application/vnd.ms-excel; charset=UTF-8");
            header("Content-Disposition: attachment; filename=" . $title . ".xls");
            header("Pragma: no-cache");
            header("Expires: 0");

            // BOM para UTF-8
            echo "\xEF\xBB\xBF";
            echo $dataReport;
            exit();
        } else {
            return $this->response->setJSON([
                        'success' => false,
                        'message' => 'No se proporcionaron datos para exportar.'
            ]);
        }
    }

    public function downloadPlantillaExcel() {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Plantilla Ajuste Entrada');
        $sheet->fromArray(['Código', 'Cantidad', 'Lote', 'Fecha Elaboración', 'Fecha Caducidad'], null, 'A1');
        $sheet->fromArray(['CCF-000011', '10', '566UU', '2025-08-25', '2026-08-25'], null, 'A2');
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Plantilla_Ajuste_Entrada.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
    public function downloadPlantillaExcelAjusteInicial() {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Plantilla Ajuste Inicial');
        $sheet->fromArray(['Código','Nombre','Precio Sin IVA', 'Cantidad','Grupo','Subgrupo','Marca','Unidad Medida','Código Barras 1','Código Barras 2','Código Barras 3', 'Lote', 'Fecha Elaboración', 'Fecha Caducidad','Precio A','Cuenta contable compras','Cuenta contable ventas'], null, 'A1');
        $sheet->fromArray(['CCF-000011','LECHE ENTERA','1.50', '10','ABARROTES','LACTEOS','NUTRI','UNIDAD','5FDS6F5SD6','FDS75F7DF','5D45D45DS4F5', '566UU', '2025-08-25', '2026-08-25','1.40','1.01.04.01.02','1.01.04.01.02'], null, 'A2');
        $sheet->getStyle('A1:Q1')->getFont()->setBold(true);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Plantilla_Ajuste_Entrada_Inicial.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
