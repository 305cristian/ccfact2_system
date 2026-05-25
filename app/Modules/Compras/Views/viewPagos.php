<!DOCTYPE html>
<!--
/**
 * Description of viewPagosRetencion
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 3 may 2026
 * @time 6:04:53 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div class="modal fade" id="modalFinalizar" ref="modalFinalizar" tabindex="-1">
    <div class="modal-dialog modal-xxl">
        <div class="modal-content">

            <div class="modal-header bg-cris-system text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>
                    Finalizar Compra
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- TOTAL -->
                <div class="alert alert-info text-center">
                    <strong>Total Compra: {{ formatToUSD(totalGeneral) }}</strong>
                </div>

                <!-- FORMA DE PAGO -->
                <div class="card ">
                    <div class="p-3">

                        <!-- HEADER -->
                        <div class="bg-light border rounded p-3 mb-3">

                            <div class="row">

                                <!-- IZQUIERDA -->
                                <div class="col-md-8">

                                    <div class="row mb-2">
                                        <div class="col-md-3 text-end fw-bold"><i class="fas fa-user-tie me-1"></i> Proveedor: </div>
                                        <div class="col-md-9">{{ formCompra.compProveedor?.prov_razon_social }} </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-md-3 text-end fw-bold"><i class="fas fa-id-card me-1"></i>Identificación:</div>
                                        <div class="col-md-9">{{ formCompra.compProveedor?.prov_ruc }} </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3 text-end fw-bold"> <i class="fas fa-money-check-alt me-1"></i> Forma pago:</div>
                                        <div class="col-md-9">{{ pagos.tipoPago }} </div>
                                    </div>
                                </div>

                                <!-- DERECHA -->
                                <div class="col-md-4">
                                    <div class="row mb-2">
                                        <div class="col-md-5 text-end fw-bold"><i class="fas fa-calendar me-1"></i> Fecha: </div>
                                        <div class="col-md-7">{{ formCompra.compFechaEmision }} </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-md-5 text-end fw-bold"> <i class="fas fa-hashtag me-1"></i> Número:</div>
                                        <div class="col-md-7">
                                            {{formCompra.compNumeroEstablecimiento}}-
                                            {{formCompra.compNumeroEmision}}-
                                            {{formCompra.compNumeroComprobante}}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-5 text-end fw-bold"><i class="fas fa-dollar-sign me-1 text-success"></i> Total:
                                        </div>
                                        <div class="col-md-7 fw-bold text-success"> $ {{ totalGeneral }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h6><i class="fas fa-credit-card me-2"></i> Pago</h6><hr>
                        <div class="row">
                            <div class="col-md-6 form-group-custom">
                                <div class="input-group">
                                    <span class="input-group-text bg-cris-system"><i class="fas fa-dollar-circle me-2"></i>Forma de Pago</span>
                                    <select v-model="pagos.tipoPago" class="form-select">
                                        <option value="CONTADO">CONTADO</option>
                                        <option value="CREDITO">CRÉDITO</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- CRÉDITO -->
                        <div v-if="pagos.tipoPago === 'CREDITO'" class="p-3">
                            <div class="row">
                                <div class="col-md-3 form-group-custom">
                                    <div class="input-group">
                                        <span class="input-group-text bg-info"> <i class="fas fa-list-ol me-2"></i>Cuotas</span>
                                        <input type="number" v-model="pagos.cuotas"class="form-control" placeholder="N° Cuotas">
                                    </div>
                                </div>
                                <div class="col-md-3 form-group-custom">
                                    <div class="input-group">
                                        <span class="input-group-text bg-info"> <i class="fas fa-calendar-day me-2"></i>Días de crédito</span>
                                        <input type="number" v-model="pagos.dias" class="form-control"  @input="calcularFechaCredito" placeholder="Días crédito">
                                    </div>
                                </div>

                                <div class="col-md-4 form-group-custom">
                                    <div class="input-group">
                                        <span class="input-group-text bg-info"><i class="fas fa-calendar me-2"></i>Fecha vence crédito</span>
                                        <input type="date" v-model="pagos.fechaVenceCredito" class="form-control" placeholder="Días crédito">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <button class="btn btn-primary" @click="generarCuotas">    <i class="fas fa-file-invoice-dollar me-2"></i> Generar Cuotas </button>
                                </div>
                            </div>

                            <div v-if="pagos.listaCuotas.length" class="mt-3">
                                <hr>
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Fecha</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr v-for="(c, i) in pagos.listaCuotas" :key="i">
                                            <td>{{ c.numero }}</td>

                                            <td>
                                                <input type="date" v-model="c.fecha"class="form-control form-control-sm">
                                            </td>

                                            <td>
                                                <input type="number"  v-model.number="c.valor" class="form-control form-control-sm">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>

                        <!-- CONTADO -->
                        <div v-if="pagos.tipoPago === 'CONTADO'" class="p-3">
                            <div class="row">
                                <!-- Forma pago -->
                                <div class="col-md-6 form-group-custom">
                                    <div class="d-flex border rounded overflow-visible">
                                        <span class="input-group-text bg-info"> <i class="fas fa-money-check-alt me-2"></i> Método de pago </span>

                                        <vue-select 
                                            class="flex-grow-1"
                                            :options="listaFormasPago"
                                            label="fp_nombre"
                                            v-model="pagos.formaPago"
                                            @option:selected="changeFormaPago"
                                            placeholder="Seleccione un método de pago"
                                            >
                                        </vue-select>

                                    </div>
                                </div>

                                <!-- Cuenta contable -->
                                <div class="col-md-6 form-group-custom">

                                    <div class="d-flex border rounded overflow-visible">

                                        <span class="input-group-text bg-info">
                                            <i class="fas fa-wallet me-2"></i>
                                            Cuenta contable
                                        </span>

                                        <vue-select 
                                            class="flex-grow-1"
                                            :options="listaCuentasFormaPago"
                                            label="cuenta"
                                            v-model="pagos.cuentaContablePago"
                                            placeholder="Seleccione una cuenta contable"
                                            >
                                        </vue-select>

                                    </div>

                                </div>

                            </div>

                            <!-- ============================= -->
                            <!-- EFECTIVO -->
                            <!-- ============================= -->

                            <div v-if="pagos.formaPago?.cod === '01'"  class="row mt-2"  >
                                <div class="col-md-12 form-group-custom">
                                    <div class="input-group border rounded">
                                        <span class="input-group-text bg-secondary"> <i class="fas fa-sticky-note me-2"></i>  Nota</span>
                                        <textarea
                                            v-model="pagos.nota"
                                            class="form-control"
                                            rows="2"
                                            placeholder="Ingrese una observación..."
                                            ></textarea>
                                    </div>

                                </div>

                            </div>

                            <!-- ============================= -->
                            <!-- TRANSFERENCIA -->
                            <!-- ============================= -->

                            <div v-if="pagos.formaPago?.cod === '02'" class="row mt-2" >

                                <!-- Banco -->
                                <div class="col-md-6 form-group-custom">
                                    <div class="input-group border rounded">
                                        <span class="input-group-text bg-primary">  <i class="fas fa-university me-2"></i>Banco  </span>

                                        <input
                                            v-model="pagos.banco"
                                            type="text"
                                            class="form-control"
                                            placeholder="Banco"
                                            >

                                    </div>
                                </div>

                                <!-- Número -->
                                <div class="col-md-6 form-group-custom">
                                    <div class="input-group border rounded">
                                        <span class="input-group-text bg-primary"> <i class="fas fa-exchange-alt me-2"></i> N° Transferencia </span>

                                        <input
                                            v-model="pagos.numeroTransferencia"
                                            type="text"
                                            class="form-control"
                                            placeholder="Número transferencia"
                                            >

                                    </div>
                                </div>

                                <!-- Fecha -->
                                <div class="col-md-6 form-group-custom">
                                    <div class="input-group border rounded">
                                        <span class="input-group-text bg-primary"> <i class="fas fa-calendar me-2"></i> Fecha transferencia</span>

                                        <input
                                            v-model="pagos.fechaTransferencia"
                                            type="date"
                                            class="form-control"
                                            >

                                    </div>
                                </div>

                                <!-- Nota -->
                                <div class="col-md-6 form-group-custom">
                                    <div class="input-group border rounded">
                                        <span class="input-group-text bg-primary"> <i class="fas fa-sticky-note me-2"></i> Nota</span>

                                        <input
                                            v-model="pagos.nota"
                                            type="text"
                                            class="form-control"
                                            placeholder="Observación"
                                            >

                                    </div>
                                </div>
                            </div>

                            <!-- ============================= -->
                            <!-- CHEQUE -->
                            <!-- ============================= -->

                            <div v-if="pagos.formaPago?.cod === '03'" class="row mt-2" >

                                <!-- Banco -->
                                <div class="col-md-4 form-group-custom">
                                    <div class="input-group border rounded">
                                        <span class="input-group-text bg-warning"> <i class="fas fa-university me-2"></i>Banco</span>

                                        <input
                                            v-model="pagos.banco"
                                            type="text"
                                            class="form-control"
                                            >

                                    </div>
                                </div>

                                <!-- N° Cheque -->
                                <div class="col-md-4 form-group-custom">
                                    <div class="input-group border rounded">
                                        <span class="input-group-text bg-warning"><i class="fas fa-money-check me-2"></i> N° Cheque </span>

                                        <input
                                            v-model="pagos.numeroCheque"
                                            type="text"
                                            class="form-control"
                                            >

                                    </div>
                                </div>

                                <!-- Fecha -->
                                <div class="col-md-4 form-group-custom">
                                    <div class="input-group border rounded">
                                        <span class="input-group-text bg-warning"> <i class="fas fa-calendar-alt me-2"></i>  Fecha cheque </span>

                                        <input
                                            v-model="pagos.fechaCheque"
                                            type="date"
                                            class="form-control"
                                            >
                                    </div>
                                </div>
                            </div>

                            <!-- ============================= -->
                            <!-- TARJETA -->
                            <!-- ============================= -->

                            <div v-if="pagos.formaPago?.cod === '04'"class="row mt-2" >

                                <!-- Marca -->
                                <div class="col-md-4 form-group-custom">
                                    <div class="input-group border rounded">
                                        <span class="input-group-text bg-danger"> <i class="fas fa-credit-card me-2"></i> Marca</span>

                                        <select v-model="pagos.marcaTarjeta"class="form-select" >
                                            <option value="">Seleccione</option>
                                            <option value="VISA">VISA</option>
                                            <option value="MASTERCARD">MASTERCARD</option>
                                            <option value="AMEX">AMEX</option>
                                            <option value="DINERS">DINERS</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Lote -->
                                <div class="col-md-4 form-group-custom">
                                    <div class="input-group border rounded">
                                        <span class="input-group-text bg-danger"><i class="fas fa-layer-group me-2"></i> Lote </span>

                                        <input
                                            v-model="pagos.loteTarjeta"
                                            type="text"
                                            class="form-control"
                                            placeholder="Número lote"
                                            >

                                    </div>
                                </div>

                                <!-- Autorización -->
                                <div class="col-md-4 form-group-custom">
                                    <div class="input-group border rounded">
                                        <span class="input-group-text bg-danger"><i class="fas fa-check-circle me-2"></i> Autorización</span>

                                        <input
                                            v-model="pagos.autorizacionTarjeta"
                                            type="text"
                                            class="form-control"
                                            placeholder="Código autorización"
                                            >

                                    </div>
                                </div>

                                <!-- Últimos dígitos -->
                                <div class="col-md-4 form-group-custom">
                                    <div class="input-group border rounded">
                                        <span class="input-group-text bg-danger"><i class="fas fa-hashtag me-2"></i> Últimos 4 dígitos </span>

                                        <input
                                            v-model="pagos.ultimosDigitos"
                                            type="text"
                                            maxlength="4"
                                            class="form-control"
                                            placeholder="0000"
                                            >

                                    </div>
                                </div>

                                <!-- Fecha -->
                                <div class="col-md-4 form-group-custom">
                                    <div class="input-group border rounded">
                                        <span class="input-group-text bg-danger"><i class="fas fa-calendar-alt me-2"></i>Fecha voucher</span>

                                        <input
                                            v-model="pagos.fechaVoucher"
                                            type="date"
                                            class="form-control"
                                            >

                                    </div>
                                </div>

                                <!-- Nota -->
                                <div class="col-md-4 form-group-custom">
                                    <div class="input-group border rounded">
                                        <span class="input-group-text bg-danger"> <i class="fas fa-sticky-note me-2"></i>Nota</span>

                                        <input
                                            v-model="pagos.nota"
                                            type="text"
                                            class="form-control"
                                            placeholder="Observación"
                                            >

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-success" @click="guardarCompra">
                    <i class="fas fa-save me-2"></i> Completar Registro
                </button>
            </div>

        </div>
    </div>
</div>