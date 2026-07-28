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
use Modules\Login\Models\LoginModel;

class WelcomeController extends \App\Controllers\BaseController {

    protected $ip;
    protected LoginModel $loginModel;
    protected $dirTemplate;
    protected $viewRutaModule;

    //put your code here
    public function __construct() {

        //LIBRERIAS
        $this->ip = new Ip();

        //MODELOS
        $this->loginModel = new LoginModel();
        //RUTAS
        $this->dirTemplate = '\Modules\Comun\Views\template';
        $this->viewRutaModule = '\Modules\Login\Views';
    }

    public function index() {
        $request = service('request');
        $this->user->validateSession();
        
        if (empty($this->session->get('login_registrado'))) {
            $this->registerSessionDb();
            $this->session->set('login_registrado', 1);
        }
        
        $send['title'] = 'CCFACT';

        $data['seleccionarProyecto'] = empty($this->session->get('fk_proyecto'));
        $data['listaProyectosEmpleado'] = [];
        
        if ($data['seleccionarProyecto']) {
            $data['listaProyectosEmpleado'] = $this->loginModel->getProyectosEmpleado((int) $this->user->id);
        }
        
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['otrosListaModulos'] = $this->ccm->getData('cc_modulos', ['md_estado' => 0, 'md_tipo' => 'modulo']);

        $send['view'] = view($this->viewRutaModule . '\viewStartSystem', $data);
        $send['user'] = $this->user;
        $send['ccm'] = $this->ccm;
        $send['pathname'] = $request->getUri()->getPath();
        return view($this->dirTemplate . '\dashboard', $send);
    }

    public function seleccionarProyecto() {

        $this->user->validateSession();

        $proyectoId = (int) $this->request->getPost('fk_proyecto');

        if ($proyectoId <= 0) {
            $this->session->set('message', 'Debe seleccionar un proyecto.');
            return redirect('welcome');
        }

        $proyecto = $this->loginModel->getProyectoEmpleado((int) $this->user->id, $proyectoId);

        if (!$proyecto) {
            $this->session->set('message', 'No tiene permisos para acceder al proyecto seleccionado.');
            return redirect('welcome');
        }

        $this->session->set([
            'fk_proyecto' => (int) $proyecto->id,
            'proy_codigo' => $proyecto->proy_codigo,
            'proy_nombre' => $proyecto->proy_nombre,
        ]);

        $redirectUrl = trim((string) $this->request->getPost('redirectUrl'));

        if ($redirectUrl !== '' && str_starts_with($redirectUrl, site_url())) {
            return redirect()->to($redirectUrl);
        }

        return redirect('welcome');
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
