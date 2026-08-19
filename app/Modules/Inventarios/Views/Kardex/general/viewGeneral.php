<!DOCTYPE html>
<!--
/**
 * Description of viewGeneral
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 9 mar 2026
 * @time 8:07:35 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<style>
    .multiselect__tags {
        border-radius: 0px 5px 5px 0px
    }
    /* Cards resumen */
    .kpi-card-success {
        background-color: #D0FAE5;
    }
    .kpi-card-danger {
        background-color: #FFE4E6;
    }
    .kpi-card-primary {
        background-color: #DBEAFE;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
    }
    .table-responsive {
        max-height: 600px;
        white-space: nowrap;
        overflow-x: auto;
        overflow-y: auto;
    }

</style>
<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system">
                <i class="fas fa-box"></i> Kardex General
            </h5>           
        </div>

        <div class="card-body">
            <fieldset>
                <legend>
                    <i class="fas fa-box me-2"></i> Filtros de Kardex
                </legend>

                <div class="row col-md-12">

                    <!-- Botones de Seleccion -->
                    <div class="col-md-6 form-group-custom">
                        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">

                            <input type="radio" class="btn-check" id="btnradio0" value="NDC" v-model="filtros.movimiento" autocomplete="off">
                            <label class="btn btn-outline-secondary" for="btnradio0">
                                <i class="fas fa-cart-plus me-2"></i> NDC
                            </label>
                            
                            <input type="radio" class="btn-check" id="btnradio1" value="COMPRAS" v-model="filtros.movimiento" autocomplete="off">
                            <label class="btn btn-outline-success" for="btnradio1">
                                <i class="fas fa-cart-plus me-2"></i> COMPRAS
                            </label>

                            <input type="radio" class="btn-check" id="btnradio2" value="VENTAS" v-model="filtros.movimiento" autocomplete="off">
                            <label class="btn btn-outline-warning" for="btnradio2">
                                <i class="fas fa-cash-register me-2"></i> VENTAS
                            </label>

                            <input type="radio" class="btn-check" id="btnradio3" value="TRANSFERENCIAS" v-model="filtros.movimiento" autocomplete="off">
                            <label class="btn btn-outline-info" for="btnradio3">
                                <i class="fas fa-exchange-alt me-2"></i> TRANSFERENCIAS
                            </label>

                            <input type="radio" class="btn-check" id="btnradio4" value="AJUSTES_DE_ENTRADA" v-model="filtros.movimiento" autocomplete="off">
                            <label class="btn btn-outline-primary" for="btnradio4">
                                <i class="fas fa-arrow-down me-2"></i> AJUSTES DE ENTRADA
                            </label>

                            <input type="radio" class="btn-check" id="btnradio5" value="AJUSTES_DE_SALIDA" v-model="filtros.movimiento" autocomplete="off">
                            <label class="btn btn-outline-danger" for="btnradio5">
                                <i class="fas fa-arrow-up me-2"></i> AJUSTES DE SALIDA
                            </label>

                        </div>                  
                    </div>


                    <!-- Buscador productos multiselect -->
                    <div class="col-md-4 col-sm-6 form-group-custom">

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="input-group-text bg-cris-system" style="border-radius: 5px 0px 0px 5px">
                                <i class="fas fa-box-open"></i>
                            </span>

                            <vue-multiselect
                                tag-placeholder="Producto no Encontrado"
                                placeholder="Buscar Productos (CODIGO, NOMBRE)"
                                label="producto"
                                track-by="id"
                                :multiple="false"
                                :searchable="true"
                                :options-limit="10"
                                :show-no-results="true"
                                :options="listaSearchProductos"
                                @select="onSelectProducto($event)"
                                @remove="onRemove($event)"                              
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
                                        </div>
                                    </div>
                                </template>
                            </vue-multiselect>

                        </div>
                    </div>

                    <!-- Grupo -->
                    <div class="col-md-2 col-sm-6 form-group-custom">
                        <div class="d-flex justify-content-between align-items-center border">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-tags me-2"></i>Grupo
                            </span>
                            <vue-select
                                class="flex-grow-1"
                                :options="listaGrupos"
                                label="gr_nombre"
                                v-model="filtros.kardGrupo"
                                :reduce="g => g.id"
                                placeholder="Seleccione un Grupo"> 
                            </vue-select>

                        </div>
                    </div>



                    <!--Fechas DE CONTROL KARDEX-->
                    <div class="col-md-3 col-sm-6 col-12 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system"><i class="fas fa-calendar me-2"></i> Fechas Kardex</span>
                            <input type="text"  ref="dateRangeKardex" v-model='filtros.rangoFechasKardex'  placeholder="Seleccione un rango de fechas" class="form-control" data-style="btn-white">  
                        </div>
                    </div>

                    <!--Fechas DE CONTROL EMISION DOCUMENTO-->
                    <div class="col-md-3 col-sm-6 col-12 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system"><i class="fas fa-calendar me-2"></i> Fechas de Emisión</span>
                            <input type="text"  ref="dateRangeEmision" v-model='filtros.rangoFechasEmision'  placeholder="Seleccione un rango de fechas" class="form-control" data-style="btn-white">  
                        </div>
                    </div>


                    <!-- Bodega -->
                    <div class="col-md-2 col-sm-6  form-group-custom">
                        <div class="d-flex justify-content-between align-items-center border">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-warehouse me-2"></i>Bodega
                            </span>
                            <vue-select
                                class="flex-grow-1"
                                :options="listaBodegas"
                                label="bod_nombre"
                                v-model="filtros.kardBodega"
                                :reduce="b => b.id"
                                placeholder="Seleccione una bodega"
                                @option:selected="showContent = false">
                            </vue-select>
                        </div>
                    </div>

                    <!--FILTRO PARA TRANSFERENCIAS (DEFINES SI QUIERES ENTRADAS O SALIDAS DE BODEGA)-->
                    <div v-if="filtros.movimiento === 'TRANSFERENCIAS'" class="col-md-2 col-sm-6  form-group-custom">
                        <div class="d-flex justify-content-between align-items-center border">
                            <span class="input-group-text bg-info">
                                <i class="fas fa-file-alt me-2"></i>Movimiento
                            </span>
                            <vue-select
                                class="flex-grow-1"
                                :options="[
                                {label: 'ENTRADA', value: 'ENTRADA'},
                                {label: 'SALIDA', value: 'SALIDA'}
                                ]"
                                v-model="filtros.tipoTransferencia"
                                label="label"
                                :reduce="o => o.value"
                                placeholder="Tipo Transferencia"
                                >
                            </vue-select>
                        </div>
                    </div>


                    <!-- Buscar -->
                    <div class="col-md-2 col-sm-6 ">
                        <button class="btn btn-system" @click="searchDataReport()" :disabled="loading">
                            <span><i class="fas fa-search"></i>  Consultar Movimientos</span>
                        </button>
                    </div>

                </div>
                <div class="row col-md-12">
                    <span class="badge badge-primary fs-6">{{nombreProducto}}</span>
                </div>
            </fieldset>
            <div  v-if="!showContent" class="empty-state">
                <span v-if="loading" ><i class="fas fa-circle-notch fa-spin"></i></span>
                <span v-else ><i class="fas fa-inbox"></i></span>
                <p>No hay kardex Generado</p>
                <p style="font-size: 0.9rem; color: #d1d5db;">
                    Utiliza los filtros de búsqueda de arriba para proceder
                </p>
                <p style="font-size: 0.9rem; color: #d1d5db;">
                    De click en el boton  Consultar Movimientos
                </p>
            </div>
            <div v-else>
                <hr>
                <!-- VIEW PAGINATION HEAD-->
                <?php echo view('\Modules\Inventarios\Views\Kardex\viewPaginationHead') ?>
                <!-- FIN VIEW PAGINATION HEAD-->      
                <div class="table-responsive mt-3">
                    <div v-if="loading" class="table-loading">
                        <div class="loader-box"> 
                            <span><i class="fas fa-spinner fa-spin fa-2x"></i></span>
                        </div>
                    </div>
                    <table class="table table-hover table-striped align-middle" id='tblKardexProducto'>
                        <thead class="bg-system text-white">
                            <tr>
                                <th>FECHA DE MOVIMIENTO</th>
                                <th>FECHA DE EMISIÓN</th>
                                <th>CÓDIGO</th>
                                <th>PRODUCTO</th>
                                <th>GRUPO</th>
                                <th>SUBGRUPO</th>
                                <th>BODEGA</th>
                                <th>LOTE</th>
                                <th>F. CADUC.</th>
                                <th>CANTIDAD</th>
                                <th>C. PROMEDIO</th>
                                <th>TOTAL CST. PROMEDIO</th>
                                <th>C. ULTIMO</th>
                                <th>TOTAL CST. ÚLTIMO</th>
                                <th>TRANSACCIÓN</th>
                                <th v-if="mostrarDocumento">NUM DOCUMENTO</th>                                
                                <th v-if="mostrarMotivoAjuste">MOTIVO DE AJUSTE</th>                                
                                <th v-if="mostrarProvClie">PROV/CLI</th>                                
                                <th class="text-center">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="listaKardexGeneral.length === 0">
                                <td colspan="18" class="text-center text-muted py-4">
                                    <i class="fas fa-box-open fa-2x mb-2"></i>
                                    <br>
                                    No se encontraron registros
                                </td>
                            </tr>

                            <tr v-for="item in listaKardexGeneral" :key="item.id">

                                <td>{{ item.fecha_movimiento }}</td>
                                <td>{{ item.fecha_emision }}</td>
                                <td>{{ item.codigo }}</td>
                                <td>{{ item.producto }}</td>
                                <td>{{ item.grupo }}</td>
                                <td>{{ item.subgrupo }}</td>
                                <td>{{ item.bodega }}</td>
                                <td>
                                    <span v-if="item.lote">{{ item.lote }} </span>
                                    <span v-else class="text-muted" style="font-style: italic; ; font-size: 12px">N/A </span>
                                </td>

                                <td>
                                    <span v-if="item.fecha_caducidad"> {{ item.fecha_caducidad }} </span>
                                    <span v-else class="text-muted" style="font-style: italic; font-size: 12px">  N/A</span>
                                </td>

                                <!-- Cantidad con signo -->
                                <td :style="getColStyle()"> {{ item.cantidad }}  </td>


                                <td>{{ formatToUSD(item.costo_promedio) }}</td>
                                <td>{{ formatToUSD(item.total_promedio) }}</td>
                                <td>{{ formatToUSD(item.costo_ultimo) }}</td>
                                <td>{{ formatToUSD(item.total_ultimo) }}</td>

                                <!-- Transaccion -->
                                <td :style="getColStyle()">
                                    {{ item.transaccion || '-' }}
                                </td>

                                <!-- Documento -->
                                <td v-if="mostrarDocumento">
                                    {{ item.documento || '-' }}
                                </td>

                                <!-- Motivo -->
                                <td v-if="mostrarMotivoAjuste">
                                    {{ item.motivo || '-' }}
                                </td>

                                <!-- Prov / Cliente -->
                                <td v-if="mostrarProvClie">
                                    {{ item.proveedor_cliente || '-' }}
                                </td>

                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary"
                                            @click="verDocumento(item.kar_documento_id, item.transaccion_cod)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                    <!-- VIEW PAGINATION FOOT-->
                    <?php echo view('\Modules\Inventarios\Views\Kardex\viewPaginationFoot') ?>
                    <!-- FIN VIEW PAGINATION FOOT-->     
                </div>
            </div>
        </div>
        <!--MODAL DETALLE KARDEX-->
        <?php echo view('\Modules\Inventarios\Views\Kardex\viewModalReport') ?>
        <!--CLOSE MODAL DETALLE KARDEX-->
    </div>
