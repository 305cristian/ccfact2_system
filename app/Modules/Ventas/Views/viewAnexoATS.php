<!DOCTYPE html>
<!--
/**
 * Description of viewAnexoATS
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 16 ago 2026
 * @time 11:37:26 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<fieldset v-if="!emptyCar && requiereFormaPagoAts" class="border rounded p-3 mt-3">
    <legend>
        <i class="fas fa-file-invoice-dollar me-1"></i>Informacion ATS
    </legend>
    <div class="row">
        <div class="col-12 col-lg-7 form-group-custom mb-0">
            <div class="d-flex border rounded overflow-visible">
                <span class="input-group-text bg-cris-system text-white">
                    <i class="fas fa-credit-card me-2"></i>Forma pago ATS
                </span>
                <vue-select
                    class="flex-grow-1"
                    :options="listaFormasPagoSri"
                    label="fp_nombre_sri"
                    v-model="ats.formaPago"
                    multiple
                    placeholder="Seleccione forma de pago ATS">
                    <template #option="{ codigo, fp_nombre_sri }">
                        {{ codigo }} - {{ fp_nombre_sri }}
                    </template>
                    <template #selected-option="{ codigo, fp_nombre_sri }">
                        {{ codigo }} - {{ fp_nombre_sri }}
                    </template>
                </vue-select>
            </div>
            <small class="text-muted d-block mt-1">
                Obligatorio cuando la venta supera el valor maximo configurado para ATS.
            </small>
        </div>
    </div>
</fieldset>