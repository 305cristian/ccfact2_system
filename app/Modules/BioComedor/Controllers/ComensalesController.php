<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\BioComedor\Controllers;

use App\Controllers\BaseController;
use Modules\BioComedor\Models\BioModel;

/**
 * Description of ComensalesController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 23 jul 2026
 * @time 11:28:18 p.m.
 */
class ComensalesController extends BaseController {
    //put your code here

    protected string $dirViewModule;
    protected BioModel $bioModel;

    public function __construct() {

        $this->dirViewModule = 'Modules\BioComedor\Views';
        $this->bioModel = new BioModel();
    }

    public function index() {

        $this->user->validateSession();

        $data['title'] = "Comensales";
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;
        $data['listaContratistas'] = $this->ccm->getData('cc_bio_contratistas', ['cont_estado' => 1], 'id, cont_ruc, cont_nombre');
        $data['listaProyectos'] = $this->ccm->getData('cc_bio_proyectos', ['proy_estado' => 1], 'id, proy_codigo, proy_nombre');
        $data['listaDepartamentos'] = $this->bioModel->getListaDepartamentosActivos();
        $data['listaAreas'] = $this->bioModel->getListaAreasActivas();
        $data['codigoComensal'] = $this->bioModel->generarCodigoComensal();

        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewComensales', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
    }

    public function getComensales() {

        $this->user->validateSession();
        $response = $this->bioModel->getListaComensales();

        return $this->response->setJSON($response ?: false);
    }

    public function getCodigoComensal() {

        $this->user->validateSession();

        return $this->response->setJSON([
                    'codigo' => $this->bioModel->generarCodigoComensal(),
        ]);
    }

