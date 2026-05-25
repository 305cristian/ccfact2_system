<!DOCTYPE html>
<!--
/**
 * Description of viewRetencion
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 24 may 2026
 * @time 9:25:22 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<br>
<fieldset v-if="!emptyCar">
    <legend>Datos de la retención </legend>
    <!-- RETENCION -->
    <div class=" p-3 mb-3">
        <div class="row col-md-12">
<!--            <h6><i class="fas fa-file-invoice-dollar me-2"></i> Retención</h6>
            <hr>-->

            <div class="col-md-6">
                <div class="form-check">
                    <input type="checkbox" class="form-check-inline" v-model="formRetencion.compAplicaRetencion" id="aplica" :disabled="formRetencion.compNoSujetoRetecion">
                    <label for="aplica">Aplica Retención</label>
                </div>
            </div>
            <div class="col-md-6 form-group-custom" v-if="formRetencion.compAplicaRetencion">
                <div class="input-group">
                    <span class="input-group-text bg-cris-system">
                        <i class="fas fa-file-invoice-dollar me-2"></i>
                        Asumir Retención
                    </span>
                    <select v-model="formRetencion.asumirRetencion" class="form-select">
                        <option value="NO_ASUMIR">NO ASUMIR</option>
                        <option value="ASUMIR_RENTA">ASUMIR RENTA</option>
                        <option value="ASUMIR_IVA_RENTA">ASUMIR IVA Y RENTA</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6" v-if="formRetencion.compAplicaRetencion">
                <div class="form-check">
                    <input type="checkbox" class="form-check-inline" v-model="formRetencion.compNoSujetoRetecion" id="nosujetoret">
                    <label for="nosujetoret"> Comprobante no sujeto a retención</label>
                </div>
            </div>

        </div>

        <!-- BLOQUE RETENCION -->
        <div v-if="formRetencion.compAplicaRetencion" class="card p-3 mb-3">

            <div class="row">
                <!-- R. F. IVA -->
                <div  v-if="!formRetencion.compNoSujetoRetecion" class="mb-3"  >
                    <div class="card-header text-system">
                        R. F. IVA
                    </div>
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="border p-3 h-100">
                                    <h6><i class="fas fa-boxes me-2 text-primary"></i> Transferencia de bienes</h6>
                                    <div class="form-group col-md-12 mt-3">
                                        <template v-for="lrb in listaRetencionesIvaBienes">
                                            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                                <input type="radio" class="btn-check" :id="lrb.id" v-bind:value="lrb.id" v-model="retencionBienes" @change="agregarRetencionIva(lrb)"  autocomplete="off">
                                                <label class="btn btn-system" :for="lrb.id"> {{lrb.ret_porcentaje}}%</label>
                                            </div>
                                        </template>
                                    </div>
                                </div>



                            </div>

                            <div class="col-md-6">
                                <div class="border p-3 h-100">
                                    <h6><i class="fas fa-handshake me-2 text-success"></i> Prestación de servicios</h6>
                                    <div class="form-group col-md-12 mt-3">
                                        <template v-for="lrs in listaRetencionesIvaServicios">
                                            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                                <input type="radio" class="btn-check" :id="lrs.id" v-bind:value="lrs.id" v-model="retencionServicios" @change="agregarRetencionIva(lrs)" autocomplete="off">
                                                <label class="btn btn-system" :for="lrs.id"> {{lrs.ret_porcentaje}}%</label>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- R. F. RENTA -->
                <div class=" mb-3">
                    <div class="card-header text-system">
                        R. F. RENTA
                    </div>
                    <div class="card-body">
                        <div class=" row align-items-end">

                            <!-- Número Comprobante -->
                            <div class="col-md-5 form-group-custom">
                                <div class="input-group">
                                    <span class="input-group-text bg-cris-system">
                                        <i class="fas fa-hashtag me-2"></i> N° Comprobante
                                    </span>
                                    <input v-model="formCompra.compNumeroEstablecimiento" type="text" class="form-control" style="flex:1" placeholder="001">
                                    <input v-model="formCompra.compNumeroEmisión" type="text" class="form-control" style="flex:1" placeholder="002">
                                    <input v-model="formCompra.compNumeroComprobante" type="text" class="form-control" style="flex:2" placeholder="653">
                                </div>
                            </div>

                            <!--Fecha de caducidad de comprobante-->
                            <div class="col-md-3 form-group">
                                <div class="input-group">
                                    <span class="input-group-text bg-cris-system">
                                        <i class="fas fa-calendar me-2"></i>F. Emisión
                                    </span>
                                    <input v-model="formCompra.compFechaCaducidad" type="date" class="form-control">
                                </div>
                            </div>

                            <!-- Autorización SRI -->
                            <div class="col-md-4 form-group-custom">
                                <div class="input-group">
                                    <span class="input-group-text bg-cris-system">
                                        <i class="fas fa-key me-2"></i> Aut. SRI
                                    </span>
                                    <input v-model="formCompra.compAutSRI" type="text" class="form-control" placeholder="Ejm. 0123456789">
                                </div>
                            </div>

                            <div class="col-md-10 form-group ">
                                <div class="d-flex align-items-center border rounded ">      
                                    <span class="input-group-text bg-cris-system">
                                        <i class="fas fa-file-invoice-dollar me-2"></i>
                                        Código
                                    </span>
                                    <vue-select
                                        append-to-body
                                        class="flex-grow-1"
                                        :options="listaRetencionesRenta"
                                        label="retencionName"
                                        v-model="retencionRenta"
                                        placeholder="Seleccione una retención"
                                        >                                                  
                                    </vue-select>
                                </div>
                            </div>
                            <div class="col-md-2 form-group">
                                <button class="btn btn-info w-100" @click="agregarRetencionRenta"> <i class="fas fa-plus me-2"></i> Agregar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div v-if="listaRetencionesSeleccionadas.length > 0" class="table-responsive mt-3">

                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="50"></th>
                            <th>Base Imponible</th>
                            <th>Impuesto</th>
                            <th>Cod. Impuesto</th>
                            <th>Porcentaje</th>
                            <th>Valor Retenido</th>
                        </tr>

                    </thead>

                    <tbody>

                        <tr
                            v-for="(ret, index) in listaRetencionesSeleccionadas"
                            :key="index"
                            >
                            <td>
                                <button
                                    class="btn btn-danger btn-sm"
                                    @click="listaRetencionesSeleccionadas.splice(index,1)"
                                    >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                            <td>{{ ret.base }} </td>
                            <td> {{ ret.ret_impuesto }} </td>
                            <td> {{ ret.ret_codigo }}  </td>
                            <td>{{ ret.ret_porcentaje }} % </td>
                            <td> {{ ret.valorRetenido }} </td>
                        </tr>
                    </tbody>

                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="5" class="text-end">  Total Retenido  </td>
                            <td>
                                {{totalValorRetenido()}}
                            </td>

                        </tr>

                    </tfoot>
                </table>

            </div>
        </div>
    </div>
</fieldset>
