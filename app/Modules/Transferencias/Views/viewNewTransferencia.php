<!DOCTYPE html>
<!--
/**
 * Description of viewNewTransferencia
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 4 dic 2025
 * @time 11:33:50 p.m.
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

<div id="appTransfer" class="container-fluid">
    <div class="card card-system card-outline">

        <div class="card-header">
            <h5 class="card-title text-system">
                <i class="fas fa-random"></i>
                {{isEdit ? 'Actualizar Transferencia' : 'Nueva Transferencia de Bodega'}}
            </h5>
        </div>

        <div class="card-body">

            <!-- ============================= -->
            <!-- DATOS GENERALES -->
            <!-- ============================= -->

            <fieldset>
                <legend>Datos Generales</legend>

                <div class="row">

                    <!-- Fecha -->
                    <div class="col-md-2 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system" ><i class="fas fa-calendar me-2"></i> Fecha </span>
                            <input type="date"class="form-control" v-model="formDataTransfer.trbFecha">
                        </div>
                    </div>

                    <!-- Bodega Origen -->
                    <div class="col-md-3 form-group-custom">
                        <div class="d-flex align-items-center border">
                            <span class="input-group-text bg-cris-system">
                                <span v-if='loadingBodega'><i class="loading-spin"></i></span>
                                <span v-else><i class="fas fa-warehouse me-2"></i></span>
                                Bod. Origen
                            </span>
                            <vue-select
                                class="flex-grow-1"
                                :options="listaBodegas"
                                label="bod_nombre"
                                v-model="formDataTransfer.trbBodegaOrigen"
                                @option:selected="changeBodega"
                                placeholder="Seleccione la bodega origen"/>
                        </div>
                    </div>

                    <!-- Bodega Destino -->
                    <div class="col-md-3 form-group-custom">
                        <div class="d-flex align-items-center border">
                            <span class="input-group-text bg-cris-system"> <i class="fas fa-warehouse me-2"></i> Bod. Destino</span>
                            <vue-select
                                class="flex-grow-1"
                                :options="listaBodegas"
                                label="bod_nombre"
                                v-model="formDataTransfer.trbBodegaDestino"
                                @option:selected="loadUsersConfirm"
                                placeholder="Seleccione la bodega destino"/>
                        </div>
                    </div>

                    <!-- Usuario confirma -->
                    <div class="col-md-4 form-group-custom">                      
                        <div class="d-flex align-items-center border">
                            <span class="input-group-text bg-cris-system">
                                <span v-if='loadingUsers'><i class="loading-spin"></i></span>
                                <span v-else><i class="fas fa-user-check me-2"></i></span>
                                Usuario a confirmar 
                            </span>
                            <vue-select
                                class="flex-grow-1"
                                :options="listaUsuariosDestino"
                                label="empleado"
                                v-model="formDataTransfer.trbUsuarioDestino"
                                placeholder="Seleccione el usuario que confirma"/>
                        </div>
                    </div>

                </div>

                <div class="row mt-2">

                    <!-- Centro de costos -->
                    <div class="col-md-4 form-group-custom">
                        <div class="d-flex align-items-center border">
                            <span class="input-group-text bg-cris-system"><i class="fas fa-project-diagram me-2"></i> Centro de costos </span>
                            <vue-select
                                class="flex-grow-1"
                                :options="listaCentroCostos"
                                label="cc_nombre"
                                v-model="formDataTransfer.trbCentroCosto"
                                placeholder="Seleccione un centro de costos"/>
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="col-md-3 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system"><i class="fas fa-toggle-on me-2"></i> Estado </span>
                            <select class="form-select" v-model="formDataTransfer.trbEstado">
                                <option disabled value="">Seleccione un estado</option>
                                <option value="1">BORRADOR</option>
                                <option value="2">POR CONFIRMAR</option>
                            </select>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="col-md-5 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system"> <i class="fas fa-comments me-2"></i> </span>
                            <input type="text"class="form-control" v-model="formDataTransfer.trbObservaciones" placeholder="Observaciones...">
                        </div>
                    </div>
                </div>
            </fieldset>

            <br>

            <!-- ============================= -->
            <!-- BUSQUEDA DE PRODUCTOS -->
            <!-- ============================= -->

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
                                    <i class="fas fa-upload me-2"></i> Cargar datos
                                </span>
                            </button>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <a :href="url + '/comun/descargar/downloadPlantillaExcelTransferencias'" class="btn btn-outline-primary w-100">
                                <i class="fas fa-download me-2"></i> Descargar Plantilla
                            </a>
                        </div>
                    </div>
                </fieldset>
            </div>

            <!-- ============================= -->
            <!-- CART -->
            <!-- ============================= -->
            <?php echo view('Modules\Transferencias\Views\viewCart') ?>

            <!-- BOTONES -->
            <div v-if="!emptyCar" class="d-flex justify-content-end gap-3 mt-4">

                <button @click="cancelarTransferencia()"
                         class="btngr btn-danger-gradiant"
                         style="min-width: 150px;"
                         :disabled="loadingProcess">
                    <i class="fas fa-times-circle me-2"></i>Cancelar
                </button>
                <button class="btngr btn-primary-gradiant"
                        style="min-width: 150px;"
                        @click="saveTransferencia()"
                        :disabled="loadingProcess">
                    <span v-if="loadingProcess">
                        <i class="loading-spin"></i>{{isEdit?'Actualizando...':'Grabando...'}}
                    </span>
                    <span v-else>
                        <i class="fas fa-save me-2"></i>{{isEdit?'Actualizar Transferencia':'Grabar Transferencia'}}
                    </span>
                </button>
            </div>

        </div>
    </div>
    <!-- MODAL DETALLE -->
    <?php echo view('\Modules\Transferencias\Views\reportes\viewModalReport') ?>
    <!-- FIN MODAL DETALLE -->
</div>


<script type="text/javascript">
    // ============================
    // VARIABLES GLOBALES DESDE PHP
    // ============================
    var fechaActual = DateTime.now().toFormat('yyyy-MM-dd');
    var listaBodegas = <?= json_encode($listaBodegas); ?>;
    var listaCentroCostos = <?= json_encode($listaCentroCostos); ?>;
    var permitirDuplicados = <?= $permitirDuplicados ?>;
    var bodegaOrigenId = '<?= $bodegaId; ?>';
    var dataTransferencia = <?= json_encode($dataTransferencia ?? null); ?>;

    var searchTimeout = null;

    if (window.appTrb) {
        window.appTrb.unmount();
    }

    window.appTrb = Vue.createApp({
        components: {
            "vue-multiselect": window['vue-multiselect'].Multiselect,
            "vue-select": window['vue-select']
        },
        data() {
            return {
                url: siteUrl,

                // FLAGS
                isEdit: false,
                loading: false,
                loadingProcess: false,
                loadingBodega: false,
                loadingUsers: false,

                //IMPORTACION EXCEL 
                mostrarImportacion: false,
                selectedExcelFile: null,
                excelFilename: '',

                // LISTAS
                listaBodegas: listaBodegas,
                listaCentroCostos: listaCentroCostos,
                listaUsuariosDestino: [],

                // FORM PRINCIPAL
                formDataTransfer: {
                    trbFecha: fechaActual,
                    trbBodegaOrigen: '',
                    trbBodegaDestino: '',
                    trbCentroCosto: '',
                    trbEstado: '', // 1=BORRADOR, 2=POR CONFIRMAR
                    trbObservaciones: '',
                    trbUsuarioDestino: '',
                    trbPermitirDuplicados: permitirDuplicados
                },

                // CART
                listaCartData: [],
                totalCart: 0,
                totalIva: 0,
                totalCartIva: 0,
                totalItems: 0,
                totalArticles: 0,
                emptyCar: true,

                // BUSQUEDA PRODUCTOS
                listaSearchProductos: [],
                productoVmodel: null,
                codeSearch: "",

                // MODAL REPORTE
                dataTransf: '',
                idTransferencia: '',
                secuencialTransferencia: '',
                cargandoDetalle: false,
                modalInstance: null,
            };
        },
        created() {
            this.showDetailCart();
        },
        mounted() {
            // Bodega origen por defecto
            if (bodegaOrigenId) {
                this.formDataTransfer.trbBodegaOrigen = this.listaBodegas.find(b => b.id === bodegaOrigenId) || '';
            }

            // Modo edición
            if (dataTransferencia) {
                this.isEdit = true;



                this.formDataTransfer.trbFecha = dataTransferencia.trb_fecha || fechaActual;
                this.formDataTransfer.trbBodegaOrigen = this.listaBodegas.find(b => b.id === dataTransferencia.fk_bodega_origen) || '';
                this.formDataTransfer.trbBodegaDestino = this.listaBodegas.find(b => b.id === dataTransferencia.fk_bodega_destino) || '';
                this.formDataTransfer.trbCentroCosto = this.listaCentroCostos.find(c => c.id === dataTransferencia.fk_centro_costo) || '';
                this.formDataTransfer.trbEstado = dataTransferencia.trb_estado;
                this.formDataTransfer.trbObservaciones = dataTransferencia.trb_observaciones;

                this.loadUsersConfirm();

                this.formDataTransfer.trbPermitirDuplicados = dataTransferencia.trb_items_duplicados;
            }
            this.modalInstance = new bootstrap.Modal(this.$refs.modalReport);
        },
        methods: {

            loadFilePicked(event) {
                const file = event.target.files?.[0] || null;
                this.selectedExcelFile = file;
                this.excelFilename = file ? file.name : '';
            },

            // ==========================
            // VALIDACIÓN CAMPOS
            // ==========================
            validarCampos() {
                const campos = [
                    {key: 'trbFecha', msg: 'Debe seleccionar una fecha'},
                    {key: 'trbBodegaOrigen', msg: 'Debe seleccionar una bodega de origen'},
                    {key: 'trbBodegaDestino', msg: 'Debe seleccionar una bodega de destino'},
                    {key: 'trbUsuarioDestino', msg: 'Debe seleccionar una usuario a confirmar'},
                    {key: 'trbCentroCosto', msg: 'Debe seleccionar una centro de costos'},
                    {key: 'trbEstado', msg: 'Debe seleccionar un estado'}
                ];

                for (const campo of campos) {
                    if (!this.formDataTransfer[campo.key] || this.formDataTransfer[campo.key] === '') {
                        return {status: true, msg: campo.msg};
                    }
                }

                if (this.formDataTransfer.trbBodegaOrigen.id === this.formDataTransfer.trbBodegaDestino.id) {
                    return {status: true, msg: 'La bodega de origen y destino no pueden ser la misma'};
                }

                return {status: false};
            },

            // ==========================
            // GUARDAR TRANSFERENCIA
            // ==========================
            async saveTransferencia() {
                let validation = this.validarCampos();
                if (validation.status) {
                    sweet_msg_toast('warning', validation.msg);
                    return;
                }

                let ruta = this.isEdit
                        ? '/transferencias/updateTransferencia'
                        : '/transferencias/saveTransferencia';

                try {
                    this.loadingProcess = true;
                    let datos = this.formData(this.formDataTransfer);
                    datos.append('transferenciaId', this.isEdit && dataTransferencia ? dataTransferencia.id : '');

                    const {data} = await axios.post(this.url + ruta, datos);

                    if (data.status === 'success') {
                        const redirectUrl = this.url + '/transferencias/nuevaTransferencia';
                        sweetMsgDialogConfirm(data.msg, this.verDetalle, data.data, redirectUrl);
                    } else if (data.status === 'warning') {
                        sweet_msg_dialog('warning', data.msg);
                    } else {
                        sweet_msg_dialog('error', data.msg);
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loadingProcess = false;
                }
            },

            async verDetalle(transferencia) {
                this.dataTransf = transferencia;
                this.cargandoDetalle = true;
                this.modalInstance.show();
                try {
                    const {data} = await axios.get(this.url + '/transferencias/getDataDetalle/' + transferencia.id);
                    this.cargandoDetalle = false;
                    await Vue.nextTick();
                    const modal = document.getElementById('detalleTransferenciaModal');
                    modal.innerHTML = data;
                } catch (error) {
                    sweet_msg_dialog('error', '', '', 'Error al cargar el detalle de la transferencia, ' + error.message);
                } finally {
                    this.cargandoDetalle = false;
                }
            },

            // ==========================
            // CAMBIAR BODEGA ORIGEN
            // ==========================
            async changeBodega() {
                if (this.emptyCar !== false) {
                    let bodegaId = this.formDataTransfer.trbBodegaOrigen.id;
                    if (bodegaId) {
                        try {
                            this.loadingBodega = true;
                            let {data} = await axios.get(this.url + '/transferencias/changeBodega/' + bodegaId);
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
                    this.formDataTransfer.trbBodegaOrigen = this.listaBodegas.find(b => b.id === bodegaOrigenId);
                    sweet_msg_dialog('warning', 'Existen productos cargados al carrito<br> No se puede cambiar de bodega');
                }
            },

            async loadUsersConfirm() {
                this.formDataTransfer.trbUsuarioDestino = [];
                let bodegaId = this.formDataTransfer.trbBodegaDestino.id;
                if (bodegaId) {
                    try {
                        this.loadingUsers = true;
                        let {data} = await axios.get(this.url + '/transferencias/loadUsersConfirm/' + bodegaId);
                        if (data.status === 'success') {
                            this.listaUsuariosDestino = data.data;
                        }else{
                            this.listaUsuariosDestino =[];
                        }
                    } catch (e) {
                        sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                    } finally {
                        this.loadingUsers = false;
                    }

                }
            },

            // ==========================
            // BUSQUEDA PRODUCTOS
            // ==========================
            searchProductos(texto) {
                clearTimeout(searchTimeout);

                if (!this.formDataTransfer.trbBodegaOrigen || !this.formDataTransfer.trbBodegaOrigen.id) {
                    this.listaSearchProductos = [];
                    sweet_msg_toast('info', 'No hay bodega seleccionada');
                }

                const datos = {
                    dataSerach: texto,
                    bodegaId: this.formDataTransfer.trbBodegaOrigen.id,
                    estado: 1
                };

                searchTimeout = setTimeout(async () => {
                    try {
                        const {data} = await axios.post(this.url + '/comun/productos/searchProductosStock', datos);
                        this.listaSearchProductos = data !== false ? data : [];
                    } catch (e) {
                        sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                        this.listaSearchProductos = [];
                    }
                }, 500);
            },

            // Código / barras
            async insertProductCode(evt) {
                if (!evt.target.value) {
                    sweet_msg_toast('warning', 'Por favor digite un código');
                    return;
                }
                const datos = {id: evt.target.value};
                await this.insertProductCart(datos);
            },

            // Agregar producto al carrito
            async insertProductCart(item) {
                this.onRemove();

                if (!this.formDataTransfer.trbBodegaOrigen || !this.formDataTransfer.trbBodegaOrigen.id) {
                    sweet_msg_toast('warning', 'Debe seleccionar una bodega de origen');
                    return;
                }

                const datos = {
                    id: item.id,
                    qty: 1,
                    bodega: this.formDataTransfer.trbBodegaOrigen.id,
                    permitirDuplicados: this.formDataTransfer.trbPermitirDuplicados
                };

                try {
                    this.loading = true;
                    const {data} = await axios.post(this.url + '/transferencias/insertProduct', datos);
                    if (data.status === 'success') {
                        sweet_msg_toast('success', data.msg);
                    } else if (data.status === 'warning') {
                        sweet_msg_toast('warning', data.msg);
                    } else {
                        sweet_msg_dialog('error', data.msg);
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loading = false;
                }

                this.showDetailCart();
            },

            onRemove() {
                this.listaSearchProductos = [];
                this.productoVmodel = null;
                this.codeSearch = '';
            },

            // ==========================
            // CART: UPDATE / DELETE / SHOW
            // ==========================
            async updateProductCart(item) {
                this.onRemove();

                if (item.qty <= 0) {
                    item.qty = 1;
                    sweet_msg_toast('warning', 'La cantidad debe ser mayor a cero');
                    return;
                }

                let datos = item;
                datos.transferId = this.isEdit && dataTransferencia ? dataTransferencia.id : '';

                try {
                    this.loading = true;
                    const {data} = await axios.post(this.url + '/transferencias/updateProduct', datos);
                    if (data.status === 'success') {
                        sweet_msg_toast('success', data.msg);
                    } else if (data.status === 'warning') {
                        sweet_msg_toast('warning', data.msg);
                    } else {
                        sweet_msg_dialog('error', data.msg);
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loading = false;
                }

                this.showDetailCart();
            },

            async deleteProduct(rowId) {
                try {
                    this.loading = true;
                    await axios.get(this.url + '/transferencias/deleteProduct/' + rowId);
                    sweet_msg_toast('info', 'Producto eliminado exitosamente');
                    this.showDetailCart();
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loading = false;
                }
            },

            async showDetailCart() {
                try {
                    const {data} = await axios.post(this.url + '/transferencias/showDetailCart');

                    if (data.totalArticles > 0) {
                        this.listaCartData = data.cartContent;
                        this.totalArticles = data.totalArticles;
                        this.totalItems = data.totalItems;
                        this.totalCart = data.totalCart;
                        this.totalCartIva = data.totalCartIva;
                        this.totalIva = data.totalIva;
                        this.emptyCar = false;
                    } else {
                        this.emptyCar = true;
                        this.listaCartData = [];
                        this.totalCart = 0;
                        this.totalIva = 0;
                        this.totalCartIva = 0;
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                }
            },

            // ==========================
            // CANCELAR TRANSFERENCIA
            // ==========================
            async cancelarTransferencia() {
                Swal.fire({
                    title: "¿Está seguro que desea cancelar la transferencia?",
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
                            await axios.post(this.url + '/transferencias/cancelarTransferencia');
                            this.showDetailCart();
                            this.clearForm();
                            window.history.pushState({}, '', this.url + '/transferencias/nuevaTransferencia');
                        } catch (e) {
                            sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                        } finally {
                            this.loading = false;
                        }
                    }
                });
            },

            // ==========================
            // UTILITARIOS
            // ==========================
            clearForm() {
                this.isEdit = false;
                this.formDataTransfer = {
                    trbFecha: fechaActual,
                    trbBodegaOrigen: this.listaBodegas.find(b => b.id === parseInt(bodegaOrigenId)) || '',
                    trbBodegaDestino: '',
                    trbCentroCosto: '',
                    trbEstado: '',
                    trbObservaciones: '',
                    trbUsuarioDestino: '',
                    trbPermitirDuplicados: permitirDuplicados
                };
            },

            formatToUSD(valor) {
                return formatToUSD(valor);
            },

            formData(obj) {
                var formData = new FormData();
                for (var key in obj) {
                    let value = obj[key];

                    // Si es null o undefined, agregar como está
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

    window.appTrb.use(AllDirectives);
    window.appTrb.mount('#appTransfer');
</script>
