<!DOCTYPE html>
<!--
/**
 * Description of viewRetenciones
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 2 oct 2025
 * @time 9:12:55 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-file-alt"></i> Retenciones</h5>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto">
                <table id="tblRetenciones" class="table table-striped nowrap w-100" >
                    <thead class="bg-system text-white">
                        <tr>
                            <th>ACIONES</th>
                            <!--<th>ID</th>-->
                            <th>CÓDIGO</th>
                            <th>DETALLE</th>
                            <th>%</th>
                            <th>RET. COMPRAS</th>
                            <th>RET. VENTAS</th>
                            <th>IMPUESTO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for='lr of listaRetenciones'>
                            <td>
                                <template v-if="admin">
                                    <button @click="loadRetencion(lr), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalRetenciones"><i class="fas fa-edit"></i> </button>
                                </template>
                            </td>
                            <!--<td>{{lr.id}}</td>-->
                            <td>{{lr.ret_codigo}}</td>
                            <td>{{lr.ret_nombre}}</td>
                            <td>{{lr.ret_porcentaje}}</td>
                            <td>{{lr.ret_cta_compras}}</td>
                            <td>{{lr.ret_cta_ventas}}</td>
                            <td>{{lr.ret_impuesto}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!--MODAL RETENCIONES-->
        <?php echo view('\Modules\Admin\Views\retenciones\viewModal') ?>
        <!--CLOSE MODAL RETENCIONES-->
    </div>
</div>

<script type="text/javascript">

<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';

    var v = new Vue({
        el: "#app",
        components: {
            "vue-multiselect": window.VueMultiselect.default
        },
        data: {
            url: siteUrl,

            //TODO: PERMISOS
            admin: admin,

            //TODO: VARIABLES
            estadoSave: true,
            loading: false,
            idEdit: '',

            //TODO: V-MODELS
            listaSearchCuentasContables: [],
            listaRetenciones: [],
            newRetencion: {
                retCodigo: '',
                retNombre: '',
                retPorcentaje: '',
                retCtaCompras: '',
                retCtaVentas: '',
                retImpuesto: 'RENTA',
                retValCompra: '',
                retValVenta: ''
            },
            formValidacion: []



        },
        created() {
            this.getRetenciones();
        },
        methods: {
            async getRetenciones() {
                let {data} = await axios.get(this.url + "/admin/retenciones/getRetenciones");
                if (data) {
                    v.listaRetenciones = data;
                } else {
                    sweet_msg_dialog('warning', data.msg);
                }
                if (v.admin) {
                    dataTableModalBtn('#tblRetenciones', 'Lista de Retenciones', '#modalRetenciones', 'CREAR RETENCIÓN');
                } else {
                    dataTable('#tblRetenciones', 'Lista de Retenciones');
                }
            },
            loadRetencion(data) {
                this.newRetencion = {
                    retCodigo: data.ret_codigo,
                    retNombre: data.ret_nombre,
                    retPorcentaje: data.ret_porcentaje,
                    retImpuesto: data.ret_impuesto,
                    retValCompra: data.ret_val_compra,
                    retValVenta: data.ret_val_venta
                };
                this.newRetencion.retCtaCompras = {ctad_codigo: data.ret_cta_compras};
                this.newRetencion.retCtaVentas = {ctad_codigo: data.ret_cta_ventas};
                
                this.idEdit = data.id;
                this.codeAux = data.ret_codigo;


            },
            async saveUpdateRetencion() {


                this.newRetencion.retCtaCompras = v.newRetencion.retCtaCompras.ctad_codigo;
                this.newRetencion.retCtaVentas = v.newRetencion.retCtaVentas.ctad_codigo;


                let datos = this.formData(this.newRetencion);

                let url = this.url + '/admin/retenciones/saveRetenciones';

                if (this.idEdit !== '') {
                    datos.append('idRet', this.idEdit);
                    datos.append('codeAux', this.codeAux);
                    url = this.url + '/admin/retenciones/updateRetenciones';
                }

                try {
                    this.loading = true;
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getRetenciones();
                        $('#modalRetenciones').modal('hide');
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
                        v.listaSearchCuentasContables = data;
                    } else {
                        v.listaSearchCuentasContables = [];
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data.message);
                    v.listaSearchCuentasContables = [];
                }

            },
            setDataCodCuenta(data) {

                this.newRetencion.retCtaCompras = data.ctad_codigo;

            },
            clear() {
                this.newRetencion = {
                    retCodigo: '',
                    retNombre: '',
                    retPorcentaje: '',
                    retCtaCompras: '',
                    retCtaVentas: '',
                    retImpuesto: 'RENTA',
                    retValCompra: '',
                    retValVenta: ''
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

        }

    });

</script>