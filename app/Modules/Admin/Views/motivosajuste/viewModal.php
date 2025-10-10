<!DOCTYPE html>
<!--
/**
 * Description of viewModal
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 7 oct 2025
 * @time 7:28:38 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="modalMotivosAjuste" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 v-if="estadoSave"><i class="fas fa-plus-circle"></i> Crear Motivo de Ajuste</h5>
                <h5 v-else><i class="fas fa-edit"></i> Actualizar Motivo de Ajuste</h5>
                <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Nombre -->
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label col-form-label-sm">
                            <span style="color: red; font-size:15px">*</span>
                            <i class="fal fa-tag"></i> Nombre
                        </label>
                        <input v-model="newMotivo.motNombre" type="text" class="form-control" placeholder="Ej: AJUSTE INICIAL"/>
                        <div v-html="formValidacion.motNombre" class="text-danger"></div>
                    </div>

                    <!-- Tipo -->
                    <div class="col-md-8 mb-3">
                        <label class="col-form-label col-form-label-sm">
                            <span style="color: red; font-size:15px">*</span>
                            <i class="fal fa-list"></i> Tipo de Motivo
                        </label>
                        <select v-model="newMotivo.motTipo" class="form-select border">
                            <option value="AJUSTES">AJUSTES</option>
                            <option value="DESPACHOS">DESPACHOS</option>

                        </select>
                        <div v-html="formValidacion.motTipo" class="text-danger"></div>
                    </div>


                    <!-- Estado -->
                    <div class="col-md-4 mb-3">
                        <label class="col-form-label col-form-label-sm">Estado</label>
                        <select v-model="newMotivo.motEstado" class="form-select border">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>


                    <!-- Detalle -->
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label col-form-label-sm">
                            <span style="color: red; font-size:15px">*</span>
                            <i class="fal fa-align-left"></i> Detalle
                        </label>
                        <textarea v-model="newMotivo.motDetalle" class="form-control" rows="4" placeholder="Ingrese detalle del motivo"></textarea>
                        <div v-html="formValidacion.motDetalle" class="text-danger"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" @click="saveUpdateMotivo()">
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
