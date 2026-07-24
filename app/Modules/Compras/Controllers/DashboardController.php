<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Compras\Controllers;

use App\Controllers\BaseController;
use App\Models\CcModel;
use Modules\Compras\Models\ComprasModel;

/**
 * Description of DashboardController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 15 jul 2026
 * @time 11:15:58 a.m.
 */
class DashboardController extends BaseController{
    //put your code here
    
    protected string $dirViewModule;
    protected ComprasModel $comprasModel;
    
    public function __construct() {
        
        $this->dirViewModule = 'Modules\Compras\Views';
        $this->comprasModel = new ComprasModel();
    }

    public function index() {

        $this->user->validateSession();

        $fechaDesde = date('Y-m-01');
        $fechaHasta = date('Y-m-d');
        $filtros = $this->getFiltrosDashboard((object) [
                    'fechaDesde' => $fechaDesde,
                    'fechaHasta' => $fechaHasta,
        ]);

        $data['title'] = "Compras";
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['fechaDesdeDashboard'] = $fechaDesde;
        $data['fechaHastaDashboard'] = $fechaHasta;
        $data['dashboardResumen'] = $this->comprasModel->getDashboardResumen($filtros);
        $data['dashboardEstados'] = $this->comprasModel->getDashboardEstados($filtros);
        $data['dashboardComprobantes'] = $this->comprasModel->getDashboardComprobantes($filtros);
        $data['dashboardTopProveedores'] = $this->comprasModel->getDashboardTopProveedores($filtros);
        $data['dashboardTendenciaMensual'] = $this->comprasModel->getDashboardTendenciaMensual($filtros);
        $data['dashboardBodegas'] = $this->comprasModel->getDashboardBodegas($filtros);
        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaCentroCostos'] = $this->ccm->getData('cc_centroscosto', ['cc_estado' => 1], 'id, cc_nombre');
        $tiposComprobantes = $this->ccm->getData('cc_tipos_comprobante', ['comp_estado' => 1], 'id, comp_codigo, comp_nombre');
        $data['listaTiposComprobantes'] = array_values(array_filter($tiposComprobantes, static fn($comprobante) => in_array((string) $comprobante->comp_codigo, ['01', '02', '03'], true)));

        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewDashboard', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
    }

    public function getDataDashboard() {

        $this->user->validateSession();
        $dataPost = json_decode(file_get_contents('php://input')) ?? (object) [];
        $filtros = $this->getFiltrosDashboard($dataPost);

        return $this->responseSetJSON('success', 'Dashboard cargado correctamente.', [
                    'resumen' => $this->comprasModel->getDashboardResumen($filtros),
                    'estados' => $this->comprasModel->getDashboardEstados($filtros),
                    'comprobantes' => $this->comprasModel->getDashboardComprobantes($filtros),
                    'proveedores' => $this->comprasModel->getDashboardTopProveedores($filtros),
                    'tendenciaMensual' => $this->comprasModel->getDashboardTendenciaMensual($filtros),
                    'bodegas' => $this->comprasModel->getDashboardBodegas($filtros),
        ]);
    }

    private function getFiltrosDashboard(object $dataPost): array {

        return [
            'fechaDesde' => $dataPost->fechaDesde ?? date('Y-m-01'),
            'fechaHasta' => $dataPost->fechaHasta ?? date('Y-m-d'),
            'proveedorId' => $dataPost->proveedorId ?? null,
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
