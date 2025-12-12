<!DOCTYPE html>
<!--
/**
 * Description of viewModalReport
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 5 dic 2025
 * @time 12:46:22 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div  ref="modalReport" class="modal fade" tabindex="-1"   data-bs-backdrop="static">

    <div class="modal-dialog modal-xxl modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header text-dark">
                <div class="d-flex align-items-center gap-3 flex-grow-1">
                    <div>
                        <h5 class="modal-title mb-0"> <i class="fas fa-clipboard-list"></i> Detalle de la transferencia</h5>
                    </div>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body p-0 bg-light" >

                <!-- Loading -->
                <div v-if="cargandoDetalle" class="text-center py-5">
                    <div class="spinner-border text-primary mb-3"></div>
                    <p class="text-muted">Cargando detalle de la transferencia...</p>
                </div>

                <!-- Contenido del Reporte -->
                <div v-else id="detalleTransferenciaModal" class="p-3 p-md-4">

                </div>
            </div>

            <!-- Footer del Modal -->
            <div class="modal-footer bg-light justify-content-between align-items-center">
                <div v-if="dataTransf.trb_estado == 2 && (userSession == dataTransf.fk_user_confirma || rootUser == 1 )" class="d-flex gap-3">
                    <button @click="confirmarTransferencia(dataTransf.id)" type="button" class="btn btn-success">
                        <i class="fas fa-check-double me-2"></i>Confirmar
                    </button>
                    <button  @click="rechazarTransferencia(dataTransf.id)"  type="button" class="btn btn-danger">
                        <i class="fas fa-stop-circle me-2"></i>Rechazar
                    </button>
                </div>
                <div class="d-flex gap-2 ms-auto">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-close me-2"></i>Cerrar
                    </button>
                    <button type="button" class="btn btn-success" @click="generarExcel">
                        <i class="fas fa-file-excel me-2"></i>Excel
                    </button>
                    <button type="button" class="btn btn-danger" @click="generarPDF">
                        <i class="fas fa-file-pdf me-2"></i>PDF
                    </button>
                    <button type="button" class="btn btn-primary" data-print data-target="detalleTransferenciaModal">
                        <i class="fas fa-print me-2"></i>Imprimir
                    </button>
                </div>
            </div>



        </div>
    </div>
</div>
