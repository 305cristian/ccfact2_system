<?php


/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Modules\Ventas\Libraries;

use Modules\Comun\Libraries\AsientoContableLib;
use Modules\Comun\Libraries\CuentasConfigLib;


/**
 * Description of VentasAsientosLib
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 9 ago 2026
 * @time 1:11:15 p.m.
 */
class VentasAsientosLib {

    protected $ccm;
    protected AsientoContableLib $asientoLib;
    protected CuentasConfigLib $cuentasConfigLib;
    protected string $tipoTransaccionVenta = '01';
    protected string $tipoTransaccionCobro = '05';
    protected string $tipoTransaccionCosto = '22';

    public function __construct() {
        $this->ccm = service('ccModel');
        $this->asientoLib = new AsientoContableLib();
        $this->cuentasConfigLib = new CuentasConfigLib();
    }

    public function generarAsientosVenta(int $ventaId): array {
        $venta = $this->obtenerVenta($ventaId);
        $detalles = $this->obtenerDetallesVenta($ventaId);

        return [
            'venta' => $this->generarAsientoVenta($venta, $detalles),
            'costo' => $this->generarAsientoCostoVenta($venta, $detalles),
        ];
    }

    public function anularAsientosVenta(int $ventaId): void {
        $this->anularAsientoSiExiste($this->tipoTransaccionVenta, $ventaId);
        $this->anularAsientoSiExiste($this->tipoTransaccionCosto, $ventaId);
        $this->anularAsientosCobrosVenta($ventaId);
    }

    private function anularAsientosCobrosVenta(int $ventaId): void {
        $cxc = $this->ccm->getData('cc_cxc', ['fk_venta' => $ventaId, 'fk_proyecto' => getProyectoId()], 'id', null, 1);

        if (!$cxc) {
            return;
        }

        $detallesCobros = $this->ccm->getData('cc_cobros_det', ['fk_cxc' => (int) $cxc->id, 'fk_proyecto' => getProyectoId()], 'fk_cobro');
        $cobrosIds = array_values(array_unique(array_map(static fn($detalle) => (int) $detalle->fk_cobro, $detallesCobros ?? [])));

        foreach ($cobrosIds as $cobroId) {
            $this->anularAsientoSiExiste($this->tipoTransaccionCobro, $cobroId);
        }
    }

    public function generarAsientoCobroContado(int $cobroId): int {
        $cobro = $this->ccm->getData('cc_cobros', ['id' => $cobroId, 'fk_proyecto' => getProyectoId(), 'cob_estado' => 'ACTIVO'], '*', null, 1);

        if (!$cobro) {
            throw new \RuntimeException('No se encontro el cobro registrado.');
        }

        $this->validarAsientoDuplicado($this->tipoTransaccionCobro, $cobroId, 'El cobro ya tiene un asiento contable registrado.');

        $detalles = $this->ccm->getData('cc_cobros_det', ['fk_cobro' => $cobroId, 'fk_proyecto' => getProyectoId()], '*');

        if (empty($detalles)) {
            throw new \RuntimeException('El cobro no tiene detalle de aplicacion.');
        }

        $totalDetalle = array_reduce($detalles, static fn($total, $detalle) => $total + (float) $detalle->cobd_valor, 0.0);
        $valorCobro = round((float) $cobro->cob_valor, 4);

        if ($valorCobro <= 0) {
            throw new \RuntimeException('El valor del cobro debe ser mayor a cero.');
        }

        if (abs(round($totalDetalle, 4) - $valorCobro) > 0.01) {
            throw new \RuntimeException('El detalle del cobro no coincide con el valor total cobrado.');
        }

        $cuentaCobro = trim((string) ($cobro->fk_cuenta_contable ?? ''));

        if ($cuentaCobro === '') {
            throw new \RuntimeException('El cobro no tiene cuenta contable de caja/banco asignada.');
        }

        $cuentaCxc = $this->cuentasConfigLib->obtenerSettingCuentaContable('025');

        if (!$cuentaCxc) {
            throw new \RuntimeException('No esta configurada la cuenta contable 025 para cuentas por cobrar clientes.');
        }

        $centroCosto = $this->obtenerCentroCostoCobro($detalles);
        $detalleAsiento = 'COBRO CXC NRO. ' . str_pad((string) $cobro->cob_numero_secuencial, 5, '0', STR_PAD_LEFT);
        $asientoId = $this->asientoLib->guardarAsiento($this->tipoTransaccionCobro, $cobroId, $detalleAsiento, $cobro->cob_fecha);

        $this->asientoLib->guardarDetalleAsiento($asientoId, $cuentaCobro, $valorCobro, 'DEBE', $this->tipoTransaccionCobro, $cobroId, 'Cobro a cliente', $cobro->fk_forma_pago, $cobroId, $centroCosto);
        $this->asientoLib->guardarDetalleAsiento($asientoId, $cuentaCxc, $valorCobro, 'HABER', $this->tipoTransaccionCobro, $cobroId, 'Cancelacion cuenta por cobrar', $cobro->fk_forma_pago, $cobroId, $centroCosto);

        if (!$this->asientoLib->validarAsientoCuadrado($asientoId)) {
            throw new \RuntimeException('El asiento contable del cobro no esta cuadrado.');
        }

        return (int) $asientoId;
    }

