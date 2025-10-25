<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of PuntoVentaController
 *
  /**
 * @author CRISTIAN PAZ
 * @date 12 abr. 2024
 * @time 12:04:43
 */
use Modules\Admin\Models\PuntoVentaModel;

class PuntoVentaController extends \App\Controllers\BaseController {

    //put your code here
    protected $dirViewModule;
    protected $pvModel;

    public function __construct() {
        $this->dirViewModule = 'Modules\Admin\Views';
        $this->pvModel = new PuntoVentaModel();
    }

    public function index() {
        $this->user->validateSession();

        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $dataview['listaComprobantes'] = $this->ccm->getData('cc_tipos_comprobante');
        $dataview['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1]);
        $dataview['listaEmpleados'] = $this->ccm->getData('cc_empleados', ['emp_estado' => 1], 'CONCAT(emp_nombre," ",emp_apellido) empleado ,id');
        $send['view'] = view($this->dirViewModule . '\puntosventa\viewPuntosVenta', $dataview);
        return $this->response->setJSON($send);
    }

    public function getPuntosVenta() {
        $response = $this->pvModel->getPuntosVenta();
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }

    public function showEmpleados($idPv) {
        $response = $this->pvModel->showEmpleados($idPv);
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }

    public function savePuntoVenta() {
        $pvComprobante = $this->request->getPost('pvComprobante');
        $pvEstablecimiento = $this->request->getPost('pvEstablecimiento');
        $pvEmision = $this->request->getPost('pvEmision');
        $pvAuthSri = $this->request->getPost('pvAuthSri');
        $pvFechaVenceAuthSri = $this->request->getPost('pvFechaVenceAuthSri');
        $pvSecInicial = $this->request->getPost('pvSecInicial');
        $pvSecFinal = $this->request->getPost('pvSecFinal');
        $pvIsElectronica = $this->request->getPost('pvIsElectronica');
        $pvBodega = $this->request->getPost('pvBodega');
        $pvEstado = $this->request->getPost('pvEstado');
        $pvEmpleado = $this->request->getPost('pvEmpleado');

        $this->db->transBegin();

        $this->validation->setRules([
            'pvComprobante' => ['label' => 'Comprobante Punto Venta', 'rules' => 'trim|required'],
            'pvEstablecimiento' => ['label' => 'Punto Establecimiento', 'rules' => 'trim|required'],
            'pvEmision' => ['label' => 'Punto Emision', 'rules' => 'trim|required'],
            'pvAuthSri' => ['label' => 'Autorización SRI', 'rules' => 'trim|required'],
            'pvFechaVenceAuthSri' => ['label' => 'Fecha Vence Autorización SRI', 'rules' => 'trim|required'],
            'pvSecInicial' => ['label' => 'Secuencia Inicial', 'rules' => 'trim|required'],
            'pvSecFinal' => ['label' => 'Secuencia Final', 'rules' => 'trim|required'],
            'pvBodega' => ['label' => 'Bodega Punto Venta', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

//            $existe = $this->ccm->getData('cc_puntos_venta', ['fk_comprobante' => $pvComprobante]);
//            if (count($existe) > 0) {
//                $response['status'] = 'existe';
//                $response['msg'] = '<h5>Ya existe un punto de venta registrado con el nombre estos datos </h5>';
//                return $this->response->setJson($response);
//            }

            $datos = [
                'fk_comprobante' => $pvComprobante,
                'pv_establecimiento' => $pvEstablecimiento,
                'pv_emision' => $pvEmision,
                'pv_auth_sri' => $pvAuthSri,
                'pv_fecha_vence_auth' => $pvFechaVenceAuthSri,
                'pv_sec_inicial' => $pvSecInicial,
                'pv_sec_final' => $pvSecFinal,
                'pv_is_electronica' => $pvIsElectronica,
                'pv_fk_bodega' => $pvBodega,
                'pv_estado' => $pvEstado,
                'pv_fecha_creacionpunto' => date('Y-m-d'),
            ];

            $pvSave = $this->ccm->guardar($datos, 'cc_puntos_venta');

            if (!empty($pvEmpleado)) {
                $empleados = explode(',', $pvEmpleado);
                foreach ($empleados as $val) {
                    $datosPvEmp = [
                        'fk_punto_venta' => $pvSave,
                        'fk_empleado' => $val,
                        'pvemp_fecha_registro' => $val,
                    ];
                    $this->ccm->guardar($datosPvEmp, 'cc_puntoventa_empleado');
                }
            }
            if ($this->db->transStatus() === false) {
                // generate an error... or use the log_message() function to log your error
                $this->db->transRollback();
                die();
            } else {
                $this->logs->logSuccess('SE HA CREADO UN NUEVO PUNTO DE VENTA CON EL ID ' . $pvSave);
                $response['status'] = 'success';
                $response['msg'] = '<h5>Punto de venta registrado exitosamente</h5>';
                $this->db->transCommit();
            }
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'pvComprobante' => $this->validation->getError('pvComprobante'),
                'pvEstablecimiento' => $this->validation->getError('pvEstablecimiento'),
                'pvEmision' => $this->validation->getError('pvEmision'),
                'pvAuthSri' => $this->validation->getError('pvAuthSri'),
                'pvFechaVenceAuthSri' => $this->validation->getError('pvFechaVenceAuthSri'),
                'pvSecInicial' => $this->validation->getError('pvSecInicial'),
                'pvSecFinal' => $this->validation->getError('pvSecFinal'),
                'pvBodega' => $this->validation->getError('pvBodega'),
            ];
        }
        return $this->response->setJson($response);
    }
    public function updatePuntoVenta() {
        $pvComprobante = $this->request->getPost('pvComprobante');
        $pvEstablecimiento = $this->request->getPost('pvEstablecimiento');
        $pvEmision = $this->request->getPost('pvEmision');
        $pvAuthSri = $this->request->getPost('pvAuthSri');
        $pvFechaVenceAuthSri = $this->request->getPost('pvFechaVenceAuthSri');
        $pvSecInicial = $this->request->getPost('pvSecInicial');
        $pvSecFinal = $this->request->getPost('pvSecFinal');
        $pvIsElectronica = $this->request->getPost('pvIsElectronica');
        $pvBodega = $this->request->getPost('pvBodega');
        $pvEstado = $this->request->getPost('pvEstado');
        $pvEmpleado = $this->request->getPost('pvEmpleado');
              
        $idPv = $this->request->getPost('idPV');

        $this->db->transBegin();

        $this->validation->setRules([
            'pvComprobante' => ['label' => 'Comprobante Punto Venta', 'rules' => 'trim|required'],
            'pvEstablecimiento' => ['label' => 'Punto Establecimiento', 'rules' => 'trim|required'],
            'pvEmision' => ['label' => 'Punto Emision', 'rules' => 'trim|required'],
            'pvAuthSri' => ['label' => 'Autorización SRI', 'rules' => 'trim|required'],
            'pvFechaVenceAuthSri' => ['label' => 'Fecha Vence Autorización SRI', 'rules' => 'trim|required'],
            'pvSecInicial' => ['label' => 'Secuencia Inicial', 'rules' => 'trim|required'],
            'pvSecFinal' => ['label' => 'Secuencia Final', 'rules' => 'trim|required'],
            'pvBodega' => ['label' => 'Bodega Punto Venta', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

//            $existe = $this->ccm->getData('cc_puntos_venta', ['fk_comprobante' => $pvComprobante]);
//            if (count($existe) > 0) {
//                $response['status'] = 'existe';
//                $response['msg'] = '<h5>Ya existe un punto de venta registrado con el nombre estos datos </h5>';
//                return $this->response->setJson($response);
//            }

            $datos = [
                'fk_comprobante' => $pvComprobante,
                'pv_establecimiento' => $pvEstablecimiento,
                'pv_emision' => $pvEmision,
                'pv_auth_sri' => $pvAuthSri,
                'pv_fecha_vence_auth' => $pvFechaVenceAuthSri,
                'pv_sec_inicial' => $pvSecInicial,
                'pv_sec_final' => $pvSecFinal,
                'pv_is_electronica' => $pvIsElectronica,
                'pv_fk_bodega' => $pvBodega,
                'pv_estado' => $pvEstado,
                'pv_fecha_creacionpunto' => date('Y-m-d'),
            ];

            $this->ccm->actualizar('cc_puntos_venta',$datos, ['id'=>$idPv]);

            if (!empty($pvEmpleado)) {
                $empleados = explode(',', $pvEmpleado);
                $this->ccm->eliminar('cc_puntoventa_empleado',['fk_punto_venta'=>$idPv]);
                foreach ($empleados as $val) {
                    $datosPvEmp = [
                        'fk_punto_venta' => $idPv,
                        'fk_empleado' => $val,
                        'pvemp_fecha_registro' => date('Y-m-d'),
                    ];
                    $this->ccm->guardar($datosPvEmp, 'cc_puntoventa_empleado');
                }
            }
            if ($this->db->transStatus() === false) {
                // generate an error... or use the log_message() function to log your error
                $this->db->transRollback();
                die();
            } else {
                $this->logs->logSuccess('SE HA ACTUALIZADO EL PUNTO DE VENTA CON EL ID ' . $idPv);
                $response['status'] = 'success';
                $response['msg'] = '<h5>Punto de venta actualizado exitosamente</h5>';
                $this->db->transCommit();
            }
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'pvComprobante' => $this->validation->getError('pvComprobante'),
                'pvEstablecimiento' => $this->validation->getError('pvEstablecimiento'),
                'pvEmision' => $this->validation->getError('pvEmision'),
                'pvAuthSri' => $this->validation->getError('pvAuthSri'),
                'pvFechaVenceAuthSri' => $this->validation->getError('pvFechaVenceAuthSri'),
                'pvSecInicial' => $this->validation->getError('pvSecInicial'),
                'pvSecFinal' => $this->validation->getError('pvSecFinal'),
                'pvBodega' => $this->validation->getError('pvBodega'),
            ];
        }
        return $this->response->setJson($response);
    }
}
