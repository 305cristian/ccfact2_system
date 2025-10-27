

<!DOCTYPE html>
<html lang="es">

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">

        <?php if (!empty($title)): ?>           
            <title ><?= $title ?></title>
        <?php else: ?>       
            <title >Sistema CCFACT</title>      
        <?php endif ?>
        <?php
        $this->session = Config\Services::session();
        $nombres = $this->session->get('nombres');
        $apellidos = $this->session->get('apellidos');
        $fotoUser = $this->session->get('foto');
        $userId = $this->session->get('id');

        $foto = $fotoUser;
        if (empty($fotoUser)) {
            $foto = 'user.png';
        }
        $enterprice = enterprice();
        ?>

        <!--constantes globales para archivos js--> 
    <input type="hidden" id="base_Url" value="<?php echo base_url(); ?>">
    <input type="hidden" id="site_Url" value="<?php echo site_url(); ?>">
    <input type="hidden" id="user_Id" value="<?php echo $userId ?>">


    <!--ESTILOS CSS-->
    <!--estilos para iconos-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>/resources/plugins/fontawesome/css/all.css">

    <!--estilos para alertas-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>/resources/plugins/toast/toastr.css"

          <!--estilos para sidebar-->
          <link rel="stylesheet" href="<?php echo base_url(); ?>/resources/plugins/adminlte/OverlayScrollbars.min.css">

    <!--estilos del dashboard del sistema-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>/resources/plugins/adminlte/adminlte.min.css">

    <!--estilos bootstrap 5-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>/resources/plugins/bootstrap5/css/bootstrap.min.css">

    <!--selectpicker-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>/resources/plugins/selectpicker/bootstrap-select.min.css">


    <!--datatables bootstrap 5-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>/resources/plugins/dataTables/DataTables-1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>/resources/plugins/dataTables/Responsive-2.5.0/css/responsive.bootstrap5.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>/resources/plugins/dataTables/Buttons-2.4.2/css/buttons.bootstrap5.min.css">

    <!--estilos para vue multiselect y search-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>/resources/plugins/vueMultiselect/vue-multiselect.min.css">

    <!--estilos para vue select y search-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>/resources/plugins/vueSelect/vue-select.css">


    <!--estilos genericos-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>/resources/css/cclibrary.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>/resources/css/styleModules.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>/resources/css/sidebar.css">


    <!--LIBRERIAS DE CODIGO JS-->
    <script>
//      TODO:constantes globales
        var baseUrl = base_Url.value;
        var siteUrl = site_Url.value;
        var userId = user_Id.value;

