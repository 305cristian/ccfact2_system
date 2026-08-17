<!DOCTYPE html>
<!--
/**
 * Description of viewDashboard
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 4 ene 2026
 * @time 12:24:15 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div class="container-fluid">
    <div class="card card-system card-outline shadow-sm">
        <div class="card-body py-4">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-system text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 58px; height: 58px;">
                    <i class="fas fa-warehouse fa-2x"></i>
                </div>
                <div>
                    <div class="text-system fw-bold text-uppercase small">Control de inventario</div>
                    <h3 class="fw-bold mb-1">Existencias</h3>
                    <p class="text-muted mb-0">
                        Consulte el stock general, por lotes, consolidado, historico y caducidades.
                    </p>
                </div>
            </div>

<!--            <div class="row g-3">
                <div class="col-xl-3 col-md-6">
                    <a href="<?= site_url('inventarios/viewInventarioGeneral') ?>" class="btn btn-outline-primary w-100 text-start py-3">
                        <i class="fas fa-box me-2"></i> Inventario general
                    </a>
                </div>
                <div class="col-xl-3 col-md-6">
                    <a href="<?= site_url('inventarios/viewInventarioLotes') ?>" class="btn btn-outline-primary w-100 text-start py-3">
                        <i class="fas fa-layer-group me-2"></i> Inventario por lotes
                    </a>
                </div>
                <div class="col-xl-3 col-md-6">
                    <a href="<?= site_url('inventarios/viewInventarioConsolidado') ?>" class="btn btn-outline-primary w-100 text-start py-3">
                        <i class="fas fa-random me-2"></i> Consolidado
                    </a>
                </div>
                <div class="col-xl-3 col-md-6">
                    <a href="<?= site_url('control/caducidad') ?>" class="btn btn-outline-primary w-100 text-start py-3">
                        <i class="fas fa-clipboard-list me-2"></i> Caducidad
                    </a>
                </div>
            </div>-->
        </div>
    </div>

    <div class="page-header mb-4 d-none">
        <h4 class="fw-bold text-system">
            <i class="fas fa-warehouse me-2"></i> Control de Inventario
        </h4>
        <p class="text-muted mb-0">
            Consulta de existencias general y por lotes, auditoría de stock por bodegas
        </p>
    </div>

    <h3 class="text-muted d-none">DASHBOARD</h3>
</div>
