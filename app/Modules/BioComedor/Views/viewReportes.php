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
                    <div class="col-md-2 mb-3">
                        <label class="col-form-label col-form-label-sm">Desde</label>
                        <input v-model="filtros.fechaDesde" type="date" class="form-control" />
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="col-form-label col-form-label-sm">Hasta</label>
                        <input v-model="filtros.fechaHasta" type="date" class="form-control" />
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
                                <td>FECHA/HORA</td>
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
                                <td>{{ marcacion.marc_fecha_hora }}</td>
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
        },
        created() {
            this.filtros.fechaDesde = this.fechaActual();
            this.filtros.fechaHasta = this.fechaActual();
            this.getReportes();
        },
        methods: {
            emptyFiltros() {
                return {
                    fechaDesde: '',
                    fechaHasta: '',
                    fkComedor: '',
                    fkServicio: '',
                    fkContratista: '',
                    fkProyecto: '',
                    marcEstado: 'VALIDA',
                    marcRetraso: '',
                };
            },
            async getReportes() {
                this.loadingReporte = true;
                try {
                    let datos = this.formData(this.filtros);
                    let response = await axios.post(this.url + '/biocomedor/reportes/getReportes', datos);
                    let data = response.data.data || {};

                    this.resumen = data.resumen || {};
                    this.porServicio = data.porServicio || [];
                    this.porComedor = data.porComedor || [];
                    this.porContratista = data.porContratista || [];
                    this.porProyecto = data.porProyecto || [];

                    if ($.fn.DataTable.isDataTable('#tblReporteMarcaciones')) {
                        $('#tblReporteMarcaciones').DataTable().destroy();
                    }

                    this.detalle = data.detalle || [];

                    this.$nextTick(() => {
                        this.renderCharts();
                        if (this.panelActivo === 'detalle') {
                            this.renderTablaReporte();
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
                this.getReportes();
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
                });
            },
            renderTablaReporte() {
                if ($.fn.DataTable.isDataTable('#tblReporteMarcaciones')) {
                    $('#tblReporteMarcaciones').DataTable().destroy();
                }

                dataTable('#tblReporteMarcaciones', 'Reporte de marcaciones');
            },
            renderCharts() {
                if (typeof Highcharts === 'undefined') {
                    return;
                }

                this.renderColumnChart('chartReporteServicios', this.porServicio, 'Consumos');
                this.renderColumnChart('chartReporteComedores', this.porComedor, 'Consumos');
                this.renderBarChart('chartReporteContratistas', this.porContratista.slice(0, 8), 'Consumos');
                this.renderBarChart('chartReporteProyectos', this.porProyecto.slice(0, 8), 'Consumos');
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
            zfill(num) {
                return zFill(num, 3);
            },
        },
    });

    window.appBioReportes.mount('#app');
</script>
