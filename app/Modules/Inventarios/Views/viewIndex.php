<?php
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of viewIndex
 * @author Cristian R. Paz
 * @Date 27 sep. 2023
 * @Time 17:25:01
 */
?>

<div class="container-fluid">

    <div class="row">
        <?php if ($listaSubModulos) {
        foreach ($listaSubModulos as $i => $smod) { ?>
            <div class="col-xl-3 col-md-6 col-lg-12">

                <a href="<?= site_url().$smod->md_url?>" style="text-decoration: none">
                    <div class="info-box bg-gradient-system">

                        <div class="info-box-content" style="border-right: 2px solid #ffffff">
                            <span class="info-box-text">SubMódulo</span>
                            <span class="info-box-number"><?= $smod->md_nombre ?></span>
                            <div class="info-box-text">
                                <span class="progress-description">Ingresar al SubMódulo <i class="fas fa-arrow-circle-right"></i></span>
                            </div>
                        </div>
                        <div class="p-4">
                            <span class="info-box-icon2 push-bottom"><i class="<?= $smod->md_icon ?>"></i></span>


                        </div>
                    </div>
                </a>
            </div>

        <?php } 
             }else{ ?>
        <h1>No hay submodulos otorgados a este usuario</h1>
        <?php } ?>

    </div>

</div>

