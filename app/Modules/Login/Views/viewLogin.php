<?php
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of login
 * @author Cristian R. Paz
 * @Date 30 ene. 2023
 * @Time 10:56:20
 */
?>
<?php
$systemDevelop = function_exists('getSettings') ? (int) getSettings('SYSTEM_DEVELOP') : 2;
$ambientesSistema = [
    0 => [
        'texto' => 'AMBIENTE LOCAL',
        'descripcion' => 'Sistema ejecutandose en entorno local',
        'clase' => 'local',
        'icono' => 'fa-laptop-code',
        'mostrar_franga' => true,
    ],
    1 => [
        'texto' => 'AMBIENTE DE PRUEBAS QA',
        'descripcion' => 'Sistema ejecutandose en entorno de pruebas',
        'clase' => 'qa',
        'icono' => 'fa-vial',
        'mostrar_franga' => true,
    ],
    2 => [
        'texto' => 'PRODUCCION',
        'descripcion' => 'Sistema productivo',
        'clase' => 'prod',
        'icono' => 'fa-check-circle',
        'mostrar_franga' => false,
    ],
];
$ambienteSistema = $ambientesSistema[$systemDevelop] ?? $ambientesSistema[2];
?>
<style>
    body.login-page {
        min-height: 100vh;
        background:
            radial-gradient(circle at 15% 20%, rgba(25, 135, 84, .16), transparent 28rem),
            radial-gradient(circle at 85% 12%, rgba(13, 110, 253, .12), transparent 26rem),
            linear-gradient(135deg, #eef4f1 0%, #f8fafc 48%, #e9f1f5 100%);
    }

    .login-card-main {
        border-radius: 8px;
        box-shadow: 0 24px 60px rgba(15, 23, 42, .18);
    }

    .login-showcase {
        background:
            radial-gradient(circle at 12% 8%, rgba(255, 255, 255, .16), transparent 18rem),
            linear-gradient(145deg, #173f4f 0%, #276b73 52%, #123140 100%);
    }

    .login-module-card {
        background: linear-gradient(180deg, rgba(255, 255, 255, .18), rgba(255, 255, 255, .09));
        border: 1px solid rgba(255, 255, 255, .18);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .12);
    }

    .login-module-card i {
        color: #d8f5f1;
    }

    .login-stat-card {
        background: rgba(255, 255, 255, .12);
        border: 1px solid rgba(255, 255, 255, .18);
    }

    .login-form-panel {
        background:
            linear-gradient(180deg, #ffffff 0%, #f7fbfb 100%);
    }

    .login-input-icon {
        width: 46px;
        justify-content: center;
        color: #0f766e;
        background-color: #f2fbfa;
        border-color: #d3e7e5;
    }

    .login-form-control {
        border-color: #d3e7e5;
        min-height: 42px;
    }

    .login-form-control:focus {
        border-color: #0f766e;
        box-shadow: 0 0 0 .18rem rgba(15, 118, 110, .14);
    }

    .login-env-banner {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1050;
        min-height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
        box-shadow: 0 8px 22px rgba(15, 23, 42, .13);
    }

    .login-env-banner.local {
        background: linear-gradient(90deg, #d97706, #f59e0b, #b45309);
    }

    .login-env-banner.qa {
        background: linear-gradient(90deg, #0369a1, #0ea5e9, #075985);
    }

    body.login-env-has-banner main {
        padding-top: 58px;
    }

    .login-env-badge {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        border-radius: 999px;
        padding: .45rem .85rem;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    .login-env-badge.local {
        color: #92400e;
        background: #fffbeb;
        border: 1px solid #fcd34d;
    }

    .login-env-badge.qa {
        color: #075985;
        background: #e0f2fe;
        border: 1px solid #7dd3fc;
    }

    .login-env-badge.prod {
        color: #166534;
        background: #f0fdf4;
        border: 1px solid #86efac;
    }
</style>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="<?php echo base_url(); ?>resources/plugins/bootstrap5/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>resources/plugins/fontawesome/css/all.css">
    </head>
    <title><?= $title ?></title>
    <body class="login-page <?= $ambienteSistema['mostrar_franga'] ? 'login-env-has-banner' : '' ?>">
        <?php
        $this->session = Config\Services::session();
        $sessionOf = $this->session->get('message');
        $validationText = trim(strip_tags((string) ($validation ?? '')));
        ?>

        <?php if ($ambienteSistema['mostrar_franga']) { ?>
            <div class="login-env-banner <?= $ambienteSistema['clase'] ?>">
                <i class="fas <?= $ambienteSistema['icono'] ?> me-2"></i>
                <?= $ambienteSistema['texto'] ?>
            </div>
        <?php } ?>

        <main class="min-vh-100 d-flex align-items-center py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-xl-11">
                        <div class="card border-0 overflow-hidden login-card-main">
                            <div class="row g-0">
                                <div class="col-lg-7 text-white login-showcase">
                                    <div class="p-4 p-xl-5 h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            <span class="badge bg-light text-dark mb-4 px-3 py-2">
                                                <i class="fas fa-shield-alt me-1"></i> ERP CCFACT
                                            </span>

                                            <h1 class="fw-bold mb-3">Gestiona tu empresa desde una sola plataforma.</h1>
                                            <p class="mb-4 text-white-50">
                                                Controla compras, ventas, inventario, contabilidad, proyectos y bio comedor
                                                con información clara para tomar decisiones.
                                            </p>

                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="rounded p-3 h-100 login-module-card">
                                                        <i class="fas fa-shopping-cart fs-4 mb-2"></i>
                                                        <h6 class="fw-bold mb-1">Compras</h6>
                                                        <small class="text-white-50">Documentos, pagos y cartera.</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="rounded p-3 h-100 login-module-card">
                                                        <i class="fas fa-boxes fs-4 mb-2"></i>
                                                        <h6 class="fw-bold mb-1">Inventario</h6>
                                                        <small class="text-white-50">Kardex, lotes, bodegas y costos.</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="rounded p-3 h-100 login-module-card">
                                                        <i class="fas fa-cash-register fs-4 mb-2"></i>
                                                        <h6 class="fw-bold mb-1">Ventas</h6>
                                                        <small class="text-white-50">Documentos, cobros y cartera.</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="rounded p-3 h-100 login-module-card">
                                                        <i class="fas fa-utensils fs-4 mb-2"></i>
                                                        <h6 class="fw-bold mb-1">Bio comedor</h6>
                                                        <small class="text-white-50">Marcaciones, consumos y servicios.</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-2 mt-4 justify-content-center">
                                            <div class="col-5">
                                                <div class="rounded p-2 login-stat-card">
                                                    <div class="fw-bold">ERP</div>
                                                    <small class="text-white-50">Modular</small>
                                                </div>
                                            </div>
<!--                                            <div class="col-4">
                                                <div class="rounded p-2 login-stat-card">
                                                    <div class="fw-bold">SRI</div>
                                                    <small class="text-white-50">Procesos</small>
                                                </div>
                                            </div>-->
                                            <div class="col-5">
                                                <div class="rounded p-2 login-stat-card">
                                                    <div class="fw-bold">Multi</div>
                                                    <small class="text-white-50">Proyecto</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-5 login-form-panel">
                                    <div class="p-4 p-xl-5 h-100 d-flex align-items-center">
                                        <div class="w-100">
                                            <div class="text-center mb-4">
                                                <img src="<?php echo base_url() ?>uploads/img/enterprice/logo.png" class="img-fluid mb-3" style="max-width: 160px;" alt="Logo CCFact">
                                                <div class="fw-bold text-uppercase small mb-2" style="color: #0f766e;">Bienvenido</div>
                                                <h3 class="fw-bold text-dark mb-2">Acceso al sistema</h3>
                                                <p class="text-muted mb-0">Ingresa tus credenciales para continuar.</p>
                                               
                                            </div>

                                            <?php if (!empty($validationText)) { ?>
                                                <div class="alert alert-warning">
                                                    <?= $validation; ?>
                                                </div>
                                            <?php } ?>

                                            <?php if (!empty($sessionOf)) { ?>
                                                <div class="alert alert-danger">
                                                    <i class="fas fa-exclamation-circle me-1"></i> <?= $sessionOf; ?>
                                                </div>
                                            <?php } ?>

                                            <form action="<?php echo site_url() ?>/index/login" method="post" autocomplete="on">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold" for="username">Usuario</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text login-input-icon">
                                                            <i class="far fa-user"></i>
                                                        </span>
                                                        <input
                                                            type="text"
                                                            id="username"
                                                            name="username"
                                                            class="form-control login-form-control"
                                                            placeholder="Ingrese su usuario"
                                                            autocomplete="username"
                                                            required>
                                                    </div>
                                                </div>

                                                <div class="mb-4">
                                                    <label class="form-label fw-bold" for="pass">Contrase&ntilde;a</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text login-input-icon">
                                                            <i class="far fa-key"></i>
                                                        </span>
                                                        <input
                                                            type="password"
                                                            id="pass"
                                                            name="pass"
                                                            class="form-control login-form-control"
                                                            placeholder="Ingrese su contrase&ntilde;a"
                                                            autocomplete="current-password"
                                                            required>
                                                    </div>
                                                </div>

                                                <button class="btn w-100 fw-bold py-2 text-white" style="background-color: #0f766e;" type="submit">
                                                    <i class="far fa-unlock-alt me-1"></i> Iniciar sesi&oacute;n
                                                </button>
                                                
                                                 <div class="mt-3 text-center">
                                                    <span class="login-env-badge <?= $ambienteSistema['clase'] ?>" title="<?= $ambienteSistema['descripcion'] ?>">
                                                        <i class="fas <?= $ambienteSistema['icono'] ?>"></i>
                                                        <?= $ambienteSistema['texto'] ?>
                                                    </span>
                                                </div>

                                                <div class="text-center mt-4">
                                                    <small class="text-muted">
                                                        &iquest;Problemas para ingresar? Contacte al administrador del sistema.
                                                    </small>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
<script src="<?php echo base_url(); ?>/resources/plugins/bootstrap5/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo base_url(); ?>/resources/plugins/fontawesome/js/all.js"></script>
