<!DOCTYPE html>
<!--
/**
 * Description of viewModal
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 17 ago 2025
 * @time 10:22:04 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="modalProveedores" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 v-if="estadoSave" class=""><i class="fas fa-user-tie"></i> Crear Proveedor</h5>
                <h5 v-else class=""><i class="fas fa-user-tie"></i> Actualizar Proveedor</h5>
                <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
            </div>
            <div class="modal-body">

                <input type="hidden" v-model="idEdit">
                <div class="col-md-12 ">
                    <div class="row">
                        <div class="mb-2 col-md-4">
                            <label for="provTipoDocumento" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i><span style="color: red; font-size:15px ">*</span> Tipo de Documento</label>
                            <select @change="setTipoProveedor()" v-model="newProveedor.provTipoDocumento" id="provTipoDocumento" class="form-select">
                                <option v-for='ltd of listaTipoDocumento' v-bind:value='ltd.id'>{{ltd.doc_nombre}}</option>
                            </select>    
                            <!--validaciones-->
                            <div v-html="formValidacion.provTipoDocumento" class="text-danger"></div>
                        </div>       

                        <div class="mb-2 col-md-4">
                            <label for="provRuc" class="col-form-label col-form-label-sm"><span style="color: red; font-size:15px ">*</span> RUC</label>
                            <input v-model="newProveedor.provRuc" type="text" class="form-control" id="provRuc" placeholder="Ingrese identificación" />
                            <!--validaciones-->
                            <div v-html="formValidacion.provRuc" class="text-danger"></div>
                        </div>

                        <div class="mb-2 col-md-4">
                            <label for="provTipoProveedor" class="col-form-label col-form-label-sm"> Tipo Proveedor</label>
                            <select v-model="newProveedor.provTipoProveedor" id="provTipoProveedor" class="form-select border">
                                <option value="1">Natural</option>
                                <option value="2">Jurídico</option>
                                <option value="4">Extranjero</option>
                            </select>
                        </div>
                    </div>  
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="provNombres" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> <span style="color: red; font-size:15px ">*</span>Nombres</label>
                            <input @keyup ="setRazonSocial()" v-model="newProveedor.provNombres" type="text" class="form-control" id="provNombres" placeholder="Ingrese los nombres" />
                            <!--validaciones-->
                            <div v-html="formValidacion.provNombres" class="text-danger"></div>
                        </div>       

                        <div class="mb-3 col-md-6">
                            <label for="provApellidos" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> <span style="color: red; font-size:15px ">*</span>Apellidos</label>
                            <input @keyup ="setRazonSocial()" v-model="newProveedor.provApellidos" type="text" class="form-control" id="provApellidos" placeholder="Ingrese los apellidos" />
                            <!--validaciones-->
                            <div v-html="formValidacion.provApellidos" class="text-danger"></div>
                        </div>  
                    </div> 
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="provRazonSocial" class="col-form-label col-form-label-sm"><span style="color: red; font-size:15px ">*</span> Razón Social</label>
                            <input v-model="newProveedor.provRazonSocial" type="text" class="form-control" id="provRazonSocial" placeholder="Ingrese razón social" />
                            <!--validaciones-->
                            <div v-html="formValidacion.provRazonSocial" class="text-danger"></div>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="provCtaContable" class="col-form-label col-form-label-sm"> Cuenta Contable</label>
                            <select title="Seleccione una cuenta" v-model="newProveedor.provCtaContable" class="form-control border selectpicker" id="provCtaContable" data-live-search="true">
                                <option v-for="lcta of listaCuentasContable" v-bind:value="lcta.ctad_codigo">{{lcta.ctad_codigo +" "+ lcta.ctad_nombre_cuenta}}</option>
                            </select>
                        </div>

                    </div>
                    <div class="row">
                        <div class="mb-3 col-md-4">
                            <label for="provTelefono" class="col-form-label col-form-label-sm">Teléfono</label>
                            <input v-model="newProveedor.provTelefono" type="number" class="form-control" id="provTelefono" placeholder="Ingrese teléfono" />
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="provCelular" class="col-form-label col-form-label-sm"><span style="color: red; font-size:15px ">*</span> Celular</label>
                            <input v-model="newProveedor.provCelular" type="number" class="form-control" id="provCelular" placeholder="Ingrese celular" />
                            <!--validaciones-->
                            <div v-html="formValidacion.provCelular" class="text-danger"></div>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="provEmail" class="col-form-label col-form-label-sm"><span style="color: red; font-size:15px ">*</span> Email</label>
                            <input v-model="newProveedor.provEmail" type="email" class="form-control" id="provEmail" placeholder="Ingrese correo" />
                            <!--validaciones-->
                            <div v-html="formValidacion.provEmail" class="text-danger"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-6 col-md-8">
                            <label for="provDireccion" class="col-form-label col-form-label-sm"><span style="color: red; font-size:15px ">*</span> Dirección</label>
                            <input v-model="newProveedor.provDireccion" type="text" class="form-control" id="provDireccion" placeholder="Ingrese dirección" />
                            <!--validaciones-->
                            <div v-html="formValidacion.provDireccion" class="text-danger"></div>
                        </div>
                        <div class="mb-2 col-md-2">
                            <label for="provSector" class="col-form-label col-form-label-sm"><span style="color: red; font-size:15px ">*</span> Sector</label>
                            <select v-model="newProveedor.provSector" id="provSector" class="form-select border">
                                <option v-for="ls of listaSectores" v-bind:value="ls.id">{{ls.sec_nombre}}</option>
                            </select>
                            <!--validaciones-->
                            <div v-html="formValidacion.provSector" class="text-danger"></div>
                        </div>
                        <div class="mb-2 col-md-2">
                            <label for="provDiasCredito" class="col-form-label col-form-label-sm"> Días de Crédito</label>
                            <input v-model="newProveedor.provDiasCredito" type="number" class="form-control" id="provDiasCredito" placeholder="0" />

                        </div>

                    </div>
                    <div class="row">
                        <div class="mb-2 col-md-4">
                            <label for="provProvincia" class="col-form-label col-form-label-sm"> <span style="color: red; font-size:15px ">*</span> Provincia</label>
                            <select title="Seleccione una provincia" @change="getCantones()" v-model="provincia" class="form-control border selectpicker" id="provProvincia" data-live-search="true" data-style="btn-white">
                                <option v-for="lprv of listaProvincia" v-bind:value="lprv.id">{{lprv.prv_nombre}}</option>
                            </select>
                        </div>
                        <div class="mb-2 col-md-4">
                            <label for="provCanton" class="col-form-label col-form-label-sm"> <span style="color: red; font-size:15px ">*</span> Cantón</label>
                            <select  @change="getParroquias()" v-model="canton" class="form-select border" id="provCanton">
                                <option v-for="lctn of listaCanton" v-bind:value="lctn.id">{{lctn.ctn_nombre}}</option>
                            </select>
                        </div>
                        <div class="mb-2 col-md-4">
                            <label for="provParroquia" class="col-form-label col-form-label-sm"> <span style="color: red; font-size:15px ">*</span> Parroquia</label>
                            <select v-model="newProveedor.provParroquia" class="form-select border" id="provParroquia">
                                <option v-for="lprr of listaParroquia" v-bind:value="lprr.id">{{lprr.prr_nombre}}</option>
                            </select>
                            <!--validaciones-->
                            <div v-html="formValidacion.provParroquia" class="text-danger"></div>
                        </div>
                    </div>

                </div> 
                <hr>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12"> 
                            <label for="provBanco" class="col-form-label col-form-label-sm"> <span style="color: red; font-size:15px "></span> # de cuenta Bancaria</label>
                            <template>
                                <vue-multiselect 
                                    v-model="vmodelBancos" 
                                    tag-placeholder="Banco no encontrado"
                                    placeholder="Buscar y agregar un banco"
                                    label="banc_nombre"
                                    track-by="id"
                                    :multiple="true"
                                    :searchable="true"
                                    :taggable="true"
                                    :loading="isLoadingBank"
                                    :options-limit="10"
                                    :show-no-results="true"
                                    :options="listaBancos"
                                    @search-change="getBancos($event)"
                                    >
                                </vue-multiselect>
                            </template>
                            <div v-if='vmodelBancos.length > 0'>
                                <table class="table" id="tblProveedorBanco">
                                    <thead class="bg-secondary">
                                        <tr>
                                            <th>Banco</th>
                                            <th>Tipo Cuenta</th>
                                            <th># de cuenta</th>
                                        </tr>
                                    </thead>
                                    <tr v-for="v of vmodelBancos" :key="v.id">
                                        <td style="display: none">{{v.id}}</td>
                                        <td style="width: 25%">{{v.banc_nombre}}</td>
                                        <td style="width: 25%">
                                            <select class="form-select input-sm" v-model='v.tipo_cuenta'>
                                                <option v-for='lbtc of listaTipoCuentaBanco' v-bind:value='lbtc.id'>{{lbtc.tipo_cuenta}}</option>
                                            </select>
                                        </td>
                                        <td style="width: 50%"><input type="number" class="form-control"  v-model.number="v.numero_cuenta"></td>
                                    </tr>
                                </table>
                            </div>
                        </div> 
                    </div>
                </div>
                <hr>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12"> 
                            <label for="provBanco" class="col-form-label col-form-label-sm"> <span style="color: red; font-size:15px "></span> Retenciones del Proveedor</label>
                            <template>
                                <vue-multiselect 
                                    v-model="vmodelRtenciones" 
                                    tag-placeholder="Retención no encontrado"
                                    placeholder="Digite el código de retención (312)"
                                    label='ret_codigo'
                                    track-by="id"
                                    :multiple="true"
                                    :searchable="true"
                                    :taggable="true"
                                    :loading="isLoadingRet"
                                    :options-limit="10"
                                    :show-no-results="true"
                                    :options="listaRetenciones"
                                    @search-change="getRetenciones($event)"
                                    >
                                    <template slot="option" slot-scope="{ option }">
                                        <span style="font-size: 12px">{{ option.ret_codigo+' - ' }} <strong>{{ option.ret_nombre }}</strong> </span>
                                    </template>
                                </vue-multiselect>
                            </template>
                            <div v-if='vmodelRtenciones.length > 0'>
                                <table class="table" id="tblRetenciones">
                                    <thead class="bg-secondary">
                                        <tr>
                                            <th>Código</th>
                                            <th>Retención</th>
                                        </tr>
                                    </thead>
                                    <tr v-for="v of vmodelRtenciones" :key="v.id">
                                        <td style="display: none">{{v.id}}</td>
                                        <td style="width: 25%">{{v.ret_codigo}}</td>
                                        <td style="width: 75%">{{v.ret_nombre}} </td>
                                    </tr>
                                </table>
                            </div>
                        </div> 
                    </div>
                </div>
                <hr>
                <div class="col-md-12 row">

                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input v-model="newProveedor.provEstado" class="form-check-input" type="checkbox" id="provEstado">
                            <label class="form-check-label" for="provEstado">Estado</label>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-system-2" @click="saveUpdateProveedor()" :disabled="loading">
                        <span v-if="estadoSave">
                            <span v-if='loading'><i class="loading-spin"></i> Creando...</span>
                            <span v-else ><i class="fas fa-save"></i> Crear Proveedor</span>
                        </span>
                        <span v-else>
                            <span v-if='loading'><i class="loading-spin"></i> Actualizando...</span>
                            <span v-else ><i class="fas fa-refresh"></i> Actualizar Proveedor</span>
                        </span>
                    </button>
                    <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal" :disabled="loading"><i class="fas fa-stop"></i> Cancelar</button>
                </div>
            </div>
        </div>

    </div>
</div>