</div>

<script type="text/javascript">

    var fechaDesde = DateTime.now().toFormat('yyyy-MM-01');
    var fechaHasta = DateTime.now().toFormat('yyyy-MM-dd');
    var listaBodegas = <?php echo json_encode($listaBodegas); ?>;
    var listaGrupos = <?php echo json_encode($listaGrupos); ?>;
    if (window.appKardexGen) {
        window.appKardexGen.unmount();
    }
    window.appKardexGen = Vue.createApp({
        components: {
            "vue-multiselect": window['vue-multiselect'].Multiselect,
            "vue-select": window['vue-select']
        },
        mixins: [window.paginationMixin], // Traigo codigo VUE de paginacion desde un archivo del resources/js/paginationMixin.js
        data() {
            return {
                url: siteUrl,
                //BOOLEANOS
                showContent: false,
                loading: false,
                downloadingexcel: false,
                downloadingpdf: false,
                //V-MODELS
                filtros: {
                    movimiento: '',
                    tipoTransferencia: '',
                    kardBodega: '',
                    kardGrupo: '',
                    rangoFechasKardex: `${fechaDesde} a ${fechaHasta}`,
                    rangoFechasEmision: "",
                    kardProductoId: null
                },
                //LISTAS
                listaGrupos: listaGrupos,
                listaBodegas: listaBodegas,
                listaSearchProductos: [],
                listaKardexGeneral: [],
                //PAGINACION
                pagination: {
                    currentPage: 1,
                    pageSize: 10,
                    totalRecords: 0,
                    filteredRecords: 0,
                    searchTerm: '',
                    sortColumn: '',
                    sortDirection: ''
                },
                searchTimeout: null,
                // Variables para Flatpickr
                flatpickrInstance: null,
                nombreProducto: '',
                //DATOS DEL MODAL DE DETALE
                detalleHtml: '',
                titleHead: '',
                cargandoDetalle: false,
                modalInstance: null

            };
        },
        mounted() {
            // Inicializar Flatpickr
            this.flatpickrInstance = flatpickr(this.$refs.dateRangeKardex, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                locale: 'es',
                allowInput: true,
                onChange: (_, dateStr) => {
                    this.filtros.rangoFechasKardex = dateStr;
                }
            });
            this.flatpickrInstance = flatpickr(this.$refs.dateRangeEmision, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                locale: 'es',
                allowInput: true,
                onChange: (_, dateStr) => {
                    this.filtros.rangoFechasEmision = dateStr;
                }
            });
            this.modalInstance = new bootstrap.Modal(this.$refs.modalReport);
        },
        computed: {
            nombreBodega() {
                return (bodega) => {
                    return this.filtros.kardBodega ? bodega : "TODAS";
                };
            },
            mostrarMotivoAjuste() {
                return this.filtros.movimiento === 'AJUSTES_DE_ENTRADA'
                        || this.filtros.movimiento === 'AJUSTES_DE_SALIDA';
            },
            mostrarProvClie() {
                return this.filtros.movimiento === 'COMPRAS'
                        || this.filtros.movimiento === 'VENTAS'
                        || this.filtros.movimiento === 'NDC';
            },
            mostrarDocumento() {
                return this.filtros.movimiento === 'COMPRAS'
                        || this.filtros.movimiento === 'VENTAS'
                        || this.filtros.movimiento === 'NDC';
            }

        },
        watch: {
            'filtros.movimiento'(val) {
                this.showContent = false;
                this.listaKardexGeneral = [];
                this.pagination.currentPage = 1;
            }

        },
        methods: {
            getColStyle() {

                let baseColor = '';
                let borderColor = '';

                switch (this.filtros.movimiento) {
                    case 'NDC':
                        baseColor = '#D1D5DC';   // suave
                        borderColor = '#99A1AF'; // fuerte
                        break;
                        
                    case 'COMPRAS':
                        baseColor = '#ecfdf5';   // suave
                        borderColor = '#16a34a'; // fuerte
                        break;

                    case 'VENTAS':
                        baseColor = '#fffbeb';
                        borderColor = '#f59e0b';
                        break;

                    case 'TRANSFERENCIAS':
                        baseColor = '#eff6ff';
                        borderColor = '#2563eb';
                        break;

                    case 'AJUSTES_DE_ENTRADA':
                        baseColor = '#f5f3ff';
                        borderColor = '#7c3aed';
                        break;

                    case 'AJUSTES_DE_SALIDA':
                        baseColor = '#fef2f2';
                        borderColor = '#dc2626';
                        break;
                }

                return {
                    backgroundColor: baseColor,
                    borderLeft: `4px solid ${borderColor}`
                };
            },
            titleDocumentoExport() {
                let titulo = 'REPORTE KARDEX GENERAL';
                switch (this.filtros.movimiento) {
                    case 'NDC':
                        titulo = 'REPORTE DE NDC';   // suave
                        break;
                    case 'COMPRAS':
                        titulo = 'REPORTE DE COMPRAS';   // suave
                        break;
                    case 'VENTAS':
                        titulo = 'REPORTE DE VENTAS';
                        break;
                    case 'TRANSFERENCIAS':
                        titulo = 'REPORTE DE TRANSFERENCIAS';
                        break;
                    case 'AJUSTES_DE_ENTRADA':
                        titulo = 'REPORTE DE AJUSTED DE ENTRADA';
                        break;
                    case 'AJUSTES_DE_SALIDA':
                        titulo = 'REPORTES DE AJUSTES DE SALIDA';
                        break;
                }
                return titulo;
            },
            async verDocumento(documentoId, transaccionCod) {

                let ruta = '';
                switch (transaccionCod) {

                    case '11': // COMPRAS (Salida) Factura compra
                        ruta = "compras/getDataDetalle";
                        this.titleHead = " DE LA NDC";
                        break;
                    case '31': // COMPRAS (Ingreso) Factura compra
                        ruta = "compras/getDataDetalle";
                        this.titleHead = " DE LA NDC";
                        break;
                        
                    case '02': // COMPRAS (Ingreso) Factura compra
                        ruta = "compras/getDataDetalle";
                        this.titleHead = " DE LA COMPRA";
                        break;
                    case '09': // COMPRAS (Salida) Anulación de Factura compra
                        ruta = "compras/getDataDetalle";
                        this.titleHead = " DE LA ANULACIÓN DE COMPRA";
                        break;
                    case '01': // VENTAS (Salida) Factura venta
                        ruta = "ventas/getDataDetalle";
                        this.titleHead = " DE LA VENTA";
                        break;
                    case '08': // VENTAS (Entrada)Anulación de Factura venta
                        ruta = "ventas/getDataDetalle";
                        this.titleHead = " DE LA ANULACIÓN DE LA VENTA";
                        break;
                    case '39': // AJUSTES (Entrada) Registro de ingreso de Entrada
                        ruta = "ajustesentrada/getDataDetalle";
                        this.titleHead = " DEL AJUSTE DE ENTRADA";
                        break;
                    case '41': // AJUSTES (Slida) Anulación Ajuste de Entrada
                        ruta = "ajustesentrada/getDataDetalle";
                        this.titleHead = " DE LA ANULACIÓN DEL AJUSTE DE ENTRADA";
                        break;
                    case '38': // AJUSTES (Salida) Registro de ajuste de salida
                        ruta = "ajustessalida/getDataDetalle";
                        this.titleHead = " DEL AJUSTE DE SALIDA";
                        break;
                    case '40': // AJUSTES (Entrada) Anulación de ajuste de salida
                        ruta = "ajustessalida/getDataDetalle";
                        this.titleHead = " DE LA ANULACIÓN DEL AJUSTE DE SALIDA";
                        break;
                    case '17': // TRANSFERENCIAS (Entrada y salida)
                        ruta = "transferencias/getDataDetalle";
                        this.titleHead = " DE LA TRANSFERENCIA";
                        break;
                    case '44': // TRANSFERENCIAS (Anulación )
                        ruta = "transferencias/getDataDetalle";
                        this.titleHead = " DE LA TRANSFERENCIA ANULADA";
                        break;
                    default:
                        sweet_msg_toast('warning', 'No existe ruta definida para este documento');
                        return;
                }

                try {
                    this.cargandoDetalle = true;
                    const {data} = await axios.get(`${this.url}/${ruta}/${documentoId}`);
                    if (data) {
                        this.modalInstance.show();
                        this.detalleHtml = data;
                        this.cargandoDetalle = false;
                    }
                } catch (error) {

                    sweet_msg_dialog('error', '', '', 'Error al cargar el detalle, ' + error.message);
                } finally {
                    this.cargandoDetalle = false;
                }

            },
            //DATA GENERAL
            async searchDataReport(paginate = false) {

                const datos = {
                    ...this.filtros,
                    draw: 1,
                    start: (this.pagination.currentPage - 1) * this.pagination.pageSize,
                    length: this.pagination.pageSize,
                    search: this.pagination.searchTerm,
                    order: [{
                            column: this.pagination.sortColumn,
                            dir: this.pagination.sortDirection
                        }]
                };
                try {
                    if (!paginate) {
                        this.showContent = false;
                    }

                    this.loading = true;
                    const {data} = await axios.post(this.url + "/kardex/getKardexGeneral", datos);
                    if (data.status === 'success') {
                        this.showContent = true;
                        this.listaKardexGeneral = data.data;
                        this.pagination.totalRecords = data.recordsTotal;
                        this.pagination.filteredRecords = data.recordsFiltered;
                    } else if (data.status === 'warning') {
                        sweet_msg_dialog('warning', data.msg);
                    } else {
                        sweet_msg_dialog('warning', 'No se han encontrado registros para mostrar');
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                } finally {
                    this.loading = false;
            }

            },
            // SEARCH PRODUCTOS
            searchProductos(dataSerach) {
                clearTimeout(this.searchTimeout);
                let datos = {
                    dataSerach: dataSerach,
                    estado: 1
                };
                this.searchTimeout = setTimeout(async () => {
                    try {
                        let {data} = await axios.post(this.url + '/comun/productos/searchProductosFull', datos);
                        if (data !== false) {
                            this.listaSearchProductos = data;
                        } else {
                            this.listaSearchProductos = [];
                        }
                    } catch (e) {
                        sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                        this.listaSearchProductos = [];
                    }
                }, 400);
            },
            onSelectProducto(option) {
                this.filtros.kardProductoId = option ? option.id : null;
                this.nombreProducto = this.listaSearchProductos.find(val => val.id === option.id)?.producto || null;
            },
            onRemove() {
                this.listaSearchProductos = [];
                this.filtros.productoSearch = null;
            },
            async exportExcel() {
                const datos = {
                    ...this.filtros,
                    search: this.pagination.searchTerm
                };
                try {
                    
                    this.downloadingexcel = true;
                    const {data} = await axios.post(this.url + '/kardex/exportExcelKardexGeneral', datos, {responseType: 'blob'});
                    const url = window.URL.createObjectURL(new Blob([data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', `${this.titleDocumentoExport()}.xlsx`);
                    document.body.appendChild(link);
                    link.click();
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.message);
                } finally {
                    this.downloadingexcel = false;
                }

            },
            async exportPdf() {
                const datos = {
                    ...this.filtros,
                    search: this.pagination.searchTerm
                };
                try {
                    this.downloadingpdf = true;
                    const {data} = await axios.post(this.url + '/kardex/exportPdfKardexGeneral', datos, {responseType: 'blob'});
                    const blob = new Blob([data], {type: 'application/pdf'});
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = `${this.titleDocumentoExport()}.pdf`;
                    link.click();
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data?.message || e.message);
                } finally {
                    this.downloadingpdf = false;
                }


            },

            formatToUSD(amount) {
                return formatToUSD(amount);
            }

        }


    });
    window.appKardexGen.use(AllDirectives);
    window.appKardexGen.mount('#app');

</script>