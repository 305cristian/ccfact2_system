<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Compras\Libraries;

use Modules\Compras\Models\ComprasModel;

/**
 * Description of ComprasFinanzasLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 5 jul 2026
 * @time 12:55:35 p.m.
 */
class ComprasFinanzasLib {

    protected $ccm;
    protected $user;
    protected ComprasModel $comprasModel;
    protected string $tipoTransaccionRetencion = '02';

    public function __construct() {
        $this->ccm = service('ccModel');
        $this->user = service('userSecion');
        $this->comprasModel = new ComprasModel();
    }

    public function guardarRetencion(int $compraId, object $retencion): ?int {

        if (empty($retencion->aplica) || !empty($retencion->noSujeto)) {
            return null;
        }

        if (empty($retencion->detalles)) {
            throw new \InvalidArgumentException('Debe existir al menos un detalle de retención.');
        }

        try {
            $puntoEmision = $this->validarPuntoEmisionRetencion($retencion);
            $secuencial = (int) $puntoEmision->pv_sec_actual;
            $numeroComprobante = str_pad((string) $secuencial, 9, '0', STR_PAD_LEFT);

            $totalRetenido = array_reduce($retencion->detalles,
                    static fn(float $total, object $detalle): float =>
                    $total + (float) $detalle->valorRetenido,
                    0.0
            );

            $formData = [
                'ret_secuencial' => $secuencial,
                'ret_documento_id' => $compraId,
                'fk_proyecto' => getProyectoId(),
                'ret_tipo_transaccion_cod' => $this->tipoTransaccionRetencion,
                'ret_numero_comprobante' => $numeroComprobante,
                'ret_numero_emision' => $puntoEmision->pv_emision,
                'ret_numero_establecimiento' => $puntoEmision->pv_establecimiento,
                'ret_autorizacion_sri' => $puntoEmision->pv_auth_sri,
                'ret_fecha_emision' => $retencion->fechaEmision,
                'ret_total_retenido' => round($totalRetenido, 2),
                'ret_estado_sri' => 'PENDIENTE',
                'ret_estado' => 1,
                'fk_user' => $this->user->id,
            ];

            $retencionId = (int) $this->ccm->guardar($formData, 'cc_retencion');

            foreach ($retencion->detalles as $detalle) {
                $this->guardarDetalleRetencion($retencionId, $detalle);
            }

            $this->ccm->actualizar('cc_compras', ['fk_retencion' => $retencionId], ['id' => $compraId, 'fk_proyecto' => getProyectoId()]);
            $this->ccm->actualizar('cc_puntos_venta', ['pv_sec_actual' => $secuencial + 1], ['id' => $puntoEmision->id, 'fk_proyecto' => getProyectoId()]);

            return $retencionId;
        } catch (\Throwable $e) {
            throw new \RuntimeException('Error al guardar la retención: ' . $e->getMessage(), 0, $e);
        }
    }

    public function crearCuentaPorPagar(int $compraId): int {
        $compra = $this->ccm->getData('cc_compras', ['id' => $compraId, 'fk_proyecto' => getProyectoId()], '*', null, 1);

        if (!$compra) {
            throw new \RuntimeException('No se encontró la compra para generar la cuenta por pagar.');
        }

        if ($compra->comp_estado !== 'ARCHIVADO') {
            throw new \RuntimeException('Solo una compra archivada puede generar cuenta por pagar, revise el estado y vuelva a registrarla.');
        }

        $totalRetenido = 0;

        if (!empty($compra->fk_retencion)) {
            $totalRetenido = (float) $this->ccm->getValueWhere('cc_retencion', ['id' => $compra->fk_retencion, 'fk_proyecto' => getProyectoId()], 'ret_total_retenido');
        }

        $totalPagar = round((float) $compra->comp_total - $totalRetenido, 4);

        if ($totalPagar <= 0) {
            throw new \RuntimeException('El valor de la cuenta por pagar debe ser mayor a cero.');
        }

        $esContado = $compra->comp_tipo_pago === 'CONTADO';

        $formData = [
            'fk_compra' => $compraId,
            'fk_proyecto' => getProyectoId(),
            'fk_proveedor' => $compra->fk_proveedor,
            'cxp_tipo_transaccion_cod' => '02',
            'cxp_numero_documento' => $compra->comp_numero_comprobante,
            'cxp_tipo_pago' => $compra->comp_tipo_pago,
            'cxp_num_cuotas' => $esContado ? 1 : (int) $compra->comp_num_cuotas,
            'cxp_total' => $totalPagar,
            'cxp_valor_pagado' => $esContado ? $totalPagar : 0,
            'cxp_saldo' => $esContado ? 0 : $totalPagar,
            'cxp_fecha_ultimo_pago' => $esContado ? $compra->comp_fecha_emision : null,
            'cxp_estado' => $esContado ? 'PAGADO' : 'PENDIENTE',
            'cxp_observacion' => $compra->comp_observacion,
            'fk_user' => $this->user->id,
        ];

        return (int) $this->ccm->guardar($formData, 'cc_cxp');
    }