    protected function obtenerVenta(int $ventaId): object {
        $venta = $this->ccm->getData('cc_ventas', ['id' => $ventaId, 'fk_proyecto' => getProyectoId()], '*', null, 1);

        if (!$venta) {
            throw new \RuntimeException('No se encontro la venta registrada.');
        }

        if ($venta->ven_estado !== 'ARCHIVADO') {
            throw new \RuntimeException('Solo las ventas archivadas generan asientos contables.');
        }

        return $venta;
    }

    protected function obtenerDetallesVenta(int $ventaId): array {
        $detalles = $this->ccm->getData('cc_ventas_det', ['fk_venta' => $ventaId, 'fk_proyecto' => getProyectoId(), 'vend_estado' => 1], '*');

        if (empty($detalles)) {
            throw new \RuntimeException('La venta no tiene detalles para generar asientos contables.');
        }

        return $detalles;
    }

    protected function generarAsientoVenta(object $venta, array $detalles): int {
        $this->validarAsientoDuplicado($this->tipoTransaccionVenta, (int) $venta->id, 'La venta ya tiene un asiento contable registrado.');

        $detalleAsiento = $this->obtenerDetalleDocumento('VENTA NRO.', $venta);
        $asientoId = $this->asientoLib->guardarAsiento($this->tipoTransaccionVenta, (int) $venta->id, $detalleAsiento, $venta->ven_fecha_emision);

        $this->guardarDebitoCliente($asientoId, $venta);
        $this->guardarCreditosItems($asientoId, $venta, $detalles);
        $this->guardarCreditosIva($asientoId, $venta);

        if (!$this->asientoLib->validarAsientoCuadrado($asientoId)) {
            throw new \RuntimeException('El asiento contable de la venta no esta cuadrado.');
        }

        return (int) $asientoId;
    }

    protected function generarAsientoCostoVenta(object $venta, array $detalles): ?int {
        $totalCosto = array_reduce($detalles, static fn($total, $detalle) => $total + (float) $detalle->vend_costo_total, 0.0);

        if (round($totalCosto, 4) <= 0) {
            return null;
        }

        $this->validarAsientoDuplicado($this->tipoTransaccionCosto, (int) $venta->id, 'La venta ya tiene un asiento de costo registrado.');

        $detalleAsiento = $this->obtenerDetalleDocumento('COSTO VENTA NRO.', $venta);
        $asientoId = $this->asientoLib->guardarAsiento($this->tipoTransaccionCosto, (int) $venta->id, $detalleAsiento, $venta->ven_fecha_emision);

        $this->guardarDebitosCostoVenta($asientoId, $venta, $detalles);
        $this->guardarCreditosInventario($asientoId, $venta, $detalles);

        if (!$this->asientoLib->validarAsientoCuadrado($asientoId)) {
            throw new \RuntimeException('El asiento contable de costo de venta no esta cuadrado.');
        }

        return (int) $asientoId;
    }

