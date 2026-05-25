<!DOCTYPE html>
<!--
/**
 * Description of viewInfoProveedor
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 22 may 2026
 * @time 3:21:48 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div v-if="formCompra.compProveedor"class=" rounded p-3 bg-light" >

    <div class="d-flex align-items-center mb-3">
        <div class="me-2">
            <i class="fas fa-building text-primary"></i>
        </div>

        <div>
            <div class="fw-bold" style="font-size: 14px;">
                {{ formCompra.compProveedor.prov_razon_social }}
            </div>

            <small class="text-muted">
                Información del proveedor
            </small>
        </div>
    </div>

    <div class="row g-2">

        <div class="col-md-6">
            <small class="text-muted d-block">
                <i class="fas fa-id-card me-1"></i>
                Identificación
            </small>

            <strong>
                {{ formCompra.compProveedor.prov_ruc || '-' }}
            </strong>
        </div>

        <div class="col-md-6">
            <small class="text-muted d-block">
                <i class="fas fa-phone-alt me-1"></i>
                Teléfono
            </small>

            <strong>
                {{ formCompra.compProveedor.prov_telefono || '-' }}
            </strong>
        </div>

        <div class="col-md-6">
            <small class="text-muted d-block">
                <i class="fas fa-map-marker-alt me-1"></i>
                Dirección
            </small>

            <strong>
                {{ formCompra.compProveedor.prov_direccion || '-' }}
            </strong>
        </div>

        <div class="col-md-6">
            <small class="text-muted d-block">
                <i class="fas fa-envelope me-1"></i>
                Email
            </small>

            <strong>
                {{ formCompra.compProveedor.prov_email || '-' }}
            </strong>
        </div>

    </div>
</div>