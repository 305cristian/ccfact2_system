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

    var v = new Vue({
        el: '#app',
        data: {
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
        },
        created() {
            this.getModulos();
            this.getAcciones();
            document.getElementById('parent').style.display = 'none';

        },
        methods: {

            toggleTipo() {
                if (v.newModulo.tipoModulo == 'modulo') {
                    document.getElementById('parent').style.display = 'none';
                } else {
                    document.getElementById('parent').style.display = 'block';
                }
            },
            async getModulos() {
                try {
                    let response = await axios.get(this.url + '/admin/modacc/getModulos');
                    if (response.data) {
                        v.listaModulos = response.data;
                    } else {
                        sweet_msg_dialog('warning', 'No se han encontrado módulos registrados');
                    }
                    if (v.admin) {
                        dataTableModalBtn('#tblModulos', 'Lista de Módulos', '#modalModulos', 'NUEVO MÓDULO');
                    } else {
                        dataTable('#tblModulos', 'Lista de Módulos');
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                }

            },
            async saveUpdateModulo() {
                let datos = v.formData(v.newModulo);
                let url = this.url + '/admin/modacc/saveModulo';

                if (v.idEdit != '') {
                    datos.append('idModulo', v.idEdit);
                    datos.append('nameAux', v.nameAux);//TODO: ESTA VARIABLE SE LA USA PARA VALIDAR QUE NO AYA OTRO MODULO CON EL MISMO NOMBRE
                    url = this.url + '/admin/modacc/updateModulo';
                }

                try {
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        v.clear();
                        v.getModulos();
                        $('#modalModulos').modal('hide');
                        $('.modal-backdrop').remove();

                    } else if (response.data.status === 'existe') {

                        sweet_msg_dialog('warning', response.data.msg);

                    } else if (response.data.status === 'vacio') {

                        v.formValidacion = response.data.msg;

                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                }

            },
            loadModulo(modulo) {
                v.newModulo = {
                    nombreModulo: modulo.md_nombre,
                    descripcionModulo: modulo.md_descripcion,
                    estadoModulo: modulo.md_estado,
                    urlModulo: modulo.md_url,
                    tipoModulo: modulo.md_tipo,
                    iconoModulo: modulo.md_icon,
                    ordenModulo: modulo.md_orden,
                    padreModulo: modulo.md_padre
                };
                v.nameAux = modulo.md_nombre;
                v.idEdit = modulo.id;
            },
            async getAcciones() {
                try {
                    let response = await axios.get(this.url + '/admin/modacc/getAcciones');
                    if (response.data) {
                        v.listaAcciones = response.data;
                    } else {
                        sweet_msg_dialog('warning', 'No se han encontrado acciones registrados');
                    }
                    if (v.admin) {
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
                    idModulo: v.newAccion.moduloAccion
                };
                try {
                    let response = await axios.post(this.url + '/admin/modacc/getSubModulo', datos);
                    if (response.data) {
                        v.listaOnlySubModulos = response.data;
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                }

            },
            async saveUpdateAccion() {
                let datos = v.formData(v.newAccion);
                let url = this.url + '/admin/modacc/saveAccion';

                if (v.idEdit2 != '') {
                    datos.append('idAccion', v.idEdit2);
                    datos.append('nameAux', v.nameAux);//TODO: ESTA VARIABLE SE LA USA PARA VALIDAR QUE NO AYA OTRA ACCION CON EL MISMO NOMBRE
                    url = this.url + '/admin/modacc/updateAccion';
                }
                try {
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        v.clear();
                        v.getAcciones();
                        $('#modalAcciones').modal('hide');
                        $('.modal-backdrop').remove();
                    } else if (response.data.status === 'existe') {

                        sweet_msg_dialog('warning', response.data.msg);

                    } else if (response.data.status === 'vacio') {

                        v.formValidacion2 = response.data.msg;

                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                }

            },
            loadAccion(accion) {

                v.newAccion = {
                    nombreAccion: accion.ac_nombre,
                    detalleAccion: accion.ac_detalle,
                    estado: accion.ac_estado,
                    moduloAccion: accion.fk_modulo,
                    subModuloAccion: accion.fk_submodulo
                };
                v.nameAux = accion.ac_nombre;
                v.idEdit2 = accion.id;
                $('#selectModulo').selectpicker('val', accion.fk_modulo);
                v.loadSubModulo();
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
                v.estadoSave = true;
                v.idEdit = '';
                v.newModulo = {
                    nombreModulo: '',
                    descripcionModulo: '',
                    estado: '1',
                    urlModulo: '',
                    tipoModulo: '',
                    iconModulo: '',
                    numOrdenModulo: '',
                    mpadreModulo: ''
                };
                v.formValidacion = [];

                //TODO: ACCIONES
                v.estadoSave2 = true;
                v.idEdit2 = '';
                v.newAccion = {
                    nombreAccion: '',
                    detalleAccion: '',
                    estado: '1',
                    moduloAccion: '',
                    subModuloAccion: ''
                };
                v.formValidacion2 = [];

            },

            zfill(num) {
                return  zFill(num, 3);
            }
        }
    });

</script>