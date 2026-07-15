<?php
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

/**
 *
 * @author CRISTIAN R. PAZ
 * @date 4 dic 2025
 * @time 11:47:40 p.m.
 */
?>
<nav class="mt-2">
    <ul class=" nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false" >
        <li class="nav-header"></li>
        <li class="nav-item margin  menu-is-opening menu-open">
            <!--AJUSTES DE ENTRADA-->
            <a href="#" class="nav-link sidebarMenuColor"><i class="nav-icon fad fa-sort-amount-up me-3 sidebarColorIcon"></i><p>TRANSFERENCIAS<i class="fas fa-angle-left right text-white"></i></p></a>
            <ul class="nav nav-treeview">
               
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/transferencias/dashboard' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/transferencias/dashboard')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-dashboard nav-icon sidebarColorIcon"></i>
                        <p> Dashboard</p>
                    </a>
                </li>
                
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/transferencias/nuevaTransferencia' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/transferencias/nuevaTransferencia')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-folder-blank nav-icon sidebarColorIcon"></i>
                        <p> Nueva Transferencia</p>
                    </a>
                </li>

                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/transferencias/gestionTransferencias' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/transferencias/gestionTransferencias')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-clipboard-list nav-icon sidebarColorIcon"></i>
                        <p>Listar Transferencias</p>
                    </a>
                </li>


            </ul>
        </li>

        <hr>
        <li class="nav-header">MÓDULOS</li>
        <?php foreach ($listaModulos as $mod) { ?> 
            <li class="nav-item sidebarSubMenuColor">
                <a href="<?= site_url() . $mod->md_url . ($mod->tiene_submodulos ? '/' . $mod->id : '') ?>" class="nav-link">
                    <i class="nav-icon <?= $mod->md_icon ?> sidebarColorIcon"></i>
                    <p><?= $mod->md_nombre ?></p>
                </a>
            </li>
        <?php } ?>
    </ul>
</nav>
