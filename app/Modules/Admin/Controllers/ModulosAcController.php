<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of ModulosAcController
 *
  /**
 * @author CRISTIAN PAZ
 * @date 14 feb. 2024
 * @time 16:29:03
 */
use Modules\Admin\Models\AdminModel;

class ModulosAcController extends \App\Controllers\BaseController {

    protected $dirViewModule;
    protected $admModel;

    public function __construct() {
        $this->dirViewModule = 'Modules\Admin\Views';
        $this->admModel = new AdminModel();
    }

    public function index() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\modulosacciones\viewModAcciones', $data);
        return $this->response->setJSON($send);
//        return view($this->dirTemplate . '\dashboard', $send);
    }

    public function getModulos() {

        $response = $this->admModel->getModulos();
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }

    public function saveModulo() {

        /*         * OBTENEMOS LOS DATOS DEL FRONT * */
        $nombreModulo = $this->request->getPost('nombreModulo');
        $descripcionModulo = $this->request->getPost('descripcionModulo');
        $estadoModulo = $this->request->getPost('estadoModulo');
        $urlModulo = $this->request->getPost('urlModulo');
        $tipoModulo = $this->request->getPost('tipoModulo');
        $iconoModulo = $this->request->getPost('iconoModulo');
        $ordenModulo = $this->request->getPost('ordenModulo');
        $padreModulo = $this->request->getPost('padreModulo');

        /*         * VALIDAMOS LOS DATOS DEL FRONT (QUE CNO ESTEN VACIOS) * */
        if ($tipoModulo == 'submodulo') {
            $this->validation->setRules([
                'nombreModulo' => ['label' => 'Nombre Módulo', 'rules' => 'trim|required'],
                'urlModulo' => ['label' => 'URL Módulo', 'rules' => 'trim|required'],
                'iconoModulo' => ['label' => 'Ícono Módulo', 'rules' => 'trim|required'],
                'tipoModulo' => ['label' => 'Tipo Módulo', 'rules' => 'trim|required'],
                'ordenModulo' => ['label' => 'Órden Módulo', 'rules' => 'trim|required'],
                'padreModulo' => ['label' => 'Módulo Parde', 'rules' => 'trim|required']
            ]);
        } else {
            $this->validation->setRules([
                'nombreModulo' => ['label' => 'Nombre Módulo', 'rules' => 'trim|required'],
                'urlModulo' => ['label' => 'URL Módulo', 'rules' => 'trim|required'],
                'iconoModulo' => ['label' => 'Ícono Módulo', 'rules' => 'trim|required'],
                'tipoModulo' => ['label' => 'Tipo Módulo', 'rules' => 'trim|required'],
                'ordenModulo' => ['label' => 'Órden Módulo', 'rules' => 'trim|required'],
            ]);
        }

        /*         * VALIDAMOS LA VALIDACION SI LOS CAMPOS SON CORRECTOS CONTINUAMOS CON EL REGISTRO * */
        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_modulos', ['md_nombre' => $nombreModulo]);
            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un módulo registrado con el nombre ' . $nombreModulo . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'md_nombre' => mb_strtoupper($nombreModulo, 'UTF-8'),
                'md_estado' => $estadoModulo,
                'md_descripcion' => $descripcionModulo,
                'md_url' => $urlModulo,
                'md_tipo' => $tipoModulo,
                'md_icon' => $iconoModulo,
                'md_orden' => $ordenModulo,
                'md_padre' => (!empty($padreModulo) ? $padreModulo : null)
            ];

            $this->ccm->guardar($datos, 'cc_modulos');
            $response['status'] = 'success';
            $response['msg'] = '<h5>Módulo registrado exitosamente</h5>';
        } else {
            /*             * SI LA VALIDACION NO SE CUMPLE ENVIAMOS AL FRONT LOS DATOS INVALIDOS * */
            $response['status'] = 'vacio';
            $response['msg'] = [
                'nombreModulo' => $this->validation->getError('nombreModulo'),
                'padreModulo' => $this->validation->getError('padreModulo'),
                'iconoModulo' => $this->validation->getError('iconoModulo'),
                'urlModulo' => $this->validation->getError('urlModulo'),
                'ordenModulo' => $this->validation->getError('ordenModulo'),
            ];
        }
        /*         * ENVIO LA RESPUESTA AL FRONT */
        return $this->response->setJson($response);
    }

    public function updateModulo() {

        /*         * OBTENEMOS LOS DATOS DEL FRONT * */
        $nombreModulo = $this->request->getPost('nombreModulo');
        $descripcionModulo = $this->request->getPost('descripcionModulo');
        $estadoModulo = $this->request->getPost('estadoModulo');
        $urlModulo = $this->request->getPost('urlModulo');
        $tipoModulo = $this->request->getPost('tipoModulo');
        $iconoModulo = $this->request->getPost('iconoModulo');
        $ordenModulo = $this->request->getPost('ordenModulo');
        $padreModulo = $this->request->getPost('padreModulo');

        /* DATOS PARA EL UPDATE */
        $nameAux = $this->request->getPost('nameAux');
        $idModulo = $this->request->getPost('idModulo');

        /*         * VALIDAMOS LOS DATOS DEL FRONT (QUE CNO ESTEN VACIOS) * */
        if ($tipoModulo == 'submodulo') {
            $this->validation->setRules([
                'nombreModulo' => ['label' => 'Nombre Módulo', 'rules' => 'trim|required'],
                'urlModulo' => ['label' => 'URL Módulo', 'rules' => 'trim|required'],
                'iconoModulo' => ['label' => 'Ícono Módulo', 'rules' => 'trim|required'],
                'tipoModulo' => ['label' => 'Tipo Módulo', 'rules' => 'trim|required'],
                'ordenModulo' => ['label' => 'Órden Módulo', 'rules' => 'trim|required'],
                'padreModulo' => ['label' => 'Módulo Parde', 'rules' => 'trim|required']
            ]);
        } else {
            $this->validation->setRules([
                'nombreModulo' => ['label' => 'Nombre Módulo', 'rules' => 'trim|required'],
                'urlModulo' => ['label' => 'URL Módulo', 'rules' => 'trim|required'],
                'iconoModulo' => ['label' => 'Ícono Módulo', 'rules' => 'trim|required'],
                'tipoModulo' => ['label' => 'Tipo Módulo', 'rules' => 'trim|required'],
                'ordenModulo' => ['label' => 'Órden Módulo', 'rules' => 'trim|required'],
            ]);
        }
        /*         * VALIDAMOS LA VALIDACION SI LOS CAMPOS SON CORRECTOS CONTINUAMOS CON EL REGISTRO * */
        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_modulos', ['md_nombre' => $nombreModulo], '*', $order = null, 1);
            if ($existe && $existe->md_nombre != $nameAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un módulo registrado con el nombre ' . $nombreModulo . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'md_nombre' => mb_strtoupper($nombreModulo, 'UTF-8'),
                'md_estado' => $estadoModulo,
                'md_descripcion' => $descripcionModulo,
                'md_url' => $urlModulo,
                'md_tipo' => $tipoModulo,
                'md_icon' => $iconoModulo,
                'md_orden' => $ordenModulo,
                'md_padre' => ($padreModulo !=='null'?$padreModulo : null)
            ];

            $this->ccm->actualizar('cc_modulos', $datos, ['id' => $idModulo]);
            $response['status'] = 'success';
            $response['msg'] = '<h5>Módulo Actualizado exitosamente</h5>';
        } else {
            /*             * SI LA VALIDACION NO SE CUMPLE ENVIAMOS AL FRONT LOS DATOS INVALIDOS * */
            $response['status'] = 'vacio';
            $response['msg'] = [
                'nombreModulo' => $this->validation->getError('nombreModulo'),
                'padreModulo' => $this->validation->getError('padreModulo'),
                'iconoModulo' => $this->validation->getError('iconoModulo'),
                'urlModulo' => $this->validation->getError('urlModulo'),
                'ordenModulo' => $this->validation->getError('ordenModulo'),
            ];
        }
        /*         * ENVIO LA RESPUESTA AL FRONT */
        return $this->response->setJson($response);
    }

    public function getAcciones() {
        $response = $this->admModel->getAcciones();
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }

    public function getSubModulo() {
        $data = json_decode(file_get_contents('php://input'));
        $respuesta = $this->ccm->getData('cc_modulos', ['md_padre' => $data->idModulo, 'md_estado' => 1]);
        return $this->response->setJSON($respuesta);
    }

    public function saveAccion() {

        /*         * OBTENEMOS LOS DATOS DEL FRONT * */
        $nombreAccion = $this->request->getPost('nombreAccion');
        $detalleAccion = $this->request->getPost('detalleAccion');
        $estado = $this->request->getPost('estado');
        $moduloAccion = $this->request->getPost('moduloAccion');
        $subModuloAccion = $this->request->getPost('subModuloAccion');

        /*         * VALIDAMOS LOS DATOS DEL FRONT (QUE CNO ESTEN VACIOS) * */
        $this->validation->setRules([
            'nombreAccion' => ['label' => 'Nombre Acción', 'rules' => 'trim|required'],
            'detalleAccion' => ['label' => 'Detalle Acción', 'rules' => 'trim|required'],
            'moduloAccion' => ['label' => 'Módulo Acción', 'rules' => 'trim|required'],
            'subModuloAccion' => ['label' => ' SubMódulo Acción', 'rules' => 'trim|required'],
        ]);

        /*         * VALIDAMOS LA VALIDACION SI LOS CAMPOS SON CORRECTOS CONTINUAMOS CON EL REGISTRO * */
        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_acciones', ['ac_nombre' => $nombreAccion]);
            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe una acción registrado con el nombre ' . $nombreAccion . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'ac_nombre' => $nombreAccion,
                'ac_estado' => $estado,
                'ac_detalle' => $detalleAccion,
                'fk_modulo' => $moduloAccion,
                'fk_submodulo' => $subModuloAccion,
                'ac_fecha_create' => date('Y-m-d'),
            ];

            $this->ccm->guardar($datos, 'cc_acciones');
            $response['status'] = 'success';
            $response['msg'] = '<h5>Acción registrado exitosamente</h5>';
        } else {
            /*             * SI LA VALIDACION NO SE CUMPLE ENVIAMOS AL FRONT LOS DATOS INVALIDOS * */
            $response['status'] = 'vacio';
            $response['msg'] = [
                'nombreAccion' => $this->validation->getError('nombreAccion'),
                'detalleAccion' => $this->validation->getError('padreModulo'),
                'moduloAccion' => $this->validation->getError('detalleAccion'),
                'subModuloAccion' => $this->validation->getError('subModuloAccion'),
            ];
        }
        /*         * ENVIO LA RESPUESTA AL FRONT */
        return $this->response->setJson($response);
    }

    public function updateAccion() {
        /*         * OBTENEMOS LOS DATOS DEL FRONT * */
        $nombreAccion = $this->request->getPost('nombreAccion');
        $detalleAccion = $this->request->getPost('detalleAccion');
        $estado = $this->request->getPost('estado');
        $moduloAccion = $this->request->getPost('moduloAccion');
        $subModuloAccion = $this->request->getPost('subModuloAccion');

        /* DATOS PARA EL UPDATE */
        $nameAux = $this->request->getPost('nameAux');
        $idAccion = $this->request->getPost('idAccion');

        /*         * VALIDAMOS LOS DATOS DEL FRONT (QUE CNO ESTEN VACIOS) * */
        $this->validation->setRules([
            'nombreAccion' => ['label' => 'Nombre Acción', 'rules' => 'trim|required'],
            'detalleAccion' => ['label' => 'Detalle Acción', 'rules' => 'trim|required'],
            'moduloAccion' => ['label' => 'Módulo Acción', 'rules' => 'trim|required'],
            'subModuloAccion' => ['label' => ' SubMódulo Acción', 'rules' => 'trim|required'],
        ]);

        /*         * VALIDAMOS LA VALIDACION SI LOS CAMPOS SON CORRECTOS CONTINUAMOS CON EL REGISTRO * */
        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_acciones', ['ac_nombre' => $nombreAccion], '*', $order = null, 1);
            if ($existe && $existe->ac_nombre != $nameAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe una acción registrado con el nombre ' . $nombreAccion . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'ac_nombre' => $nombreAccion,
                'ac_estado' => $estado,
                'ac_detalle' => $detalleAccion,
                'fk_modulo' => $moduloAccion,
                'fk_submodulo' => $subModuloAccion,
                'ac_fecha_create' => date('Y-m-d'),
            ];

            $this->ccm->actualizar('cc_acciones', $datos, ['id' => $idAccion]);
            $response['status'] = 'success';
            $response['msg'] = '<h5>Acción actualizada exitosamente</h5>';
        } else {
            /*             * SI LA VALIDACION NO SE CUMPLE ENVIAMOS AL FRONT LOS DATOS INVALIDOS * */
            $response['status'] = 'vacio';
            $response['msg'] = [
                'nombreAccion' => $this->validation->getError('nombreAccion'),
                'detalleAccion' => $this->validation->getError('padreModulo'),
                'moduloAccion' => $this->validation->getError('detalleAccion'),
                'subModuloAccion' => $this->validation->getError('subModuloAccion'),
            ];
        }
        /*         * ENVIO LA RESPUESTA AL FRONT */
        return $this->response->setJson($response);
    }
}
