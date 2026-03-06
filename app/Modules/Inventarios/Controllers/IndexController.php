<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of IndexController
 * @author Cristian R. Paz
 * @Date 27 sep. 2023
 * @Time 17:15:39
 */

namespace Modules\Inventarios\Controllers;

class IndexController extends \App\Controllers\BaseController {

    protected $dirViewModule;

    public function __construct() {

        $this->dirViewModule = 'Modules\Inventarios\Views';
    }

    public function index($idMod) {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['listaSubModulos'] = $this->modMod->getSubModulosUser($idMod, $this->user);
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewIndex', $data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function viewExistencias() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $send['sidebar'] = view($this->dirViewModule . '\Existencias\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\Existencias\viewDashboard', $data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function viewInventarioGeneral() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data2['listaGrupos'] = $this->ccm->getData('cc_grupos', ['gr_estado' => 1], '*');
        $data2['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');

        $send['sidebar'] = view($this->dirViewModule . '\Existencias\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\Existencias\general\viewGeneral', $data2);
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function viewInventarioLotes() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data2['listaGrupos'] = $this->ccm->getData('cc_grupos', ['gr_estado' => 1], '*');
        $data2['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $send['sidebar'] = view($this->dirViewModule . '\Existencias\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\Existencias\lotes\viewLotes', $data2);
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function viewInventarioConsolidado() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data2['listaGrupos'] = $this->ccm->getData('cc_grupos', ['gr_estado' => 1], '*');
        $data2['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $send['sidebar'] = view($this->dirViewModule . '\Existencias\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\Existencias\consolidado\viewConsolidado', $data2);
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }
}
