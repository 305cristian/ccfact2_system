<!DOCTYPE html>
<!--
/**
 * Description of viewCart
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 3 may 2026
 * @time 5:21:42 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<div class="cart">          
    <div class="cart-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-shopping-cart me-2"></i>Artículos a camprar</h5>
        <div v-if="loading" class="loading-data">
            <h6 style="font-family: "><i class="loading-spin"></i> Cargando Producto...</h6>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" v-model="formCompra.compPermitirDuplicados">
                    <label class="form-check-label" for="permitirDuplicados">
                        Permitir items duplicados
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid p-0">
        <div v-if="emptyCar" class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No hay productos agregados</p>
            <p style="font-size: 0.9rem; color: #d1d5db;">Utiliza la búsqueda de arriba para agregar productos</p>
        </div>
        <div v-else class="table-responsive">

            <table class="table table-stripped table-hover align-middle w-100">
                <colgroup>
                    <col style="width:110px">   <!-- Eliminar -->
                    <col style="width:125px">   <!-- Cod Prov -->
                    <col style="width:130px">  <!-- Codigo -->
                    <col style="width:370px">  <!-- Producto -->
                    <col v-if="colLotes" style="width:300px">  <!-- Lote -->
                    <col style="width:100px">   <!-- IVA -->
                    <col style="width:180px">  <!-- Cuenta -->
                    <col style="width:310px">  <!-- Cantidad -->
                    <col style="width:170px">  <!-- Descuento -->
                    <col style="width:80px">   <!-- ICE -->
                    <col style="width:80px">   <!-- IVA -->
                    <col style="width:100px">  <!-- Subtotal -->
                    <col style="width:100px">  <!-- Centro costo -->
                </colgroup>
                <thead class="cart-header">
                    <tr>
                        <th class="text-center" style="width: 100px;"><i  class="fas fa-trash"></i></th>
                        <th>C.P.</th>
                        <th>Codigo</th>
                        <th>Producto</th>
                        <th v-if="colLotes">Lote (Fechas)</th>
                        <th>IVA %</th>
                        <th>Cta. Contable</th>
                        <th class="text-center">Cantidad / Precio</th>
                        <th class="text-center">Descuento</th>
                        <th class="text-center">ICE</th>
                        <th class="text-center">IVA</th>
                        <th class="text-center">Subtotal</th>
                        <th class="text-center">C.c.</th>
                    </tr>
                </thead>

                <tbody>

                    <tr v-for="(item, index) in listaCartData" :key="index">

                        <!--CONTADOR Y DELETE PROD-->
                        <td>
                            <strong>
                                {{ index + 1 }} <i class="fas fa-angle-right"></i> 
                            </strong>
                            <button class="btn btn-danger btn-sm"
                                    title="Eliminar"
                                    @click="deleteProduct(item.rowid)"
                                    :disabled="loading">
                                <span><i class="fas fa-trash"></i></span>
                            </button>
                        </td>

                        <!--CODIGO DE PROVEEDOR-->
                        <td class="cart-product-link-cell" style="min-width: 125px;">
                            <vue-select 
                                class="w-100 cart-product-link-select"
                                v-model="item.productoVinculado"
                                :options="item.listaProductosVincular || []"
                                label="prod_nombre"
                                :placeholder="item.codigoImport || 'C.P.'"
                                @search="search => searchProductosVincular(search, item)"
                                @option:selected="producto => reemplazarProductoImportado(item, producto)">

                                <template #selected-option="{ prod_nombre }">
                                    <span>{{ item.codigoImport }}</span>
                                </template>

                                <template #option="option">
                                    <div class="producto-option-row d-flex align-items-center gap-2 flex-nowrap">
                                        <span class="badge bg-primary flex-shrink-0">{{ option.codigos }}</span>
                                        <span class="fw-bold text-dark text-truncate">{{ option.prod_nombre }}</span>
                                    </div>
                                </template>
                            </vue-select>
                        </td>

                        <!--CODIGO DE PRODUCTO-->
                        <td>
                            <span class="badge bg-cris-system text-wrap w-100 d-inline-block text-start"
                                  style="font-size: 12px;  white-space: normal; line-height: 1.2; "
                                  v-tooltip:top=" item.id ">{{ item.codigo }}</span>
                        </td>

                        <!--ITEM-->
                        <td>
                            <span
                                v-if="item.ivaPorcent > 0 "
                                class="d-inline-block rounded-circle bg-success me-1"
                                style="width:12px;height:12px"
                                title="IVA 15%">
                            </span>

                            <span
                                v-else-if="item.codigoImpuestoSelect == 0"
                                class="d-inline-block rounded-circle bg-dark me-1"
                                style="width:12px;height:12px"
                                title="Tarifa 0%">
                            </span>

                            <span
                                v-else-if="item.codigoImpuestoSelect == 7"
                                class="d-inline-block rounded-circle bg-info me-1"
                                style="width:12px;height:12px"
                                title="Exento IVA">
                            </span>

                            <span
                                v-else-if="item.codigoImpuestoSelect == 6"
                                class="d-inline-block rounded-circle bg-warning me-1"
                                style="width:12px;height:12px"
                                title="No objeto de impuesto">
                            </span>


                            <strong>{{ item.name }}</strong>
                            <span class="text-info">
                                <i class="fas fa-angles-right"></i>
                                {{ item.unidadMedida }}
                            </span>

                            <div v-if="Number(item.productoTemporal || 0) === 1">
                                <small class="text-danger">Producto importado pendiente de vincular.</small>
                            </div>

                        </td>

                        <!--DATOS DEL LOTE-->
                        <td v-if="colLotes">
                            <div v-if="item.tieneLote === '1' " class="input-group">
                                <input v-model="item.lote" type="text" class="form-control form-control-sm" @change="updateProductCart(item, true)" style="max-width: 130px;">
                                <input v-model="item.fechaElaboracion" type="date" class="form-control form-control-sm" @change="updateProductCart(item)" style="max-width: 120px;">
                                <input v-model="item.fechaCaducidad" type="date" class="form-control form-control-sm" @change="updateProductCart(item)" style="max-width: 120px;">
                            </div>
                            <div v-else> <p style="font-size: 0.9rem; color: #d1d5db; font-style: italic">Sin lotes</p></div>
                        </td>

                        <!-- IMPUESTO PORCENTAJE-->
                        <td >
                            <select v-model="item.impuestoSelect"  class="form-select form-select-sm border" @change="updateProductCart(item)">
                                <option  v-for="lti of listaImpuestosTarifa" v-bind:value="lti.id"  :key="lti.id" :class="lti.impt_estado === 'HISTORIAL' ? 'text-warning fw-bold' : ''">
                                    {{ lti.impt_detalle }}
                                    {{ lti.impt_porcentage > 0 ? ` ${lti.impt_porcentage}%` : '' }}
                                    {{ lti.impt_estado === 'HISTORIAL' ? ' (HISTORIAL)' : '' }}
                                </option>
                            </select>
                        </td>

                        <!--  CUENTA CONTABLE-->
                        <td>
                            <select class="form-select form-select-sm"
                                    v-model="item.ctaContableProducto"
                                    @change="updateProductCart(item)"
                                    :disabled="esItemIvaHistorico(item)"
                                    :title="esItemIvaHistorico(item) ? 'Cuenta bloqueada por IVA historico' : ''">
                                <option v-for="lcc in listaCuentasContables" v-bind:value="lcc.ctad_codigo">{{lcc.ctad_codigo+' - '}}{{lcc.ctad_nombre_cuenta}}</option>
                            </select>
                        </td>

                        <!--CANTIDAD Y PRECIO-->
                        <td>
                            <div class="quantity-control input-group">
                                <button class="btn btn-primary btn-sm" @click="item.qty > 1 ? item.qty-- : null; updateProductCart(item)"><i class="fas fa-minus"></i></button>
                                <input class="form-control form-control-sm"
                                       v-model.number="item.qty"
                                       type="text"
                                       @change="updateProductCart(item)"
                                       v-numbers-only="{ decimal: true }">
                                <button class="btn btn-primary btn-sm" @click="item.qty++ ; updateProductCart(item)"><i class="fas fa-plus"></i></button>
                                <input type="text"
                                       class="form-control form-control-sm"
                                       style="max-width: 80px;"
                                       v-model.number="item.price"
                                       @change="updateProductCart(item)"
                                       v-numbers-only="{ decimal: true }">
                                <button class="btn btn-primary btn-sm"><i class="fas fa-dollar-circle"></i></button>
                            </div>

                        </td>

                        <!-- DESCUENTO -->
                        <td>
                            <div>
                                <div class="input-group input-group-sm">
                                    <!-- PORCENTAJE -->
                                    <button
                                        class="btn"
                                        :class="
                                        item.tipoDescuento === 'PORCENTAJE'
                                        ? 'btn-info'
                                        : 'btn-outline-info'
                                        "
                                        @click="changeTipoDescuento(item, 'PORCENTAJE')"
                                        title="Descuento porcentual"
                                        >
                                        <i class="fas fa-percent"></i>
                                    </button>

                                    <!-- VALOR -->
                                    <button
                                        class="btn"
                                        :class="
                                        item.tipoDescuento === 'VALOR'
                                        ? 'btn-info'
                                        : 'btn-outline-info'
                                        "
                                        @click="changeTipoDescuento(item, 'VALOR')"
                                        title="Descuento por valor"
                                        >
                                        <i class="fas fa-dollar-sign"></i>
                                    </button>

                                    <!-- INPUT -->
                                    <input
                                        type="text"
                                        class="form-control form-control-sm text-end"
                                        v-model.number="item.descuento"
                                        @change="updateProductCart(item)"
                                        v-numbers-only="{ decimal: true }"
                                        placeholder="0.00">
                                </div>
                                <small
                                    class="d-block text-center text-primary"
                                    style="font-size:14px; margin-top:1px;">
                                    {{ item.discountPercent || 0 }}%  |  ${{ item.discountValue || 0 }}
                                </small>
                            </div>



                        </td>

                        <!--ICE-->
                        <td class="price-cell">{{ formatToUSD(item.iceValUnit) }}</td>

                        <!--IVA VALOR-->
                        <td class="price-cell">{{ formatToUSD(item.ivaValTotal) }}</td>

                        <!--SUBTOTAL-->
                        <td class="price-cell text-primary">{{ formatToUSD(item.itemBaseIvaTotal) }}</td>

                        <td style="max-width: 150px;">
                            <select class="form-select form-select-sm" v-model="item.centroCosto" @change="updateProductCart(item)">
                                <option v-for="cc in listaCentroCostos" v-bind:value="cc.id" :key="cc.id">{{cc.cc_nombre}}</option>
                            </select>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>


    <div   v-if="!emptyCar"  class="d-flex justify-content-between mb-4 mr-4">


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

                <!-- IZQUIERDA -->
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

                            <tr>
                                <td class="summary-label text-end"><strong>Descuento global</strong></td>
                                <td :class="{'text-warning':totales.totalDescuentoGlobal > 0 }" class="summary-value text-end">{{ formatToUSD(totales.totalDescuentoGlobal) }}</td>
                            </tr>

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

                <!-- DERECHA -->
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

                            <!-- BASES IMPONIBLES -->
                        <template v-if='basesImpuestoVista.length > 0 '>
                            <tr v-for="tax in basesImpuestoVista" :key="'base-' + tax.codigo">
                                <td class="summary-label text-end"> <strong>Base imponible IVA {{ tax.porcentaje }}%</strong> </td>
                                <td class="summary-value text-end"> {{ formatToUSD(tax.subtotal_neto) }} </td>
                            </tr>
                        </template>
                        <template v-else>
                            <tr>
                                <td class="summary-label text-end"> <strong>Base imponible IVA {{ ivaPrdeterminado }}%</strong> </td>
                                <td class="summary-value text-end"> $0.00 </td>
                            </tr>
                        </template>

                        <!-- IVA -->
                        <template v-if='basesImpuestoVista.length > 0 '>
                            <tr v-for="tax in basesImpuestoVista" :key="'iva-' + tax.codigo">
                                <td class="summary-label text-end"><strong>Monto IVA {{ tax.porcentaje }}%</strong></td>
                                <td class="summary-value text-end"> {{ formatToUSD(tax.iva) }}</td>
                            </tr>
                        </template>
                        <template v-else>
                            <tr>
                                <td class="summary-label text-end"> <strong>Monto IVA {{ ivaPrdeterminado }}%</strong> </td>
                                <td class="summary-value text-end"> $0.00 </td>
                            </tr>
                        </template>

                        <tr>
                            <td class="summary-label text-end"><strong>Recargo</strong></td>
                            <td :class="{'text-warning':global.recargo > 0 }" class="summary-value text-end">{{ formatToUSD(global.recargo) }}</td>
                        </tr>
                        <tr>
                            <td class="summary-label text-end"><strong>Servicios Adc</strong></td>
                            <td :class="{'text-warning':global.serviciosAdc > 0 }" class="summary-value text-end">{{ formatToUSD(global.serviciosAdc) }}</td>
                        </tr>

                        </tbody>
                    </table>

                </div>

            </div>

            <!-- TOTAL -->
            <div class="border-top p-3 bg-light">
                <div class="d-flex justify-content-end align-items-center">
                    <span class="fw-bold fs-4 me-4"> TOTAL </span>
                    <span class="fw-bold fs-4">{{ formatToUSD(totales.totalGeneral) }} </span>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- AJUSTES GLOBALES -->
<div  v-if="!emptyCar" class="p-3">
    <div class="row">

        <!-- Descuento -->
        <div class="col-md-2 form-group-custom">
            <div class="input-group border rounded ">
                <span class="input-group-text"> Descuento global: </span>
                <input
                    type="number"
                    step="0.01"
                    v-model="global.descuentoGlobal"
                    class="form-control text-end"
                    @change='updateValoresGlobales()'
                    >
            </div>
        </div>

        <!-- Recargo -->
        <div class="col-md-2 form-group-custom">
            <div class="input-group border rounded ">
                <span class="input-group-text"> Recargo: </span>
                <input
                    type="number"
                    step="0.01"
                    v-model="global.recargo"
                    class="form-control text-end"
                    @change='updateValoresGlobales()'
                    >
            </div>
        </div>

        <!-- Servicios -->
        <div class="col-md-2 form-group-custom">
            <div class="input-group border rounded ">
                <span class="input-group-text">Servicios Adicionales: </span>
                <input
                    type="number"
                    step="0.01"
                    v-model="global.serviciosAdc"
                    class="form-control text-end"
                    @change='updateValoresGlobales()'
                    >
            </div>
        </div>

        <!--         Otros 
                <div class="col-md-2 form-group-custom">
                    <div class="input-group border rounded ">
                        <span class="input-group-text">Otros cargos: </span>
                        <input
                            type="number"
                            step="0.01"
                            v-model="global.otrosCargos"
                            class="form-control text-end"
                            >
                    </div>
                </div>-->
    </div>
</div>
