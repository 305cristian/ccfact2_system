<!DOCTYPE html>
<!--
/**
 * Description of viewMod
 *
/**
 * @author CRISTIAN PAZ
 * @date 15 feb. 2024
 * @time 15:00:22
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<div class="container-fluid">
    <br>
    <div style="overflow-x: auto">
    <table id="tblModulos" class="table table-striped nowrap display" style="width: 100%">
        <thead class="bg-system text-white">
            <tr>
                <td>ID</td>
                <td>MÓDULO / SUBMÓDULO</td>
                <td>DESCRIPCIÓN</td>
                <td>TIPO</td>
                <td>URL</td>
                <td>MÓDULO PADRE</td>
                <td>ESTADO</td>
                <td>ACCIONES</td>
            </tr>
        </thead>
        <tbody>
            <tr v-for="lm of listaModulos">
                <td>{{zfill(lm.id)}}</td>
                <td>{{lm.md_nombre}}</td>
                <td>{{lm.md_descripcion}}</td>
                <td>{{lm.md_tipo}}</td>
                <td>{{lm.md_url}}</td>
                <td v-if="lm.modulo_padre">{{lm.modulo_padre}}</td>
                <td v-else>--</td>

                <td v-if="lm.md_estado == 1 "><span class="badge bg-success"><i class="fas fa-check-double"></i>  Activo</span></td>
                <td v-else><span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span></td>

                <td>
                    <template v-if="admin">
                        <button @click="loadModulo(lm), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalModulos"><i class="fas fa-edit"></i> </button>
                    </template>
                </td>
            </tr>
        </tbody>
    </table>
    </div>
    <div id="modalModulos" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 v-if="estadoSave" class=""><i class="fas fa-folder-open"></i> Crear Módulo</h5>
                    <h5 v-else class=""><i class="fas fa-folder-open"></i> Actualizar Módulo</h5>
                    <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" v-model="idEdit">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="selectTipo" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Tipo</label>
                                <select @change="toggleTipo()" title="Seleccione un tipo" v-model="newModulo.tipoModulo" class="form-select border" id="selectTipo">
                                    <option value="modulo">MÓDULO</option>
                                    <option value="submodulo"> SUBMÓDULO</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="modulo" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Nombre del Módulo</label>
                                <input  v-model="newModulo.nombreModulo" type="text" class="form-control" id="modulo" placeholder="Ingrese un nombre" />
                                <!--validaciones-->
                                <div v-html="formValidacion.nombreModulo" class="text-danger"></div>
                            </div>

                            <div class="mb-3" id="parent">
                                <label for="selectPadre" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Módulo Padre</label>
                                <select title="Seleccione un módulo" v-model="newModulo.padreModulo" class="form-select border" id="selectPadre">
                                    <option v-for="lom of listaOnlyModulos" v-bind:value="lom.id">{{lom.md_nombre}}</option>                             
                                </select>
                                <div v-html="formValidacion.padreModulo" class="text-danger"></div>
                            </div>

                            <div class="mb-3">
                                <label for="descripcion" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Detalle del Módulo</label>
                                <textarea  v-model="newModulo.descripcionModulo" type="text" class="form-control" id="descripcion" placeholder="Ingrese un detalle" ></textarea>
                                <!--validaciones-->
                                <div v-html="formValidacion.descripcionModulo" class="text-danger"></div>
                            </div>
                        </div>
                        <div class="col-md-6">

                            <div class="mb-3">
                                <label for="url" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Dirección del Módulo</label>
                                <input  v-model="newModulo.urlModulo" type="text" class="form-control" id="url" placeholder="Ingrese un url" />
                                <!--validaciones-->
                                <div v-html="formValidacion.urlModulo" class="text-danger"></div>
                            </div>
                            <div class="mb-3">
                                <label for="icono" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Ícono del Módulo</label>
                                <input  v-model="newModulo.iconoModulo" type="text" class="form-control" id="icono" placeholder="fas fa-save" />
                                <!--validaciones-->
                                <div v-html="formValidacion.iconoModulo" class="text-danger"></div>
                            </div>
                            <div class="mb-3">
                                <label for="orden" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Órden del Módulo</label>
                                <input  v-model="newModulo.ordenModulo" type="number" class="form-control" id="orden" placeholder="Ingrese un orden" />
                                <!--validaciones-->
                                <div v-html="formValidacion.ordenModulo" class="text-danger"></div>
                            </div>

                            <div class="mb-3">
                                <label for="selectEstado" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Estado</label>
                                <select v-model="newModulo.estadoModulo" class="form-select border" id="selectEstado">
                                    <option value="1">ACTIVO</option>
                                    <option value="0"> INACTIVO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="text-left">



                    </div>
                </div>
                <div class="modal-footer">
                    <button  class="btn btn-primary" @click="saveUpdateModulo()">
                        <span v-if="estadoSave"><i class="fas fa-save"></i> Crear</span>
                        <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                    </button>
                    <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-stop"></i> Cancelar</button>                        </div>
            </div>
        </div>
    </div>
</div>
