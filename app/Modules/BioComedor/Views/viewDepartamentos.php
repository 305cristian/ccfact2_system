<!DOCTYPE html>
<!--
/**
 * Description of viewDepartamentos
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 24 jul 2026
 * @time 12:36:30 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-sitemap"></i> Departamentos</h5>
        </div>

        <div class="card-body">
            <div v-if="loadingList" class="alert alert-info py-2">
                <i class="fas fa-spinner fa-spin me-1"></i> Cargando departamentos...
            </div>

            <div style="overflow-x: auto">
                <table id="tblDepartamentos" class="table table-striped nowrap display" style="width: 100%">
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
                        <tr v-for="departamento of listaDepartamentos" :key="departamento.id">
                            <td>{{ zfill(departamento.id) }}</td>
                            <td>{{ departamento.dep_codigo }}</td>
                            <td>{{ departamento.dep_nombre }}</td>
                            <td>{{ departamento.dep_descripcion ? departamento.dep_descripcion : '-' }}</td>
                            <td v-if="parseInt(departamento.dep_estado) === 1">
                                <span class="badge bg-success"><i class="fas fa-check-double"></i> Activo</span>
                            </td>
                            <td v-else>
                                <span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span>
                            </td>
                            <td>
                                <template v-if="admin">
                                    <button @click="loadDepartamento(departamento)" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="modalDepartamento" ref="modalDepartamento" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 v-if="estadoSave"><i class="fas fa-file-alt"></i> Crear Departamento</h5>
                            <h5 v-else><i class="fas fa-file-alt"></i> Actualizar Departamento</h5>
                            <button @click="cerrarModalDepartamento()" class="btn btn-danger btn-sm" :disabled="loadingSave">X</button>
                        </div>

                        <div class="modal-body">
                            <input type="hidden" v-model="idEdit">

                            <div class="mb-3">
                                <label for="depCodigo" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Codigo</label>
                                <input v-model.trim="newDepartamento.depCodigo" type="text" class="form-control" id="depCodigo" placeholder="Ingrese un codigo" />
                                <div v-html="formValidacion.depCodigo" class="text-danger"></div>
                            </div>

                            <div class="mb-3">
                                <label for="depNombre" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Nombre</label>
                                <input v-model.trim="newDepartamento.depNombre" type="text" class="form-control" id="depNombre" placeholder="Ingrese un nombre" />
                                <div v-html="formValidacion.depNombre" class="text-danger"></div>
                            </div>

                            <div class="mb-3">
                                <label for="depDescripcion" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Descripcion</label>
                                <textarea v-model.trim="newDepartamento.depDescripcion" class="form-control" id="depDescripcion" placeholder="Ingrese un detalle"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="depEstado" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Estado</label>
                                <select v-model="newDepartamento.depEstado" class="form-select border" id="depEstado">
                                    <option value="1">ACTIVO</option>
                                    <option value="0">INACTIVO</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-primary" :disabled="loadingSave" @click="saveUpdateDepartamento()">
                                <span v-if="loadingSave"><i class="fas fa-spinner fa-spin"></i> Procesando...</span>
                                <span v-else-if="estadoSave"><i class="fas fa-save"></i> Crear</span>
                                <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                            </button>
                            <button @click="cerrarModalDepartamento()" class="btn btn-danger" :disabled="loadingSave"><i class="fas fa-stop"></i> Cancelar</button>
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

    if (window.appDepartamentos) {
        window.appDepartamentos.unmount();
    }

    window.appDepartamentos = Vue.createApp({
        data() {
            return {
                url: siteUrl,
                admin: admin,
                estadoSave: true,
                loadingList: false,
                loadingSave: false,
                idEdit: '',
                newDepartamento: this.emptyDepartamento(),
                listaDepartamentos: [],
                formValidacion: [],
                modalDepartamento: null,
            };
        },
        created() {
            this.getDepartamentos();
        },
        mounted() {
            this.modalDepartamento = new bootstrap.Modal(this.$refs.modalDepartamento);
        },
        methods: {
            emptyDepartamento() {
                return {
                    depCodigo: '',
                    depNombre: '',
                    depDescripcion: '',
                    depEstado: '1',
                };
            },
            async getDepartamentos() {
                this.loadingList = true;
                try {
                    let response = await axios.get(this.url + '/biocomedor/departamentos/getDepartamentos');
                    if ($.fn.DataTable.isDataTable('#tblDepartamentos')) {
                        $('#tblDepartamentos').DataTable().destroy();
                    }

                    this.listaDepartamentos = response.data ? response.data : [];

                    this.$nextTick(() => {
                        if (this.admin) {
                            dataTableModalBtn('#tblDepartamentos', 'Lista de departamentos', '#modalDepartamento', 'CREAR DEPARTAMENTO');
                        } else {
                            dataTable('#tblDepartamentos', 'Lista de departamentos');
                        }
                    });
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    this.loadingList = false;
                }
            },
            loadDepartamento(departamento) {
                this.newDepartamento = {
                    depCodigo: departamento.dep_codigo,
                    depNombre: departamento.dep_nombre,
                    depDescripcion: departamento.dep_descripcion,
                    depEstado: departamento.dep_estado,
                };
                this.estadoSave = false;
                this.idEdit = departamento.id;
                this.formValidacion = [];
                this.modalDepartamento.show();
            },
            async saveUpdateDepartamento() {
                if (this.loadingSave) {
                    return;
                }

                this.loadingSave = true;
                let datos = this.formData(this.newDepartamento);
                let url = this.url + '/biocomedor/departamentos/saveDepartamento';

                if (this.idEdit !== '') {
                    datos.append('idDepartamento', this.idEdit);
                    url = this.url + '/biocomedor/departamentos/updateDepartamento';
                }

                try {
                    let response = await axios.post(url, datos);

                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        this.cerrarModalDepartamento();
                        this.getDepartamentos();
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
                this.newDepartamento = this.emptyDepartamento();
                this.estadoSave = true;
                this.idEdit = '';
                this.formValidacion = [];
            },
            cerrarModalDepartamento() {
                this.clear();
                this.modalDepartamento.hide();
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

    window.appDepartamentos.mount('#app');
</script>
