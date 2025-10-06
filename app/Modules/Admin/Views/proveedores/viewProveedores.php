<!DOCTYPE html>
<!--
/**
 * Description of proveedoresView
 *
/**
 * @author CRISTIAN PAZ
 * @date 15 abr. 2024
 * @time 12:38:02
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-users-cog"></i> Administrar Proveedores</h5>
        </div>
        <div class="card-body">
            <div class="row col-md-12">


                <div class="col-md-3 mb-2">
                    <label for="selectStock" class="col-form-label col-form-label-sm"><i class="fal fa-user-tie"></i> Nombres</label>
                    <vue-multiselect
                        v-model="keyProveedor" 
                        tag-placeholder="Proveedor no Encontrado"
                        placeholder="Buscar Por Nombres"
                        label="prov_razon_social"
                        track-by="prov_ruc"
                        :multiple="false"
                        :searchable="true"
                        :taggable="true"
                        :options-limit="10"
                        :show-no-results="true"
                        :options="listaSearchProveedores"
                        @remove="onRemove($event)"
                        @input="setDataCiruc($event)"
                        @search-change="searchProveedores($event)"/>

                    <template slot="option" slot-scope="{ option }">
                        <span style="font-size: 12px">{{ option.prov_razon_social+': '}} <strong>{{ option.prov_ruc }} </strong></span>
                    </template>
                    </vue-multiselect>
                </div>

                <div class="col-md-3 mb-2">
                    <label for="cirucProveedor" class="col-form-label col-form-label-sm"><i class="fal fa-qrcode"></i> CI/RUC</label>
                    <input  v-model="cirucProveedor" type="number" class="form-control" id="cirucProveedor" placeholder="Digite la CI/RUC" />                               
                </div>

                <div class="col-md-2  mb-2" style="position: relative; top: 30px">
                    <button class="btn btn-system-2" @click="getProveedores()">
                        <span v-if='loading'><i class="loading-spin"></i> Buscando...</span>
                        <span v-else ><i class="fas fa-search"></i> Buscar Proveedores </span> 
                    </button>
                </div>
                <div id="panelBtnCreate" class="col-md-2  mb-2" style="position: relative; top: 30px">
                    <button class="btn btn-system-2" data-bs-toggle="modal" data-bs-target="#modalProveedores"><span class="fas fa-user-tie"></span> Crear Proveedor</button>
                </div>
            </div>
            <br>
            <hr>
            <br>
            <div id="panelMain" class="col-md-12">
                <?php echo view('\Modules\Admin\Views\proveedores\viewTable') ?>
            </div>
        </div>
        <!--MODAL PROVEEDORES-->
        <?php echo view('\Modules\Admin\Views\proveedores\viewModal') ?>
        <!--CLOSE MODAL PROVEEDORES-->
    </div>
</div>

<script type="text/javascript">
<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';
    var listaTipoDocumento = <?php echo json_encode($listaTipoDocumento) ?>;
    var listaCuentasContable = <?php echo json_encode($listaCuentasContable) ?>;
    var listaSectores = <?php echo json_encode($listaSectores) ?>;
    var listaProvincia = <?php echo json_encode($listaProvincia) ?>;
    var listaTipoCuentaBanco = <?php echo json_encode($listaTipoCuentaBanco) ?>;

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

            //TODO: V-MODELS
            cirucProveedor: '',
            provincia: '',
            canton: '',
            idEdit: '',
            newProveedor: {
                provRuc: '',
                provNombres: '',
                provApellidos: '',
                provRazonSocial: '',
                provTelefono: '',
                provCelular: '',
                provEmail: '',
                provDireccion: '',
                provSector: '',
                provParroquia: '',
                provTipoProveedor: '',
                provTipoDocumento: '',
                provDiasCredito: '',
                provCtaContable: '',
                provEstado: true
            },
            vmodelBancos: [],
            listaBancos: [],
            listaTipoCuentaBanco: listaTipoCuentaBanco,
            isLoadingBank: false,

            vmodelRtenciones: [],
            listaRetenciones: [],
            isLoadingRet: false,

            //TODO: LISTAS
            listaProveedores: [],
            listaCuentasContable: listaCuentasContable,
            listaSectores: listaSectores,
            listaTipoDocumento: listaTipoDocumento,
            listaProvincia: listaProvincia,
            listaCanton: [],
            listaParroquia: [],
            keyProveedor: [],
            listaSearchProveedores: [],
            formValidacion: []
        },
        created() {
            panelMain.style.display = "none";
        },
        mounted() {
            $(".selectpicker").selectpicker();
        },
        methods: {

            async getRetenciones(dataSerach) {
                let datos = {dataSerach: dataSerach};
                try {
                    v.isLoadingRet = true;
                    let {data} = await axios.post(this.url + "/admin/proveedores/getRetenciones", datos);
                    if (data) {
                        v.listaRetenciones = data;
                    } else {
                        v.listaRetenciones = [];
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data.message);
                } finally {
                    v.isLoadingRet = false;
                }
            },

            async getBancos(dataSerach) {
                let datos = {dataSerach: dataSerach};
                try {
                    v.isLoadingBank = true;
                    let {data} = await axios.post(this.url + "/admin/proveedores/getBancos", datos);
                    if (data) {
                        v.listaBancos = data;
                    } else {
                        v.listaBancos = [];
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data.message);
                } finally {
                    v.isLoadingBank = false;
                }


            },

            async searchProveedores(dataSerach) {
                let datos = {dataSerach: dataSerach};
                try {
                    let {data} = await axios.post(this.url + '/admin/proveedores/searchProveedores', datos);
                    if (data !== false) {
                        v.listaSearchProveedores = data;
                    } else {
                        v.listaSearchProveedores = [];
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data.message);
                    v.listaSearchProveedores = [];
                }

            },
            setDataCiruc(data) {
                v.cirucProveedor = data ? data.prov_ruc : "";
            },
            onRemove() {
                v.keyProveedor = [];
                v.listaSearchProveedores = [];
            },
            async getProveedores() {
                let datos = {
                    ciruc: v.cirucProveedor ? v.cirucProveedor : ""
                };

                try {

                    v.loading = true;
                    let response = await axios.post(this.url + '/admin/proveedores/getProveedores', datos);
                    if (response.data.status === "success") {
                        v.listaProveedores = response.data.data;
                        panelMain.style.display = "block";
                        panelBtnCreate.style.display = "none";

                    } else {
                        sweet_msg_dialog('warning', response.data.msg);
                        panelMain.style.display = "none";
                    }
                    if (v.admin) {
                        dataTableModalBtn('#tblProveedores', 'Lista de Proveedores', '#modalProveedores', 'CREAR PROVEEDOR');
                    } else {
                        dataTable('#tblProveedores', 'Lista de Proveedores');
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    v.loading = false;
                }
            },
            async getCantones() {
                let id = v.provincia ? v.provincia : 0;
                let {data} = await axios.get(this.url + "/comun/clientes/getCantones/" + id);
                if (data) {
                    v.listaCanton = data;
                    v.listaParroquia = [];
                } else {
                    v.listaCanton = [];
                }
            },
            async getParroquias() {
                let id = v.canton ? v.canton : 0;
                let {data} = await axios.get(this.url + "/comun/clientes/getParroquias/" + id);
                if (data) {
                    v.listaParroquia = data;
                } else {
                    v.listaParroquia = [];
                }
            },
            setTipoProveedor() {
                if (v.newProveedor.provTipoDocumento === '1') {
                    v.newProveedor.provTipoProveedor = "2";
                } else if (v.newProveedor.provTipoDocumento === '2') {
                    v.newProveedor.provTipoProveedor = "1";
                } else if (v.newProveedor.provTipoDocumento === '3') {
                    v.newProveedor.provTipoProveedor = "4";
                } else {
                    v.newProveedor.provTipoProveedor = "4";
                }
            },

            setRazonSocial() {
                let nombres = v.newProveedor.provNombres;
                let apellidos = v.newProveedor.provApellidos;
                v.newProveedor.provRazonSocial = `${nombres.toUpperCase()} ${apellidos.toUpperCase()}`;
            },

            async loadProveedor(prov) {
                swalLoading('Cargando...', );
                v.newProveedor = {
                    provRuc: prov.prov_ruc,
                    provNombres: prov.prov_nombres,
                    provApellidos: prov.prov_apellidos,
                    provRazonSocial: prov.prov_razon_social,
                    provTelefono: prov.prov_telefono,
                    provCelular: prov.prov_celular,
                    provEmail: prov.prov_email,
                    provDireccion: prov.prov_direccion,
                    provSector: prov.fk_sector,
                    provParroquia: prov.fk_parroquia,
                    provTipoProveedor: prov.fk_tipo_sujeto,
                    provTipoDocumento: prov.fk_tipo_documento,
                    provDiasCredito: prov.prov_cupo_credito,
                    provCtaContable: prov.fk_codigo_cuenta_contable,
                    provEstado: prov.prov_estado === "1" ? true : false
                };
                v.canton = prov.id_canton;
                v.provincia = prov.id_provincia;
                v.rucAux = prov.prov_ruc;
                v.idEdit = prov.id;
                $("#provProvincia").selectpicker('val', prov.id_provincia);
                $("#provCtaContable").selectpicker('val', prov.fk_codigo_cuenta_contable);

                await v.getCantones();
                await v.getParroquias();

                await v.loadDatosAdicionalesProveedor(prov.id);

                Swal.close();
            },
            async loadDatosAdicionalesProveedor(idProveedor) {
                let {data} = await axios.get(this.url + '/admin/proveedores/datosAdicionalesProveedor/' + idProveedor);
                if (data) {
                    v.vmodelBancos = data.listaCuentasBancarias;
                    v.vmodelRtenciones = data.listaRetenciones;
                }
            },

            async saveUpdateProveedor() {
                let datos = v.formData(v.newProveedor);

                v.vmodelBancos.forEach((bank, index) => {
                    datos.append(`listaCuentasBancarias[${index}][id]`, bank.id);
                    datos.append(`listaCuentasBancarias[${index}][tipo_cuenta]`, bank.tipo_cuenta);
                    datos.append(`listaCuentasBancarias[${index}][numero_cuenta]`, bank.numero_cuenta);
                });

                v.vmodelRtenciones.forEach((ret, index) => {
                    datos.append(`listaRetencionesProveedor[${index}][id]`, ret.id);
                });

                let url = this.url + '/admin/proveedores/saveProveedor';

                if (v.idEdit !== '') {
                    datos.append('idProv', v.idEdit);
                    datos.append('rucAux', v.rucAux);
                    url = this.url + '/admin/proveedores/updateProveedor';
                }
                try {
                    v.loading = true;
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        v.clear();
                        $('#modalProveedores').modal('hide');
                        $('.modal-backdrop').remove();
                    } else if (response.data.status === 'existe') {
                        sweet_msg_dialog('warning', response.data.msg);
                    } else if (response.data.status === 'vacio') {
                        v.formValidacion = response.data.msg;
                    } else if (response.data.status === 'error') {
                        sweet_msg_dialog('error', response.data.msg);
                    } else if (response.data.status === 'warning') {
                        sweet_msg_dialog('warning', response.data.msg);
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    v.loading = false;
                }
            },

            clear() {
                v.newProveedor = {
                    provRuc: '',
                    provNombres: '',
                    provApellidos: '',
                    provRazonSocial: '',
                    provTelefono: '',
                    provCelular: '',
                    provEmail: '',
                    provDireccion: '',
                    provSector: '',
                    provParroquia: '',
                    provTipoProveedor: '',
                    provTipoDocumento: '',
                    provDiasCredito: '',
                    provCtaContable: '',
                    provEstado: true
                };
                v.provincia = '';
                v.canton = '';
                v.listaCanton = [];
                v.listaParroquia = [];
                v.estadoSave = true;
                v.idEdit = '';
                v.formValidacion = [];
                v.listaBancos = [];
                v.listaRetenciones = [];
                v.vmodelBancos = [];
                v.vmodelRtenciones = [];
            },

            formData(obj) {
                var formData = new FormData();
                for (var key in obj) {
                    formData.append(key, obj[key]);
                }
                return formData;
            },

            zFill(val) {
                return zFill(val, 4);
            }

        }
    });

</script>
