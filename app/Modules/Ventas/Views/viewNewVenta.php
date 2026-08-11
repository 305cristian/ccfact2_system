<!DOCTYPE html>
<!--
/**
 * Description of viewNewVenta
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 9 ago 2026
 * @time 12:49:57 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<style>
    .venta-precio-select {
        width: 95px;
        min-width: 95px;
        text-align: left;
    }

    .venta-precio-select .vs__dropdown-toggle {
        min-height: 30px;
        padding: 0 4px;
        background: #fff;
    }

    .venta-precio-select .vs__selected {
        margin: 2px 0 0 4px;
        font-size: 12px;
        font-weight: 600;
    }

    .venta-precio-select .vs__actions {
        padding: 2px 4px 0 2px;
    }

    .venta-precio-select .vs__dropdown-menu {
        min-width: 180px;
        text-align: left;
    }


    .multiselect__tags {
        border-radius: 0px 0px 0px 0px
    }

</style>
<div id="appVenta" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-system fw-bold">
                    <i class="far fa-cash-register"></i> VENTAS / Nueva Venta
                </h6>            
            </div>
        </div>

        <div class="card-body">
            <fieldset class="border rounded p-3 mb-3">
                <legend>Información del comprobante</legend>
                <div class="row g-3">
                    <div class="col-12 col-md-6 col-lg-3 form-group">
                        <div class="d-flex justify-content-between align-items-center border rounded-2">
                            <span class="input-group-text bg-cris-system text-white">
                                <i class="fas fa-cash-register me-2"></i>P. Emisión
                            </span>
                            <vue-select
                                append-to-body
                                class="flex-grow-1"
                                v-model="formVenta.venPuntoEmision"
                                :options="puntosEmisionComprobante"
                                :disabled="tieneItemsCart"
                                label="punto_label"
                                placeholder="Seleccione punto"
                                @option:selected="cargarPuntoEmisionVenta">
                            </vue-select>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3 form-group">
                        <div class="input-group border rounded-2">
                            <span class="input-group-text bg-cris-system text-white">
                                <i class="far fa-file-invoice me-2"></i> Comprobante
                            </span>
                            <vue-select
                                class="flex-grow-1"
                                v-model="formVenta.venTipoComprobante"
                                :options="listaTiposComprobantes"
                                :disabled="tieneItemsCart"
                                label="comp_nombre"
                                placeholder="Seleccione comprobante">
                                <template #option="{ comp_codigo, comp_nombre }">
                                    {{ comp_codigo }} - {{ comp_nombre }}
                                </template>
                                <template #selected-option="{ comp_codigo, comp_nombre }">
                                    {{ comp_codigo }} - {{ comp_nombre }}
                                </template>
                            </vue-select>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4 form-group">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system text-white">
                                <i class="fas fa-hashtag me-2"></i>N° Comprobante
                            </span>
                            <input v-model="formVenta.venNumeroEstablecimiento" class="form-control" placeholder="001" maxlength="3" readonly>
                            <input v-model="formVenta.venNumeroEmision" class="form-control" placeholder="001" maxlength="3" readonly>
                            <input v-model="formVenta.venNumeroComprobante" class="form-control" placeholder="000000001" readonly>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-2">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system text-white">
                                <i class="far fa-calendar-alt me-2"></i>F. Emisión
                            </span>
                            <input v-model="formVenta.venFechaEmision" type="date" class="form-control">
                        </div>
                    </div>

                </div>
            </fieldset>

            <fieldset class="border rounded p-3 mb-3">
                <legend>Información general</legend>
                <div class="row g-3">
                    <div class="col-12 col-md-6 col-lg-6 form-group-custom">
                        <div class="d-flex align-items-center">
                            <span class="input-group-text bg-cris-system text-white">
                                <i class="fas fa-user me-2"></i>Cliente
                            </span>
                            <div class="flex-grow-1 position-relative">
                                <vue-multiselect
                                    v-model="formVenta.venCliente"
                                    placeholder="Buscar cliente..."
                                    label="clie_razon_social"
                                    track-by="clie_dni"
                                    :options="listaSearchClientes"
                                    :searchable="true"
                                    :internal-search="false"
                                    :options-limit="10"
                                    :show-no-results="false"
                                    :show-no-options="false"
                                    @remove="limpiarClienteSeleccionado"
                                    @select="seleccionarCliente"
                                    @search-change="searchClientes">
                                    <template #option="{ option }">
                                        <span class="small">
                                            {{ option.clie_razon_social }} - <strong>{{ option.clie_dni }}</strong>
                                        </span>
                                    </template>
                                </vue-multiselect>
                                <div v-if="mostrarCrearCliente" class="position-absolute start-0 end-0 bg-white border rounded-bottom shadow-sm p-2 text-center" style="z-index: 20;">
                                    <span class="d-block text-muted mb-2">Cliente no encontrado</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary" @mousedown.prevent.stop="abrirGestionClientes">
                                        <i class="fas fa-user-plus me-1"></i> Crear cliente
                                    </button>
                                </div>
                            </div>
                            <button
                                type="button"
                                class="input-group-text bg-system text-white px-3"
                                :disabled="!clienteDetalle"
                                title="Ver datos del cliente"
                                style="font-size: 0;"
                                @click="togglePanelCliente">
                                <i v-if="clienteDetalle" class="fas fa-user me-2" style="font-size: 14px;"></i>
                                <i class="fas" style="font-size: 14px;" :class="clientePanelVisible ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 form-group">
                        <div class="input-group border rounded-2">
                            <span class="input-group-text bg-cris-system text-white">
                                <i class="fas fa-project-diagram me-2"></i>Centro de costo
                            </span>
                            <vue-select
                                class="flex-grow-1"
                                v-model="formVenta.venCentroCosto"
                                :options="listaCentroCostos"
                                label="cc_nombre"
                                placeholder="Seleccione centro">
                            </vue-select>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-2">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system text-white">
                                <i class="fas fa-toggle-on me-2"></i>Estado
                            </span>
                            <select v-model="formVenta.venEstado" class="form-select">
                                <option value="BORRADOR">BORRADOR</option>
                                <option value="ARCHIVADO">ARCHIVADO</option>
                            </select>
                        </div>
                    </div>

                    <!--vista cliente-->
                    <?php echo view('Modules\Ventas\Views\viewInfoClientes') ?>

                    <div class="col-12 col-md-6 col-lg-5 form-group">
                        <div class="d-flex justify-content-between align-items-center border rounded-2">
                            <span class="input-group-text bg-cris-system text-white">
                                <i class="fas fa-tags me-2"></i>Tipo de venta
                            </span>
                            <vue-select
                                class="flex-grow-1"
                                v-model="formVenta.venTipoVenta"
                                :options="listaTipoVenta"
                                label="tv_nombre"
                                placeholder="Seleccione tipo de venta">
                                <template #option="{ tv_codigo, tv_nombre }">
                                    {{ tv_codigo }} - {{ tv_nombre }}
                                </template>
                                <template #selected-option="{ tv_codigo, tv_nombre }">
                                    {{ tv_codigo }} - {{ tv_nombre }}
                                </template>
                            </vue-select>
                        </div>
                    </div>



                    <div class="col-12 col-md-10 col-lg-7">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system text-white">
                                <i class="fas fa-comments me-2"></i>Observaciones
                            </span>
                            <input v-model="formVenta.venObservacion" class="form-control" placeholder="Ej. Venta de productos">
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset class="border rounded p-3 mb-3">
                <legend>Búsqueda de productos</legend>
                <div class="row g-2">
                    <div class="col-md-7 form-group-custom">
                        <div class="d-flex align-items-center">
                            <vue-multiselect
                                v-model="productoSeleccionado"
                                placeholder="Buscar producto..."
                                label="prod_nombre"
                                track-by="id"
                                :options="listaSearchProductos"
                                :searchable="true"
                                :options-limit="10"
                                @search-change="searchProductos"
                                @select="insertProductCart($event)">

                                <template #option="{ option }">
                                    <div class="producto-option-row">
                                        <div class="row g-2 align-items-center w-100">
                                            <div class="col-auto">
                                                <span class="badge bg-primary">{{ option.codigos }}</span>
                                            </div>
                                            <div class="col">
                                                <span class="fw-bold text-dark">{{ option.prod_nombre }}  |</span>
                                                <small class="text-white ms-2">Stock: {{ option.stb_stock || 0 }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </vue-multiselect>
                            <button class="btn btn-warning" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group">
<!--                            <span class="input-group-text bg-cris-system text-white">
                                <i class="fas fa-barcode me-2"></i>Código
                            </span>-->
                            <input
                                v-model="codeSearch"
                                @keyup.enter="insertProductCode($event)"
                                class="form-control"
                                placeholder="Código / Código barras">
                            <button class="btn btn-warning" type="button">
                                <i class="fas fa-barcode"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </fieldset>

            <?php echo view('Modules\Ventas\Views\viewCart') ?>
        </div>
    </div>
</div>

<script>
    var fechaActual = DateTime.now().toFormat('yyyy-MM-dd');
    var listaClientes = <?= json_encode($listaClientes ?? []) ?>;
    var listaBodegas = <?= json_encode($listaBodegas ?? []) ?>;
    var listaCentroCostos = <?= json_encode($listaCentroCostos ?? []) ?>;
    var listaTiposComprobantes = <?= json_encode($listaTiposComprobantes ?? []) ?>;
    var listaPuntosEmision = <?= json_encode($listaPuntosEmision ?? []) ?>;
    var listaTipoVenta = <?= json_encode($listaTipoVenta ?? []) ?>;
    var listaFormasPagoSri = <?= json_encode($listaFormasPagoSri ?? []) ?>;
    var bodegaMainUsuario = <?= json_encode($bodegaMainUsuario ?? null) ?>;
    var permitirDuplicados = <?= getSettings('PERMITIR_ITEMS_DUPLICADOS'); ?>;

    var permitirCambioPrecio = <?= !empty($permitirCambioPrecio) ? $permitirCambioPrecio : 0; ?>;
    
     var dataVenta = <?= json_encode($dataVenta??null); ?>;


    var appVenta = Vue.createApp({
        components: {
            "vue-multiselect": window['vue-multiselect'].Multiselect,
            'vue-select': window["vue-select"]
        },
        data() {
            return {
                url: siteUrl,

                //PERMISOS
                permitirCambioPrecio: permitirCambioPrecio,

                listaClientes: listaClientes,
                listaBodegas: listaBodegas,
                listaCentroCostos: listaCentroCostos,
                listaTiposComprobantes: listaTiposComprobantes,
                listaPuntosEmision: listaPuntosEmision,
                listaTipoVenta: listaTipoVenta,
                listaFormasPagoSri: listaFormasPagoSri,
                bodegaMainUsuario: bodegaMainUsuario,
                listaSearchClientes: [],
                clienteSearch: '',
                searchTimeoutCliente: null,
                loadingSearchClientes: false,
                clienteDetalle: null,
                clientePanelVisible: false,
                clienteEditando: false,
                loadingCliente: false,
                ciRucClienteAux: '',
                formCliente: {
                    clieCiruc: '',
                    clieNombres: '',
                    clieApellidos: '',
                    clieRazonSocial: '',
                    clieSexo: '',
                    clieGenero: '',
                    clieTelefono: '',
                    clieCelular: '',
                    clieEmail: '',
                    clieDireccion: '',
                    clieParroquia: '',
                    clieTipoCliente: '',
                    clieTipoDocumento: '',
                    clieDiasCredito: '',
                    clieCupoCredito: '',
                    clieEstado: true
                },
                productoSeleccionado: null,
                listaSearchProductos: [],
                listaCartData: [],
                codeSearch: '',
                loading: false,
                emptyCar: true,
                ivaPrdeterminado: 0,
                basesImpuesto: [],
                totales: {
                    totalSubtotalBruto: 0,
                    totalDescuentoItems: 0,
                    totalDescuentoGlobal: 0,
                    totalSubtotalNeto: 0,
                    totalIce: 0,
                    totalIrbpnr: 0,
                    tarifCeroNeto: 0,
                    tarifExcentoNeto: 0,
                    tarifNoObjetoNeto: 0,
                    totalGeneral: 0
                },
                global: {
                    recargo: 0,
                    serviciosAdc: 0
                },
                formVenta: {
                    venTipoComprobante: null,
                    venPuntoEmision: null,
                    venNumeroEstablecimiento: '',
                    venNumeroEmision: '',
                    venNumeroComprobante: '',
                    venFechaEmision: fechaActual,
                    venCliente: null,
                    venBodega: null,
                    venCentroCosto: null,
                    venTipoVenta: null,
                    venEstado: 'BORRADOR',
                    venPermitirDuplicados: permitirDuplicados,
                    venObservacion: ''
                }
            };
        },
        mounted() {
            this.formVenta.venTipoComprobante = this.listaTiposComprobantes.find(item => String(item.comp_codigo) === '01') ?? null;
            this.formVenta.venTipoVenta = this.listaTipoVenta.find(item => String(item.id) === '1') ?? null;
            this.formVenta.venCentroCosto = this.listaCentroCostos.find(item => String(item.cc_facturacion_elect) === '1') ?? null;
            this.showDetailCart();
        },
        computed: {
            colLotes() {
                return this.listaCartData.some(item => Number(item.tieneLote) === 1);
            },
            tieneItemsCart() {
                return Number(this.listaCartData.length || 0) > 0;
            },
            mostrarCrearCliente() {
                return this.clienteSearch.length >= 2
                        && !this.loadingSearchClientes
                        && this.listaSearchClientes.length === 0;
            },
            puntosEmisionComprobante() {
                const codigoComprobante = this.formVenta.venTipoComprobante?.comp_codigo;

                if (!codigoComprobante) {
                    return [];
                }

                return this.listaPuntosEmision.filter(punto => String(punto.fk_comprobante) === String(codigoComprobante));
            },
            basesImpuestoVista() {
                if (!this.basesImpuesto) {
                    return [];
                }

                return this.basesImpuesto.filter(tax => Number(tax.porcentaje || 0) > 0);
            }
        },
        watch: {
            'formVenta.venTipoComprobante'() {
                this.aplicarPuntoEmisionVenta();
            }
        },
        methods: {

            aplicarPuntoEmisionVenta() {
                if (this.tieneItemsCart) {
                    sweet_msg_toast('warning', 'Existen productos cargados al carrito. No puede cambiar el comprobante ni el punto de emisión.');
                    return;
                }

                const puntos = this.puntosEmisionComprobante;

                this.limpiarPuntoEmisionVenta();

                if (!puntos.length) {
                    return;
                }

                const puntoBodegaMain = puntos.find(punto => Number(punto.pv_fk_bodega) === Number(this.bodegaMainUsuario));

                if (puntoBodegaMain) {
                    this.cargarPuntoEmisionVenta(puntoBodegaMain);
                    return;
                }

                if (puntos.length === 1) {
                    this.cargarPuntoEmisionVenta(puntos[0]);
                }
            },
            cargarPuntoEmisionVenta(punto) {
                if (this.tieneItemsCart) {
                    sweet_msg_toast('warning', 'Existen productos cargados al carrito. No puede cambiar el punto de emisión.');
                    return;
                }

                if (!punto) {
                    this.limpiarPuntoEmisionVenta();
                    return;
                }

                if (Number(punto.pv_sec_actual || 0) > Number(punto.pv_sec_final || 0)) {
                    sweet_msg_toast('warning', 'El punto de emisión seleccionado ya no tiene secuencial disponible.');
                    this.limpiarPuntoEmisionVenta();
                    return;
                }

                this.formVenta.venPuntoEmision = punto;
                this.formVenta.venNumeroEstablecimiento = punto.pv_establecimiento || '';
                this.formVenta.venNumeroEmision = punto.pv_emision || '';
                this.formVenta.venNumeroComprobante = this.zFill(punto.pv_sec_actual, 9);
                this.formVenta.venBodega = this.listaBodegas.find(bodega => Number(bodega.id) === Number(punto.pv_fk_bodega)) ?? null;
            },
            limpiarPuntoEmisionVenta() {
                this.formVenta.venPuntoEmision = null;
                this.formVenta.venNumeroEstablecimiento = '';
                this.formVenta.venNumeroEmision = '';
                this.formVenta.venNumeroComprobante = '';
                this.formVenta.venBodega = null;
            },

            searchClientes(search) {
                this.clienteSearch = (search || '').trim();
                clearTimeout(this.searchTimeoutCliente);

                if (!search || search.length < 2) {
                    this.listaSearchClientes = [];
                    this.loadingSearchClientes = false;
                    return;
                }

                this.loadingSearchClientes = true;
                this.searchTimeoutCliente = setTimeout(() => {
                    this.buscarClientes();
                }, 500);
            },
            async buscarClientes() {
                try {
                    const {data} = await axios.post(this.url + '/admin/clientes/searchClientes', {
                        dataSerach: this.clienteSearch
                    });

                    this.listaSearchClientes = data !== false ? data : [];
                } catch (e) {
                    this.listaSearchClientes = [];
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loadingSearchClientes = false;
                }
            },
            async seleccionarCliente(cliente) {
                if (!cliente?.clie_dni) {
                    this.limpiarClienteSeleccionado();
                    return;
                }

                try {
                    this.loadingCliente = true;
                    const {data} = await axios.post(this.url + '/admin/clientes/getClientes', {
                        ciruc: cliente.clie_dni
                    });

                    if (data.status !== 'success' || !data.data?.length) {
                        sweet_msg_toast('warning', 'No se pudo cargar la información del cliente.');
                        return;
                    }

                    this.setClienteDetalle(data.data[0]);
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loadingCliente = false;
                }
            },
            setClienteDetalle(cliente) {
                this.clienteSearch = '';
                this.clienteDetalle = cliente;
                this.formVenta.venCliente = {
                    id: cliente.id,
                    clie_dni: cliente.clie_dni,
                    clie_razon_social: cliente.clie_razon_social
                };
                this.ciRucClienteAux = cliente.clie_dni;
                this.clientePanelVisible = false;
                this.clienteEditando = false;

                this.formCliente = {
                    clieCiruc: cliente.clie_dni || '',
                    clieNombres: cliente.clie_nombres || '',
                    clieApellidos: cliente.clie_apellidos || '',
                    clieRazonSocial: cliente.clie_razon_social || '',
                    clieSexo: cliente.clie_sexo || '',
                    clieGenero: cliente.clie_genero || '',
                    clieTelefono: cliente.clie_telefono || '',
                    clieCelular: cliente.clie_celular || '',
                    clieEmail: cliente.clie_email || '',
                    clieDireccion: cliente.clie_direccion || '',
                    clieParroquia: cliente.fk_parroquia || '',
                    clieTipoCliente: cliente.fk_tipo_sujeto || '',
                    clieTipoDocumento: cliente.fk_tipo_documento || '',
                    clieDiasCredito: cliente.clie_dias_credito || '',
                    clieCupoCredito: cliente.clie_cupo_credito || '',
                    clieEstado: String(cliente.clie_estado) === '1'
                };
            },
            limpiarClienteSeleccionado() {
                this.formVenta.venCliente = null;
                this.listaSearchClientes = [];
                this.clienteSearch = '';
                this.clienteDetalle = null;
                this.clientePanelVisible = false;
                this.clienteEditando = false;
                this.ciRucClienteAux = '';
            },
            togglePanelCliente() {
                if (!this.clienteDetalle) {
                    sweet_msg_toast('warning', 'Seleccione un cliente primero.');
                    return;
                }

                this.clientePanelVisible = !this.clientePanelVisible;

                if (!this.clientePanelVisible) {
                    this.clienteEditando = false;
                }
            },
            async actualizarClienteVenta() {
                if (!this.clienteDetalle?.id) {
                    sweet_msg_toast('warning', 'Seleccione un cliente para actualizar.');
                    return;
                }

                const datos = this.formData(this.formCliente);
                datos.append('idClie', this.clienteDetalle.id);
                datos.append('ciRucAux', this.ciRucClienteAux);

                try {
                    this.loadingCliente = true;
                    const {data} = await axios.post(this.url + '/admin/clientes/updateCliente', datos);

                    if (data.status === 'success') {
                        sweet_msg_toast('success', 'Cliente actualizado correctamente.');
                        await this.seleccionarCliente({clie_dni: this.formCliente.clieCiruc});
                        this.clienteEditando = false;
                    } else if (data.status === 'vacio') {
                        const errores = Object.values(data.msg || {}).filter(Boolean).join('<br>');
                        sweet_msg_dialog('warning', errores || 'Revise los datos del cliente.');
                    } else {
                        sweet_msg_dialog(data.status || 'warning', data.msg || 'No se pudo actualizar el cliente.');
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loadingCliente = false;
                }
            },
            abrirGestionClientes() {
                window.open(this.url + '/admin/clientes/managamentClientes', '_blank');
            },

            async searchProductos(search) {
                if (!this.formVenta.venBodega) {
                    this.listaSearchProductos = [];
                    sweet_msg_toast('warning', 'Debe seleccionar una bodega');
                    return;
                }

                if (!search || search.length < 2) {
                    this.listaSearchProductos = [];
                    return;
                }

                try {
                    const {data} = await axios.post(this.url + '/comun/productos/searchProductosStock', {
                        dataSerach: search,
                        bodegaId: this.formVenta.venBodega?.id ?? null
                    });

                    this.listaSearchProductos = data !== false ? data : [];
                } catch (e) {
                    this.listaSearchProductos = [];
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                }
            },
            async insertProductCode(evt) {
                if (evt.target.value === '') {
                    sweet_msg_toast('warning', 'Por favor digite un código');
                    return false;
                }

                await this.insertProductCart({id: evt.target.value});
                this.codeSearch = '';
            },
            async insertProductCart(item) {
                if (!this.formVenta.venBodega) {
                    sweet_msg_toast('warning', 'Seleccione una bodega antes de agregar productos.');
                    return false;
                }

                const datos = {
                    id: item.id,
                    qty: 1,
                    permitirDuplicados: this.formVenta.venPermitirDuplicados,
                    bodegaId: this.formVenta.venBodega.id
                };

                try {
                    this.loading = true;
                    const {data} = await axios.post(this.url + '/ventas/insertProduct', datos);
                    sweet_msg_toast(data.status, data.msg);
                    this.productoSeleccionado = null;
                    await this.showDetailCart();
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loading = false;
                }
            },
            async updateProductCart(item) {
                if (Number(item.qty || 0) <= 0) {
                    item.qty = 1;
                    sweet_msg_toast('warning', 'La cantidad debe ser mayor a cero');
                    return false;
                }

                if (!item.idBodega) {
                    sweet_msg_toast('warning', 'Seleccione una bodega antes de actualizar el producto.');
                    return false;
                }

                if (Number(item.tieneLote || 0) === 1 && !item.idLote) {
                    sweet_msg_toast('warning', `El producto ${item.name} tiene control de lotes, seleccione uno por favor.`);
                    return false;
                }

                try {
                    this.loading = true;

                    let datos = {...item};
                    datos.ventaId = this.isEdit ? dataVenta.id : '';

                    const {data} = await axios.post(this.url + '/ventas/updateProduct', datos);
                    sweet_msg_toast(data.status, data.msg);
                    await this.showDetailCart();
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loading = false;
                }
            },
            async cambiarPrecioItem(item) {
                item.price = Number(item.precioSeleccionado?.valor || 0);
                await this.updateProductCart(item);
            },
            changeTipoDescuento(item, tipoDescuento) {
                item.tipoDescuento = tipoDescuento;
                item.descuento = 0;
            },
            async deleteProduct(rowId) {
                try {
                    this.loading = true;
                    const {data} = await axios.get(this.url + '/ventas/deleteProduct/' + rowId);
                    sweet_msg_toast(data.status, data.msg);
                    await this.showDetailCart();
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loading = false;
                }
            },
            async showDetailCart() {
                try {
                    const {data} = await axios.post(this.url + '/ventas/showDetailCart');

                    this.listaCartData = data.cartContent || [];
                    this.totales = {
                        totalArticles: data.totalArticles,
                        totalItems: data.totalItems,
                        totalSubtotalBruto: data.totalSubtotalBruto,
                        totalBienes: data.totalBienes,
                        totalServicios: data.totalServicios,
                        tarifCeroNeto: data.tarifCeroNeto,
                        tarifIvaNeto: data.tarifIvaNeto,
                        tarifNoObjetoNeto: data.tarifNoObjetoNeto,
                        tarifExcentoNeto: data.tarifExcentoNeto,
                        totalIva: data.totalIva,
                        totalIce: data.totalIce,
                        totalIrbpnr: data.totalIrbpnr,
                        totalDescuentoGlobal: data.totalDescuentoGlobal,
                        totalDescuentoItems: data.totalDescuentoItems,
                        totalSubtotalNeto: data.totalSubtotalNeto,
                        totalGeneral: data.totalGeneral
                    };

                    this.basesImpuesto = data.basesImpuesto || [];
                    this.global = {
                        recargo: data.totalRecargo,
                        serviciosAdc: data.totalServiciosAdc
                    };

                    this.emptyCar = !(Number(data.totalArticles || 0) > 0);
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                }
            },
            zFill(value, width) {
                return String(value || '').padStart(width, '0');
            },
            formatToUSD(value) {
                return new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD'
                }).format(Number(value || 0));
            },

            formData(obj) {
                const formData = new FormData();
                Object.keys(obj).forEach(key => formData.append(key, obj[key]));
                return formData;
            },
        }
    });
    window.appVenta.use(AllDirectives);
    appVenta.mount('#appVenta');
</script>
