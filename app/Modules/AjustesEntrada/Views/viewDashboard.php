<!DOCTYPE html>
<!--
/**
 * Description of viewDashboard
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 15 jul 2026
 * @time 12:45:26 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<style>
    .dashboard-card {
        border: 1px solid #d8e2dc;
        border-left: 4px solid #7fa685;
        border-radius: 6px;
        background: #fff;
        padding: 14px 16px;
        min-height: 92px;
    }

    .dashboard-card-label {
        color: #5c6770;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .dashboard-card-value {
        color: #0b1f33;
        font-size: 22px;
        font-weight: 800;
    }

    .dashboard-box {
        border: 1px solid #d8e2dc;
        border-radius: 6px;
        background: #fff;
        padding: 12px;
    }

    .dashboard-chart {
        width: 100%;
        min-height: 320px;
    }
</style>


<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title text-system">
                <i class="fas fa-chart-line me-2"></i> Dashboard de Ajustes de Entrada
            </h5>
            <small class="text-muted">
                {{ fechaDesde }} a {{ fechaHasta }}
            </small>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3 form-group-custom">
                    <div class="input-group">
                        <span class="input-group-text bg-cris-system">
                            <i class="fas fa-calendar me-2"></i> Fecha
                        </span>
                        <input
                            ref="dateRangeDashboard"
                            v-model="filtros.fechas"
                            type="text"
                            class="form-control"
                            placeholder="Rango de fechas">
                    </div>
                </div>

                <div class="col-md-2 form-group-custom">
                    <div class="d-flex align-items-center border rounded overflow-visible">
                        <span class="input-group-text bg-cris-system">
                            <i class="fas fa-warehouse me-2"></i> Bodega
                        </span>
                        <vue-select
                            class="flex-grow-1"
                            :options="listaBodegas"
                            label="bod_nombre"
                            v-model="filtros.bodegaId"
                            :reduce="bodega => bodega.id"
                            placeholder="Todas">
                        </vue-select>
                    </div>
                </div>

                <div class="col-md-2 form-group-custom">
                    <div class="d-flex align-items-center border rounded overflow-visible">
                        <span class="input-group-text bg-cris-system">
                            <i class="fas fa-tag me-2"></i> Motivo
                        </span>
                        <vue-select
                            class="flex-grow-1"
                            :options="listaMotivos"
                            label="mot_nombre"
                            v-model="filtros.motivoId"
                            :reduce="motivo => motivo.id"
                            placeholder="Todos">
                        </vue-select>
                    </div>
                </div>

                <div class="col-md-2 form-group-custom">
                    <div class="d-flex align-items-center border rounded overflow-visible">
                        <span class="input-group-text bg-cris-system">
                            <i class="fas fa-project-diagram me-2"></i> Centro costo
                        </span>
                        <vue-select
                            class="flex-grow-1"
                            :options="listaCentroCostos"
                            label="cc_nombre"
                            v-model="filtros.centroCostoId"
                            :reduce="centro => centro.id"
                            placeholder="Todos">
                        </vue-select>
                    </div>
                </div>
                <!--
                                <div class="col-md-2 form-group-custom">
                                    <div class="input-group">
                                        <span class="input-group-text bg-cris-system">
                                            <i class="fas fa-toggle-on me-2"></i> Estado
                                        </span>
                                        <select v-model="filtros.estado" class="form-select">
                                            <option value="">Todos</option>
                                            <option value="2">Archivados</option>
                                            <option value="1">Borradores</option>
                                            <option value="-1">Anulados</option>
                                        </select>
                                    </div>
                                </div>-->
                <!--
                                <div class="col-md-3 form-group-custom">
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" id="tipoAjusteNormalDash" value="AJUSTE_NORMAL" v-model="filtros.tipo">
                                        <label class="btn btn-outline-success" for="tipoAjusteNormalDash">
                                            <i class="fas fa-file-archive me-1"></i> Ajuste Normal
                                        </label>
                
                                        <input type="radio" class="btn-check" id="tipoCompraSinFacturaDash" value="COMPRA_SIN_FACTURA" v-model="filtros.tipo">
                                        <label class="btn btn-outline-primary" for="tipoCompraSinFacturaDash">
                                            <i class="fas fa-file me-1"></i> Compra sin Factura
                                        </label>
                                    </div>
                                </div>-->
                <div class="col-md-3">
                    <button
                        type="button"
                        class="btn btn-system-2 "
                        :disabled="loading"
                        @click="cargarDashboard">
                        <i class="fas fa-search me-1"></i> Filtrar Datos
                    </button>


                    <button
                        type="button"
                        class="btn btn-secondary"
                        :disabled="loading"
                        @click="limpiarFiltros">
                        <i class="fas fa-eraser me-1"></i> Limpiar
                    </button>
                </div>
            </div>

            <div v-if="loading" class="alert alert-info py-2">
                <i class="fas fa-spinner fa-spin me-1"></i> Actualizando dashboard...
            </div>

            <div class="row g-3 mb-3">
                <div class="col-xl-2 col-md-4 col-sm-6" v-for="card in cardsResumen" :key="card.label">
                    <div class="dashboard-card">
                        <div class="dashboard-card-label">
                            <i :class="card.icon"></i> {{ card.label }}
                        </div>
                        <div class="dashboard-card-value">{{ card.value }}</div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-xl-4">
                    <div class="dashboard-box">
                        <div id="chartEstadosAjustes" class="dashboard-chart"></div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="dashboard-box">
                        <div id="chartMotivosAjustes" class="dashboard-chart"></div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="dashboard-box">
                        <div id="chartBodegasAjustes" class="dashboard-chart"></div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-xl-8">
                    <div class="dashboard-box">
                        <div id="chartTendenciaAjustes" class="dashboard-chart"></div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="dashboard-box">
                        <div id="chartCentrosCostoAjustes" class="dashboard-chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var fechaDesdeDashboard = <?= json_encode($fechaDesdeDashboard ?? date('Y-m-01')) ?>;
    var fechaHastaDashboard = <?= json_encode($fechaHastaDashboard ?? date('Y-m-d')) ?>;
    var dashboardResumen = <?= json_encode($dashboardResumen ?? (object) []) ?>;
    var dashboardEstados = <?= json_encode($dashboardEstados ?? []) ?>;
    var dashboardMotivos = <?= json_encode($dashboardMotivos ?? []) ?>;
    var dashboardBodegas = <?= json_encode($dashboardBodegas ?? []) ?>;
    var dashboardTendenciaMensual = <?= json_encode($dashboardTendenciaMensual ?? []) ?>;
    var dashboardCentrosCosto = <?= json_encode($dashboardCentrosCosto ?? []) ?>;
    var listaBodegas = <?= json_encode($listaBodegas ?? []) ?>;
    var listaMotivos = <?= json_encode($listaMotivos ?? []) ?>;
    var listaCentroCostos = <?= json_encode($listaCentroCostos ?? []) ?>;

    if (window.appDashboardAjustesEntrada) {
        window.appDashboardAjustesEntrada.unmount();
    }

    window.appDashboardAjustesEntrada = Vue.createApp({
        components: {
            "vue-select": window['vue-select']
        },
        data() {
            return {
                url: siteUrl,
                loading: false,
                fechaDesde: fechaDesdeDashboard,
                fechaHasta: fechaHastaDashboard,
                filtros: {
                    fechas: `${fechaDesdeDashboard} a ${fechaHastaDashboard}`,
                    bodegaId: null,
                    motivoId: null,
                    centroCostoId: null,
                    estado: '2',
                    tipo: 'AJUSTE_NORMAL'
                },
                resumen: dashboardResumen,
                estados: dashboardEstados,
                motivos: dashboardMotivos,
                bodegas: dashboardBodegas,
                tendenciaMensual: dashboardTendenciaMensual,
                centrosCosto: dashboardCentrosCosto,
                listaBodegas: listaBodegas,
                listaMotivos: listaMotivos,
                listaCentroCostos: listaCentroCostos,
                charts: {
                    estados: null,
                    motivos: null,
                    bodegas: null,
                    tendencia: null,
                    centrosCosto: null
                }
            };
        },

        computed: {
            cardsResumen() {
                return [
                    {label: 'Ajustes', icon: 'fas fa-file-invoice', value: Number(this.resumen.total_ajustes || 0)},
                    {label: 'Archivados', icon: 'fas fa-check', value: Number(this.resumen.total_archivados || 0)},
                    {label: 'Borradores', icon: 'fas fa-edit', value: Number(this.resumen.total_borradores || 0)},
                    {label: 'Anulados', icon: 'fas fa-ban', value: Number(this.resumen.total_anulados || 0)},
                    {label: 'Valor archivado', icon: 'fas fa-dollar-sign', value: this.formatToUSD(this.resumen.total_valor || 0)},
                    {label: 'Items', icon: 'fas fa-boxes', value: Number(this.resumen.total_items || 0).toFixed(2)}
                ];
            }
        },

        mounted() {
            flatpickr(this.$refs.dateRangeDashboard, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                locale: 'es',
                allowInput: true,
                defaultDate: [fechaDesdeDashboard, fechaHastaDashboard],
                onChange: (_, dateStr) => {
                    this.filtros.fechas = dateStr;
                }
            });

            this.renderCharts();
        },

        methods: {
            getFechasFiltro() {
                const fechas = String(this.filtros.fechas || '').split(' a ');
                return {
                    fechaDesde: fechas[0] || fechaDesdeDashboard,
                    fechaHasta: fechas[1] || fechas[0] || fechaHastaDashboard
                };
            },

            async cargarDashboard() {
                const fechas = this.getFechasFiltro();
                const datos = {
                    fechaDesde: fechas.fechaDesde,
                    fechaHasta: fechas.fechaHasta,
                    bodegaId: this.filtros.bodegaId,
                    motivoId: this.filtros.motivoId,
                    centroCostoId: this.filtros.centroCostoId,
                    estado: this.filtros.estado,
                    tipo: this.filtros.tipo
                };

                try {
                    this.loading = true;
                    const {data} = await axios.post(this.url + '/ajustesentrada/getDataDashboard', datos);

                    if (data.status !== 'success') {
                        sweet_msg_dialog(data.status || 'warning', data.msg || 'No se pudo cargar el dashboard.');
                        return;
                    }

                    this.fechaDesde = datos.fechaDesde;
                    this.fechaHasta = datos.fechaHasta;
                    this.resumen = data.data.resumen || {};
                    this.estados = data.data.estados || [];
                    this.motivos = data.data.motivos || [];
                    this.bodegas = data.data.bodegas || [];
                    this.tendenciaMensual = data.data.tendenciaMensual || [];
                    this.centrosCosto = data.data.centrosCosto || [];

                    await Vue.nextTick();
                    this.renderCharts();
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loading = false;
                }
            },

            limpiarFiltros() {
                this.filtros = {
                    fechas: `${fechaDesdeDashboard} a ${fechaHastaDashboard}`,
                    bodegaId: null,
                    motivoId: null,
                    centroCostoId: null,
                    estado: '2',
                    tipo: 'AJUSTE_NORMAL'
                };
                this.$refs.dateRangeDashboard._flatpickr.setDate([fechaDesdeDashboard, fechaHastaDashboard], false);
                this.cargarDashboard();
            },

            renderCharts() {
                if (!window.Highcharts) {
                    sweet_msg_toast('warning', 'Highcharts no esta cargado.');
                    return;
                }

                Object.keys(this.charts).forEach(key => {
                    if (this.charts[key]) {
                        this.charts[key].destroy();
                    }
                });

                this.charts.estados = Highcharts.chart('chartEstadosAjustes', {
                    chart: {type: 'pie'},
                    title: {text: 'Ajustes por estado'},
                    tooltip: {pointFormat: '<b>{point.y}</b> ajustes'},
                    series: [{
                            name: 'Estados',
                            colorByPoint: true,
                            data: this.estados.map(item => ({
                                    name: this.labelEstado(item.estado),
                                    y: Number(item.total || 0)
                                }))
                        }]
                });

                this.charts.motivos = Highcharts.chart('chartMotivosAjustes', {
                    colors: ['#7fa685', '#5f8fb8', '#d6a94f', '#c96f5f', '#8c7bb8', '#5ea89a'],
                    chart: {type: 'column'},
                    title: {text: 'Ajustes por motivo'},
                    xAxis: {
                        categories: this.motivos.map(item => item.motivo || 'SIN MOTIVO')
                    },
                    yAxis: {title: {text: 'Valor ajustado'}},
                    tooltip: {valuePrefix: '$'},
                    plotOptions: {
                        column: {
                            colorByPoint: true
                        }
                    },
                    series: [{
                        name: 'Total',
                        data: this.motivos.map(item => Number(item.valor || 0))
                    }]
                });

                this.charts.bodegas = Highcharts.chart('chartBodegasAjustes', {
                    colors: ['#7fa685', '#5f8fb8', '#d6a94f', '#c96f5f', '#8c7bb8', '#5ea89a'],
                    chart: {type: 'bar'},
                    title: {text: 'Ajustes por bodega'},
                    xAxis: {
                        categories: this.bodegas.map(item => item.bodega || 'SIN BODEGA')
                    },
                    yAxis: {title: {text: 'Valor ajustado'}},
                    tooltip: {valuePrefix: '$'},
                    plotOptions: {
                        bar: {
                            colorByPoint: true
                        }
                    },
                    series: [{
                        name: 'Total',
                        data: this.bodegas.map(item => Number(item.valor || 0))
                    }]
                });

                this.charts.tendencia = Highcharts.chart('chartTendenciaAjustes', {
                    chart: {type: 'areaspline'},
                    title: {text: 'Tendencia mensual de ajustes'},
                    xAxis: {
                        categories: this.tendenciaMensual.map(item => item.periodo)
                    },
                    yAxis: {title: {text: 'Valor ajustado'}},
                    tooltip: {
                        shared: true,
                        valuePrefix: '$'
                    },
                    plotOptions: {
                        areaspline: {
                            fillOpacity: 0.25
                        }
                    },
                    series: [{
                            name: 'Ajustes',
                            data: this.tendenciaMensual.map(item => Number(item.valor || 0))
                        }]
                });

                this.charts.centrosCosto = Highcharts.chart('chartCentrosCostoAjustes', {
                    colors: ['#7fa685', '#5f8fb8', '#d6a94f', '#c96f5f', '#8c7bb8', '#5ea89a'],
                    chart: {type: 'bar'},
                    title: {text: 'Ajustes por centro de costo'},
                    xAxis: {
                        categories: this.centrosCosto.map(item => item.centro_costo || 'SIN CENTRO')
                    },
                    yAxis: {title: {text: 'Valor ajustado'}},
                    tooltip: {valuePrefix: '$'},
                    plotOptions: {
                        bar: {
                            colorByPoint: true
                        }
                    },
                    series: [{
                        name: 'Total',
                        data: this.centrosCosto.map(item => Number(item.valor || 0))
                        }]
                });
            },

            labelEstado(estado) {
                const mapa = {
                    2: 'ARCHIVADO',
                    1: 'BORRADOR',
                    '-1': 'ANULADO'
                };
                return mapa[estado] || estado;
            },

            formatToUSD(amount) {
                return formatToUSD(amount);
            }
        }
    });

    window.appDashboardAjustesEntrada.mount('#app');
</script>
