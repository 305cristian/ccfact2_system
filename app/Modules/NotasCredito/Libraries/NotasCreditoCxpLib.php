<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\NotasCredito\Libraries;

/**
 * Description of NotasCreditoCxpLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 28 jul 2026
 * @time 10:37:52 a.m.
 */
class NotasCreditoCxpLib {

    protected $ccm;
    protected $user;

    public function __construct() {
        $this->ccm = service('ccModel');
        $this->user = service('userSecion');
    }

    public function aplicarNotaCreditoCuentaPorPagar(int $notaCreditoId, object $dataPostNotaCredito): void {

        $compra = $dataPostNotaCredito->compra;
        $valorNotaCredito = round((float) ($dataPostNotaCredito->totales->total ?? 0), 4);

        if ($valorNotaCredito <= 0) {
            throw new \RuntimeException('El valor de la nota de credito debe ser mayor a cero para aplicar a CxP.');
        }

        $cxp = $this->ccm->getData('cc_cxp', ['fk_compra' => (int) $compra->compraRelacionadaId, 'fk_proyecto' => getProyectoId()], '*', null, 1);

        if (!$cxp) {
            throw new \RuntimeException('La compra relacionada no tiene cuenta por pagar registrada.');
        }

        if (!in_array($cxp->cxp_estado, ['PENDIENTE', 'PARCIAL'], true)) {
            throw new \RuntimeException('La cuenta por pagar relacionada no tiene saldo pendiente aplicable.');
        }

        $saldoActual = round((float) $cxp->cxp_saldo, 4);

        if ($valorNotaCredito > $saldoActual) {
            throw new \RuntimeException('El valor de la nota de credito no puede superar el saldo pendiente de la cuenta por pagar.');
        }

        $pagoNdcId = $this->guardarMovimientoPagoNotaCredito($notaCreditoId, $cxp, $compra, $valorNotaCredito);

        $nuevoSaldo = round($saldoActual - $valorNotaCredito, 4);
        $nuevoPagado = round((float) $cxp->cxp_valor_pagado + $valorNotaCredito, 4);
        $nuevoEstado = $nuevoSaldo <= 0 ? 'PAGADO' : 'PARCIAL';
        $observacion_ = trim((string) ($cxp->cxp_observacion ?? ''));
        $observacionNdc = "NDC #{$notaCreditoId} aplicada por " . number_format($valorNotaCredito, 4, '.', '');
        $observacion = trim($observacion_ . ($observacion_ !== '' ? ' | ' : '') . $observacionNdc);

        $dataSet = [
            'cxp_valor_pagado' => $nuevoPagado,
            'cxp_saldo' => $nuevoSaldo,
            'cxp_fecha_ultimo_pago' => $compra->compFechaEmision,
            'cxp_estado' => $nuevoEstado,
            'cxp_observacion' => $observacion,
        ];
        $this->ccm->actualizar('cc_cxp', $dataSet, ['id' => (int) $cxp->id, 'fk_proyecto' => getProyectoId()]);

        $this->aplicarNotaCreditoCuotas((int) $cxp->id, $valorNotaCredito, $notaCreditoId, $pagoNdcId);
    }

