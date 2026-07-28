<!DOCTYPE html>
<!--
/**
 * Description of viewNotaCredito
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 15 jul 2026
 * @time 3:03:16 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<link rel="stylesheet" href="<?php echo base_url(); ?>/resources/css/modules/ndc/style.css">

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title text-system">
                <i class="fas fa-file-invoice me-2"></i> Compras / Nota de Credito
            </h5>
            <!--            <button type="button" class="btn btn-outline-secondary btn-sm" @click="volverGestion">
                            <i class="fas fa-arrow-left me-1"></i> Volver
                        </button>-->
        </div>

        <div class="card-body">
            <fieldset>
                <legend>Factura relacionada </legend>
                <div class="row g-3">
                    <div class="col-md-2">
                        <div class="ndc-label">Compra</div>
                        <div class="ndc-value">#{{ zFill(compra.comp_secuencial, 5) }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="ndc-label">Comprobante</div>
                        <div class="ndc-value">{{ numeroComprobante(compra) }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="ndc-label">Fecha emision</div>
                        <div class="ndc-value">{{ compra.comp_fecha_emision }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="ndc-label">Estado</div>
                        <div class="ndc-value">{{ compra.comp_estado }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="ndc-label">Total factura</div>
                        <div class="ndc-value fw-bold">{{ formatToUSD(compra.comp_total) }}</div>
                    </div>

                </div>
            </fieldset>

            <br>
            <fieldset>
                <legend>Datos del proveedor</legend>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="ndc-label">Proveedor</div>
                        <div class="ndc-value">{{ compra.prov_razon_social }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="ndc-label">RUC</div>
                        <div class="ndc-value">{{ compra.prov_ruc }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="ndc-label">Direccion</div>
                        <div class="ndc-value">{{ compra.prov_direccion || '-' }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="ndc-label">Telefono</div>
                        <div class="ndc-value">{{ compra.prov_telefono || '-' }}</div>
                    </div>
                </div>
                <!--</div>-->
            </fieldset>
            <br>
            <fieldset>
                <legend>Datos de la nota de credito</legend>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-file-invoice me-2"></i> Comprobante
                            </span>
                            <input
                                type="text"
                                class="form-control"
                                :value="tipoComprobanteNdcLabel"
                                readonly>
                        </div>
                        <div v-html="formValidacion.compTipoComprobante" class="text-danger"></div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-rotate-left me-2"></i> Tipo NDC
                            </span>
                            <select v-model="form.compTipoNotaCredito" class="form-select">
                                <option value="">Seleccione</option>
                                <option value="DEVOLUCION">DEVOLUCION</option>
                                <option value="DESCUENTO">DESCUENTO</option>
                            </select>
                        </div>
                        <div v-html="formValidacion.compTipoNotaCredito" class="text-danger"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group border">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-receipt me-2"></i> Sustento
                            </span>
                            <vue-select
                                class="flex-grow-1"
                                :options="listaSustentos"
                                label="sus_nombre"
                                v-model="form.compSustento"
                                placeholder="Seleccione un sustento">
                                <template #option="sustento">
                                    {{ sustento.sus_codigo }} - {{ sustento.sus_nombre }}
                                </template>
                                <template #selected-option="sustento">
                                    {{ sustento.sus_codigo }} - {{ sustento.sus_nombre }}
                                </template>
                            </vue-select>
                        </div>
                        <div v-html="formValidacion.compSustento" class="text-danger"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group border">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-tags me-2"></i> Tipo Compra
                            </span>
                            <vue-select
                                class="flex-grow-1"
                                :options="listaTiposCompra"
                                label="tc_nombre"
                                v-model="form.compTipoCompra"
                                placeholder="Seleccione un tipo de compra">
                                <template #option="tipo">
                                    {{ tipo.tc_codigo }} - {{ tipo.tc_nombre }}
                                </template>
                                <template #selected-option="tipo">
                                    {{ tipo.tc_codigo }} - {{ tipo.tc_nombre }}
                                </template>
                            </vue-select>
                        </div>
                        <div v-html="formValidacion.compTipoCompra" class="text-danger"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-hashtag me-2"></i> N. NDC
                            </span>
                            <input
                                type="text"
                                v-model.trim="form.compNumeroEstablecimiento"
                                maxlength="3"
                                class="form-control text-center"
                                placeholder="001">
                            <input
                                type="text"
                                v-model.trim="form.compNumeroEmision"
                                maxlength="3"
                                class="form-control text-center"
                                placeholder="001">
                            <input
                                type="text"
                                v-model.trim="form.compNumeroComprobante"
                                maxlength="9"
                                class="form-control text-center"
                                placeholder="000000001">
                        </div>
                        <div v-html="formValidacion.compNumeroComprobante" class="text-danger"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-key me-2"></i> Aut. SRI
                            </span>
                            <input
                                type="text"
                                v-model.trim="form.compAutSRI"
                                class="form-control"
                                placeholder="Autorizacion SRI">
                        </div>
                        <div v-html="formValidacion.compAutSRI" class="text-danger"></div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-calendar me-2"></i> F. Emision
                            </span>
                            <input type="date" v-model="form.compFechaEmision" class="form-control">
                        </div>
                        <div v-html="formValidacion.compFechaEmision" class="text-danger"></div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-calendar-times me-2"></i> Caduca Aut.
                            </span>
                            <input type="date" v-model="form.compFechaCaducidadAutorizacion" class="form-control">
                        </div>
                        <div v-html="formValidacion.compFechaCaducidadAutorizacion" class="text-danger"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-comment-dots me-2"></i> Observaciones
                            </span>
                            <input type="text" v-model.trim="form.compObservacion" class="form-control" placeholder="Observaciones">
                        </div>
                    </div>
                </div>
            </fieldset>

            <br>
            <fieldset>
                <legend>Items de la factura</legend>
                <!--                <div class="ndc-section">
                                    <div class="ndc-section-title">Items de la factura</div>-->
                <div v-if="esNotaDescuento" class="row mb-3">
                    <div class="col-md-8">
                        <div class="d-flex gap-2 flex-wrap">
                            <label class="ndc-mode-option" :class="{active: form.tipoDescuentoNdc === 'ITEMS'}">
                                <input type="radio" class="form-check-input me-1" value="ITEMS" v-model="form.tipoDescuentoNdc">
                                Descuento por items
                            </label>
                            <label class="ndc-mode-option" :class="{active: form.tipoDescuentoNdc === 'GLOBAL'}">
                                <input type="radio" class="form-check-input me-1" value="GLOBAL" v-model="form.tipoDescuentoNdc">
                                Descuento global por impuesto
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center border rounded overflow-visible">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-box me-2"></i> Producto
                            </span>
                            <vue-select
                                class="flex-grow-1"
                                :options="itemsBuscadorNdc"
                                label="label"
                                v-model="itemSeleccionado"
                                :disabled="!form.compTipoNotaCredito"
                                :placeholder="placeholderBuscadorItems">
                                <template #option="item">
                                    {{ item.prod_codigo }} - {{ item.prod_nombre }} {{ itemLabelExtra(item) }}
                                </template>
                                <template #selected-option="item">
                                    {{ item.prod_codigo }} - {{ item.prod_nombre }}
                                </template>
                            </vue-select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-system w-100" @click="agregarItem" :disabled="!form.compTipoNotaCredito">
                            <i class="fas fa-plus me-1"></i> Agregar
                        </button>
                    </div>
                </div>
                <!--</div>-->
            </fieldset>
            <br>

            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="bg-system text-white">
                        <tr>
                            <th style="width: 50px"></th>
                            <th>Codigo</th>
                            <th>Producto / Lote</th>
                            <th class="text-end">Cant. Factura</th>
                            <th class="text-end">Costo Factura</th>
                            <th class="text-end">Cant. Disponible</th>
                            <th class="text-end">Cant. NDC</th>
                            <th class="text-end">Costo NDC</th>
                            <th>Cta Contable</th>
                            <th class="text-end">IVA</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in itemsNdc" :key="item.id">
                            <td>
                                <button type="button" class="btn btn-sm btn-danger" @click="eliminarItem(index)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                            <td>{{ item.prod_codigo }}</td>
                            <td class="text-wrap">
                                <strong>{{ item.prod_nombre }}</strong>
                                <small v-if="item.lote" class="d-block text-muted">Lote: {{ item.lote }}</small>
                                <small v-if="item.esDescuentoGlobal" class="d-block text-primary fw-bold">Descuento global por impuesto</small>
                            </td>
                            <td class="text-end">{{ item.esDescuentoGlobal ? '-' : Number(item.compd_cantidad || 0).toFixed(2) }}</td>
                            <td class="text-end">{{ item.esDescuentoGlobal ? '-' : formatToUSD(item.compd_precio_neto) }}</td>
                            <td class="text-end">{{ item.esDescuentoGlobal ? '-' : Number(item.cantidad_disponible_ndc || 0).toFixed(2) }}</td>
                            <td class="text-end">
                                <input
                                    type="text"
                                    v-numbers-only='{decimal:true}'
                                    min="0.01"
                                    :max="item.cantidad_disponible_ndc"
                                    step="0.01"
                                    v-model.number="item.cantidadNdc"
                                    :readonly="!esNotaDevolucion"
                                    size="7"
                                    class="form-control form-control-sm text-end w-auto d-inline-block">
                            </td>
                            <td class="text-end">
                                <input
                                    type="text"
                                    v-numbers-only='{decimal:true}'
                                    min="0.01"
                                    step="0.000001"
                                    v-model.number="item.precioNdc"
                                    :readonly="!permiteEditarPrecio"
                                    size="8"
                                    class="form-control form-control-sm text-end w-auto d-inline-block">
                            </td>
                            <td>
                                <span class="badge text-bg-light border text-dark text-wrap text-start fw-semibold">
                                    {{ cuentaContableLabel(item) }}
                                </span>
                            </td>
                            <td class="text-end">{{ formatToUSD(totalIvaItem(item)) }}</td>
                            <td class="text-end fw-bold">{{ formatToUSD(totalItem(item)) }}</td>
                        </tr>
                        <tr v-if="!itemsNdc.length">
                            <td colspan="11" class="text-center text-muted py-4">
                                Agregue al menos un item de la factura para la nota de credito.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="itemsNdc.length" class="ndc-total-wrapper mt-3">
                <div class="ndc-total-box">
                    <table class="table-bordered table-sm">
                        <tbody>
                            <tr>
                                <td class="text-end fw-bold text-muted">Subtotal factura items</td>
                                <td class="text-end">{{ formatToUSD(totales.subtotalFacturaItems) }}</td>
                                <td class="text-end fw-bold text-muted">Base tarifa 0%</td>
                                <td class="text-end">{{ formatToUSD(baseTarifaCero) }}</td>
                            </tr>
                            <tr>
                                <td class="text-end fw-bold text-muted">Subtotal NDC</td>
                                <td class="text-end">{{ formatToUSD(totales.subtotal) }}</td>
                                <td class="text-end fw-bold text-muted">IVA tarifa 0%</td>
                                <td class="text-end">{{ formatToUSD(0) }}</td>
                            </tr>
                        <template v-for="base in basesIvaNdc" :key="base.porcentaje">
                            <tr>
                                <td class="text-end fw-bold text-muted">IVA NDC</td>
                                <td class="text-end">{{ formatToUSD(totales.iva) }}</td>
                                <td class="text-end fw-bold text-muted">Base imponible IVA {{ Number(base.porcentaje).toFixed(0) }}%</td>
                                <td class="text-end">{{ formatToUSD(base.base) }}</td>
                            </tr>
                            <tr>
                                <td class="text-end fw-bold text-muted">Tipo NDC</td>
                                <td class="text-end">{{ form.compTipoNotaCredito || 'SIN DEFINIR' }}</td>
                                <td class="text-end fw-bold text-muted">Monto IVA {{ Number(base.porcentaje).toFixed(0) }}%</td>
                                <td class="text-end">{{ formatToUSD(base.iva) }}</td>
                            </tr>
                        </template>
                        <tr>
                            <td class="text-end fw-bold text-muted">Total factura items</td>
                            <td class="text-end">{{ formatToUSD(totales.totalFacturaItems) }}</td>
                            <td class="text-end fw-bold text-muted">Otros impuestos</td>
                            <td class="text-end">{{ formatToUSD(0) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="3" class="text-end">TOTAL NDC</td>
                            <td class="text-end text-danger">{{ formatToUSD(totales.total) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="text-end mt-3">
                <button type="button" class="btn btn-danger me-2" :disabled="loadingGuardarNdc" @click="volverGestion">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" :disabled="loadingGuardarNdc" @click="guardarNotaCredito">
                    <span v-if="loadingGuardarNdc">
                        <i class="fas fa-spinner fa-spin me-1"></i> Procesando
                    </span>
                    <span v-else>
                        <i class="fas fa-save me-1"></i> Guardar NDC
                    </span>
                </button>
            </div>
        </div>
    </div>

<div class="modal fade" ref="modalDestinoFinanciero" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-hand-holding-usd me-2"></i> Destino financiero de la NDC
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2">
                    Seleccione como se aplicara el valor de la nota de credito por {{ formatToUSD(totales.total) }}.
                </div>

                <div class="row g-2">
                    <div class="col-12">
                        <label class="form-label fw-bold">Aplicar NDC a</label>
                        <select class="form-select" v-model="formFinanciero.destino">
                            <option value="">Seleccione</option>
                            <option value="CXP">Cuenta por pagar</option>
                            <option value="ANTICIPO_PROVEEDOR">Anticipo a proveedor</option>
                        </select>
                        <div v-html="formValidacion.destinoFinanciero" class="text-danger"></div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Observacion financiera</label>
                        <textarea
                            class="form-control"
                            rows="2"
                            v-model.trim="formFinanciero.observacion"
                            placeholder="Opcional"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" :disabled="loadingGuardarNdc">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" :disabled="loadingGuardarNdc" @click="confirmarGuardarNotaCredito">
                    <span v-if="loadingGuardarNdc">
                        <i class="fas fa-spinner fa-spin me-1"></i> Procesando
                    </span>
                    <span v-else>
                        <i class="fas fa-check me-1"></i> Confirmar
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">
    var compraOriginal = <?= json_encode($compra ?? (object) []) ?>;
    var listaSustentos = <?= json_encode($listaSustentos ?? []) ?>;
    var listaTiposCompra = <?= json_encode($listaTiposCompra ?? []) ?>;
    var tipoComprobanteNdc = <?= json_encode($tipoComprobanteNdc ?? (object) []) ?>;
    var listaProductosDescuento = <?= json_encode($listaProductosDescuento ?? []) ?>;
    
    var fechaActual = DateTime.now().toFormat('yyyy-MM-dd');

    if (window.appNotaCredito) {
        window.appNotaCredito.unmount();
    }

    window.appNotaCredito = Vue.createApp({
        components: {
            "vue-select": window['vue-select']
        },
        data() {
            return {
                url: siteUrl,
                compra: compraOriginal,
                listaSustentos: listaSustentos,
                listaTiposCompra: listaTiposCompra,
                tipoComprobanteNdc: tipoComprobanteNdc,
                listaProductosDescuento: listaProductosDescuento,
                itemSeleccionado: null,
                itemsNdc: [],
                modalDestinoFinancieroInstance: null,
                loadingGuardarNdc: false,
                formValidacion: [],
                formFinanciero: {
                    destino: '',
                    observacion: ''
                },
                form: {
                    compTipoComprobante: tipoComprobanteNdc,
                    compTipoNotaCredito: '',
                    compSustento: null,
                    compTipoCompra: null,
                    compNumeroEstablecimiento: '',
                    compNumeroEmision: '',
                    compNumeroComprobante: '',
                    compAutSRI: '',
                    compFechaEmision: fechaActual,
                    compFechaCaducidadAutorizacion: '',
                    compObservacion: '',
                    tipoDescuentoNdc: 'ITEMS'
                }
            };
        },
        created() {
            this.cargarDatosFacturaRelacionada();
        },
        mounted() {
            this.modalDestinoFinancieroInstance = new bootstrap.Modal(this.$refs.modalDestinoFinanciero);
        },
        computed: {
            esNotaDevolucion() {
                return this.form.compTipoNotaCredito === 'DEVOLUCION';
            },
            esNotaDescuento() {
                return this.form.compTipoNotaCredito === 'DESCUENTO';
            },
            permiteEditarPrecio() {
                return this.esNotaDevolucion || this.esNotaDescuento;
            },
            tipoComprobanteNdcLabel() {
                if (!this.tipoComprobanteNdc || !this.tipoComprobanteNdc.comp_codigo) {
                    return '04 - NOTA DE CREDITO';
                }

                return `${this.tipoComprobanteNdc.comp_codigo} - ${this.tipoComprobanteNdc.comp_nombre}`;
            },
            esDescuentoGlobal() {
                return this.esNotaDescuento && this.form.tipoDescuentoNdc === 'GLOBAL';
            },
            placeholderBuscadorItems() {
                if (!this.form.compTipoNotaCredito) {
                    return 'Seleccione primero el tipo de NDC';
                }

                if (this.esDescuentoGlobal) {
                    return 'Seleccione descuento global por impuesto';
                }

                return 'Buscar item de la factura';
            },
            itemsBuscadorNdc() {
                if (!this.form.compTipoNotaCredito) {
                    return [];
                }

                return this.esDescuentoGlobal ? this.itemsDescuentoGlobalDisponibles : this.itemsDisponibles;
            },
            itemsDisponibles() {
                const idsAgregados = this.itemsNdc.map(item => Number(item.id));
                return (this.compra.detalle || [])
                        .filter(item => Number(item.cantidad_disponible_ndc || 0) > 0)
                        .filter(item => !idsAgregados.includes(Number(item.id)))
                        .map(item => ({
                                ...item,
                                label: `${item.prod_codigo} - ${item.prod_nombre}`
                            }));
            },
            itemsDescuentoGlobalDisponibles() {
                const idsAgregados = this.itemsNdc.map(item => String(item.id));
                return this.tarifasFactura.map(tarifa => {
                    let productoDescuento = this.productoDescuentoPorPorcentaje(tarifa.porcentaje);
                    if (!productoDescuento) {
                        return null;
                    }

                    return {
                        id: `GLOBAL-${productoDescuento.id}`,
                        fk_producto: productoDescuento.id,
                        prod_codigo: productoDescuento.prod_codigo,
                        prod_nombre: productoDescuento.prod_nombre,
                        compd_cantidad: 1,
                        cantidad_disponible_ndc: 1,
                        cantidadNdc: 1,
                        compd_precio_neto: 0,
                        precioNdc: 0,
                        compd_cta_entrada: productoDescuento.fk_cuentacontablecompras,
                        ctaContableNdc: productoDescuento.fk_cuentacontablecompras,
                        ctaContableNombreNdc: productoDescuento.ctad_nombre_cuenta || '',
                        compd_impt_codigo: productoDescuento.impt_codigo || tarifa.codigo,
                        compd_impt_porcentaje: Number(productoDescuento.impt_porcentage ?? productoDescuento.prod_ivaporcentage ?? tarifa.porcentaje),
                        fk_impuesto_tarifa: productoDescuento.fk_impuestotarifa || tarifa.fk_impuesto_tarifa,
                        esDescuentoGlobal: true,
                        label: `${productoDescuento.prod_codigo} - ${productoDescuento.prod_nombre}`
                    };
                }).filter(item => item && !idsAgregados.includes(String(item.id)));
            },
            tarifasFactura() {
                let tarifas = {};

                (this.compra.detalle || []).forEach(item => {
                    let porcentaje = Number(item.compd_impt_porcentaje || 0);
                    let codigo = String(item.compd_impt_codigo ?? porcentaje);
                    let key = `${codigo}-${porcentaje}`;

                    if (!tarifas[key]) {
                        tarifas[key] = {
                            codigo: codigo,
                            porcentaje: porcentaje,
                            fk_impuesto_tarifa: item.fk_impuesto_tarifa || null,
                        };
                    }
                });

                return Object.values(tarifas);
            },
            totales() {
                return this.itemsNdc.reduce((total, item) => {
                    total.subtotal += this.subtotalItem(item);
                    total.iva += this.totalIvaItem(item);
                    total.total += this.totalItem(item);
                    total.subtotalFacturaItems += this.subtotalFacturaItem(item);
                    total.ivaFacturaItems += this.ivaFacturaItem(item);
                    total.totalFacturaItems += this.totalFacturaItem(item);
                    return total;
                }, {subtotal: 0, iva: 0, total: 0, subtotalFacturaItems: 0, ivaFacturaItems: 0, totalFacturaItems: 0});
            },
            baseTarifaCero() {
                return this.itemsNdc.reduce((total, item) => {
                    let porcentaje = Number(item.compd_impt_porcentaje || 0);
                    return porcentaje === 0 ? total + this.subtotalItem(item) : total;
                }, 0);
            },
            basesIvaNdc() {
                let bases = {};

                this.itemsNdc.forEach(item => {
                    let porcentaje = Number(item.compd_impt_porcentaje || 0);
                    if (porcentaje <= 0) {
                        return;
                    }

                    if (!bases[porcentaje]) {
                        bases[porcentaje] = {
                            porcentaje: porcentaje,
                            base: 0,
                            iva: 0,
                        };
                    }

                    bases[porcentaje].base += this.subtotalItem(item);
                    bases[porcentaje].iva += this.totalIvaItem(item);
                });

                return Object.values(bases);
            }
        },
        watch: {
            'form.compTipoNotaCredito'(nuevo, anterior) {
                if (anterior && nuevo !== anterior && this.itemsNdc.length) {
                    this.itemsNdc = [];
                    sweet_msg_toast('warning', 'Se limpio el detalle para cambiar el tipo de nota de credito.');
                }

                if (!this.esNotaDescuento) {
                    this.form.tipoDescuentoNdc = 'ITEMS';
                }

                this.itemSeleccionado = null;
                this.normalizarItemsPorTipoNdc();
            },
            'form.tipoDescuentoNdc'(nuevo, anterior) {
                this.itemSeleccionado = null;

                if (this.esNotaDescuento && this.itemsNdc.length) {
                    this.itemsNdc = [];
                    sweet_msg_toast('warning', 'Se limpio el detalle para cambiar la modalidad del descuento.');
                }
            }
        },
        methods: {
            cargarDatosFacturaRelacionada() {
                this.form.compSustento = this.listaSustentos.find(item => String(item.sus_codigo) === String(this.compra.cod_sustento)) || null;
                this.form.compTipoCompra = this.listaTiposCompra.find(item => Number(item.id) === Number(this.compra.fk_tipo_compra)) || null;
            },
            setErrorCampo(campo, mensaje) {
                this.formValidacion[campo] = mensaje ? `<small>${mensaje}</small>` : '';
            },
            limpiarValidaciones() {
                this.formValidacion = [];
            },
            validarNotaCredito() {
                let valido = true;
                this.limpiarValidaciones();

                if (!this.form.compTipoNotaCredito) {
                    this.setErrorCampo('compTipoNotaCredito', 'Seleccione el tipo de nota de credito.');
                    valido = false;
                }

                if (!this.form.compSustento) {
                    this.setErrorCampo('compSustento', 'Seleccione el sustento.');
                    valido = false;
                }

                if (!this.form.compTipoCompra) {
                    this.setErrorCampo('compTipoCompra', 'Seleccione el tipo de compra.');
                    valido = false;
                }

                if (!this.form.compNumeroEstablecimiento || !this.form.compNumeroEmision || !this.form.compNumeroComprobante) {
                    this.setErrorCampo('compNumeroComprobante', 'Ingrese establecimiento, emision y numero de comprobante.');
                    valido = false;
                }

                if (!this.form.compAutSRI) {
                    this.setErrorCampo('compAutSRI', 'Ingrese la autorizacion SRI.');
                    valido = false;
                }

                if (!this.form.compFechaEmision) {
                    this.setErrorCampo('compFechaEmision', 'Ingrese la fecha de emision.');
                    valido = false;
                }

                if (!this.form.compFechaCaducidadAutorizacion) {
                    this.setErrorCampo('compFechaCaducidadAutorizacion', 'Ingrese la fecha de caducidad de autorizacion.');
                    valido = false;
                }

                if (!this.itemsNdc.length) {
                    sweet_msg_toast('warning', 'Agregue al menos un item para la nota de credito.');
                    valido = false;
                }

                for (const item of this.itemsNdc) {
                    let nombreProducto = item.prod_nombre || 'item';
                    let cantidadNdc = Number(item.cantidadNdc || 0);
                    let precioNdc = Number(item.precioNdc || 0);
                    let precioFactura = Number(item.compd_precio_neto || 0);
                    let cantidadDisponible = Number(item.cantidad_disponible_ndc || 0);

                    if (!this.cuentaContableItem(item)) {
                        sweet_msg_toast('warning', `El item ${nombreProducto} no tiene cuenta contable.`);
                        valido = false;
                        break;
                    }

                    if (this.esNotaDevolucion && (cantidadNdc <= 0 || cantidadNdc > cantidadDisponible)) {
                        sweet_msg_toast('warning', `Cantidad invalida para ${nombreProducto}.`);
                        valido = false;
                        break;
                    }

                    if (this.esNotaDescuento && !item.esDescuentoGlobal && precioNdc >= precioFactura) {
                        sweet_msg_toast('warning', `El costo NDC de ${nombreProducto} debe ser menor al costo de factura.`);
                        valido = false;
                        break;
                    }

                    if (precioNdc <= 0) {
                        sweet_msg_toast('warning', `Costo NDC invalido para ${nombreProducto}.`);
                        valido = false;
                        break;
                    }
                }

                if (valido && Number(this.totales.total || 0) <= 0) {
                    sweet_msg_toast('warning', 'El total de la nota de credito debe ser mayor a cero.');
                    valido = false;
                }

                return valido;
            },
            construirDataNotaCredito() {
                const compra = {
                    compraRelacionadaId: Number(this.compra.id),
                    compTipoComprobante: this.tipoComprobanteNdc?.comp_codigo ?? '04',
                    compTipoComprobanteId: this.tipoComprobanteNdc?.id ?? null,
                    compTipoNotaCredito: this.form.compTipoNotaCredito,
                    compTipoDescuentoNdc: this.esNotaDescuento ? this.form.tipoDescuentoNdc : null,
                    compNumeroEstablecimiento: this.form.compNumeroEstablecimiento?.trim(),
                    compNumeroEmision: this.form.compNumeroEmision?.trim(),
                    compNumeroComprobante: this.form.compNumeroComprobante?.trim(),
                    compAutSRI: this.form.compAutSRI?.trim(),
                    compFechaEmision: this.form.compFechaEmision,
                    compFechaCaducidad: this.form.compFechaCaducidadAutorizacion,
                    compProveedor: Number(this.compra.fk_proveedor),
                    compBodega: Number(this.compra.fk_bodega),
                    compSustento: this.form.compSustento?.sus_codigo ?? null,
                    compCentroCosto: this.compra.fk_centro_costo ? Number(this.compra.fk_centro_costo) : null,
                    compTipoCompra: this.form.compTipoCompra?.id ?? null,
                    compTipoCosto: this.compra.tipo_costo ?? null,
                    compObservaciones: this.form.compObservacion?.trim() || '',
                    compTotal: Number(this.totales.total || 0),
                    destinoFinanciero: this.formFinanciero.destino,
                    observacionFinanciera: this.formFinanciero.observacion?.trim() || null
                };

                const detalle = this.itemsNdc.map(item => ({
                        compraDetalleRelacionadaId: item.esDescuentoGlobal ? null : Number(item.id),
                        productoId: Number(item.fk_producto || item.id),
                        codigo: item.prod_codigo,
                        nombre: item.prod_nombre,
                        cantidadFactura: Number(item.compd_cantidad || 0),
                        precioFactura: Number(item.compd_precio_neto || 0),
                        cantidadDisponible: Number(item.cantidad_disponible_ndc || 0),
                        cantidadNdc: Number(item.cantidadNdc || 0),
                        precioNdc: Number(item.precioNdc || 0),
                        subtotalNdc: Number(this.subtotalItem(item) || 0),
                        ivaNdc: Number(this.totalIvaItem(item) || 0),
                        totalNdc: Number(this.totalItem(item) || 0),
                        cuentaContable: this.cuentaContableItem(item),
                        cuentaContableNombre: this.cuentaContableNombreParaItemNdc(item),
                        impuestoTarifaId: item.fk_impuesto_tarifa ?? null,
                        impuestoCodigo: item.compd_impt_codigo ?? null,
                        ivaPorcentaje: Number(item.compd_impt_porcentaje || 0),
                        loteId: item.fk_lote ? Number(item.fk_lote) : null,
                        lote: item.lote || null,
                        fechaElaboracion: item.fecha_elaboracion || null,
                        fechaCaducidad: item.fecha_caducidad || null,
                        esDescuentoGlobal: Boolean(item.esDescuentoGlobal)
                    }));

                const basesAgrupadas = {};

                this.itemsNdc.forEach(item => {
                    let codigo = String(item.compd_impt_codigo ?? '');
                    let porcentaje = Number(item.compd_impt_porcentaje || 0);
                    let key = `${codigo}-${porcentaje}`;

                    if (!basesAgrupadas[key]) {
                        basesAgrupadas[key] = {
                            fk_impuesto_tarifa: item.fk_impuesto_tarifa ?? null,
                            codigo: codigo,
                            detalle: porcentaje > 0 ? `IVA ${porcentaje.toFixed(0)}%` : 'TARIFA CERO',
                            porcentaje: porcentaje,
                            subtotal_bruto: 0,
                            subtotal_neto: 0,
                            iva: 0
                        };
                    }

                    basesAgrupadas[key].subtotal_bruto += Number(this.subtotalItem(item) || 0);
                    basesAgrupadas[key].subtotal_neto += Number(this.subtotalItem(item) || 0);
                    basesAgrupadas[key].iva += Number(this.totalIvaItem(item) || 0);
                });

                const basesImpuestos = Object.values(basesAgrupadas);

                return {
                    compra,
                    detalle,
                    totales: {
                        subtotal: Number(this.totales.subtotal || 0),
                        iva: Number(this.totales.iva || 0),
                        total: Number(this.totales.total || 0),
                        subtotalFacturaItems: Number(this.totales.subtotalFacturaItems || 0),
                        totalFacturaItems: Number(this.totales.totalFacturaItems || 0)
                    },
                    basesImpuestos
                };
            },
            async guardarNotaCredito() {
                if (!this.validarNotaCredito()) {
                    return;
                }

                this.formFinanciero.destino = '';
                this.formFinanciero.observacion = '';
                this.setErrorCampo('destinoFinanciero', '');
                this.modalDestinoFinancieroInstance.show();
            },
            async confirmarGuardarNotaCredito() {
                this.setErrorCampo('destinoFinanciero', '');

                if (!this.formFinanciero.destino) {
                    this.setErrorCampo('destinoFinanciero', 'Seleccione si la NDC se aplicara a CxP o anticipo a proveedor.');
                    return;
                }

                try {
                    this.loadingGuardarNdc = true;

                    const dataPostNotaCredito = this.construirDataNotaCredito();
                    const {data} = await axios.post(`${this.url}/notacredito/saveNotaCredito`, dataPostNotaCredito);

                    if (data.status === 'success') {
                        this.modalDestinoFinancieroInstance.hide();
                        sweet_msg_dialog('success', data.msg || 'Nota de credito procesada correctamente.');
                    } else if (data.status === 'warning') {
                        sweet_msg_dialog('warning', data.msg || 'Revise los datos de la nota de credito.');
                    } else {
                        sweet_msg_dialog('error', data.msg || 'Error al procesar la nota de credito.');
                    }
                } catch (e) {
                    sweet_msg_dialog('error', 'Error en el sistema al procesar la nota de credito.');
                } finally {
                    this.loadingGuardarNdc = false;
                }
            },
            agregarItem() {
                if (!this.form.compTipoNotaCredito) {
                    sweet_msg_toast('warning', 'Seleccione primero el tipo de NDC.');
                    return;
                }

                if (!this.itemSeleccionado) {
                    sweet_msg_toast('warning', 'Seleccione un item de la factura.');
                    return;
                }

                let cuentaContableNdc = this.cuentaContableParaItemNdc(this.itemSeleccionado);
                if (this.esNotaDescuento && !cuentaContableNdc) {
                    sweet_msg_toast('warning', 'No existe producto de descuento con cuenta contable configurada para el IVA del item seleccionado.');
                    return;
                }

                this.itemsNdc.push({
                    ...this.itemSeleccionado,
                    cantidadNdc: Number(this.itemSeleccionado.cantidadNdc ?? this.itemSeleccionado.cantidad_disponible_ndc ?? 0),
                    precioNdc: Number(this.itemSeleccionado.precioNdc ?? this.itemSeleccionado.compd_precio_neto ?? 0),
                    ctaContableNdc: cuentaContableNdc,
                    ctaContableNombreNdc: this.cuentaContableNombreParaItemNdc(this.itemSeleccionado)
                });
                this.normalizarItemsPorTipoNdc();
                this.itemSeleccionado = null;
            },
            itemLabelExtra(item) {
                if (!item.esDescuentoGlobal) {
                    return `| Disp: ${Number(item.cantidad_disponible_ndc || 0).toFixed(2)}`;
                }

            },
            productoDescuentoPorPorcentaje(porcentaje) {
                return this.listaProductosDescuento.find(producto => {
                    let porcentajeProducto = Number(producto.impt_porcentage ?? producto.prod_ivaporcentage ?? 0);
                    return Math.abs(porcentajeProducto - Number(porcentaje || 0)) < 0.0001;
                }) || null;
            },
            cuentaContableParaItemNdc(item) {
                if (item.esDescuentoGlobal) {
                    return item.ctaContableNdc || item.compd_cta_entrada || '';
                }

                if (this.esNotaDescuento) {
                    let productoDescuento = this.productoDescuentoPorPorcentaje(item.compd_impt_porcentaje);
                    return productoDescuento ? productoDescuento.fk_cuentacontablecompras : '';
                }

                return item.compd_cta_entrada || '';
            },
            cuentaContableNombreParaItemNdc(item) {
                if (item.esDescuentoGlobal) {
                    return item.ctaContableNombreNdc || item.compd_cta_entrada_nombre || '';
                }

                if (this.esNotaDescuento) {
                    let productoDescuento = this.productoDescuentoPorPorcentaje(item.compd_impt_porcentaje);
                    return productoDescuento ? (productoDescuento.ctad_nombre_cuenta || '') : '';
                }

                return item.compd_cta_entrada_nombre || '';
            },
            cuentaContableItem(item) {
                return item.ctaContableNdc || this.cuentaContableParaItemNdc(item);
            },
            cuentaContableLabel(item) {
                let codigo = this.cuentaContableItem(item);
                let nombre = item.ctaContableNombreNdc || this.cuentaContableNombreParaItemNdc(item);

                if (!codigo) {
                    return 'SIN CTA';
                }

                return nombre ? `${codigo} - ${nombre}` : codigo;
            },
            eliminarItem(index) {
                this.itemsNdc.splice(index, 1);
            },
            normalizarItemsPorTipoNdc() {
                this.itemsNdc.forEach(item => {
                    if (this.esDescuentoGlobal) {
                        item.cantidadNdc = 1;
                        item.ctaContableNdc = this.cuentaContableParaItemNdc(item);
                        item.ctaContableNombreNdc = this.cuentaContableNombreParaItemNdc(item);
                        return;
                    }

                    if (this.esNotaDescuento) {
                        item.cantidadNdc = Number(item.cantidad_disponible_ndc || 0);
                    }

                    item.ctaContableNdc = this.cuentaContableParaItemNdc(item);
                    item.ctaContableNombreNdc = this.cuentaContableNombreParaItemNdc(item);

                    if (!Number(item.precioNdc || 0)) {
                        item.precioNdc = Number(item.compd_precio_neto || 0);
                    }
                });
            },
            subtotalFacturaItem(item) {
                if (item.esDescuentoGlobal) {
                    return 0;
                }

                return Number(item.compd_cantidad || 0) * Number(item.compd_precio_neto || 0);
            },
            ivaFacturaItem(item) {
                return this.subtotalFacturaItem(item) * (Number(item.compd_impt_porcentaje || 0) / 100);
            },
            totalFacturaItem(item) {
                return this.subtotalFacturaItem(item) + this.ivaFacturaItem(item);
            },
            subtotalItem(item) {
                if (item.esDescuentoGlobal) {
                    return Number(item.precioNdc || 0);
                }

                if (this.esNotaDescuento) {
                    let diferenciaPrecio = Number(item.compd_precio_neto || 0) - Number(item.precioNdc || 0);
                    return Math.max(0, diferenciaPrecio) * Number(item.cantidadNdc || 0);
                }

                return Number(item.cantidadNdc || 0) * Number(item.precioNdc || 0);
            },
            totalIvaItem(item) {
                return this.subtotalItem(item) * (Number(item.compd_impt_porcentaje || 0) / 100);
            },
            totalItem(item) {
                return this.subtotalItem(item) + this.totalIvaItem(item);
            },
            numeroComprobante(compra) {
                return [
                    compra.comp_numero_establecimiento,
                    compra.comp_numero_emision,
                    compra.comp_numero_comprobante
                ].filter(Boolean).join('-');
            },
            volverGestion() {
                window.location.href = `${this.url}/compras/gestionCompras`;
            },
            formatToUSD(amount) {
                return formatToUSD(amount);
            },
            zFill(value, size) {
                return zFill(value, size);
            }
        }
    });

    window.appNotaCredito.use(AllDirectives);
    window.appNotaCredito.mount('#app');
</script>
