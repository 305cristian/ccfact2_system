<!DOCTYPE html>
<!--
/**
 * Description of viewNewAjuste
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 8 oct 2025
 * @time 4:47:22 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<style>
    .multiselect__tags {
        border-radius: 5px 0px 0px 5px
    }
</style>
<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-folder-blank"></i> Nuevo Ajuste de Entrada</h5>
        </div>
        <div class="card-body">

            <fieldset>
                <legend>Datos Generales</legend>
                <div class="row">
                    <!-- Fecha -->
                    <div class="col-md-2 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system"><i class="fas fa-calendar me-2"></i>Fecha</span>
                            <input v-model="formDataAjuste.ajenFecha" type="date" class="form-control">
                        </div>
                    </div>
                    <!-- Subacuenta -->
                    <div class="col-md-3 form-group-custom">
                        <div class="input-group">                  
                            <span class="input-group-text bg-cris-system" id="basic-addon1"><i class="fas fa-receipt me-2"></i> Sustento</span>
                            <select title="Seleccione un sustento" v-model="formDataAjuste.ajenSustento" class="form-control selectpicker show-tick borderspk" data-live-search="true" data-style="btn-white">
                                <option v-for="ls of listaSustentos" v-bind:value="ls.sus_codigo">{{ls.sus_nombre}}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Bodega -->
                    <div class="col-md-2 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system"><i class="fas fa-warehouse me-2"></i>Bodega</span>
                            <select title="Seleccione una bodega" v-model="formDataAjuste.ajenBodega" class="form-control selectpicker show-tick borderspk"  data-live-search="true" data-style="btn-white">

                                <option v-for="lb of listaBodegas" v-bind:value="lb.id">{{lb.bod_nombre}}</option>
                            </select>
                        </div>
                    </div>
                    <!-- Motivo de Ajuste -->
                    <div class="col-md-2 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system"><i class="fas fa-tag me-2"></i>Motivo</span>
                            <select title="Seleccione un motivo" v-model="formDataAjuste.ajenMotivo" class="form-control selectpicker show-tick borderspk" data-live-search="true" data-style="btn-white">

                                <option v-for="lm of listaMotivos" v-bind:value="lm.id">{{lm.mot_nombre}}</option>

                            </select>
                        </div>
                    </div>
                    <!-- Centro de Costo -->
                    <div class="col-md-3 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system"><i class="fas fa-project-diagram me-2"></i>Centro de Costo</span>
                            <select title="Seleccione un centro de costos" v-model="formDataAjuste.ajenCentrocosto" class="form-control selectpicker show-tick borderspk" data-live-search="true" data-style="btn-white">
                                <option v-for="lcc of listaCentroCostos" v-bind:value="lcc.id">{{lcc.cc_nombre}}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Observaciones -->
                    <div class="col-md-6 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system"><i class="fas fa-comments me-2"></i>Observaciones</span>
                            <input v-model="formDataAjuste.ajenObservaciones" type="text" class="form-control" placeholder="Observaciones...">
                        </div>
                    </div>
                    <!-- Estado -->
                    <div class="col-md-3 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system"><i class="fas fa-toggle-on me-2"></i>Estado</span>
                            <select title="Seleccione un estado" v-model="formDataAjuste.ajenEstado" class="form-control selectpicker show-tick borderspk" data-style="btn-white">               
                                <option value="2">ARCHIVAR</option>
                                <option value="1">BORRADOR</option>
                            </select>
                        </div>
                    </div>
                    <!-- Botones de Seleccion -->
                    <div class="col-md-3 form-group-custom">
                        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                            <input type="radio" class="btn-check" value="AJUSTE_INICIAL" v-model="formDataAjuste.ajenTipo"  autocomplete="off" >
                            <label class="btn btn-outline-success" for="btnradio1"> <i class="fas fa-file-archive me-2"></i> Ajuste Inicial</label>
                            <input type="radio" class="btn-check" value="COMPRA_SIN_FACTURA" v-model="formDataAjuste.ajenTipo" autocomplete="off" checked>
                            <label class="btn btn-outline-primary" for="btnradio2"> <i class="fas fa-file me-2"></i> Compra sin Factura</label>
                        </div>
                    </div>

                </div>
            </fieldset>

            <br>
            <fieldset>
                <legend>Búsqueda de Productos y Selección des proveedor</legend>
                <!-- Datos del Producto -->
                <div class="row">

                    <div class="col-md-4 form-group-custom">
                        <div class="d-flex justify-content-between align-items-center">
                            <vue-multiselect
                                v-model="productoVmodel" 
                                tag-placeholder="Producto no Encontrado"
                                placeholder="Buscar Productos"
                                label="prod_nombre"
                                track-by="id"
                                :multiple="false"
                                :searchable="true"
                                :options-limit="10"
                                :show-no-results="true"
                                :options="listaSearchProductos"
                                @remove="onRemove($event)"
                                @input="insertProduct($event)"
                                @search-change="searchProductos($event)">

                                <template slot="option" slot-scope="{ option }">
                                    <span style="font-size: 12px"><strong>{{ option.codigos+': '}}</strong> {{  option.prod_nombre}} </span>
                                </template>
                            </vue-multiselect>
                            <span class="input-group-text" style="border-radius: 0px 5px 5px 0px"><i class="fas fa-search"></i></span>
                        </div>
                    </div>

                    <div class="col-md-3 form-group-custom">
                        <div class="input-group">
                            <input type="text" class="form-control" v-model="codeSearch" placeholder="Cod. Producto / Cod. Común / Código de Barras" @keyup.enter="insertProductCode($event)">
                            <span class="input-group-text"><i class="fas fa-qrcode"></i></span>
                        </div>
                    </div>
                    <div class="col-md-4 form-group-custom ">
                        <div class="d-flex justify-content-between align-items-center ">  
                            <vue-multiselect
                                v-model="formDataAjuste.ajenProveedor" 
                                tag-placeholder="Proveedor no Encontrado"
                                placeholder="Buscar Proveedores"
                                label="proveedor"
                                track-by="prov_ruc"
                                :multiple="false"
                                :searchable="true"
                                :options-limit="10"
                                :show-no-results="true"
                                :options="listaSearchProveedores"
                                @remove="onRemove($event)"
                                @search-change="searchProveedor($event)">

                                <template slot="option" slot-scope="{ option }">
                                    <span style="font-size: 12px"><strong>{{ option.prov_ruc+': '}} </strong> {{  option.prov_razon_social}}</span>
                                </template>
                            </vue-multiselect>
                            <span class="input-group-text" style="border-radius: 0px 5px 5px 0px"><i class="fas fa-user-tie"></i></span>
                        </div>
                    </div>
                </div>        
            </fieldset>
            <br>

            <!--VIEW CART-->
            <?php echo view('\Modules\AjustesEntrada\Views\viewCart') ?>
            <!--VIEW CART-->

            <!-- Botones de Control -->
            <div class="row mt-4 mb-5">
                <div class="col-12 d-flex gap-3 justify-content-end">
                    <button class="btngr btn-danger-gradiant" style="min-width: 150px;">
                        <i class="fas fa-times-circle me-2"></i>Cancelar
                    </button>
                    <button class="btngr btn-primary-gradiant" style="min-width: 150px;" @click="saveAjuste()">
                        <i class="fas fa-save me-2"></i>Grabar Ajuste
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">

    var listaSustentos = <?= json_encode($listaSustentos); ?>;
    var listaBodegas = <?= json_encode($listaBodegas); ?>;
    var listaMotivos = <?= json_encode($listaMotivos); ?>;
    var listaCentroCostos = <?= json_encode($listaCentroCostos); ?>;

    var permitirDuplicados = <?php echo getSettings('PERMITIR_ITEMS_DUPLICADOS'); ?>;

    var searchTimeout = null;

    var v = new Vue({
        el: '#app',
        components: {
            "vue-multiselect": window.VueMultiselect.default
        },
        data: {
            url: siteUrl,

            //LISTAS PARA EL PROCESO
            listaSustentos: listaSustentos,
            listaBodegas: listaBodegas,
            listaMotivos: listaMotivos,
            listaCentroCostos: listaCentroCostos,

            formDataAjuste: {
                ajenSustento: '',
                ajenBodega: '',
                ajenCentrocosto: '',
                ajenFecha: new Date().toISOString().split('T')[0],
                ajenMotivo: '',
                ajenEstado: '',
                ajenObservaciones: '',
                ajenProveedor: '',
                ajenTipo: 'COMPRA_SIN_FACTURA'
            },

            //DATOS DEL CART
            listaCartData: [],
            totalCart: '',
            totalIva: '',
            totalCartIva: '',
            totalItems: '',
            totalArticles: '',
            totalBienes: '',
            totalServicios: '',
            emptyCar: true,

            //VUE-MULTISELECT PROVEEDOR
            listaSearchProveedores: [],

            permitirDuplicados: permitirDuplicados,

            //VUE-MULTISELECT PRODUCTOS
            listaSearchProductos: [],
            productoVmodel: null,
            codeSearch: "",

            loading: false
        },
        created() {
            this.showDetailCart();
        },
        methods: {

            async saveAjuste() {

                this.formDataAjuste.ajenProveedor = this.formDataAjuste.ajenProveedor ? this.formDataAjuste.ajenProveedor.id : "-1";
                let datos = this.formData(this.formDataAjuste);

                try {
                    this.loading = true;

                    let {data} = await axios.post(this.url + '/ajustesentrada/saveAjuste', datos);
                    if (data.status === "success") {
                        sweet_msg_dialog('success', data.msg, '/ajustesentrada/nuevoAjuste');
                    } else if (data.status === "warning") {
                        sweet_msg_dialog('warning', data.msg);
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                } finally {
                    this.loading = false;
                }


            },

            //SEARCH PROVEEDORES
            searchProveedor(dataSerach) {
                clearTimeout(searchTimeout);
                const datos = {dataSerach};
                searchTimeout = setTimeout(async () => {
                    try {
                        const {data} = await axios.post(this.url + '/comun/proveedores/searchProveedor', datos);
                        if (data !== false) {
                            this.listaSearchProveedores = data;
                        } else {
                            this.listaSearchProveedores = [];
                        }
                    } catch (e) {
                        sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                        this.listaSearchProveedores = [];
                    }
                }, 500);

            },

            //SEARCH PRODUCTOS
            searchProductos(dataSerach) {
                clearTimeout(searchTimeout);
                let datos = {dataSerach: dataSerach};
                searchTimeout = setTimeout(async () => {
                    try {
                        let {data} = await axios.post(this.url + '/comun/productos/searchProductos', datos);
                        if (data !== false) {
                            this.listaSearchProductos = data;
                        } else {
                            this.listaSearchProductos = [];
                        }
                    } catch (e) {
                        sweet_msg_dialog('error', '', '', e.data.message);
                        this.listaSearchProductos = [];
                    }
                }, 500);


            },

            onRemove() {
                this.listaSearchProductos = [];
                this.productoVmodel = "";
                this.codeSearch = "";
            },

            async insertProductCode(evt) {
                if (evt.target.value === "") {
                    sweet_msg_toast('warning', 'Por favor digite un código');
                    return false;
                }
                let datos = {id: evt.target.value};
                await this.insertProductCart(datos);

                //LA VALIDACION DE EXISTENCIA DEL PRODUCTO SE LA REALIZARA AL MOMENTO DE INSERTARLO

            },
            async insertProductCart(item) {
                this.onRemove();//Removemos datos del anterior producto insertado

                let datos = {
                    id: item.id,
                    qty: 1,
                    permitirDuplicados: this.permitirDuplicados
                };

                try {
                    this.loading = true;

                    let {data} = await axios.post(this.url + '/ajustesentrada/insertProduct', datos);
                    if (data.status === "success") {
                        sweet_msg_toast('success', data.msg);
                    } else if (data.status === "warning") {
                        sweet_msg_toast('warning', data.msg);
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                } finally {
                    this.loading = false;
                }

                this.showDetailCart();
            },

            async updateProductCart(item) {
                this.onRemove();//Removemos datos del anterior producto insertado

                if (item.qty <= 0) {
                    item.qty = 1;
                    sweet_msg_toast('warning', 'La cantidad debe ser mayor a cero');
                    return false;
                }

                let datos = item;

                try {
                    this.loading = true;

                    let {data} = await axios.post(this.url + '/ajustesentrada/updateProduct', datos);
                    if (data.status === "success") {
                        sweet_msg_toast('success', data.msg);
                    } else if (data.status === "warning") {
                        sweet_msg_toast('warning', data.msg);
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                } finally {
                    this.loading = false;
                }

                this.showDetailCart();
            },

            async showDetailCart() {

                try {
                    let {data} = await axios.post(this.url + '/ajustesentrada/showDetailCart');

                    if (data.totalArticles > 0) {

                        //TODO DATOS LISTAS
                        this.listaCartData = data.cartContent;
                        this.totalArticles = data.totalArticles;
                        this.totalItems = data.totalItems;
                        this.totalCart = data.totalCart;
                        this.totalCartIva = data.totalCartIva;
                        this.totalIva = data.totalIva;
                        this.totalBienes = data.totalBienes;
                        this.totalServicios = data.totalServicios;
                        this.emptyCar = false;

                    } else {
                        this.emptyCar = true;
                        this.listaCartData = [];
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                }


            },

            agregarProducto() {
                this.cartAjuste.push({
                    codigo: 'PROD001',
                    nombre: 'Producto de Ejemplo',
                    lote: '',
                    fechaCaducidad: '',
                    cantidad: 1,
                    precio: 100.00,
                    stock: 0,
                    seleccionado: false
                });
            },

            async deleteProduct(rowId) {

                try {
                    this.loading = true;
                    await axios.post(this.url + '/ajustesentrada/deleteProduct/' + rowId);
                    this.showDetailCart();
                    sweet_msg_toast('info', 'Producto eliminado exitosamente');
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                } finally {
                    this.loading = false;
                }



            },

            formatMoney(amount) {
                return parseFloat(amount).toFixed(2);
            },

            formData(obj) {
                var formData = new FormData();
                for (var key in obj) {
                    formData.append(key, obj[key]);
                }
                return formData;
            },

        },

    });

</script>