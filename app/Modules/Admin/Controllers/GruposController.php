<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of GruposController
 *
  /**
 * @author CRISTIAN PAZ
 * @date 15 abr. 2024
 * @time 11:47:59
 */
use Modules\Admin\Models\GruposModel;

class GruposController extends \App\Controllers\BaseController {

    protected $dirViewModule;
    protected $modelGrupos;

    public function __construct() {
        $this->dirViewModule = 'Modules\Admin\Views';
        $this->modelGrupos = new GruposModel();
    }

    public function index() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user;
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\grupossubgrupos\viewGruposSubgrupos');
        $send['user'] = $this->user;
        $send['ccm'] = $this->ccm;
        return $this->response->setJSON($send);
    }

    public function getGrupos() {
        $response = $this->ccm->getData('cc_grupos');
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }
     public function getSubgrupoByGrupo() {
        $data = json_decode(file_get_contents("php://input"));
        
        $response = $this->ccm->getData('cc_subgrupos', ['fk_grupo' => $data->idGrupo], '*');
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }

    public function saveGrupo() {
        $grNombre = $this->request->getPost('grNombre');
        $grDescripcion = $this->request->getPost('grDescripcion');
        $grIcon = $this->request->getPost('grIcon');
        $grEstado = $this->request->getPost('grEstado');

        $this->validation->setRules([
            'grNombre' => ['label' => 'Nombre Grupo', 'rules' => 'trim|required'],
            'grDescripcion' => ['label' => 'Descripcion grupo', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_grupos', ['gr_nombre' => $grNombre]);
            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un grupo registrado con el nombre ' . $grNombre . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'gr_nombre' => mb_strtoupper($grNombre, 'UTF-8'),
                'gr_descripcion' => mb_strtoupper($grDescripcion, 'UTF-8'),
                'gr_estado' => $grEstado,
                'gr_icon' => $grIcon,
                'gr_fecha_creacion' => date('Y-m-d')
            ];

            $grSave = $this->ccm->guardar($datos, 'cc_grupos');
            $this->logs->logSuccess('SE HA CREADO UN GRUPO CON EL ID ' . $grSave);
            $response['status'] = 'success';
            $response['msg'] = '<h5>Grupo registrado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'grNombre' => $this->validation->getError('grNombre'),
                'grDescripcion' => $this->validation->getError('grDescripcion'),
            ];
        }
        return $this->response->setJson($response);
    }

    public function updateGrupo() {
        $grNombre = $this->request->getPost('grNombre');
        $grDescripcion = $this->request->getPost('grDescripcion');
        $grIcon = $this->request->getPost('grIcon');
        $grEstado = $this->request->getPost('grEstado');

        $idGrupo = $this->request->getPost('idGrupo');
        $nameAux = $this->request->getPost('nameAux');

        $this->validation->setRules([
            'grNombre' => ['label' => 'Nombre Grupo', 'rules' => 'trim|required'],
            'grDescripcion' => ['label' => 'Descripcion grupo', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_grupos', ['gr_nombre' => $grNombre], '*', $orderBy = null, 1);

            if ($existe && $existe->gr_nombre != $nameAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un grupo registrado con el nombre ' . $grNombre . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'gr_nombre' => mb_strtoupper($grNombre, 'UTF-8'),
                'gr_descripcion' => mb_strtoupper($grDescripcion, 'UTF-8'),
                'gr_estado' => $grEstado,
                'gr_icon' => $grIcon,
                'gr_fecha_creacion' => date('Y-m-d')
            ];

            $this->ccm->actualizar('cc_grupos', $datos, ['id' => $idGrupo]);

            $this->logs->logSuccess('SE HA ACTUAÑLIZADO UN GRUPO CON EL ID ' . $idGrupo);
            $response['status'] = 'success';
            $response['msg'] = '<h5>Grupo actualizado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'grNombre' => $this->validation->getError('grNombre'),
                'grDescripcion' => $this->validation->getError('grDescripcion'),
            ];
        }
        return $this->response->setJson($response);
    }

    public function getSubGrupos() {
        $response = $this->modelGrupos->getSubgrupos();
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }

    public function saveSubGrupo() {
        $sgrNombre = $this->request->getPost('sgrNombre');
        $sgrDetalle = $this->request->getPost('sgrDetalle');
        $sgrIcon = $this->request->getPost('sgrIcon');
        $sgrGrupo = $this->request->getPost('sgrGrupo');
        $sgrEstado = $this->request->getPost('sgrEstado');

        $this->validation->setRules([
            'sgrNombre' => ['label' => 'Nombre subGrupo', 'rules' => 'trim|required'],
            'sgrDetalle' => ['label' => 'Detalle subgrupo', 'rules' => 'trim|required'],
            'sgrGrupo' => ['label' => 'Grupo', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_subgrupos', ['sgr_nombre' => $sgrNombre]);
            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un subgrupo registrado con el nombre ' . $sgrNombre . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'sgr_nombre' => mb_strtoupper($sgrNombre, 'UTF-8'),
                'sgr_detalle' => mb_strtoupper($sgrDetalle, 'UTF-8'),
                'sgr_estado' => $sgrEstado,
                'sgr_icon' => $sgrIcon,
                'fk_grupo' => $sgrGrupo,
                'sgr_fecha_creacion' => date('Y-m-d')
            ];

            $sgrSave = $this->ccm->guardar($datos, 'cc_subgrupos');
            $this->logs->logSuccess('SE HA CREADO UN SUBGRUPO CON EL ID ' . $sgrSave);
            $response['status'] = 'success';
            $response['msg'] = '<h5>SubGrupo registrado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'sgrNombre' => $this->validation->getError('sgrNombre'),
                'sgrDetalle' => $this->validation->getError('sgrDetalle'),
                'sgrGrupo' => $this->validation->getError('sgrGrupo'),
            ];
        }
        return $this->response->setJson($response);
    }

    public function updateSubGrupo() {
        $sgrNombre = $this->request->getPost('sgrNombre');
        $sgrDetalle = $this->request->getPost('sgrDetalle');
        $sgrIcon = $this->request->getPost('sgrIcon');
        $sgrGrupo = $this->request->getPost('sgrGrupo');
        $sgrEstado = $this->request->getPost('sgrEstado');

        $idSubGrupo = $this->request->getPost('idSubGrupo');
        $nameAux = $this->request->getPost('nameAux');

        $this->validation->setRules([
            'sgrNombre' => ['label' => 'Nombre subGrupo', 'rules' => 'trim|required'],
            'sgrDetalle' => ['label' => 'Detalle subgrupo', 'rules' => 'trim|required'],
            'sgrGrupo' => ['label' => 'Grupo', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {

            $existe = $this->ccm->getData('cc_subgrupos', ['sgr_nombre' => $sgrNombre], '*', $orderBy = null, 1);

            if ($existe && $existe->sgr_nombre != $nameAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe un subgrupo registrado con el nombre ' . $sgrNombre . '</h5>';
                return $this->response->setJson($response);
            }

            $datos = [
                'sgr_nombre' => mb_strtoupper($sgrNombre, 'UTF-8'),
                'sgr_detalle' => mb_strtoupper($sgrDetalle, 'UTF-8'),
                'sgr_estado' => $sgrEstado,
                'sgr_icon' => $sgrIcon,
                'fk_grupo' => $sgrGrupo,
                'sgr_fecha_creacion' => date('Y-m-d')
            ];

            $this->ccm->actualizar('cc_subgrupos', $datos, ['id' => $idSubGrupo]);

            $this->logs->logSuccess('SE HA ACTUALIZADO UN SUBGRUPO CON EL ID ' . $idSubGrupo);
            $response['status'] = 'success';
            $response['msg'] = '<h5>SubGrupo actualizado exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'sgrNombre' => $this->validation->getError('sgrNombre'),
                'sgrDetalle' => $this->validation->getError('sgrDetalle'),
                'sgrGrupo' => $this->validation->getError('sgrGrupo'),
            ];
        }
        return $this->response->setJson($response);
    }
}
