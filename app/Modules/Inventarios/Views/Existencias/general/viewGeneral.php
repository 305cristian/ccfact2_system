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
    .table-responsive {
        max-height: 500px;
        white-space: nowrap;
        overflow-x: auto;
        overflow-y: auto;
    }
     .table-scroll {
        max-height: 600px;  
        overflow-y: auto;
    }

    .table-scroll thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #f8f9fa;
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
                                <span><i class="fas fa-spinner fa-spin fa-2x"></i></span>
                            </div>
                        </div>
                        <table class="table table-hover table-striped align-middle">
                            <thead class="bg-system text-white">
                                <tr>
                                    <th>CÓDIGO</th>
                                    <th>BARCODE</th>
                                    <th class="sortable" @click="sort('prod_nombre')" >PRODUCTO <i :class="getSortClass('prod_nombre')"></i></th>
                                    <th class="text-left">PRES.</th>
                                    <th class="text-center">STOCK</th>
                                    <th class="text-center">RESERVA</th>
                                    <th class="text-center">STOCK DISPONIBLE</th>
                                    <th class="text-center">IVA</th>
                                    <th class="text-center">CTRL.LOTE</th>
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

                                    <!--PRESENTACION-->
                                    <td><span class="text-muted">{{ item.um_nombre_corto || '-' }}</span></td>

                                    <!-- STOCK -->
                                    <td class="text-center">
                                        <span class="text-muted" >
                                            {{ item.stb_stock }}
                                        </span>
                                    </td>
                                    <!-- RESERVA -->

                                    <td class="text-center">
                                        <span 
                                            v-if="parseFloat(item.reservaProducto) > 0"
                                            @click="verReserva(item.id)"
                                            class="text-muted badge bg-warning"
                                            style="cursor:pointer"
                                            >
                                            {{ parseFloat(item.reservaProducto).toFixed(2) }}
                                        </span>

                                        <span v-else>
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
                                        <span class="badge" :class="item.prod_ivaporcentage === '0.00' ? 'bg-secondary' : 'bg-info'" >
                                            {{ item.prod_ivaporcentage === '0.00' ? 'SIN IVA' : 'IVA' }}
                                        </span>
                                    </td>

                                    <!-- LOTE -->
                                    <td class="text-center">
                                        <span class="badge" :class="item.prod_ctrllote === '1' ? 'bg-info' : 'bg-secondary'" >
                                            {{ item.prod_ctrllote === '1' ? 'SI' : 'NO' }}
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

                                    <!-- ACCIONES  -->
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li v-if="updateDataProducto"><a class="dropdown-item" href="#" @click="loadDataProducto(item.id)"><i class="fas fa-eye text-warning"></i> Ver detalle</a></li>
                                                <li><a class="dropdown-item" href="#" @click="openMiniKardex(item)"><i class="fas fa-chart-line me-2 text-primary""></i> Ver Kardex</a></li>
                                            </ul>
                                        </div>
                                    </td>

                                </tr>

                                <tr v-if="listaInventario.length === 0">
                                    <td colspan="15" class="text-center text-muted py-4">
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
    <!-- VIEW MODAL EDIT PRODUCTO-->
    <?php echo view('\Modules\Inventarios\Views\Existencias\general\viewModalProducto') ?>
    <!-- FIN MODAL EDIT PRODUCTO-->     

    <!-- VIEW MODAL MINI KARDEX-->
    <?php echo view('\Modules\Inventarios\Views\Existencias\general\viewModalMiniKardex') ?>
    <!-- FIN MODAL  MINI KARDEX-->      
</div>

<script type="text/javascript">

    var listaGrupos = <?php echo json_encode($listaGrupos); ?>;
    var listaBodegas = <?php echo json_encode($listaBodegas); ?>;

    //VARIABLES GLOBALES PARA UPDATE PODUCTO
    var listaUnidadesMedida = <?php echo json_encode($listaUnidadesMedida) ?>;
    var listaMarcas =<?php echo json_encode($listaMarcas); ?>;
    var listaTipoProducto = <?php echo json_encode($listaTipoProducto); ?>;
    var listaImpuestosTarifa = <?php echo json_encode($listaImpuestosTarifa); ?>;
    var listaImpuestosICE = <?php echo json_encode($listaImpuestosICE); ?>;
