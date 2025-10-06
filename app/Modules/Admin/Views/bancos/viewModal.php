<!DOCTYPE html>
<!--
/**
 * Description of viewModal
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 2 oct 2025
 * @time 3:49:52 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<!-- MODAL BANCO -->
<div id="modalBanco" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 v-if="estadoSave"><i class="fas fa-file-alt"></i> Crear Banco</h5>
                <h5 v-else><i class="fas fa-file-alt"></i> Actualizar Banco</h5>
                <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
            </div>
            <div class="modal-body">
                <input type="hidden" v-model="idEdit">
                <div class="mb-3">
                    <label for="bancNombre" class="col-form-label col-form-label-sm"><span style="color: red; font-size:15px ">*</span> Nombre</label>
                    <input v-model="newBanco.bancNombre" type="text" class="form-control" id="bancNombre" placeholder="Ingrese un nombre" />
                    <div v-html="formValidacion.bancNombre" class="text-danger"></div>
                </div>
                <div class="mb-3">
                    <label for="bancTipo" class="col-form-label col-form-label-sm">Tipo</label>
                    <select v-model="newBanco.bancTipo" class="form-select border" id="bancTipo">
                        <option value="BANCO">BANCO</option>
                        <option value="COOPERATIVA">COOPERATIVA</option>
                    </select>
                    <div v-html="formValidacion.bancTipo" class="text-danger"></div>
                </div>
                <div class="mb-3">
                    <label for="bancEstado" class="col-form-label col-form-label-sm">Estado</label>
                    <select v-model="newBanco.bancEstado" class="form-select border" id="bancEstado">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" @click="saveUpdateBanco()">
                    <span v-if="estadoSave">
                        <span v-if='loading'><i class="loading-spin"></i> Creando...</span>
                        <span v-else><i class="fas fa-save"></i> Crear</span>
                    </span>
                    <span v-else>
                        <span  v-if='loading'><i class="loading-spin"></i> Actualizando...</span>
                        <span  v-else><i class="fas fa-refresh"></i> Actualizar</span>
                    </span>
                </button>
                <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-stop"></i> Cancelar</button>
            </div>
        </div>
    </div>
</div>
<!-- CLOSE MODAL BANCO -->