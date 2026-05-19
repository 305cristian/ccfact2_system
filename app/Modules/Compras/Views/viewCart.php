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

<div class="table-responsive">
    <table class="table table-stripped w-100">
        <thead>
            <tr>
                <th></th>
                <th>Código</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Desc.</th>
                <th>ICE</th>
                <th>IVA</th>
                <th>IRBPNR</th>
                <th>Subtotal</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>
            <tr v-for="(item, index) in listaCompra" :key="index">

                <!-- ELIMINAR -->
                <td>
                    <button class="btn btn-danger btn-sm"
                            @click="eliminarItem(index)">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>

                <!-- CODIGO -->
                <td>{{ item.codigo }}</td>

                <!-- PRODUCTO -->
                <td>
                    <strong>{{ item.nombre }}</strong>
                </td>

                <!-- CANTIDAD -->
                <td>
                    <input type="number"
                           v-model.number="item.cantidad"
                           class="form-control form-control-sm"
                           @change="updateItem(item)">
                </td>

                <!-- PRECIO -->
                <td>
                    <input type="number"
                           v-model.number="item.precio"
                           class="form-control form-control-sm"
                           @change="updateItem(item)">
                </td>

                <!-- DESCUENTO -->
                <td>
                    <input type="number"
                           v-model.number="item.descuento"
                           class="form-control form-control-sm"
                           @change="updateItem(item)">
                </td>

                <!-- ICE -->
                <td>
                    <input type="number"
                           v-model.number="item.ice_porcentaje"
                           class="form-control form-control-sm"
                           @change="updateItem(item)">
                </td>

                <!-- IVA -->
                <td>
                    <select v-model="item.iva_porcentaje"
                            class="form-select form-select-sm"
                            @change="updateItem(item)">
                        <option v-for="iva in listaIvas" :value="iva.valor">
                            {{ iva.nombre }}
                        </option>
                    </select>
                </td>

                <!-- IRBPNR -->
                <td>
                    <input type="number"
                           v-model.number="item.irbpnr_unitario"
                           class="form-control form-control-sm"
                           @change="updateItem(item)">
                </td>

                <!-- SUBTOTAL -->
                <td class="text-end">
                    {{ formatToUSD(item.subtotal) }}
                </td>

                <!-- TOTAL -->
                <td class="text-end text-primary">
                    <strong>{{ formatToUSD(item.total) }}</strong>
                </td>

            </tr>
        </tbody>
    </table>
</div>

<div v-if="!emptyCar" class="d-flex justify-content-end align-items-end">
    <div class="summary-card">

        <div class="summary-row">
            <span>Subtotal:</span>
            <span>{{ formatToUSD(totalSubtotal) }}</span>
        </div>

        <div class="summary-row">
            <span>IVA:</span>
            <span>{{ formatToUSD(totalIva) }}</span>
        </div>

        <div class="summary-row">
            <span>IRBPNR:</span>
            <span>{{ formatToUSD(totalIrbpnr) }}</span>
        </div>

        <div class="summary-row total">
            <strong>Total:</strong>
            <strong>{{ formatToUSD(totalGeneral) }}</strong>
        </div>

    </div>
</div>