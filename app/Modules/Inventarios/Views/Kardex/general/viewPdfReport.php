<!DOCTYPE html>
<!--
/**
 * Description of viewPdfReport
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 29 abr 2026
 * @time 5:23:52 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<?php
$totalCantidad = 0;
$totalPromedio = 0;
$totalUltimo = 0;
?>

<table class="report">
    <thead>
        <tr>
            <th>F. MOV</th>
            <th>F. EMISIÓN</th>
            <th>CÓDIGO</th>
            <th>PRODUCTO</th>
            <th>GRUPO</th>
            <th>SUBGRUPO</th>
            <th>BODEGA</th>
            <th>LOTE</th>
            <th>F. CADUC.</th>
            <th>CANT.</th>
            <th>C.P.</th>
            <th>TOT. C.P.</th>
            <th>C.U.</th>
            <th>TOT. C.U.</th>
            <th>TRANS.</th>

            <?php if ($mostrarDocumento): ?>
                <th>DOC</th>
            <?php endif; ?>

            <?php if ($mostrarMotivo): ?>
                <th>MOTIVO</th>
            <?php endif; ?>

            <?php if ($mostrarProvClie): ?>
                <th>PROV/CLI</th>
            <?php endif; ?>

        </tr>
    </thead>

    <tbody>
        <?php
        foreach ($data as $item):

            $totalCantidad += $item->cantidad;
            $totalPromedio += $item->total_promedio;
            $totalUltimo += $item->total_ultimo;
            ?>

            <tr>
                <td><?= esc($item->fecha_movimiento) ?></td>
                <td><?= esc($item->fecha_emision) ?></td>
                <td><?= esc($item->prod_codigo) ?></td>
                <td><?= esc($item->prod_nombre) ?></td>
                <td><?= esc($item->gr_nombre) ?></td>
                <td><?= esc($item->sgr_nombre) ?></td>
                <td><?= esc($item->bod_nombre) ?></td>

                <td><?= $item->lot_lote ? esc($item->lot_lote) : 'N/A' ?></td>
                <td><?= $item->lot_fecha_caducidad ? esc($item->lot_fecha_caducidad) : 'N/A' ?></td>

                <td class="text-right"><?= number_format($item->cantidad, 2) ?></td>
                <td class="text-right">$<?= number_format($item->kar_costo_promedio, 2) ?></td>
                <td class="text-right">$<?= number_format($item->total_promedio, 2) ?></td>
                <td class="text-right">$<?= number_format($item->kar_costo_ultimo, 2) ?></td>
                <td class="text-right">$<?= number_format($item->total_ultimo, 2) ?></td>

                <td><?= esc($item->transaccion ?? '-') ?></td>

                <?php if ($mostrarDocumento): ?>
                    <td><?= esc($item->documento ?? '-') ?></td>
                <?php endif; ?>

                <?php if ($mostrarMotivo): ?>
                    <td><?= esc($item->motivo ?? '-') ?></td>
                <?php endif; ?>

                <?php if ($mostrarProvClie): ?>
                    <td><?= esc($item->proveedor_cliente ?? '-') ?></td>
    <?php endif; ?>

            </tr>

<?php endforeach; ?>
    </tbody>

    <tfoot>
        <tr style="font-weight:bold; background:#f2f2f2;">
            <td colspan="9" class="text-right"><strong>TOTALES</strong></td>

            <td class="text-right">
                <strong><?= number_format($totalCantidad, 2) ?></strong>
            </td>

            <td></td>

            <td class="text-right">
                <strong>$<?= number_format($totalPromedio, 2) ?></strong>
            </td>

            <td></td>

            <td class="text-right">
                <strong>$<?= number_format($totalUltimo, 2) ?></strong>
            </td>

            <td colspan="5"></td>
        </tr>
    </tfoot>
</table>