<?php

namespace Modules\Ventas\Controllers;

use App\Controllers\BaseController;

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of DashboardController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 9 ago 2026
 * @time 8:26:02 a.m.
 */
class DashboardController extends BaseController {

    //put your code here
    //put your code here
    protected $dirViewModule;

    public function __construct() {
        $this->dirViewModule = 'Modules\Compras\Views';
    }
    
    public function index() {
        
    }
}
