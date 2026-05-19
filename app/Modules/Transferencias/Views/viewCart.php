<!DOCTYPE html>
<!--
/**
 * Description of viewCart
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 4 dic 2025
 * @time 11:58:46 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->


<div class="cart">
    <div class="cart-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-shopping-cart me-2"></i>Artículos a transferir</h5>
        <div v-if="loading" class="loading-data">
            <h6><i class="loading-spin"></i> Cargando Producto...</h6>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-check">
                    <input class="form-check-input"
                           type="checkbox"
                           v-model="formDataTransfer.trbPermitirDuplicados">
                    <label class="form-check-label">
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
            <p style="font-size: 0.9rem; color: #d1d5db;">
                Utiliza la búsqueda de arriba para agregar productos
            </p>
        </div>

        <div v-else class="table-container table-responsive">
            <table class="table table-stripped w-100">
                <thead>
                    <tr>
                        <th style="width: 5px;" class="text-center">
                            <i class="fas fa-trash"></i>
                        </th>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Lote/Fecha Elab./Fecha Caduc.</th>
                        <th>Cantidad/Precio</th>
                        <th>Stock</th>
                        <th>Subtotal</th>
                        <th>IVA</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in listaCartData" :key="index">
                        <td>
                            <button class="btn btn-danger btn-sm" 
                                    title="Eliminar"
                                    @click="deleteProduct(item.rowid)"
                                    :disabled="loading">
                                <span><i class="fas fa-trash"></i></span>
                            </button>    
                        </td>
                        <td>
                            <span class="badge-type" v-tooltip:top=" item.id ">
                                {{ item.codigo }}
                            </span>
                        </td>
                        <td>
                            <span class="text-danger">
                                {{ item.ivaPorcent != '0.00' ? '*' : '' }}
                            </span>
                            <strong>
                                {{ item.name }}
                                <span class="text-info">
                                    <i class="fas fa-angles-right"></i> {{item.unidadMedida}}
                                </span>
                            </strong>
                        </td>
                        <td>
                            <!-- Si el producto maneja lotes -->
                            <div v-if="item.tieneLote === '1'">
                                <div class="mb-3">
                                    <div class="input-group input-group-sm shadow-sm">
                                        <span class="input-group-text bg-primary text-white">
                                            <i class="bi bi-upc-scan"></i>
                                        </span>
                                        <select class="form-select"
                                                v-model="item.idLote"
                                                @change="updateProductCart(item)"
                                                style="font-size: 0.875rem;">
                                            <option disabled value="">Seleccione un lote</option>
                                            <option v-for="lote in item.lotes" 
                                                    :key="lote.fk_lote" 
                                                    :value="lote.fk_lote"
                                                    :class="{'text-danger': lote.stockLote < 5, 'text-success': lote.stockLote >= 5}">
                                                {{ lote.lote }} • {{ lote.fechaElaboracion }} → {{ lote.fechaCaducidad }}  ({{ lote.stockLote+' ' }}{{item.unidadMedida}})
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Si el producto NO maneja lotes -->
                            <div v-else>
                                <p style="font-size: 0.9rem; color: #d1d5db; font-style: italic">
                                    El producto no maneja lotes
                                </p>
                            </div>
                        </td>


                        <td>
                            <div class="quantity-control input-group">
                                <button class="btn btn-primary btn-sm"
                                        @click="item.qty > 1 ? item.qty-- : null; updateProductCart(item)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input class="form-control form-control-sm"
                                       v-model.number="item.qty"
                                       type="text"
                                       @change="updateProductCart(item)"
                                       v-numbers-only="{ decimal: true }">
                                <button class="btn btn-primary btn-sm"
                                        @click="item.qty++ ; updateProductCart(item)">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <input type="text"
                                       readonly
                                       class="form-control form-control-sm"
                                       style="max-width: 80px;"
                                       v-model.number="item.price"
                                       @change="updateProductCart(item)"
                                       v-numbers-only="{ decimal: true }">
                                <button class="btn btn-primary btn-sm">
                                    <i class="fas fa-dollar-circle"></i>
                                </button>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex gap-1">
                                <span class="badge bg-info"
                                      v-tooltip:top="'Stock de Bodega'">
                                    {{ item.stockBodega }}
                                </span>
                                <span class="badge bg-info"
                                      v-tooltip:top="'Stock General'">
                                    {{ item.stock }}
                                </span>
                            </div>
                        </td>
                        <td class="price-cell">{{ formatToUSD(item.totitembaseiva) }}</td>
                        <td class="price-cell">{{ formatToUSD(item.totivaval) }}</td>
                        <td class="price-cell text-primary">
                            <strong>{{ formatToUSD(item.totalpriceiva) }}</strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Resumen de totales -->
    <div v-if="!emptyCar" class="d-flex justify-content-end align-items-end">
        <div class="summary-card">
            <div class="summary-row">
                <span class="summary-label">SubTotal:</span>
                <span class="summary-value">{{ formatToUSD(totalCart) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">IVA ({{ivaPrdeterminado}}%):</span>
                <span class="summary-value">{{ formatToUSD(totalIva) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Total:</span>
                <span class="summary-value">{{ formatToUSD(totalCartIva) }}</span>
            </div>
        </div>
    </div>
</div>

<!--
<div class="cart">

    <div class="cart-header d-flex justify-content-between">
        <h5>
            <i class="fas fa-boxes"></i>
            Artículos de la Transferencia
        </h5>
        <div class="form-check">
            <input class="form-check-input"
                   type="checkbox"
                   v-model="formDataTransfer.trbPermitirDuplicados">
            <label class="form-check-label">
                Permitir ítems duplicados
            </label>
        </div>
    </div>

    <div class="container-fluid p-0">

        <div v-if="emptyCar" class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No hay productos agregados</p>
        </div>

        <div v-else class="table-responsive">
            <table class="table table-striped">

                <thead>
                    <tr>
                        <th></th>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Lote</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>IVA</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="item in listaCartData" :key="item.rowid">

                        <td>
                            <button class="btn btn-sm btn-danger"
                                    @click="deleteProduct(item.rowid)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>

                        <td>{{ item.codigo }}</td>

                        <td>
                            {{ item.name }}
                            <span v-if="item.ivaPorcent > 0" class="text-danger">*</span>
                        </td>

                        <td>
                            <select v-if="item.tieneLote === '1'"
                                    v-model="item.idLote"
                                    class="form-select form-select-sm"
                                    @change="updateProductCart(item)">
                                <option disabled value="">Seleccione lote</option>
                                <option v-for="l in item.lotes"
                                        :value="l.fk_lote">
                                    {{ l.lote }} ({{l.stockLote}})
                                </option>
                            </select>
                            <small v-else class="text-muted">No maneja lote</small>
                        </td>

                        <td>
                            <input type="number"
                                   class="form-control form-control-sm"
                                   v-model="item.qty"
                                   @change="updateProductCart(item)">
                        </td>

                        <td>{{ formatToUSD(item.total) }}</td>
                        <td>{{ formatToUSD(item.totivaval) }}</td>
                        <td class="text-primary">
                            <strong>{{ formatToUSD(item.totalpriceiva) }}</strong>
                        </td>

                    </tr>
                </tbody>

            </table>
        </div>
    </div>

    <div v-if="!emptyCar" class="summary-card">
        <div>Subtotal: {{ formatToUSD(totalCart) }}</div>
        <div>IVA: {{ formatToUSD(totalIva) }}</div>
        <div><strong>Total: {{ formatToUSD(totalCartIva) }}</strong></div>
    </div>

</div>-->

