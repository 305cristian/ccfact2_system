<!DOCTYPE html>
<!--
/**
 * Description of viewAcc
 *
/**
 * @author CRISTIAN PAZ
 * @date 15 feb. 2024
 * @time 15:04:47
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div class="container-fluid">
    <br>
    <table id="tblAcciones" class="table table-striped nowrap display" style="width: 100%">
        <thead class="bg-system text-white">
            <tr>
                <td>ID</td>
                <td>ACCIÓN</td>
                <td>DESCRIPCIÓN</td>
                <td>MÓDULO</td>
                <td>SUBMÓDULO</td>
                <td>ESTADO</td>
                <td>ACCIONES</td>
            </tr>
        </thead>
        <tbody>
            <tr v-for="lac of listaAcciones">
                <td>{{zfill(lac.id)}}</td>
                <td>{{lac.ac_nombre}}</td>
                <td>{{lac.ac_detalle}}</td>
                <td>{{lac.modulo}}</td>
                <td>{{lac.submodulo}}</td>

                <td v-if="lac.ac_estado == 1 "><span class="badge bg-success"><i class="fas fa-check-double"></i>  Activo</span></td>
                <td v-else><span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span></td>

                <td>
                    <template v-if="admin">
                        <button @click="loadAccion(lac), estadoSave2 = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalAcciones"><i class="fas fa-edit"></i> </button>
                    </template>
                </td>
            </tr>
        </tbody>
    </table>

    <div id="modalAcciones" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 v-if="estadoSave2" class=""><i class="fas fa-file-alt"></i> Crear Acción</h5>
                    <h5 v-else class=""><i class="fas fa-file-alt"></i> Actualizar Acción</h5>
                    <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
                </div>
                <div class="modal-body">
                    <div class="text-left">
                        <input type="hidden" v-model="idEdit2">
                        <div class="mb-3">
                            <label for="accion" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Nombre de la acción</label>
                            <input  v-model="newAccion.nombreAccion" type="text" class="form-control" id="accion" placeholder="Ingrese un nombre" />
                            <!--validaciones-->
                            <div v-html="formValidacion2.nombreAccion" class="text-danger"></div>
                        </div>

                        <div class="mb-3">
                            <label for="detalle" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Detalle de la acción</label>
                            <textarea  v-model="newAccion.detalleAccion" type="text" class="form-control" id="detalle" placeholder="Ingrese un detalle"></textarea>
                            <!--validaciones-->
                            <div v-html="formValidacion2.detalleAccion" class="text-danger"></div>
                        </div>

                        <div class="mb-3">
                            <label for="modulo" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Módulo</label>
                            <select id="selectModulo" @change='loadSubModulo()' title="Seleccione un módulo" v-model="newAccion.moduloAccion" class="form-control border selectpicker show-tick" id="modulo">
                                <option v-for="lom of listaOnlyModulos" v-bind:value="lom.id">{{lom.md_nombre}}</option>                             
                            </select>
                            <!--validaciones-->
                            <div v-html="formValidacion2.moduloAccion" class="text-danger"></div>
                        </div>
                        <div class="mb-3">
                            <label for="submodulo" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> SubMódulo</label>
                            <select title="Seleccione un submódulo" v-model="newAccion.subModuloAccion" class="form-select border" id="submodulo">
                                <option v-for="losbm of listaOnlySubModulos" v-bind:value="losbm.id">{{losbm.md_nombre}}</option>                             
                            </select>
                            <!--validaciones-->
                            <div v-html="formValidacion2.subModuloAccion" class="text-danger"></div>
                        </div>

                        <div class="mb-3">
                            <label for="selectEstado" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Estado</label>
                            <select v-model="newAccion.estado" class="form-select border" id="selectEstado">
                                <option value="1">ACTIVO</option>
                                <option value="0"> INACTIVO</option>
                            </select>
                        </div>


                    </div>
                </div>
                <div class="modal-footer">
                    <button  class="btn btn-primary" @click="saveUpdateAccion()">
                        <span v-if="estadoSave2"><i class="fas fa-save"></i> Crear</span>
                        <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                    </button>
                    <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-stop"></i> Cancelar</button> 
                </div>
            </div>
        </div>
    </div>
</div>
