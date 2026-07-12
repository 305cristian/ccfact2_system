<!DOCTYPE html>
<!--
/**
 * Description of viewAsientoContable
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 12 jul 2026
 * @time 11:29:39 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<?php
$asiento = $asiento ?? null;
?>

<?php if (empty($asiento)): ?>
    <div class="alert alert-warning mb-0">
        No se encontro el asiento contable de la compra.
    </div>
<?php else: ?>
    <div class="bg-white border rounded p-3">
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Secuencial:</strong>
                <?= esc(str_pad((string) $asiento->ac_secuencial, 5, '0', STR_PAD_LEFT)) ?>
            </div>
            <div class="col-md-3">
                <strong>Fecha:</strong>
                <?= esc($asiento->ac_fecha ?? '') ?>
            </div>
            <div class="col-md-3">
                <strong>Estado:</strong>
                <?= (int) $asiento->ac_estado === 1 ? 'ACTIVO' : 'ANULADO' ?>
            </div>
            <div class="col-md-3">
                <strong>Usuario:</strong>
                <?= esc($asiento->usuario_registra ?? '-') ?>
            </div>
        </div>

        <div class="mb-3">
            <strong>Detalle:</strong>
            <?= esc($asiento->ac_detalle ?? '') ?>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Cuenta</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th class="text-end">Debe</th>
                        <th class="text-end">Haber</th>
                        <th>Observacion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($asiento->detalle ?? []) as $detalle): ?>
                        <tr>
                            <td class="<?= ($detalle->acd_tipo ?? '') === 'HABER' ? 'ps-4' : '' ?>">
                                <?= esc($detalle->codigo_cuenta_contable ?? '') ?>
                            </td>
                            <td class="<?= ($detalle->acd_tipo ?? '') === 'HABER' ? 'ps-4' : '' ?>">
                                <?= esc($detalle->cuenta_contable ?? '') ?>
                            </td>
                            <td><?= esc($detalle->acd_tipo ?? '') ?></td>
                            <td class="text-end">
                                <?= ($detalle->acd_tipo ?? '') === 'DEBE' ? '$' . number_format((float) $detalle->acd_valor, 2) : '' ?>
                            </td>
                            <td class="text-end">
                                <?= ($detalle->acd_tipo ?? '') === 'HABER' ? '$' . number_format((float) $detalle->acd_valor, 2) : '' ?>
                            </td>
                            <td><?= esc($detalle->acd_observacion ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="3" class="text-end">Totales</td>
                        <td class="text-end">$<?= number_format((float) ($asiento->totalDebe ?? 0), 2) ?></td>
                        <td class="text-end">$<?= number_format((float) ($asiento->totalHaber ?? 0), 2) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<?php endif; ?>
