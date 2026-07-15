<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Transferencias\Controllers;

use App\Controllers\BaseController;
use App\Models\CcModel;
use Modules\Transferencias\Models\TransferenciasModel;
use function view;

/**
 * Description of DashboardController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 15 jul 2026
 * @time 2:46:29 p.m.
 */
class DashboardController extends BaseController {

    //put your code here

    protected string $dirViewModule;
    protected $gm;
    protected TransferenciasModel $transferModel;

    public function __construct() {

        $this->dirViewModule = 'Modules\Transferencias\Views';
        $this->gm = new CcModel();
        $this->transferModel = new TransferenciasModel();
    }

    public function index() {

        $this->user->validateSession();
        $fechaDesde = date('Y-m-01');
        $fechaHasta = date('Y-m-d');
        $filtros = $this->getFiltrosDashboard((object) [
                    'fechaDesde' => $fechaDesde,
                    'fechaHasta' => $fechaHasta,
        ]);

        $data['title'] = "Dashboard Transferencias";
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['fechaDesdeDashboard'] = $fechaDesde;
        $data['fechaHastaDashboard'] = $fechaHasta;
        $data['dashboardResumen'] = $this->transferModel->getDashboardResumen($filtros);
        $data['dashboardEstados'] = $this->transferModel->getDashboardEstados($filtros);
        $data['dashboardBodegasOrigen'] = $this->transferModel->getDashboardBodegasOrigen($filtros);
        $data['dashboardBodegasDestino'] = $this->transferModel->getDashboardBodegasDestino($filtros);
        $data['dashboardTendenciaMensual'] = $this->transferModel->getDashboardTendenciaMensual($filtros);
        $data['dashboardUsuariosConfirmacion'] = $this->transferModel->getDashboardUsuariosConfirmacion($filtros);
        $data['dashboardRutas'] = $this->transferModel->getDashboardRutas($filtros);
        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaUsuarios'] = $this->ccm->getData('cc_empleados', ['emp_estado' => 1], 'id, CONCAT(emp_nombre, " ", emp_apellido) empleado');

        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewDashboard', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
    }

    public function getDataDashboard() {

        $this->user->validateSession();
        $dataPost = json_decode(file_get_contents('php://input')) ?? (object) [];
        $filtros = $this->getFiltrosDashboard($dataPost);

        return $this->responseSetJSON('success', 'Dashboard cargado correctamente.', [
                    'resumen' => $this->transferModel->getDashboardResumen($filtros),
                    'estados' => $this->transferModel->getDashboardEstados($filtros),
                    'bodegasOrigen' => $this->transferModel->getDashboardBodegasOrigen($filtros),
                    'bodegasDestino' => $this->transferModel->getDashboardBodegasDestino($filtros),
                    'tendenciaMensual' => $this->transferModel->getDashboardTendenciaMensual($filtros),
                    'usuariosConfirmacion' => $this->transferModel->getDashboardUsuariosConfirmacion($filtros),
                    'rutas' => $this->transferModel->getDashboardRutas($filtros),
        ]);
    }

    private function getFiltrosDashboard(object $dataPost): array {

        return [
            'fechaDesde' => $dataPost->fechaDesde ?? date('Y-m-01'),
            'fechaHasta' => $dataPost->fechaHasta ?? date('Y-m-d'),
            'bodegaOrigenId' => $dataPost->bodegaOrigenId ?? null,
            'bodegaDestinoId' => $dataPost->bodegaDestinoId ?? null,
            'usuarioConfirmarId' => $dataPost->usuarioConfirmarId ?? null,
        ];
    }

    public function responseSetJSON(string $status, string $mensaje, mixed $data = null) {

        return $this->response->setJSON([
                    'status' => $status,
                    'msg' => $mensaje,
                    'data' => $data,
        ]);
    }
}
