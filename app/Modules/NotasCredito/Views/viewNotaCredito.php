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

<style>
    .ndc-section {
        border: 1px solid #d8e2dc;
        border-radius: 6px;
        background: #fff;
        padding: 14px;
        margin-bottom: 14px;
    }

    .ndc-section-title {
        color: #0f766e;
        font-weight: 800;
        margin-bottom: 12px;
        text-transform: uppercase;
        font-size: 13px;
    }

    .ndc-label {
        font-weight: 700;
        color: #52616b;
        font-size: 12px;
        margin-bottom: 2px;
    }

    .ndc-value {
        border: 1px solid #dce3e8;
        border-radius: 4px;
        padding: 7px 9px;
        background: #f8fafb;
        min-height: 36px;
    }
</style>

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
            <div class="ndc-section">
                <div class="ndc-section-title">Factura relacionada</div>
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
            </div>

            <div class="ndc-section">
                <div class="ndc-section-title">Datos del proveedor</div>
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
            </div>

            <div class="ndc-section">
                <div class="ndc-section-title">Datos de la nota de credito</div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">Tipo NDC</span>
                            <select v-model="form.tipoNotaCredito" class="form-select">
                                <option value="">Seleccione</option>
                                <option value="DEVOLUCION">Devolucion</option>
                                <option value="DESCUENTO">Descuento</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">N. NDC</span>
                            <input
                                type="text"
                                v-model.trim="form.establecimiento"
                                maxlength="3"
                                class="form-control text-center"
                                placeholder="001">
                            <input
                                type="text"
                                v-model.trim="form.emision"
                                maxlength="3"
                                class="form-control text-center"
                                placeholder="001">
                            <input
                                type="text"
                                v-model.trim="form.numero"
                                maxlength="9"
                                class="form-control text-center"
                                placeholder="000000001">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">Aut. SRI</span>
                            <input
                                type="text"
                                v-model.trim="form.autorizacionSri"
                                class="form-control"
                                placeholder="Autorizacion SRI">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">F. Emision</span>
                            <input type="date" v-model="form.fechaEmision" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">Caduca Aut.</span>
                            <input type="date" v-model="form.fechaCaducidadAutorizacion" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">Observaciones</span>
                            <input type="text" v-model.trim="form.observaciones" class="form-control" placeholder="Observaciones">
                        </div>
                    </div>
                </div>
            </div>

            <div class="ndc-section">
                <div class="ndc-section-title">Items de la factura</div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center border rounded overflow-visible">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-box me-2"></i> Producto
                            </span>
                            <vue-select
                                class="flex-grow-1"
                                :options="itemsDisponibles"
                                label="label"
                                v-model="itemSeleccionado"
                                placeholder="Buscar item de la factura">
                                <template #option="item">
                                    {{ item.prod_codigo }} - {{ item.prod_nombre }} | Disp: {{ Number(item.cantidad_disponible_ndc || 0).toFixed(2) }}
                                </template>
                                <template #selected-option="item">
                                    {{ item.prod_codigo }} - {{ item.prod_nombre }}
                                </template>
                            </vue-select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-system w-100" @click="agregarItem">
                            <i class="fas fa-plus me-1"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="bg-system text-white">
                        <tr>
                            <th style="width: 50px"></th>
                            <th>Codigo</th>
                            <th>Producto / Lote</th>
                            <th class="text-end">Cant. Factura</th>
                            <th class="text-end">Cant. Disponible</th>
                            <th class="text-end">Cant. NDC</th>
                            <th class="text-end">P. Neto</th>
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
                            <td>
                                <strong>{{ item.prod_nombre }}</strong>
                                <small v-if="item.lote" class="d-block text-muted">Lote: {{ item.lote }}</small>
                            </td>
                            <td class="text-end">{{ Number(item.compd_cantidad || 0).toFixed(2) }}</td>
                            <td class="text-end">{{ Number(item.cantidad_disponible_ndc || 0).toFixed(2) }}</td>
                            <td class="text-end">
                                <input
                                    type="number"
                                    min="0.01"
                                    :max="item.cantidad_disponible_ndc"
                                    step="0.01"
                                    v-model.number="item.cantidadNdc"
                                    class="form-control form-control-sm text-end">
                            </td>
                            <td class="text-end">{{ formatToUSD(item.compd_precio_neto) }}</td>
                            <td class="text-end">{{ formatToUSD(totalIvaItem(item)) }}</td>
                            <td class="text-end fw-bold">{{ formatToUSD(totalItem(item)) }}</td>
                        </tr>
                        <tr v-if="!itemsNdc.length">
                            <td colspan="9" class="text-center text-muted py-4">
                                Agregue al menos un item de la factura para la nota de credito.
                            </td>
                        </tr>
                    </tbody>
                    <tfoot v-if="itemsNdc.length">
                        <tr>
                            <th colspan="8" class="text-end">Subtotal</th>
                            <th class="text-end">{{ formatToUSD(totales.subtotal) }}</th>
                        </tr>
                        <tr>
                            <th colspan="8" class="text-end">IVA</th>
                            <th class="text-end">{{ formatToUSD(totales.iva) }}</th>
                        </tr>
                        <tr>
                            <th colspan="8" class="text-end">Total NDC</th>
                            <th class="text-end text-danger">{{ formatToUSD(totales.total) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var compraOriginal = <?= json_encode($compra ?? (object) []) ?>;

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
                itemSeleccionado: null,
                itemsNdc: [],
                form: {
                    tipoNotaCredito: '',
                    establecimiento: '',
                    emision: '',
                    numero: '',
                    autorizacionSri: '',
                    fechaEmision: DateTime.now().toFormat('yyyy-MM-dd'),
                    fechaCaducidadAutorizacion: '',
                    observaciones: ''
                }
            };
        },
        computed: {
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
            totales() {
                return this.itemsNdc.reduce((total, item) => {
                    total.subtotal += this.subtotalItem(item);
                    total.iva += this.totalIvaItem(item);
                    total.total += this.totalItem(item);
                    return total;
                }, {subtotal: 0, iva: 0, total: 0});
            }
        },
        methods: {
            agregarItem() {
                if (!this.itemSeleccionado) {
                    sweet_msg_toast('warning', 'Seleccione un item de la factura.');
                    return;
                }

                this.itemsNdc.push({
                    ...this.itemSeleccionado,
                    cantidadNdc: Number(this.itemSeleccionado.cantidad_disponible_ndc || 0)
                });
                this.itemSeleccionado = null;
            },
            eliminarItem(index) {
                this.itemsNdc.splice(index, 1);
            },
            subtotalItem(item) {
                return Number(item.cantidadNdc || 0) * Number(item.compd_precio_neto || 0);
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

    window.appNotaCredito.mount('#app');
</script>
