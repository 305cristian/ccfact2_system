<!DOCTYPE html>
<!--
/**
 * Description of viewCart
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 8 oct 2025
 * @time 11:44:19 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->


<div class="cart">
    <div class="cart-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-shopping-cart me-2"></i>Artículos del Ajuste</h5>
        <div v-if="loading" class="loading-data">
            <h6 style="font-family: "><i class="loading-spin"></i> Cargando Producto...</h6>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" v-model="permitirDuplicados">
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

        <div v-else class="table-container table-responsive">
            <table class="table table-stripped" style="width: 100%">
                <thead>
                    <tr>

                        <th style="width: 5px;" class="text-center"><i class="fas fa-trash"></i></th>
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
                                    @click="deleteProduct(item.rowid)" :disabled="loading">
                                <span><i class="fas fa-trash"></i></span>
                            </button>    
                        </td>
                        <td><span class="badge-type">{{ item.id }}</span></td>
                        <td><strong>{{ item.name }}</strong></td>
                        <td>
                            <div v-if="item.tieneLote === '1' " class="input-group">
                                <input v-model="item.lote" type="text" class="form-control form-control-sm" @change="updateProductCart(item)" style="max-width: 120px;">
                                <input v-model="item.fechaElaboracion" type="date" class="form-control form-control-sm" @change="updateProductCart(item)" style="max-width: 100px;">
                                <input v-model="item.fechaCaducidad" type="date" class="form-control form-control-sm" @change="updateProductCart(item)" style="max-width: 100px;">
                            </div>
                            <div v-else> <p style="font-size: 0.9rem; color: #d1d5db;">El producto no maneja lotes</p></div>


                        </td>

                        <td>
                            <div class="quantity-control input-group">
                                <button class="btn btn-primary btn-sm" @click="item.qty > 1 ? item.qty-- : null; updateProductCart(item)"><i class="fas fa-minus"></i></button>
                                <input class="form-control form-control-sm" v-model.number="item.qty" type="text" @change="updateProductCart(item)" v-numbers-only="{ decimal: true }">
                                <button class="btn btn-primary btn-sm" @click="item.qty++ ; updateProductCart(item)"><i class="fas fa-plus"></i></button>
                                <input type="text" class="form-control form-control-sm" style="max-width: 80px;" v-model.number="item.price" @change="updateProductCart(item)" v-numbers-only="{ decimal: true }">
                                <button class="btn btn-primary btn-sm"><i class="fas fa-dollar-circle"></i></button>
                            </div>

                        </td>
                        <td>
                            <span class="badge bg-info">{{ item.stock }}</span>
                        </td>
                        <td class="price-cell">${{ formatMoney(item.total) }}</td>
                        <td class="price-cell">${{ formatMoney(item.totivaval) }}</td>
                        <td class="price-cell text-primary"><strong>${{ formatMoney(item.totalpriceiva) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>


    <!-- Resumen de totales -->
    <div class="d-flex justify-content-end align-items-end">
        <div class="summary-card">
            <div class="summary-row">
                <span class="summary-label">SubTotal:</span>
                <span class="summary-value">${{ parseFloat(totalCart).toFixed(2) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">IVA (0%):</span>
                <span class="summary-value">${{ parseFloat(totalIva).toFixed(2) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Total:</span>
                <span class="summary-value">${{ parseFloat(totalCartIva).toFixed(2) }}</span>
            </div>
        </div>
    </div>

</div>
