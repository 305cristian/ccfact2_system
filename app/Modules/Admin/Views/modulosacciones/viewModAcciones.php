<!DOCTYPE html>
<!--
/**
 * Description of viewModAcciones
 *
/**
 * @author CRISTIAN PAZ
 * @date 24 ene. 2024
 * @time 11:56:55
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-book-alt"></i> Módulos Acciones</h5>
        </div>
        <div class="card-body">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-mod-tab" data-bs-toggle="tab" data-bs-target="#nav-mod" type="button" role="tab" aria-controls="nav-mod" aria-selected="true"><i class="fas fa-folder-open"></i> MÓDULOS / SUBMÓDULOS</button>
                    <button class="nav-link" id="nav-acc-tab" data-bs-toggle="tab" data-bs-target="#nav-acc" type="button" role="tab" aria-controls="nav-acc" aria-selected="false"><i class="fas fa-file-alt"></i> ACCIONES</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-mod" role="tabpanel" aria-labelledby="nav-mod-tab">
                    <?php echo view('\Modules\Admin\Views\modulosacciones\viewMod') ?>
                </div>
                <div class="tab-pane fade" id="nav-acc" role="tabpanel" aria-labelledby="nav-acc-tab">
                    <?php echo view('\Modules\Admin\Views\modulosacciones\viewAcc') ?>
                </div>
            </div>


        </div>
    </div>
</div>

<script type="text/javascript">

<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';
    var listaOnlyModulos =<?php echo json_encode($listaModulos) ?>

    if (window.appModulos) {
        window.appModulos.unmount();
    }

    window.appModulos = Vue.createApp({

        data() {
            return {
                url: siteUrl,

                //TODO: PERMISOS
                admin: admin,

                //TODO: VARIABLES
                estadoSave: true,
                estadoSave2: true,

                //TODO: V-MODELS
                idEdit: '',
                idEdit2: '',

                newModulo: {
                    nombreModulo: '',
                    descripcionModulo: '',
                    estadoModulo: '1',
                    urlModulo: '',
                    tipoModulo: 'modulo',
                    iconoModulo: '',
                    ordenModulo: '',
                    padreModulo: '',
                },

                newAccion: {
                    nombreAccion: '',
                    detalleAccion: '',
                    estado: '1',
                    moduloAccion: '-1',
                    subModuloAccion: '',
                },

                //TODO: LISTAS
                listaOnlyModulos: listaOnlyModulos,
                listaOnlySubModulos: [],
                listaModulos: [],
                listaAcciones: [],

                //TODO: FORM-VALIDATION
                formValidacion: [],
                formValidacion2: []
            }
        },
        created() {
            this.getModulos();
            this.getAcciones();

        },
        mounted() {
            document.getElementById('parent').style.display = 'none';
            $('#selectModulo').selectpicker();
        },
        methods: {

            toggleTipo() {
                if (this.newModulo.tipoModulo === 'modulo') {
                    document.getElementById('parent').style.display = 'none';
                } else {
                    document.getElementById('parent').style.display = 'block';
                }
            },
            async getModulos() {
                try {
                    let response = await axios.get(this.url + '/admin/modacc/getModulos');
                    if (response.data) {
                        this.listaModulos = response.data;
                    } else {
                        sweet_msg_dialog('warning', 'No se han encontrado módulos registrados');
                    }
                    if (this.admin) {
                        dataTableModalBtn('#tblModulos', 'Lista de Módulos', '#modalModulos', 'NUEVO MÓDULO');
                    } else {
                        dataTable('#tblModulos', 'Lista de Módulos');
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                }

            },
            async saveUpdateModulo() {
                let datos = this.formData(this.newModulo);
                let url = this.url + '/admin/modacc/saveModulo';

                if (this.idEdit !== '') {
                    datos.append('idModulo', this.idEdit);
                    datos.append('nameAux', this.nameAux);//TODO: ESTA VARIABLE SE LA USA PARA VALIDAR QUE NO AYA OTRO MODULO CON EL MISMO NOMBRE
                    url = this.url + '/admin/modacc/updateModulo';
                }

                try {
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getModulos();
                        $('#modalModulos').modal('hide');
                        $('.modal-backdrop').remove();

                    } else if (response.data.status === 'existe') {

                        sweet_msg_dialog('warning', response.data.msg);

                    } else if (response.data.status === 'vacio') {

                        this.formValidacion = response.data.msg;

                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                }

            },
            loadModulo(modulo) {
                this.newModulo = {
                    nombreModulo: modulo.md_nombre,
                    descripcionModulo: modulo.md_descripcion,
                    estadoModulo: modulo.md_estado,
                    urlModulo: modulo.md_url,
                    tipoModulo: modulo.md_tipo,
                    iconoModulo: modulo.md_icon,
                    ordenModulo: modulo.md_orden,
                    padreModulo: modulo.md_padre
                };
                this.nameAux = modulo.md_nombre;
                this.idEdit = modulo.id;
            },
            async getAcciones() {
                try {
                    let response = await axios.get(this.url + '/admin/modacc/getAcciones');
                    if (response.data) {
                        this.listaAcciones = response.data;
                    } else {
                        sweet_msg_dialog('warning', 'No se han encontrado acciones registrados');
                    }
                    if (this.admin) {
                        dataTableModalBtn('#tblAcciones', 'Lista de Acciones', '#modalAcciones', 'NUEVA ACCIÓN');
                    } else {
                        dataTable('#tblAcciones', 'Lista de Acciones');
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                }

            },
            async loadSubModulo() {
                let datos = {
                    idModulo: this.newAccion.moduloAccion
                };
                try {
                    let response = await axios.post(this.url + '/admin/modacc/getSubModulo', datos);
                    if (response.data) {
                        this.listaOnlySubModulos = response.data;
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                }

            },
            async saveUpdateAccion() {
                let datos = this.formData(this.newAccion);
                let url = this.url + '/admin/modacc/saveAccion';

                if (this.idEdit2 !== '') {
                    datos.append('idAccion', this.idEdit2);
                    datos.append('nameAux', this.nameAux);//TODO: ESTA VARIABLE SE LA USA PARA VALIDAR QUE NO AYA OTRA ACCION CON EL MISMO NOMBRE
                    url = this.url + '/admin/modacc/updateAccion';
                }
                try {
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getAcciones();
                        $('#modalAcciones').modal('hide');
                        $('.modal-backdrop').remove();
                    } else if (response.data.status === 'existe') {

                        sweet_msg_dialog('warning', response.data.msg);

                    } else if (response.data.status === 'vacio') {

                        this.formValidacion2 = response.data.msg;

                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                }

            },
            loadAccion(accion) {
                console.log(accion);
                this.newAccion = {
                    nombreAccion: accion.ac_nombre,
                    detalleAccion: accion.ac_detalle,
                    estado: accion.ac_estado,
                    moduloAccion: accion.fk_modulo,
                    subModuloAccion: accion.fk_submodulo
                };
                this.nameAux = accion.ac_nombre;
                this.idEdit2 = accion.id;
                $('#selectModulo').selectpicker('val', accion.fk_modulo);
                this.loadSubModulo();
            },

            formData(obj) {
                var formData = new FormData();
                for (var key in obj) {
                    formData.append(key, obj[key]);
                }
                return formData;
            },
            clear() {
                //TODO: MODULOS
                this.estadoSave = true;
                this.idEdit = '';
                this.newModulo = {
                    nombreModulo: '',
                    descripcionModulo: '',
                    estado: '1',
                    urlModulo: '',
                    tipoModulo: '',
                    iconModulo: '',
                    numOrdenModulo: '',
                    padreModulo: ''
                };
                this.formValidacion = [];

                //TODO: ACCIONES
                this.estadoSave2 = true;
                this.idEdit2 = '';
                this.newAccion = {
                    nombreAccion: '',
                    detalleAccion: '',
                    estado: '1',
                    moduloAccion: '',
                    subModuloAccion: ''
                };
                this.formValidacion2 = [];

            },

            zfill(num) {
                return  zFill(num, 3);
            }
        }
    });
    window.appModulos.mount('#app');

</script>