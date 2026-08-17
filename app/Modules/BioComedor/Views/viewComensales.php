<!DOCTYPE html>
<!--
/**
 * Description of viewComensales
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 23 jul 2026
 * @time 11:28:27 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<style>
    .bio-comensal-foto {
        align-items: center;
        background: #edf4ef;
        border: 1px solid #d8e2dc;
        border-radius: 6px;
        display: flex;
        height: 90px;
        justify-content: center;
        overflow: hidden;
        width: 90px;
    }

    .bio-comensal-foto img {
        height: 100%;
        object-fit: cover;
        width: 100%;
    }
</style>

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-users"></i> Comensales</h5>
        </div>

        <div class="card-body">
            <div v-if="loadingList" class="alert alert-info py-2">
                <i class="fas fa-spinner fa-spin me-1"></i> Cargando comensales...
            </div>

            <div style="overflow-x: auto">
                <table id="tblComensales" class="table table-striped nowrap display" style="width: 100%">
                    <thead class="bg-system text-white">
                        <tr>
                            <td>ID</td>
                            <td>FOTO</td>
                            <td>CODIGO</td>
                            <td>CEDULA</td>
                            <td>NOMBRES</td>
                            <td>APELLIDOS</td>
                            <td>BIOMETRICO</td>
                            <td>UID RFID</td>
                            <td>DEPARTAMENTO</td>
                            <td>AREA</td>
                            <td>CONTRATISTA</td>
                            <td>PROYECTO</td>
                            <td>ESTADO</td>
                            <td>ACCIONES</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="comensal of listaComensales" :key="comensal.id">
                            <td>{{ zfill(comensal.id) }}</td>
                            <td>
                                <img v-if="comensal.comens_foto" :src="urlAssets + '/uploads/img/bio_comensales/' + comensal.comens_foto" class="rounded" style="width: 42px; height: 42px; object-fit: cover;" />
                                <span v-else class="badge bg-secondary">SIN FOTO</span>
                            </td>
                            <td>{{ comensal.comens_codigo }}</td>
                            <td>{{ comensal.comens_cedula }}</td>
                            <td>{{ comensal.comens_nombres }}</td>
                            <td>{{ comensal.comens_apellidos }}</td>
                            <td>{{ comensal.comens_identificador_biometrico ? comensal.comens_identificador_biometrico : '-' }}</td>
                            <td>{{ comensal.comens_uid_rfid ? comensal.comens_uid_rfid : '-' }}</td>
                            <td>{{ comensal.dep_nombre ? comensal.dep_nombre : '-' }}</td>
                            <td>{{ comensal.area_nombre ? comensal.area_nombre : '-' }}</td>
                            <td>{{ comensal.cont_nombre ? comensal.cont_nombre : '-' }}</td>
                            <td>{{ comensal.proy_nombre ? comensal.proy_nombre : '-' }}</td>
                            <td v-if="parseInt(comensal.comens_estado) === 1">
                                <span class="badge bg-success"><i class="fas fa-check-double"></i> Activo</span>
                            </td>
                            <td v-else>
                                <span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span>
                            </td>
                            <td>
                                <template v-if="admin">
                                    <button @click="loadComensal(comensal)" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!--MODAL CREATE COMENSAL-->
            <div id="modalComensal" ref="modalComensal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 v-if="estadoSave"><i class="fas fa-file-alt"></i> Crear Comensal</h5>
                            <h5 v-else><i class="fas fa-file-alt"></i> Actualizar Comensal</h5>
                            <button @click="cerrarModalComensal()" class="btn btn-danger btn-sm" :disabled="loadingSave">X</button>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <input type="hidden" v-model="idEdit">

                                <div class="col-md-3 mb-3">
                                    <label for="comensCodigo" class="col-form-label col-form-label-sm"><i class="fal fa-barcode"></i> Codigo</label>
                                    <input v-model.trim="newComensal.comensCodigo" type="text" class="form-control" id="comensCodigo" placeholder="Codigo interno" readonly />
                                    <div v-html="formValidacion.comensCodigo" class="text-danger"></div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="comensCedula" class="col-form-label col-form-label-sm"><i class="fal fa-id-card"></i> Cedula</label>
                                    <input v-model.trim="newComensal.comensCedula" v-numbers-only="{ decimal: false }" type="text" inputmode="numeric" class="form-control" id="comensCedula" placeholder="Cedula" />
                                    <div v-html="formValidacion.comensCedula" class="text-danger"></div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="comensNombres" class="col-form-label col-form-label-sm"><i class="fal fa-user"></i> Nombres</label>
                                    <input v-model.trim="newComensal.comensNombres" type="text" class="form-control" id="comensNombres" placeholder="Nombres" />
                                    <div v-html="formValidacion.comensNombres" class="text-danger"></div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="comensApellidos" class="col-form-label col-form-label-sm"><i class="fal fa-user"></i> Apellidos</label>
                                    <input v-model.trim="newComensal.comensApellidos" type="text" class="form-control" id="comensApellidos" placeholder="Apellidos" />
                                    <div v-html="formValidacion.comensApellidos" class="text-danger"></div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="comensIdentificadorBiometrico" class="col-form-label col-form-label-sm"><i class="fal fa-fingerprint"></i> Identificador biometrico</label>
                                    <input v-model.trim="newComensal.comensIdentificadorBiometrico" type="text" class="form-control" id="comensIdentificadorBiometrico" placeholder="Codigo del biometrico" />
                                    <div v-html="formValidacion.comensIdentificadorBiometrico" class="text-danger"></div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="comensUidRfid" class="col-form-label col-form-label-sm"><i class="fal fa-id-badge"></i> UID RFID</label>
                                    <input v-model.trim="newComensal.comensUidRfid" type="text" class="form-control" id="comensUidRfid" placeholder="Codigo tarjeta RFID" />
                                    <div v-html="formValidacion.comensUidRfid" class="text-danger"></div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="col-form-label col-form-label-sm"><i class="fal fa-sitemap"></i> Departamento</label>
                                    <vue-select
                                        class="border rounded"
                                        :options="listaDepartamentos"
                                        label="dep_nombre"
                                        v-model="newComensal.fkDepartamento"
                                        :reduce="departamento => departamento.id"
                                        placeholder="Seleccione departamento"
                                        @option:selected="onDepartamentoChange"
                                        @option:deselected="onDepartamentoChange">
                                        <template #option="departamento">
                                            {{ departamento.dep_codigo }} - {{ departamento.dep_nombre }}
                                        </template>
                                        <template #selected-option="departamento">
                                            {{ departamento.dep_codigo }} - {{ departamento.dep_nombre }}
                                        </template>
                                    </vue-select>
                                    <div v-html="formValidacion.fkDepartamento" class="text-danger"></div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="col-form-label col-form-label-sm"><i class="fal fa-layer-group"></i> Area</label>
                                    <vue-select
                                        class="border rounded"
                                        :options="areasFiltradas"
                                        label="area_nombre"
                                        v-model="newComensal.fkArea"
                                        :reduce="area => area.id"
                                        placeholder="Seleccione area">
                                        <template #option="area">
                                            {{ area.dep_nombre ? area.dep_nombre : 'SIN DEPARTAMENTO' }} / {{ area.area_codigo }} - {{ area.area_nombre }}
                                        </template>
                                        <template #selected-option="area">
                                            {{ area.dep_nombre ? area.dep_nombre : 'SIN DEPARTAMENTO' }} / {{ area.area_codigo }} - {{ area.area_nombre }}
                                        </template>
                                    </vue-select>
                                    <div v-html="formValidacion.fkArea" class="text-danger"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label col-form-label-sm"><i class="fal fa-industry-alt"></i> Contratista</label>
                                    <vue-select
                                        class="border rounded"
                                        :options="listaContratistas"
                                        label="cont_nombre"
                                        v-model="newComensal.fkContratista"
                                        :reduce="contratista => contratista.id"
                                        placeholder="Seleccione una contratista">
                                        <template #option="contratista">
                                             {{ contratista.cont_nombre }}
                                        </template>
                                        <template #selected-option="contratista">
                                             {{ contratista.cont_nombre }}
                                        </template>
                                    </vue-select>
                                    <div v-html="formValidacion.fkContratista" class="text-danger"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="col-form-label col-form-label-sm"><i class="fal fa-project-diagram"></i> Proyecto</label>
                                    <vue-select
                                        class="border rounded"
                                        :options="listaProyectos"
                                        label="proy_nombre"
                                        v-model="newComensal.fkProyecto"
                                        :reduce="proyecto => proyecto.id"
                                        placeholder="Seleccione un proyecto">
                                        <template #option="proyecto">
                                            {{ proyecto.proy_codigo }} - {{ proyecto.proy_nombre }}
                                        </template>
                                        <template #selected-option="proyecto">
                                            {{ proyecto.proy_codigo }} - {{ proyecto.proy_nombre }}
                                        </template>
                                    </vue-select>
                                    <div v-html="formValidacion.fkProyecto" class="text-danger"></div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="comensEstado" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Estado</label>
                                    <select v-model="newComensal.comensEstado" class="form-select border" id="comensEstado">
                                        <option value="1">ACTIVO</option>
                                        <option value="0">INACTIVO</option>
                                    </select>
                                </div>

                                <div class="col-md-8 mb-3">
                                    <label for="comensFoto" class="col-form-label col-form-label-sm"><i class="fal fa-image"></i> Foto</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bio-comensal-foto">
                                            <img v-if="comensFotoPreview" :src="comensFotoPreview" />
                                            <i v-else class="fas fa-user text-muted fa-2x"></i>
                                        </div>

                                        <div class="flex-grow-1">
                                            <input ref="comensFotoInput" @change="onFotoComensalChange" type="file" class="form-control" id="comensFoto" accept="image/jpeg,image/png,image/webp" />
                                            <small class="text-muted">La imagen se guardara con el numero de cedula del comensal.</small>
                                            <div v-html="formValidacion.comensFoto" class="text-danger"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-primary" :disabled="loadingSave" @click="saveUpdateComensal()">
                                <span v-if="loadingSave"><i class="fas fa-spinner fa-spin"></i> Procesando...</span>
                                <span v-else-if="estadoSave"><i class="fas fa-save"></i> Crear</span>
                                <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                            </button>
                            <button @click="cerrarModalComensal()" class="btn btn-danger" :disabled="loadingSave"><i class="fas fa-stop"></i> Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--CLOSE MODAL CREATE COMENSAL-->
        </div>
    </div>
</div>

<script type="text/javascript">

<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';
    var listaContratistas = <?= json_encode($listaContratistas ?? []) ?>;
    var listaProyectos = <?= json_encode($listaProyectos ?? []) ?>;
    var listaDepartamentos = <?= json_encode($listaDepartamentos ?? []) ?>;
    var listaAreas = <?= json_encode($listaAreas ?? []) ?>;
    var codigoComensal = <?= json_encode($codigoComensal ?? '') ?>;

    if (window.appComensales) {
        window.appComensales.unmount();
    }

    window.appComensales = Vue.createApp({
        components: {
            "vue-select": window['vue-select']
        },
        data() {
            return {
                url: siteUrl,
                urlAssets: baseUrl,
                admin: admin,
                estadoSave: true,
                loadingList: false,
                loadingSave: false,
                idEdit: '',
                newComensal: this.emptyComensal(),
                comensFotoFile: null,
                comensFotoPreview: '',
                listaComensales: [],
                listaContratistas: listaContratistas,
                listaProyectos: listaProyectos,
                listaDepartamentos: listaDepartamentos,
                listaAreas: listaAreas,
                formValidacion: [],
                modalComensal: null,
            };
        },
        created() {
            this.getComensales();
        },
        mounted() {
            this.modalComensal = new bootstrap.Modal(this.$refs.modalComensal);
        },
        computed: {
            areasFiltradas() {
                if (!this.newComensal.fkDepartamento) {
                    return [];
                }

                return this.listaAreas.filter(area => parseInt(area.fk_departamento) === parseInt(this.newComensal.fkDepartamento));
            },
        },
        methods: {
            emptyComensal() {
                return {
                    comensCodigo: codigoComensal,
                    comensCedula: '',
                    comensNombres: '',
                    comensApellidos: '',
                    comensIdentificadorBiometrico: '',
                    comensUidRfid: '',
                    fkDepartamento: '',
                    fkArea: '',
                    fkContratista: '',
                    fkProyecto: '',
                    comensEstado: '1',
                };
            },
            async getComensales() {
                this.loadingList = true;
                try {
                    let response = await axios.get(this.url + '/biocomedor/comensales/getComensales');
                    if ($.fn.DataTable.isDataTable('#tblComensales')) {
                        $('#tblComensales').DataTable().destroy();
                    }

                    this.listaComensales = response.data ? response.data : [];

                    this.$nextTick(() => {
                        if (this.admin) {
                            dataTableModalBtn('#tblComensales', 'Lista de comensales', '#modalComensal', 'CREAR COMENSAL');
                        } else {
                            dataTable('#tblComensales', 'Lista de comensales');
                        }
                    });
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    this.loadingList = false;
                }
            },
            loadComensal(comensal) {
                this.newComensal = {
                    comensCodigo: comensal.comens_codigo,
                    comensCedula: comensal.comens_cedula,
                    comensNombres: comensal.comens_nombres,
                    comensApellidos: comensal.comens_apellidos,
                    comensIdentificadorBiometrico: comensal.comens_identificador_biometrico ? comensal.comens_identificador_biometrico : '',
                    comensUidRfid: comensal.comens_uid_rfid ? comensal.comens_uid_rfid : '',
                    fkDepartamento: comensal.fk_departamento ? comensal.fk_departamento : '',
                    fkArea: comensal.fk_area ? comensal.fk_area : '',
                    fkContratista: comensal.fk_contratista ? comensal.fk_contratista : '',
                    fkProyecto: comensal.fk_proyecto ? comensal.fk_proyecto : '',
                    comensEstado: comensal.comens_estado,
                };
                this.comensFotoFile = null;
                this.comensFotoPreview = comensal.comens_foto ? this.urlAssets + '/uploads/img/bio_comensales/' + comensal.comens_foto : '';
                this.estadoSave = false;
                this.idEdit = comensal.id;
                this.formValidacion = [];
                this.modalComensal.show();
            },
            onDepartamentoChange() {
                if (!this.newComensal.fkDepartamento) {
                    this.newComensal.fkArea = '';
                    return;
                }

                let areaExiste = this.areasFiltradas.some(area => parseInt(area.id) === parseInt(this.newComensal.fkArea));
                if (!areaExiste) {
                    this.newComensal.fkArea = '';
                }
            },
            async saveUpdateComensal() {
                if (this.loadingSave) {
                    return;
                }

                this.loadingSave = true;
                let datos = this.formData(this.newComensal);
                let url = this.url + '/biocomedor/comensales/saveComensal';

                if (this.comensFotoFile) {
                    datos.append('comensFoto', this.comensFotoFile);
                }

                if (this.idEdit !== '') {
                    datos.append('idComensal', this.idEdit);
                    url = this.url + '/biocomedor/comensales/updateComensal';
                }

                try {
                    let response = await axios.post(url, datos);

                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getCodigoComensal();
                        this.getComensales();
                        this.modalComensal.hide();
                    } else if (response.data.status === 'existe') {
                        sweet_msg_dialog('warning', response.data.msg);
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
                this.newComensal = this.emptyComensal();
                this.comensFotoFile = null;
                this.comensFotoPreview = '';
                this.estadoSave = true;
                this.idEdit = '';
                this.formValidacion = [];
                if (this.$refs.comensFotoInput) {
                    this.$refs.comensFotoInput.value = '';
                }
            },
            cerrarModalComensal() {
                this.clear();
                this.modalComensal.hide();
            },
            onFotoComensalChange(event) {
                let file = event.target.files[0] || null;
                this.comensFotoFile = file;
                this.comensFotoPreview = file ? URL.createObjectURL(file) : '';
            },
            async getCodigoComensal() {
                try {
                    let response = await axios.get(this.url + '/biocomedor/comensales/getCodigoComensal');
                    this.newComensal.comensCodigo = response.data.codigo || '';
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                }
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

    window.appComensales.use(AllDirectives);
    window.appComensales.mount('#app');
</script>
