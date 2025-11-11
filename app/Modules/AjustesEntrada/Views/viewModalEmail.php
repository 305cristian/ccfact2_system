<!DOCTYPE html>
<!--
/**
 * Description of viewModalEmail
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 7 nov 2025
 * @time 12:18:48 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->


<!-- Modal de email -->
<div  ref="modalSendEmail" class="modal fade" tabindex="-1"   data-bs-backdrop="static">

    <div class="modal-dialog modal-xl modal-fullscreen-md-down modal-dialog-centered">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header text-dark">
                <div class="d-flex align-items-center gap-3 flex-grow-1">
                    <div>
                        <h5 class="modal-title mb-0">  <i class="fas fa-envelope me-2"></i> Enviar reporte por email</h5>
                    </div>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body p-0 bg-light" >
                <div class="border-1 p-3">
                    <div class="row g-3">
                        <!--Campo PARA--> 
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                Para <span class="text-danger">*</span>
                            </label>
                            <input type="email" v-model="emailData.para" class="form-control" placeholder="Ejm.: correo@empresa.com" required>
                        </div>

                        <!--Campo CC--> 
                        <div class="col-md-6">
                            <label class="form-label fw-bold">CC</label>
                            <input type="email" v-model="emailData.cc" class="form-control" placeholder="(opcional)">
                        </div>

                        <!--Campo Asunto--> 
                        <div class="col-md-12">
                            <label class="form-label fw-bold">
                                Asunto <span class="text-danger">*</span>
                            </label>
                            <input type="text" v-model="emailData.asunto" class="form-control" placeholder="Ejm.: Reporte de Ajuste de Entrada #0005" required>
                        </div>

                        <!--Campo Mensaje--> 
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Mensaje</label>
                            <textarea v-model="emailData.mensaje" class="form-control" rows="5" placeholder="Escriba un mensaje adicional..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer del Modal -->
            <div class="modal-footer bg-light">
                <div v-if="errorSendMail" class="text-danger fw-semibold text-start">
                    <span  v-html="errorSendMail"></span>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cerrar
                </button>
                <button type="submit" class="btn btn-primary" @click.prevent="sendEmailReport" :disabled="loadingEmail">
                    <span v-if="loadingEmail">
                        <i class="loading-spin me-2"></i> Enviando...
                    </span>
                    <span v-else>
                        <i class="fas fa-paper-plane me-2"></i> Enviar Email
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
