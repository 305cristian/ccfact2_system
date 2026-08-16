<!DOCTYPE html>
<!--
/**
 * Description of viewPagos
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 16 ago 2026
 * @time 11:05:50 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div class="modal fade" ref="modalFinalizarVenta" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-cris-system text-white">
                <h5 class="modal-title mb-0">
                    <i class="fas fa-cash-register me-2"></i>Finalizar Venta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-info">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <strong>Total venta: {{ formatToUSD(totales.totalGeneral) }}</strong>
                        </div>
                        <div class="col-md-4">
                            <strong>Recibido: {{ formatToUSD(pagos.valorRecibido) }}</strong>
                        </div>
                        <div class="col-md-4">
                            <strong>Cambio: {{ formatToUSD(cambioVenta) }}</strong>
                        </div>
                    </div>
                </div>

                <div class="bg-light border rounded p-3 mb-3">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row mb-2">
                                <div class="col-md-3 text-end fw-bold"><i class="fas fa-user me-1"></i> Cliente:</div>
                                <div class="col-md-9">{{ formVenta.venCliente?.clie_razon_social || '-' }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-3 text-end fw-bold"><i class="fas fa-id-card me-1"></i> CI/RUC:</div>
                                <div class="col-md-9">{{ formVenta.venCliente?.clie_dni || '-' }}</div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 text-end fw-bold"><i class="fas fa-money-check-alt me-1"></i> Tipo:</div>
                                <div class="col-md-9">{{ pagos.tipoPago }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row mb-2">
                                <div class="col-md-5 text-end fw-bold"><i class="fas fa-calendar me-1"></i> Fecha:</div>
                                <div class="col-md-7">{{ formVenta.venFechaEmision }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-5 text-end fw-bold"><i class="fas fa-hashtag me-1"></i> Numero:</div>
                                <div class="col-md-7">
                                    {{ formVenta.venNumeroEstablecimiento }}-{{ formVenta.venNumeroEmision }}-{{ formVenta.venNumeroComprobante }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5 text-end fw-bold"><i class="fas fa-dollar-sign me-1 text-success"></i> Total:</div>
                                <div class="col-md-7 fw-bold text-success">{{ formatToUSD(totales.totalGeneral) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-system"><i class="fas fa-credit-card me-2"></i>Datos de cobro</h6>
                <hr>

                <div class="row">
                    <div class="col-md-4 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system"><i class="fas fa-dollar-circle me-2"></i>Tipo pago</span>
                            <select v-model="pagos.tipoPago" class="form-select" @change="resetPagoDetalle">
                                <option value="CONTADO">CONTADO</option>
                                <option value="CREDITO">CREDITO</option>
                            </select>
                        </div>
                        <small v-if="erroresPago.tipoPago" class="text-danger d-block mt-1">{{ erroresPago.tipoPago }}</small>
                    </div>

                    <div v-if="pagos.tipoPago === 'CONTADO'" class="col-md-4 form-group-custom">
                        <div class="d-flex border rounded overflow-visible">
                            <span class="input-group-text bg-info"><i class="fas fa-money-check-alt me-2"></i>Metodo</span>
                            <vue-select
                                class="flex-grow-1"
                                :options="listaFormasPago"
                                label="fp_nombre"
                                v-model="pagos.formaPago"
                                @option:selected="changeFormaPago"
                                placeholder="Seleccione metodo">
                            </vue-select>
                        </div>
                        <small v-if="erroresPago.formaPago" class="text-danger d-block mt-1">{{ erroresPago.formaPago }}</small>
                    </div>

                    <div v-if="pagos.tipoPago === 'CONTADO'" class="col-md-4 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-success"><i class="fas fa-hand-holding-usd me-2"></i>Recibido</span>
                            <input
                                v-model.number="pagos.valorRecibido"
                                class="form-control text-end fw-bold"
                                v-numbers-only="{ decimal: true }"
                                placeholder="0.00">
                        </div>
                        <small v-if="erroresPago.valorRecibido" class="text-danger d-block mt-1">{{ erroresPago.valorRecibido }}</small>
                    </div>
                </div>

                <div v-if="pagos.tipoPago === 'CONTADO'" class="row mt-2">
                    <div class="col-md-6 form-group-custom">
                        <div class="d-flex border rounded overflow-visible">
                            <span class="input-group-text bg-info"><i class="fas fa-wallet me-2"></i>Cuenta contable</span>
                            <vue-select
                                class="flex-grow-1"
                                :options="listaCuentasFormaPago"
                                label="cuenta"
                                v-model="pagos.cuentaContablePago"
                                placeholder="Seleccione cuenta">
                            </vue-select>
                        </div>
                        <small v-if="erroresPago.cuentaContablePago" class="text-danger d-block mt-1">{{ erroresPago.cuentaContablePago }}</small>
                    </div>

                    <div class="col-md-6 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-secondary"><i class="fas fa-calendar me-2"></i>Fecha cobro</span>
                            <input v-model="pagos.fechaCobro" type="date" class="form-control">
                        </div>
                        <small v-if="erroresPago.fechaCobro" class="text-danger d-block mt-1">{{ erroresPago.fechaCobro }}</small>
                    </div>
                </div>

                <div v-if="pagos.tipoPago === 'CONTADO' && pagos.formaPago?.cod === '01'" class="row mt-2">
                    <div class="col-12 form-group-custom">
                        <div class="input-group border rounded">
                            <span class="input-group-text bg-secondary"><i class="fas fa-sticky-note me-2"></i>Nota</span>
                            <input v-model.trim="pagos.nota" class="form-control" placeholder="Observacion del cobro en efectivo">
                        </div>
                        <small v-if="erroresPago.nota" class="text-danger d-block mt-1">{{ erroresPago.nota }}</small>
                    </div>
                </div>

                <div v-if="pagos.tipoPago === 'CONTADO' && pagos.formaPago?.cod === '02'" class="row mt-2">
                    <div class="col-md-6 form-group-custom">
                        <div class="d-flex border rounded overflow-visible">
                            <span class="input-group-text bg-primary"><i class="fas fa-university me-2"></i>Banco</span>
                            <vue-select
                                class="flex-grow-1"
                                :options="listaBancosSimulados"
                                label="nombre"
                                v-model="pagos.banco"
                                placeholder="Seleccione banco">
                            </vue-select>
                        </div>
                        <small v-if="erroresPago.banco" class="text-danger d-block mt-1">{{ erroresPago.banco }}</small>
                    </div>

                    <div class="col-md-6 form-group-custom">
                        <div class="input-group border rounded">
                            <span class="input-group-text bg-primary"><i class="fas fa-exchange-alt me-2"></i>N. Transferencia</span>
                            <input v-model.trim="pagos.numeroTransferencia" class="form-control" placeholder="Numero transferencia">
                        </div>
                        <small v-if="erroresPago.numeroTransferencia" class="text-danger d-block mt-1">{{ erroresPago.numeroTransferencia }}</small>
                    </div>

                    <div class="col-md-6 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-primary"><i class="fas fa-calendar me-2"></i>Fecha transferencia</span>
                            <input v-model="pagos.fechaTransferencia" type="date" class="form-control">
                        </div>
                        <small v-if="erroresPago.fechaTransferencia" class="text-danger d-block mt-1">{{ erroresPago.fechaTransferencia }}</small>
                    </div>

                    <div class="col-md-6 form-group-custom">
                        <div class="input-group border rounded">
                            <span class="input-group-text bg-primary"><i class="fas fa-sticky-note me-2"></i>Nota</span>
                            <input v-model.trim="pagos.nota" class="form-control" placeholder="Observacion">
                        </div>
                        <small v-if="erroresPago.nota" class="text-danger d-block mt-1">{{ erroresPago.nota }}</small>
                    </div>
                </div>

                <div v-if="pagos.tipoPago === 'CONTADO' && pagos.formaPago?.cod === '03'" class="row mt-2">
                    <div class="col-md-4 form-group-custom">
                        <div class="d-flex border rounded overflow-visible">
                            <span class="input-group-text bg-warning"><i class="fas fa-university me-2"></i>Banco</span>
                            <vue-select
                                class="flex-grow-1"
                                :options="listaBancosSimulados"
                                label="nombre"
                                v-model="pagos.banco"
                                placeholder="Seleccione banco">
                            </vue-select>
                        </div>
                        <small v-if="erroresPago.banco" class="text-danger d-block mt-1">{{ erroresPago.banco }}</small>
                    </div>

                    <div class="col-md-4 form-group-custom">
                        <div class="input-group border rounded">
                            <span class="input-group-text bg-warning"><i class="fas fa-money-check me-2"></i>N. Cheque</span>
                            <input v-model.trim="pagos.numeroCheque" class="form-control" placeholder="Numero cheque">
                        </div>
                        <small v-if="erroresPago.numeroCheque" class="text-danger d-block mt-1">{{ erroresPago.numeroCheque }}</small>
                    </div>

                    <div class="col-md-4 form-group-custom">
                        <div class="input-group border rounded">
                            <span class="input-group-text bg-warning"><i class="fas fa-calendar-alt me-2"></i>Fecha cheque</span>
                            <input v-model="pagos.fechaCheque" type="date" class="form-control">
                        </div>
                        <small v-if="erroresPago.fechaCheque" class="text-danger d-block mt-1">{{ erroresPago.fechaCheque }}</small>
                    </div>
                </div>

                <div v-if="pagos.tipoPago === 'CONTADO' && pagos.formaPago?.cod === '04'" class="row mt-2">
                    <div class="col-md-4 form-group-custom">
                        <div class="input-group border rounded">
                            <span class="input-group-text bg-danger"><i class="fas fa-credit-card me-2"></i>Marca</span>
                            <select v-model="pagos.marcaTarjeta" class="form-select">
                                <option value="">Seleccione</option>
                                <option value="VISA">VISA</option>
                                <option value="MASTERCARD">MASTERCARD</option>
                                <option value="AMEX">AMEX</option>
                                <option value="DINERS">DINERS</option>
                            </select>
                        </div>
                        <small v-if="erroresPago.marcaTarjeta" class="text-danger d-block mt-1">{{ erroresPago.marcaTarjeta }}</small>
                    </div>

                    <div class="col-md-4 form-group-custom">
                        <div class="input-group border rounded">
                            <span class="input-group-text bg-danger"><i class="fas fa-layer-group me-2"></i>Lote</span>
                            <input v-model.trim="pagos.loteTarjeta" class="form-control" placeholder="Numero lote">
                        </div>
                        <small v-if="erroresPago.loteTarjeta" class="text-danger d-block mt-1">{{ erroresPago.loteTarjeta }}</small>
                    </div>

                    <div class="col-md-4 form-group-custom">
                        <div class="input-group border rounded">
                            <span class="input-group-text bg-danger"><i class="fas fa-check-circle me-2"></i>Autorizacion</span>
                            <input v-model.trim="pagos.autorizacionTarjeta" class="form-control" placeholder="Codigo autorizacion">
                        </div>
                        <small v-if="erroresPago.autorizacionTarjeta" class="text-danger d-block mt-1">{{ erroresPago.autorizacionTarjeta }}</small>
                    </div>

                    <div class="col-md-4 form-group-custom">
                        <div class="input-group border rounded">
                            <span class="input-group-text bg-danger"><i class="fas fa-hashtag me-2"></i>Ultimos 4</span>
                            <input v-model.trim="pagos.ultimosDigitos" maxlength="4" class="form-control" placeholder="0000" v-numbers-only="{ decimal: false }">
                        </div>
                        <small v-if="erroresPago.ultimosDigitos" class="text-danger d-block mt-1">{{ erroresPago.ultimosDigitos }}</small>
                    </div>

                    <div class="col-md-4 form-group-custom">
                        <div class="input-group border rounded">
                            <span class="input-group-text bg-danger"><i class="fas fa-calendar-alt me-2"></i>Fecha voucher</span>
                            <input v-model="pagos.fechaVoucher" type="date" class="form-control">
                        </div>
                        <small v-if="erroresPago.fechaVoucher" class="text-danger d-block mt-1">{{ erroresPago.fechaVoucher }}</small>
                    </div>

                    <div class="col-md-4 form-group-custom">
                        <div class="input-group border rounded">
                            <span class="input-group-text bg-danger"><i class="fas fa-sticky-note me-2"></i>Nota</span>
                            <input v-model.trim="pagos.nota" class="form-control" placeholder="Observacion">
                        </div>
                        <small v-if="erroresPago.nota" class="text-danger d-block mt-1">{{ erroresPago.nota }}</small>
                    </div>
                </div>

                <div v-if="pagos.tipoPago === 'CREDITO'" class="row mt-2">
                    <div class="col-md-3 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-info"><i class="fas fa-list-ol me-2"></i>Cuotas</span>
                            <input v-model.number="pagos.cuotas" class="form-control" v-numbers-only="{ decimal: false }" placeholder="Nro. cuotas">
                        </div>
                        <small v-if="erroresPago.cuotas" class="text-danger d-block mt-1">{{ erroresPago.cuotas }}</small>
                    </div>

                    <div class="col-md-3 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-info"><i class="fas fa-calendar-day me-2"></i>Dias credito</span>
                            <input v-model.number="pagos.dias" class="form-control" v-numbers-only="{ decimal: false }" @input="calcularFechaCreditoVenta">
                        </div>
                        <small v-if="erroresPago.dias" class="text-danger d-block mt-1">{{ erroresPago.dias }}</small>
                    </div>

                    <div class="col-md-4 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-info"><i class="fas fa-calendar me-2"></i>Vence credito</span>
                            <input v-model="pagos.fechaVenceCredito" type="date" class="form-control">
                        </div>
                        <small v-if="erroresPago.fechaVenceCredito" class="text-danger d-block mt-1">{{ erroresPago.fechaVenceCredito }}</small>
                    </div>

                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary w-100" @click="generarCuotasVenta">
                            <i class="fas fa-file-invoice-dollar me-1"></i> Generar
                        </button>
                    </div>

                    <div v-if="pagos.listaCuotas.length" class="col-12 mt-3">
                        <table class="table table-sm table-bordered mb-1">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th class="text-end">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(cuota, i) in pagos.listaCuotas" :key="i">
                                    <td>{{ cuota.numero }}</td>
                                    <td>
                                        <input v-model="cuota.fecha" type="date" class="form-control form-control-sm">
                                        <small v-if="erroresPago['cuotaFecha_' + i]" class="text-danger d-block mt-1">{{ erroresPago['cuotaFecha_' + i] }}</small>
                                    </td>
                                    <td>
                                        <input v-model.number="cuota.valor" class="form-control form-control-sm text-end" v-numbers-only="{ decimal: true }">
                                        <small v-if="erroresPago['cuotaValor_' + i]" class="text-danger d-block mt-1">{{ erroresPago['cuotaValor_' + i] }}</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <small v-if="erroresPago.totalCuotas" class="text-danger d-block mt-1">{{ erroresPago.totalCuotas }}</small>
                        <small v-if="erroresPago.listaCuotas" class="text-danger d-block mt-1">{{ erroresPago.listaCuotas }}</small>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" :disabled="loadingProcess" @click="guardarVenta">
                    <span v-if="loadingProcess"><i class="fas fa-spinner fa-spin me-2"></i>Completando</span>
                    <span v-else><i class="fas fa-save me-2"></i>Completar Registro</span>
                </button>
            </div>
        </div>
    </div>
</div>
