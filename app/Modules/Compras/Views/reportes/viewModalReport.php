<div ref="modalReport" class="modal fade" tabindex="-1" data-bs-backdrop="static">

    <div class="modal-dialog modal-xxl modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header text-dark">
                <h5 class="modal-title mb-0"> <i class="fas fa-file-invoice-dollar me-2"></i> Detalle de Compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"> </button>
            </div>

            <div class="modal-body p-0 bg-light">

                <div v-if="cargandoDetalle" class="text-center py-5">
                    <div class="spinner-border text-primary mb-3"></div>
                    <p class="text-muted">Cargando detalle de la compra...</p>
                </div>

                <div v-else id="detalleCompraModal" class="p-3 p-md-4" v-html="detalleHtml">
                    
                    <!--CUERPO DEL REPORTE-->
                    
                </div>

            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"> <i class="fas fa-times me-2"></i>Cerrar </button>

                <button type="button" class="btn btn-success" @click="generarExcel"> <i class="fas fa-file-excel me-2"></i>Excel </button>

                <button type="button" class="btn btn-danger"  @click="generarPDF"> <i class="fas fa-file-pdf me-2"></i>PDF </button>

                <button type="button" class="btn btn-primary" data-print data-target="detalleCompraModal"> <i class="fas fa-print me-2"></i>Imprimir</button>
            </div>

        </div>
    </div>
</div>
