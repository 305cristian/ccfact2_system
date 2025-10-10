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
                    <input class="form-check-input" type="checkbox" id="permitirDuplicados" checked>
                    <label class="form-check-label" for="permitirDuplicados">
                        Permitir items duplicados
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid p-0">
        <div v-if="cartAjuste.length === 0" class="empty-state">
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
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in cartAjuste" :key="index">
                        <td>
                            <button class="btn btn-danger btn-sm" title="Eliminar" @click="eliminarProducto(index)"><i class="fas fa-trash"></i></button>    
                        </td>
                        <td><span class="badge-type">{{ item.codigo }}</span></td>
                        <td><strong>{{ item.nombre }}</strong></td>
                        <td>
                            <div class="input-group">
                                <input v-model="item.lote" type="text" class="form-control form-control-sm" style="max-width: 120px;">
                                <input v-model="item.fechaElaboracion" type="date" class="form-control form-control-sm" style="max-width: 100px;">
                                <input v-model="item.fechaCaducidad" type="date" class="form-control form-control-sm" style="max-width: 100px;">
                            </div>


                        </td>

                        <td>
                            <div class="quantity-control input-group">
                                <button class="btn btn-primary btn-sm" @click="item.cantidad--"><i class="fas fa-minus"></i></button>
                                <input class="form-control form-control-sm" v-model.number="item.cantidad" type="number" @change="calcularTotal">
                                <button class="btn btn-primary btn-sm" @click="item.cantidad++"><i class="fas fa-plus"></i></button>
                                <input type="number" class="form-control form-control-sm" style="max-width: 80px;" v-model.number="item.precio">
                                <button class="btn btn-primary btn-sm"><i class="fas fa-dollar-circle"></i></button>
                            </div>
                           
                        </td>
                        <td>
                            <span class="badge bg-info">{{ item.stock }}</span>
                        </td>
                        <td class="price-cell">${{ (item.cantidad * item.precio).toFixed(2) }}</td>

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
                <span class="summary-value">${{ subtotal.toFixed(2) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">IVA (0%):</span>
                <span class="summary-value">${{ iva.toFixed(2) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Total:</span>
                <span class="summary-value">${{ total.toFixed(2) }}</span>
            </div>
        </div>
    </div>

</div>
