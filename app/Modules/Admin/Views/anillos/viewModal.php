<!DOCTYPE html>
<!--
/**
 * Description of viewModal
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 7 oct 2025
 * @time 1:47:12 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="modalAnillos" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 v-if="estadoSave"><i class="fas fa-plus-circle"></i> Crear Anillo</h5>
                <h5 v-else><i class="fas fa-edit"></i> Actualizar Anillo</h5>
                <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Nombre -->
                    <div class="col-md-8 mb-3">
                        <label class="col-form-label col-form-label-sm">
                            <span style="color: red; font-size:15px">*</span>
                            <i class="fal fa-file-alt"></i> Nombre
                        </label>
                        <input v-model="newAnillo.anNombre" type="text" class="form-control" placeholder="Ej: ANILLO 1"/>
                        <div v-html="formValidacion.anNombre" class="text-danger"></div>
                    </div>

                    <!-- Estado -->
                    <div class="col-md-4 mb-3">
                        <label class="col-form-label col-form-label-sm">Estado</label>
                        <select v-model="newAnillo.anEstado" class="form-select border">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>

                    <!-- Descripción -->
                    <div class="col-md-12 mb-3">
                        <label class="col-form-label col-form-label-sm">
                            <span style="color: red; font-size:15px">*</span>
                            <i class="fal fa-align-left"></i> Descripción
                        </label>
                        <textarea v-model="newAnillo.anDescripcion" class="form-control" rows="4" placeholder="Ingrese descripción del anillo"></textarea>
                        <div v-html="formValidacion.anDescripcion" class="text-danger"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" @click="saveUpdateAnillo()">
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