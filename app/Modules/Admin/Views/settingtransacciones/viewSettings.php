<!DOCTYPE html>
<!--
/**
 * Description of viewSettings
 *
/**
 * @author CRISTIAN PAZ
 * @date 21 mar. 2024
 * @time 15:32:52
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-user-cog"></i> Administrar Variables de configuraciòn</h5>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto">
                <table id="tblSettings" class="table table-striped nowrap display" style="width: 100%">
                    <thead class="bg-system text-white">
                        <tr>
                            <td>ID</td>
                            <td>NOMBRE</td>
                            <td>VALOR</td>
                            <td>DETALLE</td>
                            <td>ACCIONES</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="ls of listaSettings">
                            <td>{{zfill(ls.id)}}</td>
                            <td>{{ls.st_nombre}}</td>
                            <td>{{ls.st_value}}</td>
                            <td>{{ls.st_detalle}}</td>


                            <td>
                                <template v-if="admin">
                                    <button @click="loadSetting(ls), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalSettings"><i class="fas fa-edit"></i> </button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div> 
            <!--MODAL CREATE SETTINGS-->
            <div id="modalSettings" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 v-if="estadoSave" class=""><i class="fas fa-file-alt"></i> Crear Variable</h5>
                            <h5 v-else class=""><i class="fas fa-file-alt"></i> Actualizar Variable</h5>
                            <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
                        </div>
                        <div class="modal-body">
                            <div class="text-left">
                                <input type="hidden" v-model="idEdit">
                                <div class="mb-3">
                                    <label for="nombreSett" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Nombre</label>
                                    <input  v-model="newSett.nombreSett" type="text" class="form-control" id="nombreSett" placeholder="Ingrese un nombre" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.nombreSett" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="valueSett" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Código</label>
                                    <input  v-model="newSett.valueSett" type="text" class="form-control" id="valueSett" placeholder="Ingrese un valor" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.valueSett" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="detalleSett" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Detalle</label>
                                    <textarea  v-model="newSett.detalleSett" type="text" class="form-control" id="detalleSett" placeholder="Ingrese un detalle"></textarea>
                                    <!--validaciones-->
                                    <div v-html="formValidacion.detalleSett" class="text-danger"></div>
                                </div>



                            </div>
                        </div>
                        <div class="modal-footer">
                            <button  class="btn btn-primary" @click="saveUpdateSettings()">
                                <span v-if="estadoSave"><i class="fas fa-save"></i> Crear</span>
                                <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                            </button>
                            <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-stop"></i> Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--CLOSE MODAL CREATE SETTINGS-->

        </div>
    </div>
</div>

<script type="text/javascript">
<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';

    if (window.appSettings) {
        window.appSettings.unmount();
    }


    window.appSettings = Vue.createApp({

        data() {
            return {
                url: siteUrl,
                //PERMISOS
                admin: admin,

                //V-MODELS
                idEdit: '',
                newSett: {
                    nombreSett: '',
                    valueSett: '',
                    detalleSett: ''
                },

                //TODO: VARIABLES
                estadoSave: true,

                //TODO: LISTAS
                listaSettings: [],
                formValidacion: []
            }
        },
        created() {
            this.getSettings();
        },
        methods: {
            async getSettings() {
                try {
                    let response = await axios.get(this.url + '/admin/sett/getSettings');
                    if (response.data) {
                        this.listaSettings = response.data;
                    } else {
                        sweet_msg_dialog('warning', 'No se encontraron variables de configuración registradas');
                    }
                    if (this.admin) {
                        dataTableModalBtn('#tblSettings', 'Lista de variables', '#modalSettings', 'CREAR VARIABLE');
                    } else {
                        dataTable('#tblSettings', 'Lista de variables');
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                }

            },
            loadSetting(sett) {
                this.newSett = {
                    nombreSett: sett.st_nombre,
                    valueSett: sett.st_value,
                    detalleSett: sett.st_detalle
                };
                this.idEdit = sett.id;
                this.nameAux = sett.st_nombre;

            },
            async saveUpdateSettings() {
                let datos = this.formData(this.newSett);
                let url = this.url + '/admin/sett/saveSettings';

                if (this.idEdit !== '') {
                    datos.append('idSett', this.idEdit);
                    datos.append('nameAux', this.nameAux);//TODO: ESTA VARIABLE SE LA USA PARA VALIDAR QUE NO AYA OTRA REGISTRO CON EL MISMO NOMBRE
                    url = this.url + '/admin/sett/updateSettings';
                }

                try {
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getSettings();
                        $('#modalSettings').modal('hide');
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
                this.newSett = {
                    nombreSett: '',
                    valueSett: '',
                    detalleSett: ''
                };
                this.estadoSave = true;
                this.idEdit = '';
                this.formValidacion = [];
            },
            formData(obj) {
                var formData = new FormData();
                for (var key in obj) {
                    formData.append(key, obj[key]);
                }
                return formData;
            },
            zfill(num) {
                return  zFill(num, 3);
            }
        }
    });
    window.appSettings.mount('#app');
</script>