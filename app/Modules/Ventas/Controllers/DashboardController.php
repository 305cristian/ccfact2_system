<?php

namespace Modules\Ventas\Controllers;

use App\Controllers\BaseController;
use Modules\Ventas\Models\VentasModel;

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of DashboardController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 9 ago 2026
 * @time 8:26:02 a.m.
 */
class DashboardController extends BaseController {

    //put your code here
    protected $dirViewModule;
    protected VentasModel $ventasModel;

    public function __construct() {
        $this->dirViewModule = 'Modules\Ventas\Views';
        $this->ventasModel = new VentasModel();
    }

    public function index() {

        $this->user->validateSession();

        $fechaDesde = date('Y-m-01');
        $fechaHasta = date('Y-m-d');
        $filtros = $this->getFiltrosDashboard((object) [
                    'fechaDesde' => $fechaDesde,
                    'fechaHasta' => $fechaHasta,
        ]);

        $data['title'] = "Dashboard Ventas";
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['fechaDesdeDashboard'] = $fechaDesde;
        $data['fechaHastaDashboard'] = $fechaHasta;
        $data['dashboardResumen'] = $this->ventasModel->getDashboardResumen($filtros);
        $data['dashboardEstados'] = $this->ventasModel->getDashboardEstados($filtros);
        $data['dashboardComprobantes'] = $this->ventasModel->getDashboardComprobantes($filtros);
        $data['dashboardTopClientes'] = $this->ventasModel->getDashboardTopClientes($filtros);
        $data['dashboardTendenciaMensual'] = $this->ventasModel->getDashboardTendenciaMensual($filtros);
        $data['dashboardBodegas'] = $this->ventasModel->getDashboardBodegas($filtros);
        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaCentroCostos'] = $this->ccm->getData('cc_centroscosto', ['cc_estado' => 1], 'id, cc_nombre');
        $data['listaTiposComprobantes'] = array_values(array_filter(
                        $this->ccm->getData('cc_tipos_comprobante', ['comp_estado' => 1], 'id, comp_codigo, comp_nombre'),
                        static fn($comprobante) => in_array((string) $comprobante->comp_codigo, ['01', '02'], true)
        ));
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewDashboard', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
    }

    public function getDataDashboard() {

        $this->user->validateSession();
        $dataPost = json_decode(file_get_contents('php://input')) ?? (object) [];
        $filtros = $this->getFiltrosDashboard($dataPost);

        return $this->responseSetJSON('success', 'Dashboard cargado correctamente.', [
                    'resumen' => $this->ventasModel->getDashboardResumen($filtros),
                    'estados' => $this->ventasModel->getDashboardEstados($filtros),
                    'comprobantes' => $this->ventasModel->getDashboardComprobantes($filtros),
                    'clientes' => $this->ventasModel->getDashboardTopClientes($filtros),
                    'tendenciaMensual' => $this->ventasModel->getDashboardTendenciaMensual($filtros),
                    'bodegas' => $this->ventasModel->getDashboardBodegas($filtros),
        ]);
    }

    private function getFiltrosDashboard(object $dataPost): array {

        return [
            'fechaDesde' => $dataPost->fechaDesde ?? date('Y-m-01'),
            'fechaHasta' => $dataPost->fechaHasta ?? date('Y-m-d'),
            'clienteId' => $dataPost->clienteId ?? null,
            'bodegaId' => $dataPost->bodegaId ?? null,
            'centroCostoId' => $dataPost->centroCostoId ?? null,
            'tipoComprobante' => $dataPost->tipoComprobante ?? null,
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
