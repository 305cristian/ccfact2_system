<!DOCTYPE html>
<!--
/**
 * Description of viewPdfReport
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 28 ene 2026
 * @time 10:55:26 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<?php
$totalStock = 0;
$totalCostoPromedio = 0;
$totalCostoUltimo = 0;
?>
<table class="report">
    <thead>
        <tr>
            <th>CÓDIGO</th>
            <th>PRODUCTO</th>
            <th>PRES.</th>
            <th>STOCK</th>
            <th>IVA</th>
            <th>BODEGA</th>
            <th>CST. PROM.</th>
            <th>TOT. COSTO PROM.</th>
            <th>CST. ULT.</th>
            <th>TOT. COSTO ULT.</th>
            <th>GRUPO</th>
            <th>SUBGRUPO</th>
            <th>MOVIMIENTO</th>
        </tr>
    </thead>
    <tbody>

        <?php
        foreach ($data as $item):

            // ACUMULADORES
            $totalStock += $item->kardexStock;
            $totalCostoPromedio += $item->total_cst_promedio;
            $totalCostoUltimo += $item->total_cst_ultimo;
            ?>
            <tr>
                <td><?= esc($item->prod_codigo) ?></td>
                <td><?= esc($item->prod_nombre) ?></td>
                <td><?= esc($item->um_nombre_corto) ?></td>
                <td class="text-right"><?= esc($item->kardexStock) ?></td>
                <td class="text-center"><?= $item->prod_ivaporcentage === '0.00' ? 'SIN IVA' : 'IVA' ?></td>
                <td><?= $bodgaSelect ? esc($item->bod_nombre) : "ALL SELECT" ?></td>
                <td class="text-right"><?= '$' . esc($item->costoPromedio) ?></td>
                <td class="text-right"><?= '$' . esc($item->total_cst_promedio) ?></td>
                <td class="text-right"><?= '$' . esc($item->costoUltimo) ?></td>
                <td class="text-right"><?= '$' . esc($item->total_cst_ultimo) ?></td>
                <td><?= esc($item->gr_nombre) ?></td>
                <td><?= esc($item->sgr_nombre) ?></td>
                <td><?= esc($item->tr_nombre) ?></td>
            </tr>



        <?php endforeach; ?>

    </tbody>

    <tfoot>
        <tr style="font-weight:bold; background:#f2f2f2;">
            <td colspan="3" class="text-right"><strong>TOTALES</strong></td>

            <td class="text-right"><strong><?= number_format($totalStock, 2) ?></strong></td>
            <td class="text-right">—</td>
            <td class="text-right">—</td>
            <td class="text-right">—</td>
            <td class="text-right"><strong><?= number_format($totalCostoPromedio, 2) ?></strong></td>
            <td class="text-right">—</td>
            <td class="text-right"><strong><?= number_format($totalCostoUltimo, 2) ?></strong></td>

            <td colspan="3"></td>
        </tr>
    </tfoot>
</table>
