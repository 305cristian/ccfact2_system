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
            <a href="#" class="nav-link sidebarMenuColor"><i class="nav-icon fad fa-sort-amount-up me-3 sidebarColorIcon"></i><p>AJUSTES DE ENTRADA<i class="fas fa-angle-left right text-white"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/ajustesentrada/dashboard' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/ajustesentrada/dashboard')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-dashboard nav-icon sidebarColorIcon"></i>
                        <p> Dashboard</p>
                    </a>
                </li>
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/ajustesentrada/nuevoAjuste' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/ajustesentrada/nuevoAjuste')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-folder-blank nav-icon sidebarColorIcon"></i>
                        <p> Nuevo Ajuste</p>
                    </a>
                </li>

                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/ajustesentrada/gestionAjustes' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/ajustesentrada/gestionAjustes')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-clipboard-list nav-icon sidebarColorIcon"></i>
                        <p>Listar Ajustes</p>
                    </a>
                </li>

                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/ajustesentrada/ajusteInicial' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/ajustesentrada/ajusteInicial')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-file-upload nav-icon sidebarColorIcon"></i>
                        <p>Cragar Ajuste Inicial</p>
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
