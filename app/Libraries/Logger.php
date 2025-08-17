<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */


/**
 * Description of loggerLib
 * @author Cristian R. Paz
 * @Date 4 feb. 2023
 * @Time 14:50:14
 */

namespace app\Libraries;

class Logger extends \App\Controllers\BaseController {

    protected $ci;

    public function __construct() {

    }

    //put your code here

    public function logInfo($log) {
        $this->log($log, 'INFO');
    }

    public function logDanger($log) {
        $this->log($log, 'DANGER');
    }

    public function logSuccess($log) {
        $this->log($log, 'SUCCESS');
    }

    public function log($log, $log_status) {
        global $_CI4;

        $this->ci = $_CI4;
        $log_dir = $_SERVER["REQUEST_URI"] . $_SERVER["QUERY_STRING"];
        $array_log = array(
            'fk_user' => $this->ci->user->id,
            'log_fecha' => date('Y-m-d'),
            'log_hora' => date('H:i:s'),
            'log_detail' => strtoupper($log),
            'log_dir' => $log_dir,
            'log_status' => strtoupper($log_status),
        );
        $this->ci->ccm->guardar($array_log, 'cc_log');
    }

}
