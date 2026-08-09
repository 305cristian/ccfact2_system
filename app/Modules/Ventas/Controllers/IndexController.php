<?php

namespace Modules\Ventas\Controllers;
use App\Controllers\BaseController;

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of IndexController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 9 ago 2026
 * @time 8:25:42 a.m.
 */
class IndexController extends BaseController{
    //put your code here
    
      //put your code here
    protected $dirViewModule;

    public function __construct() {
        $this->dirViewModule = 'Modules\Compras\Views';
    }
}
