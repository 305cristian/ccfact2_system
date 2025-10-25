<!DOCTYPE html>
<!--
/**
 * Description of viewCuentascontables
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 2 oct 2025
 * @time 9:25:18 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-clipboard-check"></i> Cuentas Contables</h5>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto">
                <table id="tblCuentasContables" class="table table-striped nowrap w-100" >
                    <thead class="bg-system text-white">
                        <tr>
                            <th style="width: 5px">ACIONES</th>
                            <th>CÓDIGO</th>
                            <th>CTA. CONTABLE</th>
                            <th>CTA. PADRE</th>
                            <th>TIPO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for='lcd of listaCuentasDet'>
                            <td>
                                <template v-if="admin">
                                    <button @click="loadCuentaContable(lcd), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalCuentasContables"><i class="fas fa-edit"></i> </button>
                                </template>
                            </td>
                            <td>{{lcd.ctad_codigo}}</td>
                            <td>{{lcd.ctad_nombre_cuenta}}</td>
                            <td>{{lcd.cuenta_padre}}</td>
                            <td>{{lcd.tipo_cuenta}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!--MODAL PROVEEDORES-->
        <?php echo view('\Modules\Admin\Views\cuentascontables\viewModal') ?>
        <!--CLOSE MODAL PROVEEDORES-->
    </div>
</div>


<script type="text/javascript">

<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';

    if (window.appCuentasContables) {
        window.appCuentasContables.unmount();
    }

    window.appCuentasContables = Vue.createApp({

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

                //TODO: V-MODELS
                listaCuentasDet: [],
                newCuenta: {
                    ctadCodigo: '',
                    ctadNombreCuenta: '',
                    ctadCuentaPadre: '',
                    fkCtaContable: '',
                    ctadEstado: '1'
                },
                listaCuentas: [],
                listaSearchCuentasContables: [],
                formValidacion: []

            };


        },
        created() {
            this.getCuentasContables();
            this.getCuentas();
        },
        methods: {
            async getCuentas() {
                let {data} = await axios.get(this.url + '/admin/cuentascontables/getCuentas');
                this.listaCuentas = data;
            },
            async getCuentasContables() {
                let {data} = await axios.get(this.url + "/admin/cuentascontables/getCuentasContables");
                if (data) {
                    this.listaCuentasDet = data;
                } else {
                    sweet_msg_dialog('warning', data.msg);
                }
                if (this.admin) {
                    dataTableModalBtn('#tblCuentasContables', 'Lista de Cuentas Contables', '#modalCuentasContables', 'CREAR CUENTA');
                } else {
                    dataTable('#tblCuentasContables', 'Lista de Cuentas Contables');
                }
            },
            loadCuentaContable(data) {
                this.newCuenta = {
                    ctadCodigo: data.ctad_codigo,
                    ctadNombreCuenta: data.ctad_nombre_cuenta,
                    ctadEstado: data.ctad_estado
                };
                this.newCuenta.ctadCuentaPadre = {ctad_codigo: data.codigo_cuenta_padre};
                this.newCuenta.fkCtaContable = data.cta_codigo;

                this.idEdit = data.ctad_codigo;
                this.codeAux = data.ctad_codigo;

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
            async saveUpdateCuenta() {

                this.newCuenta.ctadCuentaPadre = this.newCuenta.ctadCuentaPadre.ctad_codigo;

                let datos = this.formData(this.newCuenta);
                let url = this.url + '/admin/cuentascontables/saveCuenta';
                if (this.idEdit !== '') {
                    datos.append('idCta', this.idEdit);
                    datos.append('codeAux', this.codeAux);
                    url = this.url + '/admin/cuentascontables/updateCuenta';
                }
                try {
                    this.loading = true;
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getCuentasContables();
                        $('#modalCuentasContables').modal('hide');
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
                this.newCuenta = {
                    ctadCodigo: '',
                    ctadNombreCuenta: '',
                    ctadCuentaPadre: null,
                    fkCtaContable: '',
                    ctadEstado: '1'
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
            }
        }

    });
    window.appCuentasContables.mount('#app');
</script>