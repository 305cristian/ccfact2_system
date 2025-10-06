<!DOCTYPE html>
<!--
/**
 * Description of viewClientes
 *
/**
 * @author CRISTIAN PAZ
 * @date 15 abr. 2024
 * @time 12:36:55
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-users-cog"></i> Administrar Clientes</h5>
        </div>
        <div class="card-body">
            <div class="row col-md-12">


                <div class="col-md-3 mb-2">
                    <label for="selectStock" class="col-form-label col-form-label-sm"><i class="fal fa-user-tie"></i> Nombres</label>
                    <vue-multiselect
                        v-model="keyCliente" 
                        tag-placeholder="Cliente no Encontrado"
                        placeholder="Buscar Por Nombres"
                        label="clie_razon_social"
                        track-by="clie_dni"
                        :multiple="false"
                        :searchable="true"
                        :taggable="true"
                        :options-limit="10"
                        :show-no-results="true"
                        :options="listaSearchClientes"
                        @remove="onRemove($event)"
                        @input="setDataCiruc($event)"
                        @search-change="searchClientes($event)"/>

                    <template slot="option" slot-scope="{ option }">
                        <span style="font-size: 12px">{{ option.clie_razon_social+': ' }} <strong>{{ option.clie_dni }} </strong></span>
                    </template>
                    </vue-multiselect>
                </div>

                <div class="col-md-3 mb-2">
                    <label for="cirucCliente" class="col-form-label col-form-label-sm"><i class="fal fa-qrcode"></i> CI/RUC</label>
                    <input  v-model="cirucCliente" type="number" class="form-control" id="cirucCliente" placeholder="Digite la CI/RUC" />                               
                </div>

                <div class="col-md-2  mb-2" style="position: relative; top: 30px">
                    <button class="btn btn-system-2" @click="getClientes()">
                        <span v-if='loading'><i class="loading-spin"></i> Buscando...</span>
                        <span v-else><i class="fas fa-search"></i> Buscar Clientes</span>
                    </button>
                </div>
                <div id="panelBtnCreate" class="col-md-2  mb-2" style="position: relative; top: 30px">
                    <button class="btn btn-system-2" data-bs-toggle="modal" data-bs-target="#modalClientes"><span class="fas fa-user-tie"></span> Crear Cliente</button>
                </div>
            </div>
            <br>
            <hr>
            <br>
            <div id="panelMain" class="col-md-12">
                <?php echo view('\Modules\Admin\Views\clientes\viewTable') ?>
            </div>
        </div>
        <!--MODAL CLIENTES-->
        <?php echo view('\Modules\Admin\Views\clientes\viewModal') ?>
        <!--CLOSE MODAL CLIENTES-->
    </div>
</div>

<script type="text/javascript">

<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';
    var listaTipoDocumento = <?php echo json_encode($listaTipoDocumento) ?>;
    var listaProvincia = <?php echo json_encode($listaProvincia) ?>;

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
            cirucCliente: '',
            provincia: '',
            canton: '',
            idEdit: '',
            newCliente: {
                clieCiruc: '',
                clieNombres: '',
                clieApellidos: '',
                clieRazonSocial: '',
                clieSexo: '',
                clieGenero: '',
                clieTelefono: '',
                clieCelular: '',
                clieEmail: '',
                clieDireccion: '',
                clieParroquia: '',
                clieTipoCliente: '',
                clieTipoDocumento: '',
                clieDiasCredito: '',
                clieCupoCredito: '',
                clieEstado: true
            },

            //TODO: LISTAS
            listaClientes: [],
            listaTipoDocumento: listaTipoDocumento,
            listaProvincia: listaProvincia,
            listaCanton: [],
            listaParroquia: [],

            keyCliente: [],
            listaSearchClientes: [],

            formValidacion: []
        },
        created() {
            panelMain.style.display = "none";
        },
        mounted() {
            $(".selectpicker").selectpicker();
        },
        methods: {
            async searchClientes(dataSerach) {
                let datos = {dataSerach: dataSerach};
                try {
                    let {data} = await axios.post(this.url + '/admin/clientes/searchClientes', datos);
                    if (data !== false) {
                        v.listaSearchClientes = data;
                    } else {
                        v.listaSearchClientes = [];
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data.message);
                    v.listaSearchClientes = [];
                }

            },
            setDataCiruc(data) {
                v.cirucCliente = data ? data.clie_dni : "";
            },
            onRemove() {
                v.keyCliente = [];
                v.listaSearchClientes = [];
            },
            async getClientes() {
                let datos = {
                    ciruc: v.cirucCliente ? v.cirucCliente : ""
                };

                try {
                    v.loading = true;
                    let response = await axios.post(this.url + '/admin/clientes/getClientes', datos);
                    if (response.data.status === "success") {
                        v.listaClientes = response.data.data;
                        panelMain.style.display = "block";
                        panelBtnCreate.style.display = "none";

                    } else {
                        sweet_msg_dialog('warning', response.data.msg);
                        panelMain.style.display = "none";
                    }
                    if (v.admin) {
                        dataTableModalBtn('#tblClientes', 'Lista de Clientes', '#modalClientes', 'CREAR CLIENTE');
                    } else {
                        dataTable('#tblClientes', 'Lista de Clientes');
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
            setTipoCliente() {
                if (v.newCliente.clieTipoDocumento === '1') {
                    v.newCliente.clieTipoCliente = "2";
                } else if (v.newCliente.clieTipoDocumento === '2') {
                    v.newCliente.clieTipoCliente = "1";
                } else if (v.newCliente.clieTipoDocumento === '3') {
                    v.newCliente.clieTipoCliente = "4";
                } else {
                    v.newCliente.clieTipoCliente = "4";
                }
            },
            setRazonSocial() {
                let nombres = v.newCliente.clieNombres;
                let apellidos = v.newCliente.clieApellidos;
                v.newCliente.clieRazonSocial = `${nombres.toUpperCase()} ${apellidos.toUpperCase()}`;
            },

            async loadCliente(clie) {
                swalLoading('Cargando...', );
                v.newCliente = {
                    clieCiruc: clie.clie_dni,
                    clieNombres: clie.clie_nombres,
                    clieApellidos: clie.clie_apellidos,
                    clieRazonSocial: clie.clie_razon_social,
                    clieSexo: clie.clie_sexo,
                    clieGenero: clie.clie_genero,
                    clieTelefono: clie.clie_telefono,
                    clieCelular: clie.clie_celular,
                    clieEmail: clie.clie_email,
                    clieDireccion: clie.clie_direccion,
                    clieParroquia: clie.fk_parroquia,
                    clieTipoCliente: clie.fk_tipo_sujeto,
                    clieTipoDocumento: clie.fk_tipo_documento,
                    clieDiasCredito: clie.clie_dias_credito,
                    clieCupoCredito: clie.clie_cupo_credito,
                    clieEstado: clie.clie_estado === "1" ? true : false
                };
                v.canton = clie.id_canton;
                v.provincia = clie.id_provincia;
                v.ciRucAux = clie.clie_dni;
                v.idEdit = clie.id;

                $("#clieProvincia").selectpicker('val', clie.id_provincia);

                await v.getCantones();
                await v.getParroquias();

                Swal.close();
            },

            async saveUpdateCliente() {

                let datos = v.formData(v.newCliente);
                let url = this.url + '/admin/clientes/saveCliente';

                if (v.idEdit !== '') {
                    datos.append('idClie', v.idEdit);
                    datos.append('ciRucAux', v.ciRucAux);//TODO: ESTA VARIABLE SE LA USA PARA VALIDAR QUE NO EXISTA OTRA REGISTRO CON EL MISMO CODIGO
                    url = this.url + '/admin/clientes/updateCliente';
                }
                try {
                    v.loading = true;
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        v.clear();
//                        v.getClientes();
                        $('#modalClientes').modal('hide');
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
                v.newCliente = {
                    clieCiruc: '',
                    clieNombres: '',
                    clieApellidos: '',
                    clieRazonSocial: '',
                    clieSexo: '',
                    clieGenero: '',
                    clieTelefono: '',
                    clieCelular: '',
                    clieEmail: '',
                    clieDireccion: '',
                    clieParroquia: '',
                    clieTipoCliente: '',
                    clieTipoDocumento: '',
                    clieDiasCredito: '',
                    clieCupoCredito: '',
                    clieEstado: true
                };
                v.provincia = '';
                v.canton = '';
                v.listaCanton = [];
                v.listaParroquia = [];
                v.estadoSave = true;
                v.idEdit = '';
                v.formValidacion = [];
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