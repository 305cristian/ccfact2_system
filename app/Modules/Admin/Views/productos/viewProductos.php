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
                    <label for="selectName" class="col-form-label col-form-label-sm"><i class="fal fa-box-open"></i> Producto</label>
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
                    <label for="selectCode" class="col-form-label col-form-label-sm"><i class="fal fa-barcode-alt"></i> Código</label>
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
                    <vue-select class="border rounded" v-model="selectGrupo" :options="listaGruposFiltro" label="gr_nombre" :reduce="grupo => grupo.id" placeholder="Seleccione un grupo"></vue-select>

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
    var autocodigo = '<?php echo $autocodigo; ?>';
    var valorIrbpnr =<?= getImpuestoIrbpnr() ?>


    if (window.appProductos) {
        window.appProductos.unmount();
    }
    window.appProductos = Vue.createApp({
        components: {
            "vue-multiselect": window['vue-multiselect'].Multiselect,
            "vue-select": window['vue-select']
        },
        data() {
            return {
                url: siteUrl,

                //TODO: PERMISOS
                admin: admin,

                //TODO: VARIABLES
                estadoSave: true,
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
                    prodExistenciaMinima: '5',
                    prodExistenciaMaxima: '50',
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
                    prodEstado: true,
                    prodIrbpnrValor: '',
                    prodTieneIrbpnr: '0'

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
                formValidacion: [],

                modalInstance: null

            };
        },
        created() {
            this.tipoPrecioId = this.listaTiposPvp.map(ltpc => ltpc.id);
        },
        updated() {
            this.aplicaIce();
            this.aplicaIrbpnr();
        },
        mounted() {
            this.aplicaIce();
            this.aplicaIrbpnr();
            panelMain.style.display = "none";
            this.modalInstance = new bootstrap.Modal(this.$refs.modalProductos);
        },
        watch: {
            idGrupo(val) {
                if (!val) {
                    this.listaSubGrupos = [];
                    return;
                }

                const grupoId = val.id || null;

                if (!grupoId)
                    return;
                this.getSubgrupo(grupoId);
            },
            'newProducto.prodTipoProducto'(tipoProductoId) {
                this.sincronizarTipoProductoServicio(tipoProductoId);
            }
        },
        computed: {
            listaGruposFiltro() {
                return [{id: "-1", gr_nombre: "TODOS"}, ...this.listaGrupos];
            }
        },
        methods: {

            async crearMarca(marca) {

                if (this.listaMarcas.some(m => m.mrc_nombre.toLowerCase() === marca.mrc_nombre.toLowerCase())) {
                    sweet_msg_toast('warning', 'Esta marca ya existe');
                    await Vue.nextTick();
                    this.listaMarcas = this.listaMarcas.filter(m => m.id !== null);
                    this.newProducto.prodMarca = '';

                    return;
                }

                let url = this.url + '/admin/marcas/saveMarca';
                let datos = new FormData();
                datos.append('mrcNombre', marca.mrc_nombre);
                datos.append('mrcEstado', '1');

                let {data} = await axios.post(url, datos);
                if (data.status === 'success') {
                    const nuevaMarca = data.data;
                    this.listaMarcas.push(nuevaMarca);
                    this.newProducto.prodMarca = nuevaMarca;
                    sweet_msg_toast('success', 'Marca creado exitosamente');
                }

            },
            sincronizarTipoProductoServicio(tipoProductoId) {
                const tipoProducto = this.listaTipoProducto.find(
                        tipo => parseInt(tipo.id) === parseInt(tipoProductoId)
                );

                if (!tipoProducto) {
                    this.newProducto.prodIsServicio = false;
                    return;
                }

                this.newProducto.prodIsServicio = tipoProducto.tp_nombre.toUpperCase().includes('SERVICIO');
            },
            async crearGrupo(grupo) {

                if (this.listaGrupos.some(m => m.gr_nombre.toLowerCase() === grupo.gr_nombre.toLowerCase())) {
                    sweet_msg_toast('warning', 'Este grupo ya existe');
                    await Vue.nextTick();
                    this.listaGrupos = this.listaGrupos.filter(g => g.id !== null);
                    this.idGrupo = '';
                    return;
                }

                let url = this.url + '/admin/grupos/saveGrupo';

                let datos = new FormData();
                datos.append('grNombre', grupo.gr_nombre);
                datos.append('grEstado', '1');
                datos.append('grDescripcion', 'PRODUCTOS AL GRUPO ' + grupo.gr_nombre);
                datos.append('grIcon', 'far fa-box');

                let {data} = await axios.post(url, datos);
                if (data.status === 'success') {
                    const nuevoGrupo = data.data;
                    this.listaGrupos.push(nuevoGrupo);
                    this.idGrupo = nuevoGrupo;
                    sweet_msg_toast('success', 'Grupo creado exitosamente');
                }

            },

            async crearSubGrupo(subgrupo) {

                if (this.listaSubGrupos.some(m => m.sgr_nombre.toLowerCase() === subgrupo.sgr_nombre.toLowerCase())) {
                    sweet_msg_toast('warning', 'Este subgrupo ya existe');
                    await Vue.nextTick();
                    this.listaSubGrupos = this.listaSubGrupos.filter(sg => sg.id !== null);
                    this.newProducto.prodSubgrupo = '';
                    return;
                }

                let url = this.url + '/admin/grupos/saveSubGrupo';

                let datos = new FormData();
                datos.append('sgrNombre', subgrupo.sgr_nombre);
                datos.append('sgrGrupo', this.idGrupo.id);
                datos.append('sgrDetalle', 'PRODUCTOS AL SUBGRUPO ' + subgrupo.sgr_nombre);
                datos.append('sgrEstado', '1');

                try {
                    let {data} = await axios.post(url, datos);
                    if (data.status === 'success') {
                        const nuevoSubGrupo = data.data;
                        this.listaSubGrupos.push(nuevoSubGrupo);
                        this.newProducto.prodSubgrupo = nuevoSubGrupo;
                        sweet_msg_toast('success', 'SubGrupo creado exitosamente');
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                }
            },

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
            aplicaIrbpnr() {
                if (this.newProducto.prodTieneIrbpnr === '1') {
                    document.getElementById("selectImpIrbpnr").style.display = "block";
                    this.newProducto.prodIrbpnrValor = valorIrbpnr;
                } else {
                    this.newProducto.prodIrbpnrValor = "";
                    document.getElementById("selectImpIrbpnr").style.display = "none";
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
                    idGrupo: this.idGrupo ? this.idGrupo.id : idGrupo
                };
                try {
                    let response = await axios.post(this.url + '/admin/grupos/getSubgrupoByGrupo', datos);
                    if (response.data) {
                        this.listaSubGrupos = response.data;
                    } else {
                        this.listaSubGrupos = [];
                        sweet_msg_dialog('warning', 'No se encontraron subgrupos registradas');
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                }
            },
            async loadProducto(prod) {

                swalLoading('Cargando...', '');
                await this.getSubgrupo(prod.id_grupo);

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
//                    prodSubgrupo: prod.fk_subgrupo,
//                    prodMarca: prod.fk_marca,
                    prodTipoProducto: prod.fk_tipoproducto,

                    prodIvaPorcentajeId: prod.idImpuesto,
                    prodIvaPorcentaje: prod.prod_ivaporcentage,

                    prodIcePorcentaje: prod.prod_iceporcentage,
                    prodIcePorcentajeId: prod.idImpuestoIce,
                    prodTieneICE: prod.prod_tiene_ice,
                    
                    prodTieneIrbpnr: prod.prod_tiene_irbpnr,

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
                await this.getPreciosProducto(prod.id);

                this.newProducto.prodMarca = this.listaMarcas.find(val => val.id === prod.fk_marca);
                this.newProducto.prodSubgrupo = this.listaSubGrupos.find(val => val.id === prod.fk_subgrupo);
                this.idGrupo = this.listaGrupos.find(val => val.id === prod.id_grupo);

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
                            let ivaActual = this.listaImpuestosTarifa.find(a => parseInt(a.id) === parseInt(this.newProducto.prodIvaPorcentajeId)).impt_porcentage;
                            let iva = (ivaActual / 100) + 1;
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

                if (this.idEdit !== '') {
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
                        this.consultarAutoCodigo();//Este invoca al autocodigo para un nuevo producto
                        this.modalInstance.hide();
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

                const impuesto = this.listaImpuestosTarifa.find(
                        a => parseInt(a.id) === parseInt(this.newProducto.prodIvaPorcentajeId)
                );

                if (!impuesto) {
                    return;
                }

                const precio = this.price[index] ?? 0;
                const ivaFactor = (parseFloat(impuesto.impt_porcentage) / 100) + 1;
                const aplicaImpuesto = impuesto.impt_porcentage > '0';

                const priceSinIva = aplicaImpuesto ? precio / ivaFactor : precio;
                this.tipoPrecioVal[index] = parseFloat(priceSinIva).toFixed(4);

//                const input = document.getElementById("prodPriceSinIva" + index);
//                if (input) {
//                    input.value = parseFloat(priceSinIva).toFixed(4);
//                }


//              
            },
            desglosarIva2() {
                const impuesto = this.listaImpuestosTarifa.find(
                        a => parseInt(a.id) === parseInt(this.newProducto.prodIvaPorcentajeId)
                );

                if (!impuesto) {
                    return; // seguridad
                }

                const ivaFactor = (parseFloat(impuesto.impt_porcentage) / 100) + 1;
                const aplicaImpuesto = impuesto.impt_porcentage > '0';

                this.price.forEach((val, index) => {
                    const priceSinIva = aplicaImpuesto ? val / ivaFactor : val;
                    this.tipoPrecioVal[index] = parseFloat(priceSinIva).toFixed(4);
//                    const input = document.getElementById("prodPriceSinIva" + index);
//                    if (input) {
//                        input.value = parseFloat(priceSinIva).toFixed(4);
//                    }

                });
            },
            onRemove() {
                this.keyProducto = [];
                this.listaSearchProductos = [];
            },
            clear() {
                this.newProducto = {
                    prodNombre: '',
                    prodCodigo: autocodigo,
                    prodCodigoBarras: '',
                    prodCodigoBarras2: '',
                    prodCodigoBarras3: '',
                    prodExistenciaMinima: '5',
                    prodExistenciaMaxima: '50',
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
//            formData(obj) {
//                var formData = new FormData();
//                for (var key in obj) {
//                    formData.append(key, obj[key]);
//                }
//                return formData;
//            },
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
            },
            zfill(num) {
                return zFill(num, 3);
            }
        }
    });
    window.appProductos.mount('#app');

</script>
