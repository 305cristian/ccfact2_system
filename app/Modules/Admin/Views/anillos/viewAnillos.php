<!DOCTYPE html>
<!--
/**
 * Description of viewAnillos
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 7 oct 2025
 * @time 1:37:16 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-clipboard-check"></i> Anillos</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tblAnillos" class="table table-striped nowrap w-100" >
                    <thead class="bg-system text-white">
                        <tr>
                            <th style="width: 5px">ACIONES</th>
                            <th style="width: 5px">ID</th>
                            <th>NOMBRE</th>
                            <th>DESCRIPCIÓN</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for='la of listaAnillos'>
                            <td>
                                <template v-if="admin">
                                    <button @click="loadAnillo(la), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalAnillos"><i class="fas fa-edit"></i> </button>
                                </template>
                            </td>
                            <td>{{zfill(la.id)}}</td>
                            <td>{{la.an_nombre}}</td>
                            <td>{{la.an_descripcion}}</td>
                            <td v-if="la.an_estado == 1"><span class="badge bg-success"><i class="fas fa-check-double"></i>  Activo</span></td>
                            <td v-else><span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!--MODAL ANILLOS-->
        <?php echo view('\Modules\Admin\Views\anillos\viewModal') ?>
        <!--CLOSE MODAL ANILLOS-->
    </div>
</div>

<script type="text/javascript">

<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';

    if (window.appAnillo) {
        window.appAnillo.unmount();
    }
    window.appAnillo = Vue.createApp({

        data() {
            return {
                url: siteUrl,

                //TODO: PERMISOS
                admin: admin,

                //TODO: VARIABLES
                estadoSave: true,
                loading: false,
                idEdit: '',

                //TODO: V-MODELS
                listaAnillos: [],
                newAnillo: {
                    anNombre: '',
                    anDescripcion: '',
                    anEstado: '1'
                },
                formValidacion: []
            }
        },
        created() {
            this.getAnillos();
        },
        methods: {
            async getAnillos() {
                let {data} = await axios.get(this.url + "/admin/anillos/getAnillos");
                if (data) {
                    this.listaAnillos = data;
                } else {
                    sweet_msg_dialog('warning', 'No se pudieron cargar los anillos');
                }
                if (this.admin) {
                    dataTableModalBtn('#tblAnillos', 'Lista de Anillos', '#modalAnillos', 'CREAR ANILLO');
                } else {
                    dataTable('#tblAnillos', 'Lista de Anillos');
                }
            },
            loadAnillo(data) {
                this.newAnillo = {
                    anNombre: data.an_nombre,
                    anDescripcion: data.an_descripcion,
                    anEstado: data.an_estado
                };
                this.idEdit = data.id;
                this.nameAux = data.an_nombre;
            },
            async saveUpdateAnillo() {
                let datos = this.formData(this.newAnillo);

                let url = this.url + '/admin/anillos/saveAnillo';

                if (this.idEdit !== '') {
                    datos.append('idAnillo', this.idEdit);
                    datos.append('nameAux', this.nameAux);
                    url = this.url + '/admin/anillos/updateAnillo';
                }

                try {
                    this.loading = true;
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getAnillos();
                        $('#modalAnillos').modal('hide');
                        $('.modal-backdrop').remove();
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
                this.newAnillo = {
                    anNombre: '',
                    anDescripcion: '',
                    anEstado: '1'
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
                return zFill(num, 3);
            }
        }
    });
    appAnillo.mount('#app');
</script>