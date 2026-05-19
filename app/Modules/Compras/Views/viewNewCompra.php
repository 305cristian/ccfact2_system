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
            <h5 v-if="isEdit" class="card-title text-system"><i class="fas fa-folder-blank"></i> Actualizar Ajuste de Entrada</h5>
            <h5 v-else class="card-title text-system"><i class="fas fa-folder-blank"></i> Nuevo Ajuste de Entrada</h5>
        </div>
        <div class="card-body">

            <fieldset>
                <legend>Datos Generales de Compra</legend>

                <div class="row">

                    <!-- Tipo Comprobante -->
                    <div class="col-md-3 form-group-custom">
                        <div class="d-flex justify-content-between align-items-center border">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-file-invoice me-2"></i> Comprobante
                            </span>
                            <vue-select 
                                class="flex-grow-1"
                                :options="listaTiposComprobantes"
                                label="nombre"
                                v-model="formCompra.compTipoComprobante"
                                placeholder="Seleccione tipo"/>
                        </div>
                    </div>

                    <!-- Fecha Emisión -->
                    <div class="col-md-2 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-calendar me-2"></i> Fecha
                            </span>
                            <input v-model="formCompra.compFechaEmision" type="date" class="form-control">
                        </div>
                    </div>

                    <!-- Número Comprobante -->
                    <div class="col-md-3 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-hashtag me-2"></i> N° Comprobante
                            </span>
                            <input v-model="formCompra.compNumero" type="text" class="form-control">
                        </div>
                    </div>

                    <!-- Autorización SRI -->
                    <div class="col-md-4 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-key me-2"></i> Aut. SRI
                            </span>
                            <input v-model="formCompra.compAutSRI" type="text" class="form-control">
                        </div>
                    </div>

                </div>

                <div class="row">

                    <!-- Proveedor -->
                    <div class="col-md-4 form-group-custom">
                        <div class="d-flex align-items-center">
                            <vue-multiselect
                                v-model="formCompra.compProveedor"
                                placeholder="Buscar proveedor"
                                label="prov_razon_social"
                                track-by="prov_ruc"
                                :options="listaProveedores"
                                @search-change="searchProveedor"/>
                            <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                        </div>
                    </div>

                    <!-- Bodega -->
                    <div class="col-md-2 form-group-custom">
                        <div class="d-flex align-items-center border">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-warehouse me-2"></i> Bodega
                            </span>
                            <vue-select 
                                class="flex-grow-1"
                                :options="listaBodegas"
                                label="bod_nombre"
                                v-model="formCompra.compBodega"/>
                        </div>
                    </div>

                    <!-- Sustento -->
                    <div class="col-md-3 form-group-custom">
                        <div class="d-flex align-items-center border">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-receipt me-2"></i> Sustento
                            </span>
                            <vue-select 
                                class="flex-grow-1"
                                :options="listaSustentos"
                                label="sus_nombre"
                                v-model="formCompra.compSustento"/>
                        </div>
                    </div>

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
                                v-model="formCompra.compCentroCosto"/>
                        </div>
                    </div>

                </div>

                <div class="row">

                    <!-- Tipo Compra -->
                    <div class="col-md-3 form-group-custom">
                        <div class="d-flex align-items-center border">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-tags me-2"></i> Tipo Compra
                            </span>
                            <vue-select 
                                class="flex-grow-1"
                                :options="listaTiposCompra"
                                label="descripcion"
                                v-model="formCompra.compTipoCompra"/>
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
                                label="nombre"
                                v-model="formCompra.compTipoCosto"/>
                        </div>
                    </div>

                    <!-- ODC -->
                    <div class="col-md-2 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">ODC</span>
                            <input v-model="formCompra.compODC" type="text" class="form-control">
                        </div>
                    </div>

                    <!-- Aplica Retención -->
                    <div class="col-md-2 form-group-custom d-flex align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" v-model="formCompra.compAplicaRetencion">
                            <label class="form-check-label">Aplica Retención</label>
                        </div>
                    </div>

                    <!-- Es gasto -->
                    <div class="col-md-2 form-group-custom d-flex align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" v-model="formCompra.compEsGasto">
                            <label class="form-check-label">Es gasto</label>
                        </div>
                    </div>

                </div>

                <div class="row">

                    <!-- Forma de pago -->
                    <div class="col-md-3 form-group-custom">
                        <div class="d-flex align-items-center border">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-credit-card me-2"></i> Forma Pago
                            </span>
                            <vue-select 
                                class="flex-grow-1"
                                :options="listaFormasPago"
                                label="nombre"
                                v-model="formCompra.compFormaPago"/>
                        </div>
                    </div>

                    <!-- Tipo pago -->
                    <div class="col-md-3 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-hand-holding-usd me-2"></i> Tipo
                            </span>
                            <select v-model="formCompra.compTipoPago" class="form-select">
                                <option value="CONTADO">CONTADO</option>
                                <option value="CREDITO">CRÉDITO</option>
                            </select>
                        </div>
                    </div>

                    <!-- Cuotas -->
                    <div class="col-md-2 form-group-custom" v-if="formCompra.compTipoPago === 'CREDITO'">
                        <div class="input-group">
                            <span class="input-group-text">Cuotas</span>
                            <input type="number" v-model="formCompra.compCuotas" class="form-control">
                        </div>
                    </div>

                    <!-- Días crédito -->
                    <div class="col-md-2 form-group-custom" v-if="formCompra.compTipoPago === 'CREDITO'">
                        <div class="input-group">
                            <span class="input-group-text">Días</span>
                            <input type="number" v-model="formCompra.compDiasCredito" class="form-control">
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

                <!-- Observaciones -->
                <div class="row">
                    <div class="col-md-12 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system">
                                <i class="fas fa-comments me-2"></i> Observaciones
                            </span>
                            <input v-model="formCompra.compObservaciones" type="text" class="form-control">
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
                                @select="agregarProductoCompra">

                                <template #option="{ option }">
                                    <div class="d-flex justify-content-between w-100">
                                        <span><strong>{{ option.prod_nombre }}</strong></span>
                                        <span class="badge bg-info">{{ option.prod_codigo }}</span>
                                    </div>
                                </template>

                            </vue-multiselect>

                            <span class="input-group-text">
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
                            <span class="input-group-text">
                                <i class="fas fa-barcode"></i>
                            </span>
                        </div>
                    </div>

                    <!-- CHECK DUPLICADOS -->
                    <div class="col-md-2 d-flex align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" v-model="permitirDuplicados">
                            <label class="form-check-label">Duplicados</label>
                        </div>
                    </div>

                </div>
            </fieldset>
            <br>
            <!--VIEW CART-->
            <?php echo view('\Modules\Compras\Views\viewCart') ?>
            <!--VIEW CART-->

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
    <?php echo view('\Modules\Compras\Views\viewPagosRetencion') ?>
    <!--CLOSE MODAL FINALIZAR COMPRA-->
