<!DOCTYPE html>
<!--
/**
 * Description of viewMotivosAjuste
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 7 oct 2025
 * @time 7:28:25 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-exchange-alt"></i> Motivos de Ajuste</h5>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto">
                <table id="tblMotivosAjuste" class="table table-striped nowrap w-100">
                    <thead class="bg-system text-white">
                        <tr>
                            <th style="width: 5px">ACCIONES</th>
                            <th style="width: 5px">ID</th>
                            <th>NOMBRE</th>
                            <th>DETALLE</th>
                            <th>TIPO</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for='lm of listaMotivos'>
                            <td>
                                <template v-if="admin">
                                    <button @click="loadMotivo(lm), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalMotivosAjuste">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </template>
                            </td>
                            <td>{{zFill(lm.id)}}</td>
                            <td>{{lm.mot_nombre}}</td>
                            <td>{{lm.mot_detalle}}</td>
                            <td>
                                <span class="badge bg-info">{{lm.mot_tipo}}</span>
                            </td>
                            <td>
                                <span v-if="lm.mot_estado == 1" class="badge bg-success">Activo</span>
                                <span v-else class="badge bg-danger">Inactivo</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!--MODAL MOTIVOS AJUSTE-->
        <?php echo view('\Modules\Admin\Views\motivosajuste\viewModal') ?>
        <!--CLOSE MODAL MOTIVOS AJUSTE-->
    </div>
</div>

<script type="text/javascript">

<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';

    var v = new Vue({
        el: "#app",
        data: {
            url: siteUrl,

            //TODO: PERMISOS
            admin: admin,

            //TODO: VARIABLES
            estadoSave: true,
            loading: false,
            idEdit: '',
            nombreAux: '',

            //TODO: V-MODELS
            listaMotivos: [],
            newMotivo: {
                motNombre: '',
                motDetalle: '',
                motTipo: 'AJUSTES',
                motEstado: '1'
            },
            formValidacion: []
        },
        created() {
            this.getMotivos();
        },
        methods: {
            async getMotivos() {
                let {data} = await axios.get(this.url + "/admin/motivos/getMotivos");
                if (data) {
                    v.listaMotivos = data;
                } else {
                    sweet_msg_dialog('warning', 'No se pudieron cargar los motivos');
                }
                if (v.admin) {
                    dataTableModalBtn('#tblMotivosAjuste', 'Lista de Motivos de Ajuste', '#modalMotivosAjuste', 'CREAR MOTIVO');
                } else {
                    dataTable('#tblMotivosAjuste', 'Lista de Motivos de Ajuste');
                }
            },
            loadMotivo(data) {
                this.newMotivo = {
                    motNombre: data.mot_nombre,
                    motDetalle: data.mot_detalle,
                    motTipo: data.mot_tipo,
                    motEstado: data.mot_estado
                };
                this.idEdit = data.id;
                this.nombreAux = data.mot_nombre;
            },
            async saveUpdateMotivo() {
                let datos = this.formData(this.newMotivo);

                let url = this.url + '/admin/motivos/saveMotivo';

                if (this.idEdit !== '') {
                    datos.append('idMotivo', this.idEdit);
                    datos.append('nombreAux', this.nombreAux);
                    url = this.url + '/admin/motivos/updateMotivo';
                }

                try {
                    this.loading = true;
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getMotivos();
                        $('#modalMotivosAjuste').modal('hide');
                        $('.modal-backdrop').remove();
                    } else if (response.data.status === 'existe') {
                        sweet_msg_dialog('warning', response.data.msg);
                    } else if (response.data.status === 'vacio') {
                        this.formValidacion = response.data.msg;
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    this.loading = false;
                }
            },
            clear() {
                this.newMotivo = {
                    motNombre: '',
                    motDetalle: '',
                    motTipo: 'AJUSTES',
                    motEstado: '1'
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
            zFill(num) {
                return zFill(num, 3);
            }
        }
    });

</script>