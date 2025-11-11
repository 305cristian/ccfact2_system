<!DOCTYPE html>
<!--
/**
 * Description of viewModal
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 1 ago 2024
 * @time 12:44:08 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="modalProductos" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 v-if="estadoSave" class=""><i class="fas fa-box-open"></i> Crear Producto</h5>
                <h5 v-else class=""><i class="fas fa-box-open"></i> Actualizar Producto</h5>
                <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
            </div>
            <div class="modal-body">

                <input type="hidden" v-model="idEdit">
                <div class="col-md-12 ">
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="prodNombre" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> <span style="color: red; font-size:15px ">*</span>Nombre</label>
                            <input  v-model="newProducto.prodNombre" type="text" class="form-control" id="prodNombre" placeholder="Ingrese un nombre" />
                            <!--validaciones-->
                            <div v-html="formValidacion.prodNombre" class="text-danger"></div>
                        </div>       

                        <div class="mb-3 col-md-6">
                            <label for="prodNombre" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> <span style="color: red; font-size:15px ">*</span>Código</label>
                            <input  v-model="newProducto.prodCodigo" type="text" class="form-control" id="prodCodigo" placeholder="Ingrese un código" />
                            <!--validaciones-->
                            <div v-html="formValidacion.prodCodigo" class="text-danger"></div>
                        </div>  
                    </div> 
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="prodCodigoBarras" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Cod Barras 1</label>
                            <input  v-model="newProducto.prodCodigoBarras" type="text" class="form-control" id="prodCodigoBarras" placeholder="Ingrese un codigo de barras" />     
                        </div>       
                        <div class="mb-3 col-md-6">
                            <label for="prodCodigoBarras2" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Cod Barras 2</label>
                            <input  v-model="newProducto.prodCodigoBarras2" type="text" class="form-control" id="prodCodigoBarras2" placeholder="Ingrese un codigo de barras" />                               
                        </div>       
                    </div>  
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="prodCodigoBarras3" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Cod Barras 3</label>
                            <input  v-model="newProducto.prodCodigoBarras3" type="text" class="form-control" id="prodCodigoBarras3" placeholder="Ingrese un codigo de barras" />   
                        </div>  
                        <div class="mb-3 col-md-6">                             
                            <label for="prodMarca" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Marca</label>
                            <select v-model="newProducto.prodMarca" class="form-select border" id="prodMarca">
                                <option v-for="lm of listaMarcas" v-bind:value="lm.id">{{lm.mrc_nombre}}</option>
                            </select>
                        </div>
                    </div>  

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="prodExistenciaMinima" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Stock Min</label>
                            <input  v-model="newProducto.prodExistenciaMinima" type="number" class="form-control" id="prodExistenciaMinima" placeholder="Ingrese el Stock minimo" />   
                        </div>       
                        <div class="mb-3 col-md-6">
                            <label for="prodExistenciaMaxima" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Stock Max</label>
                            <input  v-model="newProducto.prodExistenciaMaxima" type="number" class="form-control" id="prodExistenciaMaxima" placeholder="Ingrese el Stock minimo" />   
                        </div>       
                    </div>
                    <div class="row">
                        <div class="mb-3 col-md-6">                             
                            <label for="prodUnidadMedida" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> <span style="color: red; font-size:15px ">*</span>Unidad Medida</label>
                            <select v-model="newProducto.prodUnidadMedida" class="form-select border" id="prodUnidadMedida">
                                <option v-for="lum of listaUnidadesMedida" v-bind:value="lum.id">{{lum.um_nombre}}</option>
                            </select>
                            <div v-html="formValidacion.prodUnidadMedida" class="text-danger"></div>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="prodValorMedida" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Valor Unidad Medida</label>
                            <input  v-model="newProducto.prodValorMedida" type="number" class="form-control" id="prodValorMedida" placeholder="0.00" />   
                        </div>   
                    </div>
                    <div class="row">
                        <div class="mb-3 col-md-6">                                      
                            <label for="prodGrupo" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> <span style="color: red; font-size:15px ">*</span>Grupo</label>
                            <select @change="getSubgrupo()" v-model="idGrupo"  class="form-select border" id="prodGrupo">
                                <option v-for="lg of listaGrupos" v-bind:value="lg.id">{{lg.gr_nombre}}</option>
                            </select>
                            <div v-html="formValidacion.grupo" class="text-danger"></div>
                        </div>
                        <div class="mb-3 col-md-6">                             
                            <label for="prodSubgrupo" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> <span style="color: red; font-size:15px ">*</span>SubGrupo</label>
                            <select v-model="newProducto.prodSubgrupo"  class="form-select border" id="prodSubgrupo">
                                <option v-for="lsg of listaSubGrupos" v-bind:value="lsg.id">{{lsg.sgr_nombre}}</option>
                            </select>
                            <div v-html="formValidacion.prodSubgrupo" class="text-danger"></div>                                 
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-md-6">                             
                            <label for="prodTipoProducto" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> <span style="color: red; font-size:15px ">*</span>Tipo de Producto</label>
                            <select v-model="newProducto.prodTipoProducto"  class="form-select border" id="prodTipoProducto">
                                <option v-for="ltp of listaTipoProducto" v-bind:value="ltp.id">{{ltp.tp_nombre}}</option>
                            </select>
                            <div v-html="formValidacion.prodTipoProducto" class="text-danger"></div>
                        </div>
                        <div class="mb-3 col-md-6"> 

                            <label for="prodIvaPorcentajeId" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> <span style="color: red; font-size:15px ">*</span>Tipo de Impuesto</label>
                            <select @change="desglosarIva2()" v-model="newProducto.prodIvaPorcentajeId"  class="form-select border" id="prodIvaPorcentajeId">
                                <option  v-for="lti of listaImpuestosTarifa" v-bind:value="lti.id">
                                <template v-if="lti.impt_codigo == 2">{{lti.impt_detalle}} {{ivaActual}}%</template>
                                <template v-else>{{lti.impt_detalle}}</template>
                                </option>
                            </select>
                            <div v-html="formValidacion.prodIvaPorcentajeId" class="text-danger"></div>
                        </div>
                    </div>

                </div> 
                <hr>
                <div class="col-md-12">
                    <template v-for="(ltpc,index) of listaTiposPvp">
                        <div class="row">
                            <div class="mb-3 col-md-4">                             
                                <label for="prodTipoPrecio" class="col-form-label col-form-label-sm"><i class="fal fal fa-dollar-circle"></i> <span v-if="ltpc.tpc_nombre == 'pA' " style="color: red; font-size:15px ">*</span>Tipo de Precio</label>
                                <span   class="form-control text-info font-weight-bold" id="prodTipoPrecio" >{{ltpc.tpc_nombre}}</span>   
                            </div>
                            <div class="mb-3 col-md-4">                             
                                <label for="prodPriceIva" class="col-form-label col-form-label-sm"><i class="fal fa-dollar-circle"></i> {{ltpc.tpc_nombre}} con IVA</label>
                                <input  @keyup="desglosarIva(index)" v-model="price[index]" type="number" class="form-control" id="prodPriceIva" placeholder="0.00" />   
                            </div>
                            <div class="mb-3 col-md-4">                             
                                <label for="prodPriceSinIva" class="col-form-label col-form-label-sm"><i class="fal fa-dollar-circle"></i> {{ltpc.tpc_nombre}} sin IVA</label>
                                <input readonly="true" v-model="tipoPrecioVal[index]"type="number" class="form-control" :id=" 'prodPriceSinIva'+index " placeholder="0.00" />   
                                <input v-model="tipoPrecioId[index]" type="hidden"/>   
                            </div>
                        </div>
                    </template>
                </div>

                <hr>
                <div class="col-md-12">
                    <div class="row">
                        <div class="mb-3 col-md-6">                             
                            <label for="prodTieneICE" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Tiene ICE</label>
                            <select  v-model="newProducto.prodTieneICE"  class="form-select border" id="prodTieneICE">
                                <option value="1">SI</option>
                                <option value="0">NO</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-6">                             
                            <label for="prodIcePorcentajeId" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> ICE</label>
                            <select id="selectImpIce" v-model="newProducto.prodIcePorcentajeId"  class="form-select border" id="prodIcePorcentajeId">
                                <option v-for="lii of listaImpuestosICE" v-bind:value="lii.id">{{lii.impt_detalle}} {{lii.impt_porcentage}}%</option>
                            </select>
                        </div>

                    </div>
                </div>

                <hr>
                <div class="col-md-12">
                    <div class="row">
                        <div class="mb-3 col-md-6">                             
                            <label for="prodCtaCompras" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> <span style="color: red; font-size:15px ">*</span> Cta. Contable Compras</label>
                            <select v-model="newProducto.prodCtaCompras"  class="form-select border" id="prodCtaCompras">
                                <option v-for="lcc of listaCtaContable" v-bind:value="lcc.ctad_codigo">{{lcc.cuentadet}}</option>
                            </select>
                            <div class="text-danger" v-html="formValidacion.prodCtaCompras"></div>
                        </div>
                        <div class="mb-3 col-md-6">                             
                            <label for="prodCtaVentas" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> <span style="color: red; font-size:15px ">*</span> Cta. contable Ventas</label>
                            <select v-model="newProducto.prodCtaVentas"  class="form-select border" id="prodCtaVentas">
                                <option v-for="lcc of listaCtaContable" v-bind:value="lcc.ctad_codigo">{{lcc.cuentadet}}</option>
                            </select>
                            <div class="text-danger" v-html="formValidacion.prodCtaVentas"></div>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="col-md-12 row">

                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input  v-model="newProducto.prodCompra" class="form-check-input" type="checkbox" id="prodCompra">
                            <label class="form-check-label" for="prodCompra">Puede Comprarse</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check form-switch">
                            <input  v-model="newProducto.prodVenta" class="form-check-input" type="checkbox" id="prodVenta">
                            <label class="form-check-label" for="prodVenta">Puede Venderse</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check form-switch">
                            <input  v-model="newProducto.prodIsServicio" class="form-check-input" type="checkbox" id="prodIsServicio">
                            <label class="form-check-label" for="prodIsServicio">Es Servicio</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check form-switch">
                            <input  v-model="newProducto.prodIsGasto" class="form-check-input" type="checkbox" id="prodIsGasto">
                            <label class="form-check-label" for="prodIsGasto">Es Gasto</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check form-switch">
                            <input  v-model="newProducto.prodIsPromo" class="form-check-input" type="checkbox" id="prodIsPromo">
                            <label class="form-check-label" for="prodIsPromo">Es Promoción</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input  v-model="newProducto.prodEspecificaciones" class="form-check-input" type="checkbox" id="prodEspecificaciones">
                            <label class="form-check-label" for="prodEspecificaciones">Tiene Especificaciones</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input  v-model="newProducto.prodIsSuperProducto" class="form-check-input" type="checkbox" id="prodIsSuperProducto">
                            <label class="form-check-label" for="prodIsSuperProducto">Es Superproducto</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input  v-model="newProducto.prodCtrlLote" class="form-check-input" type="checkbox" id="prodCtrlLote">
                            <label class="form-check-label" for="prodCtrlLote">Tiene Control de lote</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input  v-model="newProducto.prodFacturarEnNegativo" class="form-check-input" type="checkbox" id="prodFacturarEnNegativo">
                            <label class="form-check-label" for="prodFacturarEnNegativo">Facturar en Negativo</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check form-switch">
                            <input  v-model="newProducto.prodEstado" class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                            <label class="form-check-label" for="flexSwitchCheckDefault"><strong>Estado</strong></label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button  class="btn btn-primary" @click="saveUpdateProducto()">
                        <span v-if="estadoSave">
                            <span v-if='loading'><i class="loading-spin"></i> Creando...</span>
                            <span v-else ><i class="fas fa-save"></i> Crear</span>
                        </span>
                        <span v-else>
                            <span v-if='loading'><i class="loading-spin"></i> Actualizar</span>
                            <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                        </span>
                    </button>
                    <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-stop"></i> Cancelar</button>
                </div>
            </div>
        </div>
      
    </div>
</div>