    public function saveComensal() {

        $this->user->validateSession();

        $dataComensal = $this->getDataComensalPost();

        $validacion = $this->validarDatosComensal();
        if ($validacion !== null) {
            return $this->response->setJSON($validacion);
        }

        $validacionRegistro = $this->validarFormaRegistro($dataComensal);
        if ($validacionRegistro !== null) {
            return $this->response->setJSON($validacionRegistro);
        }

        $duplicado = $this->validarDuplicadosComensal($dataComensal);
        if ($duplicado !== null) {
            return $this->response->setJSON($duplicado);
        }

        $comensalId = $this->ccm->guardar($dataComensal, 'cc_bio_comensales');
        $this->logs->logSuccess('SE HA CREADO UN COMENSAL CON EL ID ' . $comensalId);

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => '<h5>Comensal registrado exitosamente</h5>',
        ]);
    }

    public function updateComensal() {

        $this->user->validateSession();

        $idComensal = $this->request->getPost('idComensal');
        $dataComensal = $this->getDataComensalPost();

        $validacion = $this->validarDatosComensal();
        if ($validacion !== null) {
            return $this->response->setJSON($validacion);
        }

        $validacionRegistro = $this->validarFormaRegistro($dataComensal);
        if ($validacionRegistro !== null) {
            return $this->response->setJSON($validacionRegistro);
        }

        $duplicado = $this->validarDuplicadosComensal($dataComensal, (int) $idComensal);
        if ($duplicado !== null) {
            return $this->response->setJSON($duplicado);
        }

        $this->ccm->actualizar('cc_bio_comensales', $dataComensal, ['id' => $idComensal]);

        return $this->response->setJSON([
                    'status' => 'success',
                    'msg' => '<h5>Comensal actualizado exitosamente</h5>',
        ]);
    }

    private function validarDatosComensal(): ?array {

        $this->validation->setRules([
            'comensCodigo' => ['label' => 'Codigo', 'rules' => 'trim|required'],
            'comensCedula' => ['label' => 'Cedula', 'rules' => 'trim|required'],
            'comensNombres' => ['label' => 'Nombres', 'rules' => 'trim|required'],
            'comensApellidos' => ['label' => 'Apellidos', 'rules' => 'trim|required'],
            'fkDepartamento' => ['label' => 'Departamento', 'rules' => 'trim|required'],
            'fkArea' => ['label' => 'Area', 'rules' => 'trim|required'],
            'fkContratista' => ['label' => 'Contratista', 'rules' => 'trim|required'],
            'fkProyecto' => ['label' => 'Proyecto', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {
            return null;
        }

        return [
            'status' => 'vacio',
            'msg' => [
                'comensCodigo' => $this->validation->getError('comensCodigo'),
                'comensCedula' => $this->validation->getError('comensCedula'),
                'comensNombres' => $this->validation->getError('comensNombres'),
                'comensApellidos' => $this->validation->getError('comensApellidos'),
                'fkDepartamento' => $this->validation->getError('fkDepartamento'),
                'fkArea' => $this->validation->getError('fkArea'),
                'fkContratista' => $this->validation->getError('fkContratista'),
                'fkProyecto' => $this->validation->getError('fkProyecto'),
            ],
        ];
    }

    private function validarFormaRegistro(array $dataComensal): ?array {

        $tieneCodigo = trim((string) ($dataComensal['comens_codigo'] ?? '')) !== '';
        $tieneBiometrico = trim((string) ($dataComensal['comens_identificador_biometrico'] ?? '')) !== '';
        $tieneRfid = trim((string) ($dataComensal['comens_uid_rfid'] ?? '')) !== '';

        if ($tieneCodigo || $tieneBiometrico || $tieneRfid) {
            return null;
        }

        $mensaje = 'Debe ingresar al menos una forma de registro: codigo, identificador biometrico o UID RFID.';

        return [
            'status' => 'vacio',
            'msg' => [
                'comensCodigo' => $mensaje,
                'comensIdentificadorBiometrico' => $mensaje,
                'comensUidRfid' => $mensaje,
            ],
        ];
    }

    private function validarDuplicadosComensal(array $dataComensal, int $idComensal = 0): ?array {

        $campos = [
            'comens_codigo' => 'codigo',
            'comens_cedula' => 'cedula',
            'comens_identificador_biometrico' => 'identificador biometrico',
            'comens_uid_rfid' => 'UID RFID',
        ];

        foreach ($campos as $campo => $label) {
            if (!isset($dataComensal[$campo]) || trim((string) $dataComensal[$campo]) === '') {
                continue;
            }

            $existe = $this->ccm->getData('cc_bio_comensales', [$campo => $dataComensal[$campo]], 'id', null, 1);

            if ($existe && (int) $existe->id !== $idComensal) {
                return [
                    'status' => 'existe',
                    'msg' => '<h5>Ya existe un comensal registrado con el ' . $label . ' ' . $dataComensal[$campo] . '</h5>',
                ];
            }
        }

        return null;
    }

    private function getDataComensalPost(): array {

        $comensCodigo = trim((string) $this->request->getPost('comensCodigo'));
        $comensCedula = preg_replace('/\D+/', '', trim((string) $this->request->getPost('comensCedula')));
        $comensNombres = trim((string) $this->request->getPost('comensNombres'));
        $comensApellidos = trim((string) $this->request->getPost('comensApellidos'));
        $comensIdentificadorBiometrico = trim((string) $this->request->getPost('comensIdentificadorBiometrico'));
        $comensUidRfid = trim((string) $this->request->getPost('comensUidRfid'));

        return [
            'comens_codigo' => $comensCodigo !== '' ? mb_strtoupper($comensCodigo, 'UTF-8') : null,
            'comens_cedula' => $comensCedula,
            'comens_nombres' => mb_strtoupper($comensNombres, 'UTF-8'),
            'comens_apellidos' => mb_strtoupper($comensApellidos, 'UTF-8'),
            'comens_identificador_biometrico' => $comensIdentificadorBiometrico !== '' ? mb_strtoupper($comensIdentificadorBiometrico, 'UTF-8') : null,
            'comens_uid_rfid' => $comensUidRfid !== '' ? mb_strtoupper($comensUidRfid, 'UTF-8') : null,
            'fk_area' => $this->request->getPost('fkArea'),
            'fk_contratista' => $this->request->getPost('fkContratista'),
            'fk_proyecto' => $this->request->getPost('fkProyecto'),
            'comens_estado' => $this->request->getPost('comensEstado') ?? 1,
        ];
    }

}
