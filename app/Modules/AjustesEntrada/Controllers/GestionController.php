<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\AjustesEntrada\Controllers;

/**
 * Description of GestionController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 22 oct 2025
 * @time 2:57:09 p.m.
 */
use Modules\AjustesEntrada\Models\EntradasModel;

class GestionController extends \App\Controllers\BaseController {

    protected $dirViewModule;
    protected $entadasModel;

    public function __construct() {

        $this->dirViewModule = 'Modules\AjustesEntrada\Views';

        //IMPORT MODELS
        $this->entadasModel = new EntradasModel();
    }

    public function index() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);

        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaMotivos'] = $this->ccm->getData('cc_motivos_ajuste', ['mot_estado' => 1, 'mot_tipo' => "AJUSTES"], 'id, mot_nombre');
        $data['listaCentroCostos'] = $this->ccm->getData('cc_centroscosto', ['cc_estado' => 1], 'id, cc_nombre');

        $bodegaMainUsuario = $this->ccm->getValue('cc_bodegas', $this->user->id, 'id', 'id');

        $data['bodegaId'] = $this->session->get('bodegaIdAje') ? $this->session->get('bodegaIdAje') : $bodegaMainUsuario;
        $send['view'] = view($this->dirViewModule . '\viewGesionarAjuste', $data);

//        return view($this->dirTemplate . '\dashboard', $send);
        return $this->response->setJSON($send);
    }

    public function getAjustes() {

        $response = $this->entadasModel->getAjustes();

        if ($response) {
            return $this->response->setJSON($response);
        }

        return $this->response->setJSON(false);
    }

    public function getDataDetalle($idAjuste) {
        $response = $this->entadasModel->getDataDetalle($idAjuste);

        if ($response) {
            return $this->response->setJSON($response);
        }

        return $this->response->setJSON(false);
    }
}