    public function guardarPagoContado(int $cxpId, object $pago): int {

        $cxp = $this->ccm->getData('cc_cxp', ['id' => $cxpId, 'fk_proyecto' => getProyectoId()], '*', null, 1);

        if (!$cxp || $cxp->cxp_tipo_pago !== 'CONTADO') {
            throw new \RuntimeException('La cuenta por pagar no corresponde a una compra de contado.');
        }

        $formaPago = $pago->formaPago ?? null;
        $cuentaContable = $pago->cuentaContable ?? null;

        if (!in_array($formaPago, ['01', '02', '03', '04'], true) || empty($cuentaContable)) {
            throw new \InvalidArgumentException('La forma de pago y la cuenta contable son obligatorias.');
        }

        $bancoId = isset($pago->banco->codigo) ? (int) $pago->banco->codigo : null;
        $fechaPago = date('Y-m-d');
        $referencia = null;

        switch ($formaPago) {
            case '01':
                if (empty($pago->nota)) {
                    throw new \InvalidArgumentException('Debe ingresar la nota del pago en efectivo.');
                }
                break;

            case '02':
                if (!$bancoId || empty($pago->numeroTransferencia) || empty($pago->fechaTransferencia) || empty($pago->nota)) {
                    throw new \InvalidArgumentException('Los datos de la transferencia están incompletos.');
                }
                $referencia = $pago->numeroTransferencia;
                $fechaPago = $pago->fechaTransferencia;
                break;

            case '03':
                if (!$bancoId || empty($pago->numeroCheque) || empty($pago->fechaCheque)) {
                    throw new \InvalidArgumentException('Los datos del cheque están incompletos.');
                }

                $referencia = $pago->numeroCheque;
                $fechaPago = $pago->fechaCheque;
                break;

            case '04':
                if (empty($pago->marcaTarjeta) || empty($pago->loteTarjeta) || empty($pago->autorizacionTarjeta) || !preg_match('/^\d{4}$/', (string) ($pago->ultimosDigitos ?? '')) || empty($pago->fechaVoucher) || empty($pago->nota)) {
                    throw new \InvalidArgumentException('Los datos de la tarjeta están incompletos.');
                }

                $referencia = $pago->autorizacionTarjeta;
                $fechaPago = $pago->fechaVoucher;
                break;
        }

        $ultimoPago = $this->ccm->getData('cc_pagos', ['fk_proyecto' => getProyectoId()], 'pg_numero_secuencial', ['id' => 'DESC'], 1);

        $secuencial = $ultimoPago ? (int) $ultimoPago->pg_numero_secuencial + 1 : 1;

        $formData = [
            'fk_proveedor' => $cxp->fk_proveedor,
            'fk_proyecto' => getProyectoId(),
            'pg_numero_secuencial' => (string) $secuencial,
            'pg_fecha' => $fechaPago,
            'fk_forma_pago' => $formaPago,
            'fk_cuenta_contable' => $cuentaContable,
            'fk_banco' => $bancoId,
            'pg_referencia' => $referencia,
            'pg_numero_transferencia' => $pago->numeroTransferencia ?? null,
            'pg_fecha_transferencia' => $pago->fechaTransferencia ?? null,
            'pg_numero_cheque' => $pago->numeroCheque ?? null,
            'pg_fecha_cheque' => $pago->fechaCheque ?? null,
            'pg_marca_tarjeta' => $pago->marcaTarjeta ?? null,
            'pg_lote_tarjeta' => $pago->loteTarjeta ?? null,
            'pg_autorizacion_tarjeta' => $pago->autorizacionTarjeta ?? null,
            'pg_ultimos_digitos' => $pago->ultimosDigitos ?? null,
            'pg_fecha_voucher' => $pago->fechaVoucher ?? null,
            'pg_valor' => $cxp->cxp_total,
            'pg_estado' => 'ACTIVO',
            'pg_observacion' => $pago->nota ?? null,
            'fk_user' => $this->user->id,
        ];

        $pagoId = (int) $this->ccm->guardar($formData, 'cc_pagos');

        $formDataDet = [
            'fk_pago' => $pagoId,
            'fk_proyecto' => getProyectoId(),
            'fk_cxp' => $cxpId,
            'fk_cuota' => null,
            'pgd_valor' => $cxp->cxp_total,
        ];
        $this->ccm->guardar($formDataDet, 'cc_pagos_det');

        return $pagoId;
    }

