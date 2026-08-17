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
        <li class="nav-header"></li>
        <li class="nav-item margin  menu-is-opening menu-open">
            <!--AJUSTES DE ENTRADA-->
            <a href="#" class="nav-link sidebarMenuColor"><i class="nav-icon fad fa-sort-amount-down-alt me-3 sidebarColorIcon"></i><p>INVENTARIOS<i class="fas fa-angle-left right text-white"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/inventarios/existencias' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/inventarios/existencias')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-dashboard nav-icon sidebarColorIcon"></i>
                        <p> Inicio</p>
                    </a>
                </li>
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/inventarios/viewInventarioGeneral' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/inventarios/viewInventarioGeneral')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-box nav-icon sidebarColorIcon"></i>
                        <p> Inventario General</p>
                    </a>
                </li>

                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/inventarios/viewInventarioLotes' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/inventarios/viewInventarioLotes')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-layer-group nav-icon sidebarColorIcon"></i>
                        <p>Inventario Por Lotes</p>
                    </a>
                </li>
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/inventarios/viewInventarioConsolidado' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/inventarios/viewInventarioConsolidado')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-random nav-icon sidebarColorIcon"></i>
                        <p>Inventario Consolidado</p>
                    </a>
                </li>
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/inv/historico' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/inv/historico')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-history nav-icon sidebarColorIcon"></i>
                        <p>Inventario Historico</p>
                    </a>
                </li>
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/control/caducidad' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/control/caducidad')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-clipboard-list nav-icon sidebarColorIcon"></i>
                        <p>Control de Caducidad</p>
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
