<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of ClientesController
 *
  /**
 * @author CRISTIAN PAZ
 * @date 15 abr. 2024
 * @time 12:34:26
 */
use Modules\Admin\Models\ClienteModel;
use Modules\Comun\Libraries\ValidadorCiRuc;

class ClientesController extends \App\Controllers\BaseController {

    protected $dirViewModule;
    protected $cliModel;
    protected $validateCiRuc;

    public function __construct() {
        $this->dirViewModule = '\Modules\Admin\Views';

        //MODELOS
        $this->cliModel = new ClienteModel();

        //LIBRERIAS
        $this->validateCiRuc = new ValidadorCiRuc();
    }

    public function index() {
        $this->user->validateSession();
        $mod['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;
        $data['listaTipoDocumento'] = $this->ccm->getData('cc_tipo_documento');
        $data['listaProvincia'] = $this->ccm->getData('cc_provincia');
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $mod);
        $send['view'] = view($this->dirViewModule . '\clientes\viewClientes', $data);
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function getClientes() {

        $data = json_decode(file_get_contents("php://input"));
        $response = $this->cliModel->getClientes($data->ciruc);

        return $this->response->setJSON([
                    "status" => $response ? "success" : "empty",
                    "msg" => $response ? "ok" : "<h5>No se encontraron clientes en los parametros especificados</h5>",
                    "data" => $response
        ]);
    }

    public function searchClientes() {

        $data = json_decode(file_get_contents("php://input"));
        if ($data->dataSerach) {
            $response = $this->cliModel->searchClientes($data->dataSerach);
            if ($response) {
                return $this->response->setJSON($response);
            }
        }
        return $this->response->setJSON(false);
    }

    public function saveCliente() {


        $clieCiruc = $this->request->getPost('clieCiruc');
        $clieTipoDocumento = $this->request->getPost('clieTipoDocumento');
        $clieNombres = $this->request->getPost('clieNombres');
        $clieApellidos = $this->request->getPost('clieApellidos');
        $clieRazonSocial = $this->request->getPost('clieRazonSocial');
        $clieSexo = $this->request->getPost('clieSexo');
        $clieGenero = $this->request->getPost('clieGenero');
        $clieTelefono = $this->request->getPost('clieTelefono');
        $clieCelular = $this->request->getPost('clieCelular');
        $clieEmail = $this->request->getPost('clieEmail');
        $clieDireccion = $this->request->getPost('clieDireccion');
        $clieParroquia = $this->request->getPost('clieParroquia');
        $clieTipoSujeto = $this->request->getPost('clieTipoCliente');
        $clieDiasCredito = $this->request->getPost('clieDiasCredito');
        $clieCupoCredito = $this->request->getPost('clieCupoCredito');
        $clieEstado = $this->request->getPost('clieEstado');

        $this->validation->setRules([
            'clieCiruc' => ['label' => 'CI / RUC', 'rules' => 'trim|required'],
            'clieTipoDocumento' => ['label' => 'Tipo de documento', 'rules' => 'trim|required'],
            'clieNombres' => ['label' => 'Nombres', 'rules' => 'trim|required'],
            'clieApellidos' => ['label' => 'Apellidos', 'rules' => 'trim|required'],
            'clieTipoCliente' => ['label' => 'Tipo de cliente', 'rules' => 'trim|required'],
            'clieRazonSocial' => ['label' => 'Razon Social', 'rules' => 'trim|required'],
            'clieSexo' => ['label' => 'Sexo', 'rules' => 'trim|required'],
            'clieCelular' => ['label' => 'Celular', 'rules' => 'trim|required|numeric'],
            'clieEmail' => ['label' => 'Email', 'rules' => 'trim|required|valid_email'],
            'clieDireccion' => ['label' => 'Dirección', 'rules' => 'trim|required'],
            'clieParroquia' => ['label' => 'Parroquia', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $validacion = $this->validateCiRuc->validarNumeroDocumento($clieTipoDocumento, $clieCiruc);

            if ($validacion['status'] === "warning") {
                return $this->response->setJson($validacion);
            } else {
                $clieTipoSujeto = $validacion['data']; //El tipo de sujeto se da de acuerdo al tipo de cedula o ruc o pasaporte
            }

            $existe = $this->ccm->getData('cc_clientes', ['clie_dni' => trim($clieCiruc)]);
            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un cliente registrado con el CI/RUC ' . $clieCiruc . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'clie_dni' => trim($clieCiruc),
                'fk_tipo_documento' => $clieTipoDocumento,
                'clie_nombres' => mb_strtoupper($clieNombres, 'UTF-8'),
                'clie_apellidos' => mb_strtoupper($clieApellidos, 'UTF-8'),
                'clie_razon_social' => mb_strtoupper($clieRazonSocial, 'UTF-8'),
                'clie_sexo' => $clieSexo,
                'clie_genero' => $clieGenero,
                'clie_telefono' => $clieTelefono,
                'clie_celular' => $clieCelular,
                'clie_email' => $clieEmail,
                'clie_direccion' => mb_strtoupper($clieDireccion, 'UTF-8'),
                'fk_parroquia' => $clieParroquia,
                'fk_tipo_sujeto' => $clieTipoSujeto,
                'clie_dias_credito' => $clieDiasCredito,
                'clie_cupo_credito' => $clieCupoCredito,
                'clie_estado' => $clieEstado === "true" ? 1 : 0,
                'clie_fecha_creacion' => date('Y-m-d H:i:s'),
            ];

            $this->db->transBegin();

            $clieSave = $this->ccm->guardar($datos, 'cc_clientes');

            if ($this->db->transStatus == false) {
                $response['status'] = 'error';
                $response['msg'] = '<h5>Ha ocurrido un error al tratar de crear el cliente ' . $clieRazonSocial . '</h5>';
                $this->db->transRollback();
            } else {
                $this->logs->logSuccess('SE HA CREADO UN CLIENTE CON EL ID ' . $clieSave);
                $response['status'] = 'success';
                $response['msg'] = '<h5>Cliente Registrado Exitosamente <br><hr> ' . $clieSave . ' : ' . mb_strtoupper(trim($clieRazonSocial)) . '</h5>';
                $this->db->transCommit();
            }
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'clieCiruc' => $this->validation->getError('clieCiruc'),
                'clieTipoDocumento' => $this->validation->getError('clieTipoDocumento'),
                'clieTipoCliente' => $this->validation->getError('clieTipoCliente'),
                'clieNombres' => $this->validation->getError('clieNombres'),
                'clieApellidos' => $this->validation->getError('clieApellidos'),
                'clieRazonSocial' => $this->validation->getError('clieRazonSocial'),
                'clieSexo' => $this->validation->getError('clieSexo'),
                'clieCelular' => $this->validation->getError('clieCelular'),
                'clieEmail' => $this->validation->getError('clieEmail'),
                'clieDireccion' => $this->validation->getError('clieDireccion'),
                'clieParroquia' => $this->validation->getError('clieParroquia'),
            ];
        }
        return $this->response->setJson($response);
    }

    public function updateCliente() {

        $clieCiruc = $this->request->getPost('clieCiruc');
        $clieTipoDocumento = $this->request->getPost('clieTipoDocumento');
        $clieNombres = $this->request->getPost('clieNombres');
        $clieApellidos = $this->request->getPost('clieApellidos');
        $clieRazonSocial = $this->request->getPost('clieRazonSocial');
        $clieSexo = $this->request->getPost('clieSexo');
        $clieGenero = $this->request->getPost('clieGenero');
        $clieTelefono = $this->request->getPost('clieTelefono');
        $clieCelular = $this->request->getPost('clieCelular');
        $clieEmail = $this->request->getPost('clieEmail');
        $clieDireccion = $this->request->getPost('clieDireccion');
        $clieParroquia = $this->request->getPost('clieParroquia');
        $clieTipoCliente = $this->request->getPost('clieTipoCliente');
        $clieDiasCredito = $this->request->getPost('clieDiasCredito');
        $clieCupoCredito = $this->request->getPost('clieCupoCredito');
        $clieEstado = $this->request->getPost('clieEstado');

        $idClie = $this->request->getPost('idClie');
        $ciRucAux = $this->request->getPost('ciRucAux');

        $this->validation->setRules([
            'clieCiruc' => ['label' => 'CI / RUC', 'rules' => 'trim|required'],
            'clieTipoDocumento' => ['label' => 'Tipo de documento', 'rules' => 'trim|required'],
            'clieNombres' => ['label' => 'Nombres', 'rules' => 'trim|required'],
            'clieApellidos' => ['label' => 'Apellidos', 'rules' => 'trim|required'],
            'clieTipoCliente' => ['label' => 'Tipo de cliente', 'rules' => 'trim|required'],
            'clieRazonSocial' => ['label' => 'Razon Social', 'rules' => 'trim|required'],
            'clieSexo' => ['label' => 'Sexo', 'rules' => 'trim|required'],
            'clieCelular' => ['label' => 'Celular', 'rules' => 'trim|required|numeric'],
            'clieEmail' => ['label' => 'Email', 'rules' => 'trim|required|valid_email'],
            'clieDireccion' => ['label' => 'Dirección', 'rules' => 'trim|required'],
            'clieParroquia' => ['label' => 'Parroquia', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $validacion = $this->validateCiRuc->validarNumeroDocumento($clieTipoDocumento, $clieCiruc);

            if ($validacion['status'] === "warning") {
                return $this->response->setJson($validacion);
            } else {
                $clieTipoCliente = $validacion['data']; //El tipo de sujeto se da de acuerdo al tipo de cedula o ruc o pasaporte
            }

            $existeCiruc = $this->ccm->getData('cc_clientes', ['clie_dni' => trim($clieCiruc)], 'clie_dni', $orderBy = null, 1);
            if ($existeCiruc && $existeCiruc->clie_dni != $ciRucAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un cliente  registrado con el  CI/RUC  ' . $clieCiruc . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'clie_dni' => trim($clieCiruc),
                'fk_tipo_documento' => $clieTipoDocumento,
                'clie_nombres' => mb_strtoupper($clieNombres, 'UTF-8'),
                'clie_apellidos' => mb_strtoupper($clieApellidos, 'UTF-8'),
                'clie_razon_social' => mb_strtoupper($clieRazonSocial, 'UTF-8'),
                'clie_sexo' => $clieSexo,
                'clie_genero' => $clieGenero == "null" ? NULL : $clieGenero,
                'clie_telefono' => $clieTelefono,
                'clie_celular' => $clieCelular,
                'clie_email' => $clieEmail,
                'clie_direccion' => mb_strtoupper($clieDireccion, 'UTF-8'),
                'fk_parroquia' => $clieParroquia,
                'fk_tipo_sujeto' => $clieTipoCliente,
                'clie_dias_credito' => $clieDiasCredito,
                'clie_cupo_credito' => $clieCupoCredito,
                'clie_estado' => $clieEstado === "true" ? 1 : 0,
                'clie_fecha_actualizacion' => date('Y-m-d H:i:s'),
            ];

            $this->db->transBegin();

            $this->ccm->actualizar('cc_clientes', $datos, ['id' => $idClie]);

            if ($this->db->transStatus == false) {
                $response['status'] = 'error';
                $response['msg'] = '<h5>Ha ocurrido un error al tratar de actualizar el cliente ' . $clieRazonSocial . '</h5>';
                $this->db->transRollback();
            } else {
                $this->logs->logSuccess('SE HA ACTUALIZADO UN CLIENTE CON EL ID ' . $idClie);
                $response['status'] = 'success';
                $response['msg'] = '<h5>Cliente Actualizado Exitosamente <br><hr> ' . $idClie . ' : ' . mb_strtoupper(trim($clieRazonSocial)) . '</h5>';
                $this->db->transCommit();
            }
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'clieCiruc' => $this->validation->getError('clieCiruc'),
                'clieTipoDocumento' => $this->validation->getError('clieTipoDocumento'),
                'clieTipoCliente' => $this->validation->getError('clieTipoCliente'),
                'clieNombres' => $this->validation->getError('clieNombres'),
                'clieApellidos' => $this->validation->getError('clieApellidos'),
                'clieRazonSocial' => $this->validation->getError('clieRazonSocial'),
                'clieSexo' => $this->validation->getError('clieSexo'),
                'clieCelular' => $this->validation->getError('clieCelular'),
                'clieEmail' => $this->validation->getError('clieEmail'),
                'clieDireccion' => $this->validation->getError('clieDireccion'),
                'clieParroquia' => $this->validation->getError('clieParroquia'),
            ];
        }
        return $this->response->setJson($response);
    }
}
