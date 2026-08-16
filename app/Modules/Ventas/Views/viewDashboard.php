<!DOCTYPE html>
<!--
/**
 * Description of viewDashboard
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 9 ago 2026
 * @time 12:55:38 p.m.
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

    .dashboard-chart-lg {
        min-height: 360px;
    }
</style>

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title text-system mb-0">
                <i class="fas fa-chart-line me-2"></i> Dashboard de Ventas
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
                            <i class="fas fa-calendar me-2"></i> F. emision
                        </span>
                        <input
                            ref="dateRangeDashboard"
                            v-model="filtros.fechas"
                            type="text"
                            class="form-control"
                            placeholder="Rango de emision">
                    </div>
                </div>

                <div class="col-md-3 form-group-custom">
                    <div class="d-flex align-items-center border rounded overflow-visible">
                        <span class="input-group-text bg-cris-system">
                            <i class="fas fa-user me-2"></i> Cliente
                        </span>
                        <vue-select
                            class="flex-grow-1"
                            :options="listaClientes"
                            label="clie_razon_social"
                            v-model="filtros.clienteId"
                            :reduce="cliente => cliente.id"
                            @search="searchCliente"
                            placeholder="Todos">
                            <template #option="cliente">
                                {{ cliente.clie_razon_social }} - {{ cliente.clie_dni }}
                            </template>
                            <template #no-options>
                                Digite para buscar un cliente
                            </template>
                        </vue-select>
                    </div>
                </div>

                <div class="col-md-3 form-group-custom">
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

                <div class="col-md-3 form-group-custom">
                    <div class="d-flex align-items-center border rounded overflow-visible">
                        <span class="input-group-text bg-cris-system">
                            <i class="fas fa-project-diagram me-2"></i> Centro de costo
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

                <div class="col-md-4 form-group-custom">
                    <div class="d-flex align-items-center border rounded overflow-visible">
                        <span class="input-group-text bg-cris-system">
                            <i class="fas fa-file-invoice me-2"></i> Tipo comprobante
                        </span>
                        <vue-select
                            class="flex-grow-1"
                            :options="listaTiposComprobantes"
                            label="comp_nombre"
                            v-model="filtros.tipoComprobante"
                            :reduce="comprobante => comprobante.comp_codigo"
                            placeholder="Todos">
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
                        <div id="chartEstadosVentas" class="dashboard-chart"></div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="dashboard-box">
                        <div id="chartComprobantesVentas" class="dashboard-chart"></div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="dashboard-box">
                        <div id="chartClientesVentas" class="dashboard-chart"></div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-xl-8">
                    <div class="dashboard-box">
                        <div id="chartTendenciaVentas" class="dashboard-chart dashboard-chart-lg"></div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="dashboard-box">
                        <div id="chartBodegasVentas" class="dashboard-chart dashboard-chart-lg"></div>
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
    var dashboardComprobantes = <?= json_encode($dashboardComprobantes ?? []) ?>;
    var dashboardTopClientes = <?= json_encode($dashboardTopClientes ?? []) ?>;
    var dashboardTendenciaMensual = <?= json_encode($dashboardTendenciaMensual ?? []) ?>;
    var dashboardBodegas = <?= json_encode($dashboardBodegas ?? []) ?>;
    var listaBodegas = <?= json_encode($listaBodegas ?? []) ?>;
    var listaCentroCostos = <?= json_encode($listaCentroCostos ?? []) ?>;
    var listaTiposComprobantes = <?= json_encode($listaTiposComprobantes ?? []) ?>;
    var dashboardColors = ['#7fa685', '#5f8fb8', '#d6a94f', '#c96f5f', '#8c7bb8', '#5ea89a'];

    if (window.appVentasDashboard) {
        window.appVentasDashboard.unmount();
    }

    window.appVentasDashboard = Vue.createApp({
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
                    clienteId: null,
                    bodegaId: null,
                    centroCostoId: null,
                    tipoComprobante: null
                },
                resumen: dashboardResumen,
                estados: dashboardEstados,
                comprobantes: dashboardComprobantes,
                clientes: dashboardTopClientes,
                tendenciaMensual: dashboardTendenciaMensual,
                bodegas: dashboardBodegas,
                listaBodegas: listaBodegas,
                listaCentroCostos: listaCentroCostos,
                listaTiposComprobantes: listaTiposComprobantes,
                listaClientes: [],
                searchTimeout: null,
                charts: {
                    estados: null,
                    comprobantes: null,
                    clientes: null,
                    tendencia: null,
                    bodegas: null
                }
            };
        },

        computed: {
            cardsResumen() {
                return [
                    {label: 'Documentos', icon: 'fas fa-file-invoice', value: Number(this.resumen.total_documentos || 0)},
                    {label: 'Archivadas', icon: 'fas fa-check', value: Number(this.resumen.total_archivadas || 0)},
                    {label: 'Borradores', icon: 'fas fa-edit', value: Number(this.resumen.total_borradores || 0)},
                    {label: 'Anuladas', icon: 'fas fa-ban', value: Number(this.resumen.total_anuladas || 0)},
                    {label: 'Total ventas', icon: 'fas fa-dollar-sign', value: this.formatToUSD(this.resumen.total_ventas || 0)},
                    {label: 'IVA ventas', icon: 'fas fa-percentage', value: this.formatToUSD(this.resumen.total_iva || 0)}
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
                    clienteId: this.filtros.clienteId,
                    bodegaId: this.filtros.bodegaId,
                    centroCostoId: this.filtros.centroCostoId,
                    tipoComprobante: this.filtros.tipoComprobante
                };

                try {
                    this.loading = true;
                    const {data} = await axios.post(this.url + '/ventas/getDataDashboard', datos);

                    if (data.status !== 'success') {
                        sweet_msg_dialog(data.status || 'warning', data.msg || 'No se pudo cargar el dashboard.');
                        return;
                    }

                    this.fechaDesde = datos.fechaDesde;
                    this.fechaHasta = datos.fechaHasta;
                    this.resumen = data.data.resumen || {};
                    this.estados = data.data.estados || [];
                    this.comprobantes = data.data.comprobantes || [];
                    this.clientes = data.data.clientes || [];
                    this.tendenciaMensual = data.data.tendenciaMensual || [];
                    this.bodegas = data.data.bodegas || [];

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
                    clienteId: null,
                    bodegaId: null,
                    centroCostoId: null,
                    tipoComprobante: null
                };
                this.listaClientes = [];
                this.$refs.dateRangeDashboard._flatpickr.setDate([fechaDesdeDashboard, fechaHastaDashboard], false);
                this.cargarDashboard();
            },

            searchCliente(search) {
                clearTimeout(this.searchTimeout);

                if (!search || search.trim().length < 2) {
                    if (!this.filtros.clienteId) {
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

                this.charts.estados = Highcharts.chart('chartEstadosVentas', {
                    chart: {type: 'pie'},
                    title: {text: 'Ventas por estado'},
                    tooltip: {pointFormat: '<b>{point.y}</b> documentos'},
                    series: [{
                            name: 'Estados',
                            colorByPoint: true,
                            data: this.estados.map(item => ({
                                    name: this.labelEstado(item.estado || 'SIN ESTADO'),
                                    y: Number(item.total || 0)
                                }))
                        }]
                });

                this.charts.comprobantes = Highcharts.chart('chartComprobantesVentas', {
                    colors: dashboardColors,
                    chart: {type: 'column'},
                    title: {text: 'Ventas por comprobante'},
                    xAxis: {
                        categories: this.comprobantes.map(item => `${item.codigo} - ${item.nombre}`)
                    },
                    yAxis: {title: {text: 'Valor vendido'}},
                    tooltip: {valuePrefix: '$'},
                    plotOptions: {
                        column: {
                            colorByPoint: true
                        }
                    },
                    series: [{
                        name: 'Total',
                        data: this.comprobantes.map(item => Number(item.valor || 0))
                    }]
                });

                this.charts.clientes = Highcharts.chart('chartClientesVentas', {
                    colors: dashboardColors,
                    chart: {type: 'bar'},
                    title: {text: 'Top clientes'},
                    xAxis: {
                        categories: this.clientes.map(item => item.clie_razon_social || 'SIN CLIENTE')
                    },
                    yAxis: {title: {text: 'Valor vendido'}},
                    tooltip: {valuePrefix: '$'},
                    plotOptions: {
                        bar: {
                            colorByPoint: true
                        }
                    },
                    series: [{
                        name: 'Total',
                        data: this.clientes.map(item => Number(item.valor || 0))
                    }]
                });

                this.charts.tendencia = Highcharts.chart('chartTendenciaVentas', {
                    chart: {type: 'areaspline'},
                    title: {text: 'Tendencia mensual de ventas'},
                    xAxis: {
                        categories: this.tendenciaMensual.map(item => item.periodo)
                    },
                    yAxis: {title: {text: 'Valor vendido'}},
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
                            name: 'Ventas',
                            data: this.tendenciaMensual.map(item => Number(item.valor || 0))
                        }]
                });

                this.charts.bodegas = Highcharts.chart('chartBodegasVentas', {
                    colors: dashboardColors,
                    chart: {type: 'column'},
                    title: {text: 'Ventas por bodega'},
                    xAxis: {
                        categories: this.bodegas.map(item => item.bod_nombre || 'SIN BODEGA')
                    },
                    yAxis: {title: {text: 'Valor vendido'}},
                    tooltip: {valuePrefix: '$'},
                    plotOptions: {
                        column: {
                            colorByPoint: true
                        }
                    },
                    series: [{
                        name: 'Total',
                        data: this.bodegas.map(item => Number(item.valor || 0))
                    }]
                });
            },

            labelEstado(estado) {
                const mapa = {
                    BORRADOR: 'BORRADOR',
                    ARCHIVADO: 'ARCHIVADA',
                    ANULADA_EN_PENDIENTE: 'ANULADA EN BORRADOR',
                    ANULADA_EN_ARCHIVADA: 'ANULADA'
                };
                return mapa[estado] || estado;
            },

            formatToUSD(amount) {
                return formatToUSD(amount);
            }
        }
    });

    window.appVentasDashboard.mount('#app');
</script>
