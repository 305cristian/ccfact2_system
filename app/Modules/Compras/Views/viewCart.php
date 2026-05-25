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
            <table class="table table-striped table-hover align-middle w-100">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 100px;"><i  class="fas fa-trash"></i></th>
                        <th>Cod. Prov</th>
                        <th>Codigo</th>
                        <th>Producto</th>
                        <th>Lote (Fechas)</th>
                        <th>IVA</th>
                        <th>Cta. Contable</th>
                        <th class="text-center">Cantidad / Precio</th>
                        <th class="text-center">Descuento</th>
                        <th class="text-center">ICE</th>
                        <th class="text-center">IVA</th>
                        <th class="text-center">Subtotal</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Centro Costo</th>
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
                        <td><input type="text" value="00025" class="form-control form-control-sm"></td>

                        <!--CODIGO DE PRODUCTO-->
                        <td style="min-width: 130px;">
                            <span class="badge bg-cris-system text-wrap w-100 d-inline-block text-start"
                                  style="font-size: 12px;  white-space: normal; line-height: 1.2; "
                                  v-tooltip:top=" item.id ">{{ item.codigo }}</span>
                        </td>

                        <!--ITEM-->
                        <td style="min-width: 220px; max-width: 250px;">
                            <span class="text-danger">{{item.ivaPorcent != '0.00'? '*':'' }}</span>
                            <strong>{{ item.nombre }} <span class="text-info"><i class="fas fa-angles-right"></i> {{item.unidadMedida}}</span></strong>
                        </td>

                        <!--DATOS DEL LOTE-->
                        <td>
                            <div v-if="item.tieneLote === '1' " class="input-group">
                                <input v-model="item.lote" type="text" class="form-control form-control-sm" @change="updateProductCart(item)" style="max-width: 130px;">
                                <input v-model="item.fechaElaboracion" type="date" class="form-control form-control-sm" @change="updateProductCart(item)" style="max-width: 120px;">
                                <input v-model="item.fechaCaducidad" type="date" class="form-control form-control-sm" @change="updateProductCart(item)" style="max-width: 120px;">
                            </div>
                            <div v-else> <p style="font-size: 0.9rem; color: #d1d5db; font-style: italic">Sin lotes</p></div>
                        </td>
                        <td style="min-width: 100px; max-width: 100px;">
                            <select class="form-select form-select-sm">
                                <option>APLICA IVA</option>
                                <option>EXCENTO DE IVA</option>
                                <option>NO OBJETO DE IMPUESTOS</option>
                                <option>TARIFA 0%</option>
                            </select>
                        </td>
                        <td style="min-width: 160px; max-width: 160px;">
                            <select class="form-select form-select-sm">
                                <option>CUENTA contable cod</option>
                            </select>
                        </td>

                        <!--CANTIDAD Y PRECIO-->
                        <td style="min-width: 220px;">
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
                        <td style="min-width: 170px;">

                            <div class="input-group input-group-sm">

                                <!-- PORCENTAJE -->
                                <button
                                    class="btn"
                                    :class="
                                    tipoDescuento === 'PORCENTAJE'
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
                                    tipoDescuento === 'VALOR'
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
                                    placeholder="0.00"
                                    >


                            </div>



                        </td>
                        <!--                       <td style="min-width: 170px;">
                                                   
                                                    <div class="quantity-control input-group">
                                                        <button class="btn btn-info btn-sm" @click="item.qty > 1 ? item.qty-- : null; updateProductCart(item)"><i class="fas fa-percentage"></i></button>
                                                        <input class="form-control form-control-sm"
                                                               v-model.number="item.qty"
                                                               type="text"
                                                               @change="updateProductCart(item)"
                                                               v-numbers-only="{ decimal: true }">
                        
                                                        <button class="btn btn-info btn-sm"><i class="fas fa-dollar-sign"></i></button>
                                                        <input type="text"
                                                               class="form-control form-control-sm"
                                                               style="max-width: 80px;"
                                                               v-model.number="item.price"
                                                               @change="updateProductCart(item)"
                                                               v-numbers-only="{ decimal: true }">
                        
                                                    </div>
                        
                                                </td>-->

                        <!--ICE-->
                        <td class="price-cell">{{ formatToUSD(00) }}</td>

                        <!--IVA BASE-->
                        <td class="price-cell">{{ formatToUSD(item.totitembaseiva) }}</td>

                        <!--IVA VALOR-->
                        <td class="price-cell">{{ formatToUSD(item.totivaval) }}</td>

                        <!--TOTAL PRECIO MAS IVA-->
                        <td class="price-cell text-primary"><strong>{{ formatToUSD(item.totalpriceiva) }}</strong></td>

                        <td style="min-width: 100px; max-width: 100px;">
                            <select class="form-select form-select-sm">
                                <option>ccofeeerrgergmp</option>
                                <option>ccfact</option>
                            </select>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

    <div v-if="!emptyCar" class="d-flex justify-content-end align-items-end mb-4 mt-4 mr-4">
        <div class="summary-card">
            <div class="summary-row">
                <span class="summary-label">Subtotal bruto:</span>
                <span class="summary-value">{{ formatToUSD(totalCart) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">IVA ({{ivaPrdeterminado}}%):</span>
                <span class="summary-value">{{ formatToUSD(totalIva) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">IRBPRN</span>
                <span class="summary-value">0.02</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Total:</span>
                <span class="summary-value">{{ formatToUSD(totalCartIva) }}</span>
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
                        >
                </div>
            </div>

            <!-- Servicios -->
            <div class="col-md-2 form-group-custom">
                <div class="input-group border rounded ">
                    <span class="input-group-text">Servicios: </span>
                    <input
                        type="number"
                        step="0.01"
                        v-model="global.servicios"
                        class="form-control text-end"
                        >
                </div>
            </div>

            <!-- Otros -->
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
            </div>
        </div>
    </div>




    <!--    <div v-if="!emptyCar" class="row justify-content-end mt-3">
            <div class="col-12 col-lg-5 col-xl-4">
                <div class="border rounded p-3 bg-light">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal bruto:</span>
                        <span>{{ formatToUSD(totalCart || 0) }}</span>
                    </div>
    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Descuento:</span>
                        <span>{{ formatToUSD(totalDescuento || 0) }}</span>
                    </div>
    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal neto:</span>
                        <span>{{ formatToUSD(totalSubtotal || totalSubtotalNeto || 0) }}</span>
                    </div>
    
                    <div class="d-flex justify-content-between mb-2">
                        <span>ICE:</span>
                        <span>{{ formatToUSD(totalIce || 0) }}</span>
                    </div>
    
                    <div v-for="imp in resumenImpuestos"
                         :key="imp.fk_impuesto_tarifa || imp.porcentaje"
                         class="d-flex justify-content-between mb-2">
                        <span>{{ imp.detalle || ('IVA ' + imp.porcentaje + '%') }}:</span>
                        <span>{{ formatToUSD(imp.valor || 0) }}</span>
                    </div>
    
                    <div class="d-flex justify-content-between mb-2">
                        <span>IRBPNR:</span>
                        <span>{{ formatToUSD(totalIrbpnr || 0) }}</span>
                    </div>
    
                    <hr class="my-2">
    
                    <div class="d-flex justify-content-between fs-5 text-primary">
                        <strong>Total:</strong>
                        <strong>{{ formatToUSD(totalGeneral || 0) }}</strong>
                    </div>
                </div>
            </div>
        </div>-->
</div>