    public function anularAplicacionCuentaPorPagarNotaCredito(int $notaCreditoId): void {

        $pagoNdc = $this->ccm->getData('cc_pagos', ['pg_tipo_movimiento' => 'NDC_COMPRA', 'fk_compra_nota_credito' => $notaCreditoId, 'pg_estado' => 'ACTIVO', 'fk_proyecto' => getProyectoId(),], '*', null, 1);

        if (!$pagoNdc) {
            return;
        }

        $detallesPago = $this->ccm->getData('cc_pagos_det', ['fk_pago' => (int) $pagoNdc->id, 'fk_proyecto' => getProyectoId()], '*');

        if (!$detallesPago) {
            throw new \RuntimeException('No se encontraron detalles de aplicacion para anular la nota de credito.');
        }

        foreach ($detallesPago as $detallePago) {
            if (!empty($detallePago->fk_cuota)) {
                $this->revertirCuotaNotaCredito($detallePago);
            }
        }

        $cxpId = (int) $detallesPago[0]->fk_cxp;
        $cxp = $this->ccm->getData('cc_cxp', ['id' => $cxpId, 'fk_proyecto' => getProyectoId()], '*', null, 1);

        if (!$cxp) {
            throw new \RuntimeException('No se encontro la cuenta por pagar vinculada a la nota de credito.');
        }

        $valorNotaCredito = round((float) $pagoNdc->pg_valor, 4);
        $nuevoPagado = round((float) $cxp->cxp_valor_pagado - $valorNotaCredito, 4);
        $nuevoSaldo = round((float) $cxp->cxp_saldo + $valorNotaCredito, 4);

        if ($nuevoPagado < 0) {
            throw new \RuntimeException('No se puede anular la nota de credito porque el valor pagado de la CxP quedaria negativo.');
        }

        $dataSet = [
            'cxp_valor_pagado' => $nuevoPagado,
            'cxp_saldo' => $nuevoSaldo,
            'cxp_estado' => $this->resolverEstadoSaldo($nuevoPagado, $nuevoSaldo),
            'cxp_observacion' => $this->agregarObservacion($cxp->cxp_observacion ?? '', "Anulacion NDC #{$notaCreditoId} por " . number_format($valorNotaCredito, 4, '.', '')),
        ];
        $this->ccm->actualizar('cc_cxp', $dataSet, ['id' => $cxpId, 'fk_proyecto' => getProyectoId()]);

        $this->ccm->actualizar('cc_pagos', ['pg_estado' => 'ANULADO'], ['id' => (int) $pagoNdc->id, 'fk_proyecto' => getProyectoId()]);
    }

    private function guardarMovimientoPagoNotaCredito(int $notaCreditoId, object $cxp, object $compra, float $valorNotaCredito): int {

        $movimientoExistente = $this->ccm->getData('cc_pagos', ['pg_tipo_movimiento' => 'NDC_COMPRA', 'fk_compra_nota_credito' => $notaCreditoId, 'pg_estado' => 'ACTIVO', 'fk_proyecto' => getProyectoId(),], 'id', null, 1);

        if ($movimientoExistente) {
            throw new \RuntimeException('La nota de credito ya tiene un movimiento financiero activo aplicado a CxP.');
        }

        $ultimoPago = $this->ccm->getData('cc_pagos', ['fk_proyecto' => getProyectoId()], 'pg_numero_secuencial', ['id' => 'DESC'], 1);
        $secuencial = $ultimoPago ? (int) $ultimoPago->pg_numero_secuencial + 1 : 1;

        $dataPago = [
            'fk_proveedor' => (int) $cxp->fk_proveedor,
            'fk_proyecto' => getProyectoId(),
            'pg_numero_secuencial' => (string) $secuencial,
            'pg_tipo_movimiento' => 'NDC_COMPRA',
            'fk_compra_nota_credito' => $notaCreditoId,
            'pg_fecha' => $compra->compFechaEmision,
            'fk_forma_pago' => null,
            'fk_cuenta_contable' => null,
            'fk_banco' => null,
            'pg_referencia' => "NDC #{$notaCreditoId}",
            'pg_valor' => $valorNotaCredito,
            'pg_estado' => 'ACTIVO',
            'pg_observacion' => 'Aplicacion de nota de credito de compra a CxP.',
            'fk_user' => $this->user->id,
        ];

        $pagoNdcId = (int) $this->ccm->guardar($dataPago, 'cc_pagos');

        if (!$pagoNdcId) {
            throw new \RuntimeException('No se pudo registrar el movimiento financiero de la nota de credito.');
        }

        return $pagoNdcId;
    }

