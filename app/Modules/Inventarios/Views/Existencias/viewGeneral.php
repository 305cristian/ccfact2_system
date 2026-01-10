<!DOCTYPE html>
<!--
/**
 * Description of viewGeneral
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 4 ene 2026
 * @time 12:25:04 a.m.
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
                <i class="fas fa-box"></i> Inventario General
            </h5>           
        </div>

        <div class="card-body">
            <fieldset>
                <legend>
                    <i class="fas fa-box me-2"></i> Filtros de Inventario General
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
                                @option:selected="onChange"/>
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
                                @option:selected="onChange"/>
                        </div>
                    </div>


                    <!-- Impuesto -->
                    <div class="col-md-2 col-sm-6  form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-percent me-2"></i>IVA
                            </span>
                            <select class="form-select" v-model="filtros.invIva"  @change="onChange">
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
                            <select class="form-select" v-model="filtros.invStock" @change="onChange">
                                <option value="-1">Todos</option>
                                <option value="1">Con stock</option>
                                <option value="0">Sin stock</option>
                            </select>
                        </div>
                    </div>

                    <!-- Buscar -->
                    <div class="col-md-2 col-sm-6 ">
                        <button class="btn btn-system" @click="buscarInventario()" :disabled="loading">
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
                <div class="table-responsive mt-3">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="bg-system text-white">
                            <tr>
                                <th>CÓDIGO</th>
                                <th>BARCODE</th>
                                <th>PRODUCTO</th>
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
                            <tr v-for="item in listaInventario" :key="item.id">

                                <!-- CODIGO -->
                                <td v-tooltip:top="item.id"><span class="badge-type">{{ item.prod_codigo }}</span></td>

                                <!-- BARCODE -->
                                <td><span class="text-muted">{{ item.prod_codigobarras || '-' }}</span></td>

                                <!-- PRODUCTO -->
                                <td><strong>{{ item.prod_nombre }}</strong></td>

                                <!-- STOCK -->
                                <td class="text-center">
                                    <span class="text-muted" >
                                        {{ item.stb_stock }}
                                    </span>
                                </td>
                                <!-- RESERVA -->
                                <td class="text-center" >
                                        <span :class="parseFloat(item.reservaProducto) > 0
                                          ? 'text-muted badge bg-warning' 
                                          : ''">
                                        {{ parseFloat(item.reservaProducto).toFixed(2) }}
                                    </span>
                                </td>

                                <!-- STOCK DISPONIBLE -->
                                <td class="text-center">
                                    <span
                                        class="badge"
                                        :class="(parseFloat(item.stb_stock) - parseFloat(item.reservaProducto)) < parseFloat(item.prod_existenciaminima)
                                        ? 'bg-danger' 
                                        : 'bg-success'"
                                        >
                                        {{ parseFloat(item.stb_stock) -  parseFloat(item.reservaProducto) }}
                                    </span>
                                </td>



                                <!-- IVA -->
                                <td class="text-center">
                                    <span class="badge" :class="item.prod_ivaporcentage == 0 ? 'bg-secondary' : 'bg-info'" >
                                        {{ item.prod_ivaporcentage == 0 ? 'SIN IVA' : 'IVA' }}
                                    </span>
                                </td>

                                <!-- BODEGA -->
                                <td>
                                    <span v-if="filtros.invBodega">{{nombreBodega(item.bod_nombre)}}</span>
                                    <span v-else><a href="#" @click="viewStockBodega(item.id)">{{nombreBodega(item.bod_nombre)}}</a></span>
                                </td>

                                <!-- COSTO PROMEDIO -->
                                <td  class="text-end"> 
                                    <small class="text-muted">  {{ formatToUSD(item.prod_costopromedio)}} </small>
                                </td>
                                <!-- COSTO +ULTIMO -->
                                <td  class="text-end"> 
                                    <small class="text-muted">  {{ formatToUSD(item.prod_costoultimo)}} </small>
                                </td>
                                <!-- GRUPO -->
                                <td> 
                                    <small class="text-muted">  {{ item.gr_nombre || '-' }} </small>
                                </td>
                                <!-- SUBGRUPO -->
                                <td> 
                                    <small class="text-muted">  {{ item.sgr_nombre || '-' }} </small>
                                </td>

                                <!-- ACCIONES -->
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

                            <tr v-if="listaInventario.length === 0">
                                <td colspan="11" class="text-center text-muted py-4">
                                    <i class="fas fa-box-open fa-2x mb-2"></i>
                                    <br>
                                    No se encontraron productos
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</div>

<script type="text/javascript">

    var listaGrupos = <?php echo json_encode($listaGrupos); ?>;
    var listaBodegas = <?php echo json_encode($listaBodegas); ?>;

    if (window.appInvG) {
        window.appInvG.unmount();
    }
    window.appInvG = Vue.createApp({
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
                listaInventario: [],

                searchTimeout: null

            };
        },
        computed: {
            nombreBodega() {
                return (bodega, id) => {
                    if (this.filtros.invBodega) {
                        return bodega;
                    } else {
                        return "ALL SELECT";
                    }
                }


            }
        },
        methods: {

            async buscarInventario() {

                const datos = this.filtros;
                try {
                    this.loading = true;
                    const {data} = await axios.post(this.url + "/inventarios/general", datos);

                    if (data.status === 'success') {
                        this.showContent = true;
                        this.listaInventario = data.data;
                    } else {
                        sweet_msg_dialog('warning', 'No se han encontrado reistros para mostrar');
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                } finally {
                    this.loading = false;
                }

            },
            async viewStockBodega(id) {
                const {data} = await axios.get(this.url + "/inventarios/viewStockBodega/"+id);
                Swal.fire({
                    width:'30%',
                    html: data
                });
            },
            verKardex(row) {
                window.location.href = `${this.url}/kardex/producto/${row.fk_producto}?bodega=${row.fk_bodega}`;
            },
            onSelectProducto(option) {
                this.filtros.invProductoId = option ? option.id : null;
            },
            onChange() {
                this.showContent = false;
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
            formatToUSD(amount) {
                return formatToUSD(amount);
            },
        }


    });
    window.appInvG.use(AllDirectives);
    window.appInvG.mount('#app');

</script>