<!DOCTYPE html>
<!--
/**
 * Description of viewModalMiniKardex
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 23 mar 2026
 * @time 3:55:14 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div ref="modalMiniKardex" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-xxl">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="fas fa-chart-line me-2"></i>
                    Movimiento últimos 30 días
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body">

                <div class="row">

                    <!-- INFO PRODUCTO -->
                    <div class="col-md-4 border-end">

                        <div class="text-center mb-3">
                            <img v-if="productoSeleccionado?.imagen"
                                 :src="productoSeleccionado?.imagen || '/img/no-image.png'"
                                 class="img-fluid rounded"
                                 style="max-height:150px"
                                 >
                            <span v-else> <i class="fas fa-box-open-full fa-3x"></i></span>
                        </div>

                        <h6 class="fw-bold text-center">
                            {{ productoSeleccionado?.prod_nombre }}
                        </h6>

                        <div class="mt-3 small text-center">
                            <p><strong>Código: </strong> {{ productoSeleccionado?.prod_codigo }}</p>
                            <p><strong>Stock actual: </strong> 
                                <span class="badge bg-success">
                                    {{ parseFloat(productoSeleccionado?.stb_stock) - parseFloat(productoSeleccionado.reservaProducto)}}
                                </span>
                            </p>
                        </div>

                    </div>

                    <!--MINI KARDEX -->
                    <div class="col-md-8 table-scroll" 

                        <!-- Loading -->
                        <div v-if="loadingMiniKardex" class="text-center py-5">
                            <div class="spinner-border text-primary"></div>
                            <p class="mt-2">Cargando movimientos...</p>
                        </div>

                        <!-- Tabla -->
                        <div v-else>

                            <table class="table table-sm table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Movimiento</th>
                                        <th>C. Prom</th>
                                        <th>C. Ult</th>
                                        <th class="text-end text-success">Entrada</th>
                                        <th class="text-end text-danger">Salida</th>
                                        <th class="text-end text-primary">Saldo</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="row in listaMiniKardex" :key="row.id">
                                        <td>{{ row.fecha }}</td>

                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ row.movimiento }}
                                            </span>
                                        </td>

                                        <td class="text-end">
                                            {{ formatToUSD(row.kar_costo_promedio) }}
                                        </td>
                                        <td class="text-end">
                                            {{ formatToUSD(row.kar_costo_ultimo) }}
                                        </td>

                                        <td class="text-end text-success fw-bold">
                                            {{ parseFloat(row.entrada).toFixed(2) }}
                                        </td>

                                        <td class="text-end text-danger fw-bold">
                                            {{ parseFloat(row.salida).toFixed(2)  }}
                                        </td>

                                        <td class="text-end text-primary fw-bold">
                                            {{ parseFloat(row.saldo).toFixed(2)  }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>

                    </div>

                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer">

                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <span><i class="fas fa-close"></i> Cerrar</span>
                </button>

                <button class="btn btn-primary"
                        @click="verKardex(productoSeleccionado.id)">
                    <i class="fas fa-external-link-alt me-2"></i>
                    Ver kardex completo
                </button>

            </div>

        </div>
    </div>
</div>