    protected function guardarDebitoCliente(int $asientoId, object $venta): void {
        $cuenta = $this->cuentasConfigLib->obtenerSettingCuentaContable('025');

        if (!$cuenta) {
            throw new \RuntimeException('No esta configurada la cuenta contable 025 para cuentas por cobrar clientes.');
        }

        $valor = round((float) $venta->ven_total, 4);

        if ($valor <= 0) {
            throw new \RuntimeException('El total de la venta debe ser mayor a cero.');
        }

        $this->asientoLib->guardarDetalleAsiento($asientoId, $cuenta, $valor, 'DEBE', $this->tipoTransaccionVenta, (int) $venta->id, 'Cuenta por cobrar cliente', null, null, $venta->fk_centro_costo);
    }

    protected function guardarCreditosItems(int $asientoId, object $venta, array $detalles): void {
        $cuentas = [];
        $subtotalDetalles = 0.0;

        foreach ($detalles as $detalle) {
            $cuenta = trim((string) ($detalle->vend_cta_venta ?? ''));

            if ($cuenta === '') {
                $cuenta = $this->cuentasConfigLib->obtenerSettingCuentaContable('029');

                if (!$cuenta) {
                    throw new \RuntimeException("El producto {$detalle->fk_producto} no tiene cuenta contable de ventas y no esta configurada la cuenta general 029.");
                }
            }

            $valor = (float) $detalle->vend_total_neto;

            if ($valor < 0) {
                throw new \RuntimeException("El producto {$detalle->fk_producto} tiene un valor neto invalido.");
            }

            $cuentas[$cuenta] = ($cuentas[$cuenta] ?? 0) + $valor;
            $subtotalDetalles += $valor;
        }

        $subtotalVenta = round((float) $venta->ven_subtotal_neto, 4);

        if ($subtotalDetalles <= 0 || $subtotalVenta < 0) {
            throw new \RuntimeException('El subtotal de la venta no es valido para generar el asiento.');
        }

        $cuentasContables = array_keys($cuentas);
        $ultimaCuenta = array_key_last($cuentasContables);
        $valorAcumulado = 0.0;

        foreach ($cuentasContables as $indice => $cuenta) {
            if ($indice === $ultimaCuenta) {
                $valorCuenta = round($subtotalVenta - $valorAcumulado, 4);
            } else {
                $proporcion = $cuentas[$cuenta] / $subtotalDetalles;
                $valorCuenta = round($subtotalVenta * $proporcion, 4);
                $valorAcumulado += $valorCuenta;
            }

            if ($valorCuenta <= 0) {
                continue;
            }

            $this->asientoLib->guardarDetalleAsiento($asientoId, $cuenta, $valorCuenta, 'HABER', $this->tipoTransaccionVenta, (int) $venta->id, 'Venta - bienes y servicios', null, null, $venta->fk_centro_costo);
        }
    }