//        TODO: CONSTANTES GOBALES EMPRESA
        var emp_ruc = '<?= $enterprice->epr_ruc; ?>';
        var emp_telefono = '<?= $enterprice->epr_telefono; ?>';
        var emp_celular = '<?= $enterprice->epr_celular; ?>';
        var emp_email = '<?= $enterprice->epr_email; ?>';
        var emp_logo = '<?= $enterprice->epr_logo; ?>';
        var emp_nombre = '<?= $enterprice->epr_nombre_comercial; ?>';
        var emp_direccion = '<?= $enterprice->epr_direccion; ?>';
        var emp_website = '<?= $enterprice->epr_pagina_web; ?>';

        var rootElement = document.documentElement;
        var colorSystem = '<?php echo themeSelect($userId) ?>';
        var color = colorSystem.split(',')[0];
        var color2 = colorSystem.split(',')[1];
        rootElement.style.setProperty("--colorSystem", color);
        rootElement.style.setProperty("--colorSystem2", color2);

        window.empresa = {
            ruc: '<?= $enterprice->epr_ruc; ?>',
            telefono: '<?= $enterprice->epr_telefono; ?>',
            celular: '<?= $enterprice->epr_celular; ?>',
            email: '<?= $enterprice->epr_email; ?>',
            logo: '<?= $enterprice->epr_logo; ?>',
            nombre: '<?= $enterprice->epr_nombre_comercial; ?>',
            direccion: '<?= $enterprice->epr_direccion; ?>',
            website: '<?= $enterprice->epr_pagina_web; ?>'
        };

    </script>   

    <!--JQUERY-->
    <script src="<?php echo base_url(); ?>/resources/plugins/jquery-3.5.1.min.js"></script>

    <!--VUE 2.7 Y AXIOS-->
    <!--<script src="<?php // echo base_url();               ?>/resources/plugins/vue/vue2.7.js"></script>-->
    <script src="<?php echo base_url(); ?>/resources/plugins/vue/vue.global_3.5.min.js"></script>
    <script src="<?php echo base_url(); ?>/resources/plugins/axios/axios.min.js"></script>

    <!-- VUE-MULTISELECT-->
    <script src="<?php echo base_url(); ?>/resources/plugins/vueMultiselect/vue-multiselect.min.js"></script>

    <!-- VUE-SELECT-->
    <script src="<?php echo base_url(); ?>/resources/plugins/vueSelect/vue-select.min.js"></script>

    <!--libreria del dashboard-->
    <script src="<?php echo base_url(); ?>/resources/plugins/adminlte/jquery.overlayScrollbars.min.js"></script>
    <script src="<?php echo base_url(); ?>/resources/plugins/adminlte/adminlte.min.js"></script>

    <!--BOOTSTRAP 5-->
    <script src="<?php echo base_url(); ?>/resources/plugins/bootstrap5/js/bootstrap.bundle.min.js"></script>


    <!--selectpicker-->
    <script src="<?php echo base_url(); ?>/resources/plugins/selectpicker/bootstrap-select.js"></script>


    <!-- para datatable con bootstrap-->
    <script src="<?php echo base_url(); ?>/resources/plugins/dataTables/DataTables-1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url(); ?>/resources/plugins/dataTables/DataTables-1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!--<script src="<?php echo base_url(); ?>/resources/plugins/dataTables/Responsive-2.5.0/js/dataTables.responsive.min.js"></script>-->
    <!--<script src="<?php echo base_url(); ?>/resources/plugins/dataTables/Responsive-2.5.0/js/responsive.bootstrap5.min.js"></script>-->

    <!-- para botones en datatable con bootstrap-->
    <script src="<?php echo base_url(); ?>/resources/plugins/dataTables/Buttons-2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="<?php echo base_url(); ?>/resources/plugins/dataTables/Buttons-2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="<?php echo base_url(); ?>/resources/plugins/dataTables/JSZip-3.10.1/jszip.min.js"></script>
    <script src="<?php echo base_url(); ?>/resources/plugins/dataTables/pdfmake-0.2.7/pdfmake.min.js"></script>
    <script src="<?php echo base_url(); ?>/resources/plugins/dataTables/pdfmake-0.2.7/vfs_fonts.js"></script>
    <script src="<?php echo base_url(); ?>/resources/plugins/dataTables/Buttons-2.4.2/js/buttons.colVis.min.js"></script>
    <script src="<?php echo base_url(); ?>/resources/plugins/dataTables/Buttons-2.4.2/js/buttons.html5.min.js"></script>
    <script src="<?php echo base_url(); ?>/resources/plugins/dataTables/Buttons-2.4.2/js/buttons.print.min.js"></script>
    <script src="<?php echo base_url(); ?>/resources/plugins/dataTables/dataTableGeneric.js"></script>

    <!-- para alertas-->
    <script src="<?php echo base_url(); ?>/resources/plugins/toast/toastr.min.js"></script>
    <script src="<?php echo base_url(); ?>/resources/plugins/toast/alert.js"></script>
    <script src="<?php echo base_url(); ?>/resources/plugins/sweetAlert2/sweetAlert2_11.js"></script>

    <!-- para fechas-->
    <script src="<?php echo base_url(); ?>/resources/plugins/luxonDate/luxon.min.js"></script>
    <script>  var {DateTime} = luxon;</script>

    <!-- para exportacion excel-->
    <script src="<?php echo base_url(); ?>/resources/plugins/excel/xlsx.full.min.js"></script>

    <!-- para exportacion pdf-->
    <script src="<?php echo base_url(); ?>/resources/plugins/html2canva/html2canvas.min.js"></script>
    <script src="<?php echo base_url(); ?>/resources/plugins/html2pdf/html2pdf.bundle.min.js"></script>
    <script src="<?php echo base_url(); ?>/resources/plugins/jspdf/jspdf.umd.min.js"></script>
    <script src="<?php echo base_url(); ?>/resources/plugins/jspdf/jspdf.plugin.autotable.min.js"></script>
    <script src="<?php echo base_url(); ?>/resources/js/PDFExport.js"></script>

    <!-- libreria helper-->
    <script src="<?php echo base_url(); ?>/resources/js/cclibrary.js"></script>
    <script src="<?php echo base_url(); ?>/resources/js/directivasVue3.js"></script>
    <script src="<?php echo base_url(); ?>/resources/js/ExcelExport.js"></script>
    <script src="<?php echo base_url(); ?>/resources/js/PDFCapture.js"></script>





