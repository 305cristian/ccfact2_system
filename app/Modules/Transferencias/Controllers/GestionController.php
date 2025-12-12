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

        $bodegaMainUsuario = $this->ccm->getValue('cc_bodegas', $this->user->id, 'id', 'id');
        $data['userSession']= $this->user->id;
        $data['rootUser']= $this->user->root;
        $data['bodegaId'] = $this->session->get('bodegaIdAje') ? $this->session->get('bodegaIdAje') : $bodegaMainUsuario;
        $send['view'] = view($this->dirViewModule . '\viewGestionTransferencias', $data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function searchTransferencias() {
        $this->user->validateSession();
        $dataPost = json_decode(file_get_contents('php://input'));

        $filtros = [
            'trbSecuencial' => $dataPost->trbSecuencial ?? null,
            'trbBodegaOrigen' => $dataPost->trbBodegaOrigen ?? null,
            'trbBodegaDestino' => $dataPost->trbBodegaDestino ?? null,
            'trbEstado' => $dataPost->trbEstado ?? null,
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
}
