<!DOCTYPE html>
<!--
/**
 * Description of viewTerminalMarcacion
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 24 jul 2026
 * @time 12:45:28 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title ?? 'Terminal de Marcacion' ?></title>

        <link rel="stylesheet" href="<?= base_url(); ?>/resources/plugins/fontawesome/css/all.css">
        <link rel="stylesheet" href="<?= base_url(); ?>/resources/plugins/bootstrap5/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?= base_url(); ?>/resources/plugins/vueSelect/vue-select.css">
        <link rel="stylesheet" href="<?= base_url(); ?>/resources/css/cclibrary.css">
        <link rel="stylesheet" href="<?= base_url(); ?>/resources/css/styleModules.css">
        <link rel="stylesheet" href="<?= base_url(); ?>/resources/css/styleBioTerminal.css">

        <script>
            var baseUrl = '<?= base_url(); ?>';
            var siteUrl = '<?= site_url(); ?>';
        </script>

        <script src="<?= base_url(); ?>/resources/plugins/vue/vue.global_3.5.min.js"></script>
        <script src="<?= base_url(); ?>/resources/plugins/axios/axios.min.js"></script>
        <script src="<?= base_url(); ?>/resources/plugins/vueSelect/vue-select.min.js"></script>
        <script src="<?= base_url(); ?>/resources/plugins/bootstrap5/js/bootstrap.bundle.min.js"></script>

    </head>

    <body>

        <div id="app" class="container-fluid terminal-wrapper">
            <div class="card card-system card-outline">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title text-system">
                        <i class="fas fa-tv me-2"></i> Terminal de Marcación
                    </h5>
                    <div class="terminal-header-actions d-flex align-items-center gap-2">
                        <div class="terminal-theme-menu" ref="themeMenu">
                            <button class="btn btn-outline-secondary btn-sm terminal-theme-button" title="Cambiar tema" @click="toggleTemaMenu()">
                                <i class="fas fa-palette"></i>
                            </button>

                            <div v-if="temaMenuAbierto" class="terminal-theme-dropdown">
                                <button
                                    v-for="tema in temasTerminal"
                                    :key="tema.id"
                                    type="button"
                                    class="terminal-theme-option"
                                    :class="{active: temaSeleccionado === tema.id}"
                                    @click="seleccionarTemaTerminal(tema.id)">
                                    <span class="terminal-theme-swatch" :class="tema.id"></span>
                                    <span>{{ tema.nombre }}</span>
                                </button>
                            </div>
                        </div>
                        <button class="btn btn-outline-primary btn-sm" @click="activarPantallaCompleta()">
                            <i class="fas fa-expand"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" @click="limpiarDispositivo()">
                            <i class="fas fa-sync"></i> Cambiar dispositivo
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="terminal-header mb-3">
                        <div class="row align-items-center">
                            <div class="col-xl-7 mb-3 mb-xl-0">
                                <div v-if="equipoSeleccionado">
                                    <div class="terminal-device mb-1">
                                        <span>Comedor:</span> {{ equipoSeleccionado.com_codigo }} - {{ equipoSeleccionado.com_nombre }}
                                    </div>
                                    <div class="terminal-device">
                                        <span>Dispositivo:</span> {{ equipoSeleccionado.eq_codigo }} - {{ equipoSeleccionado.eq_nombre }}
                                    </div>
                                </div>
                                <div v-else class="terminal-device text-danger">
                                    Seleccione el dispositivo de marcacion para iniciar.
                                </div>
                            </div>

                            <div class="col-xl-3 mb-3 mb-xl-0">
                                <div class="terminal-label">Servicio actual</div>
                                <div class="terminal-value">
                                    <span v-if="servicioActual">{{ servicioActual.serv_nombre }}</span>
                                    <span v-else class="text-danger">SIN SERVICIO</span>
                                </div>
                            </div>

                            <div class="col-xl-2 text-xl-end">
                                <div class="terminal-clock">{{ reloj.hora }}</div>
                                <div class="terminal-date">{{ reloj.fecha }}</div>
                            </div>
                        </div>
                    </div>

                    <div v-if="!equipoSeleccionado" class="terminal-select-device rounded p-3 mb-3">
                        <label class="col-form-label col-form-label-sm fw-bold"><i class="fal fa-fingerprint"></i> Dispositivo de marcacion</label>
                        <vue-select
                            class="border rounded"
                            :options="listaEquipos"
                            label="eq_nombre"
                            v-model="fkEquipo"
                            :reduce="equipo => equipo.id"
                            placeholder="Seleccione el dispositivo de esta terminal"
                            @option:selected="guardarDispositivo">
                            <template #option="equipo">
                                {{ equipo.com_nombre }} / {{ equipo.eq_codigo }} - {{ equipo.eq_nombre }}
                            </template>
                            <template #selected-option="equipo">
                                {{ equipo.com_nombre }} / {{ equipo.eq_codigo }} - {{ equipo.eq_nombre }}
                            </template>
                        </vue-select>
                        <div v-html="formValidacion.fkEquipo" class="text-danger"></div>
                    </div>

                    <div class="row g-3">
                        <div class="col-xl-4">
                            <div class="terminal-box h-100">
                                <h6 class="fw-bold text-system mb-3">
                                    <i class="fas fa-barcode"></i> Lectura
                                </h6>

                                <input
                                    ref="inputMarcacion"
                                    v-model.trim="identificador"
                                    @keyup.enter="registrarMarcacion()"
                                    :disabled="loadingSave || !equipoSeleccionado"
                                    type="text"
                                    class="form-control terminal-input"
                                    placeholder="LEER CODIGO"
                                    autocomplete="off" />
                                <div v-html="formValidacion.identificador" class="text-danger mt-2"></div>

                                <div v-if="loadingSave" class="alert alert-info py-2 mt-3 mb-0 text-center fw-bold">
                                    <i class="fas fa-spinner fa-spin"></i> Registrando marcacion...
                                </div>

                                <div class="small text-muted mt-3">
                                    El lector QR, RFID o biometrico debe enviar el codigo y presionar Enter automaticamente.
                                </div>

                                <hr>

                                <div class="terminal-label">Estado de lectura</div>
                                <div class="terminal-value" :class="equipoSeleccionado ? 'text-success' : 'text-danger'">
                                    <i class="fas" :class="equipoSeleccionado ? 'fa-circle-check' : 'fa-circle-xmark'"></i>
                                    {{ equipoSeleccionado ? 'LISTO PARA MARCAR' : 'SIN DISPOSITIVO' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-8">
                            <div class="terminal-result" :class="resultadoClass">
                                <div v-if="resultadoMarcacion && resultadoMarcacion.data" class="row align-items-center h-100">
                                    <div class="col-xl-4 d-flex justify-content-center mb-3 mb-xl-0">
                                        <img v-if="resultadoMarcacion.data.foto" :src="resultadoMarcacion.data.foto" class="terminal-avatar-img" />
                                        <div v-else class="terminal-avatar">{{ inicialesComensal }}</div>
                                    </div>

                                    <div class="col-xl-8">
                                        <div class="terminal-message mb-3" :class="resultadoMarcacion.status === 'success' ? 'terminal-message-success' : 'terminal-message-warning'">
                                            <i class="fas" :class="resultadoMarcacion.status === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'"></i>
                                            {{ resultadoMarcacion.msg }}
                                        </div>

                                        <div class="terminal-name mb-3">{{ resultadoMarcacion.data.comensal }}</div>

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="terminal-label">Codigo</div>
                                                <div class="terminal-value">{{ resultadoMarcacion.data.codigoComensal }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="terminal-label">Servicio</div>
                                                <div class="terminal-value">
                                                    {{ resultadoMarcacion.data.servicio }}
                                                    <span v-if="parseInt(resultadoMarcacion.data.retraso) === 1" class="badge bg-danger ms-2">CON RETRASO</span>
                                                    <span v-else class="badge bg-success ms-2">NORMAL</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="terminal-label">Contratista</div>
                                                <div class="terminal-value">{{ resultadoMarcacion.data.contratista }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="terminal-label">Proyecto</div>
                                                <div class="terminal-value">{{ resultadoMarcacion.data.proyecto }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="terminal-label">Departamento</div>
                                                <div class="terminal-value">{{ resultadoMarcacion.data.departamento || '-' }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="terminal-label">Area</div>
                                                <div class="terminal-value">{{ resultadoMarcacion.data.area || '-' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div v-else-if="resultadoMarcacion" class="d-flex align-items-center justify-content-center h-100 text-center">
                                    <div>
                                        <div class="terminal-message text-danger mb-3">
                                            <i class="fas fa-times-circle"></i> {{ resultadoMarcacion.msg }}
                                        </div>
                                        <div class="terminal-value">Verifique el identificador o la configuracion del equipo.</div>
                                    </div>
                                </div>

                                <div v-else class="d-flex align-items-center justify-content-center h-100 text-center">
                                    <div>
                                        <div class="terminal-message text-system mb-3">
                                            <i class="fas fa-fingerprint"></i> Esperando marcacion
                                        </div>
                                        <div class="terminal-value">Acerque la tarjeta, escanee el QR o registre la huella.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-xl-4">
                            <div class="terminal-status-box d-flex align-items-center">
                                <div class="terminal-status-icon me-3">
                                    <i class="fas fa-keyboard"></i>
                                </div>
                                <div>
                                    <div class="terminal-label">Modo de entrada</div>
                                    <div class="terminal-value">QR / RFID / Codigo / Biometrico</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="terminal-status-box d-flex align-items-center">
                                <div class="terminal-status-icon me-3">
                                    <i class="fas fa-bullseye"></i>
                                </div>
                                <div>
                                    <div class="terminal-label">Foco de lectura</div>
                                    <div class="terminal-value" :class="inputConFoco ? 'text-success' : 'text-warning'">
                                        {{ inputConFoco ? 'ACTIVO' : 'RECUPERANDO FOCO' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="terminal-status-box d-flex align-items-center">
                                <div class="terminal-status-icon me-3">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <div class="terminal-label">Ultima marcacion</div>
                                    <div class="terminal-value">{{ ultimaMarcacionHora || 'SIN LECTURAS' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
                    var listaEquipos = <?= json_encode($listaEquipos ?? []) ?>;

                    if (window.appBioTerminalMarcacion) {
                        window.appBioTerminalMarcacion.unmount();
                    }

                    window.appBioTerminalMarcacion = Vue.createApp({
                        components: {
                            "vue-select": window['vue-select']
                        },
                        data() {
                            return {
                                url: siteUrl,
                                urlAssets: baseUrl,
                                listaEquipos: listaEquipos,
                                fkEquipo: '',
                                identificador: '',
                                loadingSave: false,
                                formValidacion: [],
                                resultadoMarcacion: null,
                                servicioActual: null,
                                inputConFoco: false,
                                ultimaMarcacionHora: '',
                                temaSeleccionado: 'verde',
                                temaMenuAbierto: false,
                                temasTerminal: [
                                    {id: 'verde', nombre: 'Verde'},
                                    {id: 'azul', nombre: 'Azul'},
                                    {id: 'amarillo', nombre: 'Amarillo'},
                                    {id: 'gris', nombre: 'Gris'},
                                ],
                                paletasTerminal: {
                                    verde: {
                                        '--terminal-bg-glow-1': 'rgba(128, 181, 154, .16)',
                                        '--terminal-bg-glow-2': 'rgba(102, 153, 204, .10)',
                                        '--terminal-bg-1': '#1b3438',
                                        '--terminal-bg-2': '#244247',
                                        '--terminal-bg-3': '#182d33',
                                        '--terminal-card-1': 'rgba(39, 75, 80, .96)',
                                        '--terminal-card-2': 'rgba(28, 56, 62, .98)',
                                        '--terminal-body-glow': 'rgba(154, 190, 166, .07)',
                                        '--terminal-body-1': 'rgba(38, 73, 79, .98)',
                                        '--terminal-body-2': 'rgba(27, 55, 62, .98)',
                                        '--terminal-header': 'rgba(27, 56, 62, .88)',
                                        '--terminal-panel-1': 'rgba(45, 82, 88, .88)',
                                        '--terminal-panel-2': 'rgba(35, 69, 76, .92)',
                                        '--terminal-panel-soft-1': 'rgba(42, 78, 85, .90)',
                                        '--terminal-panel-soft-2': 'rgba(33, 66, 73, .92)',
                                        '--terminal-border': 'rgba(169, 213, 190, .24)',
                                        '--terminal-border-strong': 'rgba(169, 213, 190, .28)',
                                        '--terminal-accent': '#95d1ad',
                                        '--terminal-accent-soft': 'rgba(127, 166, 133, .16)',
                                        '--terminal-input-bg': 'rgba(31, 60, 66, .92)',
                                        '--terminal-input-bg-focus': 'rgba(31, 60, 66, .96)',
                                    },
                                    azul: {
                                        '--terminal-bg-glow-1': 'rgba(110, 168, 254, .17)',
                                        '--terminal-bg-glow-2': 'rgba(77, 208, 225, .10)',
                                        '--terminal-bg-1': '#172d42',
                                        '--terminal-bg-2': '#244766',
                                        '--terminal-bg-3': '#122638',
                                        '--terminal-card-1': 'rgba(40, 75, 105, .96)',
                                        '--terminal-card-2': 'rgba(28, 55, 82, .98)',
                                        '--terminal-body-glow': 'rgba(110, 168, 254, .08)',
                                        '--terminal-body-1': 'rgba(38, 72, 101, .98)',
                                        '--terminal-body-2': 'rgba(27, 53, 78, .98)',
                                        '--terminal-header': 'rgba(25, 50, 74, .88)',
                                        '--terminal-panel-1': 'rgba(46, 82, 115, .88)',
                                        '--terminal-panel-2': 'rgba(35, 66, 96, .92)',
                                        '--terminal-panel-soft-1': 'rgba(42, 78, 110, .90)',
                                        '--terminal-panel-soft-2': 'rgba(33, 64, 92, .92)',
                                        '--terminal-border': 'rgba(157, 199, 255, .24)',
                                        '--terminal-border-strong': 'rgba(157, 199, 255, .30)',
                                        '--terminal-accent': '#8fc7ff',
                                        '--terminal-accent-soft': 'rgba(143, 199, 255, .16)',
                                        '--terminal-input-bg': 'rgba(30, 58, 86, .92)',
                                        '--terminal-input-bg-focus': 'rgba(30, 58, 86, .96)',
                                    },
                                    amarillo: {
                                        '--terminal-bg-glow-1': 'rgba(255, 216, 107, .14)',
                                        '--terminal-bg-glow-2': 'rgba(255, 180, 90, .10)',
                                        '--terminal-bg-1': '#342d1d',
                                        '--terminal-bg-2': '#4b4127',
                                        '--terminal-bg-3': '#2b2518',
                                        '--terminal-card-1': 'rgba(75, 65, 39, .96)',
                                        '--terminal-card-2': 'rgba(58, 50, 31, .98)',
                                        '--terminal-body-glow': 'rgba(255, 216, 107, .07)',
                                        '--terminal-body-1': 'rgba(72, 63, 39, .98)',
                                        '--terminal-body-2': 'rgba(55, 48, 31, .98)',
                                        '--terminal-header': 'rgba(55, 48, 31, .88)',
                                        '--terminal-panel-1': 'rgba(82, 72, 45, .88)',
                                        '--terminal-panel-2': 'rgba(69, 60, 35, .92)',
                                        '--terminal-panel-soft-1': 'rgba(78, 68, 42, .90)',
                                        '--terminal-panel-soft-2': 'rgba(66, 56, 33, .92)',
                                        '--terminal-border': 'rgba(255, 216, 107, .24)',
                                        '--terminal-border-strong': 'rgba(255, 216, 107, .30)',
                                        '--terminal-accent': '#ffd86b',
                                        '--terminal-accent-soft': 'rgba(255, 216, 107, .16)',
                                        '--terminal-input-bg': 'rgba(60, 52, 32, .92)',
                                        '--terminal-input-bg-focus': 'rgba(60, 52, 32, .96)',
                                    },
                                    gris: {
                                        '--terminal-bg-glow-1': 'rgba(180, 195, 202, .12)',
                                        '--terminal-bg-glow-2': 'rgba(130, 150, 160, .09)',
                                        '--terminal-bg-1': '#263236',
                                        '--terminal-bg-2': '#34454a',
                                        '--terminal-bg-3': '#202b2f',
                                        '--terminal-card-1': 'rgba(62, 78, 84, .96)',
                                        '--terminal-card-2': 'rgba(45, 59, 65, .98)',
                                        '--terminal-body-glow': 'rgba(180, 195, 202, .06)',
                                        '--terminal-body-1': 'rgba(59, 75, 81, .98)',
                                        '--terminal-body-2': 'rgba(43, 57, 63, .98)',
                                        '--terminal-header': 'rgba(43, 57, 63, .88)',
                                        '--terminal-panel-1': 'rgba(69, 86, 92, .88)',
                                        '--terminal-panel-2': 'rgba(56, 72, 78, .92)',
                                        '--terminal-panel-soft-1': 'rgba(66, 82, 88, .90)',
                                        '--terminal-panel-soft-2': 'rgba(53, 69, 75, .92)',
                                        '--terminal-border': 'rgba(202, 216, 222, .22)',
                                        '--terminal-border-strong': 'rgba(202, 216, 222, .28)',
                                        '--terminal-accent': '#c6d5dc',
                                        '--terminal-accent-soft': 'rgba(198, 213, 220, .14)',
                                        '--terminal-input-bg': 'rgba(48, 62, 68, .92)',
                                        '--terminal-input-bg-focus': 'rgba(48, 62, 68, .96)',
                                    },
                                },
                                reloj: {
                                    fecha: '',
                                    hora: '',
                                },
                                intervalReloj: null,
                                intervalServicio: null,
                                intervalFocus: null,
                                sonidos: {},
                            };
                        },
                        computed: {
                            equipoSeleccionado() {
                                if (!this.fkEquipo) {
                                    return null;
                                }

                                return this.listaEquipos.find(equipo => parseInt(equipo.id) === parseInt(this.fkEquipo)) || null;
                            },
                            resultadoClass() {
                                if (!this.resultadoMarcacion) {
                                    return 'terminal-result-neutral';
                                }

                                if (this.resultadoMarcacion.status === 'success') {
                                    return 'terminal-result-success';
                                }

                                if (this.resultadoMarcacion.status === 'warning') {
                                    return this.resultadoMarcacion.data ? 'terminal-result-warning' : 'terminal-result-danger';
                                }

                                return 'terminal-result-danger';
                            },
                            inicialesComensal() {
                                if (!this.resultadoMarcacion || !this.resultadoMarcacion.data || !this.resultadoMarcacion.data.comensal) {
                                    return 'CC';
                                }

                                let partes = this.resultadoMarcacion.data.comensal.split(' ').filter(Boolean);
                                return partes.slice(0, 2).map(parte => parte.charAt(0)).join('');
                            },
                        },
                        mounted() {
                            this.cargarTemaTerminal();
                            this.cargarDispositivoGuardado();
                            this.actualizarReloj();
                            this.getServicioActual();
                            this.intervalReloj = setInterval(this.actualizarReloj, 1000);
                            this.intervalServicio = setInterval(this.getServicioActual, 60000);
                            this.intervalFocus = setInterval(this.focusInput, 1200);
                            document.addEventListener('click', this.cerrarTemaMenuClickFuera);
                            this.cargarSonidos();
                            this.focusInput();
                        },
                        beforeUnmount() {
                            clearInterval(this.intervalReloj);
                            clearInterval(this.intervalServicio);
                            clearInterval(this.intervalFocus);
                            document.removeEventListener('click', this.cerrarTemaMenuClickFuera);
                        },
                        methods: {
                            cargarTemaTerminal() {
                                let tema = localStorage.getItem('bioTerminalTema') || this.temaSeleccionado;
                                this.temaSeleccionado = this.paletasTerminal[tema] ? tema : 'verde';
                                this.aplicarTemaTerminal();
                            },
                            toggleTemaMenu() {
                                this.temaMenuAbierto = !this.temaMenuAbierto;
                            },
                            cerrarTemaMenuClickFuera(event) {
                                if (!this.temaMenuAbierto || !this.$refs.themeMenu) {
                                    return;
                                }

                                if (!this.$refs.themeMenu.contains(event.target)) {
                                    this.temaMenuAbierto = false;
                                    this.focusInput();
                                }
                            },
                            seleccionarTemaTerminal(temaId) {
                                this.temaSeleccionado = temaId;
                                this.temaMenuAbierto = false;
                                this.guardarTemaTerminal();
                                this.focusInput();
                            },
                            guardarTemaTerminal() {
                                localStorage.setItem('bioTerminalTema', this.temaSeleccionado);
                                this.aplicarTemaTerminal();
                            },
                            aplicarTemaTerminal() {
                                let paleta = this.paletasTerminal[this.temaSeleccionado] || this.paletasTerminal.verde;
                                Object.entries(paleta).forEach(([variable, valor]) => {
                                    document.documentElement.style.setProperty(variable, valor);
                                });
                            },
                            guardarDispositivo() {
                                if (this.equipoSeleccionado) {
                                    localStorage.setItem('bioTerminalEquipoId', this.fkEquipo);
                                    this.focusInput();
                                }
                            },
                            cargarDispositivoGuardado() {
                                let equipoId = localStorage.getItem('bioTerminalEquipoId');
                                let existeEquipo = this.listaEquipos.find(equipo => parseInt(equipo.id) === parseInt(equipoId));

                                if (existeEquipo) {
                                    this.fkEquipo = equipoId;
                                }
                            },
                            limpiarDispositivo() {
                                localStorage.removeItem('bioTerminalEquipoId');
                                this.fkEquipo = '';
                                this.resultadoMarcacion = null;
                            },
                            async getServicioActual() {
                                try {
                                    let response = await axios.get(this.url + '/biocomedor/terminal/getServicioActual');
                                    this.servicioActual = response.data.status === 'success' ? response.data.data : null;
                                } catch (e) {
                                    this.servicioActual = null;
                                }
                            },
                            async registrarMarcacion() {
                                if (this.loadingSave) {
                                    return;
                                }

                                this.formValidacion = [];
                                this.resultadoMarcacion = null;

                                if (!this.equipoSeleccionado) {
                                    this.formValidacion.fkEquipo = 'Debe seleccionar el dispositivo de marcacion.';
                                    this.playSound('warning');
                                    return;
                                }

                                if (this.identificador === '') {
                                    this.formValidacion.identificador = 'Debe leer o ingresar un identificador.';
                                    this.playSound('warning');
                                    this.focusInput();
                                    return;
                                }

                                this.loadingSave = true;

                                try {
                                    let datos = this.formData({
                                        fkEquipo: this.fkEquipo,
                                        identificador: this.identificador,
                                    });

                                    let response = await axios.post(this.url + '/biocomedor/terminal/registrarMarcacion', datos);

                                    if (response.data.status === 'vacio') {
                                        this.formValidacion = response.data.msg;
                                        this.playSound('warning');
                                    } else {
                                        this.resultadoMarcacion = response.data;
                                        this.playSound(this.getTipoSonidoRespuesta(response.data));
                                    }

                                    this.ultimaMarcacionHora = this.reloj.hora;
                                    this.identificador = '';
                                } catch (e) {
                                    this.resultadoMarcacion = {
                                        status: 'error',
                                        msg: e.response && e.response.data && e.response.data.message ? e.response.data.message : 'No se pudo registrar la marcacion.',
                                        data: null,
                                    };
                                    this.playSound('error');
                                } finally {
                                    this.loadingSave = false;
                                    this.focusInput();
                                }
                            },
                            getTipoSonidoRespuesta(response) {
                                if (!response || response.status === 'error') {
                                    return 'error';
                                }

                                if (response.status === 'success') {
                                    return 'success';
                                }

                                if (response.status === 'warning') {
                                    return response.data ? 'warning' : 'error';
                                }

                                return 'error';
                            },
                            cargarSonidos() {
                                this.sonidos = {
                                    success: new Audio(this.urlAssets + '/audio/success.mp3'),
                                    warning: new Audio(this.urlAssets + '/audio/warning.mp3'),
                                    error: new Audio(this.urlAssets + '/audio/error.mp3'),
                                };

                                Object.values(this.sonidos).forEach(audio => {
                                    audio.preload = 'auto';
                                    audio.volume = 1;
                                    audio.load();
                                });
                            },
                            playSound(tipo) {
                                try {
                                    let audio = this.sonidos[tipo] || this.sonidos.error;
                                    if (!audio) {
                                        return;
                                    }

                                    audio.pause();
                                    audio.currentTime = 0;
                                    audio.play().catch(() => {
                                    });
                                } catch (e) {
                                    console.warn('No se pudo reproducir sonido de terminal.', e);
                                }
                            },
                            activarPantallaCompleta() {
                                let element = document.documentElement;

                                if (document.fullscreenElement) {
                                    document.exitFullscreen();
                                    return;
                                }

                                if (element.requestFullscreen) {
                                    element.requestFullscreen();
                                }
                            },
                            actualizarReloj() {
                                let fecha = new Date();
                                this.reloj.hora = fecha.toLocaleTimeString('es-EC', {hour12: false});
                                this.reloj.fecha = fecha.toLocaleDateString('es-EC', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric',
                                });
                            },
                            focusInput() {
                                if (!this.equipoSeleccionado || this.temaMenuAbierto) {
                                    this.inputConFoco = false;
                                    return;
                                }

                                this.$nextTick(() => {
                                    if (this.$refs.inputMarcacion) {
                                        this.$refs.inputMarcacion.focus();
                                        this.inputConFoco = document.activeElement === this.$refs.inputMarcacion;
                                    }
                                });
                            },
                            formData(obj) {
                                var formData = new FormData();
                                for (var key in obj) {
                                    formData.append(key, obj[key]);
                                }
                                return formData;
                            },
                        },
                    });

                    window.appBioTerminalMarcacion.mount('#app');
        </script>
    </body>
</html>