    protected function guardarCreditosIva(int $asientoId, object $venta): void {
        $bases = $this->ccm->getData('cc_ventas_bases_impuesto', ['fk_venta' => $venta->id, 'fk_proyecto' => getProyectoId()], '*');
        $cuentasIva = [];
        $totalBasesIva = 0.0;

        foreach ($bases ?? [] as $base) {
            $valorIva = (float) $base->imp_valor;

            if ($valorIva <= 0) {
                continue;
            }

            $porcentaje = (float) $base->imp_porcentaje;
            $cuentaIva = $this->obtenerCuentaContableIvaVenta((int) $base->fk_impuesto_tarifa, (string) $venta->ven_fecha_emision);

            if (!isset($cuentasIva[$cuentaIva])) {
                $cuentasIva[$cuentaIva] = ['porcentaje' => $porcentaje, 'valor' => 0.0];
            }

            $cuentasIva[$cuentaIva]['valor'] += $valorIva;
            $totalBasesIva += $valorIva;
        }

        if (abs($totalBasesIva - round((float) $venta->ven_totaliva, 4)) > 0.01) {
            throw new \RuntimeException('El IVA de las bases no coincide con el total del IVA de la venta.');
        }

        foreach ($cuentasIva as $cuenta => $datosIva) {
            $this->asientoLib->guardarDetalleAsiento($asientoId, $cuenta, round($datosIva['valor'], 4), 'HABER', $this->tipoTransaccionVenta, (int) $venta->id, "IVA ventas {$datosIva['porcentaje']}%", null, null, $venta->fk_centro_costo);
        }
    }

    protected function guardarDebitosCostoVenta(int $asientoId, object $venta, array $detalles): void {
        $cuentaGeneralCosto = $this->cuentasConfigLib->obtenerSettingCuentaContable('028');

        if (!$cuentaGeneralCosto) {
            throw new \RuntimeException('No esta configurada la cuenta contable 028 para costo de venta.');
        }

        $cuentas = [];

        foreach ($detalles as $detalle) {
            $valor = round((float) $detalle->vend_costo_total, 4);

            if ($valor <= 0) {
                continue;
            }

            $cuenta = trim((string) ($detalle->vend_cta_costo ?? '')) ?: $cuentaGeneralCosto;
            $cuentas[$cuenta] = ($cuentas[$cuenta] ?? 0) + $valor;
        }

        foreach ($cuentas as $cuenta => $valor) {
            $this->asientoLib->guardarDetalleAsiento($asientoId, $cuenta, round($valor, 4), 'DEBE', $this->tipoTransaccionCosto, (int) $venta->id, 'Costo de venta', null, null, $venta->fk_centro_costo);
        }
    }

    protected function guardarCreditosInventario(int $asientoId, object $venta, array $detalles): void {
        $cuentas = [];

        foreach ($detalles as $detalle) {
            $valor = round((float) $detalle->vend_costo_total, 4);

            if ($valor <= 0) {
                continue;
            }

            $cuenta = trim((string) ($detalle->vend_cta_inventario ?? ''));

            if ($cuenta === '') {
                throw new \RuntimeException("El producto {$detalle->fk_producto} no tiene cuenta contable de inventario.");
            }

            $cuentas[$cuenta] = ($cuentas[$cuenta] ?? 0) + $valor;
        }

        foreach ($cuentas as $cuenta => $valor) {
            $this->asientoLib->guardarDetalleAsiento($asientoId, $cuenta, round($valor, 4), 'HABER', $this->tipoTransaccionCosto, (int) $venta->id, 'Salida de inventario por venta', null, null, $venta->fk_centro_costo);
        }
    }

    protected function obtenerCuentaContableIvaVenta(int $tarifaId, string $fechaEmision): string {
        $tarifa = $this->ccm->getData('cc_impuesto_tarifa', ['id' => $tarifaId, 'fk_impuesto' => 1], 'id, impt_porcentage, impt_estado, impt_grupo, impt_fecha_inicio_vigencia, impt_fecha_fin_vigencia', null, 1);

        if (!$tarifa || (float) $tarifa->impt_porcentage <= 0) {
            throw new \RuntimeException("La tarifa IVA {$tarifaId} no existe o no genera IVA.");
        }

        if ($this->esTarifaHistoricaParaFecha($tarifa, $fechaEmision)) {
            $cuentaHistorica = $this->ccm->getValueWhere('cc_impuesto_tarifa_cuenta_contable', ['fk_impuesto_tarifa' => $tarifaId, 'tipo_movimiento' => 'VENTA', 'tipo_cuenta' => 'IVA', 'estado' => 1], 'fk_cuentacontable_det');

            if ($cuentaHistorica) {
                return (string) $cuentaHistorica;
            }

            throw new \RuntimeException("La tarifa IVA {$tarifa->impt_porcentage}% historica no tiene configurada la cuenta contable de IVA para ventas.");
        }

        $codigoConfig = $this->obtenerConfiguracionIvaVenta($tarifa);
        $cuenta = $this->cuentasConfigLib->obtenerSettingCuentaContable($codigoConfig);

        if (!$cuenta) {
            throw new \RuntimeException("No esta configurada la cuenta contable {$codigoConfig} para IVA ventas {$tarifa->impt_porcentage}%.");
        }

        return $cuenta;
    }

