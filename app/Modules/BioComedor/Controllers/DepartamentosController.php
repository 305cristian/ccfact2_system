<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\BioComedor\Controllers;

use App\Controllers\BaseController;

/**
 * Description of DepartamentosController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 24 jul 2026
 * @time 12:34:34 a.m.
 */
class DepartamentosController extends BaseController {
    //put your code here

    protected string $dirViewModule;

    public function __construct() {

        $this->dirViewModule = 'Modules\BioComedor\Views';
    }

    public function index() {

        $this->user->validateSession();

        $data['title'] = "Departamentos";
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;

        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewDepartamentos', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
    }

    public function getDepartamentos() {

        $this->user->validateSession();
        $response = $this->ccm->getData('cc_bio_departamentos');

        return $this->response->setJSON($response ?: false);
    }

    public function saveDepartamento() {

        $this->user->validateSession();
        $dataDepartamento = $this->getDataDepartamentoPost();

        $validacion = $this->validarDatosDepartamento();
        if ($validacion !== null) {
            return $this->response->setJSON($validacion);
        }

        $existeCodigo = $this->ccm->getData('cc_bio_departamentos', ['dep_codigo' => $dataDepartamento['dep_codigo']], 'id', null, 1);

        if ($existeCodigo) {
            return $this->response->setJSON([
                        'status' => 'existe',
                        'msg' => '<h5>Ya existe un departamento registrado con el codigo ' . $dataDepartamento['dep_codigo'] . '</h5>',
            ]);
        }

        $departamentoId = $this->ccm->guardar($dataDepartamento, 'cc_bio_departamentos');
        $this->logs->logSuccess('SE HA CREADO UN DEPARTAMENTO BIO COMEDOR CON EL ID ' . $departamentoId);

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => '<h5>Departamento registrado exitosamente</h5>',
        ]);
    }

    public function updateDepartamento() {

        $this->user->validateSession();

        $idDepartamento = $this->request->getPost('idDepartamento');
        $dataDepartamento = $this->getDataDepartamentoPost();

        $validacion = $this->validarDatosDepartamento();
        if ($validacion !== null) {
            return $this->response->setJSON($validacion);
        }

        $existeCodigo = $this->ccm->getData('cc_bio_departamentos', ['dep_codigo' => $dataDepartamento['dep_codigo']], 'id', null, 1);

        if ($existeCodigo && (int) $existeCodigo->id !== (int) $idDepartamento) {
            return $this->response->setJSON([
                        'status' => 'existe',
                        'msg' => '<h5>Ya existe un departamento registrado con el codigo ' . $dataDepartamento['dep_codigo'] . '</h5>',
            ]);
        }

        $this->ccm->actualizar('cc_bio_departamentos', $dataDepartamento, ['id' => $idDepartamento]);

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => '<h5>Departamento actualizado exitosamente</h5>',
        ]);
    }

    private function validarDatosDepartamento(): ?array {

        $this->validation->setRules([
            'depCodigo' => ['label' => 'Codigo', 'rules' => 'trim|required'],
            'depNombre' => ['label' => 'Nombre', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {
            return null;
        }

        return [
            'status' => 'vacio',
            'msg' => [
                'depCodigo' => $this->validation->getError('depCodigo'),
                'depNombre' => $this->validation->getError('depNombre'),
            ],
        ];
    }

    private function getDataDepartamentoPost(): array {

        $depCodigo = trim((string) $this->request->getPost('depCodigo'));
        $depNombre = trim((string) $this->request->getPost('depNombre'));
        $depDescripcion = trim((string) $this->request->getPost('depDescripcion'));

        return [
            'dep_codigo' => mb_strtoupper($depCodigo, 'UTF-8'),
            'dep_nombre' => mb_strtoupper($depNombre, 'UTF-8'),
            'dep_descripcion' => $depDescripcion !== '' ? $depDescripcion : null,
            'dep_estado' => $this->request->getPost('depEstado') ?? 1,
        ];
    }
}
