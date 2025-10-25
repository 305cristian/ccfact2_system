<!DOCTYPE html>
<!--
/**
 * Description of viewBancos
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 2 oct 2025
 * @time 3:29:15 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-clipboard-check"></i> Bancos</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tblBancos" class="table table-striped nowrap w-100" >
                    <thead class="bg-system text-white">
                        <tr>
                            <th style="width: 5px">ACIONES</th>
                            <th style="width: 5px">ID</th>
                            <th>BANCO</th>
                            <th>TIPO</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for='lb of listaBancos'>
                            <td>
                                <template v-if="admin">
                                    <button @click="loadBanco(lb), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalBanco"><i class="fas fa-edit"></i> </button>
                                </template>
                            </td>
                            <td>{{zfill(lb.id)}}</td>
                            <td>{{lb.banc_nombre}}</td>
                            <td>{{lb.banc_tipo}}</td>
                            <td v-if="lb.banc_estado == 1"><span class="badge bg-success"><i class="fas fa-check-double"></i>  Activo</span></td>
                            <td v-else><span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!--MODAL BANCOS-->
        <?php echo view('\Modules\Admin\Views\bancos\viewModal') ?>
        <!--CLOSE MODAL BANCOS-->
    </div>
</div>

<script type="text/javascript">

<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';

    if (window.appBancos) {
        window.appBancos.unmount();
    }

    window.appBancos = Vue.createApp({

        data() {
            return {
                url: siteUrl,

                //TODO: PERMISOS
                admin: admin,

                //TODO: VARIABLES
                estadoSave: true,
                idEdit: '',
                loading: false,

                //TODO: V-MODELS
                listaBancos: [],
                newBanco: {
                    bancNombre: '',
                    bancTipo: 'BANCO',
                    bancEstado: '1'
                },
                formValidacion: []

            };
        },
        created() {
            this.getBancos();
        },
        methods: {
            async getBancos() {
                let {data} = await axios.get(this.url + "/admin/bancos/getBancos");
                if (data) {
                    this.listaBancos = data;
                } else {
                    sweet_msg_dialog('warning', data.msg);
                }
                if (this.admin) {
                    dataTableModalBtn('#tblBancos', 'Lista de Banos', '#modalBanco', 'CREAR BANCO');
                } else {
                    dataTable('#tblBancos', 'Lista de Banos');
                }
            },
            loadBanco(data) {
                this.newBanco = {
                    bancNombre: data.banc_nombre,
                    bancTipo: data.banc_tipo,
                    bancEstado: data.banc_estado
                };
                this.idEdit = data.id;
                this.nameAux = data.banc_nombre;
            },
            async saveUpdateBanco() {
                let datos = this.formData(this.newBanco);
                let url = this.url + '/admin/bancos/saveBancos';

                if (this.idEdit !== '') {
                    datos.append('idBanc', this.idEdit);
                    datos.append('nameAux', this.nameAux);
                    url = this.url + '/admin/bancos/updateBancos';
                }

                try {
                    this.loading = true;
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getBancos();
                        $('#modalBanco').modal('hide');
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
                this.newBanco = {
                    bancNombre: '',
                    bancTipo: 'BANCO',
                    bancEstado: '1'
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
    window.appBancos.mount('#app');

</script>