</head>

<!--<body class="hold-transition sidebar-mini">-->
<?php if (!empty($pathname) == 'welcome') { ?>
    <body  class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed sidebar-collapse">

    <?php } else { ?>
    <body  class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">

    <?php } ?>
    <div  class="wrapper">
        <div  id="v_app">
            <nav   class="navbar navbar-expand-lg bg-gradient-system fixed-top main-header"><!--clase main-header de anmintle-->
                <div class="ml-2">
                    <button class="btn btn-outline-light" data-widget="pushmenu" id="menu-toggle"><span class="fas fa-bars"></span></button>
                </div>
                <div class="sidebar-heading text-white font-weight-bold ml-2">
                    <a class="nav-link text-white" href="<?php echo site_url(); ?>">Sistema Web</a><!--Solo especifica la base url base como esta logeado lo redirecciona al dasboard principal-->
                </div>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="fas fa-bars text-white"></span>
                </button>

                <div  class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto mt-2 mt-lg-0 ">

                        <li class="nav-item active">
                            <a class="nav-link text-white" href="" data-bs-target="#modalSoporte" data-bs-toggle="modal"><span><i class="fas fa-user-doctor-message qx"></i></span> Soporte</a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link text-white" href="#"><span><i class="fas fa-check-circle"></i></span> | <span><i class="fas fa-user-tie"></i> <?= $nombres . ' ' . $apellidos ?> </span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-widget="fullscreen" href="#" role="button" style="color: white">
                                <i class="fas fa-expand-arrows-alt"></i>
                            </a>
                        </li>

                        <li class="nav-item dropdown">

                            <a class="nav-link text-white dropdown-toggle" href="#"  data-bs-toggle="dropdown"  role="button" aria-expanded="false"><span><i class="fas fa-door-closed"></i> </span></a>

                            <div class=" dropdown-menu dropdown-menu-lg dropdown-menu-left dropdown-menu-end text-center">
                                <img style="width: 70%" src="<?php echo base_url('uploads/img/employee/' . $foto); ?>" class="img-circle" alt="Imagen">
                                <span class=" dropdown-header"><strong>SISTEMA CCFACT</strong></span>

                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item">
                                    <button  type="button" data-bs-target="#modalPerfil"  data-bs-toggle="modal" @click='getDataEmpleado()' class="btn btn-primary btn-sm btn-flat"><i class="fas fa-user-tie"></i> Perfil</button>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item">
                                    <button  type="button" data-bs-target="#modalThemes"  data-bs-toggle="modal" class="btn btn-secondary btn-sm btn-flat"><i class="fas fa-monitor-waveform"></i> Themes</button>
                                </a>
                                <div class="dropdown-divider"></div>
                                <div href="#" class="dropdown-item">
                                    <a type="button"  href="<?php echo site_url(); ?>/welcome/closeSession" class="btn btn-danger btn-sm btn-flat"><i class="fas fa-door-closed"></i> Log Out</a>
                                </div>

                                <div class="dropdown-divider"></div>

                                <a href="#" class="dropdown-item dropdown-footer">See All Options</a>
                            </div>

                        </li>
                    </ul>
                </div>

            </nav>
            <div class="modal fade" id="modalPerfil" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <?php echo view('\Modules\Admin\Views\employee\viewPerfil') ?>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalThemes" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <?php echo view('\Modules\Admin\Views\employee\viewThemes') ?>
                    </div>
                </div>
            </div>

            <!--Inicio Modal soporte-->
            <div id="modalSoporte" class="modal fade" data-bs-backdrop=static data-bs-keyboard=false>
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4><i class="fas fa-user-doctor"></i> Contactos</h4>
                        </div>
                        <div class="modal-body">
                            <div class="container">
                                <p>
                                    <label><i class="fas fa-phone"></i> Telefono: 0992094788</label><br>
                                    <label><i class="fas fa-whatsapp"></i> Whatsapp: 0992094788</label><br>
                                    <label><i class="fas fa-mail-bulk"></i> Correo: pcris.994@gmail.com</label>
                                </p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-close"></i> Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--Fin Modal soporte-->
        </div>
        <aside id="app2" class="main-sidebar elevation-4 bg-gradient-system">

            <a href="<?php echo site_url(); ?>/welcome" class="brand-link" style="text-decoration: none; color: white">
                <img src="<?php echo base_url() ?>/uploads/img/enterprice/logo.png" alt="Logo" height="50" class="brand-image img-circle elevation-3" style="opacity: .9">
                <span class="brand-text font-weight-bold">Ccomputers</span>
            </a>
            <div class="sidebar">  

                <!--Sidebar Menu--> 
                <?php
                if (!empty($sidebar)) {
                    echo $sidebar;
                } else {
                    //echo 'ccfact';
                }
                ?>

            </div>
        </aside>

        <!-- DE AQUI EN ADELANTE EMPIESA EL CONTENT MAIN-->
        <div class="content-wrapper mt-5">
            <div class="row">
                <div id='content-main-vue' class="content-header mt-3 pl-4 pr-3">

                    <?php echo $view; ?>

                </div> 

            </div> 
        </div>
        <!-- HASTA AQUI TERMINA EL CONTENT MAIN-->



        <footer class="main-footer">
            <!-- To the right -->
            <div class="float-right d-none d-sm-inline">
                Anything you want
            </div>
            <!-- Default to the left -->
            <strong>Copyright &copy; <?php echo date('Y') ?> <a href="#">Ccomputers</a>.</strong> All rights reserved.
        </footer>


    </div>     
