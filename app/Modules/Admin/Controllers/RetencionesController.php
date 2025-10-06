<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Admin\Controllers;

/**
 * Description of ContabilidadController
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 2 oct 2025
 * @time 9:09:39 a.m.
 */
class RetencionesController extends \App\Controllers\BaseController {

    //put your code here

    public function __construct() {
        $this->dirViewModule = 'Modules\Admin\Views';
    }

    public function index() {
        $this->user->validateSession();
        $mod['listaModulos'] = $this->modMod->getModulosUser($this->user);
        $data['user'] = $this->user; //Esto se envia a la vista para validar roles y permisos
        $send['sidebar'] = view($this->dirViewModule . '\sidebar', $mod);
        $send['view'] = view($this->dirViewModule . '\retenciones\viewRetenciones', $data);
//        $send['user'] = $this->user;//Estos datos se envian solo si se renderiza en el dashboars
//        $send['ccm'] = $this->ccm;//Estos datos se envian solo si se renderiza en el dashboars
//        return view($this->dirTemplate . '\dashboard', $send);
        return $this->response->setJSON($send);
    }

    public function getRetenciones() {

        $response = $this->ccm->getData('cc_retencion_sri');
        if ($response) {
            return $this->response->setJSON($response);
        } else {
            return $this->response->setJSON(false);
        }
    }

    public function saveRetenciones() {
        $retCodigo = $this->request->getPost('retCodigo');
        $retNombre = $this->request->getPost('retNombre');
        $retPorcentaje = $this->request->getPost('retPorcentaje');
        $retCtaCompras = $this->request->getPost('retCtaCompras');
        $retCtaVentas = $this->request->getPost('retCtaVentas');
        $retImpuesto = $this->request->getPost('retImpuesto');
        $retValCompra = $this->request->getPost('retValCompra');
        $retValVenta = $this->request->getPost('retValVenta');

        $this->validation->setRules([
            'retCodigo' => ['label' => 'Código', 'rules' => 'trim|required'],
            'retNombre' => ['label' => 'Nombre', 'rules' => 'trim|required'],
            'retPorcentaje' => ['label' => 'Porcentaje', 'rules' => 'trim|required'],
            'retCtaCompras' => ['label' => 'Cuenta Contable Compras', 'rules' => 'trim|required'],
            'retCtaVentas' => ['label' => 'Cuenta Contable Ventas', 'rules' => 'trim|required'],
            'retValCompra' => ['label' => 'Retención Valor Compra', 'rules' => 'trim|required'],
            'retValVenta' => ['label' => 'Retención Valor Venta', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {
            $existe = $this->ccm->getData('cc_retencion_sri', ['ret_codigo' => $retCodigo]);

            if (count($existe) > 0) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe una retención con el código ' . $retCodigo . '</h5>';
                return $this->response->setJSON($response);
            }

            $datos = [
                'ret_codigo' => $retCodigo,
                'ret_nombre' => mb_strtoupper($retNombre, 'UTF-8'),
                'ret_porcentaje' => $retPorcentaje,
                'ret_cta_compras' => $retCtaCompras,
                'ret_cta_ventas' => $retCtaVentas,
                'ret_impuesto' => $retImpuesto,
                'ret_val_compra' => $retValCompra,
                'ret_val_venta' => $retValVenta,
            ];

            $save = $this->ccm->guardar($datos, 'cc_retencion_sri');
            $this->logs->logSuccess('SE HA REGISTRADO UNA RETENCION CON EL ID ' . $save);

            $response['status'] = 'success';
            $response['msg'] = '<h5>Retención registrada exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'retCodigo' => $this->validation->getError('retCodigo'),
                'retNombre' => $this->validation->getError('retNombre'),
                'retPorcentaje' => $this->validation->getError('retPorcentaje'),
                'retCtaCompras' => $this->validation->getError('retCtaCompras'),
                'retCtaVentas' => $this->validation->getError('retCtaVentas'),
                'retValCompra' => $this->validation->getError('retValCompra'),
                'retValVenta' => $this->validation->getError('retValVenta'),
            ];
        }

        return $this->response->setJSON($response);
    }

    public function updateRetenciones() {
        $retCodigo = $this->request->getPost('retCodigo');
        $retNombre = $this->request->getPost('retNombre');
        $retPorcentaje = $this->request->getPost('retPorcentaje');
        $retCtaCompras = $this->request->getPost('retCtaCompras');
        $retCtaVentas = $this->request->getPost('retCtaVentas');
        $retImpuesto = $this->request->getPost('retImpuesto');
        $retValCompra = $this->request->getPost('retValCompra');
        $retValVenta = $this->request->getPost('retValVenta');

        $idRet = $this->request->getPost('idRet');
        $codeAux = $this->request->getPost('codeAux');

        $this->validation->setRules([
            'retCodigo' => ['label' => 'Código', 'rules' => 'trim|required'],
            'retNombre' => ['label' => 'Nombre', 'rules' => 'trim|required'],
            'retPorcentaje' => ['label' => 'Porcentaje', 'rules' => 'trim|required'],
            'retCtaCompras' => ['label' => 'Cuenta Contable Compras', 'rules' => 'trim|required'],
            'retCtaVentas' => ['label' => 'Cuenta Contable Ventas', 'rules' => 'trim|required'],
            'retValCompra' => ['label' => 'Retención Valor Compra', 'rules' => 'trim|required'],
            'retValVenta' => ['label' => 'Retención Valor Venta', 'rules' => 'trim|required'],
        ]);

        if ($this->validation->withRequest($this->request)->run()) {
            $existe = $this->ccm->getData('cc_retencion_sri', ['ret_codigo' => $retCodigo], '*', $orderBy = null, 1);

            if ($existe && $existe->ret_codigo != $codeAux) {
                $response['status'] = 'existe';
                $response['msg'] = '<h5>Ya existe una retención con el código ' . $retCodigo . '</h5>';
                return $this->response->setJSON($response);
            }

            $datos = [
                'ret_codigo' => $retCodigo,
                'ret_nombre' => mb_strtoupper($retNombre, 'UTF-8'),
                'ret_porcentaje' => $retPorcentaje,
                'ret_cta_compras' => $retCtaCompras,
                'ret_cta_ventas' => $retCtaVentas,
                'ret_impuesto' => $retImpuesto,
                'ret_val_compra' => $retValCompra,
                'ret_val_venta' => $retValVenta,
            ];

            $this->ccm->actualizar('cc_retencion_sri', $datos, ['id' => $idRet]);

            $response['status'] = 'success';
            $response['msg'] = '<h5>Retención actualizada exitosamente</h5>';
        } else {
            $response['status'] = 'vacio';
            $response['msg'] = [
                'retCodigo' => $this->validation->getError('retCodigo'),
                'retNombre' => $this->validation->getError('retNombre'),
                'retPorcentaje' => $this->validation->getError('retPorcentaje'),
                'retCtaCompras' => $this->validation->getError('retCtaCompras'),
                'retCtaVentas' => $this->validation->getError('retCtaVentas'),
                'retValCompra' => $this->validation->getError('retValCompra'),
                'retValVenta' => $this->validation->getError('retValVenta'),
            ];
        }

        return $this->response->setJSON($response);
    }
}
