<!DOCTYPE html>
<!--
/**
 * Description of viewCuentasConfig
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 2 oct 2025
 * @time 9:51:24 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->


<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-clipboard-check"></i> Configuración de Cuentas</h5>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto">
                <table id="tblCuentasConfig" class="table table-striped nowrap w-100" >
                    <thead class="bg-system text-white">
                        <tr>
                            <th style="width: 5px">ACIONES</th>
                            <th>CÓDIGO</th>
                            <th>NOMBRE</th>
                            <th>DETALLE</th>
                            <th>CTA. CONTABLE</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for='lccf of listaCuentasConfig'>
                            <td>
                                <template v-if="admin">
                                    <button @click="loadConfigCuenta(lccf), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalCuentaConfig"><i class="fas fa-edit"></i> </button>
                                </template>
                            </td>
                            <td>{{lccf.ctcf_codigo}}</td>
                            <td>{{lccf.ctcf_nombre}}</td>
                            <td>{{lccf.ctcf_detalle}}</td>
                            <td>{{lccf.cuenta_contable}}</td>
                            <td v-if="lccf.ctcf_estado == 1"><span class="badge bg-success"><i class="fas fa-check-double"></i>  Activo</span></td>
                            <td v-else><span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!--MODAL CUENTACONFIG-->
        <?php echo view('\Modules\Admin\Views\cuentasconfig\viewModal') ?>
        <!--CLOSE MODAL CUENTACONFIG-->
    </div>
</div>

<script type="text/javascript">

<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';

    if (window.appCuentasConf) {
        window.appCuentasConf.unmount();
    }

    window.appCuentasConf = Vue.createApp({

        components: {
            "vue-multiselect": window['vue-multiselect'].Multiselect
        },
        data() {
            return {
                url: siteUrl,

                //TODO: PERMISOS
                admin: admin,

                //TODO: VARIABLES
                estadoSave: true,
                loading: false,
                idEdit: '',
                codeAux: '',

                //TODO: V-MODELS
                listaCuentasConfig: [],
                listaSearchCuentasContables: [],
                newConfigCuenta: {
                    ctcfCodigo: '',
                    ctcfNombre: '',
                    ctcfDetalle: '',
                    fkCuentaContableDet: '',
                    ctcfEstado: '1'
                },
                formValidacion: []

            };

        },
        created() {
            this.getCuentasConfig();
        },
        methods: {
            async getCuentasConfig() {
                let {data} = await axios.get(this.url + "/admin/cuentasconfig/getCuentasConfig");
                if (data) {
                    this.listaCuentasConfig = data;
                } else {
                    sweet_msg_dialog('warning', data.msg);
                }
                if (this.admin) {
                    dataTableModalBtn('#tblCuentasConfig', 'Lista de Cuentas Configuradas', '#modalCuentaConfig', 'CONFIGURAR CUENTA');
                } else {
                    dataTable('#modalCuentaConfig', 'Lista de Cuentas Configuradas');
                }
            },
            loadConfigCuenta(data) {
                this.newConfigCuenta = {
                    ctcfCodigo: data.ctcf_codigo,
                    ctcfNombre: data.ctcf_nombre,
                    ctcfDetalle: data.ctcf_detalle,
                    ctcfEstado: data.ctcf_estado
                };
                this.newConfigCuenta.fkCuentaContableDet = {ctad_codigo: data.fk_cuentacontable_det};

                this.idEdit = data.id;
                this.codeAux = data.ctcf_codigo;
            },
            async saveUpdateConfigCuenta() {
                this.newConfigCuenta.fkCuentaContableDet = this.newConfigCuenta.fkCuentaContableDet.ctad_codigo;

                let datos = this.formData(this.newConfigCuenta);

                let url = this.url + '/admin/cuentasconfig/saveConfigCuenta';

                if (this.idEdit !== '') {
                    datos.append('idConfig', this.idEdit);
                    datos.append('codeAux', this.codeAux);
                    url = this.url + '/admin/cuentasconfig/updateConfigCuenta';
                }

                try {
                    this.loading = true;
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getCuentasConfig();
                        $('#modalCuentaConfig').modal('hide');
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
            async searchCuentasContables(dataSerach) {
                let datos = {dataSerach: dataSerach};
                try {
                    let {data} = await axios.post(this.url + '/admin/cuentascontables/searchCuentasContables', datos);
                    if (data !== false) {
                        this.listaSearchCuentasContables = data;
                    } else {
                        this.listaSearchCuentasContables = [];
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data.message);
                    this.listaSearchCuentasContables = [];
                }
            },
            clear() {
                this.newConfigCuenta = {
                    ctcfCodigo: '',
                    ctcfNombre: '',
                    ctcfDetalle: '',
                    fkCuentaContableDet: '',
                    ctcfEstado: '1'
                };
                this.estadoSave = true;
                this.idEdit = '';
                this.codeAux = '';
                this.formValidacion = [];
            },
            formData(obj) {
                var formData = new FormData();
                for (var key in obj) {
                    formData.append(key, obj[key]);
                }
                return formData;
            }
        }

    });
    window.appCuentasConf.mount('#app');
</script>