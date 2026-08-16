<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Ventas\Libraries;

/**
 * Description of VentasFinanzasLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 16 ago 2026
 * @time 12:01:52 p.m.
 */
class VentasFinanzasLib {

    protected $ccm;
    protected $user;

    public function __construct() {
        $this->ccm = service('ccModel');
        $this->user = service('userSecion');
    }

    public function crearCuentaPorCobrar(int $ventaId): int {

        $venta = $this->ccm->getData('cc_ventas', ['id' => $ventaId, 'fk_proyecto' => getProyectoId()], '*', null, 1);

        if (!$venta) {
            throw new \RuntimeException('No se encontro la venta para generar la cuenta por cobrar.');
        }

        if ($venta->ven_estado !== 'ARCHIVADO') {
            throw new \RuntimeException('Solo una venta archivada puede generar cuenta por cobrar.');
        }

        $totalVenta = round((float) $venta->ven_total, 4);

        if ($totalVenta <= 0) {
            throw new \RuntimeException('El valor de la cuenta por cobrar debe ser mayor a cero.');
        }

        $esContado = $venta->ven_tipo_pago === 'CONTADO';
        $fechaVencimiento = $esContado ? $venta->ven_fecha_emision : $this->sumarDiasFecha($venta->ven_fecha_emision, (int) ($venta->ven_dias_credito ?? 0));

        $formData = [
            'fk_venta' => $ventaId,
            'fk_cliente' => $venta->fk_cliente,
            'fk_proyecto' => getProyectoId(),
            'cxc_total' => $totalVenta,
            'cxc_valor_cobrado' => $esContado ? $totalVenta : 0,
            'cxc_saldo' => $esContado ? 0 : $totalVenta,
            'cxc_fecha_emision' => $venta->ven_fecha_emision,
            'cxc_fecha_vencimiento' => $fechaVencimiento,
            'cxc_estado' => $esContado ? 'COBRADO' : 'PENDIENTE',
            'cxc_observacion' => $venta->ven_observacion,
        ];

        return (int) $this->ccm->guardar($formData, 'cc_cxc');
    }

