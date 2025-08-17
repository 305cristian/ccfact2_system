<?php
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of sidebar
 * @author Cristian R. Paz
 * @Date 28 feb. 2023
 * @Time 17:14:04
 */
?>

<nav class="mt-2">
    <ul class=" nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false" >
       

        <hr>
        <li class="nav-header">MÓDULOS</li>
        <?php foreach ($listaModulos as $mod) { ?> 
            <li class="nav-item sidebarSubMenuColor">
                <a href="<?= site_url() . $mod->md_url ?>/<?= $mod->id ?> " class="nav-link">
                    <i class="nav-icon <?= $mod->md_icon ?> sidebarColorIcon"></i>
                    <p><?= $mod->md_nombre ?></p>
                </a>
            </li>
        <?php } ?>
    </ul>
</nav>
