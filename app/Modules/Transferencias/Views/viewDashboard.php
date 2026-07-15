<!DOCTYPE html>
<!--
/**
 * Description of viewDashboard
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 15 jul 2026
 * @time 2:46:13 p.m.
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
                <i class="fas fa-chart-line me-2"></i> Dashboard de Transferencias
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
                            <i class="fas fa-warehouse me-2"></i> Origen
                        </span>
                        <vue-select
                            class="flex-grow-1"
                            :options="listaBodegas"
                            label="bod_nombre"
                            v-model="filtros.bodegaOrigenId"
                            :reduce="bodega => bodega.id"
                            placeholder="Todas">
                        </vue-select>
                    </div>
                </div>

                <div class="col-md-2 form-group-custom">
                    <div class="d-flex align-items-center border rounded overflow-visible">
                        <span class="input-group-text bg-cris-system">
                            <i class="fas fa-warehouse me-2"></i> Destino
                        </span>
                        <vue-select
                            class="flex-grow-1"
                            :options="listaBodegas"
                            label="bod_nombre"
                            v-model="filtros.bodegaDestinoId"
                            :reduce="bodega => bodega.id"
                            placeholder="Todas">
                        </vue-select>
                    </div>
                </div>

                <div class="col-md-2 form-group-custom">
                    <div class="d-flex align-items-center border rounded overflow-visible">
                        <span class="input-group-text bg-cris-system">
                            <i class="fas fa-user-check me-2"></i> Confirma
                        </span>
                        <vue-select
                            class="flex-grow-1"
                            :options="listaUsuarios"
                            label="empleado"
                            v-model="filtros.usuarioConfirmarId"
                            :reduce="usuario => usuario.id"
                            placeholder="Todos">
                        </vue-select>
                    </div>
                </div>

                <div class="col-md-2 form-group-custom">
                    <button
                        type="button"
                        class="btn btn-system-2"
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
                        <div id="chartEstadosTransferencias" class="dashboard-chart"></div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="dashboard-box">
                        <div id="chartBodegasOrigenTransferencias" class="dashboard-chart"></div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="dashboard-box">
                        <div id="chartBodegasDestinoTransferencias" class="dashboard-chart"></div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-xl-8">
                    <div class="dashboard-box">
                        <div id="chartTendenciaTransferencias" class="dashboard-chart"></div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="dashboard-box mb-3">
                        <div id="chartUsuariosTransferencias" class="dashboard-chart"></div>
                    </div>
                    <div class="dashboard-box">
                        <div id="chartRutasTransferencias" class="dashboard-chart"></div>
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
    var dashboardBodegasOrigen = <?= json_encode($dashboardBodegasOrigen ?? []) ?>;
    var dashboardBodegasDestino = <?= json_encode($dashboardBodegasDestino ?? []) ?>;
    var dashboardTendenciaMensual = <?= json_encode($dashboardTendenciaMensual ?? []) ?>;
    var dashboardUsuariosConfirmacion = <?= json_encode($dashboardUsuariosConfirmacion ?? []) ?>;
    var dashboardRutas = <?= json_encode($dashboardRutas ?? []) ?>;
    var listaBodegas = <?= json_encode($listaBodegas ?? []) ?>;
    var listaUsuarios = <?= json_encode($listaUsuarios ?? []) ?>;
    var dashboardColors = ['#7fa685', '#5f8fb8', '#d6a94f', '#c96f5f', '#8c7bb8', '#5ea89a'];

    if (window.appDashboardTransferencias) {
        window.appDashboardTransferencias.unmount();
    }

    window.appDashboardTransferencias = Vue.createApp({
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
                    bodegaOrigenId: null,
                    bodegaDestinoId: null,
                    usuarioConfirmarId: null
                },
                resumen: dashboardResumen,
                estados: dashboardEstados,
                bodegasOrigen: dashboardBodegasOrigen,
                bodegasDestino: dashboardBodegasDestino,
                tendenciaMensual: dashboardTendenciaMensual,
                usuariosConfirmacion: dashboardUsuariosConfirmacion,
                rutas: dashboardRutas,
                listaBodegas: listaBodegas,
                listaUsuarios: listaUsuarios,
                charts: {
                    estados: null,
                    bodegasOrigen: null,
                    bodegasDestino: null,
                    tendencia: null,
                    usuarios: null,
                    rutas: null
                }
            };
        },

        computed: {
            cardsResumen() {
                return [
                    {label: 'Transferencias', icon: 'fas fa-random', value: Number(this.resumen.total_transferencias || 0)},
                    {label: 'Confirmadas', icon: 'fas fa-check', value: Number(this.resumen.total_confirmadas || 0)},
                    {label: 'Por confirmar', icon: 'fas fa-clock', value: Number(this.resumen.total_por_confirmar || 0)},
                    {label: 'Borradores', icon: 'fas fa-edit', value: Number(this.resumen.total_borradores || 0)},
                    {label: 'Valor confirmado', icon: 'fas fa-dollar-sign', value: this.formatToUSD(this.resumen.total_valor || 0)},
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
                    bodegaOrigenId: this.filtros.bodegaOrigenId,
                    bodegaDestinoId: this.filtros.bodegaDestinoId,
                    usuarioConfirmarId: this.filtros.usuarioConfirmarId
                };

                try {
                    this.loading = true;
                    const {data} = await axios.post(this.url + '/transferencias/getDataDashboard', datos);

                    if (data.status !== 'success') {
                        sweet_msg_dialog(data.status || 'warning', data.msg || 'No se pudo cargar el dashboard.');
                        return;
                    }

                    this.fechaDesde = datos.fechaDesde;
                    this.fechaHasta = datos.fechaHasta;
                    this.resumen = data.data.resumen || {};
                    this.estados = data.data.estados || [];
                    this.bodegasOrigen = data.data.bodegasOrigen || [];
                    this.bodegasDestino = data.data.bodegasDestino || [];
                    this.tendenciaMensual = data.data.tendenciaMensual || [];
                    this.usuariosConfirmacion = data.data.usuariosConfirmacion || [];
                    this.rutas = data.data.rutas || [];

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
                    bodegaOrigenId: null,
                    bodegaDestinoId: null,
                    usuarioConfirmarId: null
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

                this.charts.estados = Highcharts.chart('chartEstadosTransferencias', {
                    chart: {type: 'pie'},
                    title: {text: 'Transferencias por estado'},
                    tooltip: {pointFormat: '<b>{point.y}</b> transferencias'},
                    series: [{
                        name: 'Estados',
                        colorByPoint: true,
                        data: this.estados.map(item => ({
                            name: this.labelEstado(item.estado),
                            y: Number(item.total || 0)
                        }))
                    }]
                });

                this.charts.bodegasOrigen = Highcharts.chart('chartBodegasOrigenTransferencias', {
                    colors: dashboardColors,
                    chart: {type: 'column'},
                    title: {text: 'Salidas por bodega origen'},
                    xAxis: {
                        categories: this.bodegasOrigen.map(item => item.bodega || 'SIN BODEGA')
                    },
                    yAxis: {title: {text: 'Valor transferido'}},
                    tooltip: {valuePrefix: '$'},
                    plotOptions: {
                        column: {
                            colorByPoint: true
                        }
                    },
                    series: [{
                        name: 'Total',
                        data: this.bodegasOrigen.map(item => Number(item.valor || 0))
                    }]
                });

                this.charts.bodegasDestino = Highcharts.chart('chartBodegasDestinoTransferencias', {
                    colors: dashboardColors,
                    chart: {type: 'column'},
                    title: {text: 'Entradas por bodega destino'},
                    xAxis: {
                        categories: this.bodegasDestino.map(item => item.bodega || 'SIN BODEGA')
                    },
                    yAxis: {title: {text: 'Valor transferido'}},
                    tooltip: {valuePrefix: '$'},
                    plotOptions: {
                        column: {
                            colorByPoint: true
                        }
                    },
                    series: [{
                        name: 'Total',
                        data: this.bodegasDestino.map(item => Number(item.valor || 0))
                    }]
                });

                this.charts.tendencia = Highcharts.chart('chartTendenciaTransferencias', {
                    chart: {type: 'areaspline'},
                    title: {text: 'Tendencia mensual de transferencias'},
                    xAxis: {
                        categories: this.tendenciaMensual.map(item => item.periodo)
                    },
                    yAxis: {title: {text: 'Valor transferido'}},
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
                        name: 'Transferencias',
                        data: this.tendenciaMensual.map(item => Number(item.valor || 0))
                    }]
                });

                this.charts.usuarios = Highcharts.chart('chartUsuariosTransferencias', {
                    colors: dashboardColors,
                    chart: {type: 'bar'},
                    title: {text: 'Confirmadas por usuario'},
                    xAxis: {
                        categories: this.usuariosConfirmacion.map(item => item.usuario || 'SIN USUARIO')
                    },
                    yAxis: {title: {text: 'Cantidad'}},
                    plotOptions: {
                        bar: {
                            colorByPoint: true
                        }
                    },
                    series: [{
                        name: 'Total',
                        data: this.usuariosConfirmacion.map(item => Number(item.total || 0))
                    }]
                });

                this.charts.rutas = Highcharts.chart('chartRutasTransferencias', {
                    colors: dashboardColors,
                    chart: {type: 'bar'},
                    title: {text: 'Rutas principales'},
                    xAxis: {
                        categories: this.rutas.map(item => item.ruta || 'SIN RUTA')
                    },
                    yAxis: {title: {text: 'Valor transferido'}},
                    tooltip: {valuePrefix: '$'},
                    plotOptions: {
                        bar: {
                            colorByPoint: true
                        }
                    },
                    series: [{
                        name: 'Total',
                        data: this.rutas.map(item => Number(item.valor || 0))
                    }]
                });
            },

            labelEstado(estado) {
                const mapa = {
                    1: 'BORRADOR',
                    2: 'POR CONFIRMAR',
                    3: 'CONFIRMADA',
                    0: 'RECHAZADA',
                    '-1': 'ANULADA'
                };
                return mapa[estado] || estado;
            },

            formatToUSD(amount) {
                return formatToUSD(amount);
            }
        }
    });

    window.appDashboardTransferencias.mount('#app');
</script>
