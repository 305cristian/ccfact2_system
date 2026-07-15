<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\AjustesSalida\Controllers;

use App\Controllers\BaseController;
use App\Models\CcModel;
use Modules\AjustesSalida\Models\SalidasModel;
use function view;

/**
 * Description of DashboardController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 15 jul 2026
 * @time 2:33:39 p.m.
 */
class DashboardController  extends BaseController{
    //put your code here

    protected string $dirViewModule;
    protected $gm;
    protected SalidasModel $salidasModel;

    public function __construct() {

        $this->dirViewModule = 'Modules\AjustesSalida\Views';
        $this->gm = new CcModel();
        $this->salidasModel = new SalidasModel();
    }

    public function index() {

        $this->user->validateSession();
        $fechaDesde = date('Y-m-01');
        $fechaHasta = date('Y-m-d');
        $filtros = $this->getFiltrosDashboard((object) [
                    'fechaDesde' => $fechaDesde,
                    'fechaHasta' => $fechaHasta,
        ]);

        $data['title'] = "Dashboard Salidas";
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['fechaDesdeDashboard'] = $fechaDesde;
        $data['fechaHastaDashboard'] = $fechaHasta;
        $data['dashboardResumen'] = $this->salidasModel->getDashboardResumen($filtros);
        $data['dashboardEstados'] = $this->salidasModel->getDashboardEstados($filtros);
        $data['dashboardMotivos'] = $this->salidasModel->getDashboardMotivos($filtros);
        $data['dashboardBodegas'] = $this->salidasModel->getDashboardBodegas($filtros);
        $data['dashboardTendenciaMensual'] = $this->salidasModel->getDashboardTendenciaMensual($filtros);
        $data['dashboardCentrosCosto'] = $this->salidasModel->getDashboardCentrosCosto($filtros);
        $data['dashboardServicios'] = $this->salidasModel->getDashboardServicios($filtros);
        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaMotivos'] = $this->ccm->getData('cc_motivos_ajuste', ['mot_estado' => 1, 'mot_tipo !=' => 'AJUSTES'], 'id, mot_nombre, CONCAT(mot_nombre, " ( ", mot_tipo," )") motivo');
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
                    'resumen' => $this->salidasModel->getDashboardResumen($filtros),
                    'estados' => $this->salidasModel->getDashboardEstados($filtros),
                    'motivos' => $this->salidasModel->getDashboardMotivos($filtros),
                    'bodegas' => $this->salidasModel->getDashboardBodegas($filtros),
                    'tendenciaMensual' => $this->salidasModel->getDashboardTendenciaMensual($filtros),
                    'centrosCosto' => $this->salidasModel->getDashboardCentrosCosto($filtros),
                    'servicios' => $this->salidasModel->getDashboardServicios($filtros),
        ]);
    }

    private function getFiltrosDashboard(object $dataPost): array {

        return [
            'fechaDesde' => $dataPost->fechaDesde ?? date('Y-m-01'),
            'fechaHasta' => $dataPost->fechaHasta ?? date('Y-m-d'),
            'bodegaId' => $dataPost->bodegaId ?? null,
            'motivoId' => $dataPost->motivoId ?? null,
            'centroCostoId' => $dataPost->centroCostoId ?? null,
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
