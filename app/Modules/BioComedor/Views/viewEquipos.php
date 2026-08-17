<!DOCTYPE html>
<!--
/**
 * Description of viewEquipos
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 23 jul 2026
 * @time 11:00:40 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-fingerprint"></i> Equipos Biométricos</h5>
        </div>

        <div class="card-body">
            <div v-if="loadingList" class="alert alert-info py-2">
                <i class="fas fa-spinner fa-spin me-1"></i> Cargando equipos...
            </div>

            <div style="overflow-x: auto">
                <table id="tblEquipos" class="table table-striped nowrap display" style="width: 100%">
                    <thead class="bg-system text-white">
                        <tr>
                            <td>ID</td>
                            <td>COMEDOR</td>
                            <td>CODIGO</td>
                            <td>NOMBRE</td>
                            <td>MARCA</td>
                            <td>MODELO</td>
                            <td>IP</td>
                            <td>PUERTO</td>
                            <td>UBICACION</td>
                            <td>ESTADO</td>
                            <td>ACCIONES</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="equipo of listaEquipos" :key="equipo.id">
                            <td>{{ zfill(equipo.id) }}</td>
                            <td>{{ equipo.com_codigo }} - {{ equipo.com_nombre }}</td>
                            <td>{{ equipo.eq_codigo }}</td>
                            <td>{{ equipo.eq_nombre }}</td>
                            <td>{{ equipo.eq_marca ? equipo.eq_marca : '-' }}</td>
                            <td>{{ equipo.eq_modelo ? equipo.eq_modelo : '-' }}</td>
                            <td>{{ equipo.eq_ip ? equipo.eq_ip : '-' }}</td>
                            <td>{{ equipo.eq_puerto ? equipo.eq_puerto : '-' }}</td>
                            <td>{{ equipo.eq_ubicacion ? equipo.eq_ubicacion : '-' }}</td>
                            <td v-if="parseInt(equipo.eq_estado) === 1">
                                <span class="badge bg-success"><i class="fas fa-check-double"></i> Activo</span>
                            </td>
                            <td v-else>
                                <span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span>
                            </td>
                            <td>
                                <template v-if="admin">
                                    <button @click="loadEquipo(equipo)" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!--MODAL CREATE EQUIPO-->
            <div id="modalEquipo" ref="modalEquipo" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 v-if="estadoSave"><i class="fas fa-file-alt"></i> Crear Equipo</h5>
                            <h5 v-else><i class="fas fa-file-alt"></i> Actualizar Equipo</h5>
                            <button @click="cerrarModalEquipo()" class="btn btn-danger btn-sm" :disabled="loadingSave">X</button>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <input type="hidden" v-model="idEdit">

                                <div class="col-md-12 mb-3">
                                    <label class="col-form-label col-form-label-sm"><i class="fal fa-building"></i> Comedor</label>
                                    <vue-select
                                        class="border rounded"
                                        :options="listaComedores"
                                        label="com_nombre"
                                        v-model="newEquipo.fkComedor"
                                        :reduce="comedor => comedor.id"
                                        placeholder="Seleccione un comedor">
                                        <template #option="comedor">
                                            {{ comedor.com_codigo }} - {{ comedor.com_nombre }}
                                        </template>
                                        <template #selected-option="comedor">
                                            {{ comedor.com_codigo }} - {{ comedor.com_nombre }}
                                        </template>
                                    </vue-select>
                                    <div v-html="formValidacion.fkComedor" class="text-danger"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="eqCodigo" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Código</label>
                                    <input v-model="newEquipo.eqCodigo" type="text" class="form-control" id="eqCodigo" placeholder="Ingrese un código" />
                                    <div v-html="formValidacion.eqCodigo" class="text-danger"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="eqNombre" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Nombre</label>
                                    <input v-model="newEquipo.eqNombre" type="text" class="form-control" id="eqNombre" placeholder="Ingrese un nombre" />
                                    <div v-html="formValidacion.eqNombre" class="text-danger"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="eqMarca" class="col-form-label col-form-label-sm"><i class="fal fa-tag"></i> Marca</label>
                                    <input v-model="newEquipo.eqMarca" type="text" class="form-control" id="eqMarca" placeholder="Ingrese la marca" />
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="eqModelo" class="col-form-label col-form-label-sm"><i class="fal fa-microchip"></i> Modelo</label>
                                    <input v-model="newEquipo.eqModelo" type="text" class="form-control" id="eqModelo" placeholder="Ingrese el modelo" />
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="eqIp" class="col-form-label col-form-label-sm"><i class="fal fa-network-wired"></i> IP</label>
                                    <input v-model="newEquipo.eqIp" type="text" class="form-control" id="eqIp" placeholder="Ej. 192.168.1.10" />
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="eqPuerto" class="col-form-label col-form-label-sm"><i class="fal fa-plug"></i> Puerto</label>
                                    <input v-model="newEquipo.eqPuerto" type="number" min="0" class="form-control" id="eqPuerto" placeholder="Ej. 4370" />
                                </div>

                                <div class="col-md-8 mb-3">
                                    <label for="eqUbicacion" class="col-form-label col-form-label-sm"><i class="fal fa-map-marker-alt"></i> Ubicación</label>
                                    <input v-model="newEquipo.eqUbicacion" type="text" class="form-control" id="eqUbicacion" placeholder="Ingrese la ubicación" />
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="eqEstado" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Estado</label>
                                    <select v-model="newEquipo.eqEstado" class="form-select border" id="eqEstado">
                                        <option value="1">ACTIVO</option>
                                        <option value="0">INACTIVO</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-primary" :disabled="loadingSave" @click="saveUpdateEquipo()">
                                <span v-if="loadingSave"><i class="fas fa-spinner fa-spin"></i> Procesando...</span>
                                <span v-else-if="estadoSave"><i class="fas fa-save"></i> Crear</span>
                                <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                            </button>
                            <button @click="cerrarModalEquipo()" class="btn btn-danger" :disabled="loadingSave"><i class="fas fa-stop"></i> Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--CLOSE MODAL CREATE EQUIPO-->
        </div>
    </div>
</div>

<script type="text/javascript">

<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';
    var listaComedores = <?= json_encode($listaComedores ?? []) ?>;

    if (window.appEquipos) {
        window.appEquipos.unmount();
    }

    window.appEquipos = Vue.createApp({
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
                newEquipo: {
                    fkComedor: '',
                    eqCodigo: '',
                    eqNombre: '',
                    eqMarca: '',
                    eqModelo: '',
                    eqIp: '',
                    eqPuerto: '',
                    eqUbicacion: '',
                    eqEstado: '1',
                },
                listaEquipos: [],
                listaComedores: listaComedores,
                formValidacion: [],
                modalEquipo: null,
            };
        },
        created() {
            this.getEquipos();
        },
        mounted() {
            this.modalEquipo = new bootstrap.Modal(this.$refs.modalEquipo);
        },
        methods: {
            async getEquipos() {
                this.loadingList = true;
                try {
                    let response = await axios.get(this.url + '/biocomedor/equipos/getEquipos');
                    if ($.fn.DataTable.isDataTable('#tblEquipos')) {
                        $('#tblEquipos').DataTable().destroy();
                    }

                    this.listaEquipos = response.data ? response.data : [];

                    this.$nextTick(() => {
                        if (this.admin) {
                            dataTableModalBtn('#tblEquipos', 'Lista de equipos', '#modalEquipo', 'CREAR EQUIPO');
                        } else {
                            dataTable('#tblEquipos', 'Lista de equipos');
                        }
                    });
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    this.loadingList = false;
                }
            },
            loadEquipo(equipo) {
                this.newEquipo = {
                    fkComedor: parseInt(equipo.fk_comedor),
                    eqCodigo: equipo.eq_codigo,
                    eqNombre: equipo.eq_nombre,
                    eqMarca: equipo.eq_marca,
                    eqModelo: equipo.eq_modelo,
                    eqIp: equipo.eq_ip,
                    eqPuerto: equipo.eq_puerto,
                    eqUbicacion: equipo.eq_ubicacion,
                    eqEstado: equipo.eq_estado,
                };
                this.estadoSave = false;
                this.idEdit = equipo.id;
                this.formValidacion = [];
                this.modalEquipo.show();
            },
            async saveUpdateEquipo() {
                if (this.loadingSave) {
                    return;
                }

                this.loadingSave = true;
                let datos = this.formData(this.newEquipo);
                let url = this.url + '/biocomedor/equipos/saveEquipo';

                if (this.idEdit !== '') {
                    datos.append('idEquipo', this.idEdit);
                    url = this.url + '/biocomedor/equipos/updateEquipo';
                }

                try {
                    let response = await axios.post(url, datos);

                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        this.cerrarModalEquipo();
                        this.getEquipos();
                    } else if (response.data.status === 'existe') {
                        sweet_msg_dialog('warning', response.data.msg);
                    } else if (response.data.status === 'vacio') {
                        this.formValidacion = response.data.msg;
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    this.loadingSave = false;
                }
            },
            clear() {
                this.newEquipo = {
                    fkComedor: '',
                    eqCodigo: '',
                    eqNombre: '',
                    eqMarca: '',
                    eqModelo: '',
                    eqIp: '',
                    eqPuerto: '',
                    eqUbicacion: '',
                    eqEstado: '1',
                };
                this.estadoSave = true;
                this.idEdit = '';
                this.formValidacion = [];
            },
            cerrarModalEquipo() {
                this.clear();
                this.modalEquipo.hide();
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

    window.appEquipos.mount('#app');
</script>
