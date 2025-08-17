<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */


/**
 * Description of WelcomeController
 * @author Cristian R. Paz
 * @Date 31 ene. 2023
 * @Time 17:03:11
 */

namespace Modules\Login\Controllers;

use Modules\Comun\Libraries\Ip;

class WelcomeController extends \App\Controllers\BaseController {

    protected $ip;
    protected $dirTemplate;
    protected $viewRutaModule;

    //put your code here
    public function __construct() {

        //LIBRERIAS
        $this->ip = new Ip();

        //MODELOS
        //RUTAS
        $this->dirTemplate = '\Modules\Comun\Views\template';
        $this->viewRutaModule = '\Modules\Login\Views';
    }

    public function index() {
        $request = service('request');
        $this->user->validateSession();
        $this->registerSessionDb();
        $send['title'] = 'CCFACT';
//      $send['sidebar']= view($this->dirTemplate.'\sidebar');
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['otrosListaModulos'] = $this->ccm->getData('cc_modulos', ['md_estado' => 0, 'md_tipo' => 'modulo']);

        $send['view'] = view($this->viewRutaModule . '\viewStartSystem', $data);
        $send['user'] = $this->user;
        $send['ccm'] = $this->ccm;
        $send['pathname'] = $request->getUri()->getPath();
        return view($this->dirTemplate . '\dashboard', $send);
    }

    public function registerSessionDb() {
        $data = [
            'id_user' => $this->user->id,
            'fecha_login' => date('Y-m-d'),
            'hora_login' => date('H:i:s'),
            'ip_address' => $this->ip->getIp(),
        ];
        $this->ccm->guardar($data, 'cc_login_system');
        $this->logs->logInfo('SE HA INICIADO SECION');
    }

    public function closeSession() {

        $log = 'LOGOUT DEL SISTEMA CON EN EL USUARIO DE ID : ' . $this->user->id;
        $this->logs->logInfo($log);

        $this->session->destroy();
        return redirect('welcome');
    }
}
