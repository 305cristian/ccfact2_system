<?php

namespace Modules\NotasCredito\Controllers;

use App\Controllers\BaseController;
use App\Models\CcModel;
use Modules\NotasCredito\Libraries\NotaCreditoLib;
use Modules\NotasCredito\Libraries\NotasCreditoAnticipoProveedorLib;
use Modules\NotasCredito\Libraries\NotasCreditoAsientosLib;
use Modules\NotasCredito\Libraries\NotasCreditoCxpLib;
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
    protected NotasCreditoCxpLib $notasCreditoCxpLib;
    protected NotasCreditoAnticipoProveedorLib $notasCreditoAnticipoProveedorLib;
    protected NotasCreditoAsientosLib $notasCreditoAsientosLib;

    public function __construct() {
        $this->dirViewModule = 'Modules\NotasCredito\Views';
        $this->gm = new CcModel();
        $this->notaCreditoModel = new NotaCreditoModel();
        $this->notaCreditoLib = new NotaCreditoLib();
        $this->notasCreditoCxpLib = new NotasCreditoCxpLib();
        $this->notasCreditoAnticipoProveedorLib = new NotasCreditoAnticipoProveedorLib();
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

            // Anulo la retencion directamente si el check viene marcado, esto solo se dara siempre y cuando la retencion no haya sido autorizada por el SRI osea este la retencion en estado PENDIENTE
            if ($dataPostNotaCredito->compra->destinoFinanciero === 'CXP' && !empty($dataPostNotaCredito->compra->anularRetencionPendiente)) {
                $this->anularRetencionPendienteNotaCredito((int) $dataPostNotaCredito->compra->compraRelacionadaId);
            }

            if ($dataPostNotaCredito->compra->destinoFinanciero === 'CXP') {
                $excedenteAnticipo = $this->notasCreditoCxpLib->aplicarNotaCreditoCuentaPorPagar($compraId, $dataPostNotaCredito);

                if ($excedenteAnticipo > 0 && empty($dataPostNotaCredito->compra->anularRetencionPendiente)) {
                    $this->notasCreditoAnticipoProveedorLib->guardarAnticipoProveedorNotaCredito($compraId, $dataPostNotaCredito, $excedenteAnticipo);
                }
            }

            if ($dataPostNotaCredito->compra->destinoFinanciero === 'ANTICIPO_PROVEEDOR') {
                $this->notasCreditoAnticipoProveedorLib->guardarAnticipoProveedorNotaCredito($compraId, $dataPostNotaCredito);
            }

            $this->notasCreditoAsientosLib->generarAsientoNotaCredito($compraId);

            if ($dataPostNotaCredito->compra->destinoFinanciero === 'CXP' && !empty($dataPostNotaCredito->compra->anularRetencionPendiente)) {
                $this->limpiarRetencionCompraRelacionadaNotaCredito((int) $dataPostNotaCredito->compra->compraRelacionadaId);
            }

            $secuencial = $this->ccm->getValueWhere('cc_compras', ['id' => $compraId, 'fk_proyecto' => getProyectoId()], 'comp_secuencial');

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

        $notaCredito = $this->ccm->getData('cc_compras', ['id' => $notaCreditoId, 'fk_proyecto' => getProyectoId()], 'id, comp_secuencial, comp_estado, comp_tipo_comprobante_cod, comp_tipo_nota_credito, comp_total, fk_compra_relacionada', null, 1);

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
            $this->restaurarRetencionPendienteAnuladaPorNotaCredito($notaCredito);
            $this->notasCreditoCxpLib->anularAplicacionCuentaPorPagarNotaCredito($notaCreditoId);
            $this->notasCreditoAnticipoProveedorLib->anularAnticipoProveedorNotaCredito($notaCreditoId, $motivoAnulacion);

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

        if (empty(getProyectoId())) {
            return ['status' => true, 'msg' => 'Debe seleccionar el proyecto de trabajo para registrar la nota de credito.'];
        }

        if ($compra->destinoFinanciero === 'CXP') {
            $validacionExcedente = $this->validarConfirmacionExcedenteAnticipo($compra);

            if ($validacionExcedente['status']) {
                return $validacionExcedente;
            }
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

    private function validarConfirmacionExcedenteAnticipo(object $compra): array {

        $compraRelacionada = $this->ccm->getData('cc_compras', ['id' => (int) $compra->compraRelacionadaId, 'fk_proyecto' => getProyectoId()], 'id, comp_total, fk_retencion', null, 1);

        if (!$compraRelacionada) {
            return ['status' => true, 'msg' => 'No se encontro la factura relacionada para validar el excedente financiero.'];
        }

        $cxp = $this->ccm->getData('cc_cxp', ['fk_compra' => (int) $compraRelacionada->id, 'fk_proyecto' => getProyectoId()], 'id, cxp_saldo', null, 1);

        if (!$cxp) {
            return ['status' => false, 'msg' => ''];
        }

        $totalFactura = round((float) $compraRelacionada->comp_total, 4);
        $totalNdc = round((float) ($compra->compTotal ?? 0), 4);
        $saldoCxp = round((float) $cxp->cxp_saldo, 4);
        $excedente = round($totalNdc - $saldoCxp, 4);
        $ndcTotalFactura = abs($totalNdc - $totalFactura) <= 0.01;

        if (!$ndcTotalFactura || $excedente <= 0.0001) {
            return ['status' => false, 'msg' => ''];
        }

        if (empty($compraRelacionada->fk_retencion)) {
            return [
                'status' => true,
                'msg' => 'La nota de credito supera el saldo de la CxP y la compra no tiene retencion activa que justifique el excedente. Revise pagos o aplicaciones previas.',
            ];
        }

        $retencion = $this->ccm->getData('cc_retencion', ['id' => (int) $compraRelacionada->fk_retencion, 'fk_proyecto' => getProyectoId(), 'ret_estado' => 1], 'id, ret_numero_comprobante, ret_estado_sri, ret_total_retenido', null, 1);

        if (!$retencion) {
            return [
                'status' => true,
                'msg' => 'La nota de credito supera el saldo de la CxP, pero no se encontro una retencion activa para validar el excedente.',
            ];
        }

        if (abs($excedente - round((float) $retencion->ret_total_retenido, 4)) > 0.02) {
            return [
                'status' => true,
                'msg' => 'El excedente de la NDC no coincide con el valor retenido. Revise la CxP, la retencion o pagos aplicados antes de continuar.',
            ];
        }

        if (!in_array($retencion->ret_estado_sri, ['ENVIADO', 'AUTORIZADO'], true)) {
            if (empty($compra->anularRetencionPendiente)) {
                return [
                    'status' => true,
                    'msg' => "La retencion #{$retencion->ret_numero_comprobante} esta {$retencion->ret_estado_sri}. Debe confirmar la anulacion de la retencion para continuar.",
                ];
            }

            return ['status' => false, 'msg' => ''];
        }

        if (empty($compra->abonarExcedenteAnticipo)) {
            return [
                'status' => true,
                'msg' => 'Debe confirmar que el excedente de la nota de credito sera abonado como anticipo a proveedor.',
            ];
        }

        return ['status' => false, 'msg' => ''];
    }

    private function anularRetencionPendienteNotaCredito(int $compraRelacionadaId): void {

        $compra = $this->ccm->getData('cc_compras', ['id' => $compraRelacionadaId, 'fk_proyecto' => getProyectoId()], 'id, fk_retencion', null, 1);

        if (!$compra || empty($compra->fk_retencion)) {
            return;
        }

        $retencion = $this->ccm->getData('cc_retencion', ['id' => (int) $compra->fk_retencion, 'fk_proyecto' => getProyectoId(), 'ret_estado' => 1], 'id, ret_numero_comprobante, ret_estado_sri', null, 1);

        if (!$retencion) {
            return;
        }

        if (in_array($retencion->ret_estado_sri, ['ENVIADO', 'AUTORIZADO'], true)) {
            throw new \RuntimeException("No se puede anular la retencion #{$retencion->ret_numero_comprobante} porque esta {$retencion->ret_estado_sri}.");
        }

        $anulado = $this->ccm->actualizar('cc_retencion', ['ret_estado' => 0], ['id' => (int) $retencion->id, 'fk_proyecto' => getProyectoId()]);

        if (!$anulado) {
            throw new \RuntimeException('No se pudo anular la retencion pendiente de la compra relacionada.');
        }
    }

    private function limpiarRetencionCompraRelacionadaNotaCredito(int $compraRelacionadaId): void {
        $dataSet = [
            'fk_retencion' => null,
            'comp_aplica_retencion' => 0,
        ];
        $this->ccm->actualizar('cc_compras', $dataSet, ['id' => $compraRelacionadaId, 'fk_proyecto' => getProyectoId()]);
    }

    private function restaurarRetencionPendienteAnuladaPorNotaCredito(object $notaCredito): void {

        if (empty($notaCredito->fk_compra_relacionada)) {
            return;
        }

        $whereData = [
            'pg_tipo_movimiento' => 'NDC_COMPRA',
            'fk_compra_nota_credito' => (int) $notaCredito->id,
            'pg_estado' => 'ACTIVO',
            'fk_proyecto' => getProyectoId(),
        ];
        $pagoNdc = $this->ccm->getData('cc_pagos', $whereData, 'id, pg_valor', null, 1);

        if (!$pagoNdc) {
            return;
        }

        $whereData2 = [
            'fk_ndc' => (int) $notaCredito->id,
            'fk_proyecto' => getProyectoId(),
            'antp_estado' => 'ACTIVO',
        ];
        $anticipoActivo = $this->ccm->getData('cc_anticipo_proveedor', $whereData2, 'id', null, 1);

        if ($anticipoActivo) {
            return;
        }

        $diferenciaRetencion = round((float) $notaCredito->comp_total - (float) $pagoNdc->pg_valor, 4);

        if ($diferenciaRetencion <= 0.0001) {
            return;
        }

        $compraRelacionada = $this->ccm->getData('cc_compras', ['id' => (int) $notaCredito->fk_compra_relacionada, 'fk_proyecto' => getProyectoId(),], 'id, fk_retencion', null, 1);

        if (!$compraRelacionada || !empty($compraRelacionada->fk_retencion)) {
            return;
        }

        $whereData3 = [
            'ret_documento_id' => (int) $compraRelacionada->id,
            'fk_proyecto' => getProyectoId(),
            'ret_estado' => 0,
        ];
        $retencionesAnuladas = $this->ccm->getData('cc_retencion', $whereData3, 'id, ret_estado_sri, ret_total_retenido',['id' => 'DESC']);

        foreach ($retencionesAnuladas ?? [] as $retencion) {
            if (in_array($retencion->ret_estado_sri, ['ENVIADO', 'AUTORIZADO'], true)) {
                continue;
            }

            if (abs($diferenciaRetencion - round((float) $retencion->ret_total_retenido, 4)) > 0.02) {
                continue;
            }

            $this->ccm->actualizar('cc_retencion', ['ret_estado' => 1], ['id' => (int) $retencion->id, 'fk_proyecto' => getProyectoId()]);
            $this->ccm->actualizar('cc_compras',['fk_retencion' => (int) $retencion->id, 'comp_aplica_retencion' => 1],[ 'id' => (int) $compraRelacionada->id, 'fk_proyecto' => getProyectoId()]);
            return;
        }

        throw new \RuntimeException('No se encontro la retencion pendiente anulada para restaurar la compra relacionada.');
    }

    public function responseSetJSON(string $status, string $mensaje, mixed $data = null) {
        return $this->response->setJSON([
                    'status' => $status,
                    'msg' => $mensaje,
                    'data' => $data,
        ]);
    }
}
