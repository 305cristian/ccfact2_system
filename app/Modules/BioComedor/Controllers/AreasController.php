<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\BioComedor\Controllers;

use App\Controllers\BaseController;
use Modules\BioComedor\Models\BioModel;

/**
 * Description of AreasController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 24 jul 2026
 * @time 12:35:13 a.m.
 */
class AreasController extends BaseController {
    //put your code here

    protected string $dirViewModule;
    protected BioModel $bioModel;

    public function __construct() {

        $this->dirViewModule = 'Modules\BioComedor\Views';
        $this->bioModel = new BioModel();
    }

    public function index() {

        $this->user->validateSession();

        $data['title'] = "Areas";
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;
        $data['listaDepartamentos'] = $this->bioModel->getListaDepartamentosActivos();

        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewAreas', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
    }

    public function getAreas() {

        $this->user->validateSession();
        $response = $this->bioModel->getListaAreas();

        return $this->response->setJSON($response ?: false);
    }

    public function saveArea() {

        $this->user->validateSession();
        $dataArea = $this->getDataAreaPost();

        $validacion = $this->validarDatosArea();
        if ($validacion !== null) {
            return $this->response->setJSON($validacion);
        }

        $existeCodigo = $this->ccm->getData('cc_bio_areas', ['area_codigo' => $dataArea['area_codigo']], 'id', null, 1);

        if ($existeCodigo) {
            return $this->response->setJSON([
                        'status' => 'existe',
                        'msg' => '<h5>Ya existe un area registrada con el codigo ' . $dataArea['area_codigo'] . '</h5>',
            ]);
        }

        $areaId = $this->ccm->guardar($dataArea, 'cc_bio_areas');
        $this->logs->logSuccess('SE HA CREADO UN AREA BIO COMEDOR CON EL ID ' . $areaId);

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => '<h5>Area registrada exitosamente</h5>',
        ]);
    }

    public function updateArea() {

        $this->user->validateSession();

        $idArea = $this->request->getPost('idArea');
        $dataArea = $this->getDataAreaPost();

        $validacion = $this->validarDatosArea();
        if ($validacion !== null) {
            return $this->response->setJSON($validacion);
        }

        $existeCodigo = $this->ccm->getData('cc_bio_areas', ['area_codigo' => $dataArea['area_codigo']], 'id', null, 1);

        if ($existeCodigo && (int) $existeCodigo->id !== (int) $idArea) {
            return $this->response->setJSON([
                        'status' => 'existe',
                        'msg' => '<h5>Ya existe un area registrada con el codigo ' . $dataArea['area_codigo'] . '</h5>',
            ]);
        }

        $this->ccm->actualizar('cc_bio_areas', $dataArea, ['id' => $idArea]);

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => '<h5>Area actualizada exitosamente</h5>',
        ]);
    }

    private function validarDatosArea(): ?array {

        $this->validation->setRules([
            'fkDepartamento' => ['label' => 'Departamento', 'rules' => 'trim|required'],
            'areaCodigo' => ['label' => 'Codigo', 'rules' => 'trim|required'],
            'areaNombre' => ['label' => 'Nombre', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {
            return null;
        }

        return [
            'status' => 'vacio',
            'msg' => [
                'fkDepartamento' => $this->validation->getError('fkDepartamento'),
                'areaCodigo' => $this->validation->getError('areaCodigo'),
                'areaNombre' => $this->validation->getError('areaNombre'),
            ],
        ];
    }

    private function getDataAreaPost(): array {

        $areaCodigo = trim((string) $this->request->getPost('areaCodigo'));
        $areaNombre = trim((string) $this->request->getPost('areaNombre'));
        $areaDescripcion = trim((string) $this->request->getPost('areaDescripcion'));

        return [
            'fk_departamento' => $this->request->getPost('fkDepartamento'),
            'area_codigo' => mb_strtoupper($areaCodigo, 'UTF-8'),
            'area_nombre' => mb_strtoupper($areaNombre, 'UTF-8'),
            'area_descripcion' => $areaDescripcion !== '' ? $areaDescripcion : null,
            'area_estado' => $this->request->getPost('areaEstado') ?? 1,
        ];
    }
}