    private function aplicarNotaCreditoCuotas(int $cxpId, float $valorNotaCredito, int $notaCreditoId, int $pagoNdcId): void {

        $cuotas = $this->ccm->getData('cc_cxp_cuotas', ['fk_cxp' => $cxpId, 'fk_proyecto' => getProyectoId()], '*', ['cxpc_numero' => 'ASC']);

        if (!$cuotas) {
            $this->guardarDetalleMovimientoPagoNotaCredito($pagoNdcId, $cxpId, null, $valorNotaCredito);
            return;
        }

        $saldoAplicar = round($valorNotaCredito, 4);

        foreach ($cuotas as $cuota) {
            if ($saldoAplicar <= 0) {
                break;
            }

            if (!in_array($cuota->cxpc_estado, ['PENDIENTE', 'PARCIAL'], true)) {
                continue;
            }

            $saldoCuota = round((float) $cuota->cxpc_saldo, 4);

            if ($saldoCuota <= 0) {
                continue;
            }

            $valorAplicado = min($saldoAplicar, $saldoCuota);
            $nuevoSaldoCuota = round($saldoCuota - $valorAplicado, 4);
            $nuevoPagadoCuota = round((float) $cuota->cxpc_pagado + $valorAplicado, 4);
            $nuevoEstadoCuota = $nuevoSaldoCuota <= 0 ? 'PAGADO' : 'PARCIAL';

            $dataSet = [
                'cxpc_pagado' => $nuevoPagadoCuota,
                'cxpc_saldo' => $nuevoSaldoCuota,
                'cxpc_estado' => $nuevoEstadoCuota,
            ];
            $this->ccm->actualizar('cc_cxp_cuotas', $dataSet, ['id' => (int) $cuota->id, 'fk_proyecto' => getProyectoId()]);
            $this->guardarDetalleMovimientoPagoNotaCredito($pagoNdcId, $cxpId, (int) $cuota->id, $valorAplicado);

            $saldoAplicar = round($saldoAplicar - $valorAplicado, 4);
        }

        if ($saldoAplicar > 0.0001) {
            throw new \RuntimeException("No se pudo aplicar completamente la NDC #{$notaCreditoId} a las cuotas de la CxP.");
        }
    }

    private function guardarDetalleMovimientoPagoNotaCredito(int $pagoNdcId, int $cxpId, ?int $cuotaId, float $valorAplicado): int {

        $valorAplicado = round($valorAplicado, 4);

        if ($valorAplicado <= 0) {
            throw new \RuntimeException('El valor aplicado por la nota de credito debe ser mayor a cero.');
        }

        $dataPagoDet = [
            'fk_pago' => $pagoNdcId,
            'fk_proyecto' => getProyectoId(),
            'fk_cxp' => $cxpId,
            'fk_cuota' => $cuotaId,
            'pgd_valor' => $valorAplicado,
        ];

        $pagoDetId = (int) $this->ccm->guardar($dataPagoDet, 'cc_pagos_det');

        if (!$pagoDetId) {
            throw new \RuntimeException('No se pudo registrar el detalle de aplicacion de la nota de credito.');
        }

        return $pagoDetId;
    }

    private function revertirCuotaNotaCredito(object $detallePago): void {

        $cuota = $this->ccm->getData('cc_cxp_cuotas', ['id' => (int) $detallePago->fk_cuota, 'fk_proyecto' => getProyectoId()], '*', null, 1);

        if (!$cuota) {
            throw new \RuntimeException("No se encontro la cuota {$detallePago->fk_cuota} vinculada a la nota de credito.");
        }

        $valorAplicado = round((float) $detallePago->pgd_valor, 4);
        $nuevoPagado = round((float) $cuota->cxpc_pagado - $valorAplicado, 4);
        $nuevoSaldo = round((float) $cuota->cxpc_saldo + $valorAplicado, 4);

        if ($nuevoPagado < 0) {
            throw new \RuntimeException("No se puede anular la nota de credito porque la cuota {$cuota->cxpc_numero} quedaria con pagado negativo.");
        }

        $this->ccm->actualizar(
                'cc_cxp_cuotas',
                [
                    'cxpc_pagado' => $nuevoPagado,
                    'cxpc_saldo' => $nuevoSaldo,
                    'cxpc_estado' => $this->resolverEstadoSaldo($nuevoPagado, $nuevoSaldo),
                ],
                ['id' => (int) $cuota->id, 'fk_proyecto' => getProyectoId()]
        );
    }

    private function resolverEstadoSaldo(float $pagado, float $saldo): string {

        if (round($pagado, 4) <= 0) {
            return 'PENDIENTE';
        }

        if (round($saldo, 4) <= 0) {
            return 'PAGADO';
        }

        return 'PARCIAL';
    }

    private function agregarObservacion(string $observacionActual, string $observacionNueva): string {

        $observacionActual = trim($observacionActual);

        return trim($observacionActual . ($observacionActual !== '' ? ' | ' : '') . $observacionNueva);
    }
}
