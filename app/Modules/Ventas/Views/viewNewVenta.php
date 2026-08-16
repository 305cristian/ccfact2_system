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

<link rel="stylesheet" href="<?php echo base_url(); ?>/resources/css/styleModalPosition.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>/resources/css/modules/ventas/styles.css">

<div id="appVenta" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-system fw-bold">
                    <i class="far fa-cash-register"></i> VENTAS / {{ isEdit ? 'Actualizar Venta' : 'Nueva Venta' }}
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
            <?php echo view('Modules\Ventas\Views\viewAnexoATS') ?>

            <div v-if="!emptyCar" class="d-flex justify-content-end gap-2 mt-4">
                <button
                    type="button"
                    class="btngr btn-danger-gradiant"
                    style="min-width: 150px;"
                    :disabled="loadingProcess"
                    @click="cancelarVenta">
                    <i class="fas fa-times-circle me-2"></i>Cancelar
                </button>

                <button
                    type="button"
                    class="btngr btn-primary-gradiant"
                    :disabled="emptyCar"
                    style="min-width: 150px;"
                    :disabled="loadingProcess"
                    @click="abrirModalFinalizar">
                    <span v-if="loadingProcess" class="spinner-border spinner-border-sm me-2"></span>
                    <i v-else class="fas fa-save me-2"></i>{{ isEdit ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>
        </div>
    </div>

    <!--MODAL FINALIZAR VENTA-->
    <?php echo view('\Modules\Ventas\Views\viewPagos') ?>
    <!--CLOSE MODAL FINALIZAR VENTA-->

    <?php echo view('\Modules\Ventas\Views\reportes\viewModalReport') ?>
</div>

<script>
    var fechaActual = DateTime.now().toFormat('yyyy-MM-dd');
    var listaClientes = <?= json_encode($listaClientes ?? []) ?>;
    var listaBodegas = <?= json_encode($listaBodegas ?? []) ?>;
    var listaCentroCostos = <?= json_encode($listaCentroCostos ?? []) ?>;
    var listaTiposComprobantes = <?= json_encode($listaTiposComprobantes ?? []) ?>;
    var listaPuntosEmision = <?= json_encode($listaPuntosEmision ?? []) ?>;
    var listaTipoVenta = <?= json_encode($listaTipoVenta ?? []) ?>;
    var listaFormasPago = <?= json_encode(!empty($listaFormasPago) ? $listaFormasPago : []) ?>;
    var listaFormasPagoSri = <?= json_encode($listaFormasPagoSri ?? []) ?>;
    var listaCuentasContables = <?= json_encode(!empty($listaCuentasContables) ? $listaCuentasContables : []) ?>;
    var listaBancos = <?= json_encode(!empty($listaBancos) ? $listaBancos : []) ?>;
    var bodegaMainUsuario = <?= json_encode($bodegaMainUsuario ?? null) ?>;
    var permitirDuplicados = <?= getSettings('PERMITIR_ITEMS_DUPLICADOS'); ?>;
    var valorMaximoATSSRI = <?= getSettings('VALOR_MAXIMO_ANEXO_ATS_SRI') ?>;

    var permitirCambioPrecio = <?= !empty($permitirCambioPrecio) ? $permitirCambioPrecio : 0; ?>;

    var dataVenta = <?= json_encode($dataVenta ?? null); ?>;
    var dataCliente = <?= json_encode($dataCliente ?? null); ?>;


    var appVenta = Vue.createApp({
        components: {
            "vue-multiselect": window['vue-multiselect'].Multiselect,
            'vue-select': window["vue-select"]
        },
        data() {
            return {
                url: siteUrl,
                isEdit: !!dataVenta,
                idVenta: dataVenta?.id ?? null,
                cargandoVentaEdit: false,

                //PERMISOS
                permitirCambioPrecio: permitirCambioPrecio,

                listaClientes: listaClientes,
                listaBodegas: listaBodegas,
                listaCentroCostos: listaCentroCostos,
                listaTiposComprobantes: listaTiposComprobantes,
                listaPuntosEmision: listaPuntosEmision,
                listaTipoVenta: listaTipoVenta,
                listaFormasPago: listaFormasPago,
                listaFormasPagoSri: listaFormasPagoSri,
                listaCuentasContables: listaCuentasContables,
                listaBancosSimulados: listaBancos,
                valorMaximoATSSRI: valorMaximoATSSRI,
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
                loadingProcess: false,
                idVentaReporte: null,
                secuencialVenta: null,
                cargandoDetalle: false,
                detalleHtml: '',
                modalTitulo: 'Detalle de Venta',
                mostrarBotonesReporte: true,
                modalReporteInstance: null,
                modalFinalizarInstance: null,
                erroresPago: {},
                ats: {
                    formaPago: []
                },
                pagos: {
                    tipoPago: 'CONTADO',
                    formaPago: null,
                    cuentaContablePago: null,
                    valorRecibido: 0,
                    fechaCobro: fechaActual,
                    referencia: '',
                    nota: '',
                    banco: null,
                    numeroTransferencia: '',
                    fechaTransferencia: '',
                    numeroCheque: '',
                    fechaCheque: '',
                    marcaTarjeta: '',
                    loteTarjeta: '',
                    autorizacionTarjeta: '',
                    ultimosDigitos: '',
                    fechaVoucher: '',
                    cuotas: 1,
                    dias: 0,
                    fechaVenceCredito: fechaActual,
                    listaCuotas: []
                },
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
                    venEstado: 'ARCHIVADO',
                    venPermitirDuplicados: permitirDuplicados,
                    venObservacion: ''
                }
            };
        },
        mounted() {
            this.modalReporteInstance = new bootstrap.Modal(this.$refs.modalReport);
            this.modalFinalizarInstance = new bootstrap.Modal(this.$refs.modalFinalizarVenta);

            if (this.isEdit) {
                this.cargarDatosVenta();
            } else {
                this.formVenta.venTipoComprobante = this.listaTiposComprobantes.find(item => String(item.comp_codigo) === '01') ?? null;
                this.formVenta.venTipoVenta = this.listaTipoVenta.find(item => String(item.id) === '1') ?? null;
                this.formVenta.venCentroCosto = this.listaCentroCostos.find(item => String(item.cc_facturacion_elect) === '1') ?? null;
            }

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
            },
            cambioVenta() {
                const cambio = Number(this.pagos.valorRecibido || 0) - Number(this.totales.totalGeneral || 0);
                return cambio > 0 ? cambio : 0;
            },
            requiereFormaPagoAts() {
                return this.formVenta.venEstado === 'ARCHIVADO'
                        && Number(this.totales.totalGeneral || 0) >= Number(this.valorMaximoATSSRI || 0);
            },
            listaCuentasFormaPago() {
                if (!this.pagos.formaPago) {
                    return [];
                }

                let codigo = '';

                switch (this.pagos.formaPago.cod) {
                    case '01':
                        codigo = '1.01.01';
                        break;
                    case '02':
                    case '03':
                    case '04':
                        codigo = '1.01.02';
                        break;
                    default:
                        codigo = '';
                        break;
                }

                return this.listaCuentasContables.filter(cuenta => String(cuenta.ctad_codigo || '').startsWith(codigo));
            }
        },
        watch: {
            'formVenta.venTipoComprobante'() {
                if (this.cargandoVentaEdit) {
                    return;
                }

                this.aplicarPuntoEmisionVenta();
            }
        },
        methods: {

            abrirModalFinalizar() {
                if (this.formVenta.venEstado === 'BORRADOR') {
                    this.guardarVenta();
                    return;
                }

                if (!this.clienteDetalle) {
                    sweet_msg_dialog('warning', 'El campo Cliente esta vacio, campo obligatorio.');
                    return;
                }

                this.resetPagoDetalle();
                this.pagos.valorRecibido = Number(this.totales.totalGeneral || 0);
                this.modalFinalizarInstance.show();
            },
            resetPagoDetalle() {
                this.erroresPago = {};
                this.pagos.formaPago = null;
                this.pagos.cuentaContablePago = null;
                this.pagos.valorRecibido = this.pagos.tipoPago === 'CONTADO' ? Number(this.totales.totalGeneral || 0) : 0;
                this.pagos.fechaCobro = this.formVenta.venFechaEmision || fechaActual;
                this.changeFormaPago();
                this.pagos.cuotas = 1;
                this.pagos.dias = Number(this.clienteDetalle?.clie_dias_credito || 0);
                this.pagos.fechaVenceCredito = this.sumarDiasFecha(this.formVenta.venFechaEmision || fechaActual, this.pagos.dias);
                this.pagos.listaCuotas = [];
            },
            changeFormaPago() {
                this.pagos.nota = '';
                this.pagos.referencia = '';
                this.pagos.cuentaContablePago = null;
                this.pagos.banco = null;
                this.pagos.numeroTransferencia = '';
                this.pagos.fechaTransferencia = '';
                this.pagos.numeroCheque = '';
                this.pagos.fechaCheque = '';
                this.pagos.marcaTarjeta = '';
                this.pagos.loteTarjeta = '';
                this.pagos.autorizacionTarjeta = '';
                this.pagos.ultimosDigitos = '';
                this.pagos.fechaVoucher = '';

                this.$nextTick(() => {
                    this.pagos.cuentaContablePago = this.listaCuentasFormaPago[0] ?? null;
                });
            },
            calcularFechaCreditoVenta() {
                this.pagos.fechaVenceCredito = this.sumarDiasFecha(this.formVenta.venFechaEmision || fechaActual, Number(this.pagos.dias || 0));
            },
            generarCuotasVenta() {
                this.erroresPago = {};

                const cuotas = Number(this.pagos.cuotas || 0);
                const total = Number(this.totales.totalGeneral || 0);

                if (cuotas <= 0) {
                    this.erroresPago.cuotas = 'Debe ingresar un numero de cuotas valido';
                    return;
                }

                if (total <= 0) {
                    this.erroresPago.listaCuotas = 'El total de la venta debe ser mayor a cero';
                    return;
                }

                const valorCuota = Number((total / cuotas).toFixed(4));
                const cuotasArray = [];
                const fechaBase = this.formVenta.venFechaEmision || fechaActual;
                const diasCredito = Number(this.pagos.dias || 0);

                for (let i = 1; i <= cuotas; i++) {
                    cuotasArray.push({
                        numero: i,
                        fecha: this.sumarDiasFecha(fechaBase, diasCredito * i),
                        valor: valorCuota,
                        saldo: valorCuota
                    });
                }

                const suma = cuotasArray.reduce((totalCuotas, cuota) => totalCuotas + Number(cuota.valor || 0), 0);
                const diferencia = Number((total - suma).toFixed(4));

                if (cuotasArray.length && Math.abs(diferencia) > 0) {
                    const ultimaCuota = cuotasArray[cuotasArray.length - 1];
                    ultimaCuota.valor = Number((Number(ultimaCuota.valor) + diferencia).toFixed(4));
                    ultimaCuota.saldo = ultimaCuota.valor;
                }

                this.pagos.listaCuotas = cuotasArray;
            },
            validarPagoVenta() {
                this.erroresPago = {};
                const total = Number(this.totales.totalGeneral || 0);

                if (this.formVenta.venEstado !== 'ARCHIVADO') {
                    return true;
                }

                if (!this.pagos.tipoPago) {
                    this.erroresPago.tipoPago = 'Seleccione el tipo de pago';
                }

                if (this.pagos.tipoPago === 'CONTADO') {
                    if (!this.pagos.formaPago) {
                        this.erroresPago.formaPago = 'Seleccione el metodo de pago';
                    }

                    if (!this.pagos.cuentaContablePago) {
                        this.erroresPago.cuentaContablePago = 'Seleccione la cuenta contable del cobro';
                    }

                    if (Number(this.pagos.valorRecibido || 0) < total) {
                        this.erroresPago.valorRecibido = 'El valor recibido no puede ser menor al total de la venta';
                    }

                    if (!this.pagos.fechaCobro) {
                        this.erroresPago.fechaCobro = 'Ingrese la fecha del cobro';
                    }

                    const formaPago = this.pagos.formaPago?.cod;
                    const camposPagoContado = [];

                    if (formaPago === '01') {
                        camposPagoContado.push({campo: 'nota', valor: this.pagos.nota, mensaje: 'Ingrese una nota para el cobro en efectivo'});
                    } else if (formaPago === '02') {
                        camposPagoContado.push(
                                {campo: 'banco', valor: this.pagos.banco, mensaje: 'Seleccione el banco de la transferencia'},
                                {campo: 'numeroTransferencia', valor: this.pagos.numeroTransferencia, mensaje: 'Ingrese el numero de transferencia'},
                                {campo: 'fechaTransferencia', valor: this.pagos.fechaTransferencia, mensaje: 'Seleccione la fecha de transferencia'},
                                {campo: 'nota', valor: this.pagos.nota, mensaje: 'Ingrese una nota para la transferencia'}
                        );
                    } else if (formaPago === '03') {
                        camposPagoContado.push(
                                {campo: 'banco', valor: this.pagos.banco, mensaje: 'Seleccione el banco del cheque'},
                                {campo: 'numeroCheque', valor: this.pagos.numeroCheque, mensaje: 'Ingrese el numero de cheque'},
                                {campo: 'fechaCheque', valor: this.pagos.fechaCheque, mensaje: 'Seleccione la fecha del cheque'}
                        );
                    } else if (formaPago === '04') {
                        camposPagoContado.push(
                                {campo: 'marcaTarjeta', valor: this.pagos.marcaTarjeta, mensaje: 'Seleccione la marca de la tarjeta'},
                                {campo: 'loteTarjeta', valor: this.pagos.loteTarjeta, mensaje: 'Ingrese el lote de la tarjeta'},
                                {campo: 'autorizacionTarjeta', valor: this.pagos.autorizacionTarjeta, mensaje: 'Ingrese la autorizacion de la tarjeta'},
                                {campo: 'ultimosDigitos', valor: this.pagos.ultimosDigitos, mensaje: 'Ingrese los ultimos cuatro digitos de la tarjeta'},
                                {campo: 'fechaVoucher', valor: this.pagos.fechaVoucher, mensaje: 'Seleccione la fecha del voucher'},
                                {campo: 'nota', valor: this.pagos.nota, mensaje: 'Ingrese una nota para el cobro con tarjeta'}
                        );
                    }

                    camposPagoContado.forEach(campo => {
                        const estaVacio = campo.valor === null || campo.valor === undefined || (typeof campo.valor === 'string' && campo.valor.trim() === '');

                        if (estaVacio) {
                            this.erroresPago[campo.campo] = campo.mensaje;
                        }
                    });

                    if (formaPago === '04' && this.pagos.ultimosDigitos && !/^\d{4}$/.test(String(this.pagos.ultimosDigitos))) {
                        this.erroresPago.ultimosDigitos = 'Ingrese exactamente cuatro numeros';
                    }
                }

                if (this.pagos.tipoPago === 'CREDITO') {
                    if (Number(this.pagos.cuotas || 0) <= 0) {
                        this.erroresPago.cuotas = 'Debe ingresar un numero de cuotas valido';
                    }

                    if (!this.pagos.fechaVenceCredito) {
                        this.erroresPago.fechaVenceCredito = 'Ingrese la fecha de vencimiento';
                    }

                    if (!this.pagos.listaCuotas.length) {
                        this.erroresPago.listaCuotas = 'Debe generar al menos una cuota';
                    }

                    const sumaCuotas = this.pagos.listaCuotas.reduce((totalCuotas, cuota) => totalCuotas + Number(cuota.valor || 0), 0);

                    if (this.pagos.listaCuotas.length && Math.abs(sumaCuotas - total) > 0.01) {
                        this.erroresPago.totalCuotas = 'La suma de las cuotas debe ser igual al total de la venta';
                    }

                    this.pagos.listaCuotas.forEach((cuota, i) => {
                        if (!cuota.fecha) {
                            this.erroresPago['cuotaFecha_' + i] = 'Ingrese fecha';
                        }

                        if (Number(cuota.valor || 0) <= 0) {
                            this.erroresPago['cuotaValor_' + i] = 'Valor invalido';
                        }
                    });
                }

                if (this.requiereFormaPagoAts && (!Array.isArray(this.ats.formaPago) || !this.ats.formaPago.length)) {
                    sweet_msg_toast('warning', 'Debe seleccionar al menos una forma de pago ATS.');
                    return false;
                }

                return Object.keys(this.erroresPago).length === 0;
            },
            cargarDatosVenta() {
                this.cargandoVentaEdit = true;

                this.formVenta.venTipoComprobante = this.listaTiposComprobantes.find(item => String(item.comp_codigo) === String(dataVenta.ven_tipo_comprobante_cod)) ?? null;
                this.formVenta.venPuntoEmision = this.listaPuntosEmision.find(item => Number(item.id) === Number(dataVenta.fk_punto_venta)) ?? null;
                this.formVenta.venNumeroEstablecimiento = dataVenta.ven_numero_establecimiento || '';
                this.formVenta.venNumeroEmision = dataVenta.ven_numero_emision || '';
                this.formVenta.venNumeroComprobante = dataVenta.ven_numero_comprobante || '';
                this.formVenta.venFechaEmision = dataVenta.ven_fecha_emision || fechaActual;
                this.formVenta.venBodega = this.listaBodegas.find(item => Number(item.id) === Number(dataVenta.fk_bodega)) ?? null;
                this.formVenta.venCentroCosto = this.listaCentroCostos.find(item => Number(item.id) === Number(dataVenta.fk_centro_costo)) ?? null;
                this.formVenta.venTipoVenta = this.listaTipoVenta.find(item => Number(item.id) === Number(dataVenta.fk_tipo_venta)) ?? null;
                this.formVenta.venEstado = dataVenta.ven_estado || 'BORRADOR';
                this.formVenta.venPermitirDuplicados = String(dataVenta.ven_items_duplicados) === 'true';
                this.formVenta.venObservacion = dataVenta.ven_observacion || '';

                if (dataCliente) {
                    this.setClienteDetalle(dataCliente);
                }

                this.cargandoVentaEdit = false;
            },
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
                    datos.ventaId = this.isEdit ? this.idVenta : '';

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
            construirDataVenta() {
                return {
                    idVenta: this.idVenta,
                    venta: {
                        venTipoComprobante: this.formVenta.venTipoComprobante?.comp_codigo ?? null,
                        venPuntoEmision: this.formVenta.venPuntoEmision?.id ?? null,
                        venNumeroEstablecimiento: this.formVenta.venNumeroEstablecimiento,
                        venNumeroEmision: this.formVenta.venNumeroEmision,
                        venNumeroComprobante: this.formVenta.venNumeroComprobante,
                        venFechaEmision: this.formVenta.venFechaEmision,
                        venCliente: this.formVenta.venCliente?.id ?? null,
                        venBodega: this.formVenta.venBodega?.id ?? null,
                        venCentroCosto: this.formVenta.venCentroCosto?.id ?? null,
                        venTipoVenta: this.formVenta.venTipoVenta?.id ?? null,
                        venEstado: this.formVenta.venEstado,
                        venPermitirDuplicados: this.formVenta.venPermitirDuplicados,
                        venObservacion: this.formVenta.venObservacion
                    },
                    basesImpuestos: (this.basesImpuesto || []).map(base => ({
                            impuesto_tarifa_id: base.impuesto_tarifa_id,
                            codigo: base.codigo,
                            detalle: base.detalle,
                            porcentaje: Number(base.porcentaje || 0),
                            subtotal_bruto: Number(base.subtotal_bruto || 0),
                            subtotal_neto: Number(base.subtotal_neto || 0),
                            iva: Number(base.iva || 0)
                        })),
                    pago: {
                        tipoPago: this.pagos.tipoPago,
                        formaPago: this.pagos.formaPago?.cod ?? null,
                        cuentaContable: this.pagos.cuentaContablePago?.ctad_codigo ?? null,
                        banco: this.pagos.banco ? {
                            codigo: this.pagos.banco.codigo,
                            nombre: this.pagos.banco.nombre
                        } : null,
                        valorRecibido: Number(this.pagos.valorRecibido || 0),
                        valorCobrado: this.pagos.tipoPago === 'CONTADO' ? Number(this.totales.totalGeneral || 0) : 0,
                        cambio: this.cambioVenta,
                        fechaCobro: this.pagos.fechaCobro,
                        diasCredito: Number(this.pagos.dias || 0),
                        referencia: this.pagos.referencia,
                        nota: this.pagos.nota,
                        numeroTransferencia: this.pagos.numeroTransferencia?.trim() || null,
                        fechaTransferencia: this.pagos.fechaTransferencia || null,
                        numeroCheque: this.pagos.numeroCheque?.trim() || null,
                        fechaCheque: this.pagos.fechaCheque || null,
                        marcaTarjeta: this.pagos.marcaTarjeta || null,
                        loteTarjeta: this.pagos.loteTarjeta?.trim() || null,
                        autorizacionTarjeta: this.pagos.autorizacionTarjeta?.trim() || null,
                        ultimosDigitos: this.pagos.ultimosDigitos?.trim() || null,
                        fechaVoucher: this.pagos.fechaVoucher || null
                    },
                    ats: {
                        formasPago: this.requiereFormaPagoAts ? (this.ats.formaPago || []).map(forma => forma.codigo) : []
                    },
                    cuotas: this.pagos.tipoPago === 'CREDITO' ? this.pagos.listaCuotas.map(cuota => ({
                            numero: Number(cuota.numero || 0),
                            fecha: cuota.fecha,
                            valor: Number(cuota.valor || 0),
                            saldo: Number(cuota.valor || 0)
                        })) : []
                };
            },
            async guardarVenta() {
                try {

                    if (this.emptyCar) {
                        sweet_msg_toast('warning', 'Debe agregar al menos un item para guardar la venta.');
                        return;
                    }

                    if (!this.validarPagoVenta()) {
                        return;
                    }

                    this.loadingProcess = true;
                    const dataPostVenta = this.construirDataVenta();
                    const formData = new FormData();
                    formData.append('data', JSON.stringify(dataPostVenta));

                    const urlGuardar = this.isEdit ? '/ventas/updateVenta' : '/ventas/saveVenta';
                    const {data} = await axios.post(this.url + urlGuardar, formData);

                    if (data.status === 'success') {
                        const urlRedirect = data.data?.redirect || (this.isEdit ? this.url + '/ventas/gestionVentas' : this.url + '/ventas/nuevaVenta');
                        this.modalFinalizarInstance?.hide();
                        sweetMsgDialogConfirm(data.msg || 'Venta guardada correctamente.', this.verDetalle, data.data, urlRedirect);
                        return;
                    }

                    this.modalFinalizarInstance?.hide();
                    sweet_msg_dialog(data.status || 'warning', data.msg || 'No se pudo guardar la venta.');
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loadingProcess = false;
                }
            },
            async verDetalle(venta) {
                this.idVentaReporte = venta.id;
                this.secuencialVenta = venta.ven_secuencial;
                this.modalTitulo = 'Detalle de Venta #' + this.zFill(venta.ven_secuencial, 5);
                this.detalleHtml = '';
                this.mostrarBotonesReporte = true;
                this.cargandoDetalle = true;
                this.modalReporteInstance.show();

                try {
                    const {data} = await axios.get(`${this.url}/ventas/getDataDetalle/${venta.id}`);
                    this.detalleHtml = data;
                } catch (e) {
                    this.modalReporteInstance.hide();
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.cargandoDetalle = false;
                }
            },
            generarExcel() {
                const contenido = document.querySelector('#contentExport');
                const titulo = 'Detalle venta ' + this.zFill(this.secuencialVenta, 5);
                return generarExcelContent(contenido, titulo);
            },
            generarPDF() {
                window.open(`${this.url}/ventas/generarPDF/${this.idVentaReporte}?download=1`, '_blank');
            },
            sumarDiasFecha(fecha, dias) {
                return DateTime.fromISO(fecha || fechaActual).plus({days: Number(dias || 0)}).toFormat('yyyy-MM-dd');
            },
            async cancelarVenta() {
                const respuesta = await Swal.fire({
                    icon: 'warning',
                    title: 'Cancelar venta',
                    width: '35%',
                    html: '<h6>Se limpiara todo el carrito de venta. Esta accion no se puede deshacer.</h6>',
                    showCancelButton: true,
                    confirmButtonText: 'Si, cancelar',
                    cancelButtonText: 'No',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6e7881'
                });

                if (!respuesta.isConfirmed) {
                    return;
                }

                try {
                    this.loadingProcess = true;
                    const {data} = await axios.post(this.url + '/ventas/cancelarVenta');
                    sweet_msg_toast(data.status, data.msg);
                    this.productoSeleccionado = null;
                    this.codeSearch = '';
                    await this.showDetailCart();
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loadingProcess = false;
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
