<?php

namespace Modules\NotasCredito\Controllers;

use App\Controllers\BaseController;
use App\Models\CcModel;
use Modules\NotasCredito\Models\NotaCreditoModel;

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of IndexController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 15 jul 2026
 * @time 3:00:48 p.m.
 */
class IndexController extends BaseController {

    //put your code here
    protected $gm;
    protected string $dirViewModule;
    protected NotaCreditoModel $notaCreditoModel;

    public function __construct() {
        $this->dirViewModule = 'Modules\NotasCredito\Views';
        $this->gm = new CcModel();
        $this->notaCreditoModel = new NotaCreditoModel();
    }

    public function index(?int $compraId = null) {

        $this->user->validateSession();

        if (empty($compraId)) {
            return redirect()->to(site_url('compras/gestionCompras'));
        }

        $compra = $this->notaCreditoModel->getCompraBaseNotaCredito($compraId);

        if (!$compra) {
            return redirect()->to(site_url('compras/gestionCompras'));
        }

        $data['title'] = "Nota de Credito";
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['compra'] = $compra;

        $send['sidebar'] = view('Modules\Compras\Views\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewNotaCredito', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
    }
}
