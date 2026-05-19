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

<div class="modal fade" id="modalFinalizar" tabindex="-1">
    <div class="modal-dialog modal-lg">
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

                <!-- RETENCION -->
                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" v-model="modal.compAplicaRetencion">
                        <label>Aplica Retención</label>
                    </div>
                </div>

                <!-- BLOQUE RETENCION -->
                <div v-if="modal.compAplicaRetencion" class="card p-3 mb-3">

                    <h6><i class="fas fa-file-invoice-dollar me-2"></i> Retención</h6>

                    <div class="row">

                        <div class="col-md-6">
                            <input v-model="modal.retNumero"
                                   class="form-control"
                                   placeholder="Número retención">
                        </div>

                        <div class="col-md-6">
                            <input v-model="modal.retAutorizacion"
                                   class="form-control"
                                   placeholder="Autorización SRI">
                        </div>

                    </div>

                </div>

                <!-- FORMA DE PAGO -->
                <div class="card p-3 mb-3">

                    <h6><i class="fas fa-credit-card me-2"></i> Pago</h6>

                    <div class="row">

                        <div class="col-md-6">
                            <vue-select 
                                :options="listaFormasPago"
                                label="nombre"
                                v-model="modal.formaPago"
                                placeholder="Forma de pago"/>
                        </div>

                        <div class="col-md-6">
                            <select v-model="modal.tipoPago" class="form-select">
                                <option value="CONTADO">CONTADO</option>
                                <option value="CREDITO">CRÉDITO</option>
                            </select>
                        </div>

                    </div>

                </div>

                <!-- CRÉDITO -->
                <div v-if="modal.tipoPago === 'CREDITO'" class="card p-3">

                    <h6><i class="fas fa-calendar-alt me-2"></i> Crédito</h6>

                    <div class="row">

                        <div class="col-md-4">
                            <input type="number"
                                   v-model="modal.cuotas"
                                   class="form-control"
                                   placeholder="N° Cuotas">
                        </div>

                        <div class="col-md-4">
                            <input type="number"
                                   v-model="modal.dias"
                                   class="form-control"
                                   placeholder="Días crédito">
                        </div>

                        <div class="col-md-4">
                            <button class="btn btn-primary w-100"
                                    @click="generarCuotas">
                                Generar Cuotas
                            </button>
                        </div>

                    </div>

                </div>

                <div v-if="modal.listaCuotas.length" class="mt-3">

                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Valor</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(c, i) in modal.listaCuotas" :key="i">
                                <td>{{ c.numero }}</td>

                                <td>
                                    <input type="date"
                                           v-model="c.fecha"
                                           class="form-control form-control-sm">
                                </td>

                                <td>
                                    <input type="number"
                                           v-model.number="c.valor"
                                           class="form-control form-control-sm">
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-success" @click="guardarCompraCompleta">
                    <i class="fas fa-save me-2"></i> Guardar Todo
                </button>
            </div>

        </div>
    </div>
</div>