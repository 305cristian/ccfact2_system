<!DOCTYPE html>
<!--
/**
 * Description of viewDetalleAjuste
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 25 oct 2025
 * @time 10:47:43 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->





<!-- Modal de Detalle -->
<div  ref="modalReport" class="modal fade" tabindex="-1"   data-bs-backdrop="static">

    <div class="modal-dialog modal-xxl modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header text-white"  :class="{
                 'bg-warning': ajusteActual.ajen_estado === '1',   // Borrador
                 'bg-success': ajusteActual.ajen_estado === '2',   // Archivado
                 'bg-danger': ajusteActual.ajen_estado === '-1'    // Anulado
                 }">
                <div class="d-flex align-items-center gap-3 flex-grow-1">
                    <div>
                        <h5 class="modal-title mb-0"> <i class="fas fa-clipboard-list"></i> Detalle de Ajuste de Entrada</h5>
                    </div>
                </div>

                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body p-0 bg-light" id="detalleAjusteModal">

                <!-- Loading -->
                <div v-if="cargandoDetalle" class="text-center py-5">
                    <div class="spinner-border text-primary mb-3"></div>
                    <p class="text-muted">Cargando detalle del ajuste...</p>
                </div>

                <!-- Contenido del Reporte -->
                <div v-else id="reporte-contenido" class="p-3 p-md-4">

                    <!-- Encabezado Empresarial -->
                    <div class="bg-white rounded shadow-sm p-3 p-md-4 mb-4">
                        <div class="row align-items-center mb-4">

                            <div class="col-lg-3 text-center mb-3 mb-lg-0">
                                <div class="bg-light p-3 rounded">
                                    <img v-if='empresa.logo' style="max-width: 100%; height: auto" :src="pathUrl+'/uploads/img/enterprice/'+empresa.logo " class="img-fluid  img-circle" alt="Imagen">
                                    <span v-else><i class="fas fa-building" style="font-size: 3rem; color: #fff;"></i></span>
                                    <h6 class="mt-2 mb-0 fw-bold">{{empresa.nombre}} S.A.</h6>
                                    <small class="text-dark">RUC: {{empresa.ruc}}</small>
                                </div>
                            </div>

                            <div class="col-lg-5 mb-3 mb-lg-0">
                                <p class="mb-1 small">
                                    <i class="bi bi-geo-alt text-primary me-2"></i>
                                    {{empresa.direccion}}
                                </p>
                                <p class="mb-1 small">
                                    <i class="bi bi-telephone text-primary me-2"></i>
                                    {{empresa.telefono +' / '+ empresa.celular}}
                                </p>
                                <p class="mb-0 small">
                                    <i class="bi bi-envelope text-primary me-2"></i>
                                    {{empresa.email}}
                                </p>
                                <p class="mb-0 small">
                                    <i class="bi bi-envelope text-primary me-2"></i>
                                    {{empresa.website}}
                                </p>
                            </div>

                            <div class="col-lg-4">
                                <div class="border border-2 border-system rounded p-3 text-center">
                                    <h4 class="fw-bold mb-3">AJUSTE DE ENTRADA</h4>
                                    <h5 class="text-danger fw-bold mb-2">#{{ zFill(ajusteActual.ajen_secuencial,5) }}</h5>
                                    <p class="mb-2 small">
                                        <strong>Fecha de Emisión:</strong><br>
                                        <span class="badge bg-primary-subtle text-primary">
                                            {{ formatearFecha(ajusteActual.ajen_fecha) }}
                                        </span>
                                    </p>
                                    <span  v-html='estadoDocumento'> </span>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <!-- Info del Ajuste -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-3 small">
                                            <i class="fas fa-building me-2"></i>Información del Ajuste
                                        </h6>
                                        <div class="mb-2">
                                            <strong class="text-muted small d-block">Bodega:</strong>
                                            <span class="fw-semibold small">{{ ajusteActual.bod_nombre }}</span>
                                        </div>
<!--                                        <div class="mb-2">
                                            <strong class="text-muted small d-block">Centro de Costo:</strong>
                                            <span class="fw-semibold small">{{ ajusteActual.cc_nombre }}</span>
                                        </div>-->
                                        <div class="mb-2">
                                            <strong class="text-muted small d-block">Motivo:</strong>
                                            <span class="fw-semibold small">{{ ajusteActual.mot_nombre }}</span>
                                        </div>
                                        <div>
                                            <strong class="text-muted small d-block">Usuario:</strong>
                                            <span class="fw-semibold small">{{ ajusteActual.user_create }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-3 small">
                                            <i class="fas fa-user-tie me-2"></i>Información del Proveedor
                                        </h6>
                                        <div class="mb-2">
                                            <strong class="text-muted small d-block">RUC:</strong>
                                            <span class="fw-semibold small">{{ ajusteActual.prov_ruc }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong class="text-muted small d-block">Proveedor:</strong>
                                            <span class="fw-semibold small">{{ ajusteActual.prov_razon_social }}</span>
                                        </div>
                                        <div>
                                            <strong class="text-muted small d-block">Observaciones:</strong>
                                            <span class="text-muted fst-italic small">
                                                {{ ajusteActual.ajen_observacion || 'Sin observaciones' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detalle de Productos -->
                    <div class="bg-white rounded shadow-sm p-3 p-md-4">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-box-seam me-2"></i>Detalle de Productos
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle mb-0">
                                <thead class="table-secondary">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Código</th>
                                        <th>Producto</th>
                                        <th class="d-none d-md-table-cell">Lote</th>
                                        <th class="d-none d-md-table-cell">Caducidad</th>
                                        <th class="text-center">Cant.</th>
                                        <th class="text-end">Costo Unit.</th>
                                        <th class="text-end">Subtotal</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, idx) in ajusteActual.detalle" :key="idx">
                                        <td class="text-center text-muted small">{{ idx + 1 }}</td>
                                        <td>
                                            <span class="px-2 py-1 rounded small"> {{ item.prod_codigo }}</span>
                                        </td>
                                        <td class="fw-semibold small">{{ item.prod_nombre }}</td>
                                        <td class="text-center small d-none d-md-table-cell">
                                            <span v-if="item.lot_lote" class="badge bg-warning-subtle text-warning small">{{ item.lot_lote }}</span>
                                            <span v-else class="text-muted">-</span>
                                        </td>
                                        <td class="text-center small d-none d-md-table-cell">
                                            <span  v-if="item.lot_fecha_caducidad" >{{formatearFecha(item.lot_fecha_caducidad)}} </span>
                                            <span  v-else  class="text-muted" >-</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info-subtle text-info">{{ item.ajend_itemcantidad }} </span>
                                        </td>
                                        <td class="text-end small">{{ formatToUSD(item.ajend_itemcosto) }}</td>
                                        <td class="text-end fw-bold small">{{ formatToUSD(item.ajend_itemcostoxcantidad) }}</td>

                                    </tr>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">TOTAL:</td>
                                        <td colspan="2" class=" d-none d-md-table-cell"></td>
                                        <td  class="text-end fw-bold text-primary fs-5"> {{ formatToUSD(calcularTotal()) }} </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Resumen -->
                        <div class="row mt-4 g-3">
                            <div class="col-md-8">
                                <div class="alert alert-info mb-0 d-flex align-items-center">
                                    <i class="bi bi-info-circle fs-4 me-3"></i>
                                    <div class="small">
                                        <strong>Total de Items:</strong> {{ ajusteActual.detalle?.length || 0 }} productos<br>
                                        <span class="text-muted">Total unidades: {{ calcularTotalUnidades() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-primary text-white border-0 shadow">
                                    <div class="card-body text-center py-3">
                                        <small class="d-block mb-1 opacity-75 text-dark">TOTAL GENERAL</small>
                                        <h3 class="mb-0 fw-bold text-dark">{{ formatToUSD(calcularTotal()) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer del reporte -->
                    <div class="bg-white rounded shadow-sm p-3 mt-4 text-center">
                        <small class="text-muted">
                            <i class="bi bi-calendar-check me-1"></i>
                            Generado el {{ formatearFechaHoraActual() }}
                        </small>
                    </div>

                </div>
            </div>

            <!-- Footer del Modal -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-close me-2"></i>Cerrar
                </button>
                <button type="button" class="btn btn-success" @click="exportarExcel">
                    <i class="fas fa-file-excel me-2"></i>Excel
                </button>
                <button type="button" class="btn btn-danger" @click="exportarPDF">
                    <i class="fas fa-file-pdf me-2"></i>PDF
                </button>
                <button type="button" class="btn btn-primary" data-print data-target="detalleAjusteModal">
                    <i class="fas fa-print me-2"></i>Imprimir
                </button>
            </div>

        </div>
    </div>
</div>






