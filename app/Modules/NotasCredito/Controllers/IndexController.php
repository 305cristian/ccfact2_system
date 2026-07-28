<?php

namespace Modules\NotasCredito\Controllers;

use App\Controllers\BaseController;
use App\Models\CcModel;
use Modules\NotasCredito\Libraries\NotaCreditoLib;
use Modules\NotasCredito\Libraries\NotasCreditoAsientosLib;
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
    protected NotaCreditoLib $notaCreditoLib;
    protected NotasCreditoAsientosLib $notasCreditoAsientosLib;

    public function __construct() {
        $this->dirViewModule = 'Modules\NotasCredito\Views';
        $this->gm = new CcModel();
        $this->notaCreditoModel = new NotaCreditoModel();
        $this->notaCreditoLib = new NotaCreditoLib();
        $this->notasCreditoAsientosLib = new NotasCreditoAsientosLib();
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
        $data['listaSustentos'] = $this->ccm->getData('cc_sustentos', ['sus_estado' => 1], 'sus_codigo, sus_nombre');
        $data['listaTiposCompra'] = $this->ccm->getData('cc_tipo_compra', ['tc_estado' => 1], 'id, tc_nombre, tc_codigo');
        $data['tipoComprobanteNdc'] = $this->ccm->getData('cc_tipos_comprobante', ['comp_estado' => 1, 'comp_codigo' => '04'], 'id, comp_codigo, comp_nombre', null, 1);
        $data['listaProductosDescuento'] = $this->notaCreditoModel->getProductosDescuentoCompra();

        $send['sidebar'] = view('Modules\Compras\Views\sidebar', $data);
        $send['view'] = view($this->dirViewModule . '\viewNotaCredito', $data);

        return $this->request->isAJAX() ? $this->response->setJSON($send) : view($this->dirTemplate . '\dashboard', $send);
    }

    public function saveNotaCredito() {

        $this->user->validateSession();

        $dataPostNotaCredito = $this->request->getJSON();

        if (!$dataPostNotaCredito || !is_object($dataPostNotaCredito)) {
            return $this->responseSetJSON('warning', 'No se recibieron datos validos para procesar la nota de credito.');
        }

        $validacion = $this->validarDataNotaCredito($dataPostNotaCredito);

        if ($validacion['status']) {
            return $this->responseSetJSON('warning', $validacion['msg']);
        }

        if ($dataPostNotaCredito->compra->destinoFinanciero === 'ANTICIPO_PROVEEDOR') {
            return $this->responseSetJSON('warning', 'El flujo de anticipo a proveedor queda pendiente hasta crear sus tablas.');
        }

        if (!getPeriodoContable(date('Y-m-d'))) {
            return $this->responseSetJSON('error', '<h5>Revise el periodo de cierre</h5><h6>No se encontro un periodo contable habil para la fecha de emision.</h6>');
        }

        $this->db->transBegin();

        try {
            $compraId = $this->notaCreditoLib->guardarNotaCredito($dataPostNotaCredito);

            foreach ($dataPostNotaCredito->detalle as $item) {
                $this->notaCreditoLib->guardarDetalleNotaCredito($compraId, $item, $dataPostNotaCredito->compra);
                if (($dataPostNotaCredito->compra->compTipoNotaCredito) === 'DEVOLUCION') {
                    $this->notaCreditoLib->generarKardexItemNotaCredito($compraId, $item, $dataPostNotaCredito->compra);
                }
            }

            $this->notaCreditoLib->guardarBasesImpuesto($compraId, $dataPostNotaCredito->basesImpuestos ?? []);

            if ($dataPostNotaCredito->compra->destinoFinanciero === 'CXP') {
                $this->notaCreditoLib->aplicarNotaCreditoCuentaPorPagar($compraId, $dataPostNotaCredito);
                $this->notasCreditoAsientosLib->generarAsientoNotaCredito($compraId);
            }

            $secuencial = $this->ccm->getValueWhere('cc_compras', ['id' => $compraId], 'comp_secuencial');

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return $this->responseSetJSON('error', 'No se pudo registrar la nota de credito.');
            }

            $this->db->transCommit();

            return $this->responseSetJSON('success', 'Nota de credito registrada correctamente.', [
                        'id' => $compraId,
                        'comp_secuencial' => $secuencial,
            ]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return $this->responseSetJSON('error', 'Error al registrar la nota de credito: ' . $e->getMessage());
        }
    }

    public function anularNotaCredito() {

        $this->user->validateSession();

        $data = $this->request->getJSON();

        if (!is_object($data)) {
            return $this->responseSetJSON('warning', 'No se recibieron datos para anular la nota de credito.');
        }

        $notaCreditoId = (int) ($data->notaCreditoId ?? 0);
        $motivoAnulacion = trim((string) ($data->motivoAnulacion ?? ''));

        if ($notaCreditoId <= 0) {
            return $this->responseSetJSON('warning', 'El identificador de la nota de credito no es valido.');
        }

        if ($motivoAnulacion === '') {
            return $this->responseSetJSON('warning', 'Debe especificar el motivo de la anulacion.');
        }

        $notaCredito = $this->ccm->getData('cc_compras', ['id' => $notaCreditoId], 'id, comp_secuencial, comp_estado, comp_tipo_comprobante_cod, comp_tipo_nota_credito', null, 1);

        if (!$notaCredito) {
            return $this->responseSetJSON('warning', 'La nota de credito no se encuentra registrada.');
        }

        if ($notaCredito->comp_tipo_comprobante_cod !== '04') {
            return $this->responseSetJSON('warning', 'El documento seleccionado no corresponde a una nota de credito.');
        }

        if ($notaCredito->comp_estado !== 'ARCHIVADO') {
            return $this->responseSetJSON('warning', 'Solo se pueden anular notas de credito archivadas.');
        }

        if (!getPeriodoContable(date('Y-m-d'))) {
            return $this->responseSetJSON('error', '<h5>Revise el periodo de cierre</h5><h6>No se encontro un periodo contable habil para la fecha de anulacion.</h6>');
        }

        $this->db->transBegin();

        try {
            $this->notaCreditoLib->anularAplicacionCuentaPorPagarNotaCredito($notaCreditoId);
            
            if ($notaCredito->comp_tipo_nota_credito === 'DEVOLUCION') {
                $this->notaCreditoLib->revertirKardexNotaCredito($notaCreditoId);
            }
            $this->notasCreditoAsientosLib->anularAsientoNotaCredito($notaCreditoId);

            $anulado = $this->notaCreditoLib->anularNotaCreditoArchivada($notaCreditoId, $motivoAnulacion);

            if (!$anulado || $this->db->transStatus() === false) {
                throw new \RuntimeException('No se pudo actualizar el estado de la nota de credito.');
            }

            $this->db->transCommit();

            return $this->responseSetJSON('success', "Nota de credito #{$notaCredito->comp_secuencial} anulada correctamente.", [
                        'id' => $notaCreditoId,
                        'estado' => 'ANULADA_EN_ARCHIVADA',
            ]);
        } catch (\Throwable $e) {
            $this->db->transRollback();

            return $this->responseSetJSON('error', 'Error al anular la nota de credito: ' . $e->getMessage());
        }
    }

    private function validarDataNotaCredito(object $dataPostNotaCredito): array {

        if (empty($dataPostNotaCredito->compra) || !is_object($dataPostNotaCredito->compra)) {
            return ['status' => true, 'msg' => 'No se recibieron los datos de cabecera de la nota de credito.'];
        }

        if (empty($dataPostNotaCredito->detalle) || !is_array($dataPostNotaCredito->detalle)) {
            return ['status' => true, 'msg' => 'Debe existir al menos un detalle para la nota de credito.'];
        }

        $compra = $dataPostNotaCredito->compra;

        if (empty($compra->compraRelacionadaId)) {
            return ['status' => true, 'msg' => 'No se recibio la compra relacionada.'];
        }

        if (empty($compra->compTipoNotaCredito)) {
            return ['status' => true, 'msg' => 'Debe seleccionar el tipo de nota de credito.'];
        }

        if (!in_array($compra->compTipoNotaCredito, ['DEVOLUCION', 'DESCUENTO'], true)) {
            return ['status' => true, 'msg' => 'El tipo de nota de credito no es valido.'];
        }

        if (empty($compra->compNumeroEstablecimiento) || empty($compra->compNumeroEmision) || empty($compra->compNumeroComprobante)) {
            return ['status' => true, 'msg' => 'Debe completar el numero de comprobante de la nota de credito.'];
        }

        if (empty($compra->compAutSRI)) {
            return ['status' => true, 'msg' => 'Debe ingresar la autorizacion SRI de la nota de credito.'];
        }

        if (empty($compra->compFechaEmision)) {
            return ['status' => true, 'msg' => 'Debe ingresar la fecha de emision de la nota de credito.'];
        }

        if ((float) ($compra->compTotal ?? 0) <= 0) {
            return ['status' => true, 'msg' => 'El total de la nota de credito debe ser mayor a cero.'];
        }

        if (empty($compra->destinoFinanciero)) {
            return ['status' => true, 'msg' => 'Debe seleccionar si la nota de credito se aplicara a CxP o anticipo a proveedor.'];
        }

        if (!in_array($compra->destinoFinanciero, ['CXP', 'ANTICIPO_PROVEEDOR'], true)) {
            return ['status' => true, 'msg' => 'El destino financiero de la nota de credito no es valido.'];
        }

        foreach ($dataPostNotaCredito->detalle as $item) {
            if ((float) ($item->cantidadNdc ?? 0) <= 0) {
                return ['status' => true, 'msg' => 'Todos los items deben tener cantidad mayor a cero.'];
            }

            if ((float) ($item->precioNdc ?? 0) <= 0) {
                return ['status' => true, 'msg' => 'Todos los items deben tener precio mayor a cero.'];
            }

            if (empty($item->cuentaContable)) {
                return ['status' => true, 'msg' => 'Todos los items deben tener cuenta contable.'];
            }
        }

        return ['status' => false, 'msg' => ''];
    }

    public function responseSetJSON(string $status, string $mensaje, mixed $data = null) {
        return $this->response->setJSON([
                    'status' => $status,
                    'msg' => $mensaje,
                    'data' => $data,
        ]);
    }
}