    public function guardarCobroContado(int $cxcId, object $pago): int {

        $cxc = $this->ccm->getData('cc_cxc', ['id' => $cxcId, 'fk_proyecto' => getProyectoId()], '*', null, 1);

        if (!$cxc) {
            throw new \RuntimeException('No se encontro la cuenta por cobrar.');
        }

        if ((float) $cxc->cxc_total <= 0) {
            throw new \RuntimeException('El valor del cobro debe ser mayor a cero.');
        }

        $formaPago = $pago->formaPago ?? null;
        $cuentaContable = $pago->cuentaContable ?? null;

        if (!in_array($formaPago, ['01', '02', '03', '04'], true) || empty($cuentaContable)) {
            throw new \InvalidArgumentException('La forma de pago y la cuenta contable son obligatorias.');
        }

        $bancoId = isset($pago->banco->codigo) ? (int) $pago->banco->codigo : null;
        $fechaCobro = $pago->fechaCobro ?? date('Y-m-d');
        $referencia = $pago->referencia ?? null;

        switch ($formaPago) {
            case '01':
                if (empty($pago->nota)) {
                    throw new \InvalidArgumentException('Debe ingresar la nota del cobro en efectivo.');
                }
                break;

            case '02':
                if (!$bancoId || empty($pago->numeroTransferencia) || empty($pago->fechaTransferencia) || empty($pago->nota)) {
                    throw new \InvalidArgumentException('Los datos de la transferencia estan incompletos.');
                }

                $referencia = $pago->numeroTransferencia;
                break;

            case '03':
                if (!$bancoId || empty($pago->numeroCheque) || empty($pago->fechaCheque)) {
                    throw new \InvalidArgumentException('Los datos del cheque estan incompletos.');
                }

                $referencia = $pago->numeroCheque;
                break;

            case '04':
                if (empty($pago->marcaTarjeta) || empty($pago->loteTarjeta) || empty($pago->autorizacionTarjeta) || !preg_match('/^\d{4}$/', (string) ($pago->ultimosDigitos ?? '')) || empty($pago->fechaVoucher) || empty($pago->nota)) {
                    throw new \InvalidArgumentException('Los datos de la tarjeta estan incompletos.');
                }

                $referencia = $pago->autorizacionTarjeta;
                break;
        }

        $ultimoCobro = $this->ccm->getData('cc_cobros', ['fk_proyecto' => getProyectoId()], 'cob_numero_secuencial', ['cob_numero_secuencial' => 'DESC'], 1);
        $secuencial = $ultimoCobro ? (int) $ultimoCobro->cob_numero_secuencial + 1 : 1;
        $valorCobrado = round((float) $cxc->cxc_total, 4);
        $valorRecibido = round((float) ($pago->valorRecibido ?? $valorCobrado), 4);
        $cambio = max(0, round($valorRecibido - $valorCobrado, 4));

        $formData = [
            'fk_cliente' => $cxc->fk_cliente,
            'fk_proyecto' => getProyectoId(),
            'cob_numero_secuencial' => $secuencial,
            'cob_fecha' => $fechaCobro,
            'fk_forma_pago' => $formaPago,
            'fk_cuenta_contable' => $cuentaContable,
            'fk_banco' => $bancoId,
            'cob_referencia' => $referencia,
            'cob_numero_transferencia' => $pago->numeroTransferencia ?? null,
            'cob_fecha_transferencia' => $pago->fechaTransferencia ?? null,
            'cob_numero_cheque' => $pago->numeroCheque ?? null,
            'cob_fecha_cheque' => $pago->fechaCheque ?? null,
            'cob_marca_tarjeta' => $pago->marcaTarjeta ?? null,
            'cob_lote_tarjeta' => $pago->loteTarjeta ?? null,
            'cob_autorizacion_tarjeta' => $pago->autorizacionTarjeta ?? null,
            'cob_ultimos_digitos' => $pago->ultimosDigitos ?? null,
            'cob_fecha_voucher' => $pago->fechaVoucher ?? null,
            'cob_valor' => $valorCobrado,
            'cob_valor_recibido' => $valorRecibido,
            'cob_cambio' => $cambio,
            'cob_estado' => 'ACTIVO',
            'cob_observacion' => $pago->nota ?? null,
            'fk_user' => $this->user->id,
        ];

        $cobroId = (int) $this->ccm->guardar($formData, 'cc_cobros');

        $formDataDet = [
            'fk_cobro' => $cobroId,
            'fk_proyecto' => getProyectoId(),
            'fk_cxc' => $cxcId,
            'fk_cuota' => null,
            'cobd_valor' => $valorCobrado,
        ];

        $this->ccm->guardar($formDataDet, 'cc_cobros_det');

        return $cobroId;
    }

    public function guardarCuotas(int $cxcId, array $cuotas): int {

        $cxc = $this->ccm->getData('cc_cxc', ['id' => $cxcId, 'fk_proyecto' => getProyectoId()], '*', null, 1);

        if (!$cxc) {
            throw new \RuntimeException('No se encontro la cuenta por cobrar.');
        }

        if ($cxc->cxc_estado !== 'PENDIENTE') {
            throw new \RuntimeException('Solo se pueden generar cuotas para ventas registradas a credito.');
        }

        if (!$cuotas) {
            throw new \InvalidArgumentException('Debe generar al menos una cuota para la venta a credito.');
        }

        $totalCuotas = 0;
        $numeros = [];

        foreach ($cuotas as $cuota) {
            $numero = (int) ($cuota->numero ?? 0);
            $valor = round((float) ($cuota->valor ?? 0), 4);
            $fecha = $cuota->fecha ?? null;

            if ($numero <= 0 || in_array($numero, $numeros, true)) {
                throw new \InvalidArgumentException('La numeracion de las cuotas no es valida.');
            }

            if (!$fecha || $valor <= 0) {
                throw new \InvalidArgumentException("Revise la fecha y el valor de la cuota {$numero}.");
            }

            $numeros[] = $numero;
            $totalCuotas += $valor;
        }

        if (round($totalCuotas, 4) !== round((float) $cxc->cxc_total, 4)) {
            throw new \InvalidArgumentException('La suma de las cuotas no coincide con el total por cobrar.');
        }

        foreach ($cuotas as $cuota) {
            $valor = round((float) $cuota->valor, 4);

            $formData = [
                'fk_cxc' => $cxcId,
                'fk_proyecto' => getProyectoId(),
                'cxcc_numero' => (int) $cuota->numero,
                'cxcc_fecha_vencimiento' => $cuota->fecha,
                'cxcc_valor' => $valor,
                'cxcc_cobrado' => 0,
                'cxcc_saldo' => $valor,
                'cxcc_estado' => 'PENDIENTE',
            ];

            $this->ccm->guardar($formData, 'cc_cxc_cuotas');
        }

        return count($cuotas);
    }

