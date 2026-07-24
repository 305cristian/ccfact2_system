<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\BioComedor\Controllers;

use App\Controllers\BaseController;

/**
 * Description of ContratistasController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 23 jul 2026
 * @time 11:13:29 p.m.
 */
class ContratistasController extends BaseController {
    //put your code here

    protected string $dirViewModule;

    public function __construct() {

        $this->dirViewModule = 'Modules\BioComedor\Views';
    }

    public function index() {

        $this->user->validateSession();

        $data['title'] = "Contratistas";
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;

        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewContratistas', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
    }

    public function getContratistas() {

        $this->user->validateSession();
        $response = $this->ccm->getData('cc_bio_contratistas');

        return $this->response->setJSON($response ?: false);
    }

    public function saveContratista() {

        $this->user->validateSession();
        $dataContratista = $this->getDataContratistaPost();

        $this->validation->setRules([
            'contRuc' => ['label' => 'RUC', 'rules' => 'trim|required'],
            'contNombre' => ['label' => 'Nombre', 'rules' => 'trim|required'],
            'contEmail' => ['label' => 'Email', 'rules' => 'trim|permit_empty|valid_email'],
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                        'status' => 'vacio',
                        'msg' => [
                            'contRuc' => $this->validation->getError('contRuc'),
                            'contNombre' => $this->validation->getError('contNombre'),
                            'contEmail' => $this->validation->getError('contEmail'),
                        ],
            ]);
        }

        $existeRuc = $this->ccm->getData('cc_bio_contratistas', ['cont_ruc' => $dataContratista['cont_ruc']], 'id', null, 1);

        if ($existeRuc) {
            return $this->response->setJSON([
                        'status' => 'existe',
                        'msg' => '<h5>Ya existe una contratista registrada con el RUC ' . $dataContratista['cont_ruc'] . '</h5>',
            ]);
        }

        $contratistaId = $this->ccm->guardar($dataContratista, 'cc_bio_contratistas');
        $this->logs->logSuccess('SE HA CREADO UNA CONTRATISTA CON EL ID ' . $contratistaId);

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => '<h5>Contratista registrada exitosamente</h5>',
        ]);
    }

    public function updateContratista() {

        $this->user->validateSession();

        $idContratista = $this->request->getPost('idContratista');
        $dataContratista = $this->getDataContratistaPost();

        $this->validation->setRules([
            'contRuc' => ['label' => 'RUC', 'rules' => 'trim|required'],
            'contNombre' => ['label' => 'Nombre', 'rules' => 'trim|required'],
            'contEmail' => ['label' => 'Email', 'rules' => 'trim|permit_empty|valid_email'],
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                        'status' => 'vacio',
                        'msg' => [
                            'contRuc' => $this->validation->getError('contRuc'),
                            'contNombre' => $this->validation->getError('contNombre'),
                            'contEmail' => $this->validation->getError('contEmail'),
                        ],
            ]);
        }

        $existeRuc = $this->ccm->getData('cc_bio_contratistas', ['cont_ruc' => $dataContratista['cont_ruc']], 'id, cont_ruc', null, 1);

        if ($existeRuc && (int) $existeRuc->id !== (int) $idContratista) {
            return $this->response->setJSON([
                        'status' => 'existe',
                        'msg' => '<h5>Ya existe una contratista registrada con el RUC ' . $dataContratista['cont_ruc'] . '</h5>',
            ]);
        }

        $this->ccm->actualizar('cc_bio_contratistas', $dataContratista, ['id' => $idContratista]);

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => '<h5>Contratista actualizada exitosamente</h5>',
        ]);
    }

    private function getDataContratistaPost(): array {

        $contRuc = preg_replace('/\D+/', '', trim((string) $this->request->getPost('contRuc')));
        $contNombre = trim((string) $this->request->getPost('contNombre'));
        $contDireccion = trim((string) $this->request->getPost('contDireccion'));
        $contTelefono = preg_replace('/\D+/', '', trim((string) $this->request->getPost('contTelefono')));
        $contEmail = trim((string) $this->request->getPost('contEmail'));

        return [
            'cont_ruc' => $contRuc,
            'cont_nombre' => mb_strtoupper($contNombre, 'UTF-8'),
            'cont_direccion' => $contDireccion !== '' ? mb_strtoupper($contDireccion, 'UTF-8') : null,
            'cont_telefono' => $contTelefono !== '' ? $contTelefono : null,
            'cont_email' => $contEmail !== '' ? mb_strtolower($contEmail, 'UTF-8') : null,
            'cont_estado' => $this->request->getPost('contEstado') ?? 1,
        ];
    }
}
