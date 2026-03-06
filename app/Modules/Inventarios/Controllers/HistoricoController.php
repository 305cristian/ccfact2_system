<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Inventarios\Controllers;

/**
 * Description of HistoricoController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 3 mar 2026
 * @time 9:34:19 p.m.
 */
class HistoricoController extends \App\Controllers\BaseController {

    //put your code here

    protected $dirViewModule;

    public function __construct() {
        $this->dirViewModule = 'Modules\Inventarios\Views';
    }

    public function index() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $send['sidebar'] = view($this->dirViewModule . '\Existencias\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\Existencias\historico\viewHistorico', $data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }
}
