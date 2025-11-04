<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of ProveedoresController
 *
  /**
 * @author CRISTIAN PAZ
 * @date 15 abr. 2024
 * @time 12:36:01
 */
use Modules\Admin\Models\ProveedorModel;
use Modules\Comun\Libraries\ValidadorCiRuc;

class ProveedoresController extends \App\Controllers\BaseController {

    protected $dirViewModule;
    protected $provModel;
    protected $validateCiRuc;

    public function __construct() {
        $this->dirViewModule = 'Modules\Admin\Views';

        //MODELOS
        $this->provModel = new ProveedorModel();

        //LIBRERIAS
        $this->validateCiRuc = new ValidadorCiRuc();
    }

    public function index() {
        $this->user->validateSession();
        $mod['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;
        $data['listaTipoDocumento'] = $this->ccm->getData('cc_tipo_documento');
        $data['listaSectores'] = $this->ccm->getData('cc_sectores', ['sec_estado' => 1]);
        $data['listaCuentasContable'] = $this->ccm->getData('cc_cuenta_contabledet', ['fk_cta_contable' => 2, 'ctad_estado' => 1]);
        $data['listaProvincia'] = $this->ccm->getData('cc_provincia');
        $data['listaTipoCuentaBanco'] = $this->ccm->getData('cc_banco_tipo_cuenta');
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $mod);
        $send['view'] = view($this->dirViewModule . '\proveedores\viewProveedores', $data);
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function getProveedores() {

        $data = json_decode(file_get_contents("php://input"));
        $response = $this->provModel->getProveedores($data->ciruc);

        return $this->response->setJSON([
                    "status" => $response ? "success" : "empty",
                    "msg" => $response ? "ok" : "<h5>No se encontraron clientes en los parametros especificados</h5>",
                    "data" => $response
        ]);
    }

    public function searchProveedores() {

        $data = json_decode(file_get_contents("php://input"));
        if ($data->dataSerach) {
            $response = $this->provModel->searchProveedores($data->dataSerach);
            if ($response) {
                return $this->response->setJSON($response);
            }
        }
        return $this->response->setJSON(false);
    }

    public function saveProveedor() {


        $provRuc = $this->request->getPost('provRuc');
        $provTipoDocumento = $this->request->getPost('provTipoDocumento');
        $provNombres = $this->request->getPost('provNombres');
        $provApellidos = $this->request->getPost('provApellidos');
        $provRazonSocial = $this->request->getPost('provRazonSocial');
        $provTelefono = $this->request->getPost('provTelefono');
        $provCelular = $this->request->getPost('provCelular');
        $provEmail = $this->request->getPost('provEmail');
        $provDireccion = $this->request->getPost('provDireccion');
        $provSector = $this->request->getPost('provSector');
        $provParroquia = $this->request->getPost('provParroquia');
        $provTipoSujeto = $this->request->getPost('provTipoProveedor');
        $provDiasCredito = $this->request->getPost('provDiasCredito');
        $provCtaContable = $this->request->getPost('provCtaContable');
        $provEstado = $this->request->getPost('provEstado');

        $this->validation->setRules([
            'provRuc' => ['label' => 'RUC', 'rules' => 'trim|required'],
            'provTipoDocumento' => ['label' => 'Tipo de documento', 'rules' => 'trim|required'],
            'provNombres' => ['label' => 'Nombres', 'rules' => 'trim|required'],
            'provApellidos' => ['label' => 'Apellidos', 'rules' => 'trim|required'],
            'provRazonSocial' => ['label' => 'Razón Social', 'rules' => 'trim|required'],
            'provCelular' => ['label' => 'Celular', 'rules' => 'trim|required|numeric'],
            'provEmail' => ['label' => 'Email', 'rules' => 'trim|required|valid_email'],
            'provDireccion' => ['label' => 'Dirección', 'rules' => 'trim|required'],
            'provSector' => ['label' => 'Sector', 'rules' => 'trim|required'],
            'provParroquia' => ['label' => 'Parroquia', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $validacion = $this->validateCiRuc->validarNumeroDocumento($provTipoDocumento, $provRuc);

            if ($validacion['status'] === "warning") {
                return $this->response->setJson($validacion);
            } else {
                $provTipoSujeto = $validacion['data']; //Tipo de sujeto según cédula, RUC o pasaporte
            }

            $existe = $this->ccm->getData('cc_proveedores', ['prov_ruc' => trim($provRuc)]);
            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un proveedor registrado con el RUC ' . $provRuc . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'prov_ruc' => trim($provRuc),
                'fk_tipo_documento' => $provTipoDocumento,
                'prov_nombres' => mb_strtoupper($provNombres, 'UTF-8'),
                'prov_apellidos' => mb_strtoupper($provApellidos, 'UTF-8'),
                'prov_razon_social' => mb_strtoupper($provRazonSocial, 'UTF-8'),
                'prov_telefono' => $provTelefono,
                'prov_celular' => $provCelular,
                'prov_email' => $provEmail,
                'prov_direccion' => mb_strtoupper($provDireccion, 'UTF-8'),
                'fk_sector' => $provSector,
                'fk_parroquia' => $provParroquia,
                'fk_tipo_sujeto' => $provTipoSujeto,
                'prov_dias_credito' => $provDiasCredito,
                'fk_codigo_cuenta_contable' => $provCtaContable ? $provCtaContable : NULL,
                'prov_estado' => $provEstado === "true" ? 1 : 0,
                'prov_fecha_creacion' => date('Y-m-d H:i:s'),
            ];

            $this->db->transBegin();

            $provSave = $this->ccm->guardar($datos, 'cc_proveedores');

            if ($provSave) {

                $listaCuentasBancarias = $this->request->getPost('listaCuentasBancarias');
                $listaRetencionesProveedor = $this->request->getPost('listaRetencionesProveedor');
                if ($listaCuentasBancarias) {
                    foreach ($listaCuentasBancarias as $val) {
                        $datosCuenta = [
                            'fk_proveedor' => $provSave,
                            'fk_banco' => $val['id'],
                            'fk_tipo_cuenta' => $val['tipo_cuenta'],
                            'numero_cuenta' => $val['numero_cuenta'],
                        ];
                        $save = $this->ccm->guardar($datosCuenta, 'cc_proveedor_banco');

                        if (empty($save)) {
                            $this->db->transRollback();
                        }
                    }
                }
                if ($listaRetencionesProveedor) {
                    foreach ($listaRetencionesProveedor as $val) {
                        $datosRetencion = [
                            'fk_proveedor' => $provSave,
                            'fk_retencion' => $val['id']
                        ];
                        $save = $this->ccm->guardar($datosRetencion, 'cc_proveedor_retencion');
                        if (empty($save)) {
                            $this->db->transRollback();
                        }
                    }
                }
            }


            if ($this->db->transStatus() == false) {
                $response['status'] = 'error';
                $response['msg'] = '<h5>Ha ocurrido un error al tratar de crear el proveedor ' . $provRazonSocial . '</h5>';
                $this->db->transRollback();
            } else {
                $this->logs->logSuccess('SE HA CREADO UN PROVEEDOR CON EL ID ' . $provSave);
                $response['status'] = 'success';
                $response['msg'] = '<h5>Proveedor Registrado Exitosamente <br><hr> ' . $provSave . ' : ' . mb_strtoupper(trim($provRazonSocial)) . '</h5>';
                $this->db->transCommit();
            }
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'provRuc' => $this->validation->getError('provRuc'),
                'provTipoDocumento' => $this->validation->getError('provTipoDocumento'),
                'provTipoProveedor' => $this->validation->getError('provTipoProveedor'),
                'provNombres' => $this->validation->getError('provNombres'),
                'provApellidos' => $this->validation->getError('provApellidos'),
                'provRazonSocial' => $this->validation->getError('provRazonSocial'),
                'provCelular' => $this->validation->getError('provCelular'),
                'provEmail' => $this->validation->getError('provEmail'),
                'provDireccion' => $this->validation->getError('provDireccion'),
                'provSector' => $this->validation->getError('provSector'),
                'provParroquia' => $this->validation->getError('provParroquia'),
            ];
        }

        return $this->response->setJson($response);
    }

    public function updateProveedor() {

        $provRuc = $this->request->getPost('provRuc');
        $provTipoDocumento = $this->request->getPost('provTipoDocumento');
        $provNombres = $this->request->getPost('provNombres');
        $provApellidos = $this->request->getPost('provApellidos');
        $provRazonSocial = $this->request->getPost('provRazonSocial');
        $provTelefono = $this->request->getPost('provTelefono');
        $provCelular = $this->request->getPost('provCelular');
        $provEmail = $this->request->getPost('provEmail');
        $provDireccion = $this->request->getPost('provDireccion');
        $provSector = $this->request->getPost('provSector');
        $provParroquia = $this->request->getPost('provParroquia');
        $provTipoSujeto = $this->request->getPost('provTipoProveedor');
        $provDiasCredito = $this->request->getPost('provDiasCredito');
        $provCtaContable = $this->request->getPost('provCtaContable');
        $provEstado = $this->request->getPost('provEstado');

        $idProv = $this->request->getPost('idProv');
        $rucAux = $this->request->getPost('rucAux');

        $this->validation->setRules([
            'provRuc' => ['label' => 'RUC', 'rules' => 'trim|required'],
            'provTipoDocumento' => ['label' => 'Tipo de documento', 'rules' => 'trim|required'],
            'provNombres' => ['label' => 'Nombres', 'rules' => 'trim|required'],
            'provApellidos' => ['label' => 'Apellidos', 'rules' => 'trim|required'],
            'provRazonSocial' => ['label' => 'Razón Social', 'rules' => 'trim|required'],
            'provCelular' => ['label' => 'Celular', 'rules' => 'trim|required|numeric'],
            'provEmail' => ['label' => 'Email', 'rules' => 'trim|required|valid_email'],
            'provDireccion' => ['label' => 'Dirección', 'rules' => 'trim|required'],
            'provSector' => ['label' => 'Sector', 'rules' => 'trim|required'],
            'provParroquia' => ['label' => 'Parroquia', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $validacion = $this->validateCiRuc->validarNumeroDocumento($provTipoDocumento, $provRuc);

            if ($validacion['status'] === "warning") {
                return $this->response->setJson($validacion);
            } else {
                $provTipoSujeto = $validacion['data']; //Tipo de sujeto según cédula, RUC o pasaporte
            }

            $existeCiruc = $this->ccm->getData('cc_proveedores', ['prov_ruc' => trim($provRuc)], 'prov_ruc', $orderBy = null, 1);
            if ($existeCiruc && $existeCiruc->prov_ruc != $rucAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un proveedor  registrado con el  CI/RUC  ' . $provRuc . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'prov_ruc' => trim($provRuc),
                'fk_tipo_documento' => $provTipoDocumento,
                'prov_nombres' => mb_strtoupper($provNombres, 'UTF-8'),
                'prov_apellidos' => mb_strtoupper($provApellidos, 'UTF-8'),
                'prov_razon_social' => mb_strtoupper($provRazonSocial, 'UTF-8'),
                'prov_telefono' => $provTelefono,
                'prov_celular' => $provCelular,
                'prov_email' => $provEmail,
                'prov_direccion' => mb_strtoupper($provDireccion, 'UTF-8'),
                'fk_sector' => $provSector,
                'fk_parroquia' => $provParroquia,
                'fk_tipo_sujeto' => $provTipoSujeto,
                'prov_dias_credito' => $provDiasCredito,
                'fk_codigo_cuenta_contable' => $provCtaContable != "null" ? $provCtaContable : NULL,
                'prov_estado' => $provEstado === "true" ? 1 : 0,
                'prov_fecha_actualizacion' => date('Y-m-d H:i:s'),
            ];

            $this->db->transBegin();

            $update = $this->ccm->actualizar('cc_proveedores', $datos, ['id' => $idProv]);

            if ($update > 0) {

                $listaCuentasBancarias = $this->request->getPost('listaCuentasBancarias');
                $listaRetencionesProveedor = $this->request->getPost('listaRetencionesProveedor');

                if ($listaCuentasBancarias) {
                    $this->ccm->eliminar('cc_proveedor_banco', ['fk_proveedor' => $idProv]);
                    foreach ($listaCuentasBancarias as $val) {
                        $datosCuenta = [
                            'fk_proveedor' => $idProv,
                            'fk_banco' => $val['id'],
                            'fk_tipo_cuenta' => $val['tipo_cuenta'],
                            'numero_cuenta' => $val['numero_cuenta'],
                        ];
                        $save = $this->ccm->guardar($datosCuenta, 'cc_proveedor_banco');

                        if (empty($save)) {
                            $this->db->transRollback();
                        }
                    }
                }
                if ($listaRetencionesProveedor) {
                    $this->ccm->eliminar('cc_proveedor_retencion', ['fk_proveedor' => $idProv]);
                    foreach ($listaRetencionesProveedor as $val) {
                        $datosRetencion = [
                            'fk_proveedor' => $idProv,
                            'fk_retencion' => $val['id']
                        ];
                        $save = $this->ccm->guardar($datosRetencion, 'cc_proveedor_retencion');
                        if (empty($save)) {
                            $this->db->transRollback();
                        }
                    }
                }
            }

            if ($this->db->transStatus() == false) {
                $response['status'] = 'error';
                $response['msg'] = '<h5>Ha ocurrido un error al tratar de actualizar el proveedor ' . $provRazonSocial . '</h5>';
                $this->db->transRollback();
            } else {
                $this->logs->logSuccess('SE HA ACTUALIZADO UN PROVEEDOR CON EL ID ' . $idProv);
                $response['status'] = 'success';
                $response['msg'] = '<h5>Proveedor Registrado Exitosamente <br><hr> ' . $idProv . ' : ' . mb_strtoupper(trim($provRazonSocial)) . '</h5>';
                $this->db->transCommit();
            }
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'provRuc' => $this->validation->getError('provRuc'),
                'provTipoDocumento' => $this->validation->getError('provTipoDocumento'),
                'provTipoProveedor' => $this->validation->getError('provTipoProveedor'),
                'provNombres' => $this->validation->getError('provNombres'),
                'provApellidos' => $this->validation->getError('provApellidos'),
                'provRazonSocial' => $this->validation->getError('provRazonSocial'),
                'provCelular' => $this->validation->getError('provCelular'),
                'provEmail' => $this->validation->getError('provEmail'),
                'provDireccion' => $this->validation->getError('provDireccion'),
                'provSector' => $this->validation->getError('provSector'),
                'provParroquia' => $this->validation->getError('provParroquia'),
            ];
        }

        return $this->response->setJson($response);
    }

    public function datosAdicionalesProveedor($idProveedor) {
        $cuentasBancos = $this->provModel->getDataCuentas($idProveedor);
        $retenciones = $this->provModel->getDataRetenciones($idProveedor);

        return $this->response->setJSON([
                    'listaCuentasBancarias' => $cuentasBancos ? $cuentasBancos : [],
                    'listaRetenciones' => $retenciones ? $retenciones : [],
        ]);
    }

    public function getBancos() {
        $data = json_decode(file_get_contents("php://input"));

        if ($data->dataSerach) {
            $response = $this->provModel->getBancos($data->dataSerach);
            if ($response) {
                return $this->response->setJSON($response);
            }
        }
        return $this->response->setJSON(false);
    }

    public function getRetenciones() {
        $data = json_decode(file_get_contents("php://input"));

        if ($data->dataSerach) {
            $response = $this->provModel->getRetenciones($data->dataSerach);
            if ($response) {
                return $this->response->setJSON($response);
            }
        }
        return $this->response->setJSON(false);
    }
}
