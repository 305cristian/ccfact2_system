<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\BioComedor\Controllers;

use App\Controllers\BaseController;
use Modules\BioComedor\Libraries\BioMarcacionesLib;
use Modules\BioComedor\Models\BioModel;

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
