<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Inventarios\Controllers;

/**
 * Description of KardexController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 9 mar 2026
 * @time 8:11:28 a.m.
 */
class KardexController extends \App\Controllers\BaseController {

    //put your code here
    protected $dirViewModule;

    public function __construct() {
        $this->dirViewModule = 'Modules\Inventarios\Views';
    }

    public function index() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data2['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $send['sidebar'] = view($this->dirViewModule . '\Kardex\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\Kardex\viewDashboard', $data2);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function viewKardexProducto() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data2['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $send['sidebar'] = view($this->dirViewModule . '\Kardex\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\Kardex\producto\viewProductos', $data2);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function viewKardexLote() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data2['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $send['sidebar'] = view($this->dirViewModule . '\Kardex\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\Kardex\lotes\viewLotes', $data2);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function viewKardexGeneral() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data2['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $send['sidebar'] = view($this->dirViewModule . '\Kardex\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\Kardex\general\viewGeneral', $data2);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }
}
