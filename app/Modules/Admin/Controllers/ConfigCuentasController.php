<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of ConfigCuentasController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 2 oct 2025
 * @time 9:46:24 a.m.
 */
use Modules\Admin\Models\CuentasContablesModel;

class ConfigCuentasController extends \App\Controllers\BaseController {

    protected $ctaModel;

    //put your code here

    public function __construct() {

        $this->dirViewModule = 'Modules\Admin\Views';
        $this->ctaModel = new CuentasContablesModel();
    }

    public function index() {
        $this->user->validateSession();
        $mod['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user; //Esto se envia a la vista para validar roles y permisos
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $mod);
        $send['view'] = view($this->dirViewModule . '\cuentasconfig\viewCuentasConfig', $data);
//        $send['user'] = $this->user;//Estos datos se envian solo si se renderiza en el dashboars
//        $send['ccm'] = $this->ccm;//Estos datos se envian solo si se renderiza en el dashboars
//        return view($this->dirTemplate . '\dashboard', $send);
        return $this->response->setJSON($send);
    }

    public function getCuentasConfig() {
        $response = $this->ctaModel->getCuentasConfig();

        if ($response) {
            return $this->response->setJSON($response);
        }
    }

    public function saveConfigCuenta() {
        $ctcfCodigo = $this->request->getPost('ctcfCodigo');
        $ctcfNombre = $this->request->getPost('ctcfNombre');
        $ctcfDetalle = $this->request->getPost('ctcfDetalle');
        $fkCuentaContableDet = $this->request->getPost('fkCuentaContableDet');
        $ctcfEstado = $this->request->getPost('ctcfEstado');

        $this->validation->setRules([
            'ctcfCodigo' => ['label' => 'Código', 'rules' => 'trim|required'],
            'ctcfNombre' => ['label' => 'Nombre', 'rules' => 'trim|required'],
            'ctcfDetalle' => ['label' => 'Detalle', 'rules' => 'trim|required'],
            'fkCuentaContableDet' => ['label' => 'Cuenta Contable Detalle', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {
            $existe = $this->ccm->getData('cc_cuenta_contabledet_config', ['ctcf_codigo' => $ctcfCodigo]);

            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe una configuración con el código ' . $ctcfCodigo . '</h5>';
                return $this->response->setJSON($response);
            }

            $datos = [
                'ctcf_codigo' => $ctcfCodigo,
                'ctcf_nombre' => mb_strtoupper($ctcfNombre, 'UTF-8'),
                'ctcf_detalle' => $ctcfDetalle,
                'fk_cuentacontable_det' => $fkCuentaContableDet,
                'ctcf_estado' => $ctcfEstado,
            ];

            $save = $this->ccm->guardar($datos, 'cc_cuenta_contabledet_config');
            $this->logs->logSuccess('SE HA REGISTRADO UNA CONFIGURACIÓN DE CUENTA CON EL ID ' . $save);

            $response['status'] = 'success';
            $response['msg'] = '<h5>Configuración registrada exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'ctcfCodigo' => $this->validation->getError('ctcfCodigo'),
                'ctcfNombre' => $this->validation->getError('ctcfNombre'),
                'ctcfDetalle' => $this->validation->getError('ctcfDetalle'),
                'fkCuentaContableDet' => $this->validation->getError('fkCuentaContableDet'),
            ];
        }

        return $this->response->setJSON($response);
    }

    public function updateConfigCuenta() {
        $ctcfCodigo = $this->request->getPost('ctcfCodigo');
        $ctcfNombre = $this->request->getPost('ctcfNombre');
        $ctcfDetalle = $this->request->getPost('ctcfDetalle');
        $fkCuentaContableDet = $this->request->getPost('fkCuentaContableDet');
        $ctcfEstado = $this->request->getPost('ctcfEstado');

        $idConfig = $this->request->getPost('idConfig');
        $codeAux = $this->request->getPost('codeAux');

        $this->validation->setRules([
            'ctcfCodigo' => ['label' => 'Código', 'rules' => 'trim|required'],
            'ctcfNombre' => ['label' => 'Nombre', 'rules' => 'trim|required'],
            'ctcfDetalle' => ['label' => 'Detalle', 'rules' => 'trim|required'],
            'fkCuentaContableDet' => ['label' => 'Cuenta Contable Detalle', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {
            $existe = $this->ccm->getData('cc_cuenta_contabledet_config', ['ctcf_codigo' => $ctcfCodigo], '*', $orderBy = null, 1);

            if ($existe && $existe->ctcf_codigo != $codeAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe una configuración con el código ' . $ctcfCodigo . '</h5>';
                return $this->response->setJSON($response);
            }

            $datos = [
                'ctcf_codigo' => $ctcfCodigo,
                'ctcf_nombre' => mb_strtoupper($ctcfNombre, 'UTF-8'),
                'ctcf_detalle' => $ctcfDetalle,
                'fk_cuentacontable_det' => $fkCuentaContableDet,
                'ctcf_estado' => $ctcfEstado,
            ];

            $this->ccm->actualizar('cc_cuenta_contabledet_config', $datos, ['id' => $idConfig]);
            $this->logs->logSuccess('SE HA ACTUALIZADO LA CONFIGURACIÓN DE CUENTA CON EL ID ' . $idConfig);

            $response['status'] = 'success';
            $response['msg'] = '<h5>Configuración actualizada exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'ctcfCodigo' => $this->validation->getError('ctcfCodigo'),
                'ctcfNombre' => $this->validation->getError('ctcfNombre'),
                'ctcfDetalle' => $this->validation->getError('ctcfDetalle'),
                'fkCuentaContableDet' => $this->validation->getError('fkCuentaContableDet'),
            ];
        }

        return $this->response->setJSON($response);
    }
}
