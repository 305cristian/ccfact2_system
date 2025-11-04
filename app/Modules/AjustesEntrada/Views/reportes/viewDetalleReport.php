<!DOCTYPE html>
<!--
/**
 * Description of viewDetalleAjuste
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 2 nov 2025
 * @time 3:40:30 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div class="border-1 p-3" id="contentExport">

    <!-- Encabezado Empresarial -->

    <!-- Encabezado del reporte -->
    <table class="table table-borderless align-middle mb-4" style="width: 100%;">
        <tr>
            <!-- Logo y empresa -->
            <td style="width:30%; text-align:center; vertical-align:middle; background-color: #e9ecef; padding: 10px; border-radius: 10px">
                <div class=" p-2 rounded">
                    <?php if (!empty($empresa->epr_logo)): ?>
                        <img src="<?= base_url('uploads/img/enterprice/' . $empresa->epr_logo) ?>" 
                             style="width:120px; height:auto;" alt="Logo">
                         <?php else: ?>
                        <div style="font-size:2rem; font-weight:bold;">LOGO</div>
                    <?php endif; ?>
                    <h6 class="fw-bold mb-0"><?= esc($empresa->epr_nombre_comercial) ?> S.A.</h6>
                    <small>RUC: <?= esc($empresa->epr_ruc) ?></small>
                </div>
            </td>

            <!-- Información de contacto -->
            <td style="width: 30%; vertical-align:middle; font-size:10pt;padding: 5px" class="align-middle text-left">
                <div class="p-1 rounded col-md-12">
                    <p class="text-muted mb-1 small">
                        <i class="fas fa-map me-2"></i>
                        <?= $empresa->epr_direccion ?>
                    </p>
                    <p class="text-muted mb-1 small">
                        <i class="fas fa-phone me-2"></i>
                        <?= $empresa->epr_telefono . ' / ' . $empresa->epr_celular ?>
                    </p>
                    <p class="text-muted mb-0 small">
                        <i class="fas fa-envelope me-2"></i>
                        <?= $empresa->epr_email ?>
                    </p>
                    <p class="text-muted mb-0 small">
                        <i class="fas fa-globe me-2"></i>
                        <?= $empresa->epr_pagina_web ?>
                    </p>
                </div>
            </td>

            <!-- Información del documento -->
            <td style="width:30%; text-align:center; vertical-align: middle; border: 1px solid #e9ecef">
                <div class="text-center">
                    <h5 class="fw-bold mb-2">AJUSTE DE ENTRADA</h5>
                    <h6 class="text-danger mb-2">#<?= str_pad($ajuste->ajen_secuencial, 5, '0', STR_PAD_LEFT) ?></h6>
                    <p class="fw-bold small mb-1">Fecha de Emisión:<br><span class="text-primary"><?= date('d/m/Y', strtotime($ajuste->ajen_fecha)) ?></span></p>
                    <?php
                    $estado = [
                        '1' => ['bg-warning', '📄 BORRADOR'],
                        '2' => ['bg-success', '✅ ARCHIVADO'],
                        '-1' => ['bg-danger', 'ANULADO']
                    ];
                    [$estadoClass, $estadoTexto] = $estado[$ajuste->ajen_estado] ?? ['bg-secondary', 'DESCONOCIDO'];
                    ?>
                    <span class="badge  <?= $estadoClass ?>"> <?= $estadoTexto ?></span>
                </div>
            </td>
        </tr>
    </table>
    <br>
    <!-- Información del Ajuste y Proveedor -->
    <table border="1" style="width:100%; margin-bottom:15px;" class="table table-bordered">
        <tr>
            <!-- Columna izquierda: Ajuste -->
            <td style="width:50%; vertical-align:top; padding: 10px">
                <p class="fw-bold border-bottom mb-2"><strong><i class="fas fa-building me-2"></i> Información del Ajuste</strong></p>
                <br>
                <div class="bg-light p-2 rounded p-2">
                    <p class="mb-1"><strong>&nbsp;Bodega:</strong> <?= esc($ajuste->bod_nombre) ?></p>
                    <p class="mb-1"><strong>&nbsp;Motivo:</strong> <?= esc($ajuste->mot_nombre) ?></p>
                    <p class="mb-0"><strong>&nbsp;Usuario:</strong> <?= esc($ajuste->user_create) ?></p>
                </div>
            </td>

            <!-- Columna derecha: Proveedor -->
            <td style="width:50%; vertical-align:top; padding: 10px">
                <p class="fw-bold border-bottom mb-2"><strong><i class="fas fa-building me-2"></i> Información del Proveedor</strong></p>
                <br>
                <div class="bg-light p-2 rounded p-2">
                    <p class="mb-1"><strong>&nbsp;RUC:</strong> <?= esc($ajuste->prov_ruc) ?></p>
                    <p class="mb-1"><strong>&nbsp;Proveedor:</strong> <?= esc($ajuste->prov_razon_social) ?></p>
                    <p class="mb-0"><strong>&nbsp;Observaciones:</strong> <?= esc($ajuste->ajen_observaciones ?: 'Sin observaciones') ?></p>
                </div>
            </td>
        </tr>
    </table>

    <!-- Detalle de Productos -->
    <br>
    <div class="table-responsive">
        <h6 class="fw-bold">
            <i class="bi bi-box-seam me-2"></i>Detalle de Productos
        </h6>
        <table border="1" width="100%" cellspacing="0" class="table table-bordered">
            <thead class="table-secondary">
                <tr>
                    <th class="text-center" style="width: 5%;">#</th>
                    <th style="width: 12%;">Código</th>
                    <th style="width: 30%;">Producto</th>
                    <th class="text-center d-none d-md-table-cell" style="width: 12%;">Lote</th>
                    <th class="text-center d-none d-md-table-cell" style="width: 12%;">Caducidad</th>
                    <th class="text-center" style="width: 8%;">Cant.</th>
                    <th class="text-end" style="width: 10%;">Costo Unit.</th>
                    <th class="text-end" style="width: 11%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalGeneral = 0;
                $totalUnidades = 0;
                foreach ($ajuste->detalle as $idx => $item):
                    $totalGeneral += $item->ajend_itemcostoxcantidad;
                    $totalUnidades += $item->ajend_itemcantidad;
                    ?>
                    <tr>
                        <td class="text-center text-muted small"><?= $idx + 1 ?></td>
                        <td class="fw-semibold small"> <?= $item->prod_codigo ?> </td>
                        <td class="fw-semibold small"><?= $item->prod_nombre ?></td>
                        <td class="text-center small d-none d-md-table-cell">
                            <?php if (!empty($item->lot_lote)): ?>
                                <span class="text-warning">
                                    <?= $item->lot_lote ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center small  d-none d-md-table-cell">
                            <?php if (!empty($item->lot_fecha_caducidad)): ?>
                                <?= date('d/m/Y', strtotime($item->lot_fecha_caducidad)) ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="text-info">
                                <?= $item->ajend_itemcantidad ?>
                            </span>
                        </td>
                        <td class="text-end small">$<?= number_format($item->ajend_itemcosto, 2) ?></td>
                        <td class="text-end fw-semibold small">
                            $<?= number_format($item->ajend_itemcostoxcantidad, 2) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td class="d-none d-md-table-cell"></td>
                    <td class="d-none d-md-table-cell"></td>
                    <td colspan="5" class="text-end fw-bold">TOTAL:</td>
                    <td class="text-end fw-bold fs-5">
                        $<?= number_format($totalGeneral, 2) ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Resumen -->
    <table class="table table-borderless mt-4" style="width: 100%;">
        <tr></tr>
        <tr>
            <!-- Columna: Totales de items -->
            <td style="width: 60%; vertical-align: middle; background-color: #d1ecf1; border-radius: 6px; padding: 8px 12px; border-radius: 10px">
                <div style="color: #0c5460;">
                    <p><strong>Total de Ítems:</strong> <?= count($ajuste->detalle) ?> productos</p>
                    <p><strong>Total unidades:</strong> <?= number_format($totalUnidades, 2) ?></p>
                </div>
            </td>
            <!-- Separador -->
            <td style="width: 10%; vertical-align: top;"></td>

            <!-- Columna: Total general -->
            <td style="width: 30%; vertical-align: middle; text-align: center; border-radius: 10px; background-color: #DFDFDF">
                <small style="display: block; color: #fff;">TOTAL GENERAL</small>
                <h3 style="margin: 4px 0 0; color: #fff;"> $<?= number_format($totalGeneral, 2) ?> </h3> 
            </td>
        </tr>
    </table>

    <!-- Footer del reporte -->
    <div class="bg-white rounded text-center  p-3 mt-4 text-center">
        <small class="text-muted">
            <i class="fas fa-calendar-check me-1"></i>
            Generado el <?= date('d/m/Y, h:i:s a') ?>
        </small>
    </div>

</div>

