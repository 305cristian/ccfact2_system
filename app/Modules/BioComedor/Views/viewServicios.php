<!DOCTYPE html>
<!--
/**
 * Description of viewServicios
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 24 jul 2026
 * @time 1:00:47 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-clock"></i> Servicios</h5>
        </div>

        <div class="card-body">
            <div v-if="loadingList" class="alert alert-info py-2">
                <i class="fas fa-spinner fa-spin me-1"></i> Cargando servicios...
            </div>

            <div style="overflow-x: auto">
                <table id="tblServicios" class="table table-striped nowrap display" style="width: 100%">
                    <thead class="bg-system text-white">
                        <tr>
                            <td>ID</td>
                            <td>CODIGO</td>
                            <td>NOMBRE</td>
                            <td>ORDEN</td>
                            <td>DESCRIPCION</td>
                            <td>ESTADO</td>
                            <td>ACCIONES</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="servicio of listaServicios" :key="servicio.id">
                            <td>{{ zfill(servicio.id) }}</td>
                            <td>{{ servicio.serv_codigo }}</td>
                            <td>{{ servicio.serv_nombre }}</td>
                            <td>{{ servicio.serv_orden }}</td>
                            <td>{{ servicio.serv_descripcion ? servicio.serv_descripcion : '-' }}</td>
                            <td v-if="parseInt(servicio.serv_estado) === 1">
                                <span class="badge bg-success"><i class="fas fa-check-double"></i> Activo</span>
                            </td>
                            <td v-else>
                                <span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span>
                            </td>
                            <td>
                                <template v-if="admin">
                                    <button @click="loadServicio(servicio), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalServicio">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="modalServicio" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 v-if="estadoSave"><i class="fas fa-file-alt"></i> Crear Servicio</h5>
                            <h5 v-else><i class="fas fa-file-alt"></i> Actualizar Servicio</h5>
                            <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal" :disabled="loadingSave">X</button>
                        </div>

                        <div class="modal-body">
                            <input type="hidden" v-model="idEdit">

                            <div class="mb-3">
                                <label for="servCodigo" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Codigo</label>
                                <input v-model.trim="newServicio.servCodigo" type="text" class="form-control" id="servCodigo" placeholder="Ingrese un codigo" />
                                <div v-html="formValidacion.servCodigo" class="text-danger"></div>
                            </div>

                            <div class="mb-3">
                                <label for="servNombre" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Nombre</label>
                                <input v-model.trim="newServicio.servNombre" type="text" class="form-control" id="servNombre" placeholder="Ej. DESAYUNO" />
                                <div v-html="formValidacion.servNombre" class="text-danger"></div>
                            </div>

                            <div class="mb-3">
                                <label for="servOrden" class="col-form-label col-form-label-sm"><i class="fal fa-sort-numeric-down"></i> Orden</label>
                                <input v-model.trim="newServicio.servOrden" v-numbers-only="{ decimal: false }" type="text" inputmode="numeric" class="form-control" id="servOrden" placeholder="Orden de visualizacion" />
                                <div v-html="formValidacion.servOrden" class="text-danger"></div>
                            </div>

                            <div class="mb-3">
                                <label for="servDescripcion" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Descripcion</label>
                                <textarea v-model.trim="newServicio.servDescripcion" class="form-control" id="servDescripcion" placeholder="Ingrese un detalle"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="servEstado" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Estado</label>
                                <select v-model="newServicio.servEstado" class="form-select border" id="servEstado">
                                    <option value="1">ACTIVO</option>
                                    <option value="0">INACTIVO</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-primary" :disabled="loadingSave" @click="saveUpdateServicio()">
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

    if (window.appServicios) {
        window.appServicios.unmount();
    }

    window.appServicios = Vue.createApp({
        data() {
            return {
                url: siteUrl,
                admin: admin,
                estadoSave: true,
                loadingList: false,
                loadingSave: false,
                idEdit: '',
                newServicio: this.emptyServicio(),
                listaServicios: [],
                formValidacion: [],
            };
        },
        created() {
            this.getServicios();
        },
        methods: {
            emptyServicio() {
                return {
                    servCodigo: '',
                    servNombre: '',
                    servOrden: '1',
                    servDescripcion: '',
                    servEstado: '1',
                };
            },
            async getServicios() {
                this.loadingList = true;
                try {
                    let response = await axios.get(this.url + '/biocomedor/servicios/getServicios');
                    if ($.fn.DataTable.isDataTable('#tblServicios')) {
                        $('#tblServicios').DataTable().destroy();
                    }

                    this.listaServicios = response.data ? response.data : [];

                    this.$nextTick(() => {
                        if (this.admin) {
                            dataTableModalBtn('#tblServicios', 'Lista de servicios', '#modalServicio', 'CREAR SERVICIO');
                        } else {
                            dataTable('#tblServicios', 'Lista de servicios');
                        }
                    });
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    this.loadingList = false;
                }
            },
            loadServicio(servicio) {
                this.newServicio = {
                    servCodigo: servicio.serv_codigo,
                    servNombre: servicio.serv_nombre,
                    servOrden: servicio.serv_orden,
                    servDescripcion: servicio.serv_descripcion,
                    servEstado: servicio.serv_estado,
                };
                this.idEdit = servicio.id;
                this.formValidacion = [];
            },
            async saveUpdateServicio() {
                if (this.loadingSave) {
                    return;
                }

                this.loadingSave = true;
                let datos = this.formData(this.newServicio);
                let url = this.url + '/biocomedor/servicios/saveServicio';

                if (this.idEdit !== '') {
                    datos.append('idServicio', this.idEdit);
                    url = this.url + '/biocomedor/servicios/updateServicio';
                }

                try {
                    let response = await axios.post(url, datos);

                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getServicios();
                        $('#modalServicio').modal('hide');
                        $('.modal-backdrop').remove();
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
                this.newServicio = this.emptyServicio();
                this.estadoSave = true;
                this.idEdit = '';
                this.formValidacion = [];
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

    window.appServicios.use(AllDirectives);
    window.appServicios.mount('#app');
</script>
