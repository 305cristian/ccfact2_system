<!DOCTYPE html>
<!--
/**
 * Description of viewModal
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 2 oct 2025
 * @time 4:29:48 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<!-- MODAL RETENCION -->
<div id="modalRetenciones" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 v-if="estadoSave"><i class="fas fa-file-alt"></i> Crear Retención</h5>
                <h5 v-else><i class="fas fa-file-alt"></i> Actualizar Retención</h5>
                <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
            </div>
            <div class="modal-body row">
                <div class="col-md-4 mb-3">
                    <label for="retCodigo" class="form-label"><span style="color: red; font-size:15px ">*</span> Código</label>
                    <input v-model="newRetencion.retCodigo" type="text" class="form-control" id="retCodigo">
                    <div v-html="formValidacion.retCodigo" class="text-danger"></div>
                </div>
                <div class="col-md-8 mb-3">
                    <label for="retNombre" class="form-label"><span style="color: red; font-size:15px ">*</span> Nombre</label>
                    <input v-model="newRetencion.retNombre" type="text" class="form-control" id="retNombre">
                    <div v-html="formValidacion.retNombre" class="text-danger"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="retPorcentaje" class="form-label"><span style="color: red; font-size:15px ">*</span> Porcentaje</label>
                    <input v-model="newRetencion.retPorcentaje" type="number" step="0.01" class="form-control" id="retPorcentaje">
                    <div v-html="formValidacion.retPorcentaje" class="text-danger"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="retCtaCompras" class="form-label"><span style="color: red; font-size:15px ">*</span> Cuenta Compras</label>
                    <vue-multiselect
                        v-model="newRetencion.retCtaCompras" 
                        tag-placeholder="Cuenta no Encontrada"
                        placeholder="Buscar Por Nombre o Código"
                        label="ctad_codigo"
                        track-by="ctad_codigo"
                        :multiple="false"
                        :searchable="true"
                        :options-limit="10"
                        :show-no-results="true"
                        :options="listaSearchCuentasContables"
                        @search-change="searchCuentasContables($event)">

                        <template slot="option" slot-scope="{ option }">
                            <span style="font-size: 12px">{{ option.ctad_codigo+': '}} <strong>{{ option.ctad_nombre_cuenta }} </strong></span>
                        </template>
                    </vue-multiselect>

                    <div v-html="formValidacion.retCtaCompras" class="text-danger"></div>

                </div>
                <div class="col-md-4 mb-3">
                    <label for="retCtaVentas" class="form-label"><span style="color: red; font-size:15px ">*</span> Cuenta Ventas</label>
                    <vue-multiselect
                        v-model="newRetencion.retCtaVentas" 
                        tag-placeholder="Cuenta no Encontrada"
                        placeholder="Buscar Por Nombre o Código"
                        label="ctad_codigo"
                        track-by="ctad_codigo"
                        :multiple="false"
                        :searchable="true"
                        :options-limit="10"
                        :show-no-results="true"
                        :options="listaSearchCuentasContables"
                        @search-change="searchCuentasContables($event)">

                        <template slot="option" slot-scope="{ option }">
                            <span style="font-size: 12px">{{ option.ctad_codigo+': '}} <strong>{{ option.ctad_nombre_cuenta }} </strong></span>
                        </template>
                    </vue-multiselect>

                    <div v-html="formValidacion.retCtaVentas" class="text-danger"></div>

                </div>
                <div class="col-md-4 mb-3">
                    <label for="retImpuesto" class="form-label">Impuesto</label>
                    <select v-model="newRetencion.retImpuesto" class="form-select">
                        <option value="RENTA">RENTA</option>
                        <option value="IVA">IVA</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="retValCompra" class="form-label"><span style="color: red; font-size:15px ">*</span> Valor Compra</label>
                    <input v-model="newRetencion.retValCompra" type="text" class="form-control" id="retValCompra" placeholder="ej: subtotalNeto">
                    <div v-html="formValidacion.retValCompra" class="text-danger"></div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="retValVenta" class="form-label"><span style="color: red; font-size:15px ">*</span> Valor Venta</label>
                    <input v-model="newRetencion.retValVenta" type="text" class="form-control" id="retValVenta" placeholder="ej: subtotalNeto">
                    <div v-html="formValidacion.retValVenta" class="text-danger"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" @click="saveUpdateRetencion()">
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
<!-- CLOSE MODAL RETENCION -->