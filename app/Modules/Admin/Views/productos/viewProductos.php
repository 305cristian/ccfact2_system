<!DOCTYPE html>
<!--
/**
 * Description of productosView
 *
/**
 * @author CRISTIAN PAZ
 * @date 15 abr. 2024
 * @time 11:35:26
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-box-open"></i> Administrar Productos</h5>
        </div>
        <div class="card-body" style="overflow-x: auto">
            <div class="row col-md-12">

                <div class="col-md-3 mb-2">
                    <label for="selectStock" class="col-form-label col-form-label-sm"><i class="fal fa-box-open"></i> Producto</label>
                    <vue-multiselect
                        v-model="keyProducto" 
                        tag-placeholder="Producto no Encontrado"
                        placeholder="Buscar Por Nombre"
                        label="prodNombre"
                        track-by="prodCode"
                        :multiple="false"
                        :searchable="true"
                        :options-limit="15"
                        :show-no-results="true"
                        :options="listaSearchProductos"
                        @remove="onRemove"
                        @search-change="searchProductos($event, 'name')"/>

                    <template slot="option" slot-scope="{ option }">
                        <span style="font-size: 12px">{{ option.prodCode+' - ' }} <strong>{{ option.prodNombre }}</strong> </span>
                    </template>
                    </vue-multiselect>
                </div>
                <div class="col-md-3 mb-2">
                    <label for="selectStock" class="col-form-label col-form-label-sm"><i class="fal fa-barcode-alt"></i> Código</label>
                    <vue-multiselect
                        v-model="keyProducto" 
                        tag-placeholder="Producto no Encontrado"
                        placeholder="Buscar Por Códgio"
                        label="codigos"
                        track-by="prodCode"
                        :multiple="false"
                        :searchable="true"
                        :options-limit="2"
                        :show-no-results="true"
                        :options="listaSearchProductos"
                        @remove="onRemove"
                        @search-change="searchProductos($event, 'code')"/>

                    <template slot="option" slot-scope="{ option }">
                        <span style="font-size: 12px">{{ option.prodCode+': ' }} <strong>{{ option.prodNombre }} </strong></span>
                    </template>
                    </vue-multiselect>
                </div>

                <div class="col-md-2 mb-2">
                    <label for="selectStock" class="col-form-label col-form-label-sm"><i class="fal fa-group"></i> Grupo</label>
                    <select v-model="selectGrupo" id="selectGrupo" title="Seleccione un Grupo" class="form-control selectpicker border" data-live-search="true" data-style="btn-white">                  
                        <option value="-1">TODOS</option>
                        <option v-for="lg of listaGrupos" v-bind:value="lg.id">{{lg.gr_nombre}}</option>                  
                    </select>

                </div>
                <div class="col-md-2  mb-2">
                    <label for="selectStock"  class="col-form-label col-form-label-sm"><i class="fal fa-file-archive"></i> Stock</label>
                    <select v-model="selectStock" id="selectStock" class="form-select">                  
                        <option v-bind:value="1">CON STOCK</option>
                        <option v-bind:value="0">SIN STOCK</option>
                        <option v-bind:value="-1">TODOS</option>
                    </select>

                </div>
                <div class="col-md-2  mb-2">
                    <label for="selectImpuesto" class="col-form-label col-form-label-sm"><i class="fal fa-clipboard"></i> Impuesto</label>
                    <select v-model="selectImpuesto" id="selectImpuesto" class="form-select">                  
                        <option v-bind:value="2">Aplica IVA</option>
                        <option v-bind:value="1">No Aplica IVA</option>
                        <option v-bind:value="-1">TODOS</option>
                    </select>
                </div>
                <div class="col-md-2  mb-2">
                    <label for="selectEstado" class="col-form-label col-form-label-sm"><i class="fal fa-file-binary"></i> Estado</label>
                    <select v-model="selectEstado" id="selectEstado" class="form-select">                  
                        <option v-bind:value="1">ACTIVO</option>
                        <option v-bind:value="0">INACTIVO</option>
                        <option v-bind:value="-1">TODOS</option>
                    </select>
                </div>
                <div class="col-md-2  mb-2" style="position: relative; top: 30px">
                    <button class="btn btn-system-2" @click="getProductos()">
                        <span v-if='loading'><i class="loading-spin"></i> Buscando...</span>
                        <span v-else><i class="fas fa-search"></i> Buscar Productos</span>
                    </button>
                </div>
                <div id="panelBtnCreate" class="col-md-2  mb-2" style="position: relative; top: 30px">
                    <button class="btn btn-system-2" data-bs-toggle="modal" data-bs-target="#modalProductos"><span class="fas fa-box-archive"></span> Crear Producto</button>
                </div>
            </div>
            <br>
            <hr>
            <br>
            <div id="panelMain" class="col-md-12">
                <?php echo view('\Modules\Admin\Views\productos\viewTable') ?>
            </div>
        </div>
        <!--MODAL PRODUCTOS-->
        <?php echo view('\Modules\Admin\Views\productos\viewModal') ?>
        <!--CLOSE MODAL PRODUCTOS-->

    </div>
</div>

<script type="text/javascript">

<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';

    var listaUnidadesMedida = <?php echo json_encode($listaUnidadesMedida) ?>;
    var listaMarcas =<?php echo json_encode($listaMarcas); ?>;
    var listaTipoProducto = <?php echo json_encode($listaTipoProducto); ?>;
    var listaImpuestosTarifa = <?php echo json_encode($listaImpuestosTarifa); ?>;
    var listaImpuestosICE = <?php echo json_encode($listaImpuestosICE); ?>;
    var listaGrupos = <?php echo json_encode($listaGrupos); ?>;
    var listaCtaContable = <?php echo json_encode($listaCtaContable); ?>;
    var listaTiposPvp = <?php echo json_encode($listaTiposPvp); ?>;
    var ivaActual =<?php echo getSettings("IVA"); ?>;
    var autocodigo = '<?php echo $autocodigo; ?>';


    if (window.appProductos) {
        window.appProductos.unmount();
    }
    window.appProductos = Vue.createApp({
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
                ivaActual: ivaActual,
                loading: false,

                //TODO: V-MODELS
                idEdit: '',
                idGrupo: '',
                newProducto: {
                    prodNombre: '',
                    prodCodigo: autocodigo,
                    prodCodigoBarras: '',
                    prodCodigoBarras2: '',
                    prodCodigoBarras3: '',
                    //prodDetalle: '', //no
                    prodExistenciaMinima: '',
                    prodExistenciaMaxima: '',
                    //prodStockActual: '', //no
                    //prodCostoPromedio: '', //no
                    //prodCostoUltimo: '', //no
                    //prodCostoAlto: '', //no
                    prodVenta: true,
                    prodCompra: true,
                    prodIsServicio: false,
                    prodIsGasto: false,
                    prodValorMedida: '',
                    prodUnidadMedida: '',
                    prodSubgrupo: '',
                    prodMarca: '',
                    prodTipoProducto: '',

                    prodIvaPorcentaje: '0',
                    prodIvaPorcentajeId: '1',

                    prodIcePorcentaje: '',
                    prodIcePorcentajeId: '',
                    prodTieneICE: '0',

                    prodIsPromo: false,
                    prodPvpPromo: '',
                    //prodCostoInventario: '',/no
                    prodEspecificaciones: false,
                    prodCtaCompras: '',
                    prodCtaVentas: '',
                    prodIsSuperProducto: false,
                    prodCtrlLote: false,
                    prodFacturarEnNegativo: false,
                    prodFacturarPrecioInferiorCosto: false,
                    prodImagen: '',
                    prodEstado: true

                },
                //V-MODELS FILTROS SEARCH PROD
                selectGrupo: "",
                selectStock: "-1",
                selectEstado: "-1",
                selectImpuesto: "-1",

                //TIPO PRECIO PVP
                price: [],
                tipoPrecioVal: [],
                tipoPrecioId: [],

                //TODO: LISTAS
                listaUnidadesMedida: listaUnidadesMedida,
                listaMarcas: listaMarcas,
                listaTipoProducto: listaTipoProducto,
                listaImpuestosTarifa: listaImpuestosTarifa,
                listaImpuestosICE: listaImpuestosICE,
                listaGrupos: listaGrupos,
                listaCtaContable: listaCtaContable,
                listaTiposPvp: listaTiposPvp,
                listaProductos: [],

                keyProducto: [],
                listaSearchProductos: [],

                listaSubGrupos: [],
                formValidacion: []

            };
        },
        created() {


            this.tipoPrecioId = this.listaTiposPvp.map(ltpc => ltpc.id);
        },
        updated() {
            this.aplicaIce();
        },
        mounted() {
            $(".selectpicker").selectpicker();
            this.aplicaIce();
            panelMain.style.display = "none";
        },
        methods: {
            async searchProductos(dataSerach, val) {
                let datos = {dataSerach: dataSerach, val: val};
                try {
                    let {data} = await axios.post(this.url + '/admin/productos/searchProductos', datos);
                    if (data !== false) {
                        this.listaSearchProductos = data;
                    } else {
                        this.listaSearchProductos = [];
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data.message);
                    this.listaSearchProductos = [];
                }

            },
            aplicaIce() {
                if (this.newProducto.prodTieneICE === '1') {
                    document.getElementById("selectImpIce").style.display = "block";
                } else {
                    this.newProducto.prodIcePorcentajeId = "";
                    document.getElementById("selectImpIce").style.display = "none";
                }
            },

            async getProductos() {
                let datos = {
                    idProd: this.keyProducto ? this.keyProducto.prodCode : "",
                    stock: this.selectStock,
                    estado: this.selectEstado,
                    impuesto: this.selectImpuesto,
                    grupo: this.selectGrupo

                };
                //let datos = Object.fromEntries(Object.entries(dataFilter).filter(([_, value]) => value !== undefined && value !== ""));

                try {
                    this.loading = true;
                    let response = await axios.post(this.url + '/admin/productos/getProductos', datos);
                    if (response.data) {
                        this.listaProductos = response.data;
                        panelMain.style.display = "block";
                        panelBtnCreate.style.display = "none";

                    } else {
                        sweet_msg_dialog('warning', 'No se encontraron productos en los parametros seleccionados');
                        panelMain.style.display = "none";
                    }
                    if (this.admin) {
                        dataTableModalBtn('#tblProductos', 'Lista de Productos', '#modalProductos', 'CREAR PRODUCTO');
                    } else {
                        dataTable('#tblProductos', 'Lista de Productos');
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    this.loading = false;
                }
            },
            async getSubgrupo(idGrupo) {

                let datos = {
                    idGrupo: this.idGrupo ? this.idGrupo : idGrupo
                };
                try {
                    let response = await axios.post(this.url + '/admin/grupos/getSubgrupoByGrupo', datos);
                    if (response.data) {
                        this.listaSubGrupos = response.data;
                    } else {
                        sweet_msg_dialog('warning', 'No se encontraron subgrupos registradas');
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                }
            },
            async loadProducto(prod) {
                swalLoading('Cargando...', '');
                await this.getSubgrupo(prod.id_grupo);
                await this.getPreciosProducto(prod.id);
                this.newProducto = {
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
                this.idGrupo = prod.id_grupo;
                this.idEdit = prod.id;
                this.nameAux = prod.prod_nombre;
                this.codeAux = prod.prod_codigo;

                Swal.close();


            },
            async getPreciosProducto(idProducto) {
                try {
                    let {data} = await axios.get(`${this.url}/admin/productos/getPreciosProducto/${idProducto}`);
                    if (data) {
                        data.map((val, index) => {
                            let iva = (this.ivaActual / 100) + 1;
                            this.price[index] = (val.pp_valor * parseFloat(iva)).toFixed(2);
                            this.tipoPrecioVal[index] = val.pp_valor;
                        });
                    } else {
                        sweet_msg_toast("warning", "El producto selecionado, no tiene precios establecidos");
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data.message);
                }

            },
            async saveUpdateProducto() {
                let datos = this.formData(this.newProducto);
                let url = this.url + '/admin/productos/saveProducto';

                if (this.idEdit != '') {
                    datos.append('idProd', this.idEdit);
                    datos.append('codeAux', this.codeAux);//TODO: ESTA VARIABLE SE LA USA PARA VALIDAR QUE NO EXISTA OTRA REGISTRO CON EL MISMO CODIGO
                    datos.append('nameAux', this.nameAux);//TODO: ESTA VARIABLE SE LA USA PARA VALIDAR QUE NO EXISTA OTRA REGISTRO CON EL MISMO NOMBRE
                    url = this.url + '/admin/productos/updateProducto';
                }
                datos.append('tipoPrecioVal', this.tipoPrecioVal);
                datos.append('tipoPrecioId', this.tipoPrecioId);
                datos.append('grupo', this.idGrupo);
                try {
                    this.loading = true;
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getProductos();
                        this.consultarAutoCodigo();//Este invoca al autocodigo para cun nuevo producto
                        $('#modalProductos').modal('hide');
                        $('.modal-backdrop').remove();
                        this.tipoPrecioId = this.listaTiposPvp.map(ltpc => ltpc.id);//Esta linea vuelve a  cargar los ID de los N tipos de precio
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
            async consultarAutoCodigo() {
                try {
                    let {data} = await axios.get(`${this.url}/admin/productos/consultarAutoCodigo`);
                    if (data) {
                        this.newProducto.prodCodigo = data;
                    } else {
                        sweet_msg_toast("warning", "No se ha encontrado ningun codigo autogenerado");
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
                if (this.newProducto.prodIvaPorcentajeId === '2') {
                    priceSinIva = precio / parseFloat(iva);
                }

                v.tipoPrecioVal[index] = priceSinIva;
                document.getElementById("prodPriceSinIva" + index).value = priceSinIva;
            },
            desglosarIva2() {
                let iva = (this.ivaActual / 100) + 1;
                let priceSinIva = 0;
                this.price.map((val, index) => {
                    priceSinIva = val;
                    if (this.newProducto.prodIvaPorcentajeId === '2') {
                        priceSinIva = val / parseFloat(iva);
                    }

                    this.tipoPrecioVal[index] = priceSinIva;
                    document.getElementById("prodPriceSinIva" + index).value = priceSinIva;
                });
            },
            onRemove() {
                this.keyProducto = [];
                this.listaSearchProductos = [];
            },
            clear() {
                this.newProducto = {
                    prodNombre: '',
                    prodCodigo: '',
                    prodCodigoBarras: '',
                    prodCodigoBarras2: '',
                    prodCodigoBarras3: '',
                    prodExistenciaMinima: '',
                    prodExistenciaMaxima: '',
                    prodVenta: true,
                    prodCompra: true,
                    prodIsServicio: false,
                    prodIsGasto: false,
                    prodValorMedida: '',
                    prodUnidadMedida: '',
                    prodSubgrupo: '',
                    prodMarca: '',
                    prodTipoProducto: '',
                    prodIvaPorcentaje: '0',
                    prodIvaPorcentajeId: '1',
                    prodIcePorcentaje: '',
                    prodIcePorcentajeId: '',
                    prodTieneICE: '0',
                    prodIsPromo: false,
                    prodPvpPromo: '',
                    prodEspecificaciones: false,
                    prodCtaCompras: '',
                    prodCtaVentas: '',
                    prodIsSuperProducto: false,
                    prodCtrlLote: false,
                    prodFacturarEnNegativo: false,
                    prodFacturarPrecioInferiorCosto: false,
                    prodImagen: '',
                    prodEstado: true

                };
                this.estadoSave = true;
                this.idEdit = '';
                this.formValidacion = [];

                //Tipos de precio
                this.price = [];
                this.tipoPrecioVal = [];
            },
            formData(obj) {
                var formData = new FormData();
                for (var key in obj) {
                    formData.append(key, obj[key]);
                }
                return formData;
            },
            zfill(num) {
                return zFill(num, 3);
            }
        }
    });
    window.appProductos.mount('#app');

</script>