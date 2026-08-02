<!DOCTYPE html>
<!--
/**
 * Description of viewImpuestos
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 30 jul 2026
 * @time 6:01:49 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title text-system mb-0"><i class="fas fa-percent"></i> Impuestos / Tarifas</h5>
            <button class="btn btn-danger btn-sm" @click="clear()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <button class="btn btn-success" @click="guardarTarifa()" :disabled="loading || !estadoSave">
                    <span v-if="loading && estadoSave"><i class="loading-spin"></i> Guardando...</span>
                    <span v-else><i class="fas fa-save"></i> Guardar</span>
                </button>
                <button class="btn btn-primary" @click="nuevo()">
                    <i class="fas fa-plus"></i> Nuevo
                </button>
                <button class="btn btn-system-2" @click="modificarTarifa()" :disabled="loading || estadoSave || !idEdit">
                    <span v-if="loading && !estadoSave"><i class="loading-spin"></i> Modificando...</span>
                    <span v-else><i class="fas fa-pen"></i> Modificar</span>
                </button>
                <button class="btn btn-danger" @click="clear()" :disabled="loading">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>

            <div class="row align-items-start">
                <div class="col-md-7">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Impuesto</label>
                            <vue-select
                                class="border rounded"
                                v-model="newTarifa.fkImpuesto"
                                :options="listaImpuestos"
                                label="imp_nombre"
                                :reduce="impuesto => impuesto.id"
                                placeholder="Seleccione">
                            </vue-select>
                            <div v-html="formValidacion.fkImpuesto" class="text-danger small"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="imptCodigo" class="form-label small fw-bold">Codigo</label>
                            <input v-model="newTarifa.imptCodigo" type="text" class="form-control" id="imptCodigo" placeholder="Ej. 4">
                            <div v-html="formValidacion.imptCodigo" class="text-danger small"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="imptPorcentage" class="form-label small fw-bold">Valor %</label>
                            <input v-model="newTarifa.imptPorcentage" type="number" step="0.0001" class="form-control" id="imptPorcentage" placeholder="0.0000">
                            <div v-html="formValidacion.imptPorcentage" class="text-danger small"></div>
                        </div>
                        <div class="col-md-8">
                            <label for="imptDetalle" class="form-label small fw-bold">Descripcion</label>
                            <input v-model="newTarifa.imptDetalle" type="text" class="form-control" id="imptDetalle" placeholder="Ej. APLICA IVA 15%">
                            <div v-html="formValidacion.imptDetalle" class="text-danger small"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="imptGrupo" class="form-label small fw-bold">Grupo</label>
                            <select v-model="newTarifa.imptGrupo" class="form-select" id="imptGrupo">
                                <option value="">SIN GRUPO</option>
                                <option value="GENERAL">GENERAL</option>
                                <option value="ESPECIAL">ESPECIAL</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="imptFechaInicioVigencia" class="form-label small fw-bold">Fecha Inicio</label>
                            <input v-model="newTarifa.imptFechaInicioVigencia" type="date" class="form-control" id="imptFechaInicioVigencia">
                        </div>
                        <div class="col-md-4">
                            <label for="imptFechaFinVigencia" class="form-label small fw-bold">Fecha Fin</label>
                            <input v-model="newTarifa.imptFechaFinVigencia" type="date" class="form-control" id="imptFechaFinVigencia">
                        </div>
                        <div class="col-md-4">
                            <label for="imptEstado" class="form-label small fw-bold">Estado</label>
                            <select v-model="newTarifa.imptEstado" class="form-select" id="imptEstado">
                                <option value="ACTIVO">ACTIVO</option>
                                <option value="HISTORIAL">HISTORIAL</option>
                            </select>
                            <div v-html="formValidacion.imptEstado" class="text-danger small"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="imptPredeterminado" class="form-label small fw-bold">Predeterminado</label>
                            <select v-model="newTarifa.imptPredeterminado" class="form-select" id="imptPredeterminado">
                                <option value="0">NO</option>
                                <option value="1">SI</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="imptReportIva" class="form-label small fw-bold">Reporte IVA</label>
                            <select v-model="newTarifa.imptReportIva" class="form-select" id="imptReportIva">
                                <option value="0">NO REPORTA</option>
                                <option value="1">BASE TARIFA 0</option>
                                <option value="2">GENERA IVA</option>
                            </select>
                            <div v-html="formValidacion.imptReportIva" class="text-danger small"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="border rounded p-3 h-100 bg-light">
                        <h6 class="fw-bold text-system mb-2"><i class="fas fa-circle-info"></i> Actualizacion masiva</h6>
                        <div class="small text-muted">
                            Para cambiar productos masivamente, use el boton de actualizacion en la fila de la tarifa que sera el nuevo IVA destino.
                        </div>
                        <div class="alert alert-warning small mt-3 mb-0 py-2">
                            <strong>Flujo recomendado:</strong> primero actualice la tarifa de IVA en productos y luego actualice las cuentas contables asociadas.
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive mt-4 border rounded" style="min-height: 430px; overflow-y: auto;">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Impuesto</th>
                            <th>Codigo</th>
                            <th>Porcentaje</th>
                            <th>Descripcion</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Estado</th>
                            <th>Pred.</th>
                            <th style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="tarifa of listaTarifas" :class="{'table-active': Number(idEdit) === Number(tarifa.id)}" @dblclick="loadTarifa(tarifa), estadoSave = false" style="cursor: pointer;">
                            <td>{{ zfill(tarifa.id) }}</td>
                            <td>{{ tarifa.imp_nombre }}</td>
                            <td>{{ tarifa.impt_codigo }}</td>
                            <td>{{ Number(tarifa.impt_porcentage || 0).toFixed(2) }}%</td>
                            <td>{{ tarifa.impt_detalle }}</td>
                            <td>{{ mostrarFecha(tarifa.impt_fecha_inicio_vigencia, '__/__/____') }}</td>
                            <td>{{ mostrarFecha(tarifa.impt_fecha_fin_vigencia, '-') }}</td>
                            <td>
                                <span v-if="tarifa.impt_estado === 'ACTIVO'" class="badge bg-success">ACTIVO</span>
                                <span v-else class="badge bg-secondary">HISTORIAL</span>
                            </td>
                            <td>
                                <span v-if="Number(tarifa.impt_predeterminado) === 1" class="badge bg-primary">SI</span>
                                <span v-else>-</span>
                            </td>
                            <td>
                                <button v-if="permiteActualizacionMasiva(tarifa)"
                                        class="btn btn-warning btn-sm text-white"
                                        title="Cambiar productos a esta tarifa"
                                        @click.stop="abrirModalCambioMasivo(tarifa)">
                                    <i class="fas fa-arrows-rotate"></i>
                                </button>
                                <button v-if="permiteActualizacionMasiva(tarifa)"
                                        class="btn btn-system-2 btn-sm ms-1"
                                        title="Cambiar cuentas contables de productos"
                                        @click.stop="abrirModalCambioCuentas(tarifa)">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </button>
                                <button class="btn btn-info btn-sm text-white ms-1"
                                        title="Configurar cuenta contable por tarifa"
                                        @click.stop="abrirModalCuentaTarifaDesdeTarifa(tarifa)">
                                    <i class="fas fa-link"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="listaTarifas.length === 0">
                            <td colspan="10" class="text-center text-muted py-4">No existen tarifas registradas.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card card-system card-outline mt-3">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title text-system mb-0"><i class="fas fa-file-invoice-dollar"></i> Cuentas contables por tarifa</h5>
            <button class="btn btn-primary btn-sm" @click="abrirModalCuentaTarifa()">
                <i class="fas fa-plus"></i> Nueva configuracion
            </button>
        </div>
        <div class="card-body">
            <div class="alert alert-info small py-2">
                Estas cuentas se usan cuando una tarifa pasa a historico. Permiten que compras y notas de credito respeten la cuenta contable original del IVA, inventario o descuento.
            </div>

            <div class="table-responsive border rounded">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Tarifa</th>
                            <th>Grupo</th>
                            <th>Movimiento</th>
                            <th>Tipo cuenta</th>
                            <th>Cuenta contable</th>
                            <th>Comentario</th>
                            <th>Estado</th>
                            <th style="width: 90px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="cuentaTarifa of listaCuentasTarifaContable">
                            <td>{{ zfill(cuentaTarifa.id) }}</td>
                            <td>
                                <strong>{{ cuentaTarifa.imp_nombre }}</strong> /
                                {{ cuentaTarifa.impt_detalle }} {{ Number(cuentaTarifa.impt_porcentage || 0).toFixed(2) }}%
                                <span v-if="cuentaTarifa.impt_estado === 'HISTORIAL'" class="badge bg-warning text-dark ms-1">HISTORIAL</span>
                                <span v-else class="badge bg-success ms-1">ACTIVO</span>
                            </td>
                            <td>{{ cuentaTarifa.impt_grupo || '-' }}</td>
                            <td><span class="badge bg-primary">{{ cuentaTarifa.tipo_movimiento }}</span></td>
                            <td><span class="badge bg-secondary">{{ cuentaTarifa.tipo_cuenta }}</span></td>
                            <td>{{ cuentaTarifa.fk_cuentacontable_det }} - {{ cuentaTarifa.ctad_nombre_cuenta }}</td>
                            <td>{{ cuentaTarifa.comentario || '-' }}</td>
                            <td>
                                <span v-if="Number(cuentaTarifa.estado) === 1" class="badge bg-success">ACTIVO</span>
                                <span v-else class="badge bg-danger">INACTIVO</span>
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm" @click="abrirModalCuentaTarifa(cuentaTarifa)">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="listaCuentasTarifaContable.length === 0">
                            <td colspan="9" class="text-center text-muted py-4">No existen cuentas configuradas por tarifa.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div ref="modalCuentaTarifaContable" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> {{ idCuentaTarifaEdit ? 'Actualizar' : 'Crear' }} cuenta por tarifa</h5>
                    <button class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Tarifa de impuesto</label>
                            <vue-select
                                class="border rounded"
                                v-model="formCuentaTarifa.fkImpuestoTarifa"
                                :options="tarifasCuentaOptions"
                                label="labelCuenta"
                                :reduce="tarifa => tarifa.id"
                                placeholder="Seleccione tarifa">
                            </vue-select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Movimiento</label>
                            <select v-model="formCuentaTarifa.tipoMovimiento" class="form-select">
                                <option value="COMPRA">COMPRA</option>
                                <option value="VENTA">VENTA</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tipo cuenta</label>
                            <select v-model="formCuentaTarifa.tipoCuenta" class="form-select">
                                <option value="IVA">IVA</option>
                                <option value="INVENTARIO">INVENTARIO</option>
                                <option value="DESCUENTO">DESCUENTO</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Estado</label>
                            <select v-model="formCuentaTarifa.estado" class="form-select">
                                <option value="1">ACTIVO</option>
                                <option value="0">INACTIVO</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Cuenta contable</label>
                            <vue-select
                                class="border rounded"
                                v-model="formCuentaTarifa.fkCuentaContableDet"
                                :options="listaCuentasContable"
                                label="cuentadet"
                                :reduce="cuenta => cuenta.ctad_codigo"
                                placeholder="Seleccione cuenta contable">
                            </vue-select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Comentario</label>
                            <textarea v-model="formCuentaTarifa.comentario" class="form-control" rows="2" maxlength="255" placeholder="Ej. Cuenta historica para IVA 15% en compras"></textarea>
                            <div class="text-muted small text-end">{{ (formCuentaTarifa.comentario || '').length }}/255</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" @click="guardarCuentaTarifaContable()" :disabled="loadingCuentaTarifa">
                        <span v-if="loadingCuentaTarifa"><i class="loading-spin"></i> Guardando...</span>
                        <span v-else><i class="fas fa-save"></i> Guardar</span>
                    </button>
                    <button class="btn btn-danger" data-bs-dismiss="modal" :disabled="loadingCuentaTarifa">
                        <i class="fas fa-stop"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div ref="modalCambioMasivoIva" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="mb-0"><i class="fas fa-arrows-rotate"></i> Cambio masivo de IVA en productos</h5>
                    <button class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-info py-2">
                        Nueva tarifa destino:
                        <strong>{{ tarifaDestino ? `${tarifaDestino.impt_detalle} ${Number(tarifaDestino.impt_porcentage || 0).toFixed(2)}%` : '-' }}</strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tarifa antigua a reemplazar</label>
                        <vue-select
                            class="border rounded"
                            v-model="cambioMasivo.tarifaOrigen"
                            :options="tarifasOrigenCambio"
                            label="labelCambio"
                            :reduce="tarifa => tarifa.id"
                            placeholder="Seleccione la tarifa que sera reemplazada">
                        </vue-select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Metodo de calculo</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="asumirValorIva" value="ASUMIR_VALOR_IVA" v-model="cambioMasivo.metodoCalculo">
                            <label class="form-check-label" for="asumirValorIva">Asumir Valor de IVA</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="calcularBase" value="CALCULAR_BASE" v-model="cambioMasivo.metodoCalculo">
                            <label class="form-check-label" for="calcularBase">Calcular IVA sobre la base imponible</label>
                        </div>
                    </div>

                    <div class="small text-muted">
                        Este proceso cambiara los productos vinculados a la tarifa antigua hacia la tarifa destino seleccionada.
                        Si elige asumir el valor de IVA, se recalcularan los precios base para conservar el precio final.
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-warning text-white fw-bold" @click="confirmarCambioMasivo()" :disabled="loadingCambioMasivo">
                        <span v-if="loadingCambioMasivo"><i class="loading-spin"></i> Aplicando...</span>
                        <span v-else><i class="fas fa-check"></i> Aplicar cambio masivo</span>
                    </button>
                    <button class="btn btn-danger" data-bs-dismiss="modal">
                        <i class="fas fa-stop"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div ref="modalCambioCuentasProductos" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> Cambio masivo de cuentas contables</h5>
                    <button class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2">
                        Productos vinculados a la tarifa:
                        <strong>{{ tarifaCuentas ? `${tarifaCuentas.impt_detalle} ${Number(tarifaCuentas.impt_porcentage || 0).toFixed(2)}%` : '-' }}</strong>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Cta. compras origen</label>
                            <vue-select
                                class="border rounded"
                                v-model="cambioCuentas.cuentaCompraOrigen"
                                :options="listaCuentasContable"
                                label="cuentadet"
                                :reduce="cuenta => cuenta.ctad_codigo"
                                placeholder="Cuenta actual a reemplazar">
                            </vue-select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Cta. compras destino</label>
                            <vue-select
                                class="border rounded"
                                v-model="cambioCuentas.cuentaCompraDestino"
                                :options="listaCuentasContable"
                                label="cuentadet"
                                :reduce="cuenta => cuenta.ctad_codigo"
                                placeholder="Nueva cuenta de compras">
                            </vue-select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Cta. ventas origen</label>
                            <vue-select
                                class="border rounded"
                                v-model="cambioCuentas.cuentaVentaOrigen"
                                :options="listaCuentasContable"
                                label="cuentadet"
                                :reduce="cuenta => cuenta.ctad_codigo"
                                placeholder="Cuenta actual a reemplazar">
                            </vue-select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Cta. ventas destino</label>
                            <vue-select
                                class="border rounded"
                                v-model="cambioCuentas.cuentaVentaDestino"
                                :options="listaCuentasContable"
                                label="cuentadet"
                                :reduce="cuenta => cuenta.ctad_codigo"
                                placeholder="Nueva cuenta de ventas">
                            </vue-select>
                        </div>
                    </div>

                    <div class="small text-muted mt-3">
                        Solo se actualizaran productos activos asociados a esta tarifa de IVA y que actualmente tengan la cuenta origen seleccionada.
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-warning text-white fw-bold" @click="confirmarCambioCuentas()" :disabled="loadingCambioCuentas">
                        <span v-if="loadingCambioCuentas"><i class="loading-spin"></i> Aplicando...</span>
                        <span v-else><i class="fas fa-check"></i> Aplicar cambio de cuentas</span>
                    </button>
                    <button class="btn btn-danger" data-bs-dismiss="modal">
                        <i class="fas fa-stop"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';
    var listaImpuestos = <?php echo json_encode($listaImpuestos); ?>;
    var listaCuentasContable = <?php echo json_encode($listaCuentasContable); ?>;

    if (window.appImpuestosTarifas) {
        window.appImpuestosTarifas.unmount();
    }

    window.appImpuestosTarifas = Vue.createApp({
        components: {
            "vue-select": window['vue-select']
        },
        data() {
            return {
                url: siteUrl,
                admin: admin,
                estadoSave: true,
                idEdit: '',
                loading: false,
                loadingCambioMasivo: false,
                loadingCambioCuentas: false,
                loadingCuentaTarifa: false,
                tarifaDestino: null,
                tarifaCuentas: null,
                idCuentaTarifaEdit: '',
                cambioMasivo: {
                    tarifaOrigen: '',
                    metodoCalculo: 'CALCULAR_BASE'
                },
                cambioCuentas: this.getCambioCuentasEmpty(),
                formCuentaTarifa: this.getCuentaTarifaEmpty(),
                listaImpuestos: listaImpuestos,
                listaCuentasContable: listaCuentasContable,
                listaCuentasTarifaContable: [],
                listaTarifas: [],
                newTarifa: this.getTarifaEmpty(),
                formValidacion: [],
                modalCuentaTarifaContable: null,
                modalCambioCuentasProductos: null,
                modalCambioMasivoIva: null

            };
        },
        computed: {
            tarifasOrigenCambio() {
                if (!this.tarifaDestino) {
                    return [];
                }

                return this.listaTarifas
                        .filter(tarifa => {
                            const mismoImpuesto = Number(tarifa.fk_impuesto) === Number(this.tarifaDestino.fk_impuesto);
                            const distintaTarifa = Number(tarifa.id) !== Number(this.tarifaDestino.id);
                            const mismoGrupo = (tarifa.impt_grupo || '') === (this.tarifaDestino.impt_grupo || '');
                            return mismoImpuesto && distintaTarifa && mismoGrupo;
                        })
                        .map(tarifa => ({
                                ...tarifa,
                                labelCambio: `${tarifa.impt_codigo} - ${tarifa.impt_detalle} ${Number(tarifa.impt_porcentage || 0).toFixed(2)}%`
                            }));
            },
            tarifasCuentaOptions() {
                return this.listaTarifas.map(tarifa => ({
                        ...tarifa,
                        labelCuenta: `${tarifa.imp_nombre} / ${tarifa.impt_codigo} - ${tarifa.impt_detalle} ${Number(tarifa.impt_porcentage || 0).toFixed(2)}% (${tarifa.impt_estado})`
                    }));
            }
        },
        created() {
            this.getTarifas();
            this.getCuentasTarifaContable();
        },
        mounted() {
            this.modalCuentaTarifaContable = new bootstrap.Modal(this.$refs.modalCuentaTarifaContable);
            this.modalCambioCuentasProductos = new bootstrap.Modal(this.$refs.modalCambioCuentasProductos);
            this.modalCambioMasivoIva = new bootstrap.Modal(this.$refs.modalCambioMasivoIva);
        },
        methods: {
            async getTarifas() {
                let {data} = await axios.get(this.url + '/admin/impuestos/getTarifas');
                this.listaTarifas = data || [];
            },
            async getCuentasTarifaContable() {
                let {data} = await axios.get(this.url + '/admin/impuestos/getCuentasTarifaContable');
                this.listaCuentasTarifaContable = data || [];
            },
            loadTarifa(tarifa) {
                this.newTarifa = {
                    fkImpuesto: tarifa.fk_impuesto,
                    imptCodigo: tarifa.impt_codigo,
                    imptPorcentage: tarifa.impt_porcentage,
                    imptDetalle: tarifa.impt_detalle,
                    imptFechaInicioVigencia: this.normalizarFechaVista(tarifa.impt_fecha_inicio_vigencia),
                    imptFechaFinVigencia: this.normalizarFechaVista(tarifa.impt_fecha_fin_vigencia),
                    imptEstado: tarifa.impt_estado,
                    imptPredeterminado: String(tarifa.impt_predeterminado ?? 0),
                    imptReportIva: String(tarifa.impt_report_iva ?? 0),
                    imptGrupo: tarifa.impt_grupo || ''
                };
                this.idEdit = tarifa.id;
                this.formValidacion = [];
            },
            nuevo() {
                this.clear();
            },
            async guardarTarifa() {
                if (!this.estadoSave) {
                    sweet_msg_toast('warning', 'Presione Nuevo para registrar una tarifa nueva.');
                    return;
                }

                await this.enviarTarifa(this.url + '/admin/impuestos/saveTarifa');
            },
            async modificarTarifa() {
                if (!this.idEdit) {
                    sweet_msg_toast('warning', 'Seleccione una tarifa para modificar.');
                    return;
                }

                await this.enviarTarifa(this.url + '/admin/impuestos/updateTarifa', this.idEdit);
            },
            abrirModalCambioMasivo(tarifa) {
                if (!this.permiteActualizacionMasiva(tarifa)) {
                    sweet_msg_toast('warning', 'La actualizacion masiva solo aplica para IVA general o especial mayor a 0%.');
                    return;
                }

                this.tarifaDestino = tarifa;
                this.cambioMasivo = {
                    tarifaOrigen: '',
                    metodoCalculo: 'CALCULAR_BASE'
                };
                this.modalCambioMasivoIva.show();
            },
            abrirModalCambioCuentas(tarifa) {
                if (!this.permiteActualizacionMasiva(tarifa)) {
                    sweet_msg_toast('warning', 'La actualizacion masiva de cuentas solo aplica para IVA general o especial mayor a 0%.');
                    return;
                }

                this.tarifaCuentas = tarifa;
                this.cambioCuentas = this.getCambioCuentasEmpty();
                this.modalCambioCuentasProductos.show();
            },
            abrirModalCuentaTarifa(cuentaTarifa = null) {
                this.idCuentaTarifaEdit = '';
                this.formCuentaTarifa = this.getCuentaTarifaEmpty();

                if (cuentaTarifa) {
                    this.idCuentaTarifaEdit = cuentaTarifa.id;
                    this.formCuentaTarifa = {
                        fkImpuestoTarifa: cuentaTarifa.fk_impuesto_tarifa,
                        tipoMovimiento: cuentaTarifa.tipo_movimiento,
                        tipoCuenta: cuentaTarifa.tipo_cuenta,
                        fkCuentaContableDet: cuentaTarifa.fk_cuentacontable_det,
                        estado: String(cuentaTarifa.estado ?? 1),
                        comentario: cuentaTarifa.comentario || ''
                    };
                }

                this.modalCuentaTarifaContable.show();
            },
            abrirModalCuentaTarifaDesdeTarifa(tarifa) {
                this.idCuentaTarifaEdit = '';
                this.formCuentaTarifa = this.getCuentaTarifaEmpty();
                this.formCuentaTarifa.fkImpuestoTarifa = tarifa.id;
                this.modalCuentaTarifaContable.show();
            },
            permiteActualizacionMasiva(tarifa) {
                const grupo = tarifa.impt_grupo || '';
                const estado = tarifa.impt_estado || '';
                return Number(tarifa.fk_impuesto) === 1
                        && Number(tarifa.impt_porcentage || 0) > 0
                        && ['GENERAL', 'ESPECIAL'].includes(grupo)
                        && estado === 'ACTIVO';
            },
            normalizarFechaVista(fecha) {
                return fecha && fecha !== '0000-00-00' ? fecha : '';
            },
            mostrarFecha(fecha, textoVacio) {
                return fecha && fecha !== '0000-00-00' ? fecha : textoVacio;
            },
            async confirmarCambioMasivo() {
                if (!this.tarifaDestino || !this.cambioMasivo.tarifaOrigen) {
                    sweet_msg_toast('warning', 'Seleccione la tarifa antigua a reemplazar.');
                    return;
                }

                const tarifaOrigen = this.listaTarifas.find(tarifa => Number(tarifa.id) === Number(this.cambioMasivo.tarifaOrigen));

                if (!tarifaOrigen) {
                    sweet_msg_toast('warning', 'No se encontro la tarifa antigua seleccionada.');
                    return;
                }

                const metodo = this.cambioMasivo.metodoCalculo === 'ASUMIR_VALOR_IVA'
                        ? 'Asumir valor de IVA: recalcula precios base para conservar el precio final.'
                        : 'Calcular IVA sobre la base: conserva precios base y cambia el precio final con el nuevo IVA.';

                const confirmacion = await Swal.fire({
                    title: 'Confirmar cambio masivo',
                    html: `
                        <div class="text-start">
                            <p class="mb-2">Se reemplazara la tarifa:</p>
                            <div class="alert alert-warning py-2 mb-2">
                                <strong>${tarifaOrigen.impt_detalle} ${Number(tarifaOrigen.impt_porcentage || 0).toFixed(2)}%</strong>
                            </div>
                            <p class="mb-2">Por la nueva tarifa:</p>
                            <div class="alert alert-info py-2 mb-2">
                                <strong>${this.tarifaDestino.impt_detalle} ${Number(this.tarifaDestino.impt_porcentage || 0).toFixed(2)}%</strong>
                            </div>
                            <p class="mb-0"><strong>Metodo:</strong> ${metodo}</p>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Si, aplicar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#dc3545'
                });

                if (!confirmacion.isConfirmed) {
                    return;
                }

                await this.aplicarCambioMasivo();
            },
            async aplicarCambioMasivo() {
                try {
                    this.loadingCambioMasivo = true;
                    const datos = {
                        tarifaDestinoId: this.tarifaDestino.id,
                        tarifaOrigenId: this.cambioMasivo.tarifaOrigen,
                        metodoCalculo: this.cambioMasivo.metodoCalculo
                    };
                    const {data} = await axios.post(this.url + '/admin/impuestos/aplicarCambioMasivo', datos);

                    if (data.status === 'success') {
                        sweet_msg_dialog('success', data.msg);
                        this.modalCambioMasivoIva.hide();
                        await this.getTarifas();
                        return;
                    }

                    if (data.status === 'warning') {
                        sweet_msg_dialog('warning', data.msg);
                        return;
                    }

                    sweet_msg_dialog('error', data.msg || 'No se pudo aplicar el cambio masivo.');
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loadingCambioMasivo = false;
                }
            },
            async confirmarCambioCuentas() {
                const actualizaCompras = this.cambioCuentas.cuentaCompraOrigen || this.cambioCuentas.cuentaCompraDestino;
                const actualizaVentas = this.cambioCuentas.cuentaVentaOrigen || this.cambioCuentas.cuentaVentaDestino;

                if (!this.tarifaCuentas) {
                    sweet_msg_toast('warning', 'Seleccione la tarifa de IVA.');
                    return;
                }

                if (!actualizaCompras && !actualizaVentas) {
                    sweet_msg_toast('warning', 'Seleccione al menos una cuenta origen y destino.');
                    return;
                }

                if (actualizaCompras && (!this.cambioCuentas.cuentaCompraOrigen || !this.cambioCuentas.cuentaCompraDestino)) {
                    sweet_msg_toast('warning', 'Complete la cuenta origen y destino de compras.');
                    return;
                }

                if (actualizaVentas && (!this.cambioCuentas.cuentaVentaOrigen || !this.cambioCuentas.cuentaVentaDestino)) {
                    sweet_msg_toast('warning', 'Complete la cuenta origen y destino de ventas.');
                    return;
                }

                const confirmacion = await Swal.fire({
                    title: 'Confirmar cambio de cuentas',
                    html: `
                        <div class="text-start">
                            <p class="mb-2">Se actualizaran productos activos vinculados a:</p>
                            <div class="alert alert-info py-2 mb-2">
                                <strong>${this.tarifaCuentas.impt_detalle} ${Number(this.tarifaCuentas.impt_porcentage || 0).toFixed(2)}%</strong>
                            </div>
                            ${actualizaCompras ? `<p class="mb-1"><strong>Compras:</strong> ${this.cambioCuentas.cuentaCompraOrigen} -> ${this.cambioCuentas.cuentaCompraDestino}</p>` : ''}
                            ${actualizaVentas ? `<p class="mb-0"><strong>Ventas:</strong> ${this.cambioCuentas.cuentaVentaOrigen} -> ${this.cambioCuentas.cuentaVentaDestino}</p>` : ''}
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Si, aplicar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#dc3545'
                });

                if (!confirmacion.isConfirmed) {
                    return;
                }

                await this.aplicarCambioCuentas();
            },
            async aplicarCambioCuentas() {
                try {
                    this.loadingCambioCuentas = true;
                    const {data} = await axios.post(this.url + '/admin/impuestos/aplicarCambioMasivoCuentas', {
                        tarifaId: this.tarifaCuentas.id,
                        cuentaCompraOrigen: this.cambioCuentas.cuentaCompraOrigen,
                        cuentaCompraDestino: this.cambioCuentas.cuentaCompraDestino,
                        cuentaVentaOrigen: this.cambioCuentas.cuentaVentaOrigen,
                        cuentaVentaDestino: this.cambioCuentas.cuentaVentaDestino
                    });

                    if (data.status === 'success') {
                        sweet_msg_dialog('success', data.msg);
                        this.modalCambioCuentasProductos.hide();
                        return;
                    }

                    if (data.status === 'warning') {
                        sweet_msg_dialog('warning', data.msg);
                        return;
                    }

                    sweet_msg_dialog('error', data.msg || 'No se pudo aplicar el cambio de cuentas.');
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loadingCambioCuentas = false;
                }
            },
            async guardarCuentaTarifaContable() {
                if (!this.validarCuentaTarifaContable()) {
                    return;
                }

                let datos = this.formData(this.formCuentaTarifa);
                let url = this.url + '/admin/impuestos/saveCuentaTarifaContable';

                if (this.idCuentaTarifaEdit) {
                    datos.append('idCuentaTarifa', this.idCuentaTarifaEdit);
                    url = this.url + '/admin/impuestos/updateCuentaTarifaContable';
                }

                try {
                    this.loadingCuentaTarifa = true;
                    const {data} = await axios.post(url, datos);

                    if (data.status === 'success') {
                        sweet_msg_dialog('success', data.msg);
                        this.modalCuentaTarifaContable.hide();
                        await this.getCuentasTarifaContable();
                        return;
                    }

                    sweet_msg_dialog(data.status || 'warning', data.msg || 'No se pudo guardar la configuracion contable.');
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loadingCuentaTarifa = false;
                }
            },
            validarCuentaTarifaContable() {
                if (!this.formCuentaTarifa.fkImpuestoTarifa) {
                    sweet_msg_toast('warning', 'Seleccione la tarifa de impuesto.');
                    return false;
                }

                if (!this.formCuentaTarifa.tipoMovimiento) {
                    sweet_msg_toast('warning', 'Seleccione el movimiento.');
                    return false;
                }

                if (!this.formCuentaTarifa.tipoCuenta) {
                    sweet_msg_toast('warning', 'Seleccione el tipo de cuenta.');
                    return false;
                }

                if (!this.formCuentaTarifa.fkCuentaContableDet) {
                    sweet_msg_toast('warning', 'Seleccione la cuenta contable.');
                    return false;
                }

                return true;
            },
            async enviarTarifa(url, idTarifa = null) {
                let datos = this.formData(this.newTarifa);

                if (idTarifa) {
                    datos.append('idTarifa', idTarifa);
                }

                try {
                    this.loading = true;
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getTarifas();
                    } else if (response.data.status === 'existe') {
                        sweet_msg_dialog('warning', response.data.msg);
                    } else if (response.data.status === 'vacio') {
                        this.formValidacion = response.data.msg;
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    this.loading = false;
            }
            },
            clear() {
                this.newTarifa = this.getTarifaEmpty();
                this.estadoSave = true;
                this.idEdit = '';
                this.formValidacion = [];
            },
            getTarifaEmpty() {
                return {
                    fkImpuesto: '1',
                    imptCodigo: '',
                    imptPorcentage: '0.0000',
                    imptDetalle: '',
                    imptFechaInicioVigencia: '',
                    imptFechaFinVigencia: '',
                    imptEstado: 'ACTIVO',
                    imptPredeterminado: '0',
                    imptReportIva: '0',
                    imptGrupo: ''
                };
            },
            getCambioCuentasEmpty() {
                return {
                    cuentaCompraOrigen: '',
                    cuentaCompraDestino: '',
                    cuentaVentaOrigen: '',
                    cuentaVentaDestino: ''
                };
            },
            getCuentaTarifaEmpty() {
                return {
                    fkImpuestoTarifa: '',
                    tipoMovimiento: 'COMPRA',
                    tipoCuenta: 'IVA',
                    fkCuentaContableDet: '',
                    estado: '1',
                    comentario: ''
                };
            },
            formData(obj) {
                var formData = new FormData();
                for (var key in obj) {
                    formData.append(key, obj[key]);
                }
                return formData;
            },
            zfill(num) {
                return zFill(num, 3);
            }
        }
    });

    window.appImpuestosTarifas.mount('#app');
</script>
