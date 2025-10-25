<!DOCTYPE html>
<!--
/**
 * Description of viewGruposSubgrupos
 *
/**
 * @author CRISTIAN PAZ
 * @date 15 abr. 2024
 * @time 11:27:01
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-file-archive"></i> Administrar Grupos y Subgrupos</h5>
        </div>
        <div class="card-body">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-mod-tab" data-bs-toggle="tab" data-bs-target="#nav-mod" type="button" role="tab" aria-controls="nav-mod" aria-selected="true"><i class="fas fa-folder-open"></i> GRUPOS</button>
                    <button class="nav-link" id="nav-acc-tab" data-bs-toggle="tab" data-bs-target="#nav-acc" type="button" role="tab" aria-controls="nav-acc" aria-selected="false"><i class="fas fa-file-alt"></i> SUBGRUPOS</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-mod" role="tabpanel" aria-labelledby="nav-mod-tab">
                    <?php echo view('\Modules\Admin\Views\grupossubgrupos\viewGrupos') ?>
                </div>
                <div class="tab-pane fade" id="nav-acc" role="tabpanel" aria-labelledby="nav-acc-tab">
                    <?php echo view('\Modules\Admin\Views\grupossubgrupos\viewSubgrupos') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';

    if (window.appGrupos) {
        window.appGrupos.unmount();
    }

    window.appGrupos = Vue.createApp({
        data() {
            return {
                url: siteUrl,

                //TODO: PERMISOS
                admin: admin,

                //TODO: VARIABLES
                estadoSave: true,
                //TODO: V-MODELS
                idEdit: '',
                newGrupo: {
                    grNombre: '',
                    grDescripcion: '',
                    grEstado: '1',
                    grIcon: ''
                },

                //TODO: LISTAS 
                listaGrupos: [],
                formValidacion: [],

                //TODO: VARIABLES PARA SUBGRUPOS
                estadoSave2: true,
                //TODO: V-MODELS
                idEdit2: '',
                newSubGrupo: {
                    sgrNombre: '',
                    sgrDescripcion: '',
                    sgrEstado: '1',
                    sgrIcon: '',
                    sgrGrupo: ''
                },

                //TODO: LISTAS
                listaSubGrupos: [],
                formValidacion2: []

            }
        },
        created() {
            this.getGrupos();
            this.getSubGrupos();
        },
        methods: {
            async getGrupos() {
                try {
                    let response = await axios.get(this.url + '/admin/grupos/getGrupos');
                    if (response.data) {
                        this.listaGrupos = response.data;
                    } else {
                        sweet_msg_dialog('warning', 'No se encontraron grupos registradas');
                    }
                    if (this.admin) {
                        dataTableModalBtn('#tblGrupos', 'Lista de grupos', '#modalGrupo', 'CREAR GRUPO');
                    } else {
                        dataTable('#tblGrupos', 'Lista de grupos');
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                }
            },
            loadGrupo(gr) {
                this.newGrupo = {
                    grNombre: gr.gr_nombre,
                    grDescripcion: gr.gr_descripcion,
                    grEstado: gr.gr_estado,
                    grIcon: gr.gr_icon
                };
                this.idEdit = gr.id;
                this.nameAux = gr.gr_nombre;

            },

            async saveUpdateGrupo() {
                let datos = this.formData(this.newGrupo);
                let url = this.url + '/admin/grupos/saveGrupo';

                if (this.idEdit != '') {
                    datos.append('idGrupo', this.idEdit);
                    datos.append('nameAux', this.nameAux);//TODO: ESTA VARIABLE SE LA USA PARA VALIDAR QUE NO EXISTA OTRA REGISTRO CON EL MISMO NOMBRE
                    url = this.url + '/admin/grupos/updateGrupo';
                }

                try {
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getGrupos();
                        $('#modalGrupo').modal('hide');
                        $('.modal-backdrop').remove();

                    } else if (response.data.status === 'existe') {

                        sweet_msg_dialog('warning', response.data.msg);

                    } else if (response.data.status === 'vacio') {

                        this.formValidacion = response.data.msg;

                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                }
            },
            clear() {
                this.newGrupo = {
                    grNombre: '',
                    grDescripcion: '',
                    grEstado: '1',
                    grIcon: ''
                };
                this.estadoSave = true;
                this.idEdit = '';
                this.formValidacion = [];

                this.newSubGrupo = {
                    sgrNombre: '',
                    sgrDescripcion: '',
                    sgrEstado: '1',
                    sgrIcon: '',
                    sgrGrupo: ''
                };
                this.estadoSave2 = true;
                this.idEdit2 = '';
                this.formValidacion2 = [];

            },

            async getSubGrupos() {
                try {
                    let response = await axios.get(this.url + '/admin/grupos/getSubGrupos');
                    if (response.data) {
                        this.listaSubGrupos = response.data;
                    } else {
                        sweet_msg_dialog('warning', 'No se encontraron subgrupos registradas');
                    }
                    if (this.admin) {
                        dataTableModalBtn('#tblSubGrupos', 'Lista de subgrupos', '#modalSubGrupo', 'CREAR SUBGRUPO');
                    } else {
                        dataTable('#tblSubGrupos', 'Lista de subgrupos');
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                }
            },
            loadSubGrupo(sgr) {
                this.newSubGrupo = {
                    sgrNombre: sgr.sgr_nombre,
                    sgrDetalle: sgr.sgr_detalle,
                    sgrEstado: sgr.sgr_estado,
                    sgrIcon: sgr.sgr_icon,
                    sgrGrupo: sgr.fk_grupo
                };
                this.idEdit2 = sgr.id;
                this.nameAux2 = sgr.sgr_nombre;


            },
            async saveUpdateSubGrupo() {
                let datos = this.formData(this.newSubGrupo);
                let url = this.url + '/admin/grupos/saveSubGrupo';

                if (this.idEdit2 != '') {
                    datos.append('idSubGrupo', this.idEdit2);
                    datos.append('nameAux', this.nameAux2);//TODO: ESTA VARIABLE SE LA USA PARA VALIDAR QUE NO EXISTA OTRA REGISTRO CON EL MISMO NOMBRE
                    url = this.url + '/admin/grupos/updateSubGrupo';
                }

                try {
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getSubGrupos();
                        $('#modalSubGrupo').modal('hide');
                        $('.modal-backdrop').remove();

                    } else if (response.data.status === 'existe') {

                        sweet_msg_dialog('warning', response.data.msg);

                    } else if (response.data.status === 'vacio') {

                        this.formValidacion2 = response.data.msg;

                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                }
            },

            formData(obj) {
                var formData = new FormData();
                for (var key in obj) {
                    formData.append(key, obj[key]);
                }
                return formData;
            },
            zfill(num) {
                return zFill(num, 3);
            }
        }
    });
    window.appGrupos.mount('#app');

</script>
