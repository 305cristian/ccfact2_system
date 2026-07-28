<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\BioComedor\Controllers;

use App\Controllers\BaseController;
use Modules\BioComedor\Models\BioModel;

/**
 * Description of DashboardController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 23 jul 2026
 * @time 7:32:52 p.m.
 */
class DashboardController extends BaseController {
    //put your code here

    protected string $dirViewModule;
    protected BioModel $bioModel;

    public function __construct() {

        $this->dirViewModule = 'Modules\BioComedor\Views';
        $this->bioModel = new BioModel();
    }

    public function index() {

        $this->user->validateSession();

        $data['title'] = "Bio Comedor";
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);

        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewDashboard', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
    }

    public function reportes() {

        $this->user->validateSession();

        $data['title'] = "Reportes Bio Comedor";
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;
        $data['listaComedores'] = $this->ccm->getData('cc_bio_comedores', ['com_estado' => 1, 'fk_proyecto_sistema' => getProyectoId()], 'id, com_codigo, com_nombre');
        $data['listaServicios'] = $this->ccm->getData('cc_bio_servicios', ['serv_estado' => 1], 'id, serv_codigo, serv_nombre');
        $data['listaContratistas'] = $this->ccm->getData('cc_bio_contratistas', ['cont_estado' => 1], 'id, cont_ruc, cont_nombre');
        $data['listaProyectos'] = $this->ccm->getData('cc_bio_proyectos', ['proy_estado' => 1], 'id, proy_codigo, proy_nombre');

        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewReportes', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
    }

    public function getReportes() {

        $this->user->validateSession();

        $filtros = [
            'fechaDesde' => $this->request->getPost('fechaDesde'),
            'fechaHasta' => $this->request->getPost('fechaHasta'),
            'fkComedor' => $this->request->getPost('fkComedor'),
            'fkServicio' => $this->request->getPost('fkServicio'),
            'fkContratista' => $this->request->getPost('fkContratista'),
            'fkProyecto' => $this->request->getPost('fkProyecto'),
            'marcEstado' => $this->request->getPost('marcEstado'),
            'marcRetraso' => $this->request->getPost('marcRetraso'),
        ];

        return $this->response->setJSON([
                    'status' => 'success',
                    'data' => [
                        'resumen' => $this->bioModel->getReporteMarcacionesResumen($filtros),
                        'porServicio' => $this->bioModel->getReportePorServicio($filtros),
                        'porComedor' => $this->bioModel->getReportePorComedor($filtros),
                        'porContratista' => $this->bioModel->getReportePorContratista($filtros),
                        'porProyecto' => $this->bioModel->getReportePorProyecto($filtros),
                        'porFecha' => $this->bioModel->getReportePorFecha($filtros),
                        'detalle' => $this->bioModel->getListaMarcaciones($filtros),
                    ],
        ]);
    }

}
