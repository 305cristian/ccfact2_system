<!DOCTYPE html>
<!--
/**
 * Description of viewProyectos
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 24 jul 2026
 * @time 12:36:39 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-project-diagram"></i> Proyectos</h5>
        </div>

        <div class="card-body">
            <div v-if="loadingList" class="alert alert-info py-2">
                <i class="fas fa-spinner fa-spin me-1"></i> Cargando proyectos...
            </div>

            <div style="overflow-x: auto">
                <table id="tblProyectos" class="table table-striped nowrap display" style="width: 100%">
                    <thead class="bg-system text-white">
                        <tr>
                            <td>ID</td>
                            <td>CODIGO</td>
                            <td>NOMBRE</td>
                            <td>DESCRIPCION</td>
                            <td>ESTADO</td>
                            <td>ACCIONES</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="proyecto of listaProyectos" :key="proyecto.id">
                            <td>{{ zfill(proyecto.id) }}</td>
                            <td>{{ proyecto.proy_codigo }}</td>
                            <td>{{ proyecto.proy_nombre }}</td>
                            <td>{{ proyecto.proy_descripcion ? proyecto.proy_descripcion : '-' }}</td>
                            <td v-if="parseInt(proyecto.proy_estado) === 1">
                                <span class="badge bg-success"><i class="fas fa-check-double"></i> Activo</span>
                            </td>
                            <td v-else>
                                <span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span>
                            </td>
                            <td>
                                <template v-if="admin">
                                    <button @click="loadProyecto(proyecto), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalProyecto">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="modalProyecto" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 v-if="estadoSave"><i class="fas fa-file-alt"></i> Crear Proyecto</h5>
                            <h5 v-else><i class="fas fa-file-alt"></i> Actualizar Proyecto</h5>
                            <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal" :disabled="loadingSave">X</button>
                        </div>

                        <div class="modal-body">
                            <input type="hidden" v-model="idEdit">

                            <div class="mb-3">
                                <label for="proyCodigo" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Codigo</label>
                                <input v-model.trim="newProyecto.proyCodigo" type="text" class="form-control" id="proyCodigo" placeholder="Ingrese un codigo" />
                                <div v-html="formValidacion.proyCodigo" class="text-danger"></div>
                            </div>

                            <div class="mb-3">
                                <label for="proyNombre" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Nombre</label>
                                <input v-model.trim="newProyecto.proyNombre" type="text" class="form-control" id="proyNombre" placeholder="Ingrese un nombre" />
                                <div v-html="formValidacion.proyNombre" class="text-danger"></div>
                            </div>

                            <div class="mb-3">
                                <label for="proyDescripcion" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Descripcion</label>
                                <textarea v-model.trim="newProyecto.proyDescripcion" class="form-control" id="proyDescripcion" placeholder="Ingrese un detalle"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="proyEstado" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Estado</label>
                                <select v-model="newProyecto.proyEstado" class="form-select border" id="proyEstado">
                                    <option value="1">ACTIVO</option>
                                    <option value="0">INACTIVO</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-primary" :disabled="loadingSave" @click="saveUpdateProyecto()">
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

    if (window.appProyectos) {
        window.appProyectos.unmount();
    }

    window.appProyectos = Vue.createApp({
        data() {
            return {
                url: siteUrl,
                admin: admin,
                estadoSave: true,
                loadingList: false,
                loadingSave: false,
                idEdit: '',
                newProyecto: this.emptyProyecto(),
                listaProyectos: [],
                formValidacion: [],
            };
        },
        created() {
            this.getProyectos();
        },
        methods: {
            emptyProyecto() {
                return {
                    proyCodigo: '',
                    proyNombre: '',
                    proyDescripcion: '',
                    proyEstado: '1',
                };
            },
            async getProyectos() {
                this.loadingList = true;
                try {
                    let response = await axios.get(this.url + '/biocomedor/proyectos/getProyectos');
                    if ($.fn.DataTable.isDataTable('#tblProyectos')) {
                        $('#tblProyectos').DataTable().destroy();
                    }

                    this.listaProyectos = response.data ? response.data : [];

                    this.$nextTick(() => {
                        if (this.admin) {
                            dataTableModalBtn('#tblProyectos', 'Lista de proyectos', '#modalProyecto', 'CREAR PROYECTO');
                        } else {
                            dataTable('#tblProyectos', 'Lista de proyectos');
                        }
                    });
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    this.loadingList = false;
                }
            },
            loadProyecto(proyecto) {
                this.newProyecto = {
                    proyCodigo: proyecto.proy_codigo,
                    proyNombre: proyecto.proy_nombre,
                    proyDescripcion: proyecto.proy_descripcion,
                    proyEstado: proyecto.proy_estado,
                };
                this.idEdit = proyecto.id;
                this.formValidacion = [];
            },
            async saveUpdateProyecto() {
                if (this.loadingSave) {
                    return;
                }

                this.loadingSave = true;
                let datos = this.formData(this.newProyecto);
                let url = this.url + '/biocomedor/proyectos/saveProyecto';

                if (this.idEdit !== '') {
                    datos.append('idProyecto', this.idEdit);
                    url = this.url + '/biocomedor/proyectos/updateProyecto';
                }

                try {
                    let response = await axios.post(url, datos);

                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getProyectos();
                        $('#modalProyecto').modal('hide');
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
                this.newProyecto = this.emptyProyecto();
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

    window.appProyectos.mount('#app');
</script>
