<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Comun\Libraries;

use DateTime;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;


/**
 * Description of NormalizarLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 17 ago 2026
 * @time 12:38:41 p.m.
 */
class NormalizarLib {

    public function normalizarFechaExcel(mixed $valor): string {

        if ($valor === null || $valor === '') {
            return '';
        }

        if (is_numeric($valor)) {
            return ExcelDate::excelToDateTimeObject((float) $valor)->format('Y-m-d');
        }

        $valor = trim((string) $valor);
        $formatos = ['d/m/Y', 'Y-m-d', 'd-m-Y'];

        foreach ($formatos as $formato) {
            $fecha = DateTime::createFromFormat($formato, $valor);
            if ($fecha instanceof DateTime) {
                return $fecha->format('Y-m-d');
            }
        }

        $timestamp = strtotime($valor);
        return $timestamp ? date('Y-m-d', $timestamp) : '';
    }

    public function normalizarHoraExcel(mixed $valor): string {

        if ($valor === null || $valor === '') {
            return '';
        }

        if (is_numeric($valor)) {
            $segundos = (int) round(((float) $valor) * 86400);
            return gmdate('H:i:s', $segundos);
        }

        $valor = trim((string) $valor);
        $timestamp = strtotime($valor);
        return $timestamp ? date('H:i:s', $timestamp) : '';
    }
}
