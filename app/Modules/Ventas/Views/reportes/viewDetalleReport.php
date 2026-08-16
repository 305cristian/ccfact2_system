<!DOCTYPE html>
<!--
/**
 * Description of viewDetalleReport
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 16 ago 2026
 * @time 9:52:14 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<?php
$numeroComprobante = implode('-', array_filter([
    $venta->ven_numero_establecimiento,
    $venta->ven_numero_emision,
    $venta->ven_numero_comprobante,
        ]));

$estados = [
    'BORRADOR' => ['bg-warning', 'BORRADOR'],
    'ARCHIVADO' => ['bg-success', 'ARCHIVADA'],
    'ANULADA_EN_PENDIENTE' => ['bg-secondary', 'ANULADA EN BORRADOR'],
    'ANULADA_EN_ARCHIVADA' => ['bg-dark', 'ANULADA ARCHIVADA'],
];

[$estadoClase, $estadoTexto] = $estados[$venta->ven_estado] ?? ['bg-secondary', 'DESCONOCIDO'];
$tituloComprobante = trim($venta->comprobante_nombre ?? 'VENTA');
?>

<div class="border p-3" id="contentExport">
    <table class="table table-borderless align-middle mb-4">
        <tr>
            <td class="text-center bg-light" style="width:30%">
                <?php if (!empty($empresa->epr_logo)): ?>
                    <img src="<?= base_url('uploads/img/enterprice/' . $empresa->epr_logo) ?>" style="width:120px; height:auto;" alt="Logo">
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
                    #<?= str_pad($venta->ven_secuencial, 5, '0', STR_PAD_LEFT) ?>
                </h6>
                <p><?= date('d/m/Y', strtotime($venta->ven_fecha_emision)) ?></p>
                <span class="badge <?= $estadoClase ?>"><?= $estadoTexto ?></span>
            </td>
        </tr>
    </table>

    <table class="table table-bordered mb-4">
        <tr>
            <td style="width:50%">
                <strong>Comprobante:</strong> <?= esc($numeroComprobante) ?><br>
                <strong>Tipo:</strong> <?= esc($venta->comprobante_nombre) ?><br>
                <strong>Tipo de venta:</strong> <?= esc(trim(($venta->tv_codigo ?? '') . ' - ' . ($venta->tv_nombre ?? ''), ' -')) ?><br>
                <strong>Bodega:</strong> <?= esc($venta->bod_nombre ?: '-') ?><br>
                <strong>Centro de costo:</strong> <?= esc($venta->cc_nombre ?: '-') ?>
            </td>
            <td style="width:50%">
                <strong>Cliente:</strong> <?= esc($venta->clie_razon_social) ?><br>
                <strong>CI/RUC:</strong> <?= esc($venta->clie_dni) ?><br>
                <strong>Dirección:</strong> <?= esc($venta->clie_direccion ?: '-') ?><br>
                <strong>Teléfono:</strong> <?= esc($venta->clie_telefono ?: '-') ?><br>
                <strong>Usuario:</strong> <?= esc($venta->user_create ?: '-') ?>
            </td>
        </tr>
    </table>

    <?php if (in_array($venta->ven_estado, ['ANULADA_EN_PENDIENTE', 'ANULADA_EN_ARCHIVADA'], true)): ?>
        <div class="alert alert-danger mb-4">
            <h5 style="border-bottom: 2px solid #34495e;color: #34495e;font-size: 1rem;font-weight: 700;margin-bottom: 1rem;margin-top: 0;padding-bottom: .4rem; text-transform: uppercase">Datos de anulacion</h5>
            <div>
                <strong>Fecha:</strong>
                <?= $venta->ven_fecha_anulacion ? date('d/m/Y H:i', strtotime($venta->ven_fecha_anulacion)) : '-' ?>
            </div>
            <div>
                <strong>Usuario:</strong>
                <?= esc($venta->usuario_anulacion ?: '-') ?>
            </div>
            <div>
                <strong>Motivo:</strong>
                <?= esc($venta->ven_motivo_anulacion ?: '-') ?>
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
            <?php foreach ($venta->detalle as $item): ?>
                <tr>
                    <td><?= esc($item->prod_codigo) ?></td>
                    <td><?= esc($item->prod_nombre) ?></td>
                    <td><?= esc($item->lote ?: '-') ?></td>
                    <td class="text-end"><?= number_format($item->vend_cantidad, 2) ?></td>
                    <td class="text-end">$<?= number_format($item->vend_precio_bruto, 2) ?></td>
                    <td class="text-end">$<?= number_format($item->vend_descuento_valor, 2) ?></td>
                    <td class="text-end">$<?= number_format($item->vend_precio_neto, 2) ?></td>
                    <td class="text-end">$<?= number_format($item->vend_total_iva_valor, 2) ?></td>
                    <td class="text-end fw-bold">$<?= number_format($item->vend_total, 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php
    $filasIzquierda = [
        ['Subtotal bruto', (float) $venta->ven_subtotal_bruto],
        ['Descuento items', (float) $venta->ven_descuento_items],
        ['Descuento global', (float) $venta->ven_descuento_global],
        ['Subtotal neto', (float) $venta->ven_subtotal_neto],
        ['ICE', (float) $venta->ven_totalice],
        ['IRBPNR', (float) $venta->ven_totalirbpnr],
    ];

    $filasDerecha = [
        ['Base tarifa 0%', (float) $venta->ven_tarifacero_neto],
        ['Base exento IVA', (float) $venta->ven_total_excento_impuestos],
        ['Base no objeto IVA', (float) $venta->ven_total_no_objeto_impuestos],
    ];

    foreach ($venta->basesImpuestos as $base) {
        $porcentaje = number_format((float) $base->imp_porcentaje, 0);
        $filasDerecha[] = ["Base imponible IVA {$porcentaje}%", (float) $base->imp_subtotal_neto];
    }

    foreach ($venta->basesImpuestos as $base) {
        $porcentaje = number_format((float) $base->imp_porcentaje, 0);
        $filasDerecha[] = ["Monto IVA {$porcentaje}%", (float) $base->imp_valor];
    }

    $filasDerecha[] = ['Recargo', (float) $venta->ven_recargo];
    $filasDerecha[] = ['Servicios Adc', (float) $venta->ven_servicios_adicionales];

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
                    TOTAL&nbsp;&nbsp;&nbsp; $<?= number_format($venta->ven_total, 2) ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
