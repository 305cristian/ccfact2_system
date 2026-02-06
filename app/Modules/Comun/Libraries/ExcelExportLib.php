<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Comun\Libraries;

/**
 * Description of ExcelExportLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 27 ene 2026
 * @time 4:03:40 p.m.
 */
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ExcelExportLib {

    public function export(array $config) {


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        /**
         * =========================
         * LOGO (ESQUINA IZQUIERDA)
         * =========================
         */
        $logoPath = FCPATH . 'uploads/img/enterprice/logo.png'; // ruta física

        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo Empresa');
            $drawing->setPath($logoPath);
            $drawing->setHeight(45); // altura del logo
            $drawing->setCoordinates('A1'); // esquina izquierda
            $drawing->setOffsetX(5);
            $drawing->setOffsetY(5);
            $drawing->setWorksheet($sheet);
        }

        /**
         * =========================
         * ENCABEZADO DEL REPORTE
         * =========================
         */
        $sheet->mergeCells('A1:' . $config['lastColumn'] . '1');
        $sheet->setCellValue('A1', $config['title']);

        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);

        // Subtítulo / fecha
        $sheet->mergeCells('A2:' . $config['lastColumn'] . '2');
        $sheet->setCellValue('A2', 'Generado el: ' . date('d/m/Y H:i'));

        $sheet->getStyle('A2')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        /**
         * =========================
         * ENCABEZADOS DE TABLA
         * =========================
         */
        $sheet->fromArray($config['headers'], null, 'A4');

        $headerRange = 'A4:' . $config['lastColumn'] . '4';

        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'A1A1A1'] // azul Bootstrap
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);

        /**
         * =========================
         * DATA
         * =========================
         */
        $row = 5;
        foreach ($config['data'] as $item) {
            $sheet->fromArray($item, '-', "A{$row}");
            $row++;
        }

        // Autosize columnas
        foreach (range('A', $config['lastColumn']) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        /**
         * =========================
         * DESCARGA
         * =========================
         */
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $config['filename'] . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
