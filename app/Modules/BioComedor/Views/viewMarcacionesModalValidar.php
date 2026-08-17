<!--
/**
 * Description of viewMarcaciones
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 17 Agosto 2026
 * @time 1:37:15 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->/       
 
<div id="modalPreviewImportMarcaciones" ref="modalPreviewImportMarcaciones" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xxxl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5><i class="fas fa-file-excel"></i> Vista previa de importacion</h5>
                <button @click="cerrarModalPreviewImport()" class="btn btn-danger btn-sm" :disabled="loadingImportSave">X</button>
            </div>

            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4 mb-2">
                        <div class="alert alert-info mb-0 py-2">
                            <b>Total filas:</b> {{ previewImportacion.total }}
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="alert alert-success mb-0 py-2">
                            <b>Correctas:</b> {{ previewImportacion.correctas }}
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="alert alert-warning mb-0 py-2">
                            <b>Con errores:</b> {{ previewImportacion.errores }}
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="bg-system text-white">
                            <tr>
                                <td>Fila</td>
                                <td>Estado</td>
                                <td>Cedula/DNI</td>
                                <td>Comensal</td>
                                <td>Fecha</td>
                                <td>Hora</td>
                                <td>Comedor</td>
                                <td>Equipo cod</td>
                                <td>Equipo</td>
                                <td>Servicio Excel</td>
                                <td>Servicio hora</td>
                                <td>Observacion</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="fila of previewImportacion.filas" :key="fila.fila" :class="fila.valido ? '' : 'table-warning'">
                                <td>{{ fila.fila }}</td>
                                <td>
                                    <span v-if="fila.valido" class="badge bg-success">CORRECTA</span>
                                    <span v-else class="badge bg-danger">ERROR</span>
                                </td>
                                <td>{{ fila.cedula }}</td>
                                <td>
                                    <b>{{ fila.comensal || fila.comensalExcel || '-' }}</b>
                                </td>
                                <td>{{ fila.fecha || '-' }}</td>
                                <td>{{ fila.hora || '-' }}</td>
                                <td>{{ fila.comedor || fila.comedorExcel || '-' }}</td>
                                <td>{{ fila.equipoCodigoExcel || '-' }}</td>
                                <td>{{ fila.equipo || '-' }}</td>
                                <td>{{ fila.servicioExcel || '-' }}</td>
                                <td>{{ fila.servicioDetectado || '-' }}</td>
                                <td>
                                    <span v-if="fila.valido" class="text-success">Lista para registrar</span>
                                    <ul v-else class="mb-0 ps-3 text-danger">
                                        <li v-for="error of fila.errores">{{ error }}</li>
                                    </ul>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" :disabled="loadingImportSave || filasValidasImportacion.length === 0" @click="guardarMarcacionesImportadas()">
                    <span v-if="loadingImportSave"><i class="fas fa-spinner fa-spin"></i> Guardando...</span>
                    <span v-else><i class="fas fa-save"></i> Registrar correctas</span>
                </button>
                <button @click="cerrarModalPreviewImport()" class="btn btn-secondary" :disabled="loadingImportSave"><i class="fas fa-stop"></i> Cancelar</button>
            </div>
        </div>
    </div>
</div>
