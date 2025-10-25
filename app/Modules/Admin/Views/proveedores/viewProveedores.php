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
                    <label  class="col-form-label col-form-label-sm"><i class="fal fa-user-tie"></i> Nombres</label>
                    <vue-multiselect
                        v-model="keyProveedor"
                        tag-placeholder="Proveedor no Encontrado"
                        placeholder="Buscar Por Nombres"
                        label="prov_razon_social"
                        track-by="prov_ruc"
                        :multiple="false"
                        :searchable="true"
                        :options-limit="10"
                        :show-no-results="true"
                        :options="listaSearchProveedores"
                        @remove="onRemove($event)"
                        @select="setDataCiruc($event)"
                        @search-change="searchProveedores($event)">

                        <template #option="{option}">
                            <span style="font-size: 12px">{{ option.prov_razon_social+': '}} <strong>{{ option.prov_ruc }} </strong></span>
                        </template>
                    </vue-multiselect>
                </div>

                <div class="col-md-3 mb-2">
                    <label for="cirucProveedor" class="col-form-label col-form-label-sm"><i class="fal fa-qrcode"></i> CI/RUC</label>
                    <input  v-model="cirucProveedor" type="number" class="form-control " id="cirucProveedor" placeholder="Digite la CI/RUC" />                               
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

    if (window.appProveedores) {
        window.appProveedores.unmount();
    }

    window.appProveedores = Vue.createApp({
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
                keyProveedor: null,
                listaSearchProveedores: [],
                formValidacion: []
            };
        },
        created() {

        },
        mounted() {
            panelMain.style.display = "none";
            $(".selectpicker").selectpicker();
        },
        methods: {

            async getRetenciones(dataSerach) {
                let datos = {dataSerach: dataSerach};
                try {
                    this.isLoadingRet = true;
                    let {data} = await axios.post(this.url + "/admin/proveedores/getRetenciones", datos);
                    if (data) {
                        this.listaRetenciones = data;
                    } else {
                        this.listaRetenciones = [];
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data.message);
                } finally {
                    this.isLoadingRet = false;
                }
            },

            async getBancos(dataSerach) {
                let datos = {dataSerach: dataSerach};
                try {
                    this.isLoadingBank = true;
                    let {data} = await axios.post(this.url + "/admin/proveedores/getBancos", datos);
                    if (data) {
                        this.listaBancos = data;
                    } else {
                        this.listaBancos = [];
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data.message);
                } finally {
                    this.isLoadingBank = false;
                }


            },

            async searchProveedores(dataSerach) {
                console.log(dataSerach);
                let datos = {dataSerach: dataSerach};
                try {
                    let {data} = await axios.post(this.url + '/admin/proveedores/searchProveedores', datos);
                    if (data !== false) {
                        this.listaSearchProveedores = data;
                    } else {
                        this.listaSearchProveedores = [];
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data.message);
                    this.listaSearchProveedores = [];
                }

            },
            setDataCiruc(data) {
                this.cirucProveedor = data ? data.prov_ruc : "";
            },
            onRemove() {
                this.keyProveedor = [];
                this.listaSearchProveedores = [];
            },
            async getProveedores() {
                let datos = {
                    ciruc: this.cirucProveedor ? this.cirucProveedor : ""
                };

                try {

                    this.loading = true;
                    let response = await axios.post(this.url + '/admin/proveedores/getProveedores', datos);
                    if (response.data.status === "success") {
                        this.listaProveedores = response.data.data;
                        panelMain.style.display = "block";
                        panelBtnCreate.style.display = "none";

                    } else {
                        sweet_msg_dialog('warning', response.data.msg);
                        panelMain.style.display = "none";
                    }
                    if (this.admin) {
                        dataTableModalBtn('#tblProveedores', 'Lista de Proveedores', '#modalProveedores', 'CREAR PROVEEDOR');
                    } else {
                        dataTable('#tblProveedores', 'Lista de Proveedores');
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    this.loading = false;
                }
            },
            async getCantones() {
                let id = this.provincia ? this.provincia : 0;
                let {data} = await axios.get(this.url + "/comun/clientes/getCantones/" + id);
                if (data) {
                    this.listaCanton = data;
                    this.listaParroquia = [];
                } else {
                    this.listaCanton = [];
                }
            },
            async getParroquias() {
                let id = this.canton ? this.canton : 0;
                let {data} = await axios.get(this.url + "/comun/clientes/getParroquias/" + id);
                if (data) {
                    this.listaParroquia = data;
                } else {
                    this.listaParroquia = [];
                }
            },
            setTipoProveedor() {
                if (this.newProveedor.provTipoDocumento === '1') {
                    this.newProveedor.provTipoProveedor = "2";
                } else if (this.newProveedor.provTipoDocumento === '2') {
                    this.newProveedor.provTipoProveedor = "1";
                } else if (this.newProveedor.provTipoDocumento === '3') {
                    this.newProveedor.provTipoProveedor = "4";
                } else {
                    this.newProveedor.provTipoProveedor = "4";
                }
            },

            setRazonSocial() {
                let nombres = this.newProveedor.provNombres;
                let apellidos = this.newProveedor.provApellidos;
                this.newProveedor.provRazonSocial = `${nombres.toUpperCase()} ${apellidos.toUpperCase()}`;
            },

            async loadProveedor(prov) {
                swalLoading('Cargando...', );
                this.newProveedor = {
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
                this.canton = prov.id_canton;
                this.provincia = prov.id_provincia;
                this.rucAux = prov.prov_ruc;
                this.idEdit = prov.id;
                $("#provProvincia").selectpicker('val', prov.id_provincia);
                $("#provCtaContable").selectpicker('val', prov.fk_codigo_cuenta_contable);

                await this.getCantones();
                await this.getParroquias();

                await this.loadDatosAdicionalesProveedor(prov.id);

                Swal.close();
            },
            async loadDatosAdicionalesProveedor(idProveedor) {
                let {data} = await axios.get(this.url + '/admin/proveedores/datosAdicionalesProveedor/' + idProveedor);
                if (data) {
                    this.vmodelBancos = data.listaCuentasBancarias;
                    this.vmodelRtenciones = data.listaRetenciones;
                }
            },

            async saveUpdateProveedor() {
                let datos = this.formData(this.newProveedor);

                this.vmodelBancos.forEach((bank, index) => {
                    datos.append(`listaCuentasBancarias[${index}][id]`, bank.id);
                    datos.append(`listaCuentasBancarias[${index}][tipo_cuenta]`, bank.tipo_cuenta);
                    datos.append(`listaCuentasBancarias[${index}][numero_cuenta]`, bank.numero_cuenta);
                });

                this.vmodelRtenciones.forEach((ret, index) => {
                    datos.append(`listaRetencionesProveedor[${index}][id]`, ret.id);
                });

                let url = this.url + '/admin/proveedores/saveProveedor';

                if (this.idEdit !== '') {
                    datos.append('idProv', this.idEdit);
                    datos.append('rucAux', this.rucAux);
                    url = this.url + '/admin/proveedores/updateProveedor';
                }
                try {
                    this.loading = true;
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        $('#modalProveedores').modal('hide');
                        $('.modal-backdrop').remove();
                    } else if (response.data.status === 'existe') {
                        sweet_msg_dialog('warning', response.data.msg);
                    } else if (response.data.status === 'vacio') {
                        this.formValidacion = response.data.msg;
                    } else if (response.data.status === 'error') {
                        sweet_msg_dialog('error', response.data.msg);
                    } else if (response.data.status === 'warning') {
                        sweet_msg_dialog('warning', response.data.msg);
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    this.loading = false;
                }
            },

            clear() {
                this.newProveedor = {
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
                this.provincia = '';
                this.canton = '';
                this.listaCanton = [];
                this.listaParroquia = [];
                this.estadoSave = true;
                this.idEdit = '';
                this.formValidacion = [];
                this.listaBancos = [];
                this.listaRetenciones = [];
                this.vmodelBancos = [];
                this.vmodelRtenciones = [];
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
//    window.appProveedores.component('vue-multiselect', window['vue-multiselect'].Multiselect);
    window.appProveedores.mount('#app');

</script>
