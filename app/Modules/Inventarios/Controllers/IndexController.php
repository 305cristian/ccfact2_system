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
        $data['listaSubModulos'] = $this->modMod->getSubModulosUser($idMod,$this->user);
        $send['sidebar'] = view($this->dirViewModule.'\sidebar', $data);
        $send['view'] = view($this->dirViewModule.'\viewIndex', $data);
        $send['user'] = $this->user;
        $send['ccm'] = $this->ccm;
        return view($this->dirTemplate . '\dashboard', $send);
    }

}