</body>

</html>

<script type="text/javascript">

        if (window.appNavigate) {
            window.appNavigate.unmount();
        }
        window.appNavigate = Vue.createApp({

            data() {
                return {
                    pathname: 'url',
                    'bg-system': 'bg-system'
                }
            },
            methods: {
                async navigate(uri) {
                    this.pathname = uri;
                    try {
                        swalLoading('Cargando vista...');
                        let response = await axios.get(uri);
                        if (response) {
                            $('#content-main-vue').html(response.data.view);
                            Swal.close();
                        }
                    } catch (e) {
                        sweet_msg_toast('error', e.response.data.message);
                    }
                }
            }
        });
        appNavigate.mount('#app2');

        if (window.appDashboard) {
            window.appDashboard.unmount();
        }

        window.appDashboard = Vue.createApp({

            data() {
                return {
                    url: siteUrl,
                    dataEmpleado: {},
                    passActual: '',
                    passNew: '',
                    passConfNew: '',
                    colors: [
                        {id: 0, hex: "#f12711", disabled: false},
                        {id: 1, hex: "#f5af19", disabled: false}
                    ],
                    id: 2
                }

            },

            computed: {
                gradient() {
                    let colors = "linear-gradient(45deg";
                    this.colors.forEach(function (e) {
                        colors += "," + e.hex;
                    });
                    colors += ")";
                    return colors;
                }
            },

            methods: {

                async getDataEmpleado() {

                    let datos = {
                        idUser: userId
                    };
                    let response = await  axios.post(this.url + '/admin/empleado', datos);
                    if (response.data) {
                        this.dataEmpleado = {
                            nombre: response.data.emp_nombre,
                            apellido: response.data.emp_apellido,
                            usuario: response.data.emp_username,
                            email: response.data.emp_email,
                            celular: response.data.emp_celular,
                            idUser: response.data.id
                        };
                    }
                },
                async updateEmployee() {
                    if (!this.dataEmpleado.usuario) {
                        sweet_msg_toast('warning', 'El nombre de usuario no puede estar vacio');
                        return false;
                    }
                    if (this.dataEmpleado.usuario.length <= 3) {
                        sweet_msg_toast('warning', 'El nombre de usuario debe contener al menos 4 caracteres');
                        return false;
                    }
                    if (!this.dataEmpleado.nombre) {
                        sweet_msg_toast('warning', 'El nombre del empleado no puede estar vacio');
                        return false;
                    }
                    if (!this.dataEmpleado.apellido) {
                        sweet_msg_toast('warning', 'El apellido del empleado no puede estar vacio');
                        return false;
                    }
                    if (!this.dataEmpleado.email) {
                        sweet_msg_toast('warning', 'El email del empleado no puede estar vacio');
                        return false;
                    }
                    if (!this.dataEmpleado.celular) {
                        sweet_msg_toast('warning', 'El celular del empleado no puede estar vacio');
                        return false;
                    }
                    try {
                        let datos = this.dataEmpleado;
                        let response = await axios.post(this.url + '/admin/updateEmployee', datos);
                        if (response) {
                            sweet_msg_dialog('success', 'Datos actualizados exitosamente', '/');
                        }
                    } catch (e) {
                        sweet_msg_dialog('error', '', '', e);
                    }

                },
                async resetPassword() {
                    if (this.passNew.length <= 3) {
                        sweet_msg_toast('warning', 'La contraseña debe contener al menos 4 caracteres');
                        return false;
                    }
                    try {
                        let datos = {
                            passActual: this.passActual,
                            passNew: this.passNew,
                            passConfNew: this.passConfNew
                        };
                        let response = await axios.post(this.url + '/admin/resetPassword', datos);
                        if (response.data.estado === 'success') {
                            sweet_msg_dialog('success', response.data.msg, '/');
                        } else if (response.data.estado === 'danger') {
                            sweet_msg_dialog('warning', response.data.msg);
                        }
                    } catch (e) {
                        sweet_msg_dialog('error', '', '', e)
                    }

                },

                async changeThemes() {
                    let datos = {
                        color1: this.colors[0].hex,
                        color2: this.colors[1].hex
                    };
                    let response = await axios.post(this.url + '/admin/changeThemes', datos);
                    if (response.data) {
                        sweet_msg_dialog('success', 'Tema actualizado exitosamente', '/');
                    }


                },
                generateGradient() {
                    for (let i = 0; i < this.colors.length; i++) {
                        if (this.colors[i].disabled === false)
                            this.colors[i].hex = this.randomHex();
                    }
                },
                randomHex() {
                    return (
                            "#" +
                            Math.random()
                            .toString(16)
                            .slice(2, 8)
                            );
                }

            }
        });
        appDashboard.mixin({
            mounted() {

                if (!window.__imprimirGlobalRegistrado) {
                    window.__imprimirGlobalRegistrado = true;

                    $(document).on("click", "[data-print]", function (event) {
                        event.preventDefault();

                        const targetId = $(this).data("target");
                        const contentElement = document.getElementById(targetId);

                        if (!contentElement) {
                            console.error(`Elemento con ID '${elemId}' no encontrado`);
                            return;
                        }

                        // Obtiene el contenido HTML a imprimir
                        const contentHTML = contentElement.innerHTML;

                        // Abre una nueva ventana de impresión
                        const printWindow = window.open("", "_blank");

                        // Define los estilos y contenido del documento
                        printWindow.document.open();
                        printWindow.document.write(`
                            <!DOCTYPE html>
                            <html lang="es">
                            <head>
                                <meta charset="UTF-8">
                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                <title>Imprimir</title>
                                <link rel="stylesheet" href="${baseUrl}resources/plugins/bootstrap5/css/bootstrap.min.css">
                                <style>
                                    @media print {  
                                    }

                                </style>
                            </head>
                            <body>
                                ${contentHTML}
                                <script>
                                    window.onload = function() {
                                        window.focus();
                                        window.print();
                                        window.close();
                                    };
                                <\/script>
                            </body>
                            </html>
                          `);
                        printWindow.document.close();
                    });
                }
            }
        });
        appDashboard.mount('#v_app');


</script>