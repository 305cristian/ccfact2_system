<?php
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of sidebar
 *
  /**
 * @author CRISTIAN R. PAZ
 * @date 3 may 2026
 * @time 4:02:23 p.m.
 */
?>

<nav class="mt-2">
    <ul class=" nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false" >
        <li class="nav-header"></li>
        <li class="nav-item margin  menu-is-opening menu-open">
            <!--COMPRAS-->
            <a href="#" class="nav-link sidebarMenuColor"><i class="nav-icon fad fa-shopping-cart me-3 sidebarColorIcon"></i><p>COMPRAS<i class="fas fa-angle-left right text-white"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/compras/dashboard' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/compras/dashboard')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-dashboard nav-icon sidebarColorIcon"></i>
                        <p> Dashboard</p>
                    </a>
                </li>
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/compras/nuevaCompra' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/compras/nuevaCompra')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-shopping-bag nav-icon sidebarColorIcon"></i>
                        <p> Nueva Compra</p>
                    </a>
                </li>
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/compras/gestionCompras' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/compras/gestionCompras')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-clipboard-list nav-icon sidebarColorIcon"></i>
                        <p> Listar Compras</p>
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