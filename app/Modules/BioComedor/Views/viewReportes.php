<!DOCTYPE html>
<!--
/**
 * Description of viewReportes
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 24 jul 2026
 * @time 2:38:02 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<style>
    .bio-report-card {
        border: 1px solid #d8e2dc;
        border-left: 4px solid #7fa685;
        border-radius: 6px;
        background: #fff;
        padding: 12px 14px;
        min-height: 84px;
    }

    .bio-report-label {
        color: #5c6770;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .bio-report-value {
        color: #0b1f33;
        font-size: 22px;
        font-weight: 800;
    }

    .bio-report-box {
        border: 1px solid #d8e2dc;
        border-radius: 6px;
        background: #fff;
        padding: 12px;
    }

    .bio-report-panel {
        border-bottom: 1px solid #d8e2dc;
    }

    .bio-report-panel .nav-link {
        color: #566573;
        border: 0;
        border-bottom: 3px solid transparent;
        font-weight: 600;
        padding: 10px 16px;
    }

    .bio-report-panel .nav-link.active {
        color: #fff;
        background: #0d6efd;
        border-radius: 5px 5px 0 0;
        border-bottom-color: #0d6efd;
    }
</style>

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title text-system">
                <i class="fas fa-chart-bar me-2"></i> Reportes Bio Comedor
            </h5>
            <small class="text-muted">Consumos, retrasos y marcaciones</small>
        </div>

        <div class="card-body">
            <div class="border rounded p-3 mb-3">
                <h6 class="text-system fw-bold mb-3"><i class="fas fa-filter"></i> Filtros</h6>

                <div class="row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label class="col-form-label col-form-label-sm">Fecha</label>
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system"><i class="fas fa-calendar me-2"></i> Rango de fechas</span>
                            <input
                                ref="dateRangeReportes"
                                v-model="filtros.fechas"
                                type="text"
                                class="form-control"
                                placeholder="Seleccione rango de fechas">
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="col-form-label col-form-label-sm">Comedor</label>
                        <vue-select class="border rounded" :options="listaComedores" label="com_nombre" v-model="filtros.fkComedor" :reduce="comedor => comedor.id" placeholder="Todos los comedores"></vue-select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="col-form-label col-form-label-sm">Servicio</label>
                        <vue-select class="border rounded" :options="listaServicios" label="serv_nombre" v-model="filtros.fkServicio" :reduce="servicio => servicio.id" placeholder="Todos los servicios"></vue-select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="col-form-label col-form-label-sm">Estado</label>
                        <select v-model="filtros.marcEstado" class="form-select border">
                            <option value="">TODOS</option>
                            <option value="VALIDA">VALIDA</option>
                            <option value="REPETIDA">REPETIDA</option>
                            <option value="ANULADA">ANULADA</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="col-form-label col-form-label-sm">Contratista</label>
                        <vue-select class="border rounded" :options="listaContratistas" label="cont_nombre" v-model="filtros.fkContratista" :reduce="contratista => contratista.id" placeholder="Todas las contratistas"></vue-select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="col-form-label col-form-label-sm">Proyecto</label>
                        <vue-select class="border rounded" :options="listaProyectos" label="proy_nombre" v-model="filtros.fkProyecto" :reduce="proyecto => proyecto.id" placeholder="Todos los proyectos"></vue-select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="col-form-label col-form-label-sm">Retraso</label>
                        <select v-model="filtros.marcRetraso" class="form-select border">
                            <option value="">TODOS</option>
                            <option value="0">NORMAL</option>
                            <option value="1">CON RETRASO</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="col-form-label col-form-label-sm">Costo base</label>
                        <input v-model="filtros.costoBase" type="number" step="0.0001" min="0" class="form-control" placeholder="Ej. 4.20" />
                    </div>

                    <div class="col-md-2 mb-3">
                        <button class="btn btn-primary w-100" :disabled="loadingReporte" @click="getReportes()">
                            <span v-if="loadingReporte"><i class="fas fa-spinner fa-spin"></i> Consultando...</span>
                            <span v-else><i class="fas fa-search"></i> Consultar</span>
                        </button>
                    </div>

                    <div class="col-md-2 mb-3">
                        <button class="btn btn-outline-secondary w-100" :disabled="loadingReporte" @click="limpiarFiltros()">
                            <i class="fas fa-eraser"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>

            <ul class="nav bio-report-panel mb-3">
                <li class="nav-item">
                    <button type="button" class="nav-link" :class="{active: panelActivo === 'graficos'}" @click="activarPanel('graficos')">
                        <i class="fas fa-chart-column me-1"></i> Graficos
                        <span class="badge bg-light text-dark ms-1">{{ numero(resumen.total_consumos) }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" :class="{active: panelActivo === 'detalle'}" @click="activarPanel('detalle')">
                        <i class="fas fa-table me-1"></i> Tabla
                        <span class="badge bg-secondary ms-1">{{ numero(detalle.length) }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" :class="{active: panelActivo === 'costos'}" @click="activarPanel('costos')">
                        <i class="fas fa-dollar-sign me-1"></i> Costos
                        <span class="badge bg-secondary ms-1">{{ numero(costosPorServicio.length) }}</span>
                    </button>
                </li>
            </ul>

            <div v-show="panelActivo === 'graficos'">
                <div class="row g-3 mb-3">
                    <div class="col-xl-2 col-md-4 col-sm-6" v-for="card in cardsResumen" :key="card.label">
                        <div class="bio-report-card">
                            <div class="bio-report-label">
                                <i :class="card.icon"></i> {{ card.label }}
                            </div>
                            <div class="bio-report-value">{{ card.value }}</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <div class="bio-report-box">
                            <h6 class="fw-bold text-system mb-2"><i class="fas fa-chart-line"></i> Tendencia por fecha</h6>
                            <div id="chartReporteTendencia" style="height: 320px;"></div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="bio-report-box">
                            <h6 class="fw-bold text-system mb-2"><i class="fas fa-chart-column"></i> Consumos por servicio</h6>
                            <div id="chartReporteServicios" style="height: 320px;"></div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="bio-report-box">
                            <h6 class="fw-bold text-system mb-2"><i class="fas fa-chart-pie"></i> Consumos por comedor</h6>
                            <div id="chartReporteComedores" style="height: 320px;"></div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="bio-report-box">
                            <h6 class="fw-bold text-system mb-2"><i class="fas fa-industry-alt"></i> Top contratistas</h6>
                            <div id="chartReporteContratistas" style="height: 320px;"></div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="bio-report-box">
                            <h6 class="fw-bold text-system mb-2"><i class="fas fa-project-diagram"></i> Top proyectos</h6>
                            <div id="chartReporteProyectos" style="height: 320px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-show="panelActivo === 'detalle'">
                <div v-if="loadingReporte" class="alert alert-info py-2">
                    <i class="fas fa-spinner fa-spin me-1"></i> Cargando reporte...
                </div>

                <div style="overflow-x: auto">
                    <table id="tblReporteMarcaciones" class="table table-striped nowrap display" style="width: 100%">
                        <thead class="bg-system text-white">
                            <tr>
                                <td>ID</td>
                                <td>FECHA</td>
                                <td>HORA</td>
                                <td>COMENSAL</td>
                                <td>COMEDOR</td>
                                <td>SERVICIO</td>
                                <td>CONTRATISTA</td>
                                <td>PROYECTO</td>
                                <td>ESTADO</td>
                                <td>RETRASO</td>
                                <td>ORIGEN</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="marcacion of detalle" :key="marcacion.id">
                                <td>{{ zfill(marcacion.id) }}</td>
                                <td>{{ marcacion.marc_fecha }}</td>
                                <td>{{ marcacion.marc_hora }}</td>
                                <td>{{ marcacion.comens_codigo }} - {{ marcacion.comens_nombres }} {{ marcacion.comens_apellidos }}</td>
                                <td>{{ marcacion.com_nombre }}</td>
                                <td>{{ marcacion.serv_nombre }}</td>
                                <td>{{ marcacion.cont_nombre }}</td>
                                <td>{{ marcacion.proy_nombre }}</td>
                                <td>
                                    <span v-if="marcacion.marc_estado === 'VALIDA'" class="badge bg-success">VALIDA</span>
                                    <span v-else-if="marcacion.marc_estado === 'REPETIDA'" class="badge bg-warning text-dark">REPETIDA</span>
                                    <span v-else class="badge bg-danger">{{ marcacion.marc_estado }}</span>
                                </td>
                                <td>
                                    <span v-if="parseInt(marcacion.marc_es_retraso) === 1" class="badge bg-danger">SI</span>
                                    <span v-else class="badge bg-info">NO</span>
                                </td>
                                <td>{{ marcacion.marc_origen }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-show="panelActivo === 'costos'">
                <div class="alert alert-warning small py-2">
                    <strong>Lectura:</strong> el costo diario se calcula con salidas archivadas por servicio dividido para consumos validos del mismo dia y servicio.
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-xl-3 col-md-6" v-for="card in cardsCostos" :key="card.label">
                        <div class="bio-report-card">
                            <div class="bio-report-label">
                                <i :class="card.icon"></i> {{ card.label }}
                            </div>
                            <div class="bio-report-value">{{ card.value }}</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <div class="bio-report-box">
                            <h6 class="fw-bold text-system mb-2"><i class="fas fa-chart-line"></i> Costo global diario</h6>
                            <div id="chartReporteCostosGlobal" style="height: 360px;"></div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="bio-report-box">
                            <h6 class="fw-bold text-system mb-2"><i class="fas fa-chart-line"></i> Costo diario por servicio</h6>
                            <div id="chartReporteCostosServicio" style="height: 360px;"></div>
                        </div>
                    </div>
                </div>

                <div style="overflow-x: auto">
                    <table id="tblReporteCostosServicio" class="table table-striped nowrap display" style="width: 100%">
                        <thead class="bg-system text-white">
                            <tr>
                                <td>FECHA</td>
                                <td>SERVICIO</td>
                                <td>DESPACHOS</td>
                                <td>COSTO DESPACHADO</td>
                                <td>CONSUMOS</td>
                                <td>COSTO DIA</td>
                                <td>COSTO BASE</td>
                                <td>DIFERENCIA</td>
                                <td>ESTADO</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row of costosPorServicio" :key="`${row.fecha}-${row.fk_servicio}`">
                                <td>{{ row.fecha }}</td>
                                <td>{{ row.servicio }}</td>
                                <td>{{ numero(row.despachos) }}</td>
                                <td>{{ moneda(row.costo_total) }}</td>
                                <td>{{ numero(row.consumos) }}</td>
                                <td class="fw-bold">{{ moneda(row.costo_unitario) }}</td>
                                <td>{{ moneda(row.costo_base) }}</td>
                                <td :class="Number(row.diferencia || 0) > 0 ? 'text-danger fw-bold' : 'text-success fw-bold'">
                                    {{ moneda(row.diferencia) }}
                                </td>
                                <td>
                                    <span v-if="row.estado_costo === 'SOBRE_BASE'" class="badge bg-danger">SOBRE BASE</span>
                                    <span v-else class="badge bg-success">DENTRO BASE</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var listaComedores = <?= json_encode($listaComedores ?? []) ?>;
    var listaServicios = <?= json_encode($listaServicios ?? []) ?>;
    var listaContratistas = <?= json_encode($listaContratistas ?? []) ?>;
    var listaProyectos = <?= json_encode($listaProyectos ?? []) ?>;

    if (window.appBioReportes) {
        window.appBioReportes.unmount();
    }

    window.appBioReportes = Vue.createApp({
        components: {
            "vue-select": window['vue-select']
        },
        data() {
            return {
                url: siteUrl,
                loadingReporte: false,
                listaComedores: listaComedores,
                listaServicios: listaServicios,
                listaContratistas: listaContratistas,
                listaProyectos: listaProyectos,
                panelActivo: 'graficos',
                filtros: this.emptyFiltros(),
                resumen: {},
                porServicio: [],
                porComedor: [],
                porContratista: [],
                porProyecto: [],
                porFecha: [],
                costosPorServicio: [],
                detalle: [],
            };
        },
        computed: {
            cardsResumen() {
                return [
                    {label: 'Marcaciones', value: this.numero(this.resumen.total_marcaciones), icon: 'fas fa-fingerprint me-1'},
                    {label: 'Consumos', value: this.numero(this.resumen.total_consumos), icon: 'fas fa-utensils me-1'},
                    {label: 'Validas', value: this.numero(this.resumen.total_validas), icon: 'fas fa-check-circle me-1'},
                    {label: 'Repetidas', value: this.numero(this.resumen.total_repetidas), icon: 'fas fa-copy me-1'},
                    {label: 'Anuladas', value: this.numero(this.resumen.total_anuladas), icon: 'fas fa-ban me-1'},
                    {label: 'Retrasos', value: this.numero(this.resumen.total_retrasos), icon: 'fas fa-clock me-1'},
                ];
            },
            cardsCostos() {
                const totalCosto = this.costosPorServicio.reduce((total, row) => total + Number(row.costo_total || 0), 0);
                const totalConsumos = this.costosPorServicio.reduce((total, row) => total + Number(row.consumos || 0), 0);
                const costoPromedio = totalConsumos > 0 ? totalCosto / totalConsumos : 0;
                const sobreBase = this.costosPorServicio.filter(row => row.estado_costo === 'SOBRE_BASE').length;

                return [
                    {label: 'Costo despachado', value: this.moneda(totalCosto), icon: 'fas fa-boxes me-1'},
                    {label: 'Consumos base', value: this.numero(totalConsumos), icon: 'fas fa-utensils me-1'},
                    {label: 'Costo promedio', value: this.moneda(costoPromedio), icon: 'fas fa-chart-line me-1'},
                    {label: 'Dias sobre base', value: this.numero(sobreBase), icon: 'fas fa-exclamation-triangle me-1'},
                ];
            },
        },
        created() {
            this.filtros.fechaDesde = this.fechaActual();
            this.filtros.fechaHasta = this.fechaActual();
            this.filtros.fechas = `${this.filtros.fechaDesde} a ${this.filtros.fechaHasta}`;
            this.getReportes();
        },
        mounted() {
            flatpickr(this.$refs.dateRangeReportes, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                locale: 'es',
                allowInput: true,
                defaultDate: [this.filtros.fechaDesde, this.filtros.fechaHasta],
                onChange: (_, dateStr) => {
                    this.filtros.fechas = dateStr;
                }
            });
        },
        methods: {
            emptyFiltros() {
                return {
                    fechas: '',
                    fechaDesde: '',
                    fechaHasta: '',
                    fkComedor: '',
                    fkServicio: '',
                    fkContratista: '',
                    fkProyecto: '',
                    marcEstado: 'VALIDA',
                    marcRetraso: '',
                    costoBase: '4.20',
                };
            },
            async getReportes() {
                this.loadingReporte = true;
                try {
                    const fechas = this.getFechasFiltro();
                    this.filtros.fechaDesde = fechas.fechaDesde;
                    this.filtros.fechaHasta = fechas.fechaHasta;
                    let datos = this.formData(this.filtros);
                    let response = await axios.post(this.url + '/biocomedor/reportes/getReportes', datos);
                    let data = response.data.data || {};

                    this.resumen = data.resumen || {};
                    this.porServicio = data.porServicio || [];
                    this.porComedor = data.porComedor || [];
                    this.porContratista = data.porContratista || [];
                    this.porProyecto = data.porProyecto || [];
                    this.porFecha = data.porFecha || [];
                    this.costosPorServicio = data.costosPorServicio || [];

                    if ($.fn.DataTable.isDataTable('#tblReporteMarcaciones')) {
                        $('#tblReporteMarcaciones').DataTable().destroy();
                    }

                    if ($.fn.DataTable.isDataTable('#tblReporteCostosServicio')) {
                        $('#tblReporteCostosServicio').DataTable().destroy();
                    }

                    this.detalle = data.detalle || [];

                    this.$nextTick(() => {
                        this.renderCharts();
                        if (this.panelActivo === 'detalle') {
                            this.renderTablaReporte();
                        }

                        if (this.panelActivo === 'costos') {
                            this.renderCostos();
                        }
                    });
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    this.loadingReporte = false;
                }
            },
            limpiarFiltros() {
                this.filtros = this.emptyFiltros();
                this.filtros.fechaDesde = this.fechaActual();
                this.filtros.fechaHasta = this.fechaActual();
                this.filtros.fechas = `${this.filtros.fechaDesde} a ${this.filtros.fechaHasta}`;

                if (this.$refs.dateRangeReportes && this.$refs.dateRangeReportes._flatpickr) {
                    this.$refs.dateRangeReportes._flatpickr.setDate([this.filtros.fechaDesde, this.filtros.fechaHasta], false);
                }

                this.getReportes();
            },
            getFechasFiltro() {
                const fechas = String(this.filtros.fechas || '').split(' a ');

                return {
                    fechaDesde: fechas[0] || this.fechaActual(),
                    fechaHasta: fechas[1] || fechas[0] || this.fechaActual()
                };
            },
            activarPanel(panel) {
                this.panelActivo = panel;
                this.$nextTick(() => {
                    if (panel === 'graficos') {
                        this.renderCharts();
                    }

                    if (panel === 'detalle') {
                        this.renderTablaReporte();
                    }

                    if (panel === 'costos') {
                        this.renderCostos();
                    }
                });
            },
            renderTablaReporte() {
                if ($.fn.DataTable.isDataTable('#tblReporteMarcaciones')) {
                    $('#tblReporteMarcaciones').DataTable().destroy();
                }

                dataTable('#tblReporteMarcaciones', 'Reporte de marcaciones');
            },
            renderTablaCostos() {
                if ($.fn.DataTable.isDataTable('#tblReporteCostosServicio')) {
                    $('#tblReporteCostosServicio').DataTable().destroy();
                }

                dataTable('#tblReporteCostosServicio', 'Reporte de costos por servicio');
            },
            renderCharts() {
                if (typeof Highcharts === 'undefined') {
                    return;
                }

                this.renderLineChart('chartReporteTendencia', this.porFecha);
                this.renderColumnChart('chartReporteServicios', this.porServicio, 'Consumos');
                this.renderColumnChart('chartReporteComedores', this.porComedor, 'Consumos');
                this.renderBarChart('chartReporteContratistas', this.porContratista.slice(0, 8), 'Consumos');
                this.renderBarChart('chartReporteProyectos', this.porProyecto.slice(0, 8), 'Consumos');
            },
            renderCostos() {
                if (typeof Highcharts === 'undefined') {
                    return;
                }

                this.renderCostosGlobalChart();
                this.renderCostosServicioChart();
                this.renderTablaCostos();
            },
            renderLineChart(container, rows) {
                Highcharts.chart(container, {
                    chart: {type: 'spline'},
                    title: {text: ''},
                    xAxis: {categories: rows.map(row => row.fecha || '-')},
                    yAxis: {title: {text: 'Cantidad'}},
                    tooltip: {shared: true},
                    series: [
                        {name: 'Marcaciones', data: rows.map(row => Number(row.marcaciones || 0))},
                        {name: 'Consumos', data: rows.map(row => Number(row.consumos || 0))},
                        {name: 'Retrasos', data: rows.map(row => Number(row.retrasos || 0))}
                    ],
                    credits: {enabled: false}
                });
            },
            renderColumnChart(container, rows, title) {
                Highcharts.chart(container, {
                    chart: {type: 'column'},
                    title: {text: ''},
                    xAxis: {categories: rows.map(row => row.nombre || '-')},
                    yAxis: {title: {text: title}},
                    series: [
                        {name: 'Consumos', data: rows.map(row => Number(row.consumos || 0))},
                        {name: 'Retrasos', data: rows.map(row => Number(row.retrasos || 0))}
                    ],
                    credits: {enabled: false}
                });
            },
            renderBarChart(container, rows, title) {
                Highcharts.chart(container, {
                    chart: {type: 'bar'},
                    title: {text: ''},
                    xAxis: {categories: rows.map(row => row.nombre || '-')},
                    yAxis: {title: {text: title}},
                    series: [
                        {name: 'Consumos', data: rows.map(row => Number(row.consumos || 0))},
                        {name: 'Retrasos', data: rows.map(row => Number(row.retrasos || 0))}
                    ],
                    credits: {enabled: false}
                });
            },
            getCostosGlobalPorFecha() {
                const rows = this.costosPorServicio || [];
                const agrupado = {};

                rows.forEach(row => {
                    const fecha = row.fecha || '-';

                    if (!agrupado[fecha]) {
                        agrupado[fecha] = {
                            fecha: fecha,
                            costo_total: 0,
                            consumos: 0
                        };
                    }

                    agrupado[fecha].costo_total += Number(row.costo_total || 0);
                    agrupado[fecha].consumos += Number(row.consumos || 0);
                });

                return Object.values(agrupado).map(row => ({
                        ...row,
                        costo_unitario: row.consumos > 0 ? Number((row.costo_total / row.consumos).toFixed(4)) : 0
                    }));
            },
            renderCostosGlobalChart() {
                const rows = this.getCostosGlobalPorFecha();
                const costoBase = Number(this.filtros.costoBase || 0);
                const series = [
                    {
                        name: 'Costo global diario',
                        data: rows.map(row => Number(row.costo_unitario || 0)),
                        color: '#5f6f7f',
                        dataLabels: {
                            enabled: true,
                            format: '${y:.2f}',
                            style: {
                                fontWeight: 'bold',
                                textOutline: 'none'
                            }
                        }
                    }
                ];

                if (costoBase > 0) {
                    series.push({
                        name: 'Costo base',
                        type: 'line',
                        dashStyle: 'ShortDash',
                        color: '#dc3545',
                        marker: {enabled: false},
                        data: rows.map(() => costoBase),
                        dataLabels: {enabled: false}
                    });
                }

                Highcharts.chart('chartReporteCostosGlobal', {
                    chart: {type: 'spline'},
                    title: {text: ''},
                    xAxis: {
                        categories: rows.map(row => row.fecha || '-'),
                        labels: {
                            rotation: -55
                        }
                    },
                    yAxis: {
                        title: {text: 'Costo por consumo'},
                        labels: {format: '${value}'}
                    },
                    tooltip: {
                        shared: true,
                        valuePrefix: '$',
                        valueDecimals: 4
                    },
                    plotOptions: {
                        spline: {
                            marker: {
                                enabled: true,
                                radius: 4
                            }
                        }
                    },
                    series: series,
                    credits: {enabled: false}
                });
            },
            renderCostosServicioChart() {
                const rows = this.costosPorServicio || [];
                const fechas = [...new Set(rows.map(row => row.fecha || '-'))];
                const servicios = [...new Set(rows.map(row => row.servicio || 'SIN SERVICIO'))];
                const costoBase = Number(this.filtros.costoBase || 0);
                const series = servicios.map(servicio => ({
                        name: servicio,
                        data: fechas.map(fecha => {
                            const row = rows.find(item => item.fecha === fecha && item.servicio === servicio);
                            return row ? Number(row.costo_unitario || 0) : null;
                        }),
                        dataLabels: {
                            enabled: true,
                            format: '${y:.2f}',
                            style: {
                                fontWeight: 'bold',
                                textOutline: 'none'
                            }
                        }
                    }));

                if (costoBase > 0) {
                    series.push({
                        name: 'Costo base',
                        type: 'line',
                        dashStyle: 'ShortDash',
                        color: '#dc3545',
                        marker: {enabled: false},
                        data: fechas.map(() => costoBase),
                        dataLabels: {enabled: false}
                    });
                }

                Highcharts.chart('chartReporteCostosServicio', {
                    chart: {type: 'spline'},
                    title: {text: ''},
                    xAxis: {
                        categories: fechas,
                        labels: {
                            rotation: -55
                        }
                    },
                    yAxis: {
                        title: {text: 'Costo por consumo'},
                        labels: {format: '${value}'}
                    },
                    tooltip: {
                        shared: true,
                        valuePrefix: '$',
                        valueDecimals: 4
                    },
                    plotOptions: {
                        spline: {
                            marker: {
                                enabled: true,
                                radius: 4
                            }
                        }
                    },
                    series: series,
                    credits: {enabled: false}
                });
            },
            fechaActual(fecha = new Date()) {
                let year = fecha.getFullYear();
                let month = String(fecha.getMonth() + 1).padStart(2, '0');
                let day = String(fecha.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            },
            formData(obj) {
                var formData = new FormData();
                for (var key in obj) {
                    formData.append(key, obj[key]);
                }
                return formData;
            },
            numero(value) {
                return Number(value || 0);
            },
            moneda(value) {
                return new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 4
                }).format(Number(value || 0));
            },
            zfill(num) {
                return zFill(num, 3);
            },
        },
    });

    window.appBioReportes.mount('#app');
</script>
