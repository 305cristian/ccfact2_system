<?php

namespace Modules\Compras\Controllers;

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
 * @date 3 may 2026
 * @time 3:40:05 p.m.
 */
class IndexController extends BaseController {

    protected $dirViewModule;

    public function __construct() {

        $this->dirViewModule = 'Modules\Compras\Views';
    }

    public function index($moduloId) {
        $this->user->validateSession();
        $data['moduloId'] = $moduloId;
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewDashboard', $data);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($send);
        } else {
            return view($this->dirTemplate . '\dashboard', $send);
        }
    }

    public function nuevaCompra() {
        $view = $this->parametrosIndex();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($view);
        } else {
            return view($this->dirTemplate . '\dashboard', $view);
        }
    }

    public function nuevaCompraEdit($compraId) {
        $view = $this->parametrosIndex($compraId);
        return view($this->dirTemplate . '\dashboard', $view);
    }

    public function parametrosIndex($compraId = null) {

        $this->user->validateSession();
        $data['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $data);

        $data['listaTiposCompra'] = $this->ccm->getData('cc_tipo_compra', ['tc_estado' => 1], 'id, tc_nombre');
        $data['listaFormasPago'] = $this->ccm->getData('cc_formas_pago', ['fp_estado' => 1], 'cod, fp_nombre');
        $data['listaTiposComprobantes'] = $this->ccm->getData('cc_tipos_comprobante', ['comp_estado' => 1], 'comp_codigo, comp_nombre, id');

        $data['listaSustentos'] = $this->ccm->getData('cc_sustentos', ['sus_estado' => 1], 'sus_codigo, sus_nombre');
        $data['listaBodegas'] = $this->ccm->getData('cc_bodegas', ['bod_estado' => 1], 'id, bod_nombre');
        $data['listaCentroCostos'] = $this->ccm->getData('cc_centroscosto', ['cc_estado' => 1], 'id, cc_nombre');

        $bodegaMainUsuario = bodegaMain($this->user->id);

        $data['bodegaId'] = $this->session->get('bodegaIdComp') ? $this->session->get('bodegaIdComp') : $bodegaMainUsuario;

        $data['permitirDuplicados'] = getSettings('PERMITIR_ITEMS_DUPLICADOS');
        $data['dataCompra'] = null;
        $data['dataProveedor'] = null;

        if (!empty($compraId)) {
            $data['dataCompra'] = $this->ccm->getData('cc_compras', ['id' => $compraId], '*', null, 1);
            $data['dataProveedor'] = $this->searchModel->searchProveedorById($data['dataCompra']->fk_proveedor);
        }

        $send['view'] = view($this->dirViewModule . '\viewNewCompra', $data);

        return $send;
    }
}
