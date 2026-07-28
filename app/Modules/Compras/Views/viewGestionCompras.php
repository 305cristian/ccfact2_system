<!DOCTYPE html>
<!--
/**
 * Description of viewGestionCompras
 *
 * @author CRISTIAN R. PAZ
 * @date 3 may 2026
 */
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title text-system">
                <i class="fas fa-file-invoice-dollar me-2"></i> Gestion de Compras
            </h5>
        </div>

        <div class="card-body">
            <div class="row mb-3">

                <div class="col-md-3 form-group-custom">
                    <div class="input-group">
                        <span class="input-group-text bg-cris-system">
                            <i class="fas fa-calendar me-2"></i> F. emision
                        </span>
                        <input
                            ref="dateRangeEmision"
                            v-model="filtros.compFechasEmision"
                            type="text"
                            class="form-control"
                            placeholder="Rango de emision">
                    </div>
                </div>

                <div class="col-md-3 form-group-custom">
                    <div class="input-group">
                        <span class="input-group-text bg-cris-system">
                            <i class="fas fa-calendar-check me-2"></i> F. archivado
                        </span>
                        <input
                            ref="dateRangeArchivado"
                            v-model="filtros.compFechasArchivado"
                            type="text"
                            class="form-control"
                            placeholder="Rango de archivado">
                    </div>
                </div>

                <div class="col-md-2 form-group-custom">
                    <div class="input-group">
                        <span class="input-group-text bg-cris-system">
                            <i class="fas fa-hashtag me-2"></i> Compra
                        </span>
                        <input
                            v-model.trim="filtros.compSecuencial"
                            @keyup.enter="searchCompras"
                            type="number"
                            min="1"
                            class="form-control"
                            placeholder="Ej. 25">
                    </div>
                </div>

                <div class="col-md-3 form-group-custom">
                    <div class="input-group">
                        <span class="input-group-text bg-cris-system">
                            <i class="fas fa-file-invoice me-2"></i> N. comprobante
                        </span>
                        <input
                            v-model.trim="filtros.compComprobante"
                            @keyup.enter="searchCompras"
                            type="text"
                            class="form-control"
                            placeholder="Ej. 000000001">
                    </div>
                </div>

                <div class="col-md-4 form-group-custom">
                    <div class="d-flex align-items-center border rounded overflow-visible">
                        <span class="input-group-text bg-cris-system">
                            <i class="fas fa-user-tie me-2"></i> Proveedor
                        </span>
                        <vue-select
                            class="flex-grow-1"
                            :options="listaProveedores"
                            label="prov_razon_social"
                            v-model="filtros.compProveedor"
                            :reduce="proveedor => proveedor.id"
                            @search="searchProveedor"
                            placeholder="RUC o razon social">
                            <template #no-options>
                                Digite para buscar un proveedor
                            </template>
                        </vue-select>
                    </div>
                </div>

                <div class="col-md-4 form-group-custom">
                    <div class="d-flex align-items-center border rounded overflow-visible">
                        <span class="input-group-text bg-cris-system">
                            <i class="fas fa-warehouse me-2"></i> Bodega
                        </span>
                        <vue-select
                            class="flex-grow-1"
                            :options="listaBodegas"
                            label="bod_nombre"
                            v-model="filtros.compBodega"
                            :reduce="bodega => bodega.id"
                            placeholder="Todas las bodegas">
                        </vue-select>
                    </div>
                </div>

                <div class="col-md-4 form-group-custom">
                    <div class="d-flex align-items-center border rounded overflow-visible">
                        <span class="input-group-text bg-cris-system">
                            <i class="fas fa-project-diagram me-2"></i> Centro de costo
                        </span>
                        <vue-select
                            class="flex-grow-1"
                            :options="listaCentroCostos"
                            label="cc_nombre"
                            v-model="filtros.compCentroCosto"
                            :reduce="centro => centro.id"
                            placeholder="Todos los centros">
                        </vue-select>
                    </div>
                </div>

                <div class="col-md-4 form-group-custom">
                    <div class="d-flex align-items-center border rounded overflow-visible">
                        <span class="input-group-text bg-cris-system">
                            <i class="fas fa-file-invoice me-2"></i> Tipo comprobante
                        </span>
                        <vue-select
                            class="flex-grow-1"
                            :options="listaTiposComprobantes"
                            label="comp_nombre"
                            v-model="filtros.compTipoComprobante"
                            :reduce="comprobante => comprobante.comp_codigo"
                            placeholder="Todos los comprobantes">
                            <template #option="comprobante">
                                {{ comprobante.comp_codigo }} - {{ comprobante.comp_nombre }}
                            </template>
                            <template #selected-option="comprobante">
                                {{ comprobante.comp_codigo }} - {{ comprobante.comp_nombre }}
                            </template>
                        </vue-select>
                    </div>
                </div>

                <div class="col-md-4 form-group-custom">
                    <div class="d-flex align-items-center border rounded overflow-visible">
                        <span class="input-group-text bg-cris-system">
                            <i class="fas fa-coins me-2"></i> Tipo de costo
                        </span>
                        <vue-select
                            class="flex-grow-1"
                            :options="listaTiposCostos"
                            label="label"
                            v-model="filtros.compTipoCosto"
                            :reduce="tipo => tipo.value"
                            placeholder="Todos los tipos">
                        </vue-select>
                    </div>
                </div>

                <div class="col-md-2 form-group-custom">
                    <button
                        type="button"
                        class="btn btn-outline-system w-100"
                        :disabled="loading"
                        @click="searchCompras">
                        <i class="fas fa-search me-1"></i> Buscar
                    </button>
                </div>

                <div class="col-md-2 form-group-custom">
                    <button
                        type="button"
                        class="btn btn-outline-secondary w-100"
                        :disabled="loading"
                        @click="limpiarFiltros">
                        <i class="fas fa-eraser me-1"></i> Limpiar
                    </button>
                </div>
            </div>

            <ul class="nav nav-pills mb-3">
                <li class="nav-item" v-for="estado in estados" :key="estado.value">
                    <button
                        type="button"
                        class="nav-link border-bottom"
                        :class="{active: estadoActivo === estado.value}"
                        @click="cambiarEstado(estado.value)">
                        <i :class="estado.icon"></i>
                        {{ estado.label }}
                        <span v-if="loadingContadores" class="badge ms-2" :class="estado.badge">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                        <span v-else class="badge ms-2" :class="estado.badge">
                            {{ contadores[estado.value] ?? 0 }}
                        </span>
                    </button>
                </li>
            </ul>

            <hr>

            <div v-if="loading" class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-system"></i>
                <div class="mt-2 text-muted">Cargando compras...</div>
            </div>

            <div v-else-if="listaCompras.length" class="table-responsive">
                <table id="tblCompras" class="table table-striped table-hover w-100">
                    <thead class="bg-system text-white">
                        <tr>
                            <th>ACCIONES</th>
                            <th>CODIGO</th>
                            <th>FECHA DE EMISIÓN</th>
                            <th>TIPO COMPROBANTE</th>
                            <th>COMPROBANTE</th>
                            <th>PROVEEDOR</th>
                            <th>RUC</th>
                            <th>BODEGA</th>
                            <th>CENTRO DE COSTO</th>
                            <th>TIPO DE PAGO</th>
                            <th class="text-end">TOTAL</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="compra in listaCompras" :key="compra.id">
                            <td>
                                <div class="dropdown">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <button class="dropdown-item" @click="verDetalle(compra)">
                                                <i class="fas fa-eye me-2"></i> Ver detalle
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item" @click="openModalEmail(compra)">
                                                <i class="fas fa-envelope me-2"></i> Enviar por email
                                            </button>
                                        </li>
                                        <li v-if="compra.comp_estado === 'ARCHIVADO'">
                                            <button class="dropdown-item" @click="verAsientoContable(compra)">
                                                <i class="fas fa-balance-scale me-2"></i> Ver asiento contable
                                            </button>
                                        </li>
                                        <li v-if="compra.comp_estado === 'BORRADOR'">
                                            <button
                                                class="dropdown-item text-warning"
                                                @click="loadCompraEdit(compra.id)">
                                                <i class="fas fa-edit me-2"></i> Modificar compra
                                            </button>
                                        </li>
                                        <li v-if="compra.comp_estado === 'ARCHIVADO'">
                                            <button
                                                class="dropdown-item text-primary"
                                                @click="edicionRapidaCompra(compra)">
                                                <i class="fas fa-edit me-2"></i> Edición rápida
                                            </button>
                                        </li>
                                        <li v-if="compra.comp_estado === 'ARCHIVADO'">
                                            <button
                                                class="dropdown-item"
                                                @click="editarCentrosCostos(compra)">
                                                <i class="fas fa-project-diagram me-2"></i> Editar centros de costos
                                            </button>
                                        </li>
                                        <li v-if="compra.comp_estado === 'ARCHIVADO'">
                                            <button
                                                class="dropdown-item"
                                                @click="editarLotes(compra)">
                                                <i class="fas fa-boxes me-2"></i> Editar lotes
                                            </button>
                                        </li>
                                        <li>
                                            <button
                                                class="dropdown-item"
                                                @click="clonarCompra(compra.id)">
                                                <i class="fas fa-clone me-2"></i> Clonar compra
                                            </button>
                                        </li>
                                        <li v-if="compra.comp_estado === 'ARCHIVADO' && ['01', '02', '03'].includes(String(compra.comp_tipo_comprobante_cod))">
                                            <button
                                                class="dropdown-item"
                                                @click="generarNotaCredito(compra)">
                                                <i class="fas fa-file-invoice me-2"></i> Generar nota de crédito
                                            </button>
                                        </li>
                                        <li v-if="compra.comp_estado === 'ARCHIVADO' && compra.comp_tipo_comprobante_cod === '04'">
                                            <button
                                                class="dropdown-item text-danger"
                                                @click="anularNotaCredito(compra)">
                                                <i class="fas fa-ban me-2"></i> Anular nota de crédito
                                            </button>
                                        </li>
                                        <li v-if="['BORRADOR', 'ARCHIVADO'].includes(compra.comp_estado) && compra.comp_tipo_comprobante_cod !== '04'">
                                            <button
                                                class="dropdown-item text-danger"
                                                @click="anularCompra(compra)">
                                                <i class="fas fa-ban me-2"></i> Anular compra
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td>{{ zFill(compra.comp_secuencial, 5) }}</td>
                            <td>{{ compra.comp_fecha_emision }}</td>
                            <td>
                                <span class="badge rounded-pill border" :style="styleTipoComprobante(compra.comp_tipo_comprobante_cod)">
                                     {{ compra.tipo_comprobante ?? '-' }}
                                </span>
                            </td>
                            <td>{{ numeroComprobante(compra) }}</td>
                            <td>{{ compra.proveedor }}</td>
                            <td>{{ compra.prov_ruc }}</td>
                            <td>{{ compra.bodega ?? '-' }}</td>
                            <td>{{ compra.centro_costo ?? '-' }}</td>
                            <td>{{ compra.comp_tipo_pago ?? '-' }}</td>
                            <td class="text-end">{{ formatToUSD(compra.comp_total) }}</td>
                            <td>
                                <span class="badge" :class="badgeEstado(compra.comp_estado)">
                                    <i :class="iconEstado(compra.comp_estado)"></i>
                                    {{ labelEstado(compra.comp_estado) }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                    <span class="small fw-bold text-muted me-1">Leyenda:</span>
                    <span
                        v-for="tipo in leyendaTiposComprobante"
                        :key="tipo.codigo"
                        class="badge rounded-pill border"
                        :style="tipo.style">
                        {{ tipo.codigo }} - {{ tipo.label }}
                    </span>
                </div>
            </div>

            <div v-else class="text-center text-muted py-5">
                <i class="fas fa-search fa-2x mb-2"></i>
                <div>No se encontraron compras con los filtros seleccionados.</div>
            </div>
        </div>
    </div>

    <?php echo view('\Modules\Compras\Views\viewEdicionLotes') ?>
    <?php echo view('\Modules\Compras\Views\viewEdicionCentroCostos') ?>
    <?php echo view('\Modules\Compras\Views\viewEdicionRapida') ?>
    <?php echo view('\Modules\Compras\Views\reportes\viewModalReport') ?>

    <div ref="modalSendEmail" class="modal fade" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-fullscreen-md-down modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-dark">
                    <h5 class="modal-title mb-0">
                        <i class="fas fa-envelope me-2"></i> Enviar compra por email
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body bg-light">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Para <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                v-model.trim="emailData.para"
                                class="form-control"
                                placeholder="correo@empresa.com">
                            <small class="text-muted">Puede ingresar varios correos separados por coma.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">CC</label>
                            <input
                                type="text"
                                v-model.trim="emailData.cc"
                                class="form-control"
                                placeholder="(opcional)">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Asunto <span class="text-danger">*</span></label>
                            <input
                                type="text"
                                v-model.trim="emailData.asunto"
                                class="form-control"
                                placeholder="Reporte de Compra #00001">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Mensaje</label>
                            <textarea
                                v-model="emailData.mensaje"
                                class="form-control"
                                rows="5"
                                placeholder="Escriba un mensaje adicional..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <div v-if="errorSendMail" class="text-danger fw-semibold me-auto" v-html="errorSendMail"></div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cerrar
                    </button>
                    <button type="button" class="btn btn-primary" :disabled="loadingEmail" @click="sendEmailReport">
                        <span v-if="loadingEmail">
                            <i class="fas fa-spinner fa-spin me-2"></i> Enviando...
                        </span>
                        <span v-else>
                            <i class="fas fa-paper-plane me-2"></i> Enviar Email
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var fechaDesde = DateTime.now().toFormat('yyyy-MM-01');
    var fechaHasta = DateTime.now().toFormat('yyyy-MM-dd');
    var listaBodegas = <?= json_encode($listaBodegas); ?>;
    var listaCentroCostos = <?= json_encode($listaCentroCostos); ?>;
    var listaTiposComprobantes = <?= json_encode($listaTiposComprobantes); ?>;
    var listaTiposCompra = <?= json_encode($listaTiposCompra); ?>;
    var listaSustentos = <?= json_encode($listaSustentos); ?>;

    if (window.appGestionCompras) {
        window.appGestionCompras.unmount();
    }

    window.appGestionCompras = Vue.createApp({
        components: {
            'vue-select': window['vue-select']
        },
        data() {
            return {
                url: siteUrl,
                loading: false,
                loadingContadores: false,
                searchTimeout: null,
                estadoActivo: 'ARCHIVADO',
                estados: [
                    {value: 'BORRADOR', label: 'Borradores', icon: 'fas fa-edit', badge: 'bg-warning'},
                    {value: 'ARCHIVADO', label: 'Archivadas', icon: 'fas fa-check', badge: 'bg-success'},
//                    {value: 'ANULADA', label: 'Anuladas', icon: 'fas fa-ban', badge: 'bg-danger'},
                    {value: 'ANULADA_EN_PENDIENTE', label: 'Anuladas en borrador', icon: 'fas fa-file-alt', badge: 'bg-secondary'},
                    {value: 'ANULADA_EN_ARCHIVADA', label: 'Anuladas', icon: 'fas fa-ban', badge: 'bg-danger'}
                ],
                filtros: {
                    compFechasEmision: `${fechaDesde} a ${fechaHasta}`,
                    compFechasArchivado: '',
                    compSecuencial: '',
                    compComprobante: '',
                    compProveedor: null,
                    compBodega: null,
                    compCentroCosto: null,
                    compTipoComprobante: null,
                    compTipoCosto: null,
                    compEstado: 'BORRADOR'
                },
                listaBodegas: listaBodegas,
                listaCentroCostos: listaCentroCostos,
                listaTiposComprobantes: listaTiposComprobantes,
                listaTiposComprobantesEdicionRapida: listaTiposComprobantes.filter(comprobante => ['01', '02'].includes(String(comprobante.comp_codigo))),
                listaTiposCompra: listaTiposCompra,
                listaSustentos: listaSustentos,
                listaTiposCostos: [
                    {value: 'DIRECTOS', label: 'DIRECTOS'},
                    {value: 'INDIRECTOS', label: 'INDIRECTOS'}
                ],
                leyendaTiposComprobante: [
                    {codigo: '01', label: 'Factura', style: {backgroundColor: '#e8f1fb', color: '#1f5f99', borderColor: '#b8d5ee'}},
                    {codigo: '02', label: 'Nota de venta', style: {backgroundColor: '#e8f6f3', color: '#176f5d', borderColor: '#b7ded5'}},
                    {codigo: '03', label: 'Liquidacion de compra', style: {backgroundColor: '#fff4db', color: '#8a5a00', borderColor: '#efd38c'}},
                    {codigo: '04', label: 'Nota de credito', style: {backgroundColor: '#f1ecfb', color: '#5b3f91', borderColor: '#d3c4ee'}}
                ],
                listaProveedores: [],
                listaCompras: [],
                contadores: {},
                idCompra: null,
                secuencialCompra: null,
                cargandoDetalle: false,
                detalleHtml: '',
                modalTitulo: 'Detalle de Compra',
                mostrarBotonesReporte: true,
                modalInstance: null,
                modalEdicionRapidaInstance: null,
                modalCentrosCostosInstance: null,
                modalLotesInstance: null,
                modalInstanceEmail: null,
                loadingEdicionRapida: false,
                loadingCentrosCostos: false,
                loadingGuardarCentrosCostos: false,
                loadingLotes: false,
                loadingGuardarLotes: false,
                loadingEmail: false,
                compraEdicionRapida: null,
                compraCentrosCostos: null,
                compraLotes: null,
                emailData: {
                    para: '',
                    cc: '',
                    asunto: '',
                    mensaje: ''
                },
                errorSendMail: '',
                formEdicionRapida: {
                    compraId: null,
                    compTipoComprobante: null,
                    compNumeroEstablecimiento: '',
                    compNumeroEmision: '',
                    compNumeroComprobante: '',
                    compAutSRI: '',
                    compFechaEmision: '',
                    compFechaCaducidad: '',
                    compSustento: null,
                    compTipoCompra: null,
                    compTipoCosto: null,
                    compODC: '',
                    compObservaciones: ''
                },
                erroresEdicionRapida: {},
                formCentrosCostos: {
                    compraId: null,
                    centroCostoId: null,
                    detalles: []
                },
                erroresCentrosCostos: {},
                formLotes: {
                    compraId: null,
                    detalles: []
                },
                erroresLotes: {}
            };
        },
        mounted() {
            flatpickr(this.$refs.dateRangeEmision, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                locale: 'es',
                allowInput: true,
                defaultDate: [fechaDesde, fechaHasta],
                onChange: (_, dateStr) => {
                    this.filtros.compFechasEmision = dateStr;
                }
            });

            flatpickr(this.$refs.dateRangeArchivado, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                locale: 'es',
                allowInput: true,
                onChange: (_, dateStr) => {
                    this.filtros.compFechasArchivado = dateStr;
                }
            });

            this.modalInstance = new bootstrap.Modal(this.$refs.modalReport);
            this.modalEdicionRapidaInstance = new bootstrap.Modal(this.$refs.modalEdicionRapida);
            this.modalCentrosCostosInstance = new bootstrap.Modal(this.$refs.modalCentrosCostos);
            this.modalLotesInstance = new bootstrap.Modal(this.$refs.modalLotes);
            this.modalInstanceEmail = new bootstrap.Modal(this.$refs.modalSendEmail);
            this.searchCompras();
        },
        methods: {
            cambiarEstado(estado) {
                this.estadoActivo = estado;
                this.filtros.compEstado = estado;
                this.searchCompras();
            },
            async searchCompras() {
                try {
                    this.loading = true;
                    this.listaCompras = [];

                    const {data} = await axios.post(this.url + '/compras/searchCompras', this.filtros);

                    this.listaCompras = data.status === 'success' ? data.data : [];

                    await Vue.nextTick();

                    if (this.listaCompras.length) {
                        dataTable('#tblCompras', 'Listado de compras');
                    }

                    this.cargarContadores();
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loading = false;
                }
            },
            async cargarContadores() {
                try {
                    const datos = {
                        compFechasEmision: this.filtros.compFechasEmision,
                        compFechasArchivado: this.filtros.compFechasArchivado
                    };

                    this.loadingContadores = true;
                    const {data} = await axios.post(this.url + '/compras/contadoresCompras', datos);

                    if (data.status === 'success') {
                        this.contadores = data.data;
                    }

                } catch (e) {
                    this.contadores = {};
                } finally {
                    this.loadingContadores = false;
                }
            },

            searchProveedor(search) {
                console.log(search);
                clearTimeout(this.searchTimeout);

                if (!search || search.trim().length < 2) {
                    if (!this.filtros.compProveedor) {
                        this.listaProveedores = [];
                    }
                    return;
                }

                this.searchTimeout = setTimeout(async () => {
                    try {
                        const datos = {
                            dataSerach: search.trim()
                        };
                        const {data} = await axios.post(this.url + '/comun/proveedores/searchProveedor', datos);
                        this.listaProveedores = data || [];
                    } catch (e) {
                        this.listaProveedores = [];
                    }
                }, 400);
            },
            limpiarFiltros() {
                this.estadoActivo = 'BORRADOR';
                this.filtros = {
                    compFechasEmision: `${fechaDesde} a ${fechaHasta}`,
                    compFechasArchivado: '',
                    compSecuencial: '',
                    compComprobante: '',
                    compProveedor: null,
                    compBodega: null,
                    compCentroCosto: null,
                    compTipoComprobante: null,
                    compTipoCosto: null,
                    compEstado: 'BORRADOR'
                };
                this.$refs.dateRangeEmision._flatpickr.setDate([fechaDesde, fechaHasta], false);
                this.$refs.dateRangeArchivado._flatpickr.clear();
                this.searchCompras();
            },
            async loadCompraEdit(compraId) {
                try {
                    swalLoading('Cargando compra');

                    const {data} = await axios.get(`${this.url}/compras/loadCompraEdit/${compraId}`);

                    if (data.status === 'success') {
                        window.location.href = data.redirect;
                        return;
                    }
                    sweet_msg_dialog('error', data.msg);
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    Swal.close();
                }
            },
            async clonarCompra(compraId) {
                try {
                    swalLoading('Clonando compra');

                    const {data} = await axios.get(`${this.url}/compras/clonarCompra/${compraId}`);

                    if (data.status === 'success') {
                        window.location.href = data.redirect;
                        return;
                    }

                    sweet_msg_dialog('error', data.msg || 'No se pudo clonar la compra.');
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    Swal.close();
                }
            },
            async anularCompra(compra) {
                const {value: motivo} = await Swal.fire({
                    title: `Anular compra #${this.zFill(compra.comp_secuencial, 5)}`,
                    input: 'textarea',
                    inputLabel: 'Motivo de anulacion',
                    inputPlaceholder: 'Ingrese el motivo de anulacion...',
                    showCancelButton: true,
                    confirmButtonText: 'Anular',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#dc3545',
                    preConfirm: (value) => {
                        if (!value || !value.trim()) {
                            Swal.showValidationMessage('Debe ingresar un motivo de anulacion');
                            return false;
                        }

                        return value.trim();
                    }
                });

                if (!motivo) {
                    return;
                }

                try {
                    swalLoading('Anulando compra');
                    const datos = {
                        compraId: compra.id,
                        motivoAnulacion: motivo
                    };
                    const {data} = await axios.post(`${this.url}/compras/anularCompra`, datos);

                    Swal.close();

                    if (data.status === 'success') {
                        sweet_msg_dialog('success', data.msg);
                        await this.searchCompras();
                        return;
                    }
                    sweet_msg_dialog(data.status || 'warning', data.msg || 'No se pudo anular la compra.');
                } catch (e) {
                    Swal.close();
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                }
            },
            async anularNotaCredito(compra) {
                const {value: motivo} = await Swal.fire({
                    title: `Anular NDC #${this.zFill(compra.comp_secuencial, 5)}`,
                    input: 'textarea',
                    inputLabel: 'Motivo de anulacion',
                    inputPlaceholder: 'Ingrese el motivo de anulacion...',
                    showCancelButton: true,
                    confirmButtonText: 'Anular',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#dc3545',
                    preConfirm: (value) => {
                        if (!value || !value.trim()) {
                            Swal.showValidationMessage('Debe ingresar un motivo de anulacion');
                            return false;
                        }

                        return value.trim();
                    }
                });

                if (!motivo) {
                    return;
                }

                try {
                    swalLoading('Anulando nota de credito');
                    const datos = {
                        notaCreditoId: compra.id,
                        motivoAnulacion: motivo
                    };
                    const {data} = await axios.post(`${this.url}/notacredito/anularNotaCredito`, datos);

                    Swal.close();

                    if (data.status === 'success') {
                        sweet_msg_dialog('success', data.msg);
                        await this.searchCompras();
                        return;
                    }
                    sweet_msg_dialog(data.status || 'warning', data.msg || 'No se pudo anular la nota de credito.');
                } catch (e) {
                    Swal.close();
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                }
            },
            async verDetalle(compra) {
                this.idCompra = compra.id;
                this.secuencialCompra = compra.comp_secuencial;
                this.modalTitulo = 'Detalle de Compra';
                this.mostrarBotonesReporte = true;
                this.detalleHtml = '';
                this.cargandoDetalle = true;
                this.modalInstance.show();

                try {
                    const {data} = await axios.get(`${this.url}/compras/getDataDetalle/${compra.id}`);
                    this.detalleHtml = data;
                } catch (e) {
                    this.modalInstance.hide();
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.cargandoDetalle = false;
                }
            },
            async verAsientoContable(compra) {
                this.idCompra = compra.id;
                this.secuencialCompra = compra.comp_secuencial;
                this.modalTitulo = `Asiento contable - Compra #${this.zFill(compra.comp_secuencial, 5)}`;
                this.mostrarBotonesReporte = false;
                this.detalleHtml = '';
                this.cargandoDetalle = true;
                this.modalInstance.show();

                try {
                    const {data} = await axios.get(`${this.url}/compras/getAsientoContable/${compra.id}`);

                    if (data.status !== 'success') {
                        this.modalInstance.hide();
                        sweet_msg_dialog(data.status || 'warning', data.msg || 'No se pudo cargar el asiento contable.');
                        return;
                    }

                    this.detalleHtml = data.data;
                } catch (e) {
                    this.modalInstance.hide();
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.cargandoDetalle = false;
                }
            },
            generarNotaCredito(compra) {
                window.location.href = `${this.url}/notacredito/nuevaNdc/${compra.id}`;
                return;
            },
            edicionRapidaCompra(compra) {
                this.compraEdicionRapida = compra;
                this.erroresEdicionRapida = {};
                this.formEdicionRapida = {
                    compraId: Number(compra.id),
                    compTipoComprobante: compra.comp_tipo_comprobante_cod || null,
                    compNumeroEstablecimiento: compra.comp_numero_establecimiento || '',
                    compNumeroEmision: compra.comp_numero_emision || '',
                    compNumeroComprobante: compra.comp_numero_comprobante || '',
                    compAutSRI: compra.comp_autorizacion_sri || '',
                    compFechaEmision: compra.comp_fecha_emision || '',
                    compFechaCaducidad: compra.comp_fecha_vencimiento_autorizacion || '',
                    compSustento: compra.cod_sustento || null,
                    compTipoCompra: compra.fk_tipo_compra || null,
                    compTipoCosto: compra.tipo_costo || 'DIRECTOS',
                    compODC: compra.fk_orden_compra || '',
                    compObservaciones: compra.comp_observacion || ''
                };
                this.modalEdicionRapidaInstance.show();
            },
            validarEdicionRapida() {
                const errores = {};
                const requeridos = {
                    compTipoComprobante: 'Seleccione el tipo de comprobante.',
                    compNumeroEstablecimiento: 'Ingrese el punto de establecimiento.',
                    compNumeroEmision: 'Ingrese el punto de emision.',
                    compNumeroComprobante: 'Ingrese el numero de comprobante.',
                    compAutSRI: 'Ingrese la autorizacion SRI.',
                    compFechaEmision: 'Ingrese la fecha de emision.',
                    compFechaCaducidad: 'Ingrese la fecha de vencimiento de autorizacion.',
                    compSustento: 'Seleccione el sustento tributario.',
                    compTipoCompra: 'Seleccione el tipo de compra.',
                    compTipoCosto: 'Seleccione el tipo de costo.'
                };

                Object.entries(requeridos).forEach(([campo, mensaje]) => {
                    const valor = this.formEdicionRapida[campo];
                    if (valor === null || valor === undefined || String(valor).trim() === '') {
                        errores[campo] = mensaje;
                    }
                });

                if (this.formEdicionRapida.compNumeroEstablecimiento && !/^\d{1,3}$/.test(this.formEdicionRapida.compNumeroEstablecimiento)) {
                    errores.compNumeroEstablecimiento = 'Maximo 3 digitos.';
                }

                if (this.formEdicionRapida.compNumeroEmision && !/^\d{1,3}$/.test(this.formEdicionRapida.compNumeroEmision)) {
                    errores.compNumeroEmision = 'Maximo 3 digitos.';
                }

                if (this.formEdicionRapida.compNumeroComprobante && !/^\d{1,9}$/.test(this.formEdicionRapida.compNumeroComprobante)) {
                    errores.compNumeroComprobante = 'Maximo 9 digitos.';
                }

                if (this.formEdicionRapida.compTipoComprobante && !['01', '02'].includes(String(this.formEdicionRapida.compTipoComprobante))) {
                    errores.compTipoComprobante = 'Comprobante no permitido.';
                }

                this.erroresEdicionRapida = errores;
                return Object.keys(errores).length === 0;
            },
            async guardarEdicionRapida() {
                if (!this.validarEdicionRapida()) {
                    return;
                }

                try {
                    this.loadingEdicionRapida = true;
                    const {data} = await axios.post(`${this.url}/compras/updateEdicionRapida`, this.formEdicionRapida);

                    if (data.status === 'success') {
                        this.modalEdicionRapidaInstance.hide();
                        sweet_msg_dialog('success', data.msg);
                        await this.searchCompras();
                        return;
                    }

                    sweet_msg_dialog(data.status || 'warning', data.msg || 'No se pudo actualizar la compra.');
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loadingEdicionRapida = false;
                }
            },
            editarCentrosCostos(compra) {
                this.compraCentrosCostos = compra;
                this.erroresCentrosCostos = {};
                this.formCentrosCostos = {
                    compraId: Number(compra.id),
                    centroCostoId: compra.fk_centro_costo || null,
                    detalles: []
                };
                this.modalCentrosCostosInstance.show();
                this.cargarCentrosCostosCompra(compra.id);
            },
            async cargarCentrosCostosCompra(compraId) {
                try {
                    this.loadingCentrosCostos = true;
                    const {data} = await axios.get(`${this.url}/compras/getCentrosCostosCompra/${compraId}`);

                    if (data.status !== 'success') {
                        this.modalCentrosCostosInstance.hide();
                        sweet_msg_dialog(data.status || 'warning', data.msg || 'No se pudieron cargar los centros de costo.');
                        return;
                    }

                    this.formCentrosCostos.centroCostoId = data.data.compra.fk_centro_costo || null;
                    this.formCentrosCostos.detalles = (data.data.detalle || []).map(detalle => ({
                        id: Number(detalle.id),
                        codigo: detalle.prod_codigo,
                        producto: detalle.prod_nombre,
                        cantidad: Number(detalle.compd_cantidad || 0),
                        centroCostoId: detalle.compd_centro_costo || null
                    }));
                } catch (e) {
                    this.modalCentrosCostosInstance.hide();
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loadingCentrosCostos = false;
                }
            },
            validarCentrosCostos() {
                const errores = {
                    detalles: {}
                };

                if (!this.formCentrosCostos.centroCostoId) {
                    errores.centroCostoId = 'Seleccione el centro de costo global.';
                }

                if (!this.formCentrosCostos.detalles.length) {
                    errores.detallesGeneral = 'La compra no tiene detalles para actualizar.';
                }

                this.formCentrosCostos.detalles.forEach(detalle => {
                    if (!detalle.centroCostoId) {
                        errores.detalles[detalle.id] = 'Seleccione un centro.';
                    }
                });

                this.erroresCentrosCostos = errores;
                return !errores.centroCostoId && !errores.detallesGeneral && Object.keys(errores.detalles).length === 0;
            },
            async guardarCentrosCostos() {
                if (!this.validarCentrosCostos()) {
                    return;
                }

                const payload = {
                    compraId: this.formCentrosCostos.compraId,
                    centroCostoId: this.formCentrosCostos.centroCostoId,
                    detalles: this.formCentrosCostos.detalles.map(detalle => ({
                        id: detalle.id,
                        centroCostoId: detalle.centroCostoId
                    }))
                };

                try {
                    this.loadingGuardarCentrosCostos = true;
                    const {data} = await axios.post(`${this.url}/compras/updateCentrosCostosCompra`, payload);

                    if (data.status === 'success') {
                        this.modalCentrosCostosInstance.hide();
                        sweet_msg_dialog('success', data.msg);
                        await this.searchCompras();
                        return;
                    }

                    sweet_msg_dialog(data.status || 'warning', data.msg || 'No se pudieron actualizar los centros de costo.');
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loadingGuardarCentrosCostos = false;
                }
            },
            editarLotes(compra) {
                this.compraLotes = compra;
                this.erroresLotes = {};
                this.formLotes = {
                    compraId: Number(compra.id),
                    detalles: []
                };
                this.modalLotesInstance.show();
                this.cargarLotesCompra(compra.id);
            },
            async cargarLotesCompra(compraId) {
                try {
                    this.loadingLotes = true;
                    const {data} = await axios.get(`${this.url}/compras/getLotesCompra/${compraId}`);

                    if (data.status !== 'success') {
                        this.modalLotesInstance.hide();
                        sweet_msg_dialog(data.status || 'warning', data.msg || 'No se pudieron cargar los lotes.');
                        return;
                    }

                    this.formLotes.detalles = (data.data.detalle || []).map(detalle => ({
                        id: Number(detalle.id),
                        loteId: detalle.fk_lote ? Number(detalle.fk_lote) : null,
                        productoId: Number(detalle.fk_producto),
                        codigo: detalle.prod_codigo,
                        producto: detalle.prod_nombre,
                        cantidad: Number(detalle.compd_cantidad || 0),
                        lote: detalle.lot_lote || detalle.compd_lote || '',
                        fechaElaboracion: detalle.lot_fecha_elaboracion || detalle.compd_fecha_elaboracion || '',
                        fechaCaducidad: detalle.lot_fecha_caducidad || detalle.compd_fecha_caducidad || ''
                    }));
                } catch (e) {
                    this.modalLotesInstance.hide();
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loadingLotes = false;
                }
            },
            validarLotes() {
                const errores = {
                    detalles: {}
                };

                if (!this.formLotes.detalles.length) {
                    errores.detallesGeneral = 'La compra no tiene items con control de lote.';
                }

                this.formLotes.detalles.forEach(detalle => {
                    const itemErrores = {};

                    if (!detalle.lote || String(detalle.lote).trim() === '') {
                        itemErrores.lote = 'Ingrese el lote.';
                    }

                    if (!detalle.fechaElaboracion) {
                        itemErrores.fechaElaboracion = 'Ingrese la fecha.';
                    }

                    if (!detalle.fechaCaducidad) {
                        itemErrores.fechaCaducidad = 'Ingrese la fecha.';
                    }

                    if (detalle.fechaElaboracion && detalle.fechaCaducidad && detalle.fechaElaboracion > detalle.fechaCaducidad) {
                        itemErrores.fechaCaducidad = 'La caducidad debe ser mayor.';
                    }

                    if (Object.keys(itemErrores).length) {
                        errores.detalles[detalle.id] = itemErrores;
                    }
                });

                this.erroresLotes = errores;
                return !errores.detallesGeneral && Object.keys(errores.detalles).length === 0;
            },
            async guardarLotes() {
                if (!this.validarLotes()) {
                    return;
                }

                const payload = {
                    compraId: this.formLotes.compraId,
                    detalles: this.formLotes.detalles.map(detalle => ({
                        id: detalle.id,
                        loteId: detalle.loteId,
                        lote: detalle.lote,
                        fechaElaboracion: detalle.fechaElaboracion,
                        fechaCaducidad: detalle.fechaCaducidad
                    }))
                };

                try {
                    this.loadingGuardarLotes = true;
                    const {data} = await axios.post(`${this.url}/compras/updateLotesCompra`, payload);

                    if (data.status === 'success') {
                        this.modalLotesInstance.hide();
                        sweet_msg_dialog('success', data.msg);
                        await this.searchCompras();
                        return;
                    }

                    sweet_msg_dialog(data.status || 'warning', data.msg || 'No se pudieron actualizar los lotes.');
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loadingGuardarLotes = false;
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

            openModalEmail(compra) {
                this.idCompra = compra.id;
                this.secuencialCompra = compra.comp_secuencial;
                this.errorSendMail = '';
                this.emailData = {
                    para: compra.prov_email || '',
                    cc: '',
                    asunto: `Reporte de Compra #${this.zFill(compra.comp_secuencial, 5)}`,
                    mensaje: 'Estimado(a), adjunto el reporte solicitado.'
                };
                this.modalInstanceEmail.show();
            },

            async sendEmailReport() {
                this.errorSendMail = '';

                if (!this.emailData.para || !this.emailData.asunto) {
                    this.errorSendMail = 'Debe completar los campos obligatorios (Para y Asunto).';
                    return;
                }

                const datos = {
                    ...this.emailData,
                    idCompra: this.idCompra
                };

                try {
                    this.loadingEmail = true;
                    const {data} = await axios.post(`${this.url}/compras/sendEmailReport`, datos);

                    if (data.status === 'success') {
                        this.modalInstanceEmail.hide();
                        sweet_msg_dialog('success', data.msg);
                        return;
                    }

                    this.errorSendMail = data.msg || 'No se pudo enviar el correo.';
                } catch (e) {
                    this.errorSendMail = 'Error al enviar email: ' + (e.response?.data?.message || e.message);
                } finally {
                    this.loadingEmail = false;
                }
            },

            numeroComprobante(compra) {
                return [
                    compra.comp_numero_establecimiento,
                    compra.comp_numero_emision,
                    compra.comp_numero_comprobante
                ].filter(Boolean).join('-');
            },

            badgeEstado(estado) {
                const mapa = {
                    BORRADOR: 'bg-warning',
                    ARCHIVADO: 'bg-success',
                    ANULADA: 'bg-danger',
                    ANULADA_EN_PENDIENTE: 'bg-secondary',
                    ANULADA_EN_ARCHIVADA: 'bg-dark'
                };
                return mapa[estado] || 'bg-secondary';
            },

            iconEstado(estado) {
                const mapa = {
                    BORRADOR: 'fas fa-edit',
                    ARCHIVADO: 'fas fa-check',
                    ANULADA: 'fas fa-ban',
                    ANULADA_EN_PENDIENTE: 'fas fa-file-alt',
                    ANULADA_EN_ARCHIVADA: 'fas fa-archive'
                };
                return mapa[estado] || 'fas fa-question';
            },

            labelEstado(estado) {
                const mapa = {
                    BORRADOR: 'BORRADOR',
                    ARCHIVADO: 'ARCHIVADA',
                    ANULADA: 'ANULADA',
                    ANULADA_EN_PENDIENTE: 'ANULADA EN BORRADOR',
                    ANULADA_EN_ARCHIVADA: 'ANULADA ARCHIVADA'
                };
                return mapa[estado] || estado;
            },
            styleTipoComprobante(codigo) {
                const mapa = {
                    '01': {backgroundColor: '#e8f1fb', color: '#1f5f99', borderColor: '#b8d5ee'},
                    '02': {backgroundColor: '#e8f6f3', color: '#176f5d', borderColor: '#b7ded5'},
                    '03': {backgroundColor: '#fff4db', color: '#8a5a00', borderColor: '#efd38c'},
                    '04': {backgroundColor: '#f1ecfb', color: '#5b3f91', borderColor: '#d3c4ee'}
                };

                return mapa[String(codigo)] || {backgroundColor: '#eef0f2', color: '#495057', borderColor: '#d4d8dc'};
            },
            formatToUSD(amount) {
                return formatToUSD(amount);
            },
            zFill(value, size) {
                return zFill(value, size);
            }
        }
    });
    window.appGestionCompras.use(AllDirectives);
    window.appGestionCompras.mount('#app');
</script>
