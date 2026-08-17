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

    <?php if (!empty($seleccionarProyecto)) { ?>
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-7 col-md-9">
                <div class="card card-system card-outline shadow-sm mt-4">
                    <div class="card-header">
                        <div class=" card-title text-system fw-bold">
                            <i class="fas fa-project-diagram"></i> Seleccionar Proyecto
                        </div>
                    </div>
                    <div class="card-body">
                        <?php
                        $this->session = Config\Services::session();
                        $message = $this->session->get('message');
                        if (!empty($message)) {
                            $this->session->remove('message');
                            ?>
                            <div class="alert alert-warning"><?= $message ?></div>
                        <?php } ?>

                        <?php if (!empty($listaProyectosEmpleado)) { ?>
                            <form action="<?= site_url('welcome/seleccionarProyecto') ?>" method="post">
                                <label class="fw-bold mb-2">Proyecto de trabajo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-system-2 text-white">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    <select name="fk_proyecto" class="form-select" required>
                                        <option value="">Seleccione un proyecto</option>
                                        <?php foreach ($listaProyectosEmpleado as $proyecto) { ?>
                                            <option value="<?= $proyecto->id ?>">
                                                <?= $proyecto->proy_codigo ?> - <?= $proyecto->proy_nombre ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-check"></i> Continuar
                                    </button>
                                </div>
                            </form>
                        <?php } else { ?>
                            <div class="alert alert-danger mb-3">
                                No tiene proyectos activos asignados. Pongase en contacto con el administrador del sistema.
                            </div>
                        <?php } ?>

                        <div class="text-end mt-3">
                            <a href="<?= site_url('welcome/closeSession') ?>" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-sign-out-alt"></i> Cerrar sesion
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } else { ?>

    <div class="module-home-hero p-3 p-xl-4 mb-4">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <div class="module-home-eyebrow">Panel principal</div>
                    <h2 class="module-home-title mb-2">Bienvenido al sistema CCFACT</h2>
                    <p class="module-home-subtitle mb-0">
                        Seleccione un modulo para iniciar su operacion. Los accesos visibles dependen de los permisos asignados a su usuario.
                    </p>
                </div>
                <div class="col-lg-4">
                    <div class="module-home-project p-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="module-home-project-icon bg-gradient-system">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="module-home-project-label">Proyecto actual</div>
                                <div class="module-home-project-name text-truncate">
                                    <?= getProyectoCodigo() ?> - <?= getProyectoNombre() ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-5">
            <?php if (!empty($listaModulos)) { ?>
                <?php foreach ($listaModulos as $i => $mod) { ?>
                    <div class="col-xl-3 col-md-6 col-lg-12">
                        <a href="<?= site_url() . $mod->md_url . ($mod->tiene_submodulos ? '/' . $mod->id : '') ?>" style="text-decoration: none">
                            <div class="info-box <?= $backgroundColor[$i % count($backgroundColor)] ?>">
                                <span class="info-box-icon push-bottom"><i class="<?= $mod->md_icon ?>"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Módulo</span>
                                    <span class="info-box-number"><?= $mod->md_nombre ?></span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: 100%"> </div>
                                    </div>
                                    <div class="info-box-content text-right">
                                        <span class="progress-description">Ingresar al módulo<i class="fas fa-arrow-circle-up"></i></span>
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
    <?php } ?>

</div>