    public function guardarCuotas(int $cxpId, array $cuotas): int {

        $cxp = $this->ccm->getData('cc_cxp', ['id' => $cxpId, 'fk_proyecto' => getProyectoId()], '*', null, 1);

        if (!$cxp) {
            throw new \RuntimeException('No se encontró la cuenta por pagar.');
        }

        if ($cxp->cxp_tipo_pago !== 'CREDITO') {
            throw new \RuntimeException(
                            'Solo se pueden generar cuotas para compras registradas a crédito.'
            );
        }

        if (count($cuotas) !== (int) $cxp->cxp_num_cuotas) {
            throw new \InvalidArgumentException('La cantidad de cuotas no coincide con la cantidad de cuotas registrada en la compra compra.'
            );
        }

        $totalCuotas = 0;
        $numeros = [];

        foreach ($cuotas as $cuota) {
            $numero = (int) ($cuota->numero ?? 0);
            $valor = round((float) ($cuota->valor ?? 0), 4);
            $fecha = $cuota->fecha ?? null;

            if ($numero <= 0 || in_array($numero, $numeros, true)) {
                throw new \InvalidArgumentException('La numeración de las cuotas no es válida.');
            }

            if (!$fecha || $valor <= 0) {
                throw new \InvalidArgumentException("Revise la fecha y el valor de la cuota {$numero}.");
            }

            $numeros[] = $numero;
            $totalCuotas += $valor;
        }

        if (round($totalCuotas, 4) !== round((float) $cxp->cxp_total, 4)) {
            throw new \InvalidArgumentException('La suma de las cuotas no coincide con el total por pagar.');
        }

        foreach ($cuotas as $cuota) {
            $valor = round((float) $cuota->valor, 4);

            $formData = [
                'fk_cxp' => $cxpId,
                'fk_proyecto' => getProyectoId(),
                'cxpc_numero' => (int) $cuota->numero,
                'cxpc_fecha_vencimiento' => $cuota->fecha,
                'cxpc_valor' => $valor,
                'cxpc_pagado' => 0,
                'cxpc_saldo' => $valor,
                'cxpc_estado' => 'PENDIENTE',
            ];

            $this->ccm->guardar($formData, 'cc_cxp_cuotas');
        }

        return count($cuotas);
    }

    public function anularRetencionCompra(int $compraId): void {
        $compra = $this->ccm->getData('cc_compras', ['id' => $compraId, 'fk_proyecto' => getProyectoId()], 'id, fk_retencion', null, 1);

        if (!$compra || empty($compra->fk_retencion)) {
            return;
        }

        $actualizado = $this->ccm->actualizar('cc_retencion', ['ret_estado' => 0], ['id' => $compra->fk_retencion, 'fk_proyecto' => getProyectoId()]);

        if (!$actualizado) {
            throw new \RuntimeException('No se pudo anular la retencion de la compra.');
        }
    }

    public function validarSinPagosActivosCredito(int $compraId): void {
        $cxp = $this->ccm->getData('cc_cxp', ['fk_compra' => $compraId, 'fk_proyecto' => getProyectoId()], 'id, cxp_tipo_pago', null, 1);

        if (!$cxp || $cxp->cxp_tipo_pago !== 'CREDITO') {
            return;
        }

        $pagosDet = $this->ccm->getData('cc_pagos_det', ['fk_cxp' => $cxp->id, 'fk_proyecto' => getProyectoId()], 'fk_pago');

        foreach ($pagosDet as $pagoDet) {
            $pago = $this->ccm->getData('cc_pagos', ['id' => (int) $pagoDet->fk_pago, 'fk_proyecto' => getProyectoId(), 'pg_estado' => 'ACTIVO'], 'id, pg_numero_secuencial', null, 1);

            if ($pago) {
                throw new \RuntimeException("No se puede anular la compra porque tiene pagos activos registrados. Primero anule el pago #{$pago->pg_numero_secuencial}.");
            }
        }
    }

    public function validarRetencionAnulableCompra(int $compraId): void {
        $compra = $this->ccm->getData('cc_compras', ['id' => $compraId, 'fk_proyecto' => getProyectoId()], 'id, fk_retencion', null, 1);

        if (!$compra || empty($compra->fk_retencion)) {
            return;
        }

        $retencion = $this->ccm->getData( 'cc_retencion', ['id' => $compra->fk_retencion, 'fk_proyecto' => getProyectoId(), 'ret_estado' => 1],'id, ret_numero_comprobante, ret_estado_sri', null, 1);

        if (!$retencion) {
            return;
        }

        if (in_array($retencion->ret_estado_sri, ['ENVIADO', 'AUTORIZADO'], true)) {
            throw new \RuntimeException("No se puede anular la compra porque la retencion #{$retencion->ret_numero_comprobante} esta {$retencion->ret_estado_sri}.");
        }
    }

