<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of EnterpriceController
 *
  /**
 * @author CRISTIAN PAZ
 * @date 8 abr. 2024
 * @time 20:46:43
 */
class EnterpriceController extends \App\Controllers\BaseController {

    //put your code here
    protected $dirViewModule;

    public function __construct() {
        $this->dirViewModule = 'Modules\Admin\Views';
    }

    public function index() {

        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\empresa\viewEmpresa');
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function getEmpresa() {

        $response = $this->ccm->getData('cc_empresa', $where_data = null, $fields = '*', $order_by = null);
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }

    public function updateEmpresa() {
        $rucEmp = $this->request->getPost('empRuc');
        $razonsocialEmp = $this->request->getPost('empRasonSocial');
        $nombrecomercialEmp = $this->request->getPost('empNombreComercial');
        $representanteEmp = $this->request->getPost('empRepLegal');
        $cuidadEmp = $this->request->getPost('empCiudad');
        $direccionEmp = $this->request->getPost('empDireccion');
        $misionEmp = $this->request->getPost('empMision');
        $visionEmp = $this->request->getPost('empVision');
        $objetivosEmp = $this->request->getPost('empObjetivos');
        $telefonoEmp = $this->request->getPost('empTelefono');
        $celularEmp = $this->request->getPost('empCelular');
        $emailEmp = $this->request->getPost('empEmail');
        $fechacreacionEmp = $this->request->getPost('empFechaCreacion');

        $idEmpresa = $this->request->getPost('idEmpresa');
        $nameAux = $this->request->getPost('nameAux');

        $this->validation->setRules([
            'empRuc' => ['label' => 'RUC Empresa', 'rules' => 'trim|required'],
            'empRasonSocial' => ['label' => 'Razon Social Empresa', 'rules' => 'trim|required'],
            'empNombreComercial' => ['label' => 'Nombre Comercial Empresa', 'rules' => 'trim|required'],
            'empRepLegal' => ['label' => 'Representante Legal Empresa', 'rules' => 'trim|required'],
            'empDireccion' => ['label' => 'Dirección Empresa', 'rules' => 'trim|required'],
            'empTelefono' => ['label' => 'Telefono Empresa', 'rules' => 'trim|required'],
            'empCelular' => ['label' => 'Celular Empresa', 'rules' => 'trim|required'],
            'empEmail' => ['label' => 'Email Empresa', 'rules' => 'trim|required'],
            'empFechaCreacion' => ['label' => 'Fecha Creación Empresa', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_empresa', ['epr_ruc' => $rucEmp], '*', $orderBy = null, 1);

            if ($existe && $existe->epr_ruc != $nameAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe una empresa registrado con este RUC ' . $rucEmp . 'Empresa ' . $nombrecomercialEmp . ' </h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'epr_ruc' => $rucEmp,
                'epr_razon_social' => mb_strtoupper($razonsocialEmp, 'UTF-8'),
                'epr_rep_legal' => mb_strtoupper($representanteEmp, 'UTF-8'),
                'epr_nombre_comercial' => mb_strtoupper($nombrecomercialEmp, 'UTF-8'),
                'epr_ciudad' => $cuidadEmp,
                'epr_direccion' => $direccionEmp,
                'epr_mision' => mb_strtoupper($misionEmp, 'UTF-8'),
                'epr_vision' => mb_strtoupper($visionEmp, 'UTF-8'),
                'epr_objetivos' => mb_strtoupper($objetivosEmp, 'UTF-8'),
                'epr_telefono' => $telefonoEmp,
                'epr_celular' => $celularEmp,
                'epr_email' => $emailEmp,
                'epr_fecha_creacion' => $fechacreacionEmp
            ];

            $this->ccm->actualizar('cc_empresa', $datos, ['id' => $idEmpresa]);
            $response['status'] = 'success';
            $response['msg'] = '<h5>Datos de la empresa actualizados exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'empRuc' => $this->validation->getError('empRuc'),
                'empRasonSocial' => $this->validation->getError('empRasonSocial'),
                'empNombreComercial' => $this->validation->getError('empNombreComercial'),
                'empRepLegal' => $this->validation->getError('empRepLegal'),
                'empDireccion' => $this->validation->getError('empDireccion'),
                'empTelefono' => $this->validation->getError('empTelefono'),
                'empCelular' => $this->validation->getError('empCelular'),
                'empEmail' => $this->validation->getError('empEmail'),
                'empFechaCreacion' => $this->validation->getError('empFechaCreacion'),
            ];
        }
        return $this->response->setJson($response);
    }
}
