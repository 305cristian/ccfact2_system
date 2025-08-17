<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Comun\Libraries;

/**
 * Description of ValidadorCiRuc
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 16 ago 2025
 * @time 6:13:14 p.m.
 */
class ValidadorCiRuc  {

    protected $docIdent;

    public function __construct($docIdent = null) {
        $this->docIdent = $docIdent ?? service('validadorEc');
    }

    public function validarNumeroDocumento($clieTipoDocumento, $clieCiruc) {


        $valida = true;

        if ($clieTipoDocumento == 1) {// SI ES RUC
            if (strlen($clieCiruc) != 13) {
                return[
                    'status' => "warning",
                    'msg' => "El Ruc es invalido, el tamaño de caracteres es invalido",
                ];
            }

            $keyDigito = (int) substr($clieCiruc, 2, 1);

            if ($keyDigito >= 0 && $keyDigito <= 5) {
                // RUC Persona Natural
                $valida = $this->docIdent->validarRucPersonaNatural($clieCiruc);
                $clieTipoCliente = "NATURAL";
            } elseif ($keyDigito == 6) {
                // RUC Sociedad Pública
                $valida = $this->docIdent->validarRucSociedadPublica($clieCiruc);
                $clieTipoCliente = "JURIDICA PUBLICA";
            } elseif ($keyDigito == 9) {
                // RUC Sociedad Privada
                $valida = $this->docIdent->validarRucSociedadPrivada($clieCiruc);
                $clieTipoCliente = "JURIDICA PRIVADA";
            } else {
                $valida = false;
            }
            if ($valida == false) {
                return [
                    'status' => "warning",
                    'msg' => "El RUC es invalido...",
                ];
            }
        } else if ($clieTipoDocumento == 2) { //SI ES CEDULA
            if (strlen($clieCiruc) != 10) {
                return [
                    'status' => "warning",
                    'msg' => "El número de cédula es invalido, el tamaño de caracteres es invalido",
                ];
            }
            $valida = $this->docIdent->validarCedula(trim($clieCiruc));
            if ($valida == false) {
                return[
                    'status' => "warning",
                    'msg' => "El número de cédula es invalido...",
                ];
            }
        }
    }
}
