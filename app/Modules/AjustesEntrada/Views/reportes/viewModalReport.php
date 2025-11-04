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
            <div class="modal-header text-dark">
                <div class="d-flex align-items-center gap-3 flex-grow-1">
                    <div>
                        <h5 class="modal-title mb-0"> <i class="fas fa-clipboard-list"></i> Detalle de Ajuste de Entrada</h5>
                    </div>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body p-0 bg-light" >

                <!-- Loading -->
                <div v-if="cargandoDetalle" class="text-center py-5">
                    <div class="spinner-border text-primary mb-3"></div>
                    <p class="text-muted">Cargando detalle del ajuste...</p>
                </div>

                <!-- Contenido del Reporte -->
                <div v-else id="detalleAjusteModal" class="p-3 p-md-4">

                </div>
            </div>

            <!-- Footer del Modal -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-close me-2"></i>Cerrar
                </button>
                <button type="button" class="btn btn-success" @click="generarExcel">
                    <i class="fas fa-file-excel me-2"></i>Excel
                </button>
                <button type="button" class="btn btn-danger" @click="generarPDF">
                    <i class="fas fa-file-pdf me-2"></i>PDF
                </button>
                <button type="button" class="btn btn-primary" data-print data-target="detalleAjusteModal">
                    <i class="fas fa-print me-2"></i>Imprimir
                </button>
            </div>



        </div>
    </div>
</div>






