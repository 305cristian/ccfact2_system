<!DOCTYPE html>
<!--
/**
 * Description of viewAnexoATS
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 24 may 2026
 * @time 10:53:57 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<fieldset v-if="!emptyCar">

    <legend>
        Información de pago anexo ATS
    </legend>

    <!-- ANEXO ATS -->
    <div class="p-3 mb-3">
        <div class="row">
            <!-- Pago residente -->
            <div class="col-md-4 form-group-custom">
                <div class="d-flex border rounded overflow-visible">
                    <span class="input-group-text bg-cris-system"> <i class="fas fa-globe-americas me-2"></i>Pago residente</span>
                    <select v-model="ats.residente" class="form-select" >
                        <option value="RESIDENTE"> RESIDENTE</option>
                        <option value="NO_RESIDENTE"> NO RESIDENTE</option>
                    </select>
                </div>
            </div>

            <!-- Forma pago ATS -->
            <div
                v-if="formCompra.compEstado === 'ARCHIVADO' && Number(totales.totalGeneral) >= Number(valorMaximoATSSRI)"
                class="col-md-8 form-group-custom"
                >
                <div class="d-flex border rounded">
                    <span class="input-group-text bg-cris-system"><i class="fas fa-file-invoice-dollar me-2"></i> Forma pago ATS</span>
                    <vue-select
                        class="flex-grow-1"
                        :options="listaFormasPagoSRI"
                        label="fp_nombre_sri"
                        v-model="ats.formaPago"
                        multiple
                        placeholder="Seleccione"
                        >
                    </vue-select>
                </div>
            </div>
        </div>
    </div>
</fieldset>
