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
                                        <li v-if="compra.comp_estado === 'BORRADOR'">
                                            <button
                                                class="dropdown-item text-warning"
                                                @click="loadCompraEdit(compra.id)">
                                                <i class="fas fa-edit me-2"></i> Modificar compra
                                            </button>
                                        </li>
                                        <li>
                                            <button
                                                class="dropdown-item"
                                                @click="clonarCompra(compra.id)">
                                                <i class="fas fa-clone me-2"></i> Clonar compra
                                            </button>
                                        </li>
                                        <li v-if="['BORRADOR', 'ARCHIVADO'].includes(compra.comp_estado)">
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
            </div>

            <div v-else class="text-center text-muted py-5">
                <i class="fas fa-search fa-2x mb-2"></i>
                <div>No se encontraron compras con los filtros seleccionados.</div>
            </div>
        </div>
    </div>

    <?php echo view('\Modules\Compras\Views\reportes\viewModalReport') ?>
</div>

<script type="text/javascript">
    var fechaDesde = DateTime.now().toFormat('yyyy-MM-01');
    var fechaHasta = DateTime.now().toFormat('yyyy-MM-dd');
    var listaBodegas = <?= json_encode($listaBodegas); ?>;
    var listaCentroCostos = <?= json_encode($listaCentroCostos); ?>;
    var listaTiposComprobantes = <?= json_encode($listaTiposComprobantes); ?>;

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
                listaTiposCostos: [
                    {value: 'DIRECTOS', label: 'DIRECTOS'},
                    {value: 'INDIRECTOS', label: 'INDIRECTOS'}
                ],
                listaProveedores: [],
                listaCompras: [],
                contadores: {},
                idCompra: null,
                secuencialCompra: null,
                cargandoDetalle: false,
                detalleHtml: '',
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
            async verDetalle(compra) {
                this.idCompra = compra.id;
                this.secuencialCompra = compra.comp_secuencial;
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

            generarExcel() {
                const contenido = document.getElementById('contentExport');
                const titulo = `Compra_${this.zFill(this.secuencialCompra, 5)}`;

                return generarExcelContent(contenido, titulo);
            },

            generarPDF() {
                window.open(`${this.url}/compras/generarPDF/${this.idCompra}?download=1`, '_blank');
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
            formatToUSD(amount) {
                return formatToUSD(amount);
            },
            zFill(value, size) {
                return zFill(value, size);
            }
        }
    });

    window.appGestionCompras.mount('#app');
</script>
