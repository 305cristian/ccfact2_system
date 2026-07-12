<!DOCTYPE html>
<!--
/**
 * Description of viewEdicionRapida
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 12 jul 2026
 * @time 11:47:52 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div class="modal fade" ref="modalEdicionRapida" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-cris-system text-white">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i> Edicion rapida
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2">
                    Esta opcion solo actualiza datos administrativos. No modifica valores, pagos, retencion, centros de costo, lotes ni cantidades.
                </div>

                <div class="row">
                    <div class="col-md-12 form-group-custom">
                        <div class="d-flex align-items-center border rounded overflow-visible">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-file-invoice me-2"></i> Comprobante
                            </span>
                            <vue-select
                                class="flex-grow-1"
                                :options="listaTiposComprobantesEdicionRapida"
                                label="comp_nombre"
                                v-model="formEdicionRapida.compTipoComprobante"
                                :reduce="comprobante => comprobante.comp_codigo"
                                placeholder="Seleccione un comprobante">
                                <template #option="comprobante">
                                    {{ comprobante.comp_codigo }} - {{ comprobante.comp_nombre }}
                                </template>
                                <template #selected-option="comprobante">
                                    {{ comprobante.comp_codigo }} - {{ comprobante.comp_nombre }}
                                </template>
                            </vue-select>
                        </div>
                        <small class="text-danger">{{ erroresEdicionRapida.compTipoComprobante }}</small>
                    </div>

                    <div class="col-md-6 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-store me-2"></i> P. Establecimiento
                            </span>
                            <input v-model.trim="formEdicionRapida.compNumeroEstablecimiento" type="text" v-numbers-only inputmode="numeric" maxlength="3" class="form-control" :class="{'is-invalid': erroresEdicionRapida.compNumeroEstablecimiento}">
                        </div>
                        <small class="text-danger">{{ erroresEdicionRapida.compNumeroEstablecimiento }}</small>
                    </div>

                    <div class="col-md-6 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-cash-register me-2"></i> P. Emision
                            </span>
                            <input v-model.trim="formEdicionRapida.compNumeroEmision" type="text" v-numbers-only inputmode="numeric" maxlength="3" class="form-control" :class="{'is-invalid': erroresEdicionRapida.compNumeroEmision}">
                        </div>
                        <small class="text-danger">{{ erroresEdicionRapida.compNumeroEmision }}</small>
                    </div>

                    <div class="col-md-6 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-file-invoice me-2"></i> N. Comprobante
                            </span>
                            <input v-model.trim="formEdicionRapida.compNumeroComprobante" type="text" v-numbers-only inputmode="numeric" maxlength="9" class="form-control" :class="{'is-invalid': erroresEdicionRapida.compNumeroComprobante}">
                        </div>
                        <small class="text-danger">{{ erroresEdicionRapida.compNumeroComprobante }}</small>
                    </div>

                    <div class="col-md-6 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-key me-2"></i> Aut. SRI
                            </span>
                            <input v-model.trim="formEdicionRapida.compAutSRI" type="text" class="form-control" :class="{'is-invalid': erroresEdicionRapida.compAutSRI}">
                        </div>
                        <small class="text-danger">{{ erroresEdicionRapida.compAutSRI }}</small>
                    </div>

                    <div class="col-md-6 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-calendar me-2"></i> F. Emision
                            </span>
                            <input v-model="formEdicionRapida.compFechaEmision" type="date" class="form-control" :class="{'is-invalid': erroresEdicionRapida.compFechaEmision}">
                        </div>
                        <small class="text-danger">{{ erroresEdicionRapida.compFechaEmision }}</small>
                    </div>

                    <div class="col-md-6 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-calendar-check me-2"></i> F.V. Aut.
                            </span>
                            <input v-model="formEdicionRapida.compFechaCaducidad" type="date" class="form-control" :class="{'is-invalid': erroresEdicionRapida.compFechaCaducidad}">
                        </div>
                        <small class="text-danger">{{ erroresEdicionRapida.compFechaCaducidad }}</small>
                    </div>

                    <div class="col-md-12 form-group-custom">
                        <div class="d-flex align-items-center border rounded overflow-visible">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-file-contract me-2"></i> Sustento
                            </span>
                            <vue-select
                                class="flex-grow-1"
                                :options="listaSustentos"
                                label="sus_nombre"
                                v-model="formEdicionRapida.compSustento"
                                :reduce="sustento => sustento.sus_codigo"
                                placeholder="Seleccione un sustento">
                                <template #option="sustento">
                                    {{ sustento.sus_codigo }} - {{ sustento.sus_nombre }}
                                </template>
                                <template #selected-option="sustento">
                                    {{ sustento.sus_codigo }} - {{ sustento.sus_nombre }}
                                </template>
                            </vue-select>
                        </div>
                        <small class="text-danger">{{ erroresEdicionRapida.compSustento }}</small>
                    </div>

                    <div class="col-md-16 form-group-custom">
                        <div class="d-flex align-items-center border rounded overflow-visible">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-shopping-cart me-2"></i> Tipo compra
                            </span>
                            <vue-select
                                class="flex-grow-1"
                                :options="listaTiposCompra"
                                label="tc_nombre"
                                v-model="formEdicionRapida.compTipoCompra"
                                :reduce="tipo => tipo.id"
                                placeholder="Seleccione un tipo">
                                <template #option="tipo">
                                    {{ tipo.tc_codigo }} - {{ tipo.tc_nombre }}
                                </template>
                                <template #selected-option="tipo">
                                    {{ tipo.tc_codigo }} - {{ tipo.tc_nombre }}
                                </template>
                            </vue-select>
                        </div>
                        <small class="text-danger">{{ erroresEdicionRapida.compTipoCompra }}</small>
                    </div>

                    <div class="col-md-6 form-group-custom">
                        <div class="d-flex align-items-center border rounded overflow-visible">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-coins me-2"></i> Tipo costo
                            </span>
                            <vue-select
                                class="flex-grow-1"
                                :options="listaTiposCostos"
                                label="label"
                                v-model="formEdicionRapida.compTipoCosto"
                                :reduce="tipo => tipo.value"
                                placeholder="Seleccione un tipo">
                            </vue-select>
                        </div>
                        <small class="text-danger">{{ erroresEdicionRapida.compTipoCosto }}</small>
                    </div>

                    <div class="col-md-6 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-clipboard-list me-2"></i> ODC
                            </span>
                            <input v-model.trim="formEdicionRapida.compODC" type="number" min="1" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-12 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-sticky-note me-2"></i> Observacion
                            </span>
                            <textarea v-model.trim="formEdicionRapida.compObservaciones" rows="2" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" :disabled="loadingEdicionRapida" @click="guardarEdicionRapida">
                    <i v-if="loadingEdicionRapida" class="fas fa-spinner fa-spin me-1"></i>
                    <i v-else class="fas fa-save me-1"></i>
                    Guardar cambios
                </button>
            </div>
        </div>
    </div>
</div>
