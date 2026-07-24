<!DOCTYPE html>
<!--
/**
 * Description of viewContratistas
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 23 jul 2026
 * @time 11:14:43 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-industry-alt"></i> Contratistas</h5>
        </div>

        <div class="card-body">
            <div v-if="loadingList" class="alert alert-info py-2">
                <i class="fas fa-spinner fa-spin me-1"></i> Cargando contratistas...
            </div>

            <div style="overflow-x: auto">
                <table id="tblContratistas" class="table table-striped nowrap display" style="width: 100%">
                    <thead class="bg-system text-white">
                        <tr>
                            <td>ID</td>
                            <td>RUC</td>
                            <td>NOMBRE</td>
                            <td>DIRECCION</td>
                            <td>TELEFONO</td>
                            <td>EMAIL</td>
                            <td>ESTADO</td>
                            <td>ACCIONES</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="contratista of listaContratistas" :key="contratista.id">
                            <td>{{ zfill(contratista.id) }}</td>
                            <td>{{ contratista.cont_ruc }}</td>
                            <td>{{ contratista.cont_nombre }}</td>
                            <td>{{ contratista.cont_direccion ? contratista.cont_direccion : '-' }}</td>
                            <td>{{ contratista.cont_telefono ? contratista.cont_telefono : '-' }}</td>
                            <td>{{ contratista.cont_email ? contratista.cont_email : '-' }}</td>
                            <td v-if="parseInt(contratista.cont_estado) === 1">
                                <span class="badge bg-success"><i class="fas fa-check-double"></i> Activo</span>
                            </td>
                            <td v-else>
                                <span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span>
                            </td>
                            <td>
                                <template v-if="admin">
                                    <button @click="loadContratista(contratista), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalContratista">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!--MODAL CREATE CONTRATISTA-->
            <div id="modalContratista" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 v-if="estadoSave"><i class="fas fa-file-alt"></i> Crear Contratista</h5>
                            <h5 v-else><i class="fas fa-file-alt"></i> Actualizar Contratista</h5>
                            <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal" :disabled="loadingSave">X</button>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <input type="hidden" v-model="idEdit">

                                <div class="col-md-6 mb-3">
                                    <label for="contRuc" class="col-form-label col-form-label-sm"><i class="fal fa-id-card"></i> RUC</label>
                                    <input v-model="newContratista.contRuc" v-numbers-only="{ decimal: false }" type="text" inputmode="numeric" class="form-control" id="contRuc" placeholder="Ingrese RUC o identificacion" />
                                    <div v-html="formValidacion.contRuc" class="text-danger"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="contNombre" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Nombre</label>
                                    <input v-model="newContratista.contNombre" type="text" class="form-control" id="contNombre" placeholder="Ingrese nombre o razon social" />
                                    <div v-html="formValidacion.contNombre" class="text-danger"></div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="contDireccion" class="col-form-label col-form-label-sm"><i class="fal fa-map-marker-alt"></i> Direccion</label>
                                    <input v-model="newContratista.contDireccion" type="text" class="form-control" id="contDireccion" placeholder="Ingrese direccion" />
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="contTelefono" class="col-form-label col-form-label-sm"><i class="fal fa-phone"></i> Telefono</label>
                                    <input v-model="newContratista.contTelefono" v-numbers-only="{ decimal: false }" type="text" inputmode="numeric" class="form-control" id="contTelefono" placeholder="Ingrese telefono" />
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="contEmail" class="col-form-label col-form-label-sm"><i class="fal fa-envelope"></i> Email</label>
                                    <input v-model="newContratista.contEmail" v-email-only type="email" class="form-control" id="contEmail" placeholder="Ingrese email" />
                                    <div v-html="formValidacion.contEmail" class="text-danger"></div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="contEstado" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Estado</label>
                                    <select v-model="newContratista.contEstado" class="form-select border" id="contEstado">
                                        <option value="1">ACTIVO</option>
                                        <option value="0">INACTIVO</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-primary" :disabled="loadingSave" @click="saveUpdateContratista()">
                                <span v-if="loadingSave"><i class="fas fa-spinner fa-spin"></i> Procesando...</span>
                                <span v-else-if="estadoSave"><i class="fas fa-save"></i> Crear</span>
                                <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                            </button>
                            <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal" :disabled="loadingSave"><i class="fas fa-stop"></i> Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--CLOSE MODAL CREATE CONTRATISTA-->
        </div>
    </div>
</div>

<script type="text/javascript">

<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';

    if (window.appContratistas) {
        window.appContratistas.unmount();
    }

    window.appContratistas = Vue.createApp({
        data() {
            return {
                url: siteUrl,
                admin: admin,
                estadoSave: true,
                loadingList: false,
                loadingSave: false,
                idEdit: '',
                newContratista: {
                    contRuc: '',
                    contNombre: '',
                    contDireccion: '',
                    contTelefono: '',
                    contEmail: '',
                    contEstado: '1',
                },
                listaContratistas: [],
                formValidacion: [],
            };
        },
        created() {
            this.getContratistas();
        },
        methods: {
            async getContratistas() {
                this.loadingList = true;
                try {
                    let response = await axios.get(this.url + '/biocomedor/contratistas/getContratistas');
                    if ($.fn.DataTable.isDataTable('#tblContratistas')) {
                        $('#tblContratistas').DataTable().destroy();
                    }

                    this.listaContratistas = response.data ? response.data : [];

                    this.$nextTick(() => {
                        if (this.admin) {
                            dataTableModalBtn('#tblContratistas', 'Lista de contratistas', '#modalContratista', 'CREAR CONTRATISTA');
                        } else {
                            dataTable('#tblContratistas', 'Lista de contratistas');
                        }
                    });
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    this.loadingList = false;
                }
            },
            loadContratista(contratista) {
                this.newContratista = {
                    contRuc: contratista.cont_ruc,
                    contNombre: contratista.cont_nombre,
                    contDireccion: contratista.cont_direccion,
                    contTelefono: contratista.cont_telefono,
                    contEmail: contratista.cont_email,
                    contEstado: contratista.cont_estado,
                };
                this.idEdit = contratista.id;
                this.formValidacion = [];
            },
            async saveUpdateContratista() {
                if (this.loadingSave) {
                    return;
                }

                this.loadingSave = true;
                let datos = this.formData(this.newContratista);
                let url = this.url + '/biocomedor/contratistas/saveContratista';

                if (this.idEdit !== '') {
                    datos.append('idContratista', this.idEdit);
                    url = this.url + '/biocomedor/contratistas/updateContratista';
                }

                try {
                    let response = await axios.post(url, datos);

                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getContratistas();
                        $('#modalContratista').modal('hide');
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
                this.newContratista = {
                    contRuc: '',
                    contNombre: '',
                    contDireccion: '',
                    contTelefono: '',
                    contEmail: '',
                    contEstado: '1',
                };
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
    window.appContratistas.use(AllDirectives);
    window.appContratistas.mount('#app');
</script>