    public function anularPagosCompra(int $compraId): void {
        $cxp = $this->ccm->getData('cc_cxp', ['fk_compra' => $compraId, 'fk_proyecto' => getProyectoId()], 'id', null, 1);

        if (!$cxp) {
            return;
        }

        $pagosDet = $this->ccm->getData('cc_pagos_det', ['fk_cxp' => $cxp->id, 'fk_proyecto' => getProyectoId()], 'fk_pago');
        $pagosIds = [];

        foreach ($pagosDet as $pagoDet) {
            $pagoId = (int) $pagoDet->fk_pago;

            if ($pagoId > 0 && !in_array($pagoId, $pagosIds, true)) {
                $pagosIds[] = $pagoId;
            }
        }

        foreach ($pagosIds as $pagoId) {
            $actualizado = $this->ccm->actualizar('cc_pagos', ['pg_estado' => 'ANULADO'], ['id' => $pagoId, 'fk_proyecto' => getProyectoId()]);

            if (!$actualizado) {
                throw new \RuntimeException('No se pudo anular uno de los pagos de la compra.');
            }
        }
    }

    public function anularCuentaPorPagarCompra(int $compraId): void {
        $cxp = $this->ccm->getData('cc_cxp', ['fk_compra' => $compraId, 'fk_proyecto' => getProyectoId()], 'id', null, 1);

        if (!$cxp) {
            return;
        }

        $this->ccm->actualizar('cc_cxp_cuotas', ['cxpc_estado' => 'ANULADO'], ['fk_cxp' => $cxp->id, 'fk_proyecto' => getProyectoId()]);

        $actualizado = $this->ccm->actualizar('cc_cxp', ['cxp_estado' => 'ANULADO', 'cxp_saldo' => 0,], ['id' => $cxp->id, 'fk_proyecto' => getProyectoId()]);

        if (!$actualizado) {
            throw new \RuntimeException('No se pudo anular la cuenta por pagar de la compra.');
        }
    }

    private function guardarDetalleRetencion(int $retencionId, object $detalle): int {

        $tipo = strtoupper((string) ($detalle->tipo ?? ''));
        $baseImponible = (float) ($detalle->baseImponible ?? 0);
        $valorRetenido = (float) ($detalle->valorRetenido ?? 0);

        if (!in_array($tipo, ['IVA', 'RENTA'], true)) {
            throw new \InvalidArgumentException('El tipo de retención no es válido.');
        }

        if ($baseImponible <= 0 || $valorRetenido <= 0) {
            throw new \InvalidArgumentException('La base y el valor retenido deben ser mayores a cero.');
        }

        $formData = [
            'fk_retencion' => $retencionId,
            'retd_tipo_retencion' => $tipo,
            'retd_codigo_sri' => $detalle->codigoSri,
            'retd_porcentaje' => (float) $detalle->porcentaje,
            'retd_base_imponible' => $baseImponible,
            'retd_valor_retenido' => $valorRetenido,
            'retd_descripcion' => $detalle->descripcion ?? null,
            'fk_sri_retencion' => (int) $detalle->retencionId,
        ];

        return (int) $this->ccm->guardar($formData, 'cc_retencion_det');
    }

    private function validarPuntoEmisionRetencion(object $retencion): object {
        $establecimiento = trim((string) ($retencion->numeroEstablecimiento ?? ''));
        $emision = trim((string) ($retencion->numeroEmision ?? ''));
        $numeroComprobante = trim((string) ($retencion->numeroComprobante ?? ''));

        if ($establecimiento === '' || $emision === '') {
            throw new \InvalidArgumentException('Debe indicar establecimiento y punto de emisión de la retención.');
        }

        $puntoEmision = $this->comprasModel->obtenerPuntoEmisionRetencion($establecimiento, $emision);

        if (!$puntoEmision) {
            throw new \RuntimeException("No existe un punto de emisión activo para retenciones {$establecimiento}-{$emision}.");
        }

        $puedeEmitir = $this->comprasModel->usuarioPuedeEmitirEnPuntoRetencion((int) $puntoEmision->id, (int) $this->user->id);

        if (!$puedeEmitir) {
            throw new \RuntimeException('No puede emitir retenciones. Su usuario no está registrado en este punto de emisión.');
        }

        $secuencial = (int) $puntoEmision->pv_sec_actual;
        $secuencialInicial = (int) $puntoEmision->pv_sec_inicial;
        $secuencialFinal = (int) $puntoEmision->pv_sec_final;

        if ($secuencial < $secuencialInicial || $secuencial > $secuencialFinal) {
            throw new \RuntimeException('El secuencial actual del punto de emisión está fuera del rango autorizado.');
        }

        if ((int) $numeroComprobante !== $secuencial) {
            $numeroEsperado = str_pad((string) $secuencial, 9, '0', STR_PAD_LEFT);
            throw new \RuntimeException("El secuencial de retención debe ser {$numeroEsperado}.");
        }

        return $puntoEmision;
    }
}
