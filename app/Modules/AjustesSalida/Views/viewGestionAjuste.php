<!DOCTYPE html>
<!--
/**
 * Description of viewGestionAjuste
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 29 nov 2025
 * @time 3:19:05 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fad fa-sort-amount-down-alt"></i> Gestion de Ajustes de Salida</h5>
        </div>
        <div class="card-body">
            <div class="row col-md-12">

                <div class="col-md-3 form-group-custom">
                    <div class="input-group">
                        <span class="input-group-text bg-cris-system"><i class="fas fa-calendar me-2"></i> Rango de Fechas</span>
                        <input type="text"  ref="dateRange" v-model='ajesFechas'  placeholder="Seleccione rango de fechas" class="form-control" data-style="btn-white">  
                    </div>
                </div>

                <!--#Ajuste-->
                <div class="col-md-2 form-group-custom">
                    <div class="input-group">
                        <span class="input-group-text bg-cris-system"><i class="fas fa-input-numeric me-2"></i>#AJUSTE</span>
                        <input type="number" class="form-control" v-model='ajesSecuencial' placeholder="Ejm. 25">
                    </div>
                </div>

                <!-- Bodega -->
                <div class="col-md-2 form-group-custom">
                    <div class="d-flex justify-content-between align-items-center border">
                        <span class="input-group-text bg-cris-system"><i class="fas fa-warehouse me-2"></i> Bodega  </span>
                        <vue-select 
                            class="flex-grow-1" 
                            :options="listaBodegas" 
                            label="bod_nombre" 
                            v-model="ajesBodega" 
                            :reduce="bodega =>bodega.id"
                            placeholder="Seleccione una bodega"/>
                    </div>
                </div>
                <!-- Motivo de Ajuste -->
                <div class="col-md-2 form-group-custom">
                    <div class="d-flex justify-content-between align-items-center border">
                        <span class="input-group-text bg-cris-system"><i class="fas fa-tag me-2"></i>Motivo</span>
                        <vue-select 
                            class="flex-grow-1"
                            :options="listaMotivos"
                            label="motivo"
                            v-model="ajesMotivo"
                            :reduce ="motivo =>motivo.id"
                            placeholder="Seleccione un motivo"/>
                    </div>
                </div>

                <!-- Centro de Costo -->
                <div class="col-md-3 form-group-custom">
                    <div class="d-flex justify-content-between align-items-center border">
                        <span class="input-group-text bg-cris-system"><i class="fas fa-project-diagram me-2"></i>Centro de Costo</span>
                        <vue-select 
                            class="flex-grow-1"
                            :options="listaCentroCostos"
                            label="cc_nombre"
                            v-model="ajesCentrocosto"
                            :reduce ="cc =>cc.id"
                            placeholder="Seleccione un centro de costos"/>
                    </div>
                </div>
                <!-- Estado -->
                <div class="col-md-2 form-group-custom">
                    <div class="input-group">
                        <span class="input-group-text bg-cris-system"><i class="fas fa-toggle-on me-2"></i>Estado</span>
                        <select title="Seleccione un estado" v-model="ajesEstado" class="form-select show-tick borderspk" data-style="btn-white">               
                            <option value="2">ARCHIVADAS</option>
                            <option value="1">EN BORRADOR</option>
                            <option value="-1">ANULADAS</option>
                        </select>
                    </div>
                </div>

                <!-- Botones de Seleccion -->
                <div class="col-md-4 form-group-custom">
                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                        <input type="radio" class="btn-check"  id="btnradio1" value="AJUSTE_MERMA" v-model="ajesTipo"  autocomplete="off" >
                        <label class="btn btn-outline-danger" for="btnradio1"> <i class="fas fa-skull-crossbones me-2"></i> Merma / Daño</label>
                        <input type="radio" class="btn-check"  id="btnradio2" value="CONSUMO_INTERNO" v-model="ajesTipo" autocomplete="off">
                        <label class="btn btn-outline-primary" for="btnradio2"> <i class="fas fa-utensils me-2"></i> Consumo Interno</label>
                        <input type="radio" class="btn-check"  id="btnradio3" value="DESPACHO" v-model="ajesTipo" autocomplete="off" checked>
                        <label class="btn btn-outline-success" for="btnradio2"> <i class="fas fa-clipboard-check me-3"></i> Despacho</label>
                    </div>                  
                </div>

                <div class="col-md-2">
                    <button class="btn btn-system" @click='searchAjustes()'><i class="fas fa-search"></i>BUSCAR AJUSTES</button>

                </div>
            </div>


            <div v-show='panelMain' >
                <div class="table-responsive" >
                    <table id="tblAjustes" class="table table-striped nowrap w-100" >
                        <thead class="bg-system text-white">
                            <tr>
                                <th style="width: 5px">ACIONES</th>
                                <th style="width: 5px">CÓDIGO</th>
                                <th>FECHA</th>
                                <th>TOTAL</th>
                                <th>OBSERVACIONES</th>
                                <th>BODEGA</th>
                                <th>C. COSTO</th>
                                <th>SERVICIO</th>
                                <th>ESTADO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for='laj of listaAjustes'>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-outline" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span><i class="fas fa-ellipsis-v"></i></span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li><button class="dropdown-item" href="#" @click.prevent="verDetalle(laj)"><span><i class="fas fa-clipboard-list"></i> Ver Detalle</span></button> </li>
                                            <li><button :disabled="laj.ajes_estado == 2 ? true : false " class="dropdown-item" href="#"  @click.prevent="loadAjusteEdit(laj.id)"> <span><i class="fas fa-edit"></i> Modificar Ajuste</span></button></li>
                                            <li><button class="dropdown-item" href="#" @click.prevent="anularAjuste(laj.id)"><span><i class="fas fa-stop-circle"></i>  Anular Ajuste</span></button></li>
                                            <li><button class="dropdown-item" href="#" @click.prevent="openModalEmail(laj)"><span><i class="fas fa-clone"></i>  Enviar por Email</span> </button></li>
                                            <li><button class="dropdown-item" href="#" @click.prevent="clonarAjuste(laj.id)"><span><i class="fas fa-clone"></i>  Clonar Ajuste</span> </button></li>
                                        </ul>
                                    </div>
                                    <!--<button @click="loadAjuste(laj), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalAnillos"><i class="fas fa-edit"></i> </button>-->
                                </td>
                                <td>{{zFill(laj.ajes_secuencial,4)}}</td>
                                <td>{{laj.ajes_fecha}}</td>
                                <td>{{laj.ajes_total}}</td>
                                <td>{{laj.ajes_observaciones?laj.ajes_observaciones:'-'}}</td>
                                <td>{{laj.bod_nombre}}</td>
                                <td>{{laj.cc_nombre}}</td>
                                <td>{{laj.serv_nombre}}</td>

                                <td>
                                    <span v-if="laj.ajes_estado == 2" class="badge bg-success"><i class="fas fa-check-double"></i>  ARCHIVADO</span>
                                    <span v-else-if="laj.ajes_estado == 1" class="badge bg-warning"><i class="fas fa-warning"></i>  BORRADOR</span>
                                    <span v-else-if="laj.ajes_estado == -1" class="badge bg-danger"><i class="fas fa-stop-circle"></i>  ANULADO</span>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div> 
            </div> 

        </div>

        <!--MODAL DETALLE-->
        <?php echo view('\Modules\AjustesSalida\Views\reportes\viewModalReport') ?>
        <!--CLOSE MODAL DETALLE-->

        <!--MODAL EMAIL-->
        <?php echo view('\Modules\AjustesSalida\Views\viewModalEmail') ?>
        <!--CLOSE MODAL EMAIL-->
    </div>
</div>
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>

<script type="text/javascript">

var fechaActual = DateTime.now().toFormat('yyyy-MM-dd');
var listaBodegas = <?= json_encode($listaBodegas); ?>;
var listaMotivos = <?= json_encode($listaMotivos); ?>;
var listaCentroCostos = <?= json_encode($listaCentroCostos); ?>;

if (window.appGestionAjs) {
    window.appGestionAjs.unmount();
}

window.appGestionAjs = Vue.createApp({
    components: {
        "vue-select": window['vue-select']
    },

    data() {
        return {
            url: siteUrl,
            pathUrl: baseUrl,
            panelMain: false,

            idAjuste: '',
            secuencialAjuste: '',
            listaAjustes: [],
            cargandoDetalle: false,
            modalInstance: null,

            //LISTAS FILTROS
            listaBodegas: listaBodegas,
            listaMotivos: listaMotivos,
            listaCentroCostos: listaCentroCostos,

            //FILTROS DE BUSQUEDA
            ajesSecuencial: '',
            ajesBodega: '',
            ajesMotivo: '',
            ajesCentrocosto: '',
            ajesEstado: '2',
            ajesTipo: 'DESPACHO',
            ajesFechas: fechaActual,

            // Variables para Flatpickr
            flatpickrInstance: null,

            //PARA ENVIO DE EMAIL
            emailData: {
                para: '',
                cc: '',
                asunto: 'xxx',
                mensaje: ''
            },
            errorSendMail: '',
            loadingEmail: false,
            modalInstanceEmail: null
        };
    },
    created() {

    },

    mounted() {

        // Inicializar Flatpickr
        this.flatpickrInstance = flatpickr(this.$refs.dateRange, {
            mode: 'range',
            dateFormat: 'Y-m-d',
            locale: 'es',
            allowInput: true,
            onChange: (selectedDates, dateStr) => {
                this.ajesFechas = dateStr;
            }
        });
        // Inicializar modal de Bootstrap
        this.modalInstance = new bootstrap.Modal(this.$refs.modalReport);
        this.modalInstanceEmail = new bootstrap.Modal(this.$refs.modalSendEmail);
    },
    methods: {

        async searchAjustes() {

            const datos = {
                ajesSecuencial: this.ajesSecuencial,
                ajesBodega: this.ajesBodega,
                ajesMotivo: this.ajesMotivo,
                ajesCentrocosto: this.ajesCentrocosto,
                ajesEstado: this.ajesEstado,
                ajesFechas: this.ajesFechas,
                ajesTipo: this.ajesTipo
            };

            try {
                swalLoading('Cargando Ajustes');
                const {data} = await axios.post(this.url + '/ajustessalida/searchAjustes', datos);
                if (data.status === 'success') {
                    this.panelMain = true;
                    this.listaAjustes = data.data;
                    Swal.close();
                } else {
                    sweet_msg_dialog('warning', 'No se han encontrado ajustes registrados en los parametros especificados');
                    this.panelMain = false;
                }
                dataTable('#tblAjustes', 'Listado de ajustes de salida');
            } catch (e) {
                sweet_msg_dialog('error', '', '', e.response.data?.message || e.message);
            }

        },

        async loadAjusteEdit(idAjuste) {

            try {
                swalLoading('Cargando documento');
                const {data} = await axios.get(this.url + '/ajustessalida/loadAjusteEdit/' + idAjuste);
                if (data.status === 'success') {
                    window.location.href = data.redirect;
                } else {
                    sweet_msg_dialog('error', data.msg);
                }
                Swal.close();
            } catch (e) {
                sweet_msg_dialog('error', '', '', 'Error al cargar el detalle del ajuste, ' + e.message);
            }

        },

        // Ver detalle del ajuste
        async verDetalle(ajuste) {
            this.idAjuste = ajuste.id;
            this.secuencialAjuste = ajuste.ajes_secuencial;
            this.cargandoDetalle = true;
            this.modalInstance.show();
            try {

                const {data} = await axios.get(this.url + '/ajustessalida/getDataDetalle/' + ajuste.id);
                this.cargandoDetalle = false;
                await Vue.nextTick();
                const modal = document.getElementById('detalleAjusteModal');
                modal.innerHTML = data;

            } catch (error) {
                sweet_msg_dialog('error', '', '', 'Error al cargar el detalle del ajuste, ' + error.message);
            } finally {
                this.cargandoDetalle = false;
            }
        },
        async anularAjuste(ajusteId) {
            Swal.fire({
                title: "¿Está seguro de anular este Ajuste?",
                text: "Esta acción revertirá los movimientos de stock generados",
                icon: "warning",
                input: "textarea",
                inputPlaceholder: "Ingrese el motivo de anulación...",
                showCancelButton: true,
                confirmButtonText: "Sí, anular",
                confirmButtonColor: "#bb2d3b",
                inputValidator: (value) => {
                    if (!value) {
                        return "Debe ingresar un motivo de anulación";
                    }
                }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    swalLoading('Anulando...');
                    let datos = {
                        ajusteId: ajusteId,
                        motivoAnulacion: result.value
                    };
                    try {
                        let {data} = await axios.post(this.url + '/ajustessalida/anularAjuste', datos);
                        sweet_msg_dialog(data.status, data.msg);
                    } catch (e) {
                        sweet_msg_dialog('error', '', '', e.response.data?.message || e.message);
                    }
                }
            });
        },

        // ==========================================
        // EXPORTAR A EXCEL
        // ==========================================

        generarExcel() {
            const contenido = document.getElementById('contentExport');
            const titulo = `Ajuste_Salida_${this.zFill(this.secuencialAjuste, 5)}`;
            return generarExcel(contenido, titulo);
        },
        // ==========================================
        // EXPORTAR A PDF
        // ==========================================
        generarPDF() {
            try {
                window.open(`${this.url}/ajustessalida/generarPDF/${this.idAjuste}?download=1`, '_blank');
            } catch (e) {
                sweet_msg_dialog('error', '', '', 'Error al generar el documento, ' + e.message);
            }
        },
        async clonarAjuste(idAjuste) {
            try {
                swalLoading('Clonando ajuste...');
                const {data} = await axios.get(`${this.url}/ajustessalida/clonarAjuste/${idAjuste}`);
                if (data.status === 'success') {
                    window.location.href = data.redirect;
                    Swal.close();
                } else {
                    sweet_msg_dialog('error', 'Ha ocurrido un error al tratar de clonar el ajuste');
                }
            } catch (e) {
                sweet_msg_dialog('error', '', '', e.message);
            }

        },

        openModalEmail(ajuste) {
            this.idAjuste = ajuste.id;
            this.secuencialAjuste = ajuste.ajes_secuencial;
            this.emailData = {
                para: 'it@cateringclp.com, pcris.994@gmail.com',
                cc: '',
                asunto: `Reporte de Ajuste de Salida #${ajuste.ajes_secuencial}`,
                mensaje: 'Estimado(a), adjunto el reporte solicitado.'
            };
//            Vue.nextTick();
            this.modalInstanceEmail.show();
        },

        async sendEmailReport() {

            if (!this.emailData.para || !this.emailData.asunto) {
                this.errorSendMail = "⚠️ Debe completar los campos obligatorios (Para, Asunto)"
                return;
            }

            let datos = this.emailData;
            datos.idAjuste = this.idAjuste;

            try {
                this.loadingEmail = true;
                const {data} = await axios.post(`${this.url}/ajustessalida/sendEmailReport`, datos);
                if (data.status === 'success') {
                    console.log('succes');
                    sweet_msg_toast('success', data.msg);
                    this.modalInstanceEmail.hide();
                    this.emailData = {
                        para: '',
                        cc: ''
                    };
                    this.loadingEmail = false;
                    sweet_msg_dialog('success', data.msg);
                } else {
                    this.errorSendMail = data.msg;
                }
            } catch (error) {
                this.errorSendMail = 'Error al enviar email: ' + error.message;
            } finally {
                this.loadingEmail = false;
            }
        },

        zFill(value, size) {
            return zFill(value, size);
        }

    }
});
window.appGestionAjs.mount('#app');
</script>