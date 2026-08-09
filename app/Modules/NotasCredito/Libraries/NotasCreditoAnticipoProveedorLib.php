<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\NotasCredito\Libraries;

/**
 * Description of NotasCreditoAnticipoProveedorLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 28 jul 2026
 * @time 10:38:15 a.m.
 */
class NotasCreditoAnticipoProveedorLib {

    protected $ccm;
    protected $user;

    public function __construct() {
        $this->ccm = service('ccModel');
        $this->user = service('userSecion');
    }

    public function guardarAnticipoProveedorNotaCredito(int $notaCreditoId, object $dataPostNotaCredito, ?float $valorAnticipo = null): int {

        $compra = $dataPostNotaCredito->compra;
        $valorNotaCredito = round((float) ($valorAnticipo ?? ($dataPostNotaCredito->totales->total ?? 0)), 6);
        $proveedorId = (int) ($compra->compProveedor ?? 0);
        $proyectoId = (int) getProyectoId();

        if ($valorNotaCredito <= 0) {
            throw new \RuntimeException('El valor de la nota de credito debe ser mayor a cero para generar el anticipo.');
        }

        if ($proveedorId <= 0 || $proyectoId <= 0) {
            throw new \RuntimeException('No se recibio proveedor o proyecto para generar el anticipo a proveedor.');
        }

        $anticipoExistente = $this->ccm->getData('cc_anticipo_proveedor', ['fk_ndc' => $notaCreditoId, 'fk_proyecto' => $proyectoId], 'id, antp_estado', null, 1);

        if ($anticipoExistente && $anticipoExistente->antp_estado !== 'ANULADO') {
            throw new \RuntimeException('La nota de credito ya tiene un anticipo a proveedor activo.');
        }

        $ultimoAnticipo = $this->ccm->getData('cc_anticipo_proveedor', ['fk_proyecto' => $proyectoId], 'antp_secuencial', ['antp_secuencial' => 'DESC'], 1);
        $secuencial = $ultimoAnticipo ? (int) $ultimoAnticipo->antp_secuencial + 1 : 1;

        $dataAnticipo = [
            'antp_secuencial' => $secuencial,
            'fk_proveedor' => $proveedorId,
            'fk_proyecto' => $proyectoId,
            'antp_tipo' => 'NDC_COMPRA',
            'fk_ndc' => $notaCreditoId,
            'antp_valor' => $valorNotaCredito,
            'antp_saldo' => $valorNotaCredito,
            'antp_fecha' => $compra->compFechaEmision,
            'antp_hora' => date('H:i:s'),
            'antp_detalle' => $this->resolverDetalleAnticipo($notaCreditoId, $compra, $valorAnticipo),
            'antp_estado' => 'ACTIVO',
            'fk_user' => $this->user->id,
        ];

        $anticipoId = (int) $this->ccm->guardar($dataAnticipo, 'cc_anticipo_proveedor');

        if (!$anticipoId) {
            throw new \RuntimeException('No se pudo registrar el anticipo a proveedor.');
        }

        $this->actualizarSaldoAnticipoProveedor($proveedorId, $proyectoId, $valorNotaCredito);

        return $anticipoId;
    }

    private function resolverDetalleAnticipo(int $notaCreditoId, object $compra, ?float $valorAnticipo): string {

        $detalle = trim((string) ($compra->observacionFinanciera ?? ''));

        if ($detalle !== '') {
            return $detalle;
        }

        if ($valorAnticipo !== null) {
            return "Anticipo generado por excedente de NDC #{$notaCreditoId}";
        }

        return "Anticipo generado por NDC #{$notaCreditoId}";
    }

    public function anularAnticipoProveedorNotaCredito(int $notaCreditoId, string $motivoAnulacion): void {

        $anticipo = $this->ccm->getData('cc_anticipo_proveedor', ['fk_ndc' => $notaCreditoId, 'fk_proyecto' => getProyectoId()], '*', null, 1);

        if (!$anticipo || $anticipo->antp_estado === 'ANULADO') {
            return;
        }

        $aplicacionActiva = $this->ccm->getData('cc_anticipo_proveedor_aplicacion', ['fk_anticipo' => (int) $anticipo->id, 'fk_proyecto' => getProyectoId(), 'apli_estado' => 'ACTIVO'], 'id', null, 1);

        if ($aplicacionActiva) {
            throw new \RuntimeException('No se puede anular la nota de credito porque el anticipo ya tiene aplicaciones activas.');
        }

        $valorOriginal = round((float) $anticipo->antp_valor, 6);
        $saldoDisponible = round((float) $anticipo->antp_saldo, 6);

        if (abs($valorOriginal - $saldoDisponible) > 0.000001) {
            throw new \RuntimeException('No se puede anular la nota de credito porque el anticipo ya fue aplicado parcial o totalmente.');
        }

        $this->actualizarSaldoAnticipoProveedor((int) $anticipo->fk_proveedor, (int) $anticipo->fk_proyecto, -$saldoDisponible);

        $dataSet = [
            'antp_estado' => 'ANULADO',
            'fk_user_anula' => $this->user->id,
            'antp_fecha_anulacion' => date('Y-m-d H:i:s'),
            'antp_motivo_anulacion' => trim($motivoAnulacion),
        ];

        $this->ccm->actualizar('cc_anticipo_proveedor', $dataSet, ['id' => (int) $anticipo->id, 'fk_proyecto' => getProyectoId()]);
    }

    private function actualizarSaldoAnticipoProveedor(int $proveedorId, int $proyectoId, float $valor): void {

        $valor = round($valor, 6);

        if (abs($valor) <= 0.000001) {
            return;
        }

        $saldo = $this->ccm->getData('cc_anticipo_proveedor_saldos', ['fk_proveedor' => $proveedorId, 'fk_proyecto' => $proyectoId], '*', null, 1);

        if (!$saldo) {
            if ($valor < 0) {
                throw new \RuntimeException('No existe saldo acumulado de anticipo para descontar.');
            }

            $this->ccm->guardar([
                'fk_proveedor' => $proveedorId,
                'fk_proyecto' => $proyectoId,
                'saldo' => $valor,
            ], 'cc_anticipo_proveedor_saldos');
            return;
        }

        $nuevoSaldo = round((float) $saldo->saldo + $valor, 6);

        if ($nuevoSaldo < -0.000001) {
            throw new \RuntimeException('El saldo acumulado de anticipos no puede quedar negativo.');
        }

        $this->ccm->actualizar('cc_anticipo_proveedor_saldos', ['saldo' => max(0, $nuevoSaldo)], ['id' => (int) $saldo->id,'fk_proyecto' => $proyectoId]);
    }
}