</div>

<script type="text/javascript">

    var fechaActual = DateTime.now().toFormat('yyyy-MM-dd');
    var listaTiposCompra = <?= json_encode($listaTiposCompra); ?>;
    var listaTiposComprobantes = <?= json_encode($listaTiposComprobantes); ?>;
    var listaFormasPago = <?= json_encode($listaFormasPago); ?>;
    var listaSustentos = <?= json_encode($listaSustentos); ?>;
    var listaBodegas = <?= json_encode($listaBodegas); ?>;
    var listaCentroCostos = <?= json_encode($listaCentroCostos); ?>;
    var permitirDuplicados = <?= $permitirDuplicados ?>;
    var bodegaIdComp = '<?= $bodegaId; ?>';
    var dataCompra =<?= json_encode($dataCompra); ?>;
    var dataProveedor =<?= json_encode($dataProveedor); ?>;

    var ivaPrdeterminado =<?= ivaPredeterminado() ?>

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
                    compNumero: '',
                    compAutSRI: '',
                    compProveedor: '',
                    compBodega: '',
                    compSustento: '',
                    compCentroCosto: '',
                    compTipoCompra: '',
                    compTipoCosto: '',
                    compODC: '',
                    compAplicaRetencion: false,
                    compEsGasto: false,
                    compFormaPago: '',
                    compTipoPago: 'CONTADO',
                    compCuotas: 1,
                    compDiasCredito: 0,
                    compEstado: 'BORRADOR',
                    compObservaciones: ''
                },

                // =========================
                // LISTAS
                // =========================
                listaProveedores: [],
                listaBodegas: listaBodegas,
                listaSustentos: listaSustentos,
                listaCentroCostos: listaCentroCostos,
                listaTiposCompra: listaTiposCompra,
                listaTiposCostos: [{value: 'DIRECTOS', id: 'DIRECTOS'}, {value: 'INDIRECTOS', id: 'INDIRECTOS'}],
                listaFormasPago: listaFormasPago,
                listaTiposComprobantes: listaTiposComprobantes,
                listaIvas: [],

                // =========================
                // BUSCADOR PRODUCTOS
                // =========================
                listaSearchProductos: [],
                productoSeleccionado: null,
                codigoBusqueda: '',
                permitirDuplicados: false,

                // =========================
                // DETALLE
                // =========================
                listaCompra: [],

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

                // =========================
                // MODAL RETENCION PAGOS CUOTAS
                // =========================
                modal: {
                    compAplicaRetencion: false,

                    retNumero: '',
                    retAutorizacion: '',

                    formaPago: null,
                    tipoPago: 'CONTADO',

                    cuotas: 1,
                    dias: 0,

                    listaCuotas: []
                },
                modalPagoInstance: null

            };
        },
        mounted() {
            this.modalPagoInstance = new bootstrap.Modal(this.$refs.modalFinalizar).show();
        },
        watch: {
            'modal.listaCuotas': {
                deep: true,
                handler() {
                    this.validarCuotas();
                }
            }
        },

        methods: {

            abrirModalFinalizar() {

                if (!this.listaCompra.length) {
                    sweet_msg_toast('warning', 'Debe agregar productos');
                    return;
                }

                this.modalPagoInstance.show();
            },
            generarCuotas() {

                let total = parseFloat(this.totalGeneral);
                let cuotas = parseInt(this.modal.cuotas);
                let dias = parseInt(this.modal.dias);

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

                this.modal.listaCuotas = cuotasArray;

                sweet_msg_toast('success', 'Cuotas generadas correctamente');
            },
            validarCuotas() {

                if (!this.modal.listaCuotas.length) {
                    sweet_msg_toast('warning', 'Debe generar las cuotas');
                    return false;
                }

                let suma = this.modal.listaCuotas.reduce((acc, c) => acc + parseFloat(c.valor || 0), 0);

                let total = parseFloat(this.totalGeneral);

                if (parseFloat(suma.toFixed(4)) !== parseFloat(total.toFixed(4))) {
                    sweet_msg_toast('error', 'Las cuotas no cuadran con el total');
                    return false;
                }

                return true;
            },

            resetFormulario() {

                this.listaCompra = [];

                this.modal = {
                    compAplicaRetencion: false,
                    retNumero: '',
                    retAutorizacion: '',
                    formaPago: null,
                    tipoPago: 'CONTADO',
                    cuotas: 1,
                    dias: 0,
                    listaCuotas: []
                };

                this.totalGeneral = 0;
            },

            async guardarCompraCompleta() {

                try {

                    // =========================
                    // VALIDACIONES BÁSICAS
                    // =========================
                    if (!this.formCompra.compProveedor) {
                        sweet_msg_toast('warning', 'Seleccione proveedor');
                        return;
                    }

                    if (!this.listaCompra.length) {
                        sweet_msg_toast('warning', 'Debe agregar productos');
                        return;
                    }

                    // =========================
                    // VALIDAR CUOTAS
                    // =========================
                    if (this.modal.tipoPago === 'CREDITO') {
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
                        compFormaPago: this.modal.formaPago?.id,

                        compTipoPago: this.modal.tipoPago,
                        compCuotas: this.modal.cuotas,
                        compDiasCredito: this.modal.dias,

                        compTotal: this.totalGeneral,

                        // 🔥 RETENCION
                        compAplicaRetencion: this.modal.compAplicaRetencion,
                        retNumero: this.modal.retNumero,
                        retAutorizacion: this.modal.retAutorizacion
                    };

                    // =========================
                    // ARMAR DETALLE
                    // =========================
                    const detalle = this.listaCompra.map(i => ({
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

                    if (this.modal.tipoPago === 'CREDITO') {
                        cuotas = this.modal.listaCuotas.map(c => ({
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

                    console.error(e);
                    sweet_msg_toast('error', 'Error en el sistema');

                } finally {
                    this.loading = false;
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

                if (!this.codigoBusqueda)
                    return;

                try {
                    const {data} = await axios.post(
                            this.url + '/comun/productos/searchByCode',
                            {codigo: this.codigoBusqueda}
                    );

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

                this.listaCompra.push(nuevoItem);
                this.productoSeleccionado = null;

                this.calcularTotales();
            },

            // =========================
            // ELIMINAR ITEM
            // =========================
            eliminarItem(index) {
                this.listaCompra.splice(index, 1);
                this.calcularTotales();
            },

            // =========================
            // UPDATE ITEM
            // =========================
            updateItem(item) {

                if (item.cantidad <= 0)
                    item.cantidad = 1;
                if (item.precio < 0)
                    item.precio = 0;

                this.calcularItem(item);
                this.calcularTotales();
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

                this.listaCompra.forEach(i => {
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






