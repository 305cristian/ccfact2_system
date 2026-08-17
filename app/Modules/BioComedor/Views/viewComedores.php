<!DOCTYPE html>
<!--
/**
 * Description of viewComedores
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 23 jul 2026
 * @time 10:45:53 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-building"></i> Comedores</h5>
        </div>

        <div class="card-body">
            <div v-if="loadingList" class="alert alert-info py-2">
                <i class="fas fa-spinner fa-spin me-1"></i> Cargando comedores...
            </div>

            <div style="overflow-x: auto">
                <table id="tblComedores" class="table table-striped nowrap display" style="width: 100%">
                    <thead class="bg-system text-white">
                        <tr>
                            <td>ID</td>
                            <td>CODIGO</td>
                            <td>NOMBRE</td>
                            <td>UBICACION</td>
                            <td>DESCRIPCION</td>
                            <td>ESTADO</td>
                            <td>ACCIONES</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="comedor of listaComedores" :key="comedor.id">
                            <td>{{ zfill(comedor.id) }}</td>
                            <td>{{ comedor.com_codigo }}</td>
                            <td>{{ comedor.com_nombre }}</td>
                            <td>{{ comedor.com_ubicacion ? comedor.com_ubicacion : '-' }}</td>
                            <td>{{ comedor.com_descripcion ? comedor.com_descripcion : '-' }}</td>
                            <td v-if="parseInt(comedor.com_estado) === 1">
                                <span class="badge bg-success"><i class="fas fa-check-double"></i> Activo</span>
                            </td>
                            <td v-else>
                                <span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span>
                            </td>
                            <td>
                                <template v-if="admin">
                                    <button @click="loadComedor(comedor)" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!--MODAL CREATE COMEDOR-->
            <div id="modalComedor" ref="modalComedor" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 v-if="estadoSave"><i class="fas fa-file-alt"></i> Crear Comedor</h5>
                            <h5 v-else><i class="fas fa-file-alt"></i> Actualizar Comedor</h5>
                            <button @click="cerrarModalComedor()" class="btn btn-danger btn-sm">X</button>
                        </div>

                        <div class="modal-body">
                            <div class="text-left">
                                <input type="hidden" v-model="idEdit">

                                <div class="mb-3">
                                    <label for="comCodigo" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Codigo</label>
                                    <input v-model="newComedor.comCodigo" type="text" class="form-control" id="comCodigo" placeholder="Ingrese un codigo" />
                                    <div v-html="formValidacion.comCodigo" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="comNombre" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Nombre</label>
                                    <input v-model="newComedor.comNombre" type="text" class="form-control" id="comNombre" placeholder="Ingrese un nombre" />
                                    <div v-html="formValidacion.comNombre" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="comUbicacion" class="col-form-label col-form-label-sm"><i class="fal fa-map-marker-alt"></i> Ubicacion</label>
                                    <input v-model="newComedor.comUbicacion" type="text" class="form-control" id="comUbicacion" placeholder="Ingrese la ubicacion" />
                                </div>

                                <div class="mb-3">
                                    <label for="comDescripcion" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Descripcion</label>
                                    <textarea v-model="newComedor.comDescripcion" class="form-control" id="comDescripcion" placeholder="Ingrese un detalle"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="comEstado" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Estado</label>
                                    <select v-model="newComedor.comEstado" class="form-select border" id="comEstado">
                                        <option value="1">ACTIVO</option>
                                        <option value="0">INACTIVO</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-primary" :disabled="loadingSave" @click="saveUpdateComedor()">
                                <span v-if="loadingSave"><i class="fas fa-spinner fa-spin"></i> Procesando...</span>
                                <span v-else-if="estadoSave"><i class="fas fa-save"></i> Crear</span>
                                <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                            </button>
                            <button @click="cerrarModalComedor()" class="btn btn-danger" :disabled="loadingSave"><i class="fas fa-stop"></i> Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--CLOSE MODAL CREATE COMEDOR-->
        </div>
    </div>
</div>

<script type="text/javascript">

<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';

    if (window.appComedores) {
        window.appComedores.unmount();
    }

    window.appComedores = Vue.createApp({
        data() {
            return {
                url: siteUrl,
                admin: admin,
                estadoSave: true,
                loadingList: false,
                loadingSave: false,
                idEdit: '',
                newComedor: {
                    comCodigo: '',
                    comNombre: '',
                    comUbicacion: '',
                    comDescripcion: '',
                    comEstado: '1',
                },
                listaComedores: [],
                formValidacion: [],
                modalComedor: null,
            };
        },
        created() {
            this.getComedores();
        },
        mounted() {
            this.modalComedor = new bootstrap.Modal(this.$refs.modalComedor);
        },
        methods: {
            async getComedores() {
                this.loadingList = true;
                try {
                    let response = await axios.get(this.url + '/biocomedor/comedores/getComedores');
                    if ($.fn.DataTable.isDataTable('#tblComedores')) {
                        $('#tblComedores').DataTable().destroy();
                    }

                    if (response.data) {
                        this.listaComedores = response.data;
                    } else {
                        this.listaComedores = [];
                    }

                    this.$nextTick(() => {
                        if (this.admin) {
                            dataTableModalBtn('#tblComedores', 'Lista de comedores', '#modalComedor', 'CREAR COMEDOR');
                        } else {
                            dataTable('#tblComedores', 'Lista de comedores');
                        }
                    });
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    this.loadingList = false;
                }
            },
            loadComedor(comedor) {
                this.newComedor = {
                    comCodigo: comedor.com_codigo,
                    comNombre: comedor.com_nombre,
                    comUbicacion: comedor.com_ubicacion,
                    comDescripcion: comedor.com_descripcion,
                    comEstado: comedor.com_estado,
                };
                this.estadoSave = false;
                this.idEdit = comedor.id;
                this.formValidacion = [];
                this.modalComedor.show();
            },
            async saveUpdateComedor() {
                if (this.loadingSave) {
                    return;
                }

                this.loadingSave = true;
                let datos = this.formData(this.newComedor);
                let url = this.url + '/biocomedor/comedores/saveComedor';

                if (this.idEdit !== '') {
                    datos.append('idComedor', this.idEdit);
                    url = this.url + '/biocomedor/comedores/updateComedor';
                }

                try {
                    let response = await axios.post(url, datos);

                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        this.cerrarModalComedor();
                        this.getComedores();
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
                this.newComedor = {
                    comCodigo: '',
                    comNombre: '',
                    comUbicacion: '',
                    comDescripcion: '',
                    comEstado: '1',
                };
                this.estadoSave = true;
                this.idEdit = '';
                this.formValidacion = [];
            },
            cerrarModalComedor() {
                this.clear();
                this.modalComedor.hide();
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

    window.appComedores.mount('#app');
</script>
