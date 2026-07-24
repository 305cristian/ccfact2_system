<!DOCTYPE html>
<!--
/**
 * Description of sidebar
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 23 jul 2026
 * @time 10:26:44 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-header"></li>

        <li class="nav-item margin menu-is-opening menu-open">
            <a href="#" class="nav-link sidebarMenuColor">
                <i class="nav-icon fad fa-utensils me-3 sidebarColorIcon"></i>
                <p>BIO COMEDOR<i class="fas fa-angle-left right text-white"></i></p>
            </a>

            <ul class="nav nav-treeview">
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/biocomedor/dashboard' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/biocomedor/dashboard')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-dashboard nav-icon sidebarColorIcon"></i>
                        <p> Dashboard</p>
                    </a>
                </li>

                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/biocomedor/reportes' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/biocomedor/reportes')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-chart-bar nav-icon sidebarColorIcon"></i>
                        <p> Reportes</p>
                    </a>
                </li>

                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/biocomedor/terminal' }">
                    <a href="<?= site_url() ?>/biocomedor/terminal" target="_blank" class="nav-link" style="cursor: pointer">
                        <i class="far fa-tv nav-icon sidebarColorIcon"></i>
                        <p> Terminal</p>
                    </a>
                </li>

                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/biocomedor/comedores' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/biocomedor/comedores')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-building nav-icon sidebarColorIcon"></i>
                        <p> Comedores</p>
                    </a>
                </li>

                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/biocomedor/equipos' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/biocomedor/equipos')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-fingerprint nav-icon sidebarColorIcon"></i>
                        <p> Equipos</p>
                    </a>
                </li>

                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/biocomedor/contratistas' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/biocomedor/contratistas')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-industry-alt nav-icon sidebarColorIcon"></i>
                        <p> Contratistas</p>
                    </a>
                </li>

                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/biocomedor/departamentos' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/biocomedor/departamentos')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-sitemap nav-icon sidebarColorIcon"></i>
                        <p> Departamentos</p>
                    </a>
                </li>

                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/biocomedor/areas' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/biocomedor/areas')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-layer-group nav-icon sidebarColorIcon"></i>
                        <p> Areas</p>
                    </a>
                </li>

                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/biocomedor/proyectos' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/biocomedor/proyectos')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-project-diagram nav-icon sidebarColorIcon"></i>
                        <p> Proyectos</p>
                    </a>
                </li>

                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/biocomedor/comensales' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/biocomedor/comensales')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-users nav-icon sidebarColorIcon"></i>
                        <p> Comensales</p>
                    </a>
                </li>

                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/biocomedor/servicios' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/biocomedor/servicios')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-clock nav-icon sidebarColorIcon"></i>
                        <p> Servicios</p>
                    </a>
                </li>

                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/biocomedor/horarios' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/biocomedor/horarios')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-calendar-clock nav-icon sidebarColorIcon"></i>
                        <p> Horarios</p>
                    </a>
                </li>

                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/biocomedor/marcaciones' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/biocomedor/marcaciones')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-clipboard-list nav-icon sidebarColorIcon"></i>
                        <p> Marcaciones</p>
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
