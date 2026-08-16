<!--
/**
 * Description of viewAsientoContable
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 16 ago 2026
 * @time 4:16:15 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<?php
$venta = $venta ?? null;
$asientos = $asientos ?? [];
?>

<?php if (empty($asientos)): ?>
    <div class="alert alert-warning mb-0">
        No se encontraron asientos contables de la venta.
    </div>
<?php else: ?>
    <div class="bg-white border rounded p-3">
        <?php if (!empty($venta)): ?>
            <div class="mb-3">
                <h6 class="fw-bold text-system mb-1">
                    Venta #<?= esc(str_pad((string) $venta->ven_secuencial, 5, '0', STR_PAD_LEFT)) ?>
                </h6>
                <div class="small text-muted">
                    <?= esc($venta->ven_numero_establecimiento . '-' . $venta->ven_numero_emision . '-' . $venta->ven_numero_comprobante) ?>
                    | Cliente: <?= esc($venta->clie_razon_social ?? '-') ?>
                </div>
            </div>
        <?php endif; ?>

        <?php foreach ($asientos as $asiento): ?>
            <div class="border rounded mb-3">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 18%;">Numero de asiento</th>
                                <td style="width: 18%;"><?= esc(str_pad((string) $asiento->ac_secuencial, 5, '0', STR_PAD_LEFT)) ?></td>
                                <th style="width: 12%;">Fecha</th>
                                <td><?= esc($asiento->ac_fecha ?? '') ?></td>
                                <th style="width: 12%;">Tipo</th>
                                <td><?= esc($asiento->ac_codigo_transaccion ?? '') ?></td>
                            </tr>
                            <tr>
                                <th>Detalle</th>
                                <td colspan="3"><?= esc($asiento->ac_detalle ?? '') ?></td>
                                <th>Usuario</th>
                                <td><?= esc($asiento->usuario_registra ?? '-') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Codigo</th>
                                <th>Cuenta contable</th>
                                <th>Detalle</th>
                                <th>Centro de costo</th>
                                <th class="text-end">Debe</th>
                                <th class="text-end">Haber</th>
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
                                    <td><?= esc($detalle->acd_detalle ?? '') ?></td>
                                    <td><?= esc($detalle->centro_costo ?? '-') ?></td>
                                    <td class="text-end">
                                        <?= ($detalle->acd_tipo ?? '') === 'DEBE' ? '$' . number_format((float) $detalle->acd_valor, 2) : '-' ?>
                                    </td>
                                    <td class="text-end">
                                        <?= ($detalle->acd_tipo ?? '') === 'HABER' ? '$' . number_format((float) $detalle->acd_valor, 2) : '-' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-success fw-bold">
                            <tr>
                                <td colspan="4" class="text-end">TOTALES</td>
                                <td class="text-end">$<?= number_format((float) ($asiento->totalDebe ?? 0), 2) ?></td>
                                <td class="text-end">$<?= number_format((float) ($asiento->totalHaber ?? 0), 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
