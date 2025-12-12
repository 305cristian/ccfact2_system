<!DOCTYPE html>
<!--
/**
 * Description of viewGestionTransferencias
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 4 dic 2025
 * @time 11:50:43 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">

        <!-- HEADER -->
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title text-system">
                <i class="fas fa-random me-2"></i> Gestión de Transferencias de Bodega
            </h5>
        </div>

        <div class="card-body">
            <!-- ========= FILTROS ========= -->

            <!--Rango de fechas-->
            <div class="row col-md-12 mb-3">
                <div class="col-md-3 form-group-custom">
                    <div class="input-group">
                        <span class="input-group-text bg-cris-system"><i class="fas fa-calendar me-2"></i> Rango de Fechas</span>
                        <input type="text"  ref="dateRange" v-model='trbFechas'  placeholder="Seleccione rango de fechas" class="form-control" >  
                    </div>
                </div>

                <!--#Transferencia-->
                <div class="col-md-2 form-group-custom">
                    <div class="input-group">
                        <span class="input-group-text bg-cris-system"><i class="fas fa-input-numeric me-2"></i>#</span>
                        <input type="number" class="form-control" v-model='trbSecuencial' placeholder="Ejm. 25">
                    </div>
                </div>

                <!-- Bodegas -->
                <div class="col-md-2 form-group-custom">
                    <div class="d-flex justify-content-between align-items-center border">
                        <span class="input-group-text bg-cris-system"><i class="fas fa-warehouse me-2"></i> Bod. de Origen  </span>
                        <vue-select 
                            class="flex-grow-1" 
                            :options="listaBodegas" 
                            label="bod_nombre" 
                            v-model="trbBodegaOrigen" 
                            :reduce="bodega =>bodega.id"
                            placeholder="Seleccione una bodega"/>
                    </div>
                </div>

                <div class="col-md-2 form-group-custom">
                    <div class="d-flex justify-content-between align-items-center border">
                        <span class="input-group-text bg-cris-system"><i class="fas fa-warehouse me-2"></i> Bod. de Destino  </span>
                        <vue-select 
                            class="flex-grow-1" 
                            :options="listaBodegas" 
                            label="bod_nombre" 
                            v-model="trbBodegaDestino" 
                            :reduce="bodega =>bodega.id"
                            placeholder="Seleccione una bodega"/>
                    </div>
                </div>

                <!--Usuario a confirmar-->
                <div class="col-md-3 form-group-custom">
                    <div class="d-flex justify-content-between align-items-center border">
                        <span class="input-group-text bg-cris-system"><i class="fas fa-warehouse me-2"></i> Usuario a confirmar  </span>
                        <vue-select 
                            class="flex-grow-1" 
                            :options="listaUsuarios" 
                            label="empleado" 
                            v-model="trbUsuarioConfirmar" 
                            :reduce="emp =>emp.id"
                            placeholder="Seleccione el usuario a confirmar"/>
                    </div>
                </div>

                <!-- ========= TABS ESTADOS ========= -->
                <ul class="nav nav-pills mb-3">
                    <li class="nav-item" v-for="estado in estados" :key="estado.value">
                        <button class="nav-link border-bottom"
                                :class="{ active: estadoActivo === estado.value }"
                                @click="cambiarEstado(estado.value)">
                            <i :class="estado.icon"></i>
                            {{ estado.label }}
                            <span v-if="loading" class="badge ms-2 loading-spin" :class="estado.badge"><i class="fas fa-spin"></i></span>
                            <span v-else class="badge ms-2":class="estado.badge">{{contadores[estado.value] ?? 0 }}</span>
                        </button>
                    </li>
                </ul>

                <!--                <div class="col-md-2">
                                    <button class="btn btn-outline-system w-100"
                                            @click="searchTransferencias">
                                        <i class="fas fa-search me-1"></i> Buscar Transferencias
                                    </button>
                                </div>-->
            </div>

            <!-- ========= TABLA ========= -->
            <hr>
            <div v-show='panelMain' >
                <div class="table-responsive">
                    <table id="tblTransferencias" class="table table-striped w-100">
                        <thead class="bg-system text-white">
                            <tr>
                                <th style="width: 5px">ACCIONES</th>
                                <th style="width: 5px">CÓDIGO</th>
                                <th>FECHA</th>
                                <th>BODEGA DE ORIGEN</th>
                                <th>BODEGA DE DESTINO</th>
                                <th>USUARIO A CONFIRMAR</th>
                                <th># ITEMS</th>
                                <th>TOTAL</th>
                                <!--<th>CENTRO DE COSTOS</th>-->
                                <th>ESTADO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="trb in listaTransferencias" :key="trb.id">
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline"
                                                data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <button class="dropdown-item"
                                                        @click="verDetalle(trb)">
                                                    <i class="fas fa-eye me-2"></i> Ver detalle
                                                </button>
                                            </li>

                                            <li>
                                                <button class="dropdown-item"
                                                        :disabled="trb.trb_estado != 1"
                                                        @click="loadTransferenciaEdit(trb.id)">
                                                    <i class="fas fa-edit me-2"></i> Editar
                                                </button>
                                            </li>
                                            <li v-if="trb.trb_estado == 0">
                                                <button class="dropdown-item"
                                                        :disabled="trb.trb_estado != 1"
                                                        @click="editarTransferencia(trb.id)">
                                                    <i class="fas fa-file-edit me-2"></i> Corregir Transferencia
                                                </button>
                                            </li>

                                            <li>
                                                <button class="dropdown-item text-primary"
                                                        @click="clonarTrasferencia(trb.id)">
                                                    <i class="fas fa-clone me-2"></i> Clonar
                                                </button>
                                            </li>

                                            <li>
                                                <button class="dropdown-item text-danger"
                                                        v-if="trb.trb_estado != -1"
                                                        @click="anularTransferencia(trb.id)">
                                                    <i class="fas fa-ban me-2"></i> Anular
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>

                                <td  style="width: 5px">{{ zFill(trb.trb_secuencial, 5) }}</td>
                                <td style="width: 5px">{{ trb.trb_fecha }}</td>
                                <td>{{ trb.bodega_origen }}</td>
                                <td>{{ trb.bodega_destino }}</td>
                                <td>{{ trb.user_confirma }}</td>
                                <td>{{ trb.trb_total_items }}</td>
                                <td>{{ formatToUSD(trb.trb_total) }}</td>
                                <!--<td>{{ trb.cc_nombre ?? '-' }}</td>-->

                                <td>
                                    <span class="badge"
                                          :class="badgeEstado(trb.trb_estado)">
                                        <i :class="iconEstado(trb.trb_estado)"></i>
                                        {{ labelEstado(trb.trb_estado) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!--MODAL DETALLE-->
        <?php echo view('\Modules\Transferencias\Views\reportes\viewModalReport') ?>
        <!--CLOSE MODAL DETALLE-->

        <!--MODAL EMAIL-->
        <?php echo view('\Modules\Transferencias\Views\viewModalEmail') ?>
        <!--CLOSE MODAL EMAIL-->


    </div>
</div>

<script type="text/javascript">

    var fechaActual = DateTime.now().toFormat('yyyy-MM-dd');
    var listaBodegas = <?= json_encode($listaBodegas); ?>;
    var listaUsuarios = <?= json_encode($listaUsuarios); ?>;
    var userSession = <?= json_encode($userSession); ?>;
    var rootUser = <?= json_encode($rootUser); ?>;

    if (window.appTransfer) {
        window.appTransfer.unmount();
    }


    window.appTransfer = Vue.createApp({
        components: {
            "vue-select": window['vue-select']
        },
        data() {
            return {
                url: siteUrl,
                userSession: userSession,
                rootUser: rootUser,
                loading: false,
                panelMain: false,

                estados: [
                    {value: 1, label: 'Borrador', icon: 'fas fa-edit', badge: 'bg-warning'},
                    {value: 2, label: 'Por Confirmar', icon: 'fas fa-clock', badge: 'bg-primary'},
                    {value: 3, label: 'Confirmadas', icon: 'fas fa-check', badge: 'bg-success'},
                    {value: 0, label: 'Rechazadas', icon: 'fas fa-file-alt', badge: 'bg-danger'},
                    {value: -1, label: 'Anuladas', icon: 'fas fa-ban', badge: 'bg-danger'}
                ],

                estadoActivo: 2,

                //PARA MODAL DETALLE
                dataTransf: '',
                cargandoDetalle: false,
                modalInstance: null,

                //FILTROS
                trbFechas: fechaActual,
                trbSecuencial: '',
                trbBodegaOrigen: '',
                trbBodegaDestino: '',
                trbEstado: '',
                trbUsuarioConfirmar: '',

                //LISTAS
                listaBodegas: listaBodegas,
                listaUsuarios: listaUsuarios,
                listaTransferencias: [],
                contadores: {},

                //PARA ENVIO DE EMAIL
                emailData: {
                    para: '',
                    cc: '',
                    asunto: '',
                    mensaje: ''
                },

                errorSendMail: '',
                loadingEmail: false,
                modalInstanceEmail: null
            };
        },

        mounted() {
            flatpickr(this.$refs.dateRange, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                locale: 'es',
                allowInput: true,
                onChange: (_, dateStr) => {
                    this.trbFechas = dateStr;
                }
            });

            //Iniciamos los contadores
            this.cargarContadores();

            // Inicializar modal de Bootstrap
            this.modalInstance = new bootstrap.Modal(this.$refs.modalReport);
            this.modalInstanceEmail = new bootstrap.Modal(this.$refs.modalSendEmail);
        },

        methods: {

            cambiarEstado(estado) {
                this.estadoActivo = estado;
                this.trbEstado = estado;
                this.searchTransferencias();
            },

            async searchTransferencias() {
                const datos = {
                    trbSecuencial: this.trbSecuencial,
                    trbBodegaOrigen: this.trbBodegaOrigen,
                    trbBodegaDestino: this.trbBodegaDestino,
                    trbEstado: this.trbEstado,
                    trbFechas: this.trbFechas,
                    trbUsuarioConfirmar: this.trbUsuarioConfirmar,
                };
                try {
                    swalLoading('Cargando Transferencias');
                    const {data} = await axios.post(this.url + '/transferencias/searchTransferencias', datos);
                    if (data.status === 'success') {
                        this.panelMain = true;
                        this.listaTransferencias = data.data;
                        this.cargarContadores();
                        Swal.close();
                    } else {
                        sweet_msg_dialog('warning', 'No se han encontrado ajustes registrados en los parametros especificados');
                        this.panelMain = false;
                    }
                    dataTable('#tblTransferencias', 'Listado de transferencias de salida');
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data?.message || e.message);
                }
            },

            async cargarContadores() {
                const datos = {
                    trbFechas: this.trbFechas
                };
                try {
                    this.loading = true;
                    const {data} = await axios.post(this.url + '/transferencias/contadoresTransferencias', datos);
                    if (data.status === 'success') {
                        this.contadores = data.data;
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data?.message || e.message);
                } finally {
                    this.loading = false;
                }



            },

            async verDetalle(transferencia) {
                this.dataTransf = transferencia;
                this.cargandoDetalle = true;
                this.modalInstance.show();
                try {
                    const {data} = await axios.get(this.url + '/transferencias/getDataDetalle/' + transferencia.id);
                    this.cargandoDetalle = false;
                    await Vue.nextTick();
                    const modal = document.getElementById('detalleTransferenciaModal');
                    modal.innerHTML = data;
                } catch (error) {
                    sweet_msg_dialog('error', '', '', 'Error al cargar el detalle de la transferencia, ' + error.message);
                } finally {
                    this.cargandoDetalle = false;
                }
            },

            generarExcel() {
                const contenido = document.getElementById('contentExport');
                const titulo = `Transferencia_${this.zFill(this.dataTransf.trb_secuencial, 5)}`;
                return generarExcelContent(contenido, titulo);
            },
            // ==========================================
            // EXPORTAR A PDF
            // ==========================================
            generarPDF() {
//                try {
//                    window.open(`${this.url}/transferencias/generarPDF/${this.idAjuste}?download=1`, '_blank');
//                } catch (e) {
//                    sweet_msg_dialog('error', '', '', 'Error al generar el documento, ' + e.message);
//                }
            },
            openModalEmail(transferencia) {
//               this.dataTransf = transferencia;
//                this.emailData = {
//                    para: 'it@cateringclp.com, pcris.994@gmail.com',
//                    cc: '',
//                    asunto: `Reporte de Transferencia #${transferencia.trb_secuencial}`,
//                    mensaje: 'Estimado(a), adjunto el reporte solicitado.'
//                };
//                this.modalInstanceEmail.show();
            },

            async sendEmailReport() {
//
//                if (!this.emailData.para || !this.emailData.asunto) {
//                    this.errorSendMail = "⚠️ Debe completar los campos obligatorios (Para, Asunto)"
//                    return;
//                }
//
//                let datos = this.emailData;
//                datos.idTransferencia = this.dataTransf.id;
//
//                try {
//                    this.loadingEmail = true;
//                    const {data} = await axios.post(`${this.url}/transferencias/sendEmailReport`, datos);
//                    if (data.status === 'success') {
//                        sweet_msg_toast('success', data.msg);
//                        this.modalInstanceEmail.hide();
//                        this.emailData = {
//                            para: '',
//                            cc: ''
//                        };
//                        this.loadingEmail = false;
//                        sweet_msg_dialog('success', data.msg);
//                    } else {
//                        this.errorSendMail = data.msg;
//                    }
//                } catch (error) {
//                    this.errorSendMail = 'Error al enviar email: ' + error.message;
//                } finally {
//                    this.loadingEmail = false;
//                }
            },

            async loadTransferenciaEdit(id) {

                try {
                    swalLoading('Cargando documento');
                    const {data} = await axios.get(this.url + '/transferencias/loadTransferenciaEdit/' + id);
                    if (data.status === 'success') {
                        window.location.href = data.redirect;
                    } else {
                        sweet_msg_dialog('error', data.msg);
                    }
                    Swal.close();
                } catch (e) {
                    sweet_msg_dialog('error', '', '', 'Error al cargar el detalle de la transferencia, ' + e.message);
                }

            },

            confirmarTransferencia(id) {
                Swal.fire({
                    title: 'Confirmar Transferencia?',
                    icon: 'question',
                    showCancelButton: true
                }).then(async r => {
                    if (r.isConfirmed) {
                        await axios.post(this.url + '/transferencias/confirmar', {id});
                        this.searchTransferencias();
                    }
                });
            },
            rechazarTransferencia(id) {
                Swal.fire({
                    title: 'Recharzar Transferencia?',
                    icon: 'question',
                    showCancelButton: true
                }).then(async r => {
                    if (r.isConfirmed) {
                        await axios.post(this.url + '/transferencias/rechazar', {id});
                        this.searchTransferencias();
                    }
                });
            },

            clonarTrasferencia(id) {
                Swal.fire({
                    title: "Anular transferencia",
                    input: "textarea",
                    showCancelButton: true
                }).then(async r => {
                    if (r.isConfirmed) {
                        await axios.post(this.url + '/transferencias/anular', {
                            transferenciaId: id,
                            motivo: r.value
                        });
                        this.searchTransferencias();
                    }
                });
            },
            anularTransferencia(id) {
                Swal.fire({
                    title: "Anular transferencia",
                    input: "textarea",
                    showCancelButton: true
                }).then(async r => {
                    if (r.isConfirmed) {
                        await axios.post(this.url + '/transferencias/anular', {
                            transferenciaId: id,
                            motivo: r.value
                        });
                        this.searchTransferencias();
                    }
                });
            },

            badgeEstado(estado) {
                const mapa = {
                    1: 'bg-warning',
                    2: 'bg-primary',
                    3: 'bg-success',
                    0: 'bg-danger',
                    '-1': 'bg-danger'
                };
                return mapa[estado] || 'bg-default';
            },

            labelEstado(estado) {
                const mapa = {
                    1: 'BORRADOR',
                    2: 'POR CONFIRMAR',
                    3: 'CONFIRMADA',
                    0: 'RECHAZADA',
                    '-1': 'ANULADA'
                };

                return mapa[estado] || 'DESCONOCIDO';
            },
            iconEstado(estado) {
                const mapa = {
                    1: 'fas fa-edit',
                    2: 'fas fa-clock',
                    3: 'fas fa-check-double',
                    0: 'fas fa-file-alt',
                    '-1': 'fas fa-ban'
                };
                return mapa[estado] || 'fas fa-status';
            },

            formatToUSD(amount) {
                return formatToUSD(amount);
            },
            zFill(value, size) {
                return zFill(value, size);
            }
        }
    });

    window.appTransfer.mount('#app');

</script>
