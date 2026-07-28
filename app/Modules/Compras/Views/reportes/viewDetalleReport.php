<?php
$numeroComprobante = implode('-', array_filter([
    $compra->comp_numero_establecimiento,
    $compra->comp_numero_emision,
    $compra->comp_numero_comprobante,
        ]));

$estados = [
    'BORRADOR' => ['bg-warning', 'BORRADOR'],
    'ARCHIVADO' => ['bg-success', 'ARCHIVADA'],
    'ANULADA' => ['bg-danger', 'ANULADA'],
    'ANULADA_EN_PENDIENTE' => ['bg-secondary', 'ANULADA EN BORRADOR'],
    'ANULADA_EN_ARCHIVADA' => ['bg-dark', 'ANULADA ARCHIVADA'],
];

[$estadoClase, $estadoTexto] = $estados[$compra->comp_estado] ?? ['bg-secondary', 'DESCONOCIDO'];
$tituloComprobante = trim( ($compra->comprobante_nombre ?? 'COMPRA'));
$esNotaCredito = ($compra->comp_tipo_comprobante_cod ?? '') === '04';
$numeroCompraRelacionada = '';

if ($esNotaCredito && !empty($compra->compraRelacionada)) {
    $numeroCompraRelacionada = implode('-', array_filter([
        $compra->compraRelacionada->comp_numero_establecimiento,
        $compra->compraRelacionada->comp_numero_emision,
        $compra->compraRelacionada->comp_numero_comprobante,
    ]));
}
?>

