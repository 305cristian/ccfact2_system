<!DOCTYPE html>
<!--
/**
 * Description of viewEdicionCentroCostos
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 12 jul 2026
 * @time 12:54:39 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div class="modal fade" ref="modalCentrosCostos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-cris-system text-white">
                <h5 class="modal-title">
                    <i class="fas fa-project-diagram me-2"></i> Editar centros de costos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2">
                    Esta opcion actualiza el centro de costo global y el centro de costo de cada item. No modifica valores, cantidades ni impuestos.
                </div>

                <div class="row mb-3">
                    <div class="col-md-12 form-group-custom">
                        <div class="d-flex align-items-center border rounded overflow-visible">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-project-diagram me-2"></i> Centro global
                            </span>
                            <vue-select
                                class="flex-grow-1"
                                :options="listaCentroCostos"
                                label="cc_nombre"
                                v-model="formCentrosCostos.centroCostoId"
                                :reduce="centro => centro.id"
                                placeholder="Seleccione un centro de costo">
                            </vue-select>
                        </div>
                        <small class="text-danger">{{ erroresCentrosCostos.centroCostoId }}</small>
                    </div>
                </div>

                <div v-if="loadingCentrosCostos" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-system"></i>
                    <div class="text-muted mt-2">Cargando detalles...</div>
                </div>

                <div v-else class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="bg-system text-white">
                            <tr>
                                <th style="width: 150px;">Codigo</th>
                                <th>Producto</th>
                                <th style="width: 120px;" class="text-end">Cantidad</th>
                                <th style="width: 360px;">Centro de costo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="detalle in formCentrosCostos.detalles" :key="detalle.id">
                                <td>{{ detalle.codigo }}</td>
                                <td>{{ detalle.producto }}</td>
                                <td class="text-end">{{ detalle.cantidad }}</td>
                                <td>
                                    <vue-select
                                        class="border rounded"
                                        :options="listaCentroCostos"
                                        label="cc_nombre"
                                        v-model="detalle.centroCostoId"
                                        :reduce="centro => centro.id"
                                        placeholder="Seleccione un centro">
                                    </vue-select>
                                    <small class="text-danger">{{ (erroresCentrosCostos.detalles || {})[detalle.id] }}</small>
                                </td>
                            </tr>
                            <tr v-if="!formCentrosCostos.detalles.length">
                                <td colspan="4" class="text-center text-muted py-4">
                                    No existen detalles para actualizar.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" :disabled="loadingGuardarCentrosCostos" @click="guardarCentrosCostos">
                    <i v-if="loadingGuardarCentrosCostos" class="fas fa-spinner fa-spin me-1"></i>
                    <i v-else class="fas fa-save me-1"></i>
                    Guardar cambios
                </button>
            </div>
        </div>
    </div>
</div>
