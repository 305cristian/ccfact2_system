<!DOCTYPE html>
<!--
/**
 * Description of viewMarcaciones
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 24 jul 2026
 * @time 1:37:15 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-clipboard-list"></i> Marcaciones</h5>
        </div>

        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <button
                    type="button"
                    class="btn btn-sm"
                    :class="panelMarcacionActivo === 'manual' ? 'btn-primary' : 'btn-outline-primary'"
                    @click="seleccionarPanelMarcacion('manual')">
                    <i class="fas fa-keyboard me-1"></i> Registro manual
                </button>
                <button
                    type="button"
                    class="btn btn-sm"
                    :class="panelMarcacionActivo === 'excel' ? 'btn-success' : 'btn-outline-success'"
                    @click="seleccionarPanelMarcacion('excel')">
                    <i class="fas fa-file-excel me-1"></i> Carga masiva Excel
                </button>
            </div>

            <div v-if="panelMarcacionActivo === 'manual'" class="border rounded p-3 mb-3">
                <h6 class="text-system fw-bold mb-3"><i class="fas fa-keyboard"></i> Registro manual</h6>

                <div class="row align-items-end">
                    <div class="col-md-3 mb-3">
                        <label class="col-form-label col-form-label-sm"><i class="fal fa-building"></i> Comedor</label>
                        <vue-select
                            class="border rounded"
                            :options="listaComedores"
                            label="com_nombre"
                            v-model="newMarcacion.fkComedor"
                            :reduce="comedor => comedor.id"
                            placeholder="Seleccione comedor"
                            @option:selected="onComedorChange"
                            @option:deselected="onComedorChange">
                            <template #option="comedor">
                                {{ comedor.com_codigo }} - {{ comedor.com_nombre }}
                            </template>
                            <template #selected-option="comedor">
                                {{ comedor.com_codigo }} - {{ comedor.com_nombre }}
                            </template>
                        </vue-select>
                        <div v-html="formValidacion.fkComedor" class="text-danger"></div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="col-form-label col-form-label-sm"><i class="fal fa-fingerprint"></i> Equipo</label>
                        <vue-select
                            class="border rounded"
                            :options="equiposFiltrados"
                            label="eq_nombre"
                            v-model="newMarcacion.fkEquipo"
                            :reduce="equipo => equipo.id"
                            placeholder="Seleccione equipo">
                            <template #option="equipo">
                                {{ equipo.eq_codigo }} - {{ equipo.eq_nombre }}
                            </template>
                            <template #selected-option="equipo">
                                {{ equipo.eq_codigo }} - {{ equipo.eq_nombre }}
                            </template>
                        </vue-select>
                        <div v-html="formValidacion.fkEquipo" class="text-danger"></div>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="tipoIdentificacion" class="col-form-label col-form-label-sm"><i class="fal fa-id-card"></i> Tipo</label>
                        <select v-model="newMarcacion.tipoIdentificacion" class="form-select border" id="tipoIdentificacion">
                            <option value="CODIGO">CODIGO</option>
                            <option value="RFID">UID RFID</option>
                            <!--<option value="BIOMETRICO">BIOMETRICO</option>-->
                        </select>
                        <div v-html="formValidacion.tipoIdentificacion" class="text-danger"></div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="identificador" class="col-form-label col-form-label-sm"><i class="fal fa-barcode"></i> Identificador</label>
                        <input v-model.trim="newMarcacion.identificador" @keyup.enter="registrarMarcacionManual()" type="text" class="form-control" id="identificador" placeholder="Codigo, UID RFID o identificador biometrico" />
                        <div v-html="formValidacion.identificador" class="text-danger"></div>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="marcFecha" class="col-form-label col-form-label-sm"><i class="fal fa-calendar"></i> Fecha</label>
                        <input v-model="newMarcacion.marcFecha" type="date" class="form-control" id="marcFecha" />
                        <div v-html="formValidacion.marcFecha" class="text-danger"></div>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="marcHora" class="col-form-label col-form-label-sm"><i class="fal fa-clock"></i> Hora</label>
                        <input v-model="newMarcacion.marcHora" type="time" step="1" class="form-control" id="marcHora" />
                        <div v-html="formValidacion.marcHora" class="text-danger"></div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <button class="btn btn-primary w-100" :disabled="loadingSave" @click="registrarMarcacionManual()">
                            <span v-if="loadingSave"><i class="fas fa-spinner fa-spin"></i> Registrando...</span>
                            <span v-else><i class="fas fa-check"></i> Registrar Marcacion</span>
                        </button>
                    </div>

                    <div class="col-md-2 mb-3">
                        <button class="btn btn-outline-secondary w-100" :disabled="loadingSave" @click="setFechaHoraActual()">
                            <i class="fas fa-sync"></i> Ahora
                        </button>
                    </div>
                </div>

                <div v-if="resultadoMarcacion" class="alert mb-0" :class="resultadoMarcacion.status === 'success' ? 'alert-success' : 'alert-warning'">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <strong><i class="fas" :class="resultadoMarcacion.status === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'"></i> {{ resultadoMarcacion.msg }}</strong>
                        <span v-if="resultadoMarcacion.data">Comensal: <b>{{ resultadoMarcacion.data.comensal }}</b></span>
                        <span v-if="resultadoMarcacion.data">Servicio: <b>{{ resultadoMarcacion.data.servicio }}</b></span>
                        <span v-if="resultadoMarcacion.data && parseInt(resultadoMarcacion.data.retraso) === 1" class="badge bg-danger">CON RETRASO</span>
                        <span v-else-if="resultadoMarcacion.data" class="badge bg-success">NORMAL</span>
                    </div>
                </div>
            </div>

            <div v-if="panelMarcacionActivo === 'excel'" class="border rounded p-3 mb-3">
                <h6 class="text-system fw-bold mb-3"><i class="fas fa-file-excel"></i> Carga masiva por Excel</h6>
                <div class="row align-items-end">
                    <div class="col-md-7 mb-3">
                        <label class="col-form-label col-form-label-sm"><i class="fal fa-file-excel"></i> Plantilla de marcaciones</label>
                        <input
                            ref="fileImportMarcaciones"
                            type="file"
                            class="form-control"
                            accept=".xlsx,.xls"
                            :disabled="loadingImportPreview || loadingImportSave"
                            @change="onFileImportMarcacionesChange">
                        <small class="text-muted">
                            Columnas esperadas: cedula/dni, comensal, fecha, hora, comedor, codigo de equipo y servicio.
                        </small>
                    </div>

                    <div class="col-md-3 mb-3">
                        <button class="btn btn-success w-100" :disabled="loadingImportPreview || !archivoMarcaciones" @click="previewImportMarcaciones()">
                            <span v-if="loadingImportPreview"><i class="fas fa-spinner fa-spin"></i> Validando...</span>
                            <span v-else><i class="fas fa-search"></i> Validar plantilla</span>
                        </button>
                    </div>

                    <div class="col-md-2 mb-3">
                        <button class="btn btn-outline-secondary w-100" :disabled="loadingImportPreview || loadingImportSave" @click="limpiarArchivoImportMarcaciones()">
                            <i class="fas fa-eraser"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>

            <div class="border rounded p-3 mb-3">
                <h6 class="text-system fw-bold mb-3"><i class="fas fa-filter"></i> Filtros</h6>
                <div class="row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label class="col-form-label col-form-label-sm">Fecha</label>
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system"><i class="fas fa-calendar me-2"></i> Rango de fechas</span>
                            <input
                                ref="dateRangeMarcaciones"
                                v-model="filtros.fechas"
                                type="text"
                                class="form-control"
                                placeholder="Seleccione rango de fechas">
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="col-form-label col-form-label-sm">Comedor</label>
                        <vue-select
                            class="border rounded"
                            :options="listaComedores"
                            label="com_nombre"
                            v-model="filtros.fkComedor"
                            :reduce="comedor => comedor.id"
                            placeholder="Todos los comedores">
                        </vue-select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="col-form-label col-form-label-sm">Servicio</label>
                        <vue-select
                            class="border rounded"
                            :options="listaServicios"
                            label="serv_nombre"
                            v-model="filtros.fkServicio"
                            :reduce="servicio => servicio.id"
                            placeholder="Todos los servicios">
                        </vue-select>
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

                    <div class="col-md-2 mb-3">
                        <label class="col-form-label col-form-label-sm">Retraso</label>
                        <select v-model="filtros.marcRetraso" class="form-select border">
                            <option value="">TODOS</option>
                            <option value="0">NORMAL</option>
                            <option value="1">CON RETRASO</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="col-form-label col-form-label-sm">Buscar</label>
                        <input v-model.trim="filtros.texto" @keyup.enter="getMarcaciones()" type="text" class="form-control" placeholder="Comensal, cedula, codigo o identificador" />
                    </div>

                    <div class="col-md-3 mb-3">
                        <button class="btn btn-primary w-100" :disabled="loadingList" @click="getMarcaciones()">
                            <span v-if="loadingList"><i class="fas fa-spinner fa-spin"></i> Consultando...</span>
                            <span v-else><i class="fas fa-search"></i> Buscar</span>
                        </button>
                    </div>

                    <div class="col-md-3 mb-3">
                        <button class="btn btn-outline-secondary w-100" :disabled="loadingList" @click="limpiarFiltros()">
                            <i class="fas fa-eraser"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="loadingList" class="alert alert-info py-2">
                <i class="fas fa-spinner fa-spin me-1"></i> Cargando marcaciones...
            </div>

            <div style="overflow-x: auto">
                <table id="tblMarcaciones" class="table table-striped nowrap display" style="width: 100%">
                    <thead class="bg-system text-white">
                        <tr>
                            <td>ID</td>
                            <td>FECHA/HORA</td>
                            <td>COMENSAL</td>
                            <td>COMEDOR</td>
                            <td>EQUIPO</td>
                            <td>SERVICIO</td>
                            <td>CONTRATISTA</td>
                            <td>PROYECTO</td>
                            <td>ESTADO</td>
                            <td>RETRASO</td>
                            <td>ORIGEN</td>
                            <td>OBSERVACION</td>
                            <td>ACCIONES</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="marcacion of listaMarcaciones" :key="marcacion.id">
                            <td>{{ zfill(marcacion.id) }}</td>
                            <td>{{ marcacion.marc_fecha_hora }}</td>
                            <td>{{ marcacion.comens_codigo }} - {{ marcacion.comens_nombres }} {{ marcacion.comens_apellidos }}</td>
                            <td>{{ marcacion.com_nombre }}</td>
                            <td>{{ marcacion.eq_nombre }}</td>
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
                            <td>{{ marcacion.marc_observacion ? marcacion.marc_observacion : '-' }}</td>
                            <td>
                                <template v-if="admin && marcacion.marc_estado !== 'ANULADA'">
                                    <button @click="loadMarcacionEdit(marcacion)" class="btn btn-warning btn-sm me-1">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="loadMarcacionAnular(marcacion)" class="btn btn-danger btn-sm">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </template>
                                <span v-else>-</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <?php echo view('\Modules\BioComedor\Views\viewMarcacionesModalValidar') ?>

            <div id="modalEditarMarcacion" ref="modalEditarMarcacion" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5><i class="fas fa-edit"></i> Corregir Marcacion</h5>
                            <button @click="cerrarModalEditar()" class="btn btn-danger btn-sm" :disabled="loadingEdit">X</button>
                        </div>

                        <div class="modal-body">
                            <div v-if="marcacionEditResumen" class="alert alert-info py-2">
                                <b>{{ marcacionEditResumen.comensal }}</b> / {{ marcacionEditResumen.fechaHora }}
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label col-form-label-sm"><i class="fal fa-building"></i> Comedor</label>
                                    <vue-select
                                        class="border rounded"
                                        :options="listaComedores"
                                        label="com_nombre"
                                        v-model="editMarcacion.fkComedor"
                                        :reduce="comedor => comedor.id"
                                        placeholder="Seleccione comedor"
                                        @option:selected="onComedorEditChange"
                                        @option:deselected="onComedorEditChange">
                                        <template #option="comedor">
                                            {{ comedor.com_codigo }} - {{ comedor.com_nombre }}
                                        </template>
                                        <template #selected-option="comedor">
                                            {{ comedor.com_codigo }} - {{ comedor.com_nombre }}
                                        </template>
                                    </vue-select>
                                    <div v-html="formValidacionEdit.fkComedor" class="text-danger"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label col-form-label-sm"><i class="fal fa-fingerprint"></i> Equipo</label>
                                    <vue-select
                                        class="border rounded"
                                        :options="equiposEditFiltrados"
                                        label="eq_nombre"
                                        v-model="editMarcacion.fkEquipo"
                                        :reduce="equipo => equipo.id"
                                        placeholder="Seleccione equipo">
                                        <template #option="equipo">
                                            {{ equipo.eq_codigo }} - {{ equipo.eq_nombre }}
                                        </template>
                                        <template #selected-option="equipo">
                                            {{ equipo.eq_codigo }} - {{ equipo.eq_nombre }}
                                        </template>
                                    </vue-select>
                                    <div v-html="formValidacionEdit.fkEquipo" class="text-danger"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label col-form-label-sm"><i class="fal fa-clock"></i> Servicio</label>
                                    <vue-select
                                        class="border rounded"
                                        :options="listaServicios"
                                        label="serv_nombre"
                                        v-model="editMarcacion.fkServicio"
                                        :reduce="servicio => servicio.id"
                                        placeholder="Seleccione servicio">
                                        <template #option="servicio">
                                            {{ servicio.serv_codigo }} - {{ servicio.serv_nombre }}
                                        </template>
                                        <template #selected-option="servicio">
                                            {{ servicio.serv_codigo }} - {{ servicio.serv_nombre }}
                                        </template>
                                    </vue-select>
                                    <div v-html="formValidacionEdit.fkServicio" class="text-danger"></div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="col-form-label col-form-label-sm"><i class="fal fa-calendar"></i> Fecha</label>
                                    <input v-model="editMarcacion.marcFecha" type="date" class="form-control" />
                                    <div v-html="formValidacionEdit.marcFecha" class="text-danger"></div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="col-form-label col-form-label-sm"><i class="fal fa-clock"></i> Hora</label>
                                    <input v-model="editMarcacion.marcHora" type="time" step="1" class="form-control" />
                                    <div v-html="formValidacionEdit.marcHora" class="text-danger"></div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="col-form-label col-form-label-sm"><i class="fal fa-comment-alt"></i> Motivo de correccion</label>
                                    <textarea v-model.trim="editMarcacion.motivoCorreccion" class="form-control" placeholder="Explique el motivo de la correccion"></textarea>
                                    <div v-html="formValidacionEdit.motivoCorreccion" class="text-danger"></div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-primary" :disabled="loadingEdit" @click="updateMarcacion()">
                                <span v-if="loadingEdit"><i class="fas fa-spinner fa-spin"></i> Corrigiendo...</span>
                                <span v-else><i class="fas fa-save"></i> Guardar Correccion</span>
                            </button>
                            <button @click="cerrarModalEditar()" class="btn btn-danger" :disabled="loadingEdit"><i class="fas fa-stop"></i> Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="modalAnularMarcacion" ref="modalAnularMarcacion" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5><i class="fas fa-ban"></i> Anular Marcacion</h5>
                            <button @click="cerrarModalAnular()" class="btn btn-danger btn-sm" :disabled="loadingAnular">X</button>
                        </div>

                        <div class="modal-body">
                            <div v-if="marcacionAnularResumen" class="alert alert-warning py-2">
                                Se anulara la marcacion de <b>{{ marcacionAnularResumen.comensal }}</b> registrada el {{ marcacionAnularResumen.fechaHora }}.
                            </div>

                            <div class="mb-3">
                                <label class="col-form-label col-form-label-sm"><i class="fal fa-comment-alt"></i> Motivo de anulacion</label>
                                <textarea v-model.trim="anularMarcacionData.motivoAnulacion" class="form-control" placeholder="Explique el motivo de la anulacion"></textarea>
                                <div v-html="formValidacionAnular.motivoAnulacion" class="text-danger"></div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-danger" :disabled="loadingAnular" @click="anularMarcacion()">
                                <span v-if="loadingAnular"><i class="fas fa-spinner fa-spin"></i> Anulando...</span>
                                <span v-else><i class="fas fa-ban"></i> Anular Marcacion</span>
                            </button>
                            <button @click="cerrarModalAnular()" class="btn btn-secondary" :disabled="loadingAnular"><i class="fas fa-stop"></i> Cancelar</button>
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
    var listaComedores = <?= json_encode($listaComedores ?? []) ?>;
    var listaEquipos = <?= json_encode($listaEquipos ?? []) ?>;
    var listaServicios = <?= json_encode($listaServicios ?? []) ?>;

    if (window.appMarcaciones) {
        window.appMarcaciones.unmount();
    }

    window.appMarcaciones = Vue.createApp({
        components: {
            "vue-select": window['vue-select']
        },
        data() {
            return {
                url: siteUrl,
                admin: admin,
                loadingList: false,
                loadingSave: false,
                loadingEdit: false,
                loadingAnular: false,
                loadingImportPreview: false,
                loadingImportSave: false,
                panelMarcacionActivo: '',
                listaComedores: listaComedores,
                listaEquipos: listaEquipos,
                listaServicios: listaServicios,
                listaMarcaciones: [],
                archivoMarcaciones: null,
                previewImportacion: this.emptyPreviewImportacion(),
                newMarcacion: this.emptyMarcacion(),
                editMarcacion: this.emptyEditMarcacion(),
                anularMarcacionData: this.emptyAnularMarcacion(),
                filtros: this.emptyFiltros(),
                formValidacion: [],
                formValidacionEdit: [],
                formValidacionAnular: [],
                resultadoMarcacion: null,
                marcacionEditResumen: null,
                marcacionAnularResumen: null,

                modalEditarMarcacion: null,
                modalAnularMarcacion: null,
                modalPreviewImportMarcaciones: null
            };
        },
        computed: {
            filasValidasImportacion() {
                return (this.previewImportacion.filas || []).filter(fila => fila.valido);
            },
            equiposFiltrados() {
                if (!this.newMarcacion.fkComedor) {
                    return [];
                }

                return this.listaEquipos.filter(equipo => parseInt(equipo.fk_comedor) === parseInt(this.newMarcacion.fkComedor));
            },
            equiposEditFiltrados() {
                if (!this.editMarcacion.fkComedor) {
                    return [];
                }

                return this.listaEquipos.filter(equipo => parseInt(equipo.fk_comedor) === parseInt(this.editMarcacion.fkComedor));
            },
        },
        created() {
            this.setFechaHoraActual();
            this.filtros.fechaDesde = this.fechaActual();
            this.filtros.fechaHasta = this.fechaActual();
            this.filtros.fechas = `${this.filtros.fechaDesde} a ${this.filtros.fechaHasta}`;
            this.getMarcaciones();
        },
        mounted() {

            this.modalEditarMarcacion = new bootstrap.Modal(this.$refs.modalEditarMarcacion);
            this.modalAnularMarcacion = new bootstrap.Modal(this.$refs.modalAnularMarcacion);
            this.modalPreviewImportMarcaciones = new bootstrap.Modal(this.$refs.modalPreviewImportMarcaciones);

            flatpickr(this.$refs.dateRangeMarcaciones, {
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
            emptyMarcacion() {
                return {
                    fkComedor: '',
                    fkEquipo: '',
                    tipoIdentificacion: 'CODIGO',
                    identificador: '',
                    marcFecha: '',
                    marcHora: '',
                };
            },
            emptyEditMarcacion() {
                return {
                    idMarcacion: '',
                    fkComedor: '',
                    fkEquipo: '',
                    fkServicio: '',
                    marcFecha: '',
                    marcHora: '',
                    motivoCorreccion: '',
                };
            },
            emptyAnularMarcacion() {
                return {
                    idMarcacion: '',
                    motivoAnulacion: '',
                };
            },
            emptyFiltros() {
                return {
                    fechas: '',
                    fechaDesde: '',
                    fechaHasta: '',
                    fkComedor: '',
                    fkServicio: '',
                    marcEstado: '',
                    marcRetraso: '',
                    texto: '',
                };
            },
            emptyPreviewImportacion() {
                return {
                    filas: [],
                    total: 0,
                    correctas: 0,
                    errores: 0,
                };
            },
            seleccionarPanelMarcacion(panel) {
                this.panelMarcacionActivo = this.panelMarcacionActivo === panel ? '' : panel;
            },
            async getMarcaciones() {
                this.loadingList = true;
                try {
                    const fechas = this.getFechasFiltro();
                    this.filtros.fechaDesde = fechas.fechaDesde;
                    this.filtros.fechaHasta = fechas.fechaHasta;
                    let datos = this.formData(this.filtros);
                    let response = await axios.post(this.url + '/biocomedor/marcaciones/getMarcaciones', datos);

                    if ($.fn.DataTable.isDataTable('#tblMarcaciones')) {
                        $('#tblMarcaciones').DataTable().destroy();
                    }

                    this.listaMarcaciones = response.data ? response.data : [];

                    this.$nextTick(() => {
                        dataTable('#tblMarcaciones', 'Lista de marcaciones');
                    });
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    this.loadingList = false;
                }
            },
            async registrarMarcacionManual() {
                if (this.loadingSave) {
                    return;
                }

                this.loadingSave = true;
                this.resultadoMarcacion = null;
                this.formValidacion = [];

                try {
                    let datos = this.formData(this.newMarcacion);
                    let response = await axios.post(this.url + '/biocomedor/marcaciones/registrarMarcacionManual', datos);

                    if (response.data.status === 'success' || response.data.status === 'warning') {
                        this.resultadoMarcacion = response.data;
                        if (response.data.data) {
                            this.newMarcacion.identificador = '';
                            this.setFechaHoraActual();
                            this.getMarcaciones();
                        }
                    } else if (response.data.status === 'vacio') {
                        this.formValidacion = response.data.msg;
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    this.loadingSave = false;
                }
            },
            onFileImportMarcacionesChange(event) {
                this.archivoMarcaciones = event.target.files && event.target.files[0] ? event.target.files[0] : null;
                this.previewImportacion = this.emptyPreviewImportacion();
            },
            limpiarArchivoImportMarcaciones() {
                this.archivoMarcaciones = null;
                this.previewImportacion = this.emptyPreviewImportacion();

                if (this.$refs.fileImportMarcaciones) {
                    this.$refs.fileImportMarcaciones.value = '';
                }
            },
            async previewImportMarcaciones() {
                if (this.loadingImportPreview) {
                    return;
                }

                if (!this.archivoMarcaciones) {
                    sweet_msg_dialog('warning', 'Debe seleccionar un archivo Excel.');
                    return;
                }

                this.loadingImportPreview = true;

                try {
                    let datos = new FormData();
                    datos.append('file', this.archivoMarcaciones);
                    let response = await axios.post(this.url + '/biocomedor/marcaciones/previewImportExcel', datos);

                    if (response.data.status === 'success') {
                        this.previewImportacion = response.data.data || this.emptyPreviewImportacion();
                        this.modalPreviewImportMarcaciones.show();
                    } else {
                        sweet_msg_dialog(response.data.status || 'warning', response.data.msg || 'No se pudo validar el archivo.');
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || 'Error al validar el archivo Excel.');
                } finally {
                    this.loadingImportPreview = false;
                }
            },
            async guardarMarcacionesImportadas() {
                if (this.loadingImportSave) {
                    return;
                }

                if (this.filasValidasImportacion.length === 0) {
                    sweet_msg_dialog('warning', 'No existen filas correctas para registrar.');
                    return;
                }

                this.loadingImportSave = true;

                try {
                    const datos = {
                        filas: this.filasValidasImportacion
                    };
                    let response = await axios.post(this.url + '/biocomedor/marcaciones/guardarMarcacionesValidas', datos);

                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        this.cerrarModalPreviewImport();
                        this.limpiarArchivoImportMarcaciones();
                        this.getMarcaciones();
                    } else {
                        sweet_msg_dialog(response.data.status || 'warning', response.data.msg || 'No se pudieron registrar las marcaciones.');
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || 'Error al guardar las marcaciones importadas.');
                } finally {
                    this.loadingImportSave = false;
                }
            },
            loadMarcacionEdit(marcacion) {

                this.modalEditarMarcacion.show();
                this.editMarcacion = {
                    idMarcacion: marcacion.id,
                    fkComedor: marcacion.fk_comedor ? marcacion.fk_comedor : '',
                    fkEquipo: marcacion.fk_equipo ? marcacion.fk_equipo : '',
                    fkServicio: marcacion.fk_servicio ? marcacion.fk_servicio : '',
                    marcFecha: marcacion.marc_fecha,
                    marcHora: this.formatHora(marcacion.marc_hora),
                    motivoCorreccion: '',
                };
                this.marcacionEditResumen = {
                    comensal: `${marcacion.comens_codigo} - ${marcacion.comens_nombres} ${marcacion.comens_apellidos}`,
                    fechaHora: marcacion.marc_fecha_hora,
                };
                this.formValidacionEdit = [];
            },
            async updateMarcacion() {
                if (this.loadingEdit) {
                    return;
                }

                this.loadingEdit = true;
                this.formValidacionEdit = [];

                try {
                    let datos = this.formData(this.editMarcacion);
                    let response = await axios.post(this.url + '/biocomedor/marcaciones/updateMarcacion', datos);

                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        this.clearEdit();
                        this.getMarcaciones();

                        this.modalEditarMarcacion.hide();
                    } else if (response.data.status === 'vacio') {
                        this.formValidacionEdit = response.data.msg;
                    } else if (response.data.status === 'warning') {
                        sweet_msg_dialog('warning', response.data.msg);
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    this.loadingEdit = false;
                }
            },
            loadMarcacionAnular(marcacion) {
                this.modalAnularMarcacion.show();
                this.anularMarcacionData = {
                    idMarcacion: marcacion.id,
                    motivoAnulacion: '',
                };
                this.marcacionAnularResumen = {
                    comensal: `${marcacion.comens_codigo} - ${marcacion.comens_nombres} ${marcacion.comens_apellidos}`,
                    fechaHora: marcacion.marc_fecha_hora,
                };
                this.formValidacionAnular = [];
            },
            async anularMarcacion() {
                if (this.loadingAnular) {
                    return;
                }

                this.loadingAnular = true;
                this.formValidacionAnular = [];

                try {
                    let datos = this.formData(this.anularMarcacionData);
                    let response = await axios.post(this.url + '/biocomedor/marcaciones/anularMarcacion', datos);

                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        this.clearAnular();
                        this.getMarcaciones();
                        this.modalAnularMarcacion.hide();
                    } else if (response.data.status === 'vacio') {
                        this.formValidacionAnular = response.data.msg;
                    } else if (response.data.status === 'warning') {
                        sweet_msg_dialog('warning', response.data.msg);
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    this.loadingAnular = false;
                }
            },
            onComedorChange() {
                if (!this.newMarcacion.fkComedor) {
                    this.newMarcacion.fkEquipo = '';
                    return;
                }

                let equipoExiste = this.equiposFiltrados.some(equipo => parseInt(equipo.id) === parseInt(this.newMarcacion.fkEquipo));
                if (!equipoExiste) {
                    this.newMarcacion.fkEquipo = '';
                }
            },
            onComedorEditChange() {
                if (!this.editMarcacion.fkComedor) {
                    this.editMarcacion.fkEquipo = '';
                    return;
                }

                let equipoExiste = this.equiposEditFiltrados.some(equipo => parseInt(equipo.id) === parseInt(this.editMarcacion.fkEquipo));
                if (!equipoExiste) {
                    this.editMarcacion.fkEquipo = '';
                }
            },
            limpiarFiltros() {
                this.filtros = this.emptyFiltros();
                this.filtros.fechaDesde = this.fechaActual();
                this.filtros.fechaHasta = this.fechaActual();
                this.filtros.fechas = `${this.filtros.fechaDesde} a ${this.filtros.fechaHasta}`;

                if (this.$refs.dateRangeMarcaciones && this.$refs.dateRangeMarcaciones._flatpickr) {
                    this.$refs.dateRangeMarcaciones._flatpickr.setDate([this.filtros.fechaDesde, this.filtros.fechaHasta], false);
                }

                this.getMarcaciones();
            },
            getFechasFiltro() {
                const fechas = String(this.filtros.fechas || '').split(' a ');

                return {
                    fechaDesde: fechas[0] || this.fechaActual(),
                    fechaHasta: fechas[1] || fechas[0] || this.fechaActual()
                };
            },
            clearEdit() {
                this.editMarcacion = this.emptyEditMarcacion();
                this.formValidacionEdit = [];
                this.marcacionEditResumen = null;
            },
            clearAnular() {
                this.anularMarcacionData = this.emptyAnularMarcacion();
                this.formValidacionAnular = [];
                this.marcacionAnularResumen = null;
            },
            cerrarModalEditar() {
                this.clearEdit();
                this.modalEditarMarcacion.hide();
            },
            cerrarModalAnular() {
                this.clearAnular();
                this.modalAnularMarcacion.hide();
            },
            cerrarModalPreviewImport() {
                this.modalPreviewImportMarcaciones.hide();
            },
            setFechaHoraActual() {
                let ahora = new Date();
                this.newMarcacion.marcFecha = this.fechaActual(ahora);
                this.newMarcacion.marcHora = this.horaActual(ahora);
            },
            fechaActual(fecha = new Date()) {
                let year = fecha.getFullYear();
                let month = String(fecha.getMonth() + 1).padStart(2, '0');
                let day = String(fecha.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            },
            horaActual(fecha = new Date()) {
                return fecha.toTimeString().slice(0, 8);
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

    window.appMarcaciones.mount('#app');
</script>
