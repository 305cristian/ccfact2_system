<!DOCTYPE html>
<!--
/**
 * Description of viewCart
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 9 ago 2026
 * @time 12:50:07 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->


<div class="cart">
    <div class="cart-header px-3 py-2 fw-bold d-flex justify-content-between align-items-center">

        <h5> <i class="fas fa-shopping-cart"></i> Artículos a vender</h5>
        <div v-if="loading" class="loading-data">
            <h6 style="font-family: "><i class="loading-spin"></i> Cargando Producto...</h6>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-check">
                    <input v-model="formVenta.venPermitirDuplicados" class="form-check-input" type="checkbox" id="venPermitirDuplicados">
                    <label class="form-check-label small" for="venPermitirDuplicados">Permitir items duplicados</label>
                </div>
            </div>
        </div>

    </div>
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle mb-0">
            <colgroup>
                <col style="width:50px">   <!-- Eliminar -->
                <col style="width:130px">  <!-- Codigo -->
                <col style="width:370px">  <!-- Producto -->
                <col v-if="colLotes" style="width:260px">  <!-- Lote -->
                <col style="width:120px">  <!-- stock -->
                <col style="width:155px">  <!-- Cantidad -->
                <col style="width:155px">  <!-- Precio -->
                <col style="width:150px">  <!-- Descuento -->
                <col style="width:120px">   <!-- IVA -->
                <col style="width:140px">  <!-- Subtotal -->
            </colgroup>

            <thead class="">
                <tr>
                    <th class="text-center"><i class="fas fa-trash"></i></th>
                    <th>Código</th>
                    <th>Producto</th>
                    <th v-if="colLotes">Lote / Fechas</th>
                    <th class="text-end">Stock</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-center">Precio</th>
                    <th class="text-center">Descuento</th>
                    <th class="text-end">IVA</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="emptyCar">
                    <td :colspan="colLotes ? 10 : 9" class="text-center text-muted py-4">
                        <i class="fas fa-search fa-2x d-block mb-2"></i>
                        Busque y agregue productos para iniciar la venta.
                    </td>
                </tr>
                <tr v-for="item in listaCartData" :key="item.rowid">
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger" @click="deleteProduct(item.rowid)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                    <td>
                        <span class="badge bg-cris-system" v-tooltip:top="item.id">{{ item.codigo }}</span>
                    </td>
                    <td>
                        <span
                            v-if="Number(item.ivaPorcent || 0) > 0"
                            class="d-inline-block rounded-circle bg-success me-1"
                            style="width:12px;height:12px"
                            :title="'IVA ' + item.ivaPorcent + '%'">
                        </span>

                        <span
                            v-else-if="Number(item.codigoImpuestoSelect) === 0"
                            class="d-inline-block rounded-circle bg-dark me-1"
                            style="width:12px;height:12px"
                            title="Tarifa 0%">
                        </span>

                        <span
                            v-else-if="Number(item.codigoImpuestoSelect) === 7"
                            class="d-inline-block rounded-circle bg-info me-1"
                            style="width:12px;height:12px"
                            title="Exento IVA">
                        </span>

                        <span
                            v-else-if="Number(item.codigoImpuestoSelect) === 6"
                            class="d-inline-block rounded-circle bg-warning me-1"
                            style="width:12px;height:12px"
                            title="No objeto de impuesto">
                        </span>

                        <strong>{{ item.name }}</strong>
                        <small class="text-muted d-block">{{ item.unidadMedida }}</small>
                    </td>
                    <td v-if="colLotes">
                        <div v-if="String(item.tieneLote) === '1'">
                            <div class="input-group input-group-sm flex-nowrap">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="fas fa-barcode"></i>
                                </span>
                                <select
                                    class="form-select form-select-sm"
                                    v-model="item.idLote"
                                    @change="updateProductCart(item)">
                                    <option disabled value="">Seleccione un lote</option>
                                    <option
                                        v-for="lote in item.lotes"
                                        :key="lote.fk_lote"
                                        :value="lote.fk_lote"
                                        :class="{'text-danger': Number(lote.stockLote || 0) < 5, 'text-success': Number(lote.stockLote || 0) >= 5}">
                                        {{ lote.lote }} - {{ lote.fechaElaboracion }} / {{ lote.fechaCaducidad }} ({{ lote.stockLote }} {{ item.unidadMedida }})
                                    </option>
                                </select>
                            </div>
                        </div>
                        <small v-else class="text-muted fst-italic">Sin control de lote</small>
                    </td>
                    <td class="text-end">{{ Number(item.stock || 0).toFixed(2) }}</td>
                    <td>
                        <div class="quantity-control input-group input-group-sm flex-nowrap">
                            <button class="btn btn-primary btn-sm" @click="item.qty > 1 ? item.qty-- : null; updateProductCart(item)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input
                                v-model.number="item.qty"
                                @change="updateProductCart(item)"
                                type="text"
                                min="1"
                                v-numbers-only="{ decimal: true }"
                                class="form-control form-control-sm text-center"
                                style="min-width: 55px;">
                            <button class="btn btn-primary btn-sm" @click="item.qty++ ; updateProductCart(item)">
                                <i class="fas fa-plus"></i>
                            </button>

                        </div>
                    </td>
                    <td>

                        <div v-if="permitirCambioPrecio">
                            <div class="quantity-control input-group input-group-sm flex-nowrap d-flex align-items-center justify-content-center">
                                <button class="btn btn-primary btn-sm"><i class="fas fa-dollar-circle"></i></button>
                                <input
                                    type="text"
                                    class="form-control form-control-sm text-end fw-bold"
                                    style="min-width: 100px;"
                                    :value="formatToUSD(item.price || 0)"
                                    readonly>
                                <vue-select
                                    append-to-body
                                    class="venta-precio-select"
                                    v-model="item.precioSeleccionado"
                                    :options="item.listaPrecios"
                                    label="nombre"
                                    :clearable="false"
                                    :searchable="false"
                                    @option:selected="cambiarPrecioItem(item)">
                                    <template #option="{ nombre, valor, origen }">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>{{ nombre }}</span>
                                            <small class="ms-2 text-muted">{{ formatToUSD(valor) }}</small>
                                        </div>
                                    </template>
                                    <template #selected-option="{ nombre }">
                                        <span>{{ nombre }}</span>
                                    </template>
                                </vue-select>
                            </div>
                        </div>
                        <div v-else class="d-flex align-items-center justify-content-center">
                            <button class="btn btn-primary btn-sm" ><i class="fas fa-dollar-circle"></i></button>
                            <input
                                type="text"
                                class="form-control form-control-sm text-end fw-bold"
                                style="min-width: 80px;"
                                :value="formatToUSD(item.price || 0)"
                                readonly>

                        </div >
                    </td>
                    <td>
                        <div class="input-group input-group-sm flex-nowrap">
                            <button
                                class="btn"
                                :class="item.tipoDescuento === 'PORCENTAJE' ? 'btn-info' : 'btn-outline-info'"
                                @click="changeTipoDescuento(item, 'PORCENTAJE')"
                                title="Descuento porcentual">
                                <i class="fas fa-percent"></i>
                            </button>
                            <button
                                class="btn"
                                :class="item.tipoDescuento === 'VALOR' ? 'btn-info' : 'btn-outline-info'"
                                @click="changeTipoDescuento(item, 'VALOR')"
                                title="Descuento por valor">
                                <i class="fas fa-dollar-sign"></i>
                            </button>
                            <input
                                type="text"
                                class="form-control form-control-sm text-end"
                                v-model.number="item.descuento"
                                @change="updateProductCart(item)"
                                v-numbers-only="{ decimal: true }"
                                placeholder="0.00">
                        </div>
                        <small class="d-block text-center text-primary" style="font-size:14px; margin-top:1px;">
                            {{ item.discountPercent || 0 }}% &nbsp;|&nbsp; ${{ item.discountValue || 0 }}
                        </small>
                    </td>
                    <td class="text-end">{{ formatToUSD(item.ivaValTotal) }}</td>
                    <td class="text-end fw-bold">{{ formatToUSD(item.total) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div v-if="!emptyCar" class="d-flex justify-content-between mb-4 mr-4">

        <div class="small text-muted mt-2 ml-4">

            <span class="d-inline-block rounded-circle bg-success me-1"
                  style="width:12px;height:12px"></span>
            Aplica IVA

            <span class="d-inline-block rounded-circle bg-dark ms-3 me-1"
                  style="width:12px;height:12px"></span>
            Tarifa 0%

            <span class="d-inline-block rounded-circle bg-info ms-3 me-1"
                  style="width:12px;height:12px"></span>
            Exento IVA

            <span class="d-inline-block rounded-circle bg-warning ms-3 me-1"
                  style="width:12px;height:12px"></span>
            No objeto
        </div>

        <div class="summary-card-table overflow-hidden" style="width:750px;">

            <div class="row g-0">

                <div class="col-6 border-end">

                    <table class="table-bordered table-sm mb-0 w-100">
                        <tbody>

                            <tr>
                                <td class="summary-label text-end"><strong>Subtotal bruto</strong></td>
                                <td class="summary-value text-end">{{ formatToUSD(totales.totalSubtotalBruto) }}</td>
                            </tr>

                            <tr>
                                <td class="summary-label text-end"><strong>Descuento items</strong></td>
                                <td class="summary-value text-end">{{ formatToUSD(totales.totalDescuentoItems) }}</td>
                            </tr>

<!--                            <tr>
                                <td class="summary-label text-end"><strong>Descuento global</strong></td>
                                <td :class="{'text-warning':totales.totalDescuentoGlobal > 0 }" class="summary-value text-end">{{ formatToUSD(totales.totalDescuentoGlobal) }}</td>
                            </tr>-->

                            <tr>
                                <td class="summary-label text-end fw-bold"><strong>Subtotal neto</strong></td>
                                <td class="summary-value text-end fw-bold">{{ formatToUSD(totales.totalSubtotalNeto) }}</td>
                            </tr>

                            <tr>
                                <td class="summary-label text-end"><strong>ICE</strong></td>
                                <td class="summary-value text-end">{{ formatToUSD(totales.totalIce) }}</td>
                            </tr>

                            <tr>
                                <td class="summary-label text-end"><strong>IRBPNR</strong></td>
                                <td class="summary-value text-end">{{ formatToUSD(totales.totalIrbpnr) }}</td>
                            </tr>

                        </tbody>
                    </table>

                </div>

                <div class="col-6">

                    <table class="table-bordered table-sm mb-0 w-100">
                        <tbody>

                            <tr>
                                <td class="summary-label text-end"><strong>Base tarifa 0%</strong></td>
                                <td class="summary-value text-end">{{ formatToUSD(totales.tarifCeroNeto) }}</td>
                            </tr>

                            <tr>
                                <td class="summary-label text-end"><strong>Base exento IVA</strong></td>
                                <td class="summary-value text-end">{{ formatToUSD(totales.tarifExcentoNeto) }}</td>
                            </tr>

                            <tr>
                                <td class="summary-label text-end"><strong>Base no objeto IVA</strong></td>
                                <td class="summary-value text-end">{{ formatToUSD(totales.tarifNoObjetoNeto) }}</td>
                            </tr>

                        <template v-if="basesImpuestoVista.length > 0">
                            <tr v-for="tax in basesImpuestoVista" :key="'base-venta-' + tax.codigo">
                                <td class="summary-label text-end"><strong>Base imponible IVA {{ tax.porcentaje }}%</strong></td>
                                <td class="summary-value text-end">{{ formatToUSD(tax.subtotal_neto) }}</td>
                            </tr>
                        </template>
                        <template v-else>
                            <tr>
                                <td class="summary-label text-end"><strong>Base imponible IVA {{ ivaPrdeterminado }}%</strong></td>
                                <td class="summary-value text-end">$0.00</td>
                            </tr>
                        </template>

                        <template v-if="basesImpuestoVista.length > 0">
                            <tr v-for="tax in basesImpuestoVista" :key="'iva-venta-' + tax.codigo">
                                <td class="summary-label text-end"><strong>Monto IVA {{ tax.porcentaje }}%</strong></td>
                                <td class="summary-value text-end">{{ formatToUSD(tax.iva) }}</td>
                            </tr>
                        </template>
                        <template v-else>
                            <tr>
                                <td class="summary-label text-end"><strong>Monto IVA {{ ivaPrdeterminado }}%</strong></td>
                                <td class="summary-value text-end">$0.00</td>
                            </tr>
                        </template>

<!--                        <tr>
                            <td class="summary-label text-end"><strong>Recargo</strong></td>
                            <td :class="{'text-warning':global.recargo > 0 }" class="summary-value text-end">{{ formatToUSD(global.recargo) }}</td>
                        </tr>
                        <tr>
                            <td class="summary-label text-end"><strong>Servicios Adc</strong></td>
                            <td :class="{'text-warning':global.serviciosAdc > 0 }" class="summary-value text-end">{{ formatToUSD(global.serviciosAdc) }}</td>
                        </tr>-->

                        </tbody>
                    </table>

                </div>

            </div>

            <div class="border-top p-3 bg-light">
                <div class="d-flex justify-content-end align-items-center">
                    <span class="fw-bold fs-4 me-4">TOTAL</span>
                    <span class="fw-bold fs-4">{{ formatToUSD(totales.totalGeneral) }}</span>
                </div>
            </div>

        </div>
    </div>
</div>
