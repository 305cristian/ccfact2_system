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

class Logger {

    protected $ccm;
    protected $user;

    public function __construct() {

        $this->ccm = service('ccModel');
        $this->user = service('userSecion');
    }

    //put your code here

    public function logInfo($log) {
        $this->log($log, 'INFO');
    }

    public function logError($log) {
        $this->log($log, 'ERROR');
    }

    public function logWarning($log) {
        $this->log($log, 'SUCCESS');
    }
    
    public function logSuccess($log) {
        $this->log($log, 'SUCCESS');
    }

    public function log($log, $log_status) {

        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $query = $_SERVER['QUERY_STRING'] ?? '';
        $log_dir = $uri . ($query ? '?' . $query : '');
        $array_log = array(
            'fk_user' => $this->user->id,
            'log_fecha' => date('Y-m-d'),
            'log_hora' => date('H:i:s'),
            'log_detail' => strtoupper($log),
            'log_dir' => $log_dir,
            'log_status' => strtoupper($log_status),
        );
        $this->ccm->guardar($array_log, 'cc_log');
    }
}
