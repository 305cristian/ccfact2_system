<!DOCTYPE html>
<!--
/**
 * Description of viewGestionVentas
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 16 ago 2026
 * @time 9:41:24 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->


<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title text-system mb-0">
                <i class="fas fa-file-invoice-dollar me-2"></i> Gestion de Ventas
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
                            v-model="filtros.venFechasEmision"
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
                            v-model="filtros.venFechasArchivado"
                            type="text"
                            class="form-control"
                            placeholder="Rango de archivado">
                    </div>
                </div>

                <div class="col-md-2 form-group-custom">
                    <div class="input-group">
                        <span class="input-group-text bg-cris-system">
                            <i class="fas fa-hashtag me-2"></i> Venta
                        </span>
                        <input
                            v-model.trim="filtros.venSecuencial"
                            @keyup.enter="searchVentas"
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
                            v-model.trim="filtros.venComprobante"
                            @keyup.enter="searchVentas"
                            type="text"
                            class="form-control"
                            placeholder="Ej. 000000001">
                    </div>
                </div>

                <div class="col-md-4 form-group-custom">
                    <div class="d-flex align-items-center border rounded overflow-visible">
                        <span class="input-group-text bg-cris-system">
                            <i class="fas fa-user me-2"></i> Cliente
                        </span>
                        <vue-select
                            class="flex-grow-1"
                            :options="listaClientes"
                            label="clie_razon_social"
                            v-model="filtros.venCliente"
                            :reduce="cliente => cliente.id"
                            @search="searchCliente"
                            placeholder="CI/RUC o razon social">
                            <template #option="cliente">
                                {{ cliente.clie_razon_social }} - {{ cliente.clie_dni }}
                            </template>
                            <template #no-options>
                                Digite para buscar un cliente
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
                            v-model="filtros.venBodega"
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
                            v-model="filtros.venCentroCosto"
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
                            v-model="filtros.venTipoComprobante"
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

                <div class="col-md-2 form-group-custom">
                    <button type="button" class="btn btn-outline-system w-100" :disabled="loading" @click="searchVentas">
                        <i class="fas fa-search me-1"></i> Buscar
                    </button>
                </div>

                <div class="col-md-2 form-group-custom">
                    <button type="button" class="btn btn-outline-secondary w-100" :disabled="loading" @click="limpiarFiltros">
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
                <div class="mt-2 text-muted">Cargando ventas...</div>
            </div>

            <div v-else-if="listaVentas.length" class="table-responsive">
                <table id="tblVentas" class="table table-striped table-hover w-100">
                    <thead class="bg-system text-white">
                        <tr>
                            <th>ACCIONES</th>
                            <th>CODIGO</th>
                            <th>FECHA EMISION</th>
                            <th>TIPO COMPROBANTE</th>
                            <th>COMPROBANTE</th>
                            <th>CLIENTE</th>
                            <th>CI/RUC</th>
                            <th>BODEGA</th>
                            <th>CENTRO COSTO</th>
                            <th>TIPO VENTA</th>
                            <th>TIPO PAGO</th>
                            <th class="text-end">TOTAL</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="venta in listaVentas" :key="venta.id">
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <button class="dropdown-item" @click="verDetalle(venta)">
                                                <i class="fas fa-eye me-2"></i> Ver detalle
                                            </button>
                                        </li>
                                        <li v-if="venta.ven_estado === 'ARCHIVADO'">
                                            <button class="dropdown-item" @click="verAsientoContable(venta)">
                                                <i class="fas fa-balance-scale me-2"></i> Ver asiento contable
                                            </button>
                                        </li>
                                        <li v-if="venta.ven_estado === 'BORRADOR'">
                                            <button class="dropdown-item" @click="loadVentaEdit(venta.id)">
                                                <i class="fas fa-edit me-2"></i> Modificar venta
                                            </button>
                                        </li>
                                        <li v-if="['BORRADOR', 'ARCHIVADO'].includes(venta.ven_estado)">
                                            <button class="dropdown-item text-danger" @click="anularVenta(venta)">
                                                <i class="fas fa-ban me-2"></i> Anular venta
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td>{{ zFill(venta.ven_secuencial, 5) }}</td>
                            <td>{{ venta.ven_fecha_emision }}</td>
                            <td>
                                <span class="badge rounded-pill border" :style="styleTipoComprobante(venta.ven_tipo_comprobante_cod)">
                                    {{ venta.tipo_comprobante ?? '-' }}
                                </span>
                            </td>
                            <td>{{ numeroComprobante(venta) }}</td>
                            <td>{{ venta.cliente }}</td>
                            <td>{{ venta.clie_dni }}</td>
                            <td>{{ venta.bodega ?? '-' }}</td>
                            <td>{{ venta.centro_costo ?? '-' }}</td>
                            <td>{{ venta.tipo_venta_codigo ? venta.tipo_venta_codigo + ' - ' + venta.tipo_venta : '-' }}</td>
                            <td>
                                <span class="badge rounded-pill" :class="badgeTipoPago(venta.ven_tipo_pago)">
                                    <i :class="venta.ven_tipo_pago === 'CREDITO' ? 'fas fa-calendar-alt' : 'fas fa-cash-register'"></i>
                                    {{ labelTipoPago(venta.ven_tipo_pago) }}
                                </span>
                            </td>
                            <td class="text-end">{{ formatToUSD(venta.ven_total) }}</td>
                            <td>
                                <span class="badge" :class="badgeEstado(venta.ven_estado)">
                                    <i :class="iconEstado(venta.ven_estado)"></i>
                                    {{ labelEstado(venta.ven_estado) }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="text-center text-muted py-5">
                <i class="fas fa-search fa-2x mb-2"></i>
                <div>No se encontraron ventas con los filtros seleccionados.</div>
            </div>
        </div>
    </div>

    <?php echo view('\Modules\Ventas\Views\reportes\viewModalReport') ?>
</div>

<script type="text/javascript">
    var fechaDesde = DateTime.now().toFormat('yyyy-MM-01');
    var fechaHasta = DateTime.now().toFormat('yyyy-MM-dd');
    var listaBodegas = <?= json_encode($listaBodegas); ?>;
    var listaCentroCostos = <?= json_encode($listaCentroCostos); ?>;
    var listaTiposComprobantes = <?= json_encode($listaTiposComprobantes); ?>;
    var tipoComprobanteFactura = listaTiposComprobantes.find(comprobante => String(comprobante.comp_codigo) === '01')?.comp_codigo ?? null;

    if (window.appGestionVentas) {
        window.appGestionVentas.unmount();
    }

    window.appGestionVentas = Vue.createApp({
        components: {
            'vue-select': window['vue-select']
        },
        data() {
            return {
                url: siteUrl,
                loading: false,
                loadingContadores: false,
                searchTimeout: null,
                estadoActivo: 'BORRADOR',
                estados: [
                    {value: 'BORRADOR', label: 'Borradores', icon: 'fas fa-edit', badge: 'bg-warning'},
                    {value: 'ARCHIVADO', label: 'Archivadas', icon: 'fas fa-check', badge: 'bg-success'},
                    {value: 'ANULADA_EN_PENDIENTE', label: 'Anuladas en borrador', icon: 'fas fa-file-alt', badge: 'bg-secondary'},
                    {value: 'ANULADA_EN_ARCHIVADA', label: 'Anuladas', icon: 'fas fa-ban', badge: 'bg-danger'}
                ],
                filtros: {
                    venFechasEmision: `${fechaDesde} a ${fechaHasta}`,
                    venFechasArchivado: '',
                    venSecuencial: '',
                    venComprobante: '',
                    venCliente: null,
                    venBodega: null,
                    venCentroCosto: null,
                    venTipoComprobante: tipoComprobanteFactura,
                    venTipoVenta: null,
                    venEstado: 'BORRADOR'
                },
                listaBodegas: listaBodegas,
                listaCentroCostos: listaCentroCostos,
                listaTiposComprobantes: listaTiposComprobantes,
                listaClientes: [],
                listaVentas: [],
                contadores: {},
                idVenta: null,
                secuencialVenta: null,
                cargandoDetalle: false,
                detalleHtml: '',
                modalTitulo: 'Detalle de Venta',
                mostrarBotonesReporte: true,
                modalInstance: null
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
                    this.filtros.venFechasEmision = dateStr;
                }
            });

            flatpickr(this.$refs.dateRangeArchivado, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                locale: 'es',
                allowInput: true,
                onChange: (_, dateStr) => {
                    this.filtros.venFechasArchivado = dateStr;
                }
            });

            this.modalInstance = new bootstrap.Modal(this.$refs.modalReport);
            this.searchVentas();
        },
        methods: {
            cambiarEstado(estado) {
                this.estadoActivo = estado;
                this.filtros.venEstado = estado;
                this.searchVentas();
            },
            async searchVentas() {
                try {
                    this.loading = true;
                    this.listaVentas = [];

                    const {data} = await axios.post(this.url + '/ventas/searchVentas', this.filtros);
                    this.listaVentas = data.status === 'success' ? data.data : [];

                    await Vue.nextTick();

                    if (this.listaVentas.length) {
                        dataTable('#tblVentas', 'Listado de ventas');
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
                    this.loadingContadores = true;
                    const {data} = await axios.post(this.url + '/ventas/contadoresVentas', {
                        venFechasEmision: this.filtros.venFechasEmision,
                        venFechasArchivado: this.filtros.venFechasArchivado
                    });

                    if (data.status === 'success') {
                        this.contadores = data.data;
                    }
                } catch (e) {
                    this.contadores = {};
                } finally {
                    this.loadingContadores = false;
                }
            },
            searchCliente(search) {
                clearTimeout(this.searchTimeout);

                if (!search || search.trim().length < 2) {
                    if (!this.filtros.venCliente) {
                        this.listaClientes = [];
                    }
                    return;
                }

                this.searchTimeout = setTimeout(async () => {
                    try {
                        const {data} = await axios.post(this.url + '/admin/clientes/searchClientes', {
                            dataSerach: search.trim()
                        });
                        this.listaClientes = data || [];
                    } catch (e) {
                        this.listaClientes = [];
                    }
                }, 400);
            },
            limpiarFiltros() {
                this.estadoActivo = 'BORRADOR';
                this.filtros = {
                    venFechasEmision: `${fechaDesde} a ${fechaHasta}`,
                    venFechasArchivado: '',
                    venSecuencial: '',
                    venComprobante: '',
                    venCliente: null,
                    venBodega: null,
                    venCentroCosto: null,
                    venTipoComprobante: tipoComprobanteFactura,
                    venTipoVenta: null,
                    venEstado: 'BORRADOR'
                };
                this.$refs.dateRangeEmision._flatpickr.setDate([fechaDesde, fechaHasta], false);
                this.$refs.dateRangeArchivado._flatpickr.clear();
                this.searchVentas();
            },
            async verDetalle(venta) {
                this.idVenta = venta.id;
                this.secuencialVenta = venta.ven_secuencial;
                this.modalTitulo = 'Detalle de Venta #' + this.zFill(venta.ven_secuencial, 5);
                this.detalleHtml = '';
                this.mostrarBotonesReporte = true;
                this.cargandoDetalle = true;
                this.modalInstance.show();

                try {
                    const {data} = await axios.get(`${this.url}/ventas/getDataDetalle/${venta.id}`);
                    this.detalleHtml = data;
                } catch (e) {
                    this.detalleHtml = '<div class="alert alert-danger m-3">No se pudo cargar el detalle de la venta.</div>';
                } finally {
                    this.cargandoDetalle = false;
                }
            },
            async verAsientoContable(venta) {
                this.idVenta = venta.id;
                this.secuencialVenta = venta.ven_secuencial;
                this.modalTitulo = 'Asiento contable - Venta #' + this.zFill(venta.ven_secuencial, 5);
                this.detalleHtml = '';
                this.mostrarBotonesReporte = false;
                this.cargandoDetalle = true;
                this.modalInstance.show();

                try {
                    const {data} = await axios.get(`${this.url}/ventas/getAsientoContable/${venta.id}`);

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
            async anularVenta(venta) {
                const respuesta = await Swal.fire({
                    icon: 'warning',
                    title: 'Anular venta',
                    html: `<h6>Se anulara la venta #${this.zFill(venta.ven_secuencial, 5)}.</h6><small>Ingrese el motivo para continuar.</small>`,
                    input: 'textarea',
                    inputPlaceholder: 'Motivo de anulacion',
                    showCancelButton: true,
                    confirmButtonText: 'Anular',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    inputValidator: value => {
                        if (!value || !value.trim()) {
                            return 'Debe ingresar el motivo de anulacion.';
                        }
                    }
                });

                if (!respuesta.isConfirmed) {
                    return;
                }

                try {
                    this.loading = true;
                    const {data} = await axios.post(`${this.url}/ventas/anularVenta`, {
                        ventaId: venta.id,
                        motivoAnulacion: respuesta.value
                    });

                    if (data.status === 'success') {
                        sweet_msg_dialog('success', data.msg || 'Venta anulada correctamente.');
                        this.searchVentas();
                        return;
                    }

                    sweet_msg_dialog(data.status || 'warning', data.msg || 'No se pudo anular la venta.');
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loading = false;
                }
            },
            async loadVentaEdit(ventaId) {
                try {
                    this.loading = true;
                    const {data} = await axios.get(`${this.url}/ventas/loadVentaEdit/${ventaId}`);

                    if (data.status === 'success' && data.redirect) {
                        window.location.href = data.redirect;
                        return;
                    }

                    sweet_msg_dialog(data.status || 'warning', data.msg || 'No se pudo cargar la venta.');
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loading = false;
                }
            },
            generarExcel() {
                const contenido = document.querySelector('#contentExport');
                const titulo = 'Detalle venta ' + this.zFill(this.secuencialVenta, 5);
                return generarExcelContent(contenido, titulo);
            },
            generarPDF() {
                window.open(`${this.url}/ventas/generarPDF/${this.idVenta}?download=1`, '_blank');
            },
            numeroComprobante(venta) {
                return `${venta.ven_numero_establecimiento || ''}-${venta.ven_numero_emision || ''}-${venta.ven_numero_comprobante || ''}`;
            },
            zFill(value, width) {
                return String(value || '').padStart(width, '0');
            },
            formatToUSD(amount) {
                return formatToUSD(amount);
            },
            badgeEstado(estado) {
                const badges = {
                    BORRADOR: 'bg-warning text-dark',
                    ARCHIVADO: 'bg-success',
                    ANULADA_EN_PENDIENTE: 'bg-secondary',
                    ANULADA_EN_ARCHIVADA: 'bg-danger'
                };
                return badges[estado] || 'bg-secondary';
            },
            iconEstado(estado) {
                const icons = {
                    BORRADOR: 'fas fa-edit',
                    ARCHIVADO: 'fas fa-check',
                    ANULADA_EN_PENDIENTE: 'fas fa-file-alt',
                    ANULADA_EN_ARCHIVADA: 'fas fa-ban'
                };
                return icons[estado] || 'fas fa-info-circle';
            },
            labelEstado(estado) {
                const labels = {
                    BORRADOR: 'BORRADOR',
                    ARCHIVADO: 'ARCHIVADA',
                    ANULADA_EN_PENDIENTE: 'ANULADA EN BORRADOR',
                    ANULADA_EN_ARCHIVADA: 'ANULADA'
                };
                return labels[estado] || estado;
            },
            styleTipoComprobante(codigo) {
                const estilos = {
                    '01': {backgroundColor: '#e8f1fb', color: '#1f5f99', borderColor: '#b8d5ee'},
                    '02': {backgroundColor: '#e8f6f3', color: '#176f5d', borderColor: '#b7ded5'}
                };
                return estilos[String(codigo)] || {backgroundColor: '#f1f3f5', color: '#495057', borderColor: '#ced4da'};
            },
            badgeTipoPago(tipoPago) {
                const badges = {
                    CONTADO: 'bg-success',
                    CREDITO: 'bg-info text-dark'
                };
                return badges[tipoPago] || 'bg-secondary';
            },
            labelTipoPago(tipoPago) {
                const labels = {
                    CONTADO: 'CONTADO',
                    CREDITO: 'CREDITO'
                };
                return labels[tipoPago] || '-';
            }
        }
    });

    window.appGestionVentas.mount('#app');
</script>
