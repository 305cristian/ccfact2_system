<!DOCTYPE html>
<!--
/**
 * Description of viewModal
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 29 ago 2024
 * @time 4:09:30 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="modalClientes" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 v-if="estadoSave" class=""><i class="fas fa-user-tie"></i> Crear Cliente</h5>
                <h5 v-else class=""><i class="fas fa-user-tie"></i> Actualizar Cliente</h5>
                <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
            </div>
            <div class="modal-body">

                <input type="hidden" v-model="idEdit">
                <div class="col-md-12 ">
                    <div class="row">
                        <div class="mb-2 col-md-4">
                            <label for="clieTipoDocumento" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i><span style="color: red; font-size:15px ">*</span> Tipo de Documento</label>
                            <select @change="setTipoCliente()"  v-model="newCliente.clieTipoDocumento" id="clieTipoDocumento"  class="form-select" >
                                <option v-for='ltd of listaTipoDocumento' v-bind:value='ltd.id'>{{ltd.doc_nombre}}</option>
                            </select>    
                            <!--validaciones-->
                            <div v-html="formValidacion.clieTipoDocumento" class="text-danger"></div>
                        </div>       

                        <div  class="mb-2 col-md-4">
                            <label for="clieCiruc" class="col-form-label col-form-label-sm"><span style="color: red; font-size:15px ">*</span> CI/RUC</label>
                            <input v-model="newCliente.clieCiruc" type="text" class="form-control" id="clieCiruc" placeholder="Ingrese identificación" />
                            <!--validaciones-->
                            <div v-html="formValidacion.clieCiruc" class="text-danger"></div>
                        </div>

                        <div class="mb-2 col-md-4">
                            <label for="clieTipoCliente" class="col-form-label col-form-label-sm"><span style="color: red; font-size:15px ">*</span> Tipo Cliente</label>
                            <select v-model="newCliente.clieTipoCliente" id="clieTipoCliente" class="form-select border">
                                <option value="NATURAL">Natural</option>
                                <option value="JURIDICO">Jurídico</option>
                                <option value="EXTRANGERO">Extrangero</option>
                            </select>
                             <!--validaciones-->
                            <div v-html="formValidacion.clieTipoCliente" class="text-danger"></div>
                        </div>
                    </div>  
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="clieNombres" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> <span style="color: red; font-size:15px ">*</span>Nombres</label>
                            <input @keyup ="setRazonSocial()"  v-model="newCliente.clieNombres" type="text" class="form-control" id="clieNombres" placeholder="Ingrese los nombres" />
                            <!--validaciones-->
                            <div v-html="formValidacion.clieNombres" class="text-danger"></div>
                        </div>       

                        <div class="mb-3 col-md-6">
                            <label for="clieApellidos" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> <span style="color: red; font-size:15px ">*</span>Apellidos</label>
                            <input @keyup ="setRazonSocial()" v-model="newCliente.clieApellidos" type="text" class="form-control" id="clieApellidos" placeholder="Ingrese los apellidos" />
                            <!--validaciones-->
                            <div v-html="formValidacion.clieApellidos" class="text-danger"></div>
                        </div>  
                    </div> 
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="col-form-label col-form-label-sm"><span style="color: red; font-size:15px ">*</span> Razón Social</label>
                            <input v-model="newCliente.clieRazonSocial" type="text" class="form-control" placeholder="Ingrese razón social" />
                            <!--validaciones-->
                            <div v-html="formValidacion.clieRazonSocial" class="text-danger"></div>
                        </div>
                        <div class="mb-3 col-md-3">
                            <label for="clieSexo" class="col-form-label col-form-label-sm"><span style="color: red; font-size:15px ">*</span> Sexo</label>
                            <select v-model="newCliente.clieSexo" id="clieSexo" class="form-select border">
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                            <!--validaciones-->
                            <div v-html="formValidacion.clieSexo" class="text-danger"></div>
                        </div>
                        <div class="mb-3 col-md-3">
                            <label for="clieGenero" class="col-form-label col-form-label-sm">Género</label>
                            <input  v-model="newCliente.clieGenero" type="text" class="form-control" id="clieGenero" placeholder="Ingrese el género" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-md-4">
                            <label for="clieTelefono" class="col-form-label col-form-label-sm">Teléfono</label>
                            <input v-model="newCliente.clieTelefono" type="number" class="form-control" id="clieTelefono" placeholder="Ingrese teléfono" />
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="clieCelular" class="col-form-label col-form-label-sm"><span style="color: red; font-size:15px ">*</span> Celular</label>
                            <input v-model="newCliente.clieCelular" type="number" class="form-control" id="clieCelular" placeholder="Ingrese celular" />
                            <!--validaciones-->
                            <div v-html="formValidacion.clieCelular" class="text-danger"></div>
                        </div>
                        <div  class="mb-3 col-md-4">
                            <label for="clieEmail" class="col-form-label col-form-label-sm"><span style="color: red; font-size:15px ">*</span> Email</label>
                            <input v-model="newCliente.clieEmail" type="email" class="form-control" id="clieEmail" placeholder="Ingrese correo" />
                            <!--validaciones-->
                            <div v-html="formValidacion.clieEmail" class="text-danger"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div  class="mb-6 col-md-10">
                            <label for="clieDireccion" class="col-form-label col-form-label-sm"><span style="color: red; font-size:15px ">*</span> Dirección</label>
                            <input v-model="newCliente.clieDireccion" type="text" class="form-control" id="clieDireccion" placeholder="Ingrese dirección" />
                            <!--validaciones-->
                            <div v-html="formValidacion.clieDireccion" class="text-danger"></div>
                        </div>
                        <div class="mb-2 col-md-2">
                            <label for="clieDiasCredito" class="col-form-label col-form-label-sm">Días de Crédito</label>
                            <input v-model="newCliente.clieDiasCredito" type="number" class="form-control" id="clieDiasCredito" placeholder="0" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-2 col-md-4">
                            <label for="clieProvincia" class="col-form-label col-form-label-sm"> <span style="color: red; font-size:15px ">*</span> Provincia</label>
                            <select @change="getCantones()" v-model="provincia" class="form-select border" id="clieProvincia">
                                <option v-for="lprv of listaProvincia" v-bind:value="lprv.id">{{lprv.prv_nombre}}</option>
                            </select>
                        </div>
                        <div class="mb-2 col-md-4">
                            <label for="clieCanton" class="col-form-label col-form-label-sm"> <span style="color: red; font-size:15px ">*</span> Cantón</label>
                            <select @change="getParroquias()" v-model="canton" class="form-select border" id="clieCanton">
                                <option v-for="lctn of listaCanton" v-bind:value="lctn.id">{{lctn.ctn_nombre}}</option>
                            </select>
                        </div>
                        <div class="mb-2 col-md-4">
                            <label for="clieParroquia" class="col-form-label col-form-label-sm"> <span style="color: red; font-size:15px ">*</span> Parroquia</label>
                            <select v-model="newCliente.clieParroquia" class="form-select border" id="clieParroquia">
                                <option v-for="lprr of listaParroquia" v-bind:value="lprr.id">{{lprr.prr_nombre}}</option>
                            </select>
                            <!--validaciones-->
                            <div v-html="formValidacion.clieParroquia" class="text-danger"></div>
                        </div>
                    </div>

                </div> 

                <hr>
                <div class="col-md-12 row">

                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input  v-model="newCliente.clieEstado" class="form-check-input" type="checkbox" id="clieEstado">
                            <label class="form-check-label" for="clieEstado">Estado</label>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button  class="btn btn-system-2" @click="saveUpdateCliente()">
                        <span v-if="estadoSave"><i class="fas fa-save"></i> Crear Cliente</span>
                        <span v-else><i class="fas fa-refresh"></i> Actualizar Cliente</span>
                    </button>
                    <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-stop"></i> Cancelar</button>
                </div>
            </div>
        </div>

    </div>
</div>