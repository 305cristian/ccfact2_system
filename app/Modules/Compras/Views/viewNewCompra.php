<!DOCTYPE html>
<!--
/**
 * Description of viewNewCompra
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 3 may 2026
 * @time 4:40:04 p.m.
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

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 v-if="isEdit" class="card-title text-system"><i class="fas fa-folder-blank"></i> Actualizar Compra</h5>
            <h5 v-else class="card-title text-system"><i class="fas fa-folder-blank"></i> Nueva Campra</h5>
        </div>
        <div class="card-body">

            <fieldset>
                <legend>Información del Comprobante 
                    <button 
                        type="button"
                        class="btn btn-sm btn-outline-secondary"
                        @click="showInfoComprobante = !showInfoComprobante"
                        >
                        <i  :class="showInfoComprobante ? 'fas fa-chevron-up'  : 'fas fa-chevron-down'"  ></i>
                    </button>
                </legend>
                <div v-show="showInfoComprobante">
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Tipo Comprobante -->
                            <div class="col-md-12 form-group-custom">
                                <div class="d-flex justify-content-between align-items-center border">
                                    <span class="input-group-text bg-cris-system">
                                        <i class="fas fa-file-invoice me-2"></i> Comprobante
                                    </span>
                                    <vue-select 
                                        class="flex-grow-1"
                                        :options="listaTiposComprobantes"
                                        label="comp_nombre"
                                        v-model="formCompra.compTipoComprobante"
                                        placeholder="Seleccione un comprobante"/>
                                </div>
                            </div>

                            <!-- Sustento -->
                            <div class="col-md-12 form-group-custom">
                                <div class="d-flex align-items-center border">
                                    <span class="input-group-text bg-cris-system">
                                        <i class="fas fa-receipt me-2"></i> Tipo de Sustento
                                    </span>
                                    <vue-select 
                                        class="flex-grow-1"
                                        :options="listaSustentos"
                                        label="sus_nombre"
                                        v-model="formCompra.compSustento"
                                        placeholder="Seleccione un sustento"/>
                                </div>
                            </div>

                            <!-- Tipo Compra -->
                            <div class="col-md-12 form-group-custom">
                                <div class="d-flex align-items-center border">
                                    <span class="input-group-text bg-cris-system">
                                        <i class="fas fa-tags me-2"></i> Tipo Compra
                                    </span>
                                    <vue-select 
                                        class="flex-grow-1"
                                        :options="listaTiposCompra"
                                        label="tc_nombre"
                                        v-model="formCompra.compTipoCompra"
                                        placeholder="Seleccione un tipo de compra"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">


                            <!-- Número Comprobante -->
                            <div class="col-md-12 form-group-custom">
                                <div class="input-group">
                                    <span class="input-group-text bg-cris-system">
                                        <i class="fas fa-hashtag me-2"></i> N° Comprobante
                                    </span>
                                    <input v-model="formCompra.compNumeroEstablecimiento" type="text" class="form-control" style="flex:1" placeholder="001">
                                    <input v-model="formCompra.compNumeroEmision" type="text" class="form-control" style="flex:1" placeholder="002">
                                    <input v-model="formCompra.compNumeroComprobante" type="text" class="form-control" style="flex:2" placeholder="653">
                                </div>
                            </div>

                            <!--Fecha de caducidad de comprobante-->
                            <div class="col-md-6 form-group">
                                <div class="input-group">
                                    <span class="input-group-text bg-cris-system">
                                        <i class="fas fa-calendar me-2"></i> Fecha de Caducidad
                                    </span>
                                    <input v-model="formCompra.compFechaCaducidad" type="date" class="form-control">
                                </div>
                            </div>

                            <!-- Autorización SRI -->
                            <div class="col-md-12 form-group-custom">
                                <div class="input-group">
                                    <span class="input-group-text bg-cris-system">
                                        <i class="fas fa-key me-2"></i> Aut. SRI
                                    </span>
                                    <input v-model="formCompra.compAutSRI" type="text" class="form-control" placeholder="Ejm. 0123456789">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </fieldset>
            <br>
            <fieldset>
                <legend>Información General
                    <button 
                        type="button"
                        class="btn btn-sm btn-outline-secondary"
                        @click="showInfoGeneral = !showInfoGeneral"
                        >
                        <i  :class="showInfoGeneral ? 'fas fa-chevron-up'  : 'fas fa-chevron-down'"  ></i>
                    </button>
                </legend>
                <div v-show="showInfoGeneral">
                    <div class="row">
                        <div class="col-md-6 border-right">
                            <!-- Proveedor -->
                            <div class="col-md-12 form-group-custom">
                                <div class="d-flex align-items-center">
                                    <vue-multiselect
                                        v-model="formCompra.compProveedor"
                                        placeholder="Buscar proveedor"
                                        label="proveedor"
                                        track-by="prov_ruc"
                                        :searchable="true"
                                        :options-limit="10"
                                        :options="listaSearchProveedores"
                                        :show-no-results="true"
                                        @search-change="searchProveedor">

                                        <template #option="{ option }">
                                            <span style="font-size: 12px"><strong>{{ option.prov_ruc+': '}} </strong> {{  option.prov_razon_social}}</span>
                                        </template>
                                    </vue-multiselect>
                                    <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                </div>
                            </div>
                            <!-- Información proveedor -->
                            <?php echo view('\Modules\Compras\Views\viewInfoProveedor') ?>
                        </div>
                        <div class="col-md-6 ps-md-4">
                            <div class="row">

                                <!-- Fecha Emisión -->
                                <div class="col-md-6 form-group">
                                    <div class="input-group">
                                        <span class="input-group-text bg-cris-system">
                                            <i class="fas fa-calendar me-2"></i> Fecha de Emisión
                                        </span>
                                        <input v-model="formCompra.compFechaEmision" type="date" class="form-control">
                                    </div>
                                </div>

                                <!-- Bodega -->
                                <div class="col-md-6 form-group">
                                    <div class="d-flex align-items-center border">                           
                                        <span class="input-group-text bg-cris-system">
                                            <span v-if='loadingBodega'><i class="fas fa-spin fa-spinner"></i></span>
                                            <span v-else><i class="fas fa-warehouse me-2"></i></span>
                                            Bodega
                                        </span>
                                        <vue-select 
                                            class="flex-grow-1"
                                            @option:selected="changeBodega"
                                            :options="listaBodegas"
                                            label="bod_nombre"
                                            v-model="formCompra.compBodega"
                                            placeholder="Seleccione una bodega"/>
                                    </div>
                                </div>

                                <!-- Observaciones -->

                                <div class="col-md-12 form-group">
                                    <div class="input-group">
                                        <span class="input-group-text bg-cris-system">
                                            <i class="fas fa-comments me-2"></i> Observaciones
                                        </span>
                                        <textarea v-model="formCompra.compObservaciones" type="text" class="form-control" placeholder="Ejm. Compra de abarrotes"></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">

                        <!-- Centro de costo -->
                        <div class="col-md-3 form-group-custom">
                            <div class="d-flex align-items-center border">
                                <span class="input-group-text bg-cris-system">
                                    <i class="fas fa-project-diagram me-2"></i> Centro Costo
                                </span>
                                <vue-select 
                                    class="flex-grow-1"
                                    :options="listaCentroCostos"
                                    label="cc_nombre"
                                    v-model="formCompra.compCentroCosto"
                                    placeholder="Seleccione un centro de costos"/>
                            </div>
                        </div>

                        <!-- Tipo Costos -->
                        <div class="col-md-3 form-group-custom">
                            <div class="d-flex align-items-center border">
                                <span class="input-group-text bg-cris-system">
                                    <i class="fas fa-coins me-2"></i> Tipo Costos
                                </span>
                                <vue-select 
                                    class="flex-grow-1"
                                    :options="listaTiposCostos"
                                    label="value"
                                    v-model="formCompra.compTipoCosto"
                                    placeholder="Seleccione un tipo de costos"/>
                            </div>
                        </div>

                        <!-- ODC -->
                        <div class="col-md-2 form-group-custom">
                            <div class="d-flex align-items-center border">
                                <span class="input-group-text bg-cris-system"><input class="form-check-inline" type="checkbox" v-model="formCompra.tieneOdc"> ODC</span>
                                <vue-select 
                                    class="flex-grow-1"
                                    :options="listaOdc"
                                    label="value"
                                    :disabled="!formCompra.tieneOdc"
                                    v-model="formCompra.compODC"
                                    placeholder="Ejm. 0236"/>
                            </div>
                        </div>

                        <!-- Es gasto -->
                        <div class="col-md-2 form-group-custom d-flex align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" v-model="formCompra.compEsGasto">
                                <label class="form-check-label">Es gasto</label>
                            </div>
                        </div>


                        <!-- Estado -->
                        <div class="col-md-2 form-group-custom">
                            <div class="input-group">
                                <span class="input-group-text bg-cris-system">
                                    <i class="fas fa-toggle-on me-2"></i> Estado
                                </span>
                                <select v-model="formCompra.compEstado" class="form-select">
                                    <option value="BORRADOR">BORRADOR</option>
                                    <option value="ARCHIVADO">ARCHIVADO</option>
                                </select>
                            </div>
                        </div>


                    </div>
                </div>
            </fieldset>
            <br>
            <fieldset>
                <legend>Búsqueda de Productos</legend>

                <div class="row">

                    <!-- BUSCADOR PRINCIPAL -->
                    <div class="col-md-5 form-group-custom">
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
                                @select="agregarProductoCompra($event)">

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

                            <span class="input-group-text bg-warning">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>

                    <!-- BUSCAR POR CÓDIGO -->
                    <div class="col-md-3 form-group-custom">
                        <div class="input-group">
                            <input 
                                v-model="codigoBusqueda"
                                @keyup.enter="buscarPorCodigo"
                                type="text"
                                class="form-control"
                                placeholder="Código / Código barras">
                            <span class="input-group-text bg-warning">
                                <i class="fas fa-barcode"></i>
                            </span>
                        </div>
                    </div>

                </div>
            </fieldset>
            <br>
            <!--VIEW CART-->
            <?php echo view('\Modules\Compras\Views\viewCart') ?>
            <!--VIEW CART-->

            <!--VIEW ANEXO ATS-->
            <?php echo view('\Modules\Compras\Views\viewAnexoATS') ?>
            <!--VIEW ANEXO ATS-->

            <!--VIEW RETENCION-->
            <?php echo view('\Modules\Compras\Views\viewRetencion') ?>
            <!--VIEW RETENCION-->

            <!-- Botones de Control -->
            <div v-if="!emptyCar" class="row mt-4 mb-5">
                <div class="col-12 d-flex gap-3 justify-content-end">
                    <button @click="cancelarCompra()" class="btngr btn-danger-gradiant" style="min-width: 150px;" :disabled="loadingProcess">
                        <i class="fas fa-times-circle me-2"></i>Cancelar
                    </button>
                    <button class="btngr btn-primary-gradiant" style="min-width: 150px;" @click="abrirModalFinalizar" :disabled="loadingProcess">
                        <span v-if="loadingProcess"><i class="loading-spin"></i>{{isEdit?'Actualizando...':'Grabando...'}}</span>
                        <span v-else><i class="fas fa-save me-2"></i>{{isEdit?'Actualizar Compra':'Guardar Compra'}}</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
    <!--MODAL FINALIZAR COMPRA-->
    <?php echo view('\Modules\Compras\Views\viewPagos') ?>
    <!--CLOSE MODAL FINALIZAR COMPRA-->
</div>

<script type="text/javascript">

    var fechaActual = DateTime.now().toFormat('yyyy-MM-dd');
    var listaTiposCompra = <?= json_encode($listaTiposCompra); ?>;
    var listaTiposComprobantes = <?= json_encode($listaTiposComprobantes); ?>;
    var listaFormasPago = <?= json_encode($listaFormasPago); ?>;
    var listaFormasPagoSRI = <?= json_encode($listaFormasPagoSRI); ?>;
    var listaSustentos = <?= json_encode($listaSustentos); ?>;
    var listaBodegas = <?= json_encode($listaBodegas); ?>;
    var listaCentroCostos = <?= json_encode($listaCentroCostos); ?>;
    var listaRetenciones = <?= json_encode($listaRetenciones); ?>;
    var listaCuentasContables = <?= json_encode($listaCuentasContables); ?>;
    var permitirDuplicados = <?= $permitirDuplicados ?>;
    var bodegaIdComp = '<?= $bodegaId; ?>';
    var dataCompra =<?= json_encode($dataCompra); ?>;
    var dataProveedor =<?= json_encode($dataProveedor); ?>;

    var ivaPrdeterminado =<?= ivaPredeterminado(); ?>;
    var valorMaximoATSSRI =<?= getSettings('VALOR_MAXIMO_ANEXO_ATS_SRI') ?>;

    if (window.appCompra) {
        window.appCompra.unmount();
    }

    window.appCompra = Vue.createApp({

        components: {
            "vue-multiselect": window['vue-multiselect'].Multiselect,
            "vue-select": window['vue-select']
        },

        data() {
            return {

                url: siteUrl,
                isEdit: false,

                // =========================
                // FORM HEADER
                // =========================
                formCompra: {
                    compFechaEmision: fechaActual,
                    compTipoComprobante: '',
                    compNumeroComprobante: '',
                    compNumeroEstablecimiento: '',
                    compNumeroEmision: '',
                    compFechaCaducidad: fechaActual,
                    compAutSRI: '',
                    compProveedor: '',
                    compBodega: '',
                    compSustento: '',
                    compCentroCosto: '',
                    compTipoCompra: '',
                    compTipoCosto: '',
                    tieneOdc: true,
                    compODC: '',
                    compAplicaRetencion: false,
                    compEsGasto: false,
                    compFormaPago: '',
                    compTipoPago: 'CONTADO',
                    compCuotas: 1,
                    compDiasCredito: 0,
                    compEstado: 'BORRADOR',
                    compObservaciones: '',
                    compPermitirDuplicados: permitirDuplicados
                },

                // =========================
                // LISTAS
                // =========================
                listaSearchProveedores: [],
                listaBodegas: listaBodegas,
                listaSustentos: listaSustentos,
                listaCentroCostos: listaCentroCostos,
                listaTiposCompra: listaTiposCompra,
                listaTiposCostos: [{value: 'DIRECTOS', id: 'DIRECTOS'}, {value: 'INDIRECTOS', id: 'INDIRECTOS'}],
                listaFormasPago: listaFormasPago,
                listaFormasPagoSRI: listaFormasPagoSRI,
                listaTiposComprobantes: listaTiposComprobantes,
                listaIvas: [],
                listaOdc: [],
                listaRetenciones: listaRetenciones,
                listaRetencionesSeleccionadas: [],
                listaCuentasContables: listaCuentasContables,

                // =========================
                // BUSCADOR PRODUCTOS
                // =========================
                listaSearchProductos: [],
                productoSeleccionado: null,
                codigoBusqueda: '',

                // =========================
                // DETALLE
                // =========================
                listaCartData: [],

                // =========================
                // TOTALES
                // =========================
                totalSubtotal: 0,
                totalIva: 0,
                totalIrbpnr: 0,
                totalGeneral: 0,

                // =========================
                // CONTROL
                // =========================
                loading: false,
                searchTimeout: null,
                emptyCar: true,
                loadingProcess: false,
                loadingBodega: false,
                showInfoComprobante: true,
                showInfoGeneral: true,

                // =========================
                // MODAL PAGOS CUOTAS
                // =========================
                modalPagoInstance: null,

                pagos: {
                    tipoPago: '',
                    formaPago: null,
                    cuotas: 1,
                    dias: 0, // aqui los dias de credito del proveedor
                    fechaVenceCredito: '',
                    listaCuotas: [],

                    cuentaContablePago: null,
                    nota: '',
                    banco: '',

                    //TRANSFERENCIA
                    numeroTransferencia: '',
                    fechaTransferencia: '',

                    //CHEQUE
                    numeroCheque: '',
                    fechaCheque: '',

                    //Tarjeta
                    marcaTarjeta: '',
                    loteTarjeta: '',
                    autorizacionTarjeta: '',
                    ultimosDigitos: '',
                    fechaVoucher: '',
                },

                // =========================
                // RETENCION
                // =========================
                formRetencion: {
                    asumirRetencion: 'NO_ASUMIR',
                    compAplicaRetencion: true,
                    compNoSujetoRetecion: false,
                    retNumeroComprobnate: '',
                    retNumeroEstablecimiento: '',
                    retNumeroEmision: '',
                    retFechaEmision: '',
                    retAutorizacionSri: '',
                    retDetalle: {}
                },
                retencionBienes: '',
                retencionServicios: '',
                retencionRenta: '',

                // =========================
                //INFORMACION ATS
                // =========================

                ats: {
                    residente: 'RESIDENTE',
                    formaPago: []
                },
                listaFormasPagoATSSeleccionadas: [],
                valorMaximoATSSRI: valorMaximoATSSRI,

                global: {
                    descuentoGlobal: 0,
                    recargo: 0,
                    servicios: 0,
                    otrosCargos: 0

                },
                tipoDescuento: 'VALOR',
                descuento: 0,

            };
        },

        mounted() {
            this.formCompra.compBodega = this.listaBodegas.find(val => val.id === bodegaIdComp);
            this.formCompra.compTipoComprobante = this.listaTiposComprobantes.find(val => val.id === '1');
            this.modalPagoInstance = new bootstrap.Modal(this.$refs.modalFinalizar);
        },
        computed: {
            listaRetencionesIvaBienes() {
                return this.listaRetenciones.filter(r => r.ret_impuesto_detalle === 'IVA_BIENES');
            },
            listaRetencionesIvaServicios() {
                return this.listaRetenciones.filter(r => r.ret_impuesto_detalle === 'IVA_SERVICIOS');
            },
            listaRetencionesRenta() {
                return this.listaRetenciones.filter(r => r.ret_impuesto_detalle === 'RENTA');
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
                        codigo = '1.01.02';
                        break;

                    case '03':
                        codigo = '1.01.02';
                        break;

                    case '04':
                        codigo = '1.01.02';
                        break;

                    default:
                        return [];

                }

                return this.listaCuentasContables.filter(
                        c => c.ctad_codigo.startsWith(codigo)
                );

            }
        },
        watch: {
            'pagos.listaCuotas': {
                deep: true,
                handler() {
                    this.validarCuotas();
                }
            },
            'formRetencion.compNoSujetoRetecion'(val) {
                if (val) {
//                    this.modal.retIvaBienes = null;
//                    this.modal.retIvaServicios = null;
//                    this.modal.retValorIva = 0;
                }
            }
        },

        methods: {

            //SEARCH PROVEEDORES
            searchProveedor(dataSerach) {
                clearTimeout(this.searchTimeout);
                const datos = {dataSerach};
                this.searchTimeout = setTimeout(async () => {
                    try {
                        const {data} = await axios.post(this.url + '/comun/proveedores/searchProveedor', datos);
                        if (data !== false) {
                            this.listaSearchProveedores = data;
                        } else {
                            this.listaSearchProveedores = [];
                        }
                    } catch (e) {
                        sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                        this.listaSearchProveedores = [];
                    }
                }, 500);

            },

            abrirModalFinalizar() {

                if (!this.listaCartData.length) {
                    sweet_msg_toast('warning', 'Debe agregar productos');
                    return;
                }

                this.modalPagoInstance.show();
            },
            generarCuotas() {

                let total = parseFloat(this.totalGeneral);
                let cuotas = parseInt(this.pagos.cuotas);
                let dias = parseInt(this.pagos.dias);

                if (!cuotas || cuotas <= 0) {
                    sweet_msg_toast('warning', 'Número de cuotas inválido');
                    return;
                }

                if (!dias || dias <= 0) {
                    sweet_msg_toast('warning', 'Días inválidos');
                    return;
                }

                let valorCuota = (total / cuotas);
                let cuotasArray = [];

                let fechaBase = DateTime.now();

                for (let i = 1; i <= cuotas; i++) {

                    let fecha = fechaBase.plus({days: dias * i}).toFormat('yyyy-MM-dd');

                    cuotasArray.push({
                        numero: i,
                        fecha: fecha,
                        valor: parseFloat(valorCuota.toFixed(4)),
                        saldo: parseFloat(valorCuota.toFixed(4)),
                        estado: 'PENDIENTE'
                    });
                }

//                AJUSTE DE DECIMALES (MUY IMPORTANTE)
                let suma = cuotasArray.reduce((acc, c) => acc + c.valor, 0);

                let diferencia = parseFloat((total - suma).toFixed(4));

                if (diferencia !== 0) {
                    cuotasArray[cuotasArray.length - 1].valor += diferencia;
                    cuotasArray[cuotasArray.length - 1].saldo += diferencia;
                }

                this.pagos.listaCuotas = cuotasArray;

                sweet_msg_toast('success', 'Cuotas generadas correctamente');
            },
            validarCuotas() {

                if (!this.pagos.listaCuotas.length) {
                    sweet_msg_toast('warning', 'Debe generar las cuotas');
                    return false;
                }

                let suma = this.pagos.listaCuotas.reduce((acc, c) => acc + parseFloat(c.valor || 0), 0);

                let total = parseFloat(this.totalGeneral);

                if (parseFloat(suma.toFixed(4)) !== parseFloat(total.toFixed(4))) {
                    sweet_msg_toast('error', 'Las cuotas no cuadran con el total');
                    return false;
                }

                return true;
            },

            resetFormulario() {

                this.listaCartData = [];

                this.pagos = {
                    formaPago: null,
                    tipoPago: 'CONTADO',
                    cuotas: 1,
                    dias: 0,
                    fechaVenceCredito: '',
                    listaCuotas: []
                };
                this.formRetencion = {
                    asumirRetencion: 'NO_ASUMIR',
                    compAplicaRetencion: true,
                    compNoSujetoRetecion: false,
                    retNumeroComprobnate: '',
                    retNumeroEstablecimiento: '',
                    retNumeroEmision: '',
                    retFechaEmision: '',
                    retAutorizacionSri: '',
                    retDetalle: {}
                };
                this.totalGeneral = 0;
            },

            async guardarCompra() {

                try {

                    // =========================
                    // VALIDACIONES BÁSICAS
                    // =========================
                    if (!this.formCompra.compProveedor) {
                        sweet_msg_toast('warning', 'Seleccione proveedor');
                        return;
                    }

                    if (!this.listaCartData.length) {
                        sweet_msg_toast('warning', 'Debe agregar productos');
                        return;
                    }

                    // =========================
                    // VALIDAR CUOTAS
                    // =========================
                    if (this.pagos.tipoPago === 'CREDITO') {
                        if (!this.validarCuotas())
                            return;
                    }

                    // =========================
                    // ARMAR OBJETO COMPRA
                    // =========================
                    const compra = {
                        ...this.formCompra,

                        compProveedor: this.formCompra.compProveedor?.id,
                        compBodega: this.formCompra.compBodega?.id,
                        compFormaPago: this.pagos.formaPago?.id,

                        compTipoPago: this.pagos.tipoPago,
                        compCuotas: this.pagos.cuotas,
                        compDiasCredito: this.pagos.dias,

                        compTotal: this.totalGeneral,

                        // 🔥 RETENCION
                        compAplicaRetencion: this.formRetencion.compAplicaRetencion,
                        retNumero: this.formRetencion.retNumero,
                        retAutorizacion: this.formRetencion.retAutorizacion
                    };

                    // =========================
                    // ARMAR DETALLE
                    // =========================
                    const detalle = this.listaCartData.map(i => ({
                            producto_id: i.id,
                            cantidad: i.cantidad,
                            precio: i.precio,
                            descuento: i.descuento,

                            iva_porcentaje: i.iva_porcentaje,
                            iva_valor: i.iva_valor,

                            ice_porcentaje: i.ice_porcentaje,
                            ice_valor: i.ice_valor,

                            irbpnr_unitario: i.irbpnr_unitario,
                            irbpnr_total: i.irbpnr_total,

                            subtotal: i.subtotal,
                            total: i.total
                        }));

                    // =========================
                    // CUOTAS (SI APLICA)
                    // =========================
                    let cuotas = [];

                    if (this.pagos.tipoPago === 'CREDITO') {
                        cuotas = this.pagos.listaCuotas.map(c => ({
                                numero: c.numero,
                                fecha: c.fecha,
                                valor: c.valor,
                                saldo: c.valor
                            }));
                    }

                    // =========================
                    // PAYLOAD FINAL
                    // =========================
                    const payload = {
                        compra,
                        detalle,
                        cuotas
                    };

                    // =========================
                    // ENVÍO
                    // =========================
                    this.loading = true;

                    const formData = new FormData();
                    formData.append('data', JSON.stringify(payload));

                    const {data} = await axios.post(
                            this.url + '/compras/saveCompraCompleta',
                            formData
                            );

                    // =========================
                    // RESPUESTA
                    // =========================
                    if (data.status === 'success') {

                        sweet_msg_toast('success', data.msg || 'Compra guardada');

                        // 🔥 LIMPIAR TODO
                        this.resetFormulario();

                    } else {
                        sweet_msg_toast('error', data.msg || 'Error al guardar');
                    }

                } catch (e) {

                    sweet_msg_toast('error', 'Error en el sistema');

                } finally {
                    this.loading = false;
                }
            },

            async changeBodega() {
                if (this.emptyCar !== false) {
                    let bodegaId = this.formCompra.compBodega?.id;

                    if (bodegaId) {
                        try {
                            this.loadingBodega = true;
                            let {data} = await axios.get(this.url + '/compras/changeBodega/' + bodegaId);
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
                    this.formDataAjuste.ajenBodega = this.listaBodegas.find(b => b.id === bodegaIdAje);

                    sweet_msg_dialog('warning', 'Existen productos cargados al carrito<br> No se puede cambiar de bodega');
                }

            },

            // =========================
            // BUSCAR PRODUCTOS
            // =========================
            async searchProductos(search) {

                clearTimeout(this.searchTimeout);

                this.searchTimeout = setTimeout(async () => {
                    const datos = {
                        dataSerach: search,
                        estado: 1
                    };
                    try {
                        const {data} = await axios.post(this.url + '/comun/productos/searchProductos', datos);
                        if (data !== false) {
                            this.listaSearchProductos = data;
                        } else {
                            this.listaSearchProductos = [];
                        }
                    } catch (e) {
                        sweet_msg_dialog('error', '', '', e.data.message);
                        this.listaSearchProductos = [];
                    }

                }, 400);
            },

            // =========================
            // BUSCAR POR CODIGO
            // =========================
            async buscarPorCodigo() {

                if (!this.codigoBusqueda) {
                    return;
                }


                try {
                    const {data} = await axios.post(this.url + '/comun/productos/searchByCode', {codigo: this.codigoBusqueda});

                    if (data) {
                        this.agregarProductoCompra(data);
                    } else {
                        sweet_msg_toast('warning', 'Producto no encontrado');
                    }

                } catch (e) {
                    console.error(e);
                }

                this.codigoBusqueda = '';
            },

            // =========================
            // AGREGAR PRODUCTO
            // =========================
            agregarProductoCompra(producto) {

                if (!this.formCompra.compBodega) {
                    sweet_msg_toast('warning', 'Seleccione una bodega');
                    return;
                }
                this.emptyCar = false;
                const nuevoItem = {
                    id: producto.id,
                    codigo: producto.prod_codigo,
                    nombre: producto.prod_nombre,

                    cantidad: 1,
                    precio: producto.prod_costoultimo || 0,
                    descuento: 0,

                    iva_porcentaje: producto.impt_porcentaje || 0,
                    fk_impuesto: producto.fk_impuesto,

                    ice_porcentaje: producto.ice_porcentaje || 0,
                    irbpnr_unitario: producto.irbpnr || 0,

                    subtotal: 0,
                    iva_valor: 0,
                    total: 0
                };

                this.calcularItem(nuevoItem);

                this.listaCartData.push(nuevoItem);
                this.productoSeleccionado = null;

                this.calcularTotales();
            },

            // =========================
            // ELIMINAR ITEM
            // =========================            
            async deleteProduct(rowId) {
                try {
                    this.loading = true;
                    await axios.get(this.url + '/ajustesentrada/deleteProduct/' + rowId);
                    this.showDetailCart();
                    sweet_msg_toast('info', 'Producto eliminado exitosamente');
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                } finally {
                    this.loading = false;
                }
            },

            // =========================
            // UPDATE ITEM
            // =========================          
            async updateProductCart(item) {
                this.onRemove();//Removemos datos del anterior producto insertado

                if (item.qty <= 0) {
                    item.qty = 1;
                    sweet_msg_toast('warning', 'La cantidad debe ser mayor a cero');
                    return false;
                }

                let datos = item;
                datos.idBodega = this.formDataAjuste.ajenBodega.id;

                try {
                    this.loading = true;

                    let {data} = await axios.post(this.url + '/ajustesentrada/updateProduct', datos);
                    if (data.status === "success") {
                        sweet_msg_toast('success', data.msg);
                    } else if (data.status === "warning") {
                        sweet_msg_toast('warning', data.msg);
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                } finally {
                    this.loading = false;
                }

                this.showDetailCart();
            },

            // =========================
            // CALCULO POR ITEM
            // =========================
            calcularItem(item) {

                let precio = parseFloat(item.precio);
                let cantidad = parseFloat(item.cantidad);

                // DESCUENTO
                let descuento = parseFloat(item.descuento || 0);

                let precioNeto = precio - descuento;
                let subtotal = precioNeto * cantidad;

                // ICE
                let ice_unit = precioNeto * (item.ice_porcentaje / 100);
                let ice_total = ice_unit * cantidad;

                // BASE IVA
                let base_iva = precioNeto + ice_unit;

                // IVA
                let iva_unit = base_iva * (item.iva_porcentaje / 100);
                let iva_total = iva_unit * cantidad;

                // IRBPNR
                let irbpnr_total = item.irbpnr_unitario * cantidad;

                // TOTAL
                let total = subtotal + iva_total + ice_total + irbpnr_total;

                item.subtotal = subtotal;
                item.iva_valor = iva_total;
                item.irbpnr_total = irbpnr_total;
                item.total = total;
            },

            // =========================
            // CALCULO GLOBAL
            // =========================
            calcularTotales() {

                let subtotal = 0;
                let iva = 0;
                let irbpnr = 0;
                let total = 0;

                this.listaCartData.forEach(i => {
                    subtotal += i.subtotal;
                    iva += i.iva_valor;
                    irbpnr += i.irbpnr_total;
                    total += i.total;
                });

                this.totalSubtotal = subtotal;
                this.totalIva = iva;
                this.totalIrbpnr = irbpnr;
                this.totalGeneral = total;
            },
            async cancelarAjuste() {
                Swal.fire({
                    title: "Esta seguro que desea cancelar la compra?",
                    html: "<h6>Esta acción borrara toda las lista cargada.</h6>",
                    icon: 'warning',
                    width: "30%",
                    showCancelButton: true,
                    confirmButtonText: "Si, Continuar",
                    confirmButtonColor: "#bb2d3b"
                }).then(async(result) => {
                    if (result.isConfirmed) {
                        try {
                            this.loading = true;
                            await axios.post(this.url + '/compras/cancelarCompra');
                            this.showDetailCart();
                            this.clear();
                            window.history.pushState({}, '', this.url + '/compras/nuevaCompra');
                        } catch (e) {
                            sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                        } finally {
                            this.loading = false;
                        }
                    }
                });


            },

            //====================
            //RETENCIONES
            //====================

            agregarRetencionIva(retencion) {

                const existe = this.listaRetencionesSeleccionadas.some(
                        r => r.id === retencion.id
                );

                if (existe) {
                    sweet_msg_toast('info', 'La retención ya se encuentra agregada');
                    return;
                }

                this.listaRetencionesSeleccionadas.push({
                    ...retencion,
                    base: this.formRetencion.baseIva ?? 0,
                    valorRetenido: this.calcularValorRetenido(
                            this.formRetencion.baseIva,
                            retencion.ret_porcentaje
                            )
                });

            },

            agregarRetencionRenta() {

                if (!this.retencionRenta) {
                    return;
                }

                const existe = this.listaRetencionesSeleccionadas.some(
                        r => r.id === this.retencionRenta.id
                );

                if (existe) {
                    sweet_msg_toast('info', 'La retención ya se encuentra agregada');
                    return;
                }

                this.listaRetencionesSeleccionadas.push({
                    ...this.retencionRenta,
                    base: this.formRetencion.baseRenta ?? 0,
                    valorRetenido: this.calcularValorRetenido(
                            this.formRetencion.baseRenta,
                            this.retencionRenta.ret_porcentaje
                            )
                });

                this.retencionRenta = null;
            },

            calcularValorRetenido(base, porcentaje) {

                let valor = (
                        parseFloat(base || 0) *
                        parseFloat(porcentaje || 0)
                        ) / 100;

                return parseFloat(valor).toFixed(2);
            },
            totalValorRetenido() {
                return this.listaRetencionesSeleccionadas
                        .reduce((acc, item) => acc + parseFloat(item.valorRetenido || 0), 0)
                        .toFixed(2);
            },
            calcularFechaCredito() {
                const dias = parseInt(this.pagos.dias || 0);
                const fecha = new Date();
                fecha.setDate(fecha.getDate() + dias);
                this.pagos.fechaVenceCredito = fecha
                        .toISOString()
                        .split('T')[0];

            },
            changeFormaPago() {

                this.pagos.nota = '';

                this.pagos.banco = '';
                this.pagos.numeroTransferencia = '';
                this.pagos.fechaTransferencia = '';

                this.pagos.numeroCheque = '';
                this.pagos.fechaCheque = '';

                this.pagos.marcaTarjeta = '';
                this.pagos.loteTarjeta = '';
                this.pagos.autorizacionTarjeta = '';
                this.pagos.ultimosDigitos = '';
                this.pagos.fechaVoucher = '';

            },
            changeTipoDescuento(item, tipo) {

                item.tipoDescuento = tipo;

                item.descuento = 0;

//                this.updateProductCart(item);

            },

            // =========================
            // UTILIDADES
            // =========================
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
            },
            formatToUSD(amount) {
                return formatToUSD(amount);
            },

            zFill(value, size) {
                return zFill(value, size);
            }

        }

    });

    window.appCompra.use(AllDirectives);
    window.appCompra.mount('#app');

</script>