    public function incrementarSecuencialPuntoVenta(int $puntoVentaId): void {

        $puntoVenta = $this->ccm->getData('cc_puntos_venta', ['id' => $puntoVentaId, 'fk_proyecto' => getProyectoId(), 'pv_estado' => '1'], 'id, pv_sec_actual, pv_sec_final', null, 1);

        if (!$puntoVenta) {
            throw new \RuntimeException('No se encontro el punto de emision de la venta.');
        }

        $secuencialActual = (int) $puntoVenta->pv_sec_actual;

        if ($secuencialActual > (int) $puntoVenta->pv_sec_final) {
            throw new \RuntimeException('El punto de emision ya no tiene secuenciales disponibles.');
        }

        $this->ccm->actualizar('cc_puntos_venta', ['pv_sec_actual' => $secuencialActual + 1], ['id' => $puntoVentaId, 'fk_proyecto' => getProyectoId()]);
    }

    public function anularCuentaPorCobrarVenta(int $ventaId): void {

        $venta = $this->ccm->getData('cc_ventas', ['id' => $ventaId, 'fk_proyecto' => getProyectoId()], 'id, ven_tipo_pago, ven_total', null, 1);

        if (!$venta) {
            throw new \RuntimeException('No se encontro la venta para anular la cuenta por cobrar.');
        }

        $cxc = $this->ccm->getData('cc_cxc', ['fk_venta' => $ventaId, 'fk_proyecto' => getProyectoId()], '*', null, 1);

        if (!$cxc) {
            return;
        }

        $cobrosActivos = $this->getCobrosActivosCxc((int) $cxc->id);

        if ($cobrosActivos) {
            $esCobroInicialContado = $venta->ven_tipo_pago === 'CONTADO'
                    && count($cobrosActivos) === 1
                    && abs(round((float) $cobrosActivos[0]->cob_valor, 4) - round((float) $venta->ven_total, 4)) <= 0.01;

            if (!$esCobroInicialContado) {
                throw new \RuntimeException('No se puede anular la venta porque la cuenta por cobrar ya tiene cobros aplicados.');
            }

            $this->anularCobrosCxc((int) $cxc->id);
        }

        $this->ccm->actualizar('cc_cxc_cuotas', [
            'cxcc_estado' => 'ANULADO',
            'cxcc_saldo' => 0,
        ], [
            'fk_cxc' => (int) $cxc->id,
            'fk_proyecto' => getProyectoId(),
        ]);

        $this->ccm->actualizar('cc_cxc', [
            'cxc_estado' => 'ANULADO',
            'cxc_saldo' => 0,
        ], [
            'id' => (int) $cxc->id,
            'fk_proyecto' => getProyectoId(),
        ]);
    }

    private function getCobrosActivosCxc(int $cxcId): array {

        $detalles = $this->ccm->getData('cc_cobros_det', [
            'fk_cxc' => $cxcId,
            'fk_proyecto' => getProyectoId(),
        ], 'fk_cobro');

        if (!$detalles) {
            return [];
        }

        $cobros = [];

        foreach ($detalles as $detalle) {
            $cobroId = (int) $detalle->fk_cobro;

            if (isset($cobros[$cobroId])) {
                continue;
            }

            $cobro = $this->ccm->getData('cc_cobros', [
                'id' => $cobroId,
                'fk_proyecto' => getProyectoId(),
                'cob_estado' => 'ACTIVO',
            ], 'id, cob_valor', null, 1);

            if ($cobro) {
                $cobros[$cobroId] = $cobro;
            }
        }

        return array_values($cobros);
    }

    private function anularCobrosCxc(int $cxcId): void {

        $cobros = $this->getCobrosActivosCxc($cxcId);

        foreach ($cobros as $cobro) {
            $this->ccm->actualizar('cc_cobros', ['cob_estado' => 'ANULADO'], [
                'id' => (int) $cobro->id,
                'fk_proyecto' => getProyectoId(),
            ]);
        }
    }

    private function sumarDiasFecha(string $fecha, int $dias): string {

        return date('Y-m-d', strtotime($fecha . ' +' . max(0, $dias) . ' days'));
    }
}
