<!DOCTYPE html>
<!--
/**
 * Description of viewDashboard
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 23 jul 2026
 * @time 10:27:05 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<style>
    .bio-dashboard-card {
        border: 1px solid #d8e2dc;
        border-left: 4px solid #7fa685;
        border-radius: 6px;
        background: #fff;
        padding: 14px 16px;
        min-height: 92px;
    }

    .bio-dashboard-label {
        color: #5c6770;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .bio-dashboard-value {
        color: #0b1f33;
        font-size: 22px;
        font-weight: 800;
    }

    .bio-dashboard-box {
        border: 1px solid #d8e2dc;
        border-radius: 6px;
        background: #fff;
        padding: 12px;
    }
</style>

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title text-system">
                <i class="fas fa-utensils me-2"></i> Dashboard Bio Comedor
            </h5>
            <small class="text-muted">Control de marcaciones y consumos</small>
        </div>

        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-xl-2 col-md-4 col-sm-6" v-for="card in cardsResumen" :key="card.label">
                    <div class="bio-dashboard-card">
                        <div class="bio-dashboard-label">
                            <i :class="card.icon"></i> {{ card.label }}
                        </div>
                        <div class="bio-dashboard-value">{{ card.value }}</div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-xl-6">
                    <div class="bio-dashboard-box">
                        <h6 class="fw-bold text-system mb-2">
                            <i class="fas fa-chart-column me-1"></i> Consumos por servicio
                        </h6>
                        <div id="chartBioServicios" style="height: 320px;"></div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="bio-dashboard-box">
                        <h6 class="fw-bold text-system mb-2">
                            <i class="fas fa-chart-line me-1"></i> Marcaciones del día
                        </h6>
                        <div id="chartBioMarcaciones" style="height: 320px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    if (window.appBioDashboard) {
        window.appBioDashboard.unmount();
    }

    window.appBioDashboard = Vue.createApp({
        data() {
            return {
                cardsResumen: [
                    {label: 'Comensales activos', value: 0, icon: 'fas fa-users me-1'},
                    {label: 'Marcaciones hoy', value: 0, icon: 'fas fa-fingerprint me-1'},
                    {label: 'Consumos válidos', value: 0, icon: 'fas fa-check-circle me-1'},
                    {label: 'Repetidas', value: 0, icon: 'fas fa-copy me-1'},
                    {label: 'Comedores', value: 0, icon: 'fas fa-building me-1'},
                    {label: 'Equipos', value: 0, icon: 'fas fa-microchip me-1'},
                ],
            };
        },
        mounted() {
            this.renderCharts();
        },
        methods: {
            renderCharts() {
                if (typeof Highcharts === 'undefined') {
                    return;
                }

                Highcharts.chart('chartBioServicios', {
                    chart: {type: 'column'},
                    title: {text: ''},
                    xAxis: {categories: ['Desayuno', 'Almuerzo', 'Merienda', 'Cena']},
                    yAxis: {title: {text: 'Consumos'}},
                    series: [{
                            name: 'Consumos',
                            colorByPoint: true,
                            data: [0, 0, 0, 0]
                        }],
                    credits: {enabled: false}
                });

                Highcharts.chart('chartBioMarcaciones', {
                    chart: {type: 'spline'},
                    title: {text: ''},
                    xAxis: {categories: ['06:00', '08:00', '10:00', '12:00', '14:00', '16:00', '18:00']},
                    yAxis: {title: {text: 'Marcaciones'}},
                    series: [{
                            name: 'Marcaciones',
                            data: [0, 0, 0, 0, 0, 0, 0]
                        }],
                    credits: {enabled: false}
                });
            }
        }
    });
    window.appBioDashboard.mount('#app');
</script>
