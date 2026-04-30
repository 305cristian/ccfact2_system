<!DOCTYPE html>
<!--
/**
 * Description of viewProductos
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 9 mar 2026
 * @time 8:08:01 a.m.
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
                <i class="fas fa-box"></i> Kardex Por Producto
            </h5>           
        </div>

        <div class="card-body">
            <fieldset>
                <legend>
                    <i class="fas fa-box me-2"></i> Filtros de Kardex
                </legend>

                <div class="row col-md-12">

                    <!--Fechas DE CONTROL-->
                    <div class="col-md-3 col-sm-6 col-12 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system"><i class="fas fa-calendar me-2"></i> Fechas de Control</span>
                            <input type="text"  ref="dateRange" v-model='filtros.rangoFechas'  placeholder="Seleccione un rango de fechas" class="form-control" data-style="btn-white">  
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


                    <!-- Bodega -->
                    <div class="col-md-3 col-sm-6  form-group-custom">
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
                                @option:selected="showContent = false"/>
                        </div>
                    </div>

                    <!-- Buscar -->
                    <div class="col-md-2 col-sm-6 ">
                        <button class="btn btn-system" @click="searchDataReport()" :disabled="loading">
                            <span><i class="fas fa-search"></i> Consultar Movimientos</span>
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
                <br>
                <hr>
                <div class="row mb-3 text-center">

                    <div class="col-md-4">
                        <div class="card kpi-card-success shadow-sm p-2">
                            <small class="text-muted">Total Entradas</small>
                            <h5 class="text-success fw-bold">{{ totalEntradas }}</h5>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card kpi-card-danger shadow-sm p-2">
                            <small class="text-muted">Total Salidas</small>
                            <h5 class="text-danger fw-bold">{{ totalSalidas }}</h5>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card kpi-card-primary shadow-sm p-2">
                            <small class="text-muted">Saldo Final</small>
                            <h5 class="text-primary fw-bold">{{ saldoFinal }}</h5>
                        </div>
                    </div>

                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-hover table-striped align-middle" id='tblKardexProducto'>
                        <thead class="bg-system text-white">
                            <tr>
                                <th>FECHA DE MOVIMIENTO</th>
                                <th>TRANSACCIÓN/#DOC</th>
                                <th>BODEGA</th>
                                <th>USUARIO</th>                            
                                <th>C. PROMEDIO</th>
                                <th>C. ULTIMO</th>
                                <th>LOTE</th>
                                <th>ENTRADA</th>
                                <th>SALIDA</th>
                                <th>SALDO</th>
                                <th>PROV/CLI</th>                                
                                <th class="text-center">ACCIONES</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="item in listaKardex" :key="item.id">

                                <!-- FECHA -->
                                <td>
                                    <small class="text-muted">{{ item.kardex_fecha }}</small>
                                </td>

                                <!--TRANSACCION DOC--> 
                                <td>
                                    <strong>{{ item.tr_nombre }}</strong><br>
                                    <small class="text-muted">Documento # {{ item.num_documento}} </small>
                                </td>

                                <!-- BODEGA -->
                                <td>
                                    <span class="badge bg-secondary">{{ item.bod_nombre}}</span>
                                    <!--<span class="badge bg-secondary">{{ nombreBodega(item.bod_nombre) }}</span>-->
                                </td>


                                <!-- USUARIO -->
                                <td class="text-end">
                                    <small class="text-muted">{{ item.empleado }}</small>
                                </td>

                                <!--COSTO PROMEDIO--> 
                                <td class="text-end">
                                    <small class="text-muted">{{ formatToUSD(item.kardex_costo_promedio) }}</small>
                                </td>

                                <!--COSTO ULTIMO--> 
                                <td class="text-end">
                                    <small class="text-muted">{{ formatToUSD(item.kardex_costo_ultimo) }}</small>
                                </td>

                                <!-- LOTE -->
                                <td>
                                    <small class="text-muted">{{ item.lot_lote || '-' }}</small>
                                </td>

                                <!-- ENTRADA -->
                                <td class="text-end text-success fw-bold">
                                    {{ parseFloat(item.entrada).toFixed(2) }}
                                </td>

                                <!-- SALIDA -->
                                <td class="text-end text-danger fw-bold">
                                    {{ parseFloat(item.salida).toFixed(2) }}
                                </td>

                                <!-- SALDO -->
                                <td class="text-end text-primary fw-bold">
                                    <div class="fw-bold text-primary">
                                        {{ parseFloat(item.kardex_total).toFixed(2) }}
                                    </div>
                                </td>



                                <!--PROVEEDOR CLIENTE--> 
                                <td> 
                                    <small class="text-muted">  {{ item.prov_clie_nombre || '-' }} </small>
                                </td>

                                <!-- ACCIONES PRO -->
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light " data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" @click="verDocumento(item.kardex_documento_id,item.tr_codigo)"><i class="fas fa-file-alt"></i> Ver documento</a></li>
                                            <!--<li><a class="dropdown-item"><i class="fas fa-eye"></i> Ver detalle</a></li>-->
                                        </ul>
                                    </div>
                                </td>

                            </tr>

                            <tr v-if="listaKardex.length === 0">
                                <td colspan="12" class="text-center text-muted py-4">
                                    <i class="fas fa-box-open fa-2x mb-2"></i>
                                    <br>
                                    No se encontraron registros
                                </td>
                            </tr>
                        </tbody>
                    </table> 
                </div>

            </div>
        </div>
    </div>
    <!--MODAL DETALLE KARDEX-->
    <?php echo view('\Modules\Inventarios\Views\Kardex\viewModalReport') ?>
    <!--CLOSE MODAL DETALLE KARDEX-->
</div>

<script type="text/javascript">

    var fechaDesde = DateTime.now().toFormat('yyyy-MM-01');
    var fechaHasta = DateTime.now().toFormat('yyyy-MM-dd');
    var listaBodegas = <?php echo json_encode($listaBodegas); ?>;

    if (window.appKardexProd) {
        window.appKardexProd.unmount();
    }
    window.appKardexProd = Vue.createApp({
        components: {
            "vue-multiselect": window['vue-multiselect'].Multiselect,
            "vue-select": window['vue-select']
        },
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
                    kardBodega: '',
                    rangoFechas: `${fechaDesde} a ${fechaHasta} `,
                    kardProductoId: null
                },

                //LISTAS
                listaBodegas: listaBodegas,
                listaSearchProductos: [],
                listaKardex: [],

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
            this.flatpickrInstance = flatpickr(this.$refs.dateRange, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                locale: 'es',
                allowInput: true,
                onChange: (_, dateStr) => {
                    this.filtros.rangoFechas = dateStr;
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
            totalEntradas() {
                return this.listaKardex.reduce((acc, item) => acc + parseFloat(item.entrada || 0), 0).toFixed(2);
            },

            totalSalidas() {
                return this.listaKardex.reduce((acc, item) => acc + parseFloat(item.salida || 0), 0).toFixed(2);
            },

            saldoFinal() {
                if (this.listaKardex.length === 0)
                    return 0;
                return parseFloat(this.listaKardex[this.listaKardex.length - 1].kardex_total).toFixed(2);
            }
        },
        methods: {
            async verDocumento(documentoId, transaccionCod) {

                let ruta = '';

                switch (transaccionCod) {

                    case '02': // COMPRAS (Ingreso) Factura compra
                        ruta = "compras/ver";
                        this.titleHead = " DE LA COMPRA";
                        break;

                    case '09': // COMPRAS (Salida) Anulación de Factura compra
                        ruta = "compras/ver";
                        this.titleHead = " DE LA ANULACIÓN DE COMPRA";
                        break;


                    case '01': // VENTAS (Salida) Factura venta
                        ruta = "ventas/ver";
                        this.titleHead = " DE LA VENTA";
                        break;

                    case '08': // VENTAS (Entrada)Anulación de Factura venta
                        ruta = "ventas/ver";
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
            getPorcentajeStock(valor) {
                if (!this.saldoFinal || this.saldoFinal === 0)
                    return 0;
                let porcentaje = (valor / this.saldoFinal) * 100;
                return Math.max(0, porcentaje);
            },

            //DATA GENERAL
            async searchDataReport() {

                if (!this.filtros.kardProductoId) {
                    sweet_msg_toast('warning', 'Ingrese un producto para generar el kardex');
                    return;
                }

                const datos = {
                    ...this.filtros
                };

                try {
                    swalLoading('Generando reporte kardex', 'Espere mientras se cargan los datos');

                    this.showContent = false;
                    this.loading = true;

                    const {data} = await axios.post(this.url + "/kardex/getKardexProducto", datos);

                    if (data.status === 'success') {
                        this.showContent = true;
                        this.listaKardex = data.data;
                        Swal.close();
                        dataTable("#tblKardexProducto", 'Reporte Kardex', this.nombreProducto);
                    } else if (data.status === 'error') {
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

            formatToUSD(amount) {
                return formatToUSD(amount);
            }

        }


    });
    window.appKardexProd.use(AllDirectives);
    window.appKardexProd.mount('#app');

</script>