//    var listaGrupos = <?php echo json_encode($listaGrupos); ?>;
    var listaSubGruposModal = <?php echo json_encode($listaSubgrupos); ?>;
    var listaCtaContable = <?php echo json_encode($listaCtaContable); ?>;
    var listaTiposPvp = <?php echo json_encode($listaTiposPvp); ?>;
    var ivaActual =<?php echo getSettings("IVA"); ?>;

    //PERMISOS

    var updateDataProducto = <?php echo!empty($updateDataProducto) ? 'true' : 'false'; ?>;

    if (window.appInvG) {
        window.appInvG.unmount();
    }
    window.appInvG = Vue.createApp({
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
                listaInventario: [],

                //EDIT PRODUCTO
                modalProdInstance: false,
                listaUnidadesMedida: listaUnidadesMedida,
                listaMarcas: listaMarcas,
                listaTipoProducto: listaTipoProducto,
                listaImpuestosTarifa: listaImpuestosTarifa,
                listaImpuestosICE: listaImpuestosICE,
                listaCtaContable: listaCtaContable,
                listaTiposPvp: listaTiposPvp,
                editProducto: {},
                formValidacion: [],
                listaSubGruposModal: listaSubGruposModal,
                listaSubGruposModalFilter: [],
                ivaActual: ivaActual,
                price: [],
                tipoPrecioVal: [],
                tipoPrecioId: [],
                updating: false,

                //MINI KARDEX
                listaMiniKardex: [],
                productoSeleccionado: '',
                modalMiniKardex: null,
                loadingMiniKardex: false,

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

                //PERMISOS
                updateDataProducto: updateDataProducto

            };
        },
        mounted() {
            this.modalProdInstance = new bootstrap.Modal(this.$refs.modalProductos);
            this.modalMiniKardex = new bootstrap.Modal(this.$refs.modalMiniKardex);
        },

        computed: {
            nombreBodega() {
                return (bodega) => {
                    return this.filtros.invBodega ? bodega : 'TODAS';
                };
            }
        },
        methods: {
            //VER RESERVA
            async verReserva(id) {
                const datos = {
                    id: id,
                    bodega: this.filtros.invBodega
                };
                const {data} = await axios.post(this.url + "/inventarios/viewReserva", datos);
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

                    const {data} = await axios.post(this.url + "/inventarios/general", datos);

                    if (data.status === 'success') {
                        this.showContent = true;
                        this.listaInventario = data.data;
                        this.pagination.totalRecords = data.recordsTotal;
                        this.pagination.filteredRecords = data.recordsFiltered;
                        Swal.close();
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

            async viewStockBodega(id) {
                const {data} = await axios.get(this.url + "/inventarios/viewStockBodega/" + id);
                Swal.fire({
                    width: '30%',
                    html: data
                });
            },
            verKardex(row) {
                window.location.href = `${this.url}/kardex/producto`;
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

            async loadDataProducto(productoId) {
                swalLoading('Cargando...', 'Cargando detalle del producto');

                const {data} = await axios.get(`${this.url}/inventarios/getDataProducto/${productoId}`);

                if (data.data) {

                    let prod = data.data;

                    this.modalProdInstance.show();
                    this.editProducto = {
                        prodNombre: prod.prod_nombre,
                        prodCodigo: prod.prod_codigo,
                        prodCodigoBarras: prod.prod_codigobarras,
                        prodCodigoBarras2: prod.prod_codigobarras2,
                        prodCodigoBarras3: prod.prod_codigobarra3,
                        prodExistenciaMinima: prod.prod_existenciaminima,
                        prodExistenciaMaxima: prod.prod_existenciamaxima,
                        prodVenta: prod.prod_venta === '1' ? true : false,
                        prodCompra: prod.prod_compra === '1' ? true : false,
                        prodIsServicio: prod.isservicio === '1' ? true : false,
                        prodIsGasto: prod.prod_isgasto === '1' ? true : false,
                        prodValorMedida: prod.prod_valormedida,
                        prodUnidadMedida: prod.fk_unidadmedida,
                        grupo: prod.id_grupo,
                        prodSubgrupo: prod.fk_subgrupo,
                        prodMarca: prod.fk_marca,
                        prodTipoProducto: prod.fk_tipoproducto,
                        prodIvaPorcentajeId: prod.idImpuesto,
                        prodIvaPorcentaje: prod.prod_ivaporcentage,
                        prodIcePorcentaje: prod.prod_iceporcentage,
                        prodIcePorcentajeId: prod.idImpuestoIce,
                        prodTieneICE: prod.prod_tiene_ice,
                        prodIsPromo: prod.prod_ispromo === '12' ? true : false,
                        prodPvpPromo: prod.prod_pvppromo,
                        prodEspecificaciones: prod.prod_especificaciones === '1' ? true : false,
                        prodCtaCompras: prod.fk_cuentacontablecompras,
                        prodCtaVentas: prod.fk_cuentacontableventas,
                        prodIsSuperProducto: prod.prod_issuperproducto === '1' ? true : false,
                        prodCtrlLote: prod.prod_ctrllote === '1' ? true : false,
                        prodFacturarEnNegativo: prod.prod_facturar_ennegativo === '1' ? true : false,
                        prodFacturarPrecioInferiorCosto: prod.prod_facturar_precio_inferiorcosto === '1' ? true : false,
                        prodImagen: prod.prod_imagen,
                        prodEstado: prod.prod_estado === '1' ? true : false

                    };
                    this.getSubgrupo();
                    await this.getPreciosProducto(prod.id);

                    this.idEdit = prod.id;
                    this.nameAux = prod.prod_nombre;
                    this.codeAux = prod.prod_codigo;

                    this.aplicaIce();

                    Swal.close();
                } else {
                    sweet_msg_dialog('warning', 'No se encontro detalle del producto');
                }


            },
            async updateProducto() {

                let datos = this.formData(this.editProducto);
                datos.append('idProd', this.idEdit);
                datos.append('codeAux', this.codeAux);//TODO: ESTA VARIABLE SE LA USA PARA VALIDAR QUE NO EXISTA OTRA REGISTRO CON EL MISMO CODIGO
                datos.append('nameAux', this.nameAux);//TODO: ESTA VARIABLE SE LA USA PARA VALIDAR QUE NO EXISTA OTRA REGISTRO CON EL MISMO NOMBRE
                datos.append('tipoPrecioVal', this.tipoPrecioVal);
                datos.append('tipoPrecioId', this.tipoPrecioId);

                try {
                    this.updating = true;
                    const {data} = await axios.post(`${this.url}/admin/productos/updateProducto`, datos);
                    if (data.status === 'success') {
                        sweet_msg_dialog('success', data.msg);
                        this.modalProdInstance.hide();
                    } else if (data.status === 'existe') {
                        sweet_msg_dialog('warning', data.msg);
                    } else if (data.status === 'vacio') {
                        this.formValidacion = data.msg;
                    } else if (data.status === 'error') {
                        sweet_msg_dialog('error', data.msg);
                    } else if (data.status === 'warning') {
                        sweet_msg_dialog('warning', data.msg);
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.message);
                } finally {
                    this.updating = false;
                }




            },
            async getSubgrupo() {
                this.listaSubGruposModalFilter = this.listaSubGruposModal.filter(val => val.fk_grupo === this.editProducto.grupo);
            },
            async getPreciosProducto(idProducto) {
                try {
                    let {data} = await axios.get(`${this.url}/admin/productos/getPreciosProducto/${idProducto}`);
                    if (data) {

                        data.map((val, index) => {
                            let iva = (this.ivaActual / 100) + 1;
                            this.price[index] = (val.pp_valor * parseFloat(iva)).toFixed(2);
                            this.tipoPrecioVal[index] = val.pp_valor;
                            this.tipoPrecioId[index] = val.fk_tipo_precio;
                        });
                    } else {
                        sweet_msg_toast("warning", "El producto selecionado, no tiene precios establecidos");
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data.message);
                }

            },
            desglosarIva(index) {
                let precio = this.price[index];
                let iva = (this.ivaActual / 100) + 1;
                let priceSinIva = 0;
                priceSinIva = precio;
                if (this.editProducto.prodIvaPorcentajeId === '2') {
                    priceSinIva = precio / parseFloat(iva);
                }

                this.tipoPrecioVal[index] = priceSinIva;
                document.getElementById("prodPriceSinIva" + index).value = priceSinIva;
            },
            desglosarIva2() {
                let iva = (this.ivaActual / 100) + 1;
                let priceSinIva = 0;
                this.price.map((val, index) => {
                    priceSinIva = val;
                    if (this.editProducto.prodIvaPorcentajeId === '2') {
                        priceSinIva = val / parseFloat(iva);
                    }

                    this.tipoPrecioVal[index] = priceSinIva;
                    document.getElementById("prodPriceSinIva" + index).value = priceSinIva;
                });
            },
            aplicaIce() {
                if (this.editProducto.prodTieneICE === '1') {
                    document.getElementById("selectImpIce").style.display = "block";
                } else {
                    this.editProducto.prodIcePorcentajeId = "";
                    document.getElementById("selectImpIce").style.display = "none";
                }
            },
            clear() {

            },
            async openMiniKardex(item) {

                this.productoSeleccionado = item;

                this.loadingMiniKardex = true;

                const datos = {
                    productoId: item.id,
                    kardBodega: this.filtros.invBodega
                };
                try {
                    const {data} = await axios.post(this.url + '/kardex/getMiniKardexProducto', datos);
                    if (data.status === 'success') {
                        this.modalMiniKardex.show();
                        this.listaMiniKardex = data.data;
                    } else {
                        sweet_msg_dialog('warning', 'No se han detectado movimientos en los últimos 30 dias en el producto seleccionado');
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.message);
                } finally {
                    this.loadingMiniKardex = false;
                }
            },
            formData(obj) {
                var formData = new FormData();
                for (var key in obj) {
                    formData.append(key, obj[key]);
                }
                return formData;
            }
            ,
            async exportExcel() {
                const datos = {
                    ...this.filtros,
                    search: this.pagination.searchTerm,
                };
                try {
                    this.downloadingexcel = true;
                    const {data} = await axios.post(this.url + '/inventarios/exportExcelGeneral', datos, {responseType: 'blob'});
                    const url = window.URL.createObjectURL(new Blob([data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'Inventario_General.xlsx');
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
                    const {data} = await axios.post(this.url + '/inventarios/exportPdfGeneral', datos, {responseType: 'blob'});
                    const blob = new Blob([data], {type: 'application/pdf'});
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = 'Inventario_General.pdf';
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
    window.appInvG.use(AllDirectives);
    window.appInvG.mount('#app');

</script>