<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Comun\Controllers;

/**
 * Description of Index_controller
 * @author Cristian R. Paz
 * @Date 29 ene. 2023
 * @Time 17:22:52
 */
class IndexController extends \App\Controllers\BaseController {

    public function __construct() {
        
    }

    public function index() {
//         $this->user->validateSession();
    }

    public function getProvincias() {
        $response = $this->ccm->getData('cc_provincia');
        if ($response) {
            return $this->response->setJSON($response);
        }
        return $this->response->setJSON(false);
    }

    public function getCantones() {
        $response = $this->ccm->getData('cc_canton');
        if ($response) {
            return $this->response->setJSON($response);
        }
        return $this->response->setJSON(false);
    }

    public function getParroquias() {
        $response = $this->ccm->getData('cc_parroquia');
        if ($response) {
            return $this->response->setJSON($response);
        }
        return $this->response->setJSON(false);
    }

    public function getCantonesByProvincia($idProvincia) {
        $response = $this->ccm->getData('cc_canton', ['fk_provincia' => $idProvincia]);
        if ($response) {
            return $this->response->setJSON($response);
        }
        return $this->response->setJSON(false);
    }

    public function getParroquiasByCanton($idCanton) {
        $response = $this->ccm->getData('cc_parroquia', ['fk_canton' => $idCanton]);
        if ($response) {
            return $this->response->setJSON($response);
        }
        return $this->response->setJSON(false);
    }

}
