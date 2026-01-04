<!DOCTYPE html>
<!--
/**
 * Description of viewNewAjuste
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 26 nov 2025
 * @time 10:36:42 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<style>
    .multiselect__tags {
        border-radius: 5px 0px 0px 5px
    }
</style>
<link rel="stylesheet" href="<?php echo base_url(); ?>/resources/css/styleModalPosition.css">

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 v-if="isEdit" class="card-title text-system">
                <i class="fas fa-folder-blank"></i> Actualizar Ajuste de Salida
            </h5>
            <h5 v-else class="card-title text-system">
                <i class="fas fa-folder-blank"></i> Nuevo Ajuste de Salida
            </h5>
        </div>

        <div class="card-body">

            <fieldset>
                <legend>Datos Generales</legend>
                <div class="row">
                    <!-- Fecha -->
                    <div class="col-md-2 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-calendar me-2"></i>Fecha
                            </span>
                            <input v-model="formDataAjuste.ajesFecha" type="date" class="form-control">
                        </div>
                    </div>

                    <!-- Bodega -->
                    <div class="col-md-2 form-group-custom">
                        <div class="d-flex justify-content-between align-items-center border">
                            <span class="input-group-text bg-cris-system">
                                <span v-if='loadingBodega'><i class="loading-spin"></i></span>
                                <span v-else><i class="fas fa-warehouse me-2"></i></span>
                                Bodega
                            </span>
                            <vue-select 
                                class="flex-grow-1" 
                                @option:selected="changeBodega"
                                :options="listaBodegas" 
                                label="bod_nombre" 
                                v-model="formDataAjuste.ajesBodega" 
                                placeholder="Seleccione una bodega"/>
                        </div>
                    </div>

                    <!-- Motivo de Ajuste -->
                    <div class="col-md-3 form-group-custom">
                        <div class="d-flex justify-content-between align-items-center border">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-tag me-2"></i>Motivo
                            </span>
                            <vue-select 
                                class="flex-grow-1"
                                :options="listaMotivos"
                                label="motivo"
                                v-model="formDataAjuste.ajesMotivo"
                                placeholder="Seleccione un motivo"/>
                        </div>
                    </div>

                    <!-- Centro de Costo -->
                    <div class="col-md-3 form-group-custom">
                        <div class="d-flex justify-content-between align-items-center border">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-project-diagram me-2"></i>Centro de Costo
                            </span>
                            <vue-select 
                                class="flex-grow-1"
                                :options="listaCentroCostos"
                                label="cc_nombre"
                                v-model="formDataAjuste.ajesCentrocosto"
                                placeholder="Seleccione un centro de costos"/>
                        </div>
                    </div>

                    <!-- Servicio -->
                    <div class="col-md-2 form-group-custom">
                        <div class="d-flex justify-content-between align-items-center border">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-receipt me-2"></i>Servicio
                            </span>
                            <vue-select 
                                class="flex-grow-1"
                                :options="listaServicios"
                                label="serv_nombre"
                                v-model="formDataAjuste.ajesServicio"
                                placeholder="Seleccione un servicio"/>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <!-- Observaciones -->
                    <div class="col-md-6 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-comments me-2"></i>Observaciones
                            </span>
                            <input v-model="formDataAjuste.ajesObservaciones"
                                   type="text"
                                   class="form-control"
                                   placeholder="Observaciones...">
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="col-md-2 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-toggle-on me-2"></i>Estado
                            </span>
                            <select title="Seleccione un estado"
                                    v-model="formDataAjuste.ajesEstado"
                                    class="form-select show-tick borderspk"
                                    data-style="btn-white">
                                <option disabled value="">Seleccione un estado</option>
                                <option value="2">ARCHIVAR</option>
                                <option value="1">BORRADOR</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tipo de ajuste salida -->
                    <div class="col-md-4 form-group-custom">
                        <div class="btn-group" role="group" aria-label="Tipo de ajuste">
                            <input type="radio"
                                   class="btn-check"
                                   id="btnradio_s1"
                                   value="AJUSTE_MERMA"
                                   v-model="formDataAjuste.ajesTipo"
                                   autocomplete="off">
                            <label class="btn btn-outline-danger" for="btnradio_s1">
                                <i class="fas fa-skull-crossbones me-2"></i> Merma / Daño
                            </label>

                            <input type="radio"
                                   class="btn-check"
                                   id="btnradio_s2"
                                   value="CONSUMO_INTERNO"
                                   v-model="formDataAjuste.ajesTipo"
                                   autocomplete="off">
                            <label class="btn btn-outline-primary" for="btnradio_s2">
                                <i class="fas fa-utensils me-2"></i> Consumo Interno
                            </label>
                            <input type="radio"
                                   class="btn-check"
                                   id="btnradio_s3"
                                   value="DESPACHO"
                                   v-model="formDataAjuste.ajesTipo"
                                   autocomplete="off">
                            <label class="btn btn-outline-success" for="btnradio_s3">
                                <i class="fas fa-clipboard-check me-2"></i> Despacho
                            </label>
                        </div>
                    </div>
                </div>
            </fieldset>

            <br>

            <fieldset>
                <legend>Importación y Búsqueda de Productos</legend>
                <div class="row">

                    <div class="col-md-1 text-start">
                        <button 
                            class="btn btn-sm"
                            :class="mostrarImportacion ? 'btn-outline-danger' : 'btn-outline-success'"
                            @click="mostrarImportacion = !mostrarImportacion">
                            <i v-if="!mostrarImportacion" class="fas fa-file-excel fa-1x me-2"></i>
                            <i v-else class="fas fa-eye-slash me-2"></i>
                            {{ mostrarImportacion ? 'Ocultar' : 'Importar' }}
                        </button>
                    </div>

                    <!-- Buscador productos multiselect -->
                    <div class="col-md-4 form-group-custom">
                        <div class="d-flex justify-content-between align-items-center">
                            <vue-multiselect
                                v-model="productoVmodel" 
                                tag-placeholder="Producto no Encontrado"
                                placeholder="Buscar Productos"
                                label="prod_nombre"
                                track-by="id"
                                :disabled='loading'
                                :multiple="false"
                                :searchable="true"
                                :options-limit="10"
                                :show-no-results="true"
                                :options="listaSearchProductos"
                                @remove="onRemove($event)"
                                @select="insertProductCart($event)"
                                @search-change="searchProductos($event)">

                                <template #option="{ option }">
                                    <div class="producto-option-row">
                                        <div class="row g-2 align-items-center w-100">
                                            <div class="col-auto">
                                                <span class="badge bg-primary">{{ option.codigos }}</span>
                                            </div>
                                            <div class="col">
                                                <span class="fw-bold text-dark">{{ option.prod_nombre }}</span>
                                            </div>
                                            <div class="col">
                                                <span class="fw-bold text-dark"> <i class="fas fa-box-archive"></i> {{ option.stb_stock }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </vue-multiselect>
                            <span class="input-group-text" style="border-radius: 0px 5px 5px 0px">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Búsqueda por código / barras -->
                    <div class="col-md-3 form-group-custom">
                        <div class="input-group">
                            <input type="text"
                                   class="form-control"
                                   v-model="codeSearch"
                                   :disabled='loading'
                                   placeholder="Cod. Producto / Cod. Común / Código de Barras"
                                   @keyup.enter="insertProductCode($event)">
                            <span class="input-group-text">
                                <i class="fas fa-qrcode"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Búsqueda de clientes -->
                    <div class="col-md-4 form-group-custom ">
                        <div class="d-flex justify-content-between align-items-center ">  
                            <vue-multiselect
                                v-model="formDataAjuste.ajesCliente" 
                                tag-placeholder="Cliente no Encontrado"
                                placeholder="Buscar Clientes"
                                label="cliente"
                                track-by="clie_dni"
                                :multiple="false"
                                :searchable="true"
                                :options-limit="10"
                                :show-no-results="true"
                                :options="listaSearchClientes"
                                @remove="onRemove($event)"
                                @search-change="searchClientes($event)">

                                <template #option="{ option }">
                                    <span style="font-size: 12px"><strong>{{ option.clie_dni+': '}} </strong> {{  option.clie_razon_social}}</span>
                                </template>
                            </vue-multiselect> 
                            <span class="input-group-text" style="border-radius: 0px 5px 5px 0px"><i class="fas fa-user-tie"></i></span>
                        </div>
                    </div>

                </div>        
            </fieldset>

            <br>

            <!-- Importar desde excel -->
            <div v-if="mostrarImportacion">
                <fieldset>
                    <legend>Importar desde excel</legend>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <small class="text-muted">
                                Plantilla: Código, Cantidad, Lote
                            </small>
                            <input 
                                type="file" 
                                @change="loadFilePicked($event)" 
                                accept=".xlsx,.xls" 
                                class="form-control" 
                                ref="excelInput" />

                            <div v-if="excelFilename" class="mt-2 small">
                                <i class="fas fa-paperclip me-1"></i> {{ excelFilename }}
                            </div>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button 
                                class="btn btn-success w-100"
                                :disabled="!selectedExcelFile || loadingProcess"
                                @click="cargarExcel">
                                <span v-if="loadingProcess">
                                    <i class="loading-spin me-2"></i> Cargando...
                                </span>
                                <span v-else>
                                    <i class="fas fa-upload me-2"></i> Cargar Plantilla
                                </span>
                            </button>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <a :href="url + '/comun/descargar/downloadPlantillaExcelSalida'" class="btn btn-outline-primary w-100">
                                <i class="fas fa-download me-2"></i> Descargar Plantilla
                            </a>
                        </div>
                    </div>
                </fieldset>
            </div>

            <br>

            <!-- VIEW CART SALIDA -->
            <?php echo view('\Modules\AjustesSalida\Views\viewCart') ?>
            <!-- FIN VIEW CART -->

            <!-- Botones de Control -->
            <div v-if="!emptyCar" class="row mt-4 mb-5">
                <div class="col-12 d-flex gap-3 justify-content-end">
                    <button @click="cancelarAjuste()"
                             class="btngr btn-danger-gradiant"
                             style="min-width: 150px;"
                             :disabled="loadingProcess">
                        <i class="fas fa-times-circle me-2"></i>Cancelar
                    </button>
                    <button class="btngr btn-primary-gradiant"
                            style="min-width: 150px;"
                            @click="saveAjuste()"
                            :disabled="loadingProcess">
                        <span v-if="loadingProcess">
                            <i class="loading-spin"></i>{{isEdit?'Actualizando...':'Grabando...'}}
                        </span>
                        <span v-else>
                            <i class="fas fa-save me-2"></i>{{isEdit?'Actualizar Ajuste':'Grabar Ajuste'}}
                        </span>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- MODAL DETALLE -->
    <?php echo view('\Modules\AjustesSalida\Views\reportes\viewModalReport') ?>
    <!-- FIN MODAL DETALLE -->
</div>

<script type="text/javascript">
    var fechaActual = DateTime.now().toFormat('yyyy-MM-dd');
    var listaBodegas = <?= json_encode($listaBodegas); ?>;
    var listaMotivos = <?= json_encode($listaMotivos); ?>;
    var listaCentroCostos = <?= json_encode($listaCentroCostos); ?>;
    var listaServicios = <?= json_encode($listaServicios); ?>;
    var permitirDuplicados = <?= $permitirDuplicados ?>;
    var bodegaIdAjs = '<?= $bodegaId; ?>';
    var dataCliente =<?= json_encode($dataCliente); ?>;
    var dataAjuste = <?= json_encode($dataAjuste); ?>;
    var searchTimeout = null;

    if (window.appAjs) {
        window.appAjs.unmount();
    }

    window.appAjs = Vue.createApp({
        components: {
            "vue-multiselect": window['vue-multiselect'].Multiselect,
            "vue-select": window['vue-select']
        },
        data() {
            return {
                url: siteUrl,

                //VARIABLES
                isEdit: false,
                mostrarImportacion: false,
                selectedExcelFile: null,
                excelFilename: '',

                //LISTAS PARA EL PROCESO
                listaBodegas: listaBodegas,
                listaMotivos: listaMotivos,
                listaCentroCostos: listaCentroCostos,
                listaServicios: listaServicios,

                formDataAjuste: {
                    ajesBodega: '',
                    ajesCentrocosto: '',
                    ajesFecha: fechaActual,
                    ajesMotivo: '',
                    ajesServicio: '',
                    ajesEstado: '',
                    ajesObservaciones: '',
                    ajesTipo: 'DESPACHO',
                    ajesCliente: '',
                    ajesPermitirDuplicados: permitirDuplicados
                },

                // CART
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
                listaSearchClientes: [],

                // VUE-MULTISELECT PRODUCTOS
                listaSearchProductos: [],
                productoVmodel: null,
                codeSearch: "",

                // MODAL REPORTE
                idAjuste: '',
                secuencialAjuste: '',
                cargandoDetalle: false,
                modalInstance: null,

                loading: false,
                loadingBodega: false,
                loadingProcess: false
            };
        },
        created() {
            this.showDetailCart();
        },
        mounted() {
            this.formDataAjuste.ajesBodega = this.listaBodegas.find(val => val.id === bodegaIdAjs);

            if (dataAjuste) {
                this.isEdit = true;
                this.formDataAjuste.ajesBodega = this.listaBodegas.find(val => val.id === dataAjuste.fk_bodega);
                this.formDataAjuste.ajesMotivo = this.listaMotivos.find(val => val.id === dataAjuste.fk_motivo_ajuste);
                this.formDataAjuste.ajesCentrocosto = this.listaCentroCostos.find(val => val.id === dataAjuste.fk_centro_costo);
                this.formDataAjuste.ajesServicio = this.listaServicios.find(val => val.id === dataAjuste.fk_servicio);
                this.formDataAjuste.ajesObservaciones = dataAjuste.ajes_observaciones;
//                this.formDataAjuste.ajesEstado = dataAjuste.ajes_estado;
                this.formDataAjuste.ajesTipo = dataAjuste.ajes_tipo;
                this.formDataAjuste.ajesCliente = dataCliente;
                this.formDataAjuste.ajesPermitirDuplicados = dataAjuste.ajes_items_duplicados;
            }

            this.modalInstance = new bootstrap.Modal(this.$refs.modalReport);
        },
        methods: {

            loadFilePicked(event) {
                const file = event.target.files?.[0] || null;
                this.selectedExcelFile = file;
                this.excelFilename = file ? file.name : '';
            },

            async cargarExcel() {
                if (!this.selectedExcelFile) {
                    sweet_msg_toast('warning', 'Seleccione un archivo Excel primero');
                    return;
                }
                if (!this.formDataAjuste.ajesBodega.id) {
                    sweet_msg_toast('warning', 'Debe seleccionar una bodega antes de importar');
                    return;
                }

                const datos = new FormData();
                datos.append('file', this.selectedExcelFile);
                datos.append('bodegaId', this.formDataAjuste.ajesBodega.id);
                datos.append('permitirDuplicados', this.formDataAjuste.ajesPermitirDuplicados);

                try {
                    this.loadingProcess = true;
                    const {data} = await axios.post(this.url + '/ajustessalida/importarExcel', datos);

                    if (data.status === 'success') {
                        sweet_msg_dialog('success', (data.msg || 'Importación completada'));
                        await this.showDetailCart();
                        this.selectedExcelFile = null;
                        this.excelFilename = '';
                        this.$refs.excelInput.value = '';
                        this.mostrarImportacion = false;
                    } else if (data.status === 'warning') {
                        sweet_msg_dialog('warning', data.msg);
                    } else {
                        sweet_msg_dialog('error', '', '', data.msg || 'Error al importar');
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loadingProcess = false;
                }
            },

            validarCampos() {
                const campos = [
                    {key: 'ajesFecha', msg: 'Debe seleccionar una fecha'},
                    {key: 'ajesBodega', msg: 'Debe seleccionar una bodega'},
                    {key: 'ajesCentrocosto', msg: 'Debe seleccionar un centro de costos'},
                    {key: 'ajesMotivo', msg: 'Debe seleccionar un motivo de ajuste'},
                    {key: 'ajesServicio', msg: 'Debe seleccionar un servicio'},
                    {key: 'ajesEstado', msg: 'Debe seleccionar un estado'},
                    {key: 'ajesTipo', msg: 'Debe seleccionar un tipo de ajuste'},
                ];

                for (const campo of campos) {
                    if (!this.formDataAjuste[campo.key]) {
                        return {status: true, msg: campo.msg};
                    }
                }
                return {status: false};
            },

            async saveAjuste() {
                let statusValidation = this.validarCampos();
                if (statusValidation.status) {
                    sweet_msg_toast('warning', statusValidation.msg);
                    return;
                }

                let ruta = this.isEdit ? '/ajustessalida/updateAjuste' : '/ajustessalida/saveAjuste';

                try {
                    this.loadingProcess = true;
                    let datos = this.formData(this.formDataAjuste);
                    datos.append('ajusteId', this.isEdit ? dataAjuste.id : '');

                    let {data} = await axios.post(this.url + ruta, datos);

                    if (data.status === "success") {
                        const url = this.url + '/ajustessalida/nuevoAjuste';
                        sweetMsgDialogConfirm(data.msg, this.verDetalle, data.data, url);
                    } else if (data.status === "warning") {
                        sweet_msg_dialog('warning', data.msg);
                    } else if (data.status === "error") {
                        sweet_msg_dialog('error', data.msg);
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loadingProcess = false;
                }
            },

            //SEARCH PROVEEDORES
            searchClientes(dataSerach) {
                clearTimeout(searchTimeout);
                const datos = {dataSerach};
                searchTimeout = setTimeout(async () => {
                    try {
                        const {data} = await axios.post(this.url + '/comun/clientes/searchClientes', datos);
                        if (data !== false) {
                            this.listaSearchClientes = data;
                        } else {
                            this.listaSearchClientes = [];
                        }
                    } catch (e) {
                        sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                        this.listaSearchClientes = [];
                    }
                }, 500);

            },

            // SEARCH PRODUCTOS
            searchProductos(dataSerach) {
                clearTimeout(searchTimeout);
                let datos = {
                    dataSerach: dataSerach,
                    bodegaId: this.formDataAjuste.ajesBodega.id,
                    estado: 1
                };
                searchTimeout = setTimeout(async () => {
                    try {
                        let {data} = await axios.post(this.url + '/comun/productos/searchProductosStock', datos);
                        if (data !== false) {
                            this.listaSearchProductos = data;
                        } else {
                            this.listaSearchProductos = [];
                        }
                    } catch (e) {
                        sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                        this.listaSearchProductos = [];
                    }
                }, 500);
            },

            async verDetalle(ajuste) {
                this.idAjuste = ajuste.id;
                this.secuencialAjuste = ajuste.ajes_secuencial;
                this.cargandoDetalle = true;
                this.modalInstance.show();
                try {
                    const {data} = await axios.get(this.url + '/ajustessalida/getDataDetalle/' + ajuste.id);
                    this.cargandoDetalle = false;
                    await Vue.nextTick();
                    const modal = document.getElementById('detalleAjusteModal');
                    modal.innerHTML = data;
                } catch (error) {
                    sweet_msg_dialog('error', '', '', 'Error al cargar el detalle del ajuste, ' + error.message);
                } finally {
                    this.cargandoDetalle = false;
                }
            },

            onRemove() {
                this.listaSearchProductos = [];
                this.productoVmodel = "";
                this.codeSearch = "";
            },

            async insertProductCode(evt) {
                if (evt.target.value === "") {
                    sweet_msg_toast('warning', 'Por favor digite un código');
                    return;
                }
                let datos = {id: evt.target.value};
                await this.insertProductCart(datos);
            },

            async insertProductCart(item) {
                this.onRemove();

                if (this.formDataAjuste.ajesBodega === "") {
                    sweet_msg_toast('warning', 'Debe seleccionar una bodega');
                    return;
                }

                let datos = {
                    id: item.id,
                    qty: 1,
                    bodega: this.formDataAjuste.ajesBodega.id,
                    permitirDuplicados: this.formDataAjuste.ajesPermitirDuplicados
                };

                try {
                    this.loading = true;
                    let {data} = await axios.post(this.url + '/ajustessalida/insertProduct', datos);
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
                this.onRemove();

                if (item.qty <= 0) {
                    item.qty = 1;
                    sweet_msg_toast('warning', 'La cantidad debe ser mayor a cero');
                    return;
                }

                let datos = item;
                datos.ajusteId = this.isEdit ? dataAjuste.id : '';

                try {
                    this.loading = true;
                    let {data} = await axios.post(this.url + '/ajustessalida/updateProduct', datos);
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
                    let {data} = await axios.post(this.url + '/ajustessalida/showDetailCart');

                    if (data.totalArticles > 0) {
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

            async changeBodega() {
                if (this.emptyCar !== false) {
                    let bodegaId = this.formDataAjuste.ajesBodega.id;
                    if (bodegaId) {
                        try {
                            this.loadingBodega = true;
                            let {data} = await axios.get(this.url + '/ajustessalida/changeBodega/' + bodegaId);
                            if (data.status === 'success') {
                                sweet_msg_toast('success', data.msg);
                            }
                        } catch (e) {
                            sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                        } finally {
                            this.loadingBodega = false;
                        }
                    }
                } else {
                    this.formDataAjuste.ajesBodega = this.listaBodegas.find(b => b.id === bodegaIdAjs);
                    sweet_msg_dialog('warning', 'Existen productos cargados al carrito<br> No se puede cambiar de bodega');
                }
            },

            async deleteProduct(rowId) {
                try {
                    this.loading = true;
                    await axios.get(this.url + '/ajustessalida/deleteProduct/' + rowId);
                    this.showDetailCart();
                    sweet_msg_toast('info', 'Producto eliminado exitosamente');
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                } finally {
                    this.loading = false;
                }
            },

            async cancelarAjuste() {
                Swal.fire({
                    title: "¿Esta seguro que desea cancelar el Ajuste?",
                    html: "<h6>Esta acción borrará toda la lista cargada.</h6>",
                    icon: 'warning',
                    width: "30%",
                    showCancelButton: true,
                    confirmButtonText: "Si, Continuar",
                    confirmButtonColor: "#bb2d3b"
                }).then(async(result) => {
                    if (result.isConfirmed) {
                        try {
                            this.loading = true;
                            await axios.post(this.url + '/ajustessalida/cancelarAjuste');
                            this.showDetailCart();
                            this.clear();
                            window.history.pushState({}, '', this.url + '/ajustessalida/nuevoAjuste');
                        } catch (e) {
                            sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                        } finally {
                            this.loading = false;
                        }
                    }
                });
            },

            generarExcel() {
                const contenido = document.getElementById('contentExport');
                const titulo = `Ajuste_Salida_${this.zFill(this.secuencialAjuste, 5)}`;
                return generarExcel(contenido, titulo);
            },

            generarPDF() {
                try {
                    window.open(`${this.url}/ajustessalida/generarPDF/${this.idAjuste}?download=1`, '_blank');
                } catch (e) {
                    sweet_msg_dialog('error', '', '', 'Error al generar el documento, ' + e.message);
                }
            },

            clear() {
                this.isEdit = false;
                this.formDataAjuste = {
                    ajesBodega: this.listaBodegas.find(val => val.id === bodegaIdAjs),
                    ajesCentrocosto: '',
                    ajesFecha: fechaActual,
                    ajesMotivo: '',
                    ajesEstado: '',
                    ajesObservaciones: '',
                    ajesTipo: 'AJUSTE_MERMA',
                    ajesPermitirDuplicados: permitirDuplicados
                };
            },

            formatToUSD(amount) {
                return formatToUSD(amount);
            },

            zFill(value, size) {
                return zFill(value, size);
            },

            formData(obj) {
                var formData = new FormData();
                for (var key in obj) {
                    let value = obj[key];

                    if (value === null || value === undefined) {
                        formData.append(key, '');
                        continue;
                    }
                    if (typeof value === 'object') {
                        value = value.id || value.sus_codigo || value.codigo || value.value || JSON.stringify(value);
                    }
                    formData.append(key, value);
                }
                return formData;
            }
        }
    });

    window.appAjs.use(AllDirectives);
    window.appAjs.mount('#app');
</script>
