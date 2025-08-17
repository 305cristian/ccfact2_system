<?php
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of start_system
 * @author Cristian R. Paz
 * @Date 28 feb. 2023
 * @Time 16:07:09
 */
?>

<?php
$backgroundColor = ['bg-mod-gradient-primary', 'bg-mod-gradient-info', 'bg-mod-gradient-success', 'bg-mod-gradient-warning', 'bg-mod-gradient-gray', ' bg-gradient-system'];
?>
<style>
    .mensaje-container {
        text-align: center;
        padding: 20px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .mensaje-texto {
        font-size: 18px;
        color: #333;
        margin-bottom: 15px;
    }

    .mensaje-enlace {
        color: #007bff;
        text-decoration: underline;
    }
</style>
<div class="container-fluid">

    <div class="row">
        <?php if (!empty($listaModulos)) { ?>
            <?php foreach ($listaModulos as $i => $mod) { ?>
                <div class="col-xl-3 col-md-6 col-lg-12">
                    <a href="<?= site_url(). $mod->md_url ?>/<?= $mod->id ?> " style="text-decoration: none">
                        <div class="info-box <?= $backgroundColor[$i] ?> ">

                            <span class="info-box-icon push-bottom"><i class="<?= $mod->md_icon ?>"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Módulo</span>
                                <span class="info-box-number"><?= $mod->md_nombre ?></span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 100%"> </div>
                                </div>
                                <div class="info-box-content text-right">
                                    <span class="progress-description">Ingresar al módulo <i class="fas fa-arrow-circle-up"></i></span>
                                </div>
                            </div>

                        </div>
                    </a>
                </div>
            <?php } ?>

        <?php } else { ?>
            <div class="mensaje-container">
                <p class="mensaje-texto">Usted no tiene asignado ningún módulo.</p>
                <p class="mensaje-texto">Póngase en contacto con el administrador del sistema.</p>
                <p class="mensaje-texto">Si tiene alguna pregunta, puede <a href="#" class="mensaje-enlace">contactarnos</a>.</p>
            </div>
        <?php } ?>
    </div>

</div>

