<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\AjustesEntrada\Controllers;

use App\Controllers\BaseController;
use App\Models\CcModel;
use Modules\AjustesEntrada\Models\EntradasModel;
use function view;

/**
 * Description of DashboardController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 15 jul 2026
 * @time 12:41:35 p.m.
 */
class DashboardController extends BaseController {

    //put your code here

    protected $gm;
    protected string $dirViewModule;
    protected EntradasModel $entradasModel;

    public function __construct() {
        
        $this->dirViewModule = 'Modules\AjustesEntrada\Views';
        $this->gm = new CcModel();
        $this->entradasModel = new EntradasModel();
    }

    public function index() {

        $this->user->validateSession();
        $fechaDesde = date('Y-m-01');
        $fechaHasta = date('Y-m-d');
        $filtros = $this->getFiltrosDashboard((object) [
                    'fechaDesde' => $fechaDesde,
                    'fechaHasta' => $fechaHasta,
                    'estado' => 2,
                    'tipo' => 'AJUSTE_NORMAL',
        ]);

        $data['title'] = "Dashboard Entradas";
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['fechaDesdeDashboard'] = $fechaDesde;
        $data['fechaHastaDashboard'] = $fechaHasta;
        $data['dashboardResumen'] = $this->entradasModel->getDashboardResumen($filtros);
        $data['dashboardEstados'] = $this->entradasModel->getDashboardEstados($filtros);
        $data['dashboardMotivos'] = $this->entradasModel->getDashboardMotivos($filtros);
        $data['dashboardBodegas'] = $this->entradasModel->getDashboardBodegas($filtros);
        $data['dashboardTendenciaMensual'] = $this->entradasModel->getDashboardTendenciaMensual($filtros);
        $data['dashboardCentrosCosto'] = $this->entradasModel->getDashboardCentrosCosto($filtros);
        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaMotivos'] = $this->ccm->getData('cc_motivos_ajuste', ['mot_estado' => 1], 'id, mot_nombre');
        $data['listaCentroCostos'] = $this->ccm->getData('cc_centroscosto', ['cc_estado' => 1], 'id, cc_nombre');
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewDashboard', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
    }

    public function getDataDashboard() {

        $this->user->validateSession();
        $dataPost = json_decode(file_get_contents('php://input')) ?? (object) [];
        $filtros = $this->getFiltrosDashboard($dataPost);

        return $this->responseSetJSON('success', 'Dashboard cargado correctamente.', [
                    'resumen' => $this->entradasModel->getDashboardResumen($filtros),
                    'estados' => $this->entradasModel->getDashboardEstados($filtros),
                    'motivos' => $this->entradasModel->getDashboardMotivos($filtros),
                    'bodegas' => $this->entradasModel->getDashboardBodegas($filtros),
                    'tendenciaMensual' => $this->entradasModel->getDashboardTendenciaMensual($filtros),
                    'centrosCosto' => $this->entradasModel->getDashboardCentrosCosto($filtros),
        ]);
    }

    private function getFiltrosDashboard(object $dataPost): array {

        return [
            'fechaDesde' => $dataPost->fechaDesde ?? date('Y-m-01'),
            'fechaHasta' => $dataPost->fechaHasta ?? date('Y-m-d'),
            'bodegaId' => $dataPost->bodegaId ?? null,
            'motivoId' => $dataPost->motivoId ?? null,
            'centroCostoId' => $dataPost->centroCostoId ?? null,
            'estado' => $dataPost->estado ?? null,
            'tipo' => $dataPost->tipo ?? null,
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
