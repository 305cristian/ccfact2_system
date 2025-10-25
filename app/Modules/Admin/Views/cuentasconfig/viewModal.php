<!DOCTYPE html>
<!--
/**
 * Description of viewModal
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 6 oct 2025
 * @time 8:53:50 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="modalCuentaConfig" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 v-if="estadoSave"><i class="fas fa-plus-circle"></i> Crear Configuración Cuenta</h5>
                <h5 v-else><i class="fas fa-edit"></i> Actualizar Configuración Cuenta</h5>
                <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Código -->
                    <div class="col-md-4 mb-3">
                        <label class="col-form-label col-form-label-sm">
                            <span style="color: red; font-size:15px">*</span>
                            <i class="fal fa-file"></i> Código
                        </label>
                        <input v-model="newConfigCuenta.ctcfCodigo" type="text" class="form-control" placeholder="Ej: 010"/>
                        <div v-html="formValidacion.ctcfCodigo" class="text-danger"></div>
                    </div>

                    <!-- Estado -->
                    <div class="col-md-4 mb-3">
                        <label class="col-form-label col-form-label-sm">Estado</label>
                        <select v-model="newConfigCuenta.ctcfEstado" class="form-select border">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>

                    <!-- Nombre -->
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label col-form-label-sm">
                            <span style="color: red; font-size:15px">*</span>
                            <i class="fal fa-file-alt"></i> Nombre
                        </label>
                        <input v-model="newConfigCuenta.ctcfNombre" type="text" class="form-control" placeholder="Ingrese nombre de la configuración"/>
                        <div v-html="formValidacion.ctcfNombre" class="text-danger"></div>
                    </div>

                    <!-- Detalle -->
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label col-form-label-sm">
                            <span style="color: red; font-size:15px">*</span>
                            <i class="fal fa-align-left"></i> Detalle
                        </label>
                        <textarea v-model="newConfigCuenta.ctcfDetalle" class="form-control" rows="3" placeholder="Ingrese detalle de la configuración"></textarea>
                        <div v-html="formValidacion.ctcfDetalle" class="text-danger"></div>
                    </div>

                    <!-- Cuenta Contable Detalle -->
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label col-form-label-sm">
                            <span style="color: red; font-size:15px">*</span>
                            <i class="fal fa-file-invoice"></i> Cuenta Contable Detalle
                        </label>
                       
                        <vue-multiselect
                            v-model="newConfigCuenta.fkCuentaContableDet"
                            placeholder="Buscar cuenta contable..."
                            label="ctad_codigo"
                            track-by="ctad_codigo"
                            :multiple="false"
                            :searchable="true"
                            :options="listaSearchCuentasContables"
                            @search-change="searchCuentasContables($event)">
                            <template #option="{ option }">
                                <span style="font-size: 12px">{{ option.ctad_codigo }}: <strong>{{ option.ctad_nombre_cuenta }}</strong></span>
                            </template>
                        </vue-multiselect>
                        <div v-html="formValidacion.fkCuentaContableDet" class="text-danger"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" @click="saveUpdateConfigCuenta()">
                    <span v-if="estadoSave">
                        <span v-if='loading'><i class="loading-spin"></i> Creando...</span>
                        <span v-else><i class="fas fa-save"></i> Crear</span>
                    </span>
                    <span v-else>
                        <span v-if='loading'><i class="loading-spin"></i> Actualizando...</span>
                        <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                    </span>
                </button>
                <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
<!-- FIN MODAL -->