<!DOCTYPE html>
<!--
/**
 * Description of viewSectores
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 7 oct 2025
 * @time 1:37:37 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-clipboard-check"></i> Sectores</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tblSectores" class="table table-striped nowrap w-100" >
                    <thead class="bg-system text-white">
                        <tr>
                            <th style="width: 5px">ACIONES</th>
                            <th style="width: 5px">ID</th>
                            <th>NOMBRE</th>
                            <th>DESCRIPCIÓN</th>
                            <th>ANILLO</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for='ls of listaSectores'>
                            <td>
                                <template v-if="admin">
                                    <button @click="loadSector(ls), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalSectores"><i class="fas fa-edit"></i> </button>
                                </template>
                            </td>
                            <td>{{zfill(ls.id)}}</td>
                            <td>{{ls.sec_nombre}}</td>
                            <td>{{ls.sec_descripcion}}</td>
                            <td>{{ls.an_nombre}}</td>
                            <td v-if="ls.sec_estado == 1"><span class="badge bg-success"><i class="fas fa-check-double"></i>  Activo</span></td>
                            <td v-else><span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!--MODAL SECTORES-->
        <?php echo view('\Modules\Admin\Views\sectores\viewModal') ?>
        <!--CLOSE MODAL SECTORES-->
    </div>
</div>

<script type="text/javascript">

<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';

    if (window.appSectores) {
        window.appSectores.unmount();
    }
    window.appSectores = Vue.createApp({

        data() {
            return {
                url: siteUrl,

                //TODO: PERMISOS
                admin: admin,

                //TODO: VARIABLES
                estadoSave: true,
                loading: false,
                idEdit: '',
                nombreAux: '',

                //TODO: V-MODELS
                listaAnillos: [],
                listaSectores: [],
                newSector: {
                    secNombre: '',
                    secDescripcion: '',
                    fkAnillo: '',
                    secEstado: '1'
                },
                formValidacion: []
            }
        },
        created() {
            this.getSectores();
            this.getAnillos();
        },
        methods: {
            async getSectores() {
                let {data} = await axios.get(this.url + "/admin/sectores/getSectores");
                if (data) {
                    this.listaSectores = data;
                } else {
                    sweet_msg_dialog('warning', 'No se pudieron cargar los sectores');
                }
                if (this.admin) {
                    dataTableModalBtn('#tblSectores', 'Lista de Sectores', '#modalSectores', 'CREAR SECTOR');
                } else {
                    dataTable('#tblSectores', 'Lista de Sectores');
                }
            },
            async getAnillos() {
                let {data} = await axios.get(this.url + "/admin/anillos/getAnillos");
                if (data) {
                    this.listaAnillos = data;
                }
            },
            loadSector(data) {
                this.newSector = {
                    secNombre: data.sec_nombre,
                    secDescripcion: data.sec_descripcion,
                    fkAnillo: data.fk_anillo,
                    secEstado: data.sec_estado
                };
                this.idEdit = data.id;
                this.nombreAux = data.sec_nombre;
            },
            async saveUpdateSector() {
                let datos = this.formData(this.newSector);

                let url = this.url + '/admin/sectores/saveSector';

                if (this.idEdit !== '') {
                    datos.append('idSector', this.idEdit);
                    datos.append('nombreAux', this.nombreAux);
                    url = this.url + '/admin/sectores/updateSector';
                }

                try {
                    this.loading = true;
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getSectores();
                        $('#modalSectores').modal('hide');
                        $('.modal-backdrop').remove();
                    } else if (response.data.status === 'existe') {
                        sweet_msg_dialog('warning', response.data.msg);
                    } else if (response.data.status === 'vacio') {
                        this.formValidacion = response.data.msg;
                    } else if (response.data.status === 'existe') {
                        sweet_msg_dialog('warning', response.data.msg);
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    this.loading = false;
                }
            },
            clear() {
                this.newSector = {
                    secNombre: '',
                    secDescripcion: '',
                    fkAnillo: '',
                    secEstado: '1'
                };
                this.estadoSave = true;
                this.idEdit = '';
                this.nombreAux = '';
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
                return zFill(num, 3);
            }
        }
    });
    appSectores.mount('#app');
</script>