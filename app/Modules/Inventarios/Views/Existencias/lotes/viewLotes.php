<!DOCTYPE html>
<!--
/**
 * Description of viewLotes
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 4 ene 2026
 * @time 12:25:13 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->


<style>
    .multiselect__tags {
        border-radius: 0px 5px 5px 0px
    }
</style>
<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system">
                <i class="fas fa-box"></i> Inventario Por Lotes
            </h5>           
        </div>

        <div class="card-body">
            <fieldset>
                <legend>
                    <i class="fas fa-box me-2"></i> Filtros de Inventario por lotes
                </legend>

                <div class="row col-md-12">

                    <!-- Buscador productos multiselect -->
                    <div class="col-md-4 col-sm-12 form-group-custom">

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
                    <div class="col-md-2 col-sm-6  form-group-custom">
                        <div class="d-flex justify-content-between align-items-center border">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-warehouse me-2"></i>Bodega
                            </span>
                            <vue-select
                                class="flex-grow-1"
                                :options="listaBodegas"
                                label="bod_nombre"
                                v-model="filtros.invBodega"
                                :reduce="b => b.id"
                                placeholder="Seleccione una bodega"
                                @option:selected="showContent = false"/>
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
                                v-model="filtros.invGrupo"
                                :reduce="g => g.id"
                                placeholder="Seleccione un Grupo"
                                @option:selected="onChangeGrupo"/>
                        </div>
                    </div>

                    <!-- Subgrupo -->
                    <div class="col-md-2 col-sm-6  form-group-custom">
                        <div class="d-flex justify-content-between align-items-center border">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-sitemap me-2"></i>Subgrupo
                            </span>
                            <vue-select
                                class="flex-grow-1"
                                :options="listaSubgrupos"
                                label="sgr_nombre"
                                v-model="filtros.invSubgrupo"
                                :reduce="s => s.id"
                                :disabled="!filtros.invGrupo"
                                placeholder="Seleccione un Subgrupo"
                                @option:selected="showContent = false"/>
                        </div>
                    </div>


                    <!-- Impuesto -->
                    <div class="col-md-2 col-sm-6  form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-percent me-2"></i>IVA
                            </span>
                            <select class="form-select" v-model="filtros.invIva"  @change="showContent = false">
                                <option value="-1">Todos</option>
                                <option value="2">Con IVA</option>
                                <option value="1">Sin IVA</option>
                            </select>
                        </div>
                    </div>

                    <!-- Estado de Stock -->
                    <div class="col-md-2 col-sm-6  form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-filter me-2"></i>Stock
                            </span>
                            <select class="form-select" v-model="filtros.invStock" @change="showContent = false">
                                <option value="-1">Todos</option>
                                <option value="1">Con stock</option>
                                <option value="0">Sin stock</option>
                            </select>
                        </div>
                    </div>

                    <!-- Buscar -->
                    <div class="col-md-2 col-sm-6 ">
                        <button class="btn btn-system" @click="searchDataReport()" :disabled="loading">
                            <span v-if="loading"><i class="loading-spin"></i> Generando...</span>
                            <span v-else><i class="fas fa-search"></i> Generar Reporte</span>
                        </button>
                    </div>

                </div>
            </fieldset>

            <div  v-if="!showContent" class="empty-state">
                <span v-if="loading" ><i class="fas fa-circle-notch fa-spin"></i></span>
                <span v-else ><i class="fas fa-inbox"></i></span>
                <p>No hay reporte Generado</p>
                <p style="font-size: 0.9rem; color: #d1d5db;">
                    Utiliza los filtros de búsqueda de arriba para proceder
                </p>
                <p style="font-size: 0.9rem; color: #d1d5db;">
                    De click en el boton Generar Reporte
                </p>
            </div>
            <div v-else>
                <!-- VIEW PAGINATION HEAD-->
                <?php echo view('\Modules\Inventarios\Views\Existencias\viewPaginationHead') ?>
                <!-- FIN VIEW PAGINATION HEAD-->      
                <div class="table-wrapper-loading">
                    <div class="table-responsive mt-3">
                        <div v-if="loading" class="table-loading">
                            <div class="loader-box"> 
                                <span><i class="fas fa-spinner fa-spin"></i> Cargando...</span>
                            </div>
                        </div>
                        <table class="table table-hover table-striped align-middle">
                            <thead class="bg-system text-white">
                                <tr>
                                    <th>CÓDIGO</th>
                                    <th>BARCODE</th>
                                    <th class="sortable" @click="sort('prod_nombre')" >PRODUCTO <i :class="getSortClass('prod_nombre')"></i></th>
                                    <th class="text-left">PRES.</th>
                                    <th class="text-left">LOTE</th>
                                    <th class="text-left">F. CADUCIDAD</th>
                                    <th class="text-center">STOCK</th>
                                    <th class="text-center">RESERVA</th>
                                    <th class="text-center">STOCK DISPONIBLE</th>
                                    <th class="text-center">IVA</th>
                                    <th>BODEGA</th>
                                    <th>C. PROMEDIO</th>
                                    <th>C. ULTIMO</th>
                                    <th>GRUPO</th>
                                    <th>SUBGRUPO</th>
                                    <th class="text-center">ACCIONES</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="item in listaInventarioLotes" :key="item.id+'_'+item.fk_lote">

                                    <!--CODIGO--> 
                                    <td v-tooltip:top="item.id"><span class="badge-type">{{ item.prod_codigo }}</span></td>

                                    <!--BARCODE--> 
                                    <td><span class="text-muted">{{ item.prod_codigobarras || '-' }}</span></td>

                                    <!--PRODUCTO--> 
                                    <td><strong>{{ item.prod_nombre }}</strong></td>

                                    <!--PRESENTACION-->
                                    <td><span class="text-muted">{{ item.um_nombre_corto || '-' }}</span></td>

                                    <!--LOTE--> 
                                    <td>{{ item.lot_lote }}</td>

                                    <!--FECHA CADUCIDAD-->
                                    <td>{{ item.lot_fecha_caducidad }}</td>


                                    <!--STOCK--> 
                                    <td class="text-center">
                                        <span class="text-muted" >
                                            {{ item.stbl_stock }}
                                        </span>
                                    </td>


                                    <!--RESERVA--> 
                                    <td class="text-center">
                                        <span 
                                            v-if="parseFloat(item.reservaProducto) > 0"
                                            @click="verReserva(item.id, item.fk_lote)"
                                            class="text-muted badge bg-warning"
                                            style="cursor:pointer"
                                            >
                                            {{ parseFloat(item.reservaProducto).toFixed(2) }}
                                        </span>

                                        <span v-else>
                                            {{ parseFloat(item.reservaProducto).toFixed(2) }}
                                        </span>
                                    </td>

                                    <!--STOCK DISPONIBLE--> 
                                    <td class="text-center">
                                        <span
                                            class="badge"
                                            :class="(parseFloat(item.stbl_stock) - parseFloat(item.reservaProducto)) < parseFloat(item.prod_existenciaminima)
                                            ? 'bg-danger' 
                                            : 'bg-success'"
                                            >
                                            {{ parseFloat(item.stbl_stock) -  parseFloat(item.reservaProducto) }}
                                        </span>
                                    </td>



                                    <!--IVA--> 
                                    <td class="text-center">
                                        <span class="badge" :class="item.prod_ivaporcentage == 0 ? 'bg-secondary' : 'bg-info'" >
                                            {{ item.prod_ivaporcentage == 0 ? 'SIN IVA' : 'IVA' }}
                                        </span>
                                    </td>

                                    <!--BODEGA--> 
                                    <td>
                                        <span v-if="filtros.invBodega">{{nombreBodega(item.bod_nombre)}}</span>
                                        <span v-else><a href="#" @click="viewStockBodegaLote(item.id, item.fk_lote)">{{nombreBodega(item.bod_nombre)}}</a></span>
                                    </td>

                                    <!--COSTO PROMEDIO--> 
                                    <td  class="text-end"> 
                                        <small class="text-muted">  {{ formatToUSD(item.prod_costopromedio)}} </small>
                                    </td>

                                    <!--COSTO ULTIMO--> 
                                    <td  class="text-end"> 
                                        <small class="text-muted">  {{ formatToUSD(item.prod_costoultimo)}} </small>
                                    </td>

                                    <!--GRUPO--> 
                                    <td> 
                                        <small class="text-muted">  {{ item.gr_nombre || '-' }} </small>
                                    </td>

                                    <!--SUBGRUPO--> 
                                    <td> 
                                        <small class="text-muted">  {{ item.sgr_nombre || '-' }} </small>
                                    </td>

                                    <!--ACCIONES--> 
                                    <td class="text-center">
                                        <button
                                            class="btn btn-outline-primary btn-sm"
                                            title="Ver Kardex"
                                            @click="verKardex(item)"
                                            >
                                            <i class="fas fa-list"></i>
                                        </button>
                                    </td>

                                </tr>

                                <tr v-if="listaInventarioLotes.length === 0">
                                    <td colspan="16" class="text-center text-muted py-4">
                                        <i class="fas fa-box-open fa-2x mb-2"></i>
                                        <br>
                                        No se encontraron productos
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- VIEW PAGINATION FOOT-->
                        <?php echo view('\Modules\Inventarios\Views\Existencias\viewPaginationFoot') ?>
                        <!-- FIN VIEW PAGINATION FOOT-->      
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">

    var listaGrupos = <?php echo json_encode($listaGrupos); ?>;
    var listaBodegas = <?php echo json_encode($listaBodegas); ?>;

    if (window.appInvL) {
        window.appInvL.unmount();
    }
    window.appInvL = Vue.createApp({
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
                    invBodega: '',
                    invGrupo: '',
                    invSubgrupo: '',
                    invProductoId: null,
                    invIva: '-1',
                    invStock: '1'
                },

                //LISTAS
                listaBodegas: listaBodegas,
                listaGrupos: listaGrupos,
                listaSubgrupos: [],
                listaSearchProductos: [],
                listaInventarioLotes: [],

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
                searchTimeout: null

            };
        },
        computed: {
            nombreBodega() {
                return (bodega) => {
                    if (this.filtros.invBodega) {
                        return bodega;
                    } else {
                        return "ALL SELECT";
                    }
                };
            }
        },
        methods: {

            //VER RESERVA
            async verReserva(id, lote) {
                const datos = {
                    id: id,
                    lote: lote,
                    bodega: this.filtros.invBodega
                };
                const {data} = await axios.post(this.url + "/inventarios/viewReservaLote", datos);
                Swal.fire({
                    width: '40%',
                    html: data
                });
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
                    const {data} = await axios.post(this.url + "/inventarios/lotes", datos);

                    if (data.status === 'success') {
                        this.showContent = true;
                        this.listaInventarioLotes = data.data;
                        this.pagination.totalRecords = data.recordsTotal;
                        this.pagination.filteredRecords = data.recordsFiltered;
                    } else {
                        sweet_msg_dialog('warning', 'No se han encontrado reistros para mostrar');
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

            async viewStockBodegaLote(id, lote) {
                const {data} = await axios.get(this.url + "/inventarios/viewStockBodegaLote/" + id + "/" + lote);
                Swal.fire({
                    width: '40%',
                    html: data
                });
            },
            verKardex(row) {
                window.location.href = `${this.url}/kardex/producto/${row.fk_producto}?bodega=${row.fk_bodega}`;
            },
            onSelectProducto(option) {
                this.filtros.invProductoId = option ? option.id : null;
            },

            async onChangeGrupo(grupo) {
                this.filtros.subgrupo = '';
                if (!grupo.id) {
                    this.listaSubgrupos = [];
                    return;
                }
                const {data} = await axios.get(this.url + '/comun/subgrupos/getSubgrupoByGrupo/' + grupo.id);
                this.listaSubgrupos = data || [];
            },

            onRemove() {
                this.listaSearchProductos = [];
                this.filtros.productoSearch = null;
            },
            async exportExcel() {
                const datos = {
                    ...this.filtros,
                    search: this.pagination.searchTerm,
                };
                try {
                    this.downloadingexcel = true;
                    const {data} = await axios.post(this.url + '/inventarios/exportExcelLotes', datos, {responseType: 'blob'});
                    const url = window.URL.createObjectURL(new Blob([data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'Inventario_Por_Lotes.xlsx');
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
                    const {data} = await axios.post(this.url + '/inventarios/exportPdfLotes', datos, {responseType: 'blob'});
                    const blob = new Blob([data], {type: 'application/pdf'});
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = 'Inventario_Por_Lotes.pdf';
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
    window.appInvL.use(AllDirectives);
    window.appInvL.mount('#app');

</script>