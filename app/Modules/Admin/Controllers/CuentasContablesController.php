<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of CuentasContablesController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 2 oct 2025
 * @time 9:23:41 a.m.
 */
use Modules\Admin\Models\CuentasContablesModel;

class CuentasContablesController extends \App\Controllers\BaseController {

    //put your code here
    protected $ctaModel;

    public function __construct() {

        $this->dirViewModule = 'Modules\Admin\Views';

        $this->ctaModel = new CuentasContablesModel();
    }

    public function index() {
        $this->user->validateSession();
        $mod['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user; //Esto se envia a la vista para validar roles y permisos
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $mod);
        $send['view'] = view($this->dirViewModule . '\cuentascontables\viewCuentascontables', $data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function getCuentasContables() {

        $response = $this->ctaModel->getCuentasContables();

        if ($response) {
            return $this->response->setJSON($response);
        }
    }

    public function searchCuentasContables() {
        $data = json_decode(file_get_contents("php://input"));
        if ($data->dataSerach) {
            $response = $this->ctaModel->searchCuentasContables($data->dataSerach);
            if ($response) {
                return $this->response->setJSON($response);
            }
        }
        return $this->response->setJSON(false);
    }

    public function getCuentas() {
        $response = $this->ccm->getData('cc_cuenta_contable');
        return $this->response->setJSON($response ?: false);
    }

    public function saveCuenta() {

        $ctadCodigo = $this->request->getPost('ctadCodigo');
        $ctadNombreCuenta = $this->request->getPost('ctadNombreCuenta');
        $ctadCuentaPadre = $this->request->getPost('ctadCuentaPadre');
        $fkCtaContable = $this->request->getPost('fkCtaContable');
        $ctadEstado = $this->request->getPost('ctadEstado');

        $this->validation->setRules([
            'ctadCodigo' => ['label' => 'Código', 'rules' => 'trim|required'],
            'ctadNombreCuenta' => ['label' => 'Nombre Cuenta', 'rules' => 'trim|required'],
            'fkCtaContable' => ['label' => 'Cuenta', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_cuenta_contabledet', ['ctad_codigo' => $ctadCodigo]);
            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe una cuenta con el código  ' . $ctadCodigo . '</h5>';
                return $this->response->setJSON($response);
            }

            $datos = [
                'ctad_codigo' => $ctadCodigo,
                'ctad_nombre_cuenta' => mb_strtoupper($ctadNombreCuenta),
                'ctad_cuenta_padre' => $ctadCuentaPadre,
                'fk_cta_contable' => $fkCtaContable,
                'ctad_estado' => $ctadEstado,
            ];

            $save = $this->ccm->guardar($datos, 'cc_cuenta_contabledet');
            $this->logs->logSuccess('SE HA REGISTRADO UNA CUENTA CONTABLE CON EL ID ' . $save);

            $response['status'] = 'success';
            $response['msg'] = '<h5>Cuenta contable registrada exitosamente</h5>';
        } else {

            $response['status'] = 'vacio';
            $response['msg'] = [
                'ctadCodigo' => $this->validation->getError('ctadCodigo'),
                'ctadNombreCuenta' => $this->validation->getError('ctadNombreCuenta'),
                'fkCtaContable' => $this->validation->getError('fkCtaContable'),
            ];
        }
        return $this->response->setJSON($response);
    }

    public function updateCuenta() {

        $ctadCodigo = $this->request->getPost('ctadCodigo');
        $ctadNombreCuenta = $this->request->getPost('ctadNombreCuenta');
        $ctadCuentaPadre = $this->request->getPost('ctadCuentaPadre');
        $fkCtaContable = $this->request->getPost('fkCtaContable');
        $ctadEstado = $this->request->getPost('ctadEstado');

        $id = $this->request->getPost('idCta');
        $codeAux = $this->request->getPost('codeAux');

        $this->validation->setRules([
            'ctadCodigo' => ['label' => 'Código', 'rules' => 'trim|required'],
            'ctadNombreCuenta' => ['label' => 'Nombre Cuenta', 'rules' => 'trim|required'],
            'fkCtaContable' => ['label' => 'Cuenta', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_cuenta_contabledet', ['ctad_codigo' => $ctadCodigo], 'ctad_codigo', $orderBy = null, 1);

            if ($existe && $existe->ctad_codigo != $codeAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe una cuenta con el código ' . $ctadCodigo . '</h5>';
                return $this->response->setJSON($response);
            }

            $datos = [
                'ctad_codigo' => $ctadCodigo,
                'ctad_nombre_cuenta' => mb_strtoupper($ctadNombreCuenta),
                'ctad_cuenta_padre' => $ctadCuentaPadre,
                'fk_cta_contable' => $fkCtaContable,
                'ctad_estado' => $ctadEstado,
            ];

            $this->ccm->actualizar('cc_cuenta_contabledet', $datos, ['ctad_codigo' => $id]);
            $this->logs->logSuccess('SE HA ACTUALIZADO UNA CUENTA CONTABLE CON EL CÓDIGO ' . $id);

            $response['status'] = 'success';
            $response['msg'] = '<h5>Cuenta actualizada exitosamente</h5>';
        } else {

            $response['status'] = 'vacio';
            $response['msg'] = [
                'ctadCodigo' => $this->validation->getError('ctadCodigo'),
                'ctadNombreCuenta' => $this->validation->getError('ctadNombreCuenta'),
                'fkCtaContable' => $this->validation->getError('fkCtaContable'),
            ];
        }

        return $this->response->setJSON($response);
    }
}