    protected function obtenerConfiguracionIvaVenta(object $tarifa): string {
        if (($tarifa->impt_grupo ?? '') === 'GENERAL') {
            return '026';
        }

        if (($tarifa->impt_grupo ?? '') === 'ESPECIAL') {
            return '027';
        }

        throw new \RuntimeException("No existe configuracion contable para IVA ventas {$tarifa->impt_porcentage}%.");
    }

    protected function esTarifaHistoricaParaFecha(object $tarifa, string $fechaEmision): bool {
        if (($tarifa->impt_estado ?? '') !== 'HISTORIAL') {
            return false;
        }

        $fechaInicio = $tarifa->impt_fecha_inicio_vigencia ?? null;
        $fechaFin = $tarifa->impt_fecha_fin_vigencia ?? null;

        if ($fechaInicio && $fechaInicio !== '0000-00-00' && $fechaInicio > $fechaEmision) {
            return false;
        }

        if ($fechaFin && $fechaFin !== '0000-00-00' && $fechaFin < $fechaEmision) {
            return false;
        }

        return true;
    }

    protected function validarAsientoDuplicado(string $tipoTransaccion, int $documentoId, string $mensaje): void {
        $asientoExistente = $this->ccm->getData('cc_asiento_contable', ['ac_codigo_transaccion' => $tipoTransaccion, 'ac_documento_id' => $documentoId, 'fk_proyecto' => getProyectoId(), 'ac_estado' => 1], 'id', null, 1);

        if ($asientoExistente) {
            throw new \RuntimeException($mensaje);
        }
    }

    protected function anularAsientoSiExiste(string $tipoTransaccion, int $documentoId): void {
        $asiento = $this->ccm->getData('cc_asiento_contable', ['ac_codigo_transaccion' => $tipoTransaccion, 'ac_documento_id' => $documentoId, 'fk_proyecto' => getProyectoId(), 'ac_estado' => 1], 'id', null, 1);

        if (!$asiento) {
            return;
        }

        if (!$this->asientoLib->anularAsiento($tipoTransaccion, $documentoId)) {
            throw new \RuntimeException('No se pudo anular el asiento contable de la venta.');
        }
    }

    protected function obtenerCentroCostoCobro(array $detalles): ?int {
        $primerDetalle = reset($detalles);

        if (!$primerDetalle || empty($primerDetalle->fk_cxc)) {
            return null;
        }

        $cxc = $this->ccm->getData('cc_cxc', ['id' => (int) $primerDetalle->fk_cxc, 'fk_proyecto' => getProyectoId()], 'fk_venta', null, 1);

        if (!$cxc || empty($cxc->fk_venta)) {
            return null;
        }

        $venta = $this->ccm->getData('cc_ventas', ['id' => (int) $cxc->fk_venta, 'fk_proyecto' => getProyectoId()], 'fk_centro_costo', null, 1);

        return $venta && !empty($venta->fk_centro_costo) ? (int) $venta->fk_centro_costo : null;
    }

    protected function obtenerDetalleDocumento(string $prefijo, object $venta): string {
        return $prefijo . ' ' . $venta->ven_numero_establecimiento . '-' . $venta->ven_numero_emision . '-' . $venta->ven_numero_comprobante;
    }
}
