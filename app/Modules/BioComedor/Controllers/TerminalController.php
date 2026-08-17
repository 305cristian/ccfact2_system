<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\BioComedor\Controllers;

use App\Controllers\BaseController;
use Modules\BioComedor\Libraries\BioMarcacionesLib;
use Modules\BioComedor\Models\BioModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Description of TerminalController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 24 jul 2026
 * @time 12:45:47 p.m.
 */
class TerminalController extends BaseController {
    //put your code here

    protected BioMarcacionesLib $bioMarcacionesLib;
    protected string $dirViewModule;
    protected BioModel $bioModel;

    public function __construct() {

        $this->dirViewModule = 'Modules\BioComedor\Views';
        $this->bioModel = new BioModel();
        $this->bioMarcacionesLib = new BioMarcacionesLib();
    }

    public function index() {

        $this->user->validateSession();

        $data['title'] = "Terminal de Marcacion";
        $data['listaEquipos'] = $this->bioModel->getListaEquiposActivos();

        return view($this->dirViewModule . '\viewTerminalMarcacion', $data);
    }

    public function getServicioActual() {

        $this->user->validateSession();

        $hora = date('H:i:s');
        
        $horario = $this->bioModel->getHorarioServicioPorHora($hora);

        return $this->response->setJSON([
                    'status' => $horario ? 'success' : 'warning',
                    'msg' => $horario ? 'Servicio activo encontrado.' : 'No existe un servicio activo para la hora actual.',
                    'data' => $horario,
        ]);
    }

    public function registrarMarcacion() {

        $this->user->validateSession();

        $validacion = $this->validarDatosTerminal();
        if ($validacion !== null) {
            return $this->response->setJSON($validacion);
        }

        $dataMarcacion = (object) [
                    'fkEquipo' => $this->request->getPost('fkEquipo'),
                    'tipoIdentificacion' => 'AUTO',
                    'identificador' => $this->request->getPost('identificador'),
                    'marcFecha' => date('Y-m-d'),
                    'marcHora' => date('H:i:s'),
                    'marcOrigen' => 'TERMINAL',
        ];

        $respuesta = $this->bioMarcacionesLib->procesarMarcacion($dataMarcacion);
        return $this->response->setJSON($respuesta);
    }

    public function descargarPlantillaMarcaciones() {

        $this->user->validateSession();

        $equipoId = (int) ($this->request->getGet('fkEquipo') ?? 0);
        $fechaDesde = trim((string) ($this->request->getGet('fechaDesde') ?? ''));
        $fechaHasta = trim((string) ($this->request->getGet('fechaHasta') ?? ''));

        if ($equipoId <= 0 || $fechaDesde === '' || $fechaHasta === '') {
            return redirect()->back()->with('error', 'Debe seleccionar equipo y rango de fechas.');
        }

        $equipo = $this->bioModel->getEquipoActivoPorId($equipoId);
        if (!$equipo) {
            return redirect()->back()->with('error', 'El equipo de marcacion no existe o esta inactivo.');
        }

        $marcaciones = $this->bioModel->getMarcacionesTerminalExport($equipoId, $fechaDesde, $fechaHasta);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Marcaciones');

        $headers = ['cedula - dni', 'comensal', 'Fecha', 'Hora', 'Comedor', 'Equipo cod', 'Servicio'];
        $sheet->fromArray($headers, null, 'A1');

        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        $row = 2;
        foreach ($marcaciones as $marcacion) {
            $sheet->setCellValueExplicit("A{$row}", (string) $marcacion->comens_cedula, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("B{$row}", $marcacion->comensal);
            $sheet->setCellValue("C{$row}", date('d/m/Y', strtotime($marcacion->marc_fecha)));
            $sheet->setCellValue("D{$row}", substr((string) $marcacion->marc_hora, 0, 5));
            $sheet->setCellValue("E{$row}", $marcacion->com_codigo);
            $sheet->setCellValueExplicit("F{$row}", (string) $marcacion->eq_codigo, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("G{$row}", $marcacion->serv_nombre);
            $row++;
        }

        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->getStyle('A:G')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

        $fileName = 'MARCACIONES_' . $equipo->com_codigo . '_' . $equipo->eq_codigo . '_' . $fechaDesde . '_' . $fechaHasta . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function validarDatosTerminal(): ?array {

        $this->validation->setRules([
            'fkEquipo' => ['label' => 'Equipo', 'rules' => 'trim|required'],
            'identificador' => ['label' => 'Identificador', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {
            return null;
        }

        return [
            'status' => 'vacio',
            'msg' => [
                'fkEquipo' => $this->validation->getError('fkEquipo'),
                'identificador' => $this->validation->getError('identificador'),
            ],
        ];
    }
}
