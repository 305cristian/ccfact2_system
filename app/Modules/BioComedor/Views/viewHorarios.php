<!DOCTYPE html>
<!--
/**
 * Description of viewHorarios
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 24 jul 2026
 * @time 1:09:07 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-calendar-clock"></i> Horarios de Servicios</h5>
        </div>

        <div class="card-body">
            <div v-if="loadingList" class="alert alert-info py-2">
                <i class="fas fa-spinner fa-spin me-1"></i> Cargando horarios...
            </div>

            <div style="overflow-x: auto">
                <table id="tblHorarios" class="table table-striped nowrap display" style="width: 100%">
                    <thead class="bg-system text-white">
                        <tr>
                            <td>ID</td>
                            <td>SERVICIO</td>
                            <td>HORA INICIO</td>
                            <td>FIN NORMAL</td>
                            <td>HORA FIN</td>
                            <td>CRUZA MEDIANOCHE</td>
                            <td>ESTADO</td>
                            <td>ACCIONES</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="horario of listaHorarios" :key="horario.id">
                            <td>{{ zfill(horario.id) }}</td>
                            <td>{{ horario.serv_codigo }} - {{ horario.serv_nombre }}</td>
                            <td>{{ horario.hor_hora_inicio }}</td>
                            <td>{{ horario.hor_hora_fin_normal }}</td>
                            <td>{{ horario.hor_hora_fin }}</td>
                            <td v-if="parseInt(horario.hor_cruza_medianoche) === 1">
                                <span class="badge bg-warning text-dark"><i class="fas fa-moon"></i> SI</span>
                            </td>
                            <td v-else>
                                <span class="badge bg-info"><i class="fas fa-sun"></i> NO</span>
                            </td>
                            <td v-if="parseInt(horario.hor_estado) === 1">
                                <span class="badge bg-success"><i class="fas fa-check-double"></i> Activo</span>
                            </td>
                            <td v-else>
                                <span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span>
                            </td>
                            <td>
                                <template v-if="admin">
                                    <button @click="loadHorario(horario), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalHorario">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="modalHorario" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 v-if="estadoSave"><i class="fas fa-file-alt"></i> Crear Horario</h5>
                            <h5 v-else><i class="fas fa-file-alt"></i> Actualizar Horario</h5>
                            <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal" :disabled="loadingSave">X</button>
                        </div>

                        <div class="modal-body">
                            <input type="hidden" v-model="idEdit">

                            <div class="mb-3">
                                <label class="col-form-label col-form-label-sm"><i class="fal fa-clock"></i> Servicio</label>
                                <vue-select
                                    class="border rounded"
                                    :options="listaServicios"
                                    label="serv_nombre"
                                    v-model="newHorario.fkServicio"
                                    :reduce="servicio => servicio.id"
                                    placeholder="Seleccione un servicio">
                                    <template #option="servicio">
                                        {{ servicio.serv_codigo }} - {{ servicio.serv_nombre }}
                                    </template>
                                    <template #selected-option="servicio">
                                        {{ servicio.serv_codigo }} - {{ servicio.serv_nombre }}
                                    </template>
                                </vue-select>
                                <div v-html="formValidacion.fkServicio" class="text-danger"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="horHoraInicio" class="col-form-label col-form-label-sm"><i class="fal fa-hourglass-start"></i> Hora inicio</label>
                                    <input v-model="newHorario.horHoraInicio" type="time" step="1" class="form-control" id="horHoraInicio" />
                                    <div v-html="formValidacion.horHoraInicio" class="text-danger"></div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="horHoraFinNormal" class="col-form-label col-form-label-sm"><i class="fal fa-alarm-clock"></i> Fin normal</label>
                                    <input v-model="newHorario.horHoraFinNormal" type="time" step="1" class="form-control" id="horHoraFinNormal" />
                                    <div v-html="formValidacion.horHoraFinNormal" class="text-danger"></div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="horHoraFin" class="col-form-label col-form-label-sm"><i class="fal fa-hourglass-end"></i> Hora fin</label>
                                    <input v-model="newHorario.horHoraFin" type="time" step="1" class="form-control" id="horHoraFin" />
                                    <div v-html="formValidacion.horHoraFin" class="text-danger"></div>
                                </div>
                            </div>

                            <div class="alert py-2" :class="cruzaMedianoche ? 'alert-warning' : 'alert-info'">
                                <i class="fas" :class="cruzaMedianoche ? 'fa-moon' : 'fa-sun'"></i>
                                {{ cruzaMedianoche ? 'Este horario cruza medianoche.' : 'Este horario no cruza medianoche.' }}
                                <span v-if="tieneRangoRetraso"> El consumo posterior al fin normal se marcara como retraso.</span>
                            </div>

                            <div class="mb-3">
                                <label for="horEstado" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Estado</label>
                                <select v-model="newHorario.horEstado" class="form-select border" id="horEstado">
                                    <option value="1">ACTIVO</option>
                                    <option value="0">INACTIVO</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-primary" :disabled="loadingSave" @click="saveUpdateHorario()">
                                <span v-if="loadingSave"><i class="fas fa-spinner fa-spin"></i> Procesando...</span>
                                <span v-else-if="estadoSave"><i class="fas fa-save"></i> Crear</span>
                                <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                            </button>
                            <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal" :disabled="loadingSave"><i class="fas fa-stop"></i> Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';
    var listaServicios = <?= json_encode($listaServicios ?? []) ?>;

    if (window.appHorarios) {
        window.appHorarios.unmount();
    }

    window.appHorarios = Vue.createApp({
        components: {
            "vue-select": window['vue-select']
        },
        data() {
            return {
                url: siteUrl,
                admin: admin,
                estadoSave: true,
                loadingList: false,
                loadingSave: false,
                idEdit: '',
                newHorario: this.emptyHorario(),
                listaHorarios: [],
                listaServicios: listaServicios,
                formValidacion: [],
            };
        },
        computed: {
            cruzaMedianoche() {
                return this.newHorario.horHoraInicio !== '' &&
                        this.newHorario.horHoraFin !== '' &&
                        this.newHorario.horHoraInicio > this.newHorario.horHoraFin;
            },
            tieneRangoRetraso() {
                return this.newHorario.horHoraFinNormal !== '' &&
                        this.newHorario.horHoraFin !== '' &&
                        this.newHorario.horHoraFinNormal !== this.newHorario.horHoraFin;
            },
        },
        created() {
            this.getHorarios();
        },
        methods: {
            emptyHorario() {
                return {
                    fkServicio: '',
                    horHoraInicio: '',
                    horHoraFinNormal: '',
                    horHoraFin: '',
                    horEstado: '1',
                };
            },
            async getHorarios() {
                this.loadingList = true;
                try {
                    let response = await axios.get(this.url + '/biocomedor/horarios/getHorarios');
                    if ($.fn.DataTable.isDataTable('#tblHorarios')) {
                        $('#tblHorarios').DataTable().destroy();
                    }

                    this.listaHorarios = response.data ? response.data : [];

                    this.$nextTick(() => {
                        if (this.admin) {
                            dataTableModalBtn('#tblHorarios', 'Lista de horarios', '#modalHorario', 'CREAR HORARIO');
                        } else {
                            dataTable('#tblHorarios', 'Lista de horarios');
                        }
                    });
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    this.loadingList = false;
                }
            },
            loadHorario(horario) {
                this.newHorario = {
                    fkServicio: horario.fk_servicio ? horario.fk_servicio : '',
                    horHoraInicio: this.formatHora(horario.hor_hora_inicio),
                    horHoraFinNormal: this.formatHora(horario.hor_hora_fin_normal),
                    horHoraFin: this.formatHora(horario.hor_hora_fin),
                    horEstado: horario.hor_estado,
                };
                this.idEdit = horario.id;
                this.formValidacion = [];
            },
            async saveUpdateHorario() {
                if (this.loadingSave) {
                    return;
                }

                this.loadingSave = true;
                let datos = this.formData(this.newHorario);
                let url = this.url + '/biocomedor/horarios/saveHorario';

                if (this.idEdit !== '') {
                    datos.append('idHorario', this.idEdit);
                    url = this.url + '/biocomedor/horarios/updateHorario';
                }

                try {
                    let response = await axios.post(url, datos);

                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getHorarios();
                        $('#modalHorario').modal('hide');
                        $('.modal-backdrop').remove();
                    } else if (response.data.status === 'vacio') {
                        this.formValidacion = response.data.msg;
                    } else if (response.data.status === 'error') {
                        sweet_msg_dialog('error', response.data.msg);
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    this.loadingSave = false;
                }
            },
            clear() {
                this.newHorario = this.emptyHorario();
                this.estadoSave = true;
                this.idEdit = '';
                this.formValidacion = [];
            },
            formatHora(hora) {
                return hora ? hora.substring(0, 8) : '';
            },
            formData(obj) {
                var formData = new FormData();
                for (var key in obj) {
                    formData.append(key, obj[key]);
                }
                return formData;
            },
            zfill(num) {
                return zFill(num, 3);
            },
        },
    });

    window.appHorarios.mount('#app');
</script>
