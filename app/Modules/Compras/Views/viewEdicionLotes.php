<!DOCTYPE html>
<!--
/**
 * Description of viewEdicionLotes
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 12 jul 2026
 * @time 1:21:01 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div class="modal fade" ref="modalLotes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-cris-system text-white">
                <h5 class="modal-title">
                    <i class="fas fa-boxes me-2"></i> Editar lotes
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2">
                    Esta opcion corrige los datos del lote ya vinculado. No reasigna stock ni recalcula kardex.
                </div>

                <div v-if="loadingLotes" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-system"></i>
                    <div class="text-muted mt-2">Cargando lotes...</div>
                </div>

                <div v-else class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="bg-system text-white">
                            <tr>
                                <th style="width: 140px;">Codigo</th>
                                <th>Producto</th>
                                <th style="width: 110px;" class="text-end">Cantidad</th>
                                <th style="width: 190px;">Lote</th>
                                <th style="width: 180px;">F. Elaboracion</th>
                                <th style="width: 180px;">F. Caducidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="detalle in formLotes.detalles" :key="detalle.id">
                                <td>{{ detalle.codigo }}</td>
                                <td>{{ detalle.producto }}</td>
                                <td class="text-end">{{ detalle.cantidad }}</td>
                                <td>
                                    <input
                                        v-model.trim="detalle.lote"
                                        type="text"
                                        class="form-control form-control-sm"
                                        :class="{'is-invalid': ((erroresLotes.detalles || {})[detalle.id] || {}).lote}">
                                    <small class="text-danger">{{ ((erroresLotes.detalles || {})[detalle.id] || {}).lote }}</small>
                                </td>
                                <td>
                                    <input
                                        v-model="detalle.fechaElaboracion"
                                        type="date"
                                        class="form-control form-control-sm"
                                        :class="{'is-invalid': ((erroresLotes.detalles || {})[detalle.id] || {}).fechaElaboracion}">
                                    <small class="text-danger">{{ ((erroresLotes.detalles || {})[detalle.id] || {}).fechaElaboracion }}</small>
                                </td>
                                <td>
                                    <input
                                        v-model="detalle.fechaCaducidad"
                                        type="date"
                                        class="form-control form-control-sm"
                                        :class="{'is-invalid': ((erroresLotes.detalles || {})[detalle.id] || {}).fechaCaducidad}">
                                    <small class="text-danger">{{ ((erroresLotes.detalles || {})[detalle.id] || {}).fechaCaducidad }}</small>
                                </td>
                            </tr>
                            <tr v-if="!formLotes.detalles.length">
                                <td colspan="6" class="text-center text-muted py-4">
                                    Esta compra no tiene items con control de lote.
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
                <button type="button" class="btn btn-primary" :disabled="loadingGuardarLotes" @click="guardarLotes">
                    <i v-if="loadingGuardarLotes" class="fas fa-spinner fa-spin me-1"></i>
                    <i v-else class="fas fa-save me-1"></i>
                    Guardar cambios
                </button>
            </div>
        </div>
    </div>
</div>