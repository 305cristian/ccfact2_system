<!DOCTYPE html>
<!--
/**
 * Description of viewInfoClientes
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 9 ago 2026
 * @time 6:21:32 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div v-if="clientePanelVisible" class="col-md-12">
    <div class="border rounded bg-light p-3">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h6 class="fw-bold text-system mb-1">
                    <i class="fas fa-address-card me-1"></i>
                    {{ clienteDetalle.clie_razon_social }}
                </h6>
                <div class="small text-muted">
                    CI/RUC: <strong>{{ clienteDetalle.clie_dni }}</strong>
                    <span class="mx-2">|</span>
                    Tipo: <strong>{{ clienteDetalle.tps_descripcion || '-' }}</strong>
                </div>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-primary" @click="clienteEditando = !clienteEditando">
                    <i class="fas" :class="clienteEditando ? 'fa-eye' : 'fa-edit'"></i>
                    {{ clienteEditando ? 'Ver datos' : 'Editar datos' }}
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" @click="clientePanelVisible = false">
                    <i class="fas fa-chevron-up"></i>
                </button>
            </div>
        </div>

        <div v-if="!clienteEditando" class="row g-2">
            <div class="col-md-3">
                <small class="text-muted d-block">Teléfono</small>
                <strong>{{ clienteDetalle.clie_telefono || '-' }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Celular</small>
                <strong>{{ clienteDetalle.clie_celular || '-' }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Email</small>
                <strong>{{ clienteDetalle.clie_email || '-' }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Crédito</small>
                <strong>{{ clienteDetalle.clie_dias_credito || 0 }} días / {{ formatToUSD(clienteDetalle.clie_cupo_credito) }}</strong>
            </div>
            <div class="col-md-12">
                <small class="text-muted d-block">Dirección</small>
                <strong>{{ clienteDetalle.clie_direccion || '-' }}</strong>
            </div>
        </div>

        <div v-else class="row g-2">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Teléfono</label>
                <input v-model="formCliente.clieTelefono" class="form-control form-control-sm" v-numbers-only="{ decimal: false }">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Celular</label>
                <input v-model="formCliente.clieCelular" class="form-control form-control-sm" v-numbers-only="{ decimal: false }">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Email</label>
                <input v-model="formCliente.clieEmail" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Días crédito</label>
                <input v-model="formCliente.clieDiasCredito" class="form-control form-control-sm" v-numbers-only="{ decimal: false }">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Cupo crédito</label>
                <input v-model="formCliente.clieCupoCredito" class="form-control form-control-sm" v-numbers-only="{ decimal: true }">
            </div>
            <div class="col-md-9">
                <label class="form-label small fw-bold">Dirección</label>
                <input v-model="formCliente.clieDireccion" class="form-control form-control-sm">
            </div>
            <div class="col-md-12 text-end">
                <button type="button" class="btn btn-sm btn-success" @click="actualizarClienteVenta" :disabled="loadingCliente">
                    <span v-if="loadingCliente"><i class="loading-spin"></i> Guardando...</span>
                    <span v-else><i class="fas fa-save me-1"></i> Actualizar cliente</span>
                </button>
            </div>
        </div>
    </div>
</div>