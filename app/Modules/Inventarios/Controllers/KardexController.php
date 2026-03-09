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

    public function viewKardex() {
        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $send['sidebar'] = view($this->dirViewModule . '\Kardex\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\Kardex\viewDashboard', $data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }
}