<div class="border p-3" id="contentExport">
    <table class="table table-borderless align-middle mb-4">
        <tr>
            <td class="text-center bg-light" style="width:30%">
                <?php if (!empty($empresa->epr_logo)): ?>
                    <img src="<?= base_url('uploads/img/enterprice/' . $empresa->epr_logo) ?>"
                         style="width:120px; height:auto;" alt="Logo">
                     <?php endif; ?>
                <h6 class="fw-bold"><?= esc($empresa->epr_nombre_comercial) ?></h6>
                <small>RUC: <?= esc($empresa->epr_ruc) ?></small>
            </td>

            <td style="width:35%">
                <p><?= esc($empresa->epr_direccion) ?></p>
                <p><?= esc($empresa->epr_telefono) ?></p>
                <p><?= esc($empresa->epr_email) ?></p>
            </td>

            <td class="text-center border" style="width:35%">
                <h5 class="fw-bold"><?= esc($tituloComprobante) ?></h5>
                <h6 class="text-danger">
                    #<?= str_pad($compra->comp_secuencial, 5, '0', STR_PAD_LEFT) ?>
                </h6>
                <p><?= date('d/m/Y', strtotime($compra->comp_fecha_emision)) ?></p>
                <span class="badge <?= $estadoClase ?>"><?= $estadoTexto ?></span>
            </td>
        </tr>
    </table>

    <table class="table table-bordered mb-4">
        <tr>
            <td style="width:50%">
                <strong>Comprobante:</strong> <?= esc($numeroComprobante) ?><br>
                <strong>Tipo:</strong> <?= esc($compra->comprobante_nombre) ?><br>
                <?php if ($esNotaCredito): ?>
                    <strong>Tipo NDC:</strong> <?= esc($compra->comp_tipo_nota_credito ?: '-') ?><br>
                    <strong>Compra relacionada:</strong>
                    <?= !empty($compra->compraRelacionada) ? '#' . str_pad($compra->compraRelacionada->comp_secuencial, 5, '0', STR_PAD_LEFT) . ' / ' . esc($numeroCompraRelacionada) : '-' ?><br>
                <?php endif; ?>
                <strong>Sustento:</strong> <?= esc($compra->sus_nombre) ?><br>
                <strong>Bodega:</strong> <?= esc($compra->bod_nombre) ?><br>
                <strong>Centro de costo:</strong> <?= esc($compra->cc_nombre) ?>
            </td>
            <td style="width:50%">
                <strong>Proveedor:</strong> <?= esc($compra->prov_razon_social) ?><br>
                <strong>RUC:</strong> <?= esc($compra->prov_ruc) ?><br>
                <strong>Dirección:</strong> <?= esc($compra->prov_direccion ?: '-') ?><br>
                <strong>Tipo de pago:</strong> <?= esc($compra->comp_tipo_pago ?: '-') ?><br>
                <strong>Usuario:</strong> <?= esc($compra->user_create) ?>
            </td>
        </tr>
    </table>

    <?php if (in_array($compra->comp_estado, ['ANULADA', 'ANULADA_EN_PENDIENTE', 'ANULADA_EN_ARCHIVADA'], true)): ?>
        <div class="alert alert-danger mb-4">
            <h5 style=" border-bottom: 2px solid #34495e;color: #34495e;font-size: 1rem;font-weight: 700;margin-bottom: 1rem;margin-top: 1.5rem;padding-bottom: .4rem; text-transform: uppercase" class="section-title-report mt-0">Datos de anulacion</h5>
            <div>
                <strong>Fecha:</strong>
                <?= $compra->comp_fecha_anulacion ? date('d/m/Y H:i', strtotime($compra->comp_fecha_anulacion)) : '-' ?>
            </div>
            <div>
                <strong>Usuario:</strong>
                <?= esc($compra->usuario_anulacion ?: '-') ?>
            </div>
            <div>
                <strong>Motivo:</strong>
                <?= esc($compra->comp_motivo_anulacion ?: '-') ?>
            </div>
        </div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead class="table-secondary">
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Lote</th>
                <th class="text-end">Cant.</th>
                <th class="text-end">P. bruto</th>
                <th class="text-end">Desc.</th>
                <th class="text-end">P. neto</th>
                <th class="text-end">IVA</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($compra->detalle as $item): ?>
                <tr>
                    <td><?= esc($item->prod_codigo) ?></td>
                    <td><?= esc($item->prod_nombre) ?></td>
                    <td><?= esc($item->lote ?: '-') ?></td>
                    <td class="text-end"><?= number_format($item->compd_cantidad, 2) ?></td>
                    <td class="text-end">$<?= number_format($item->compd_precio_bruto, 2) ?></td>
                    <td class="text-end">$<?= number_format($item->compd_descuento_valor, 2) ?></td>
                    <td class="text-end">$<?= number_format($item->compd_precio_neto, 2) ?></td>
                    <td class="text-end">$<?= number_format($item->compd_total_iva_valor, 2) ?></td>
                    <td class="text-end fw-bold">$<?= number_format($item->compd_total, 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php
    $filasIzquierda = [
        ['Subtotal bruto', (float) $compra->comp_subtotal_bruto],
        ['Descuento items', (float) $compra->comp_descuento_items],
        ['Descuento global', (float) $compra->comp_descuento_global],
        ['Subtotal neto', (float) $compra->comp_subtotal_neto],
        ['ICE', (float) $compra->comp_totalice],
        ['IRBPNR', (float) $compra->comp_totalirbpnr],
    ];

    $filasDerecha = [
        ['Base tarifa 0%', (float) $compra->comp_tarifacero_neto],
        ['Base exento IVA', (float) $compra->comp_total_excento_impuestos],
        ['Base no objeto IVA', (float) $compra->comp_total_no_objeto_impuestos],
    ];

    foreach ($compra->basesImpuestos as $base) {
        $porcentaje = number_format((float) $base->imp_porcentaje, 0);
        $filasDerecha[] = ["Base imponible IVA {$porcentaje}%", (float) $base->subtotal_neto];
    }

    foreach ($compra->basesImpuestos as $base) {
        $porcentaje = number_format((float) $base->imp_porcentaje, 0);
        $filasDerecha[] = ["Monto IVA {$porcentaje}%", (float) $base->iva_valor];
    }

    $filasDerecha[] = ['Recargo', (float) $compra->comp_recargo];
    $filasDerecha[] = ['Servicios Adc', (float) $compra->comp_servicios_adicionales];

    $totalFilas = max(count($filasIzquierda), count($filasDerecha));
    ?>

    <table class="table table-bordered ms-auto mb-4" style="max-width:680px;">
        <tbody>
            <?php for ($i = 0; $i < $totalFilas; $i++): ?>
                <?php
                $izquierda = $filasIzquierda[$i] ?? ['', null];
                $derecha = $filasDerecha[$i] ?? ['', null];
                ?>
                <tr>
                    <th class="text-end text-muted" style="width:34%"><?= esc($izquierda[0]) ?></th>
                    <td class="text-end" style="width:16%">
                        <?= $izquierda[1] !== null ? '$' . number_format($izquierda[1], 2) : '' ?>
                    </td>
                    <th class="text-end text-muted" style="width:34%"><?= esc($derecha[0]) ?></th>
                    <td class="text-end" style="width:16%">
                        <?= $derecha[1] !== null ? '$' . number_format($derecha[1], 2) : '' ?>
                    </td>
                </tr>
            <?php endfor; ?>
            <tr class="table-success">
                <td colspan="4" class="text-end fw-bold fs-5 py-3">
                    TOTAL&nbsp;&nbsp;&nbsp; $<?= number_format($compra->comp_total, 2) ?>
                </td>
            </tr>
        </tbody>
    </table>
    <hr>
    <?php if ($compra->comp_estado === 'ARCHIVADO'): ?>
        <?php if (!empty($compra->formasPagoAts)): ?>
            <h5 style=" border-bottom: 2px solid #34495e;color: #34495e;font-size: 1rem;font-weight: 700;margin-bottom: 1rem;margin-top: 1.5rem;padding-bottom: .4rem; text-transform: uppercase">Formas de pago ATS</h5>

            <div class="border rounded p-3">
                <?php foreach ($compra->formasPagoAts as $forma): ?>
                    <span class="badge bg-info text-dark me-2">
                        <?= esc($forma->codigo) ?> - <?= esc($forma->nombre) ?>
                    </span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($compra->retencion)): ?>
            <?php
            $numeroRetencion = implode('-', array_filter([
                $compra->retencion->ret_numero_establecimiento,
                $compra->retencion->ret_numero_emision,
                $compra->retencion->ret_numero_comprobante,
            ]));
            ?>
            <hr>
            <h5 style=" border-bottom: 2px solid #34495e;color: #34495e;font-size: 1rem;font-weight: 700;margin-bottom: 1rem;margin-top: 1.5rem;padding-bottom: .4rem; text-transform: uppercase">Retención</h5>

            <table class="table table-bordered">
                <tr>
                    <th>Número</th>
                    <td><?= esc($numeroRetencion) ?></td>
                    <th>Fecha</th>
                    <td>
                        <?= date('d/m/Y', strtotime($compra->retencion->ret_fecha_emision)) ?>
                    </td>
                </tr>
                <tr>
                    <th>Autorización SRI</th>
                    <td><?= esc($compra->retencion->ret_autorizacion_sri) ?></td>
                    <th>Total retenido</th>
                    <td class="fw-bold text-end">
                        $<?= number_format($compra->retencion->ret_total_retenido, 2) ?>
                    </td>
                </tr>
            </table>

            <table class="table table-bordered">
                <thead class="table-secondary">
                    <tr>
                        <th>Tipo</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th class="text-end">Base</th>
                        <th class="text-end">Porcentaje</th>
                        <th class="text-end">Retenido</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($compra->retencion->detalle as $retencion): ?>
                        <tr>
                            <td><?= esc($retencion->retd_tipo_retencion) ?></td>
                            <td><?= esc($retencion->retd_codigo_sri) ?></td>
                            <td><?= esc($retencion->retd_descripcion) ?></td>
                            <td class="text-end">
                                $<?= number_format($retencion->retd_base_imponible, 2) ?>
                            </td>
                            <td class="text-end">
                                <?= number_format($retencion->retd_porcentaje, 2) ?>%
                            </td>
                            <td class="text-end">
                                $<?= number_format($retencion->retd_valor_retenido, 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($compra->comp_estado === 'ARCHIVADO' && !empty($compra->cuentaPorPagar)): ?>
        <?php $cxp = $compra->cuentaPorPagar; ?>
        <hr>
        <h5 style=" border-bottom: 2px solid #34495e;color: #34495e;font-size: 1rem;font-weight: 700;margin-bottom: 1rem;margin-top: 1.5rem;padding-bottom: .4rem; text-transform: uppercase">Cuenta por pagar</h5>

        <table class="table table-bordered">
            <tr>
                <th>Tipo de pago</th>
                <td><?= esc($cxp->cxp_tipo_pago) ?></td>
                <th>Estado</th>
                <td><?= esc($cxp->cxp_estado) ?></td>
            </tr>
            <tr>
                <th>Total por pagar</th>
                <td class="text-end">$<?= number_format($cxp->cxp_total, 2) ?></td>
                <th>Valor pagado</th>
                <td class="text-end">$<?= number_format($cxp->cxp_valor_pagado, 2) ?></td>
            </tr>
            <tr>
                <th>Saldo</th>
                <td class="text-end fw-bold">$<?= number_format($cxp->cxp_saldo, 2) ?></td>
                <th>Número de cuotas</th>
                <td><?= (int) $cxp->cxp_num_cuotas ?></td>
            </tr>
        </table>

        <?php if (!empty($cxp->cuotas)): ?>
            <h5 style=" border-bottom: 2px solid #34495e;color: #34495e;font-size: 1rem;font-weight: 700;margin-bottom: 1rem;margin-top: 1.5rem;padding-bottom: .4rem; text-transform: uppercase">Cuotas</h5>

            <table class="table table-bordered">
                <thead class="table-secondary">
                    <tr>
                        <th>#</th>
                        <th>Vencimiento</th>
                        <th class="text-end">Valor</th>
                        <th class="text-end">Pagado</th>
                        <th class="text-end">Saldo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cxp->cuotas as $cuota): ?>
                        <tr>
                            <td><?= (int) $cuota->cxpc_numero ?></td>
                            <td><?= date('d/m/Y', strtotime($cuota->cxpc_fecha_vencimiento)) ?></td>
                            <td class="text-end">$<?= number_format($cuota->cxpc_valor, 2) ?></td>
                            <td class="text-end">$<?= number_format($cuota->cxpc_pagado, 2) ?></td>
                            <td class="text-end">$<?= number_format($cuota->cxpc_saldo, 2) ?></td>
                            <td><?= esc($cuota->cxpc_estado) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if (!empty($cxp->pagos)): ?>
            <hr>
            <h5 style=" border-bottom: 2px solid #34495e;color: #34495e;font-size: 1rem;font-weight: 700;margin-bottom: 1rem;margin-top: 1.5rem;padding-bottom: .4rem; text-transform: uppercase">Pagos aplicados</h5>

            <table class="table table-bordered">
                <thead class="table-secondary">
                    <tr>
                        <th>Fecha</th>
                        <th>Comprobante</th>
                        <th>Forma de pago</th>
                        <th>Banco</th>
                        <th>Cuota</th>
                        <th class="text-end">Valor aplicado</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cxp->pagos as $pago): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($pago->pg_fecha)) ?></td>
                            <td><?= esc($pago->pg_numero_secuencial) ?></td>
                            <td><?= esc($pago->forma_pago ?: '-') ?></td>
                            <td><?= esc($pago->banco ?: '-') ?></td>
                            <td><?= esc($pago->numero_cuota ?: '-') ?></td>
                            <td class="text-end">$<?= number_format($pago->valor_aplicado, 2) ?></td>
                            <td><?= esc($pago->pg_estado) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($compra->comp_estado === 'ARCHIVADO' && !empty($compra->asientoContable)): ?>
        <?php $asiento = $compra->asientoContable; ?>
        <hr>
        <h5 style=" border-bottom: 2px solid #34495e;color: #34495e;font-size: 1rem;font-weight: 700;margin-bottom: 1rem;margin-top: 1.5rem;padding-bottom: .4rem; text-transform: uppercase">Asiento contable</h5>

        <table class="table table-bordered">
            <tr>
                <th>Número de asiento</th>
                <td><?= (int) $asiento->ac_num_asiento ?></td>
                <th>Fecha</th>
                <td><?= date('d/m/Y', strtotime($asiento->ac_fecha)) ?></td>
            </tr>
            <tr>
                <th>Detalle</th>
                <td><?= esc($asiento->ac_detalle) ?></td>
                <th>Usuario</th>
                <td><?= esc($asiento->usuario_registra) ?></td>
            </tr>
        </table>

        <table class="table table-bordered">
            <thead class="table-secondary">
                <tr>
                    <th>Código</th>
                    <th>Cuenta contable</th>
                    <th>Detalle</th>
                    <th>Centro de costo</th>
                    <th class="text-end">Debe</th>
                    <th class="text-end">Haber</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($asiento->detalle as $movimiento): ?>
                    <tr>
                        <td><?= esc($movimiento->codigo_cuenta_contable) ?></td>
                        <td><?= esc($movimiento->cuenta_contable) ?></td>
                        <td><?= esc($movimiento->acd_detalle) ?></td>
                        <td><?= esc($movimiento->centro_costo ?: '-') ?></td>
                        <td class="text-end">
                            <?= $movimiento->acd_tipo === 'DEBE' ? '$' . number_format($movimiento->acd_valor, 2) : '-' ?>
                        </td>
                        <td class="text-end">
                            <?= $movimiento->acd_tipo === 'HABER' ? '$' . number_format($movimiento->acd_valor, 2) : '-' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="table-success fw-bold">
                <tr>
                    <td colspan="4" class="text-end">TOTALES</td>
                    <td class="text-end">$<?= number_format($asiento->totalDebe, 2) ?></td>
                    <td class="text-end">$<?= number_format($asiento->totalHaber, 2) ?></td>
                </tr>
            </tfoot>
        </table>

        <?php if (abs($asiento->totalDebe - $asiento->totalHaber) > 0.01): ?>
            <div class="alert alert-danger">
                El asiento contable no está cuadrado.
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
