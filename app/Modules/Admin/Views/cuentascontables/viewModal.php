<!DOCTYPE html>
<!--
/**
 * Description of viewModal
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 3 oct 2025
 * @time 8:33:06 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<!-- MODAL -->
<div id="modalCuentasContables" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 v-if="estadoSave"><i class="fas fa-plus-circle"></i> Crear Cuenta Detalle</h5>
                <h5 v-else><i class="fas fa-edit"></i> Actualizar Cuenta Detalle</h5>
                <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Código -->
                    <div class="col-md-4 mb-3">
                        <label class="col-form-label col-form-label-sm"> <span style="color: red; font-size:15px ">*</span> <i class="fal fa-file"></i> Código</label>
                        <input v-model="newCuenta.ctadCodigo" type="text" class="form-control" placeholder="Ej: 1.01.02"/>
                        <div v-html="formValidacion.ctadCodigo" class="text-danger"></div>
                    </div>

                    <!-- Nombre -->
                    <div class="col-md-8 mb-3">
                        <label class="col-form-label col-form-label-sm"> <span style="color: red; font-size:15px ">*</span> <i class="fal fa-file"></i> Nombre Cuenta</label>
                        <input v-model="newCuenta.ctadNombreCuenta" type="text" class="form-control" placeholder="Ingrese nombre de la cuenta"/>
                        <div v-html="formValidacion.ctadNombreCuenta" class="text-danger"></div>
                    </div>


                    <!-- Cuenta Padre -->
                    <div class="col-md-6 mb-3">
                        <label class="col-form-label col-form-label-sm"> <i class="fal fa-file-alt"></i> Cuenta Padre</label>
                        <vue-multiselect
                            v-model="newCuenta.ctadCuentaPadre"
                            placeholder="Ej: 1.01"
                            label="ctad_codigo"
                            track-by="ctad_codigo"
                            :multiple="false"
                            :searchable="true"
                            :options="listaSearchCuentasContables"
                            @search-change="searchCuentasContables($event)">
                            <template slot="option" slot-scope="{ option }">
                                <span style="font-size: 12px">{{ option.ctad_codigo }}: <strong>{{ option.ctad_nombre_cuenta }}</strong></span>
                            </template>
                        </vue-multiselect>
                    </div>

                    <!-- fkCuenta -->
                    <div class="col-md-6 mb-3">
                        <label class="col-form-label col-form-label-sm"><span style="color: red; font-size:15px ">*</span> <i class="fal fa-file-alt"></i> Cuenta</label>
                        <select v-model="newCuenta.fkCtaContable" id="selectCuenta" title="Seleccione una Cuenta" class="form-select border" data-live-search="true" data-style="btn-white">
                            <option v-for="lc of listaCuentas" v-bind:value="lc.cta_codigo">{{lc.cta_nombre}}</option>
                        </select>
                        <div v-html="formValidacion.fkCtaContable" class="text-danger"></div>
                    </div>


                    <!-- Estado -->
                    <div class="col-md-4 mb-3">
                        <label class="col-form-label col-form-label-sm">Estado</label>
                        <select v-model="newCuenta.ctadEstado" class="form-select border">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" @click="saveUpdateCuenta()">
                    <span v-if="estadoSave">
                        <span v-if='loading'><i class="loading-spin"></i> Creando...</span>
                        <span v-else><i class="fas fa-save"></i> Crear</span>
                    </span>
                    <span v-else>
                        <span  v-if='loading'><i class="loading-spin"></i> Actualizando...</span>
                        <span  v-else><i class="fas fa-refresh"></i> Actualizar</span>
                    </span>
                </button>
                <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-times"></i> Cancelar</button>
            </div>
        </div>
    </div>
</div>
<!-- FIN MODAL -->