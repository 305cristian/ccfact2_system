<!DOCTYPE html>
<!--
/**
 * Description of viewSubgrupos
 *
/**
 * @author CRISTIAN PAZ
 * @date 16 abr. 2024
 * @time 12:28:01
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div class="container-fluid">
    <br>
    <table id="tblSubGrupos" class="table table-striped nowrap display" style="width: 100%">
        <thead class="bg-system text-white">
            <tr>
                <td>ID</td>
                <td>NOMBRE</td>
                <td>DESCRIPCIÓN</td>
                <td>ICONO</td>
                <td>GRUPO</td>
                <td>ESTADO</td>
                <td>ACCIONES</td>
            </tr>
        </thead>
        <tbody>
            <tr v-for="lsg of listaSubGrupos">
                <td>{{zfill(lsg.id)}}</td>
                <td>{{lsg.sgr_nombre}}</td>
                <td>{{lsg.sgr_detalle}}</td>
                <td>{{lsg.sgr_icon}}</td>
                <td>{{lsg.gr_nombre}}</td>


                <td v-if="lsg.sgr_estado == 1"><span class="badge bg-success"><i class="fas fa-check-double"></i>  Activo</span></td>
                <td v-else><span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span></td>

                <td>
                    <template v-if="admin">
                        <button @click="loadSubGrupo(lsg), estadoSave2 = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalSubGrupo"><i class="fas fa-edit"></i> </button>
                    </template>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!--MODAL GRUPOS-->
<div id="modalSubGrupo" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 v-if="estadoSave2" class=""><i class="fas fa-file-alt"></i> Crear SubGrupo</h5>
                <h5 v-else class=""><i class="fas fa-file-alt"></i> Actualizar SubGrupo</h5>
                <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
            </div>
            <div class="modal-body">
                <div class="text-left">
                    <input type="hidden" v-model="idEdit2">
                    <div class="mb-3">
                        <label for="sgrGrupo" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Grupo</label>
                        <select v-model="newSubGrupo.sgrGrupo" class="form-select border" id="sgrGrupo">
                            <option v-for="lg of listaGrupos" v-bind:value="lg.id">{{lg.gr_nombre}}</option>                          
                        </select>
                        <!--validaciones-->
                        <div v-html="formValidacion2.sgrGrupo" class="text-danger"></div>
                    </div>

                    <div class="mb-3">
                        <label for="sgrNombre" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Nombre</label>
                        <input  v-model="newSubGrupo.sgrNombre" type="text" class="form-control" id="sgrNombre" placeholder="Ingrese un nombre" />
                        <!--validaciones-->
                        <div v-html="formValidacion2.sgrNombre" class="text-danger"></div>
                    </div>                       
                    <div class="mb-3">
                        <label for="sgrDetalle" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Descripción</label>
                        <textarea  v-model="newSubGrupo.sgrDetalle" type="text" class="form-control" id="sgrDetalle" placeholder="Ingrese una descripción" ></textarea>
                        <!--validaciones-->
                        <div v-html="formValidacion2.sgrDetalle" class="text-danger"></div>
                    </div>  
                    <div class="mb-3">
                        <label for="sgrIcon" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Icono</label>
                        <input  v-model="newSubGrupo.sgrIcon" type="text" class="form-control" id="sgrIcon" placeholder="fas fa-folder" />

                    </div>      

                    <div class="mb-3">
                        <label for="sgrEstado" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Estado</label>
                        <select v-model="newSubGrupo.sgrEstado" class="form-select border" id="sgrEstado">
                            <option value="1">ACTIVO</option>
                            <option value="0"> INACTIVO</option>
                        </select>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button  class="btn btn-primary" @click="saveUpdateSubGrupo()">
                    <span v-if="estadoSave2"><i class="fas fa-save"></i> Crear</span>
                    <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                </button>
                <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-stop"></i> Cancelar</button>
            </div>
        </div>
    </div>
    <!--CLOSE MODAL GRUPOS-->
</div>

