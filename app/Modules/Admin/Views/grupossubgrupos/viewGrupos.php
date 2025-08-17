<!DOCTYPE html>
<!--
/**
 * Description of viewGrupos
 *
/**
 * @author CRISTIAN PAZ
 * @date 16 abr. 2024
 * @time 12:27:49
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div class="container-fluid">
    <br>
    <table id="tblGrupos" class="table table-striped nowrap display" style="width: 100%">
        <thead class="bg-system text-white">
            <tr>
                <td>ID</td>
                <td>NOMBRE</td>
                <td>DESCRIPCIÓN</td>
                <td>ICONO</td>
                <td>ESTADO</td>
                <td>ACCIONES</td>
            </tr>
        </thead>
        <tbody>
            <tr v-for="lg of listaGrupos">
                <td>{{zfill(lg.id)}}</td>
                <td>{{lg.gr_nombre}}</td>
                <td>{{lg.gr_descripcion}}</td>
                <td>{{lg.gr_icon}}</td>

                <td v-if="lg.gr_estado == 1"><span class="badge bg-success"><i class="fas fa-check-double"></i>  Activo</span></td>
                <td v-else><span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span></td>

                <td>
                    <template v-if="admin">
                        <button @click="loadGrupo(lg), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalGrupo"><i class="fas fa-edit"></i> </button>
                    </template>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<!--MODAL GRUPOS-->
<div id="modalGrupo" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 v-if="estadoSave" class=""><i class="fas fa-file-alt"></i> Crear Grupo</h5>
                <h5 v-else class=""><i class="fas fa-file-alt"></i> Actualizar Grupo</h5>
                <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
            </div>
            <div class="modal-body">
                <div class="text-left">
                    <input type="hidden" v-model="idEdit">
                    <div class="mb-3">
                        <label for="grNombre" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Nombre</label>
                        <input  v-model="newGrupo.grNombre" type="text" class="form-control" id="grNombre" placeholder="Ingrese un nombre" />
                        <!--validaciones-->
                        <div v-html="formValidacion.grNombre" class="text-danger"></div>
                    </div>                       
                    <div class="mb-3">
                        <label for="grDescripcion" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Descripción</label>
                        <textarea  v-model="newGrupo.grDescripcion" type="text" class="form-control" id="grDescripcion" placeholder="Ingrese una descripción" ></textarea>
                        <!--validaciones-->
                        <div v-html="formValidacion.grDescripcion" class="text-danger"></div>
                    </div>  
                    <div class="mb-3">
                        <label for="grIcon" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Icono</label>
                        <input  v-model="newGrupo.grIcon" type="text" class="form-control" id="grIcon" placeholder="fas fa-folder" />
                     
                    </div>      

                    <div class="mb-3">
                        <label for="grEstado" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Estado</label>
                        <select v-model="newGrupo.grEstado" class="form-select border" id="grEstado">
                            <option value="1">ACTIVO</option>
                            <option value="0"> INACTIVO</option>
                        </select>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button  class="btn btn-primary" @click="saveUpdateGrupo()">
                    <span v-if="estadoSave"><i class="fas fa-save"></i> Crear</span>
                    <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                </button>
                <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-stop"></i> Cancelar</button>
            </div>
        </div>
    </div>
    <!--CLOSE MODAL GRUPOS-->
</div>

