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
<?php
/** @var array $listaTiposCompra */
/** @var array $listaTiposComprobantes */
/** @var array $listaFormasPago */
/** @var array $listaFormasPagoSRI */
/** @var array $listaSustentos */
/** @var array $listaBodegas */
/** @var array $listaCentroCostos */
/** @var array $listaRetenciones */
/** @var array $listaCuentasContables */
/** @var array $listaImpuestosTarifa */
/** @var array $listaBancos */
/** @var object $dataProveedor */
/** @var object $dataCompra */
/** @var int $bodegaId */
/** @var bool $permitirDuplicados */
?>

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
                                        placeholder="Seleccione un comprobante">
                                    </vue-select>
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
                                        placeholder="Seleccione un sustento">
                                    </vue-select>
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
                                        placeholder="Seleccione un tipo de compra">
                                    </vue-select>
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
                                            placeholder="Seleccione una bodega">
                                        </vue-select>
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
                                    placeholder="Seleccione un centro de costos">                                       
                                </vue-select>
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
                                    placeholder="Seleccione un tipo de costos">
                                </vue-select>
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
                                    placeholder="Ejm. 0236">
                                </vue-select>
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
                                @select="insertProductCart($event)">

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
                                v-model="codeSearch"
                                @keyup.enter="insertProductCode($event)"
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
            <div v-if="formCompra.compEstado === 'ARCHIVADO'">
                <?php echo view('\Modules\Compras\Views\viewRetencion') ?>
            </div>
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

    <?php echo view('\Modules\Compras\Views\reportes\viewModalReport') ?>
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
    var listaImpuestosTarifa = <?php echo json_encode($listaImpuestosTarifa); ?>;
    var listaBancos = <?php echo json_encode($listaBancos); ?>;

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
                idCompra: dataCompra?.id ?? null,
                isEdit: false,
                ivaPrdeterminado: ivaPrdeterminado,

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
                listaImpuestosTarifa: listaImpuestosTarifa,
                listaBancosSimulados: listaBancos,

                // =========================
                // BUSCADOR PRODUCTOS
                // =========================
                listaSearchProductos: [],
                productoSeleccionado: null,
                codeSearch: '',

                // =========================
                // DETALLE
                // =========================
                listaCartData: [],

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
                modalReporteInstance: null,
                secuencialCompra: null,
                cargandoDetalle: false,
                detalleHtml: '',

                pagos: {
                    tipoPago: '',
                    formaPago: null,
                    cuotas: 1,
                    dias: 0, // aqui los dias de credito del proveedor
                    fechaVenceCredito: '',
                    listaCuotas: [],

                    cuentaContablePago: null,
                    nota: '',
                    banco: null,

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
                erroresPago: {},

                // =========================
                // RETENCION
                // =========================
                formRetencion: {
                    asumirRetencion: 'NO_ASUMIR',
                    compAplicaRetencion: true,
                    compNoSujetoRetecion: false,
                    baseIvaBienes: 0,
                    baseIvaServicios: 0,
                    baseRenta: 0,
                    retNumeroComprobante: '',
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
                    serviciosAdc: 0,
//                    otrosCargos: 0

                },
                totales: {
                    totalArticles: 0,
                    totalItems: 0,
                    totalSubtotalBruto: 0,
                    totalBienes: 0,
                    totalServicios: 0,
                    tarifCeroNeto: 0,
                    tarifIvaNeto: 0,
                    totalIva: 0,
                    totalIce: 0,
                    totalIrbpnr: 0,
                    totalDescuentoGlobal: 0,
                    totalDescuentoItems: 0,
                    totalSubtotalNeto: 0,
                    totalGeneral: 0,
                    tarifNoObjetoNeto: 0,
                    tarifExcentoNeto: 0
                },
                basesImpuesto: []

            };
        },
        created() {
            this.showDetailCart();
        },

        mounted() {
            this.formCompra.compBodega = this.listaBodegas.find(val => val.id === bodegaIdComp);
            this.formCompra.compTipoComprobante = this.listaTiposComprobantes.find(val => val.id === '1');

            if (dataCompra) {
                this.cargarDatosCompra();
            }

            this.$nextTick(() => {
                if (this.$refs.modalFinalizar) {
                    this.modalPagoInstance = new bootstrap.Modal(this.$refs.modalFinalizar);
                }

                if (this.$refs.modalReport) {
                    this.modalReporteInstance = new bootstrap.Modal(this.$refs.modalReport);
                }
            });
        },
        computed: {
            colLotes() {
                return this.listaCartData.some(item => Number(item.tieneLote) === 1);
            },

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

            },

            basesImpuestoVista() {
                if (!this.basesImpuesto) {
                    return [];
                }
                return this.basesImpuesto.filter(tax => {
                    if (Number(tax.porcentaje) === 0) {
                        return false;
                    }
                    return true;
                });

            },
            totalRetenidoCompra() {
                if (
                        this.formCompra.compEstado !== 'ARCHIVADO' ||
                        !this.formRetencion.compAplicaRetencion ||
                        this.formRetencion.compNoSujetoRetecion
                        ) {
                    return 0;
                }

                return this.listaRetencionesSeleccionadas.reduce(
                        (total, retencion) => total + Number(retencion.valorRetenido || 0), 0);
            },
            totalPagarCompra() {
                const totalFactura = Number(this.totales.totalGeneral || 0);
                return Math.max(0, totalFactura - this.totalRetenidoCompra);
            }
        },
        watch: {
            'pagos.listaCuotas': {
                deep: true,
                handler() {
                    this.validarCuotas();
                }
            }
        },

        methods: {

            cargarDatosCompra() {
                this.isEdit = true;

                const buscarPorId = (lista, id) => lista.find(item => String(item.id) === String(id));

                this.formCompra.compFechaEmision = dataCompra.comp_fecha_emision;

                this.formCompra.compTipoComprobante = this.listaTiposComprobantes.find(item => String(item.comp_codigo) === String(dataCompra.comp_tipo_comprobante_cod));

                this.formCompra.compNumeroComprobante = dataCompra.comp_numero_comprobante || '';

                this.formCompra.compNumeroEstablecimiento = dataCompra.comp_numero_establecimiento || '';

                this.formCompra.compNumeroEmision = dataCompra.comp_numero_emision || '';

                this.formCompra.compFechaCaducidad = dataCompra.comp_fecha_vencimiento_autorizacion || '';

                this.formCompra.compAutSRI = dataCompra.comp_autorizacion_sri || '';

                this.formCompra.compProveedor = dataProveedor || null;

                this.formCompra.compBodega = buscarPorId(this.listaBodegas, dataCompra.fk_bodega);

                this.formCompra.compSustento = this.listaSustentos.find(item => String(item.sus_codigo) === String(dataCompra.cod_sustento));

                this.formCompra.compCentroCosto = buscarPorId(this.listaCentroCostos, dataCompra.fk_centro_costo);

                this.formCompra.compTipoCompra = buscarPorId(this.listaTiposCompra, dataCompra.fk_tipo_compra);

                this.formCompra.compTipoCosto = buscarPorId(this.listaTiposCostos, dataCompra.tipo_costo);

                this.formCompra.compEsGasto = Number(dataCompra.comp_es_gasto) === 1;

                this.formCompra.compEstado = dataCompra.comp_estado;

                this.formCompra.compObservaciones = dataCompra.comp_observacion || '';

                this.formCompra.compPermitirDuplicados = dataCompra.comp_items_duplicados === 'true';

                this.formCompra.tieneOdc = Boolean(dataCompra.fk_orden_compra);

                this.formCompra.compODC = dataCompra.fk_orden_compra ?
                        {
                            id: dataCompra.fk_orden_compra,
                            value: `#${dataCompra.fk_orden_compra}`
                        } : null;
            },

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

            async abrirModalFinalizar() {

                const camposRequeridos = [
                    {valor: this.formCompra.compTipoComprobante, mensaje: 'Debe seleccionar el tipo de comprobante'},
                    {valor: this.formCompra.compSustento, mensaje: 'Debe seleccionar el tipo de sustento'},
                    {valor: this.formCompra.compTipoCompra, mensaje: 'Debe seleccionar el tipo de compra'},
                    {valor: this.formCompra.compNumeroEstablecimiento, mensaje: 'Debe ingresar el número de establecimiento'},
                    {valor: this.formCompra.compNumeroEmision, mensaje: 'Debe ingresar el punto de emisión'},
                    {valor: this.formCompra.compNumeroComprobante, mensaje: 'Debe ingresar el número de factura'},
                    {valor: this.formCompra.compFechaCaducidad, mensaje: 'Debe seleccionar la fecha de caducidad del comprobante'},
                    {valor: this.formCompra.compAutSRI, mensaje: 'Debe ingresar la autorización SRI'},
                    {valor: this.formCompra.compProveedor, mensaje: 'Debe seleccionar un proveedor'},
                    {valor: this.formCompra.compFechaEmision, mensaje: 'Debe seleccionar la fecha de emisión'},
                    {valor: this.formCompra.compBodega, mensaje: 'Debe seleccionar una bodega'},
                    {valor: this.formCompra.compCentroCosto, mensaje: 'Debe seleccionar un centro de costos'},
                    {valor: this.formCompra.compTipoCosto, mensaje: 'Debe seleccionar el tipo de costo'},
                    {valor: this.formCompra.compEstado, mensaje: 'Debe seleccionar el estado de la compra'}
                ];

                const campoFaltante = camposRequeridos.find(campo => {
                    if (campo.valor === null || campo.valor === undefined) {
                        return true;
                    }

                    return typeof campo.valor === 'string' && campo.valor.trim() === '';
                });

                if (campoFaltante) {
                    sweet_msg_toast('warning', campoFaltante.mensaje);
                    return;
                }

                if (this.formCompra.tieneOdc && !this.formCompra.compODC) {
                    sweet_msg_toast('warning', 'Debe seleccionar una orden de compra');
                    return;
                }

                if (!this.listaCartData.length) {
                    sweet_msg_toast('warning', 'Debe agregar al menos un producto o servicio');
                    return;
                }

                const requiereFormaPagoAts =
                        this.formCompra.compEstado === 'ARCHIVADO' &&
                        Number(this.totales.totalGeneral) >= Number(this.valorMaximoATSSRI);

                if (requiereFormaPagoAts && (!Array.isArray(this.ats.formaPago) || !this.ats.formaPago.length)) {
                    sweet_msg_toast('warning', 'Debe seleccionar al menos una forma de pago ATS');
                    return;
                }

                const itemInvalido = this.listaCartData.find(item =>
                    Number(item.qty ?? item.cantidad) <= 0 || Number(item.price ?? item.precio) <= 0
                );

                if (itemInvalido) {
                    sweet_msg_toast('warning', `Revise la cantidad y el precio del producto ${itemInvalido.name ?? itemInvalido.codigo}`);
                    return;
                }

                //Cuando la compra se guarda en pendiente solo registra la compra y el detalle, omite todo proceso contable y kardex
                if (this.formCompra.compEstado === 'BORRADOR') {
                    await this.guardarCompra();
                    return;
                }

                const aplicaRetencion = Boolean(this.formRetencion.compAplicaRetencion);
                const noSujetoRetencion = Boolean(this.formRetencion.compNoSujetoRetecion);

                if (!aplicaRetencion && !noSujetoRetencion) {
                    sweet_msg_toast('warning', 'Debe indicar si la compra aplica retención');
                    return;
                }

                if (aplicaRetencion && !noSujetoRetencion) {
                    const camposRetencion = [
                        {
                            valor: this.formRetencion.retNumeroEstablecimiento,
                            mensaje: 'Debe ingresar el número de establecimiento de la retención'
                        },
                        {
                            valor: this.formRetencion.retNumeroEmision,
                            mensaje: 'Debe ingresar el punto de emisión de la retención'
                        },
                        {
                            valor: this.formRetencion.retNumeroComprobante,
                            mensaje: 'Debe ingresar el número de comprobante de la retención'
                        },
                        {
                            valor: this.formRetencion.retFechaEmision,
                            mensaje: 'Debe seleccionar la fecha de emisión de la retención'
                        },
                        {
                            valor: this.formRetencion.retAutorizacionSri,
                            mensaje: 'Debe ingresar la autorización SRI de la retención'
                        }
                    ];

                    const campoRetencionFaltante = camposRetencion.find(campo =>
                        campo.valor === null ||
                                campo.valor === undefined ||
                                (typeof campo.valor === 'string' && campo.valor.trim() === '')
                    );

                    if (campoRetencionFaltante) {
                        sweet_msg_toast('warning', campoRetencionFaltante.mensaje);
                        return;
                    }

                    if (!this.listaRetencionesSeleccionadas.length) {
                        sweet_msg_toast('warning', 'Debe aplicar al menos una retención antes de finalizar');
                        return;
                    }

                    const retencionInvalida = this.listaRetencionesSeleccionadas.some(retencion =>
                        Number(retencion.base) <= 0 || Number(retencion.valorRetenido) <= 0
                    );

                    if (retencionInvalida) {
                        sweet_msg_toast('warning', 'Revise las bases y valores de las retenciones aplicadas');
                        return;
                    }
                }

                this.modalPagoInstance.show();
            },
            generarCuotas() {

                let total = Number(this.totalPagarCompra);
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
            },
            validarCuotas() {

                delete this.erroresPago.listaCuotas;
                delete this.erroresPago.totalCuotas;

                if (!this.pagos.listaCuotas.length) {
                    this.erroresPago.listaCuotas = 'Debe generar al menos una cuota';
                    return false;
                }

                let suma = this.pagos.listaCuotas.reduce((acc, c) => acc + parseFloat(c.valor || 0), 0);

                let total = Number(this.totalPagarCompra);

                if (parseFloat(suma.toFixed(4)) !== parseFloat(total.toFixed(4))) {
                    this.erroresPago.totalCuotas = 'La suma de las cuotas debe ser igual al valor neto a pagar';
                    return false;
                }

                return true;
            },

            validarDatosPago() {
                this.erroresPago = {};

                if (!this.pagos.tipoPago) {
                    this.erroresPago.tipoPago = 'Debe seleccionar la forma de pago';
                }

                if (Number(this.totalPagarCompra) <= 0) {
                    this.erroresPago.totalPagar = 'El valor a pagar debe ser mayor a cero';
                }

                if (this.pagos.tipoPago === 'CREDITO') {
                    if (Number(this.pagos.cuotas) <= 0) {
                        this.erroresPago.cuotas = 'Debe ingresar un número de cuotas válido';
                    }

                    if (Number(this.pagos.dias) <= 0) {
                        this.erroresPago.dias = 'Debe ingresar los días de crédito';
                    }

                    if (!this.pagos.fechaVenceCredito) {
                        this.erroresPago.fechaVenceCredito = 'Debe seleccionar la fecha de vencimiento';
                    }

                    this.pagos.listaCuotas.forEach((cuota, index) => {
                        if (!cuota.fecha) {
                            this.erroresPago[`cuotaFecha_${index}`] = 'Seleccione la fecha';
                        }
                        if (Number(cuota.valor) <= 0) {
                            this.erroresPago[`cuotaValor_${index}`] = 'Ingrese un valor mayor a cero';
                        }
                    });

                    this.validarCuotas();
                }

                if (this.pagos.tipoPago === 'CONTADO') {
                    const camposPagoContado = [
                        {
                            campo: 'formaPago',
                            valor: this.pagos.formaPago,
                            mensaje: 'Debe seleccionar el método de pago'
                        },
                        {
                            campo: 'cuentaContablePago',
                            valor: this.pagos.cuentaContablePago,
                            mensaje: 'Debe seleccionar la cuenta contable del pago'
                        }
                    ];

                    const formaPago = this.pagos.formaPago?.cod;

                    if (formaPago === '01') {
                        camposPagoContado.push({
                            campo: 'nota',
                            valor: this.pagos.nota,
                            mensaje: 'Debe ingresar una nota para el pago en efectivo'
                        });
                    } else if (formaPago === '02') {
                        camposPagoContado.push(
                                {campo: 'banco', valor: this.pagos.banco, mensaje: 'Debe ingresar el banco de la transferencia'},
                                {campo: 'numeroTransferencia', valor: this.pagos.numeroTransferencia, mensaje: 'Debe ingresar el número de transferencia'},
                                {campo: 'fechaTransferencia', valor: this.pagos.fechaTransferencia, mensaje: 'Debe seleccionar la fecha de transferencia'},
                                {campo: 'nota', valor: this.pagos.nota, mensaje: 'Debe ingresar una nota para la transferencia'}
                        );
                    } else if (formaPago === '03') {
                        camposPagoContado.push(
                                {campo: 'banco', valor: this.pagos.banco, mensaje: 'Debe ingresar el banco del cheque'},
                                {campo: 'numeroCheque', valor: this.pagos.numeroCheque, mensaje: 'Debe ingresar el número de cheque'},
                                {campo: 'fechaCheque', valor: this.pagos.fechaCheque, mensaje: 'Debe seleccionar la fecha del cheque'}
                        );
                    } else if (formaPago === '04') {
                        camposPagoContado.push(
                                {campo: 'marcaTarjeta', valor: this.pagos.marcaTarjeta, mensaje: 'Debe seleccionar la marca de la tarjeta'},
                                {campo: 'loteTarjeta', valor: this.pagos.loteTarjeta, mensaje: 'Debe ingresar el lote de la tarjeta'},
                                {campo: 'autorizacionTarjeta', valor: this.pagos.autorizacionTarjeta, mensaje: 'Debe ingresar la autorización de la tarjeta'},
                                {campo: 'ultimosDigitos', valor: this.pagos.ultimosDigitos, mensaje: 'Debe ingresar los últimos cuatro dígitos de la tarjeta'},
                                {campo: 'fechaVoucher', valor: this.pagos.fechaVoucher, mensaje: 'Debe seleccionar la fecha del voucher'},
                                {campo: 'nota', valor: this.pagos.nota, mensaje: 'Debe ingresar una nota para el pago con tarjeta'}
                        );
                    }

                    camposPagoContado.forEach(campo => {
                        const estaVacio =
                                campo.valor === null ||
                                campo.valor === undefined ||
                                (typeof campo.valor === 'string' && campo.valor.trim() === '');

                        if (estaVacio) {
                            this.erroresPago[campo.campo] = campo.mensaje;
                        }
                    });

                    if (
                            formaPago === '04' &&
                            this.pagos.ultimosDigitos &&
                            !/^\d{4}$/.test(String(this.pagos.ultimosDigitos))
                            ) {
                        this.erroresPago.ultimosDigitos = 'Ingrese exactamente cuatro números';
                    }
                }

                return Object.keys(this.erroresPago).length === 0;
            },

            resetFormulario() {

                this.listaCartData = [];

                this.pagos = {
                    formaPago: null,
                    tipoPago: 'CONTADO',
                    cuotas: 1,
                    dias: 0,
                    fechaVenceCredito: '',
                    listaCuotas: [],
                    cuentaContablePago: null,
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
                    fechaVoucher: ''
                };
                this.formRetencion = {
                    asumirRetencion: 'NO_ASUMIR',
                    compAplicaRetencion: true,
                    compNoSujetoRetecion: false,
                    baseIvaBienes: 0,
                    baseIvaServicios: 0,
                    baseRenta: 0,
                    retNumeroComprobante: '',
                    retNumeroEstablecimiento: '',
                    retNumeroEmision: '',
                    retFechaEmision: '',
                    retAutorizacionSri: '',
                    retDetalle: {}
                };
                this.listaRetencionesSeleccionadas = [];
                this.retencionBienes = '';
                this.retencionServicios = '';
                this.retencionRenta = '';
                this.erroresPago = {};

                this.totales = {
                    totalArticles: 0,
                    totalItems: 0,
                    totalSubtotalBruto: 0,
                    totalBienes: 0,
                    totalServicios: 0,
                    tarifCeroNeto: 0,
                    tarifIvaNeto: 0,
                    totalIva: 0,
                    totalIce: 0,
                    totalIrbpnr: 0,
                    totalDescuentoGlobal: 0,
                    totalDescuentoItems: 0,
                    totalSubtotalNeto: 0,
                    totalGeneral: 0,
                    tarifNoObjetoNeto: 0,
                    tarifExcentoNeto: 0
                };

                this.modalPagoInstance.hide();

            },

            async guardarCompra() {

                try {
                    const esArchivado = this.formCompra.compEstado === 'ARCHIVADO';

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

                    if (esArchivado && !this.validarDatosPago()) {
                        return;
                    }

                    // =========================
                    // ARMAR OBJETO COMPRA
                    // =========================
                    const compra = {
                        compFechaEmision: this.formCompra.compFechaEmision,
                        compTipoComprobante: this.formCompra.compTipoComprobante?.comp_codigo ?? null,
                        compTipoComprobanteId: this.formCompra.compTipoComprobante?.id ?? null,
                        compNumeroComprobante: this.formCompra.compNumeroComprobante?.trim(),
                        compNumeroEstablecimiento: this.formCompra.compNumeroEstablecimiento?.trim(),
                        compNumeroEmision: this.formCompra.compNumeroEmision?.trim(),
                        compFechaCaducidad: this.formCompra.compFechaCaducidad,
                        compAutSRI: this.formCompra.compAutSRI?.trim(),
                        compProveedor: this.formCompra.compProveedor?.id,
                        compBodega: this.formCompra.compBodega?.id,
                        compSustento: this.formCompra.compSustento?.sus_codigo ?? null,
                        compCentroCosto: this.formCompra.compCentroCosto?.id ?? null,
                        compTipoCompra: this.formCompra.compTipoCompra?.id ?? null,
                        compTipoCosto: this.formCompra.compTipoCosto?.id ?? null,
                        compODC: this.formCompra.compODC?.id ?? null,
                        compEsGasto: Boolean(this.formCompra.compEsGasto),
                        compEstado: this.formCompra.compEstado,
                        compObservaciones: this.formCompra.compObservaciones?.trim() ?? '',
                        compPermitirDuplicados: Boolean(this.formCompra.compPermitirDuplicados),
                        compTotal: Number(this.totales.totalGeneral || 0)
                    };

                    if (esArchivado) {
                        Object.assign(compra, {
                            compFormaPago: this.pagos.formaPago?.cod ?? null,
                            compTipoPago: this.pagos.tipoPago,
                            compCuotas: Number(this.pagos.cuotas || 0),
                            compDiasCredito: Number(this.pagos.dias || 0),
                            compTotalRetenido: Number(this.totalRetenidoCompra || 0),
                            compTotalPagar: Number(this.totalPagarCompra || 0)
                        });
                    }

                    // =========================
                    // ARMAR DETALLE
                    // =========================
                    const detalle = this.listaCartData.map(item => ({
                            rowId: item.rowid,
                            productoId: Number(item.id),
                            codigo: item.codigo,
                            nombre: item.name,
                            cantidad: Number(item.qty || 0),
                            precioBruto: Number(item.price || 0),
                            descuentoValor: Number(item.discountValue || 0),
                            descuentoPorcentaje: Number(item.discountPercent || 0),
                            precioNeto: Number(item.priceNeto || 0),
                            subtotalBruto: Number(item.subtotalBruto || 0),
                            subtotalNeto: Number(item.subtotalNeto || 0),
                            icePorcentaje: Number(item.icePorcent || 0),
                            iceValorUnitario: Number(item.iceValUnit || 0),
                            iceValorTotal: Number(item.iceValTotal || 0),
                            impuestoTarifaId: item.impuestoSelect ?? null,
                            impuestoCodigo: item.codigoImpuestoSelect ?? null,
                            ivaPorcentaje: Number(item.ivaPorcent || 0),
                            ivaValorUnitario: Number(item.ivaValUnit || 0),
                            ivaValorTotal: Number(item.ivaValTotal || 0),
                            baseIvaUnitario: Number(item.itemBaseIvaUnit || 0),
                            baseIvaTotal: Number(item.itemBaseIvaTotal || 0),
                            irbpnrUnitario: Number(item.irbpnrUnitario || 0),
                            irbpnrTotal: Number(item.irbpnr_total || 0),
                            total: Number(item.total || 0),
                            cuentaContable: item.ctaContableProducto ?? null,
                            centroCosto: item.centroCosto ?? null,
                            controlaLote: Number(item.tieneLote || 0),
                            lote: item.lote?.trim() || null,
                            fechaElaboracion: item.fechaElaboracion || null,
                            fechaCaducidad: item.fechaCaducidad || null,
                            esServicio: Number(item.servicio || 0)
                        }));

                    const ats = {
                        residente: this.ats.residente,
                        formasPago: esArchivado ? (this.ats.formaPago || []).map(forma => forma.codigo) : []
                    };

                    let retencion = null;

                    if (esArchivado) {
                        retencion = {
                            aplica: Boolean(this.formRetencion.compAplicaRetencion),
                            noSujeto: Boolean(this.formRetencion.compNoSujetoRetecion),
                            asumir: this.formRetencion.asumirRetencion,
                            numeroComprobante: this.formRetencion.retNumeroComprobante?.trim() || null,
                            numeroEstablecimiento: this.formRetencion.retNumeroEstablecimiento?.trim() || null,
                            numeroEmision: this.formRetencion.retNumeroEmision?.trim() || null,
                            fechaEmision: this.formRetencion.retFechaEmision || null,
                            autorizacionSri: this.formRetencion.retAutorizacionSri?.trim() || null,
                            totalRetenido: Number(this.totalRetenidoCompra || 0),
                            detalles: this.listaRetencionesSeleccionadas.map(item => ({
                                    retencionId: Number(item.id),
                                    tipo: item.ret_impuesto,
                                    detalle: item.ret_impuesto_detalle,
                                    codigoSri: item.ret_codigo,
                                    descripcion: item.ret_nombre,
                                    porcentaje: Number(item.ret_porcentaje || 0),
                                    baseImponible: Number(item.base || 0),
                                    valorRetenido: Number(item.valorRetenido || 0)
                                }))
                        };
                    }

                    // =========================
                    // CUOTAS (SI APLICA)
                    // =========================
                    let cuotas = [];

                    if (esArchivado && this.pagos.tipoPago === 'CREDITO') {
                        cuotas = this.pagos.listaCuotas.map(c => ({
                                numero: Number(c.numero),
                                fecha: c.fecha,
                                valor: Number(c.valor || 0),
                                saldo: Number(c.valor || 0)
                            }));
                    }

                    const valoresGlobales = {
                        descuentoGlobal: Number(this.global.descuentoGlobal || 0),
                        recargo: Number(this.global.recargo || 0),
                        serviciosAdicionales: Number(this.global.serviciosAdc || 0)
                    };

                    const totales = {
                        subtotalBruto: Number(this.totales.totalSubtotalBruto || 0),
                        descuentoItems: Number(this.totales.totalDescuentoItems || 0),
                        descuentoGlobal: Number(this.totales.totalDescuentoGlobal || 0),
                        subtotalNeto: Number(this.totales.totalSubtotalNeto || 0),
                        iva: Number(this.totales.totalIva || 0),
                        ice: Number(this.totales.totalIce || 0),
                        irbpnr: Number(this.totales.totalIrbpnr || 0),
                        recargo: Number(this.global.recargo || 0),
                        serviciosAdicionales: Number(this.global.serviciosAdc || 0),
                        totalFactura: Number(this.totales.totalGeneral || 0),
                        totalRetenido: Number(this.totalRetenidoCompra || 0),
                        totalPagar: Number(this.totalPagarCompra || 0)
                    };

                    const basesImpuestos = this.basesImpuestoVista.map(base => ({
                            codigo: base.codigo,
                            detalle: base.detalle,
                            porcentaje: Number(base.porcentaje || 0),
                            subtotal_bruto: Number(base.subtotal_bruto || 0),
                            subtotal_neto: Number(base.subtotal_neto || 0),
                            iva: Number(base.iva || 0)
                        }));

                    // =========================
                    // PAYLOAD FINAL
                    // =========================
                    const payload = {
                        compra,
                        detalle,
                        valoresGlobales,
                        totales,
                        basesImpuestos,
                        ats
                    };

                    if (this.isEdit) {
                        payload.idCompra = Number(this.idCompra);
                    }

                    if (esArchivado) {
                        payload.retencion = retencion;
                        payload.cuotas = cuotas;
                        payload.pago = {
                            tipoPago: this.pagos.tipoPago,
                            formaPago: this.pagos.formaPago?.cod ?? null,
                            cuentaContable: this.pagos.cuentaContablePago?.ctad_codigo ?? null,
                            banco: this.pagos.banco ? {
                                codigo: this.pagos.banco.codigo,
                                nombre: this.pagos.banco.nombre
                            } : null,
                            nota: this.pagos.nota?.trim() || null,
                            numeroTransferencia: this.pagos.numeroTransferencia?.trim() || null,
                            fechaTransferencia: this.pagos.fechaTransferencia || null,
                            numeroCheque: this.pagos.numeroCheque?.trim() || null,
                            fechaCheque: this.pagos.fechaCheque || null,
                            marcaTarjeta: this.pagos.marcaTarjeta || null,
                            loteTarjeta: this.pagos.loteTarjeta?.trim() || null,
                            autorizacionTarjeta: this.pagos.autorizacionTarjeta?.trim() || null,
                            ultimosDigitos: this.pagos.ultimosDigitos?.trim() || null,
                            fechaVoucher: this.pagos.fechaVoucher || null,
                            numeroCuotas: Number(this.pagos.cuotas || 0),
                            diasCredito: Number(this.pagos.dias || 0),
                            fechaVencimiento: this.pagos.fechaVenceCredito || null,
                            totalFactura: Number(this.totales.totalGeneral || 0),
                            totalRetenido: Number(this.totalRetenidoCompra || 0),
                            totalPagar: Number(this.totalPagarCompra || 0)
                        };
                    }

                    // =========================
                    // ENVÍO
                    // =========================
                    this.loadingProcess = true;

                    const formData = new FormData();
                    formData.append('data', JSON.stringify(payload));

                    const ruta = this.isEdit ? '/compras/updateCompra' : '/compras/saveCompra';
                    const {data} = await axios.post(this.url + ruta, formData);

                    if (data.status === 'success') {
                        const url = this.url + '/compras/nuevaCompra';
                        this.modalPagoInstance?.hide();
                        sweetMsgDialogConfirm(data.msg, this.verDetalle, data.data, url);
                    } else if (data.status === 'warning') {
                        sweet_msg_dialog('warning', data.msg || 'Alerta al guardar');
                    } else {
                        sweet_msg_dialog('error', data.msg || 'Error al guardar');

                    }

                } catch (e) {

                    sweet_msg_dialog('error', 'Error en el sistema al procesar la compra');

                } finally {
                    this.loadingProcess = false;
                }
            },

            async verDetalle(compra) {
                this.idCompra = compra.id;
                this.secuencialCompra = compra.comp_secuencial;
                this.detalleHtml = '';
                this.cargandoDetalle = true;
                this.modalReporteInstance.show();

                try {
                    const {data} = await axios.get(`${this.url}/compras/getDataDetalle/${compra.id}`);
                    this.detalleHtml = data;
                } catch (e) {
                    this.modalReporteInstance.hide();
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.cargandoDetalle = false;
                }
            },

            generarExcel() {
                const contenido = document.getElementById('contentExport');
                const titulo = `Compra_${this.zFill(this.secuencialCompra, 5)}`;

                return generarExcelContent(contenido, titulo);
            },

            generarPDF() {
                window.open(`${this.url}/compras/generarPDF/${this.idCompra}?download=1`, '_blank');
            },

            async changeBodega() {

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
            },

            // =========================
            // BUSCAR PRODUCTOS
            // =========================
            onRemove() {
                this.listaSearchProductos = [];
                this.productoSeleccionado = null;
                this.codeSearch = "";
            },

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
            async insertProductCode(evt) {
                if (evt.target.value === "") {
                    sweet_msg_toast('warning', 'Por favor digite un código');
                    return false;
                }
                let datos = {id: evt.target.value};
                await this.insertProductCart(datos);

            },

            // =========================
            // AGREGAR PRODUCTO AL CART
            // =========================
            async insertProductCart(item) {
                this.onRemove();//Removemos datos del anterior producto insertado

                let datos = {
                    id: item.id,
                    qty: 1,
                    permitirDuplicados: this.formCompra.compPermitirDuplicados

                };

                try {
                    this.loading = true;

                    let {data} = await axios.post(this.url + '/compras/insertProduct', datos);
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

                try {
                    this.loading = true;

                    let {data} = await axios.post(this.url + '/compras/updateProduct', datos);
                    sweet_msg_toast(data.status, data.msg);

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                } finally {
                    this.loading = false;
                }

                this.showDetailCart();
            },

            // =========================
            // ELIMINAR ITEM
            // =========================            
            async deleteProduct(rowId) {
                try {
                    this.loading = true;
                    const {data} = await axios.get(this.url + '/compras/deleteProduct/' + rowId);
                    if (data.status === 'success') {
                        sweet_msg_toast('info', data.msg);
                        this.showDetailCart();

                    } else {
                        sweet_msg_toast(data.status, data.msg);
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                } finally {
                    this.loading = false;
                }
            },

            // =========================
            // CALCULO DATA DE CADA ITEM
            // =========================
            async showDetailCart() {

                try {
                    let {data} = await axios.post(this.url + '/compras/showDetailCart');

                    //TODO DATOS LISTAS
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

                    //Lista de impuestos (12,15,5, etc)
                    this.basesImpuesto = data.basesImpuesto;
                    this.formRetencion.baseIvaBienes = Number(data.ivaBienes || 0);
                    this.formRetencion.baseIvaServicios = Number(data.ivaServicios || 0);
                    this.formRetencion.baseRenta = Number(data.baseRenta || 0);

                    this.global = {
                        descuentoGlobal: data.totalDescuentoGlobal,
                        recargo: data.totalRecargo,
                        serviciosAdc: data.totalServiciosAdc
                    };


                    if (data.totalArticles > 0) {
                        this.emptyCar = false;
                    } else {
                        this.emptyCar = true;
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                }

            },
            changeTipoDescuento(item, tipo) {
                item.tipoDescuento = tipo;
                item.descuento = 0;

//                this.updateProductCart(item);

            },

            async cancelarCompra() {
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
                            const {data} = await axios.post(this.url + '/compras/cancelarCompra');
                            if (data.status === 'success') {
                                this.showDetailCart();
                                this.clear();
                                window.history.pushState({}, '', this.url + '/compras/nuevaCompra');
                                sweet_msg_toast('success', data.msg);
                            } else {
                                sweet_msg_dialog('error', data.msg);
                            }
                        } catch (e) {
                            sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                        } finally {
                            this.loading = false;
                        }
                    }
                });


            },

            clear() {
                this.isEdit = false;
                this.formCompra = {
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
                };
                this.global = {
                    descuentoGlobal: 0,
                    recargo: 0,
                    serviciosAdc: 0
//                    otrosCargos: 0

                };
                this.totales = {
                    totalArticles: 0,
                    totalItems: 0,
                    totalSubtotalBruto: 0,
                    totalBienes: 0,
                    totalServicios: 0,
                    tarifCeroNeto: 0,
                    tarifIvaNeto: 0,
                    totalIva: 0,
                    totalIce: 0,
                    totalIrbpnr: 0,
                    totalDescuentoGlobal: 0,
                    totalDescuentoItems: 0,
                    totalSubtotalNeto: 0,
                    totalGeneral: 0,
                    tarifNoObjetoNeto: 0,
                    tarifExcentoNeto: 0
                };
                this.basesImpuesto = [];
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

                const base = retencion.ret_impuesto_detalle === 'IVA_BIENES'
                        ? this.formRetencion.baseIvaBienes
                        : this.formRetencion.baseIvaServicios;

                this.listaRetencionesSeleccionadas.push({
                    ...retencion,
                    base: base,
                    valorRetenido: this.calcularValorRetenido(
                            base,
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

            },

            async updateValoresGlobales() {

                try {
                    const {data} = await  axios.post(this.url + '/compras/updateValoresGlobales', this.global);
                    if (data.status === 'success') {
                        if (data.status === 'success') {
                            this.showDetailCart();
                            sweet_msg_toast('success', data.msg);
                        } else {
                            sweet_msg_dialog('warning', data.msg);
                        }
                    }
                } catch (e) {
                    sweet_msg_dialog('error', 'Error al actualizar los valores globales');
                }

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






