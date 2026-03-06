<!DOCTYPE html>
<!--
/**
 * Description of viewPdfReport
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 30 ene 2026
 * @time 12:24:30 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<?php
$totalStock = 0;
$totalReserva = 0;
$totalDisponible = 0;
$totalCostoUltimo = 0;
$totalCostoTotal = 0;
?>
<table class="report">
    <thead>
        <tr>
            <th>CÓDIGO</th>
            <th>PRODUCTO</th>
            <th>PRES.</th>
            <th>LOTE</th>
            <th>F. CADUCIDAD</th>
            <th>STOCK</th>
            <th>RESERVA</th>
            <th>DISP.</th>
            <th>C.P.</th>
            <th>C.U.</th>
            <th>TOT. C.U.</th>
            <th>IVA</th>
            <th>BODEGA</th>
            <th>GRUPO</th>
            <th>SUBGRUPO</th>
        </tr>
    </thead>
    <tbody>

        <?php
        foreach ($data as $item):

            $disponible = esc($item->stbl_stock - $item->reservaProducto);

            // ACUMULADORES
            $totalStock += $item->stbl_stock;
            $totalReserva += $item->reservaProducto;
            $totalDisponible += $disponible;
            $totalCostoUltimo += $item->prod_costoultimo;
            $totalCostoTotal += ($item->prod_costoultimo * $disponible);
            ?>
            <tr>
                <td><?= esc($item->prod_codigo) ?></td>
                <td><?= esc($item->prod_nombre) ?></td>
                <td><?= esc($item->um_nombre_corto) ?></td>
                <td><?= esc($item->lot_lote) ?></td>
                <td><?= esc($item->lot_fecha_caducidad) ?></td>
                <td class="text-right"><?= esc($item->stbl_stock) ?></td>
                <td class="text-right"><?= number_format(esc($item->reservaProducto), 2) ?></td>
                <td class="text-right"><?= $disponible ?></td>
                <td class="text-right"><?= '$' . esc($item->prod_costopromedio) ?></td>
                <td class="text-right"><?= '$' . esc($item->prod_costoultimo) ?></td>
                <td class="text-right"><?= '$' . esc($item->prod_costoultimo * $disponible) ?></td>
                <td class="text-center"><?= $item->prod_ivaporcentage === '0.00' ? 'SIN IVA' : 'IVA' ?></td>
                <td><?= $bodgaSelect?esc($item->bod_nombre):"ALL SELECT" ?></td>
                <td><?= esc($item->gr_nombre) ?></td>
                <td><?= esc($item->sgr_nombre) ?></td>
            </tr>
        <?php endforeach; ?>

    </tbody>

    <tfoot>
        <tr style="font-weight:bold; background:#f2f2f2;">
            <td colspan="5" class="text-right"><strong>TOTALES</strong></td>

            <td class="text-right"><strong><?= number_format($totalStock, 2) ?></strong></td>
            <td class="text-right"><strong><?= number_format($totalReserva, 2) ?></strong></td>
            <td class="text-right"><strong><?= number_format($totalDisponible, 2) ?></strong></td>

            <td class="text-right">—</td>
            <td class="text-right">—</td>

            <td class="text-right">
                <strong>$<?= number_format($totalCostoTotal, 2) ?></strong>
            </td>

            <td colspan="6"></td>
        </tr>
    </tfoot>
</table>
