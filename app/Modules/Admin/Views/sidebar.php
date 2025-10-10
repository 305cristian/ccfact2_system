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
        <li class="nav-header">MANAGAMENT</li>
        <li class="nav-item margin">
            <!--PRODUCTOS-->
            <a href="#" class="nav-link sidebarMenuColor"><i class="nav-icon fas fa-basket-shopping sidebarColorIcon"></i><p>Productos<i class="fas fa-angle-left right text-white"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/productos/managamentProductos' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/productos/managamentProductos')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p> Producto</p>
                    </a>
                </li>

                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/grupos/managamentGrupos' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/grupos/managamentGrupos')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Grupos</p>
                    </a>
                </li>

                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/marcas/managamentMarcas' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/marcas/managamentMarcas')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Marcas</p>
                    </a>
                </li>
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/tiposprod/managamentTiposProd' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/tiposprod/managamentTiposProd')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Tipo de Producto</p>
                    </a>
                </li>
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/tiposprice/namagamentTipoPrecio' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/tiposprice/namagamentTipoPrecio')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Tipo de Precio</p>
                    </a>
                </li>
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/medida/managamentUnidadesMedida' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/medida/managamentUnidadesMedida')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Unidad De Medida</p>
                    </a>
                </li>

            </ul>
        </li>
        <!--CLIENTES-->
        <li class="nav-item margin">
            <a href="#" class="nav-link sidebarMenuColor"><i class="nav-icon fas fa-user-tie sidebarColorIcon"></i><p>Clientes<i class="fas fa-angle-left right text-white"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/clientes/managamentClientes' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/clientes/managamentClientes')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Cliente</p>
                    </a>
                </li>             
            </ul>
        </li>

        <!--PROVEEDORES-->
        <li class="nav-item margin">
            <a href="#" class="nav-link sidebarMenuColor"><i class="nav-icon fas fa-user-tie sidebarColorIcon"></i><p>Proveedores<i class="fas fa-angle-left right text-white"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/proveedores/managamentProveedores' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/proveedores/managamentProveedores')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Proveedor</p>
                    </a>
                </li>           
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/sectores/managamentSectores' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/sectores/managamentSectores')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Sectores</p>
                    </a>
                </li>           
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/anillos/managamentAnillos' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/anillos/managamentAnillos')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Anillos</p>
                    </a>
                </li>           
            </ul>

        </li>

        <!--Empresa-->
        <li class="nav-item margin">
            <a href="#" class="nav-link sidebarMenuColor"><i class="nav-icon fas fa-house-building sidebarColorIcon"></i><p>Empresa<i class="fas fa-angle-left right text-white"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/enterprice/managamentEmpresa' }">
                    <!--<a href="<?= site_url() ?>/admin/enterprice/managamentEmpresa" class="nav-link">-->
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/enterprice/managamentEmpresa')" class="nav-link" style="cursor: pointer">

                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Datos Empresa</p>
                    </a>
                </li>
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/managamentRoles' }">
                    <!--<a href="<?= site_url() ?>/admin/managamentRoles" class="nav-link">-->
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/managamentRoles')" class="nav-link" style="cursor: pointer">
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Administrar Roles</p>
                    </a>
                </li>

                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/managamentEmpleado' }">                  
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/managamentEmpleado')" class="nav-link" style="cursor: pointer">
                    <!--<a href="<?= site_url() ?>/admin/managamentEmpleado" class="nav-link">-->
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Usuarios y Roles</p>
                    </a>
                </li>

                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/bodegas/managamentBodegas' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/bodegas/managamentBodegas')" class="nav-link" style="cursor: pointer">
                   <!--<a href="<?= site_url() ?>/admin/bodegas/managamentBodegas" class="nav-link">-->
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Bodegas</p>
                    </a>
                </li>
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/cc/managamentCC' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/cc/managamentCC')" class="nav-link" style="cursor: pointer">
                    <!--<a href="<?= site_url() ?>/admin/cc/managamentCC" class="nav-link">-->
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Centros de Costos</p>
                    </a>
                </li>
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/sustento/managamentSustentos' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/sustento/managamentSustentos')" class="nav-link" style="cursor: pointer">
                    <!--<a href="<?= site_url() ?>/admin/sustento/managamentSustentos" class="nav-link">-->
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Sustentos</p>
                    </a>
                </li>
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/pventa/managamentPuntosVenta' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/pventa/managamentPuntosVenta')" class="nav-link" style="cursor: pointer">
                    <!--<a href="<?= site_url() ?>/admin/pventa/managamentPuntosVenta" class="nav-link">-->
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Puntos de Venta</p>
                    </a>
                </li>
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/motivos/managamentMotivos' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/motivos/managamentMotivos')" class="nav-link" style="cursor: pointer">
                    <!--<a href="<?= site_url() ?>/admin/pventa/managamentPuntosVenta" class="nav-link">-->
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Motivos de Ajustes</p>
                    </a>
                </li>

            </ul>

        </li>

        <!--CONTABILIDAD-->
        <li class="nav-item margin">
            <a href="#" class="nav-link sidebarMenuColor"><i class="nav-icon fas fa-dollar-circle sidebarColorIcon"></i><p>Contabilidad<i class="fas fa-angle-left right text-white"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/cuentascontables/managamentCuentas' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/cuentascontables/managamentCuentas')" class="nav-link"  style="cursor: pointer">
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Plan de Cuentas</p>
                    </a>
                </li>
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/cuentasconfig/managamentCuentasConfig' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/cuentasconfig/managamentCuentasConfig')" class="nav-link"  style="cursor: pointer">
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Conf. Cuentas</p>
                    </a>
                </li>
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/retenciones/managamentRetenciones' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/retenciones/managamentRetenciones')" class="nav-link"  style="cursor: pointer">
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Retenciones</p>
                    </a>
                </li>
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/bancos/managamentBancos' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/bancos/managamentBancos')" class="nav-link"  style="cursor: pointer">
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Bancos</p>
                    </a>
                </li>

            </ul>

        </li>

        <!--SETTINGS-->
        <li class="nav-item margin">
            <a href="#" class="nav-link sidebarMenuColor"><i class="nav-icon fas fa-cogs sidebarColorIcon"></i><p>Settings<i class="fas fa-angle-left right text-white"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/trans/managamentTransacciones' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/trans/managamentTransacciones')" class="nav-link" style="cursor: pointer">
                    <!--<a href="<?= site_url() ?>/admin/trans/managamentTransacciones" class="nav-link">-->
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Tipos de Transacción</p>
                    </a>
                </li>
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/modacc/managamentModAcc' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/modacc/managamentModAcc')" class="nav-link" style="cursor: pointer">
                    <!--<a href="<?= site_url() ?>/admin/modacc/managamentModAcc" class="nav-link">-->
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Módulos y Acciones </p>
                    </a>
                </li>
                <li class="nav-item sidebarSubMenuColor" :class="{ 'bg-system': pathname === '<?= site_url() ?>/admin/sett/managamentSettings' }">
                    <a @click.prevent="navigate('<?= site_url() ?>/admin/sett/managamentSettings')" class="nav-link" style="cursor: pointer">
                    <!--<a href="<?= site_url() ?>/admin/sett/managamentSettings" class="nav-link">-->
                        <i class="far fa-circle nav-icon sidebarColorIcon"></i>
                        <p>Variables de Configuración</p>
                    </a>
                </li>


            </ul>

        </li>


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
