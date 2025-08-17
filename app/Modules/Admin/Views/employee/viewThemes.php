<?php
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of viewThemes
 * @author Cristian R. Paz
 * @Date 20 sep. 2023
 * @Time 16:44:24
 */
?>

<div class="modal-header">
    <h5 class="modal-title" ><i class="fas fa-monitor-waveform fa-2x"></i> Generador de Temas</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div id="idmain" class="modal-body text-center d-grid justify-content-center">
    <div  :style="{background: gradient}" style="width: 500px; height: 100px">

    </div>
    <hr>

    <div class="container">

        <div v-for="(color,index) in colors" :key="color.id" :class="{shake : color.last}">
            <input class="form-control" type="text" v-model.trim="color.hex" maxlength="7" :style="{color: color.hex}" :class="{pin : color.disabled}" :disabled="color.disabled" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
        </div>

        <br>
        <button class="btn btn-primary btn-lg" @click="generateGradient"><i class="fas fa-palette"></i> GENERAR TEMA</button>
        <button class="btn btn-success btn-lg" @click="changeThemes()"><i class="fas fa-palette-boxes"></i> APLICAR TEMA</button>

    </div>

</div>

