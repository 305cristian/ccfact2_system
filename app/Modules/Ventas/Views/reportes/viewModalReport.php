<!DOCTYPE html>
<!--
/**
 * Description of viewModalReport
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 16 ago 2026
 * @time 9:52:01 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div ref="modalReport" class="modal fade" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xxl modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header text-dark">
                <h5 class="modal-title mb-0">
                    <i class="fas fa-file-invoice-dollar me-2"></i> {{ modalTitulo }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0 bg-light">
                <div v-if="cargandoDetalle" class="text-center py-5">
                    <div class="spinner-border text-primary mb-3"></div>
                    <p class="text-muted">Cargando detalle de la venta...</p>
                </div>

                <div v-else id="detalleVentaModal" class="p-3 p-md-4" v-html="detalleHtml"></div>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cerrar
                </button>
                <button v-if="mostrarBotonesReporte" type="button" class="btn btn-success" @click="generarExcel">
                    <i class="fas fa-file-excel me-2"></i>Excel
                </button>
                <button v-if="mostrarBotonesReporte" type="button" class="btn btn-danger" @click="generarPDF">
                    <i class="fas fa-file-pdf me-2"></i>PDF
                </button>
                <button type="button" class="btn btn-primary" data-print data-target="detalleVentaModal">
                    <i class="fas fa-print me-2"></i>Imprimir
                </button>
            </div>
        </div>
    </div>
</div>
