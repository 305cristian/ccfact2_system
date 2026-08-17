<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\BioComedor\Controllers;

use App\Controllers\BaseController;
use Modules\BioComedor\Libraries\BioMarcacionesLib;
use Modules\BioComedor\Models\BioModel;
use Modules\Comun\Libraries\NormalizarLib;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Description of LoadMarcacionesController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 17 ago 2026
 * @time 11:15:13 a.m.
 */
class LoadMarcacionesController extends BaseController {
    //put your code here

    protected BioModel $bioModel;
    protected BioMarcacionesLib $bioMarcacionesLib;
    
    public NormalizarLib $normaLib;

    public function __construct() {
        $this->bioModel = new BioModel();
        $this->bioMarcacionesLib = new BioMarcacionesLib();
        $this->normaLib = new NormalizarLib();
    }

    public function previewImportExcel() {

        $this->user->validateSession();

        try {
            $file = $this->request->getFile('file');

            if (!$file || !$file->isValid()) {
                return $this->responseSetJSON('warning', 'Debe seleccionar un archivo Excel valido.');
            }

            $extension = strtolower($file->getClientExtension());
            if (!in_array($extension, ['xlsx', 'xls'], true)) {
                return $this->responseSetJSON('warning', 'Solo se permiten archivos Excel .xlsx o .xls.');
            }

            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();
            $registros = $sheet->toArray(null, true, true, true);
            $filas = [];

            foreach ($registros as $i => $row) {
                if ($i === 1) {
                    continue;
                }

                $row['F'] = trim((string) $sheet->getCell('F' . $i)->getFormattedValue());// Le digo que obtenga el codigo tal cual como lo trae del excel

                if ($this->filaVacia($row)) {
                    continue;
                }

                $filas[] = $this->validarFilaExcel($i, $row);
            }

            if (empty($filas)) {
                return $this->responseSetJSON('warning', 'El archivo no contiene registros para validar.');
            }

            $correctas = array_values(array_filter($filas, static fn($fila) => (bool) $fila['valido']));

            return $this->responseSetJSON('success', 'Archivo validado correctamente.', [
                        'filas' => $filas,
                        'total' => count($filas),
                        'correctas' => count($correctas),
                        'errores' => count($filas) - count($correctas),
            ]);
        } catch (\Throwable $e) {
            return $this->responseSetJSON('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    public function guardarMarcacionesValidas() {

        $this->user->validateSession();
        $dataPost = $this->request->getJSON();
        $filas = is_array($dataPost->filas ?? null) ? $dataPost->filas : [];

        if (empty($filas)) {
            return $this->responseSetJSON('warning', 'No se recibieron marcaciones validas para guardar.');
        }

        $guardadas = 0;
        $errores = [];

        foreach ($filas as $fila) {
            $fila = (object) $fila;

            if (empty($fila->valido)) {
                continue;
            }

            $errorRepetida = $this->validarMarcacionRepetida((int) ($fila->fkComensal ?? 0), (int) ($fila->fkServicioDetectado ?? 0), (string) ($fila->fecha ?? ''), (string) ($fila->hora ?? ''));
            if ($errorRepetida !== '') {
                $errores[] = 'Fila ' . ($fila->fila ?? '-') . ': ' . $errorRepetida;
                continue;
            }

            $dataMarcacion = (object) [
                        'fkComedor' => $fila->fkComedor ?? null,
                        'fkEquipo' => $fila->fkEquipo ?? null,
                        'tipoIdentificacion' => 'CODIGO',
                        'identificador' => $fila->codigoComensal ?? '',
                        'marcFecha' => $fila->fecha ?? '',
                        'marcHora' => $fila->hora ?? '',
                        'marcOrigen' => 'IMPORTACION',
            ];

            $respuesta = $this->bioMarcacionesLib->procesarMarcacion($dataMarcacion);

            if (($respuesta['status'] ?? '') === 'success') {
                $guardadas++;
            } else {
                $errores[] = 'Fila ' . ($fila->fila ?? '-') . ': ' . ($respuesta['msg'] ?? 'No se pudo registrar la marcacion.');
            }
        }

        if ($guardadas === 0) {
            return $this->responseSetJSON('warning', 'No se registro ninguna marcacion.', [
                        'errores' => $errores,
            ]);
        }

        $this->logs->logSuccess('SE HAN IMPORTADO ' . $guardadas . ' MARCACIONES BIO COMEDOR DESDE EXCEL');

        return $this->responseSetJSON('success', "Se registraron {$guardadas} marcaciones correctamente.", [
                    'guardadas' => $guardadas,
                    'errores' => $errores,
        ]);
    }

    public function responseSetJSON(string $status, string $mensaje, mixed $data = null) {
        return $this->response->setJSON([
                    'status' => $status,
                    'msg' => $mensaje,
                    'data' => $data,
        ]);
    }

    private function validarFilaExcel(int $fila, array $row): array {

        $cedula = trim((string) ($row['A'] ?? ''));
        $comensalExcel = trim((string) ($row['B'] ?? ''));
        $fecha = $this->normaLib->normalizarFechaExcel($row['C'] ?? '');
        $hora = $this->normaLib->normalizarHoraExcel($row['D'] ?? '');
        $comedorTexto = trim((string) ($row['E'] ?? ''));
        $equipoCodigo = trim((string) ($row['F'] ?? ''));
        $servicioTexto = trim((string) ($row['G'] ?? ''));
        $errores = [];

        $data = [
            'fila' => $fila,
            'cedula' => $cedula,
            'comensalExcel' => $comensalExcel,
            'fecha' => $fecha,
            'hora' => $hora,
            'comedorExcel' => $comedorTexto,
            'equipoCodigoExcel' => $equipoCodigo,
            'servicioExcel' => $servicioTexto,
            'comensal' => '',
            'codigoComensal' => '',
            'fkComensal' => null,
            'comedor' => '',
            'equipo' => '',
            'servicioDetectado' => '',
            'fkServicioDetectado' => null,
            'valido' => false,
            'errores' => [],
        ];

        if ($cedula === '') {
            $errores[] = 'Debe ingresar cedula/dni.';
        }

        if ($fecha === '') {
            $errores[] = 'Fecha invalida.';
        }

        if ($hora === '') {
            $errores[] = 'Hora invalida.';
        }

        if ($comedorTexto === '') {
            $errores[] = 'Debe ingresar comedor.';
        }

        if ($equipoCodigo === '') {
            $errores[] = 'Debe ingresar codigo de equipo.';
        }

        if ($servicioTexto === '') {
            $errores[] = 'Debe ingresar servicio.';
        }

        $comensal = $cedula !== '' ? $this->bioModel->getComensalActivoPorCedula($cedula) : null;
        if (!$comensal) {
            $errores[] = "No existe un comensal activo con cedula {$cedula}.";
        } else {
            $data['comensal'] = trim($comensal->comens_nombres . ' ' . $comensal->comens_apellidos);
            $data['codigoComensal'] = $comensal->comens_codigo;
            $data['fkComensal'] = (int) $comensal->id;
        }

        $comedor = $comedorTexto !== '' ? $this->bioModel->getComedorActivoPorTexto($comedorTexto) : null;
        if (!$comedor) {
            $errores[] = "No existe un comedor activo para '{$comedorTexto}'.";
        } else {
            $data['fkComedor'] = (int) $comedor->id;
            $data['comedor'] = $comedor->com_codigo . ' - ' . $comedor->com_nombre;
        }

        $equipo = ($comedor && $equipoCodigo !== '') ? $this->bioModel->getEquipoActivoPorCodigoComedor((int) $comedor->id, $equipoCodigo) : null;
        if ($comedor && $equipoCodigo !== '' && !$equipo) {
            $errores[] = "No existe un equipo activo con codigo {$equipoCodigo} para el comedor {$comedor->com_nombre}.";
        } else if ($equipo) {
            $data['fkEquipo'] = (int) $equipo->id;
            $data['equipo'] = $equipo->eq_codigo . ' - ' . $equipo->eq_nombre;
        }

        $servicio = $servicioTexto !== '' ? $this->bioModel->getServicioActivoPorTexto($servicioTexto) : null;
        if (!$servicio) {
            $errores[] = "No existe un servicio activo para '{$servicioTexto}'.";
        }

        $horario = $hora !== '' ? $this->bioModel->getHorarioServicioPorHora($hora) : null;
        if (!$horario && $hora !== '') {
            $errores[] = "No existe un servicio configurado para la hora {$hora}.";
        } else if ($horario) {
            $data['servicioDetectado'] = $horario->serv_nombre;
            $data['fkServicioDetectado'] = (int) $horario->fk_servicio;
        }

        if ($servicio && $horario && (int) $servicio->id !== (int) $horario->fk_servicio) {
            $errores[] = "La hora {$hora} corresponde a {$horario->serv_nombre}, no a {$servicio->serv_nombre}.";
        }

        if ($comensal && $horario && $fecha !== '') {
            $errorRepetida = $this->validarMarcacionRepetida((int) $comensal->id, (int) $horario->fk_servicio, $fecha, $hora);
            if ($errorRepetida !== '') {
                $errores[] = $errorRepetida;
            }
        }

        $data['errores'] = $errores;
        $data['valido'] = empty($errores);

        return $data;
    }

    private function validarMarcacionRepetida(int $comensalId, int $servicioId, string $fecha, string $hora): string {

        $fechaHora = $fecha . ' ' . $hora;
        $toleranciaMinutos = (int) getSettings("TOLERANCIA_MARCACION_MINUTOS");
        $fechaHoraDesde = date('Y-m-d H:i:s', strtotime($fechaHora . ' -' . $toleranciaMinutos . ' minutes'));
        $fechaHoraHasta = date('Y-m-d H:i:s', strtotime($fechaHora . ' +' . $toleranciaMinutos . ' minutes'));
        $marcacionTolerancia = $this->bioModel->getMarcacionPorTolerancia($comensalId, $fechaHoraDesde, $fechaHoraHasta);

        if ($marcacionTolerancia) {
            return 'Existe una marcacion valida dentro de la tolerancia configurada.';
        }

        $permiteMultiple = (string) getSettings("PERMITIR_MULTIPLE_CONSUMO_SERVICIO");
        $marcacionDia = $this->bioModel->getMarcacionValidaServicioDia($comensalId, $servicioId, $fecha);

        if ($permiteMultiple !== '1' && $marcacionDia) {
            return 'El comensal ya registra un consumo valido para este servicio en la fecha seleccionada.';
        }

        return '';
    }

    private function filaVacia(array $row): bool {

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $columna) {
            if (trim((string) ($row[$columna] ?? '')) !== '') {
                return false;
            }
        }

        return true;
    }

  
}
