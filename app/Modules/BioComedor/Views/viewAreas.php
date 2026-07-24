<!DOCTYPE html>
<!--
/**
 * Description of viewAreas
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 24 jul 2026
 * @time 12:36:21 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-layer-group"></i> Areas</h5>
        </div>

        <div class="card-body">
            <div v-if="loadingList" class="alert alert-info py-2">
                <i class="fas fa-spinner fa-spin me-1"></i> Cargando areas...
            </div>

            <div style="overflow-x: auto">
                <table id="tblAreas" class="table table-striped nowrap display" style="width: 100%">
                    <thead class="bg-system text-white">
                        <tr>
                            <td>ID</td>
                            <td>DEPARTAMENTO</td>
                            <td>CODIGO</td>
                            <td>NOMBRE</td>
                            <td>DESCRIPCION</td>
                            <td>ESTADO</td>
                            <td>ACCIONES</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="area of listaAreas" :key="area.id">
                            <td>{{ zfill(area.id) }}</td>
                            <td>{{ area.dep_nombre ? area.dep_nombre : '-' }}</td>
                            <td>{{ area.area_codigo }}</td>
                            <td>{{ area.area_nombre }}</td>
                            <td>{{ area.area_descripcion ? area.area_descripcion : '-' }}</td>
                            <td v-if="parseInt(area.area_estado) === 1">
                                <span class="badge bg-success"><i class="fas fa-check-double"></i> Activo</span>
                            </td>
                            <td v-else>
                                <span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span>
                            </td>
                            <td>
                                <template v-if="admin">
                                    <button @click="loadArea(area), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalArea">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="modalArea" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 v-if="estadoSave"><i class="fas fa-file-alt"></i> Crear Area</h5>
                            <h5 v-else><i class="fas fa-file-alt"></i> Actualizar Area</h5>
                            <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal" :disabled="loadingSave">X</button>
                        </div>

                        <div class="modal-body">
                            <input type="hidden" v-model="idEdit">

                            <div class="mb-3">
                                <label class="col-form-label col-form-label-sm"><i class="fal fa-sitemap"></i> Departamento</label>
                                <vue-select
                                    class="border rounded"
                                    :options="listaDepartamentos"
                                    label="dep_nombre"
                                    v-model="newArea.fkDepartamento"
                                    :reduce="departamento => departamento.id"
                                    placeholder="Seleccione un departamento">
                                    <template #option="departamento">
                                        {{ departamento.dep_codigo }} - {{ departamento.dep_nombre }}
                                    </template>
                                    <template #selected-option="departamento">
                                        {{ departamento.dep_codigo }} - {{ departamento.dep_nombre }}
                                    </template>
                                </vue-select>
                                <div v-html="formValidacion.fkDepartamento" class="text-danger"></div>
                            </div>

                            <div class="mb-3">
                                <label for="areaCodigo" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Codigo</label>
                                <input v-model.trim="newArea.areaCodigo" type="text" class="form-control" id="areaCodigo" placeholder="Ingrese un codigo" />
                                <div v-html="formValidacion.areaCodigo" class="text-danger"></div>
                            </div>

                            <div class="mb-3">
                                <label for="areaNombre" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Nombre</label>
                                <input v-model.trim="newArea.areaNombre" type="text" class="form-control" id="areaNombre" placeholder="Ingrese un nombre" />
                                <div v-html="formValidacion.areaNombre" class="text-danger"></div>
                            </div>

                            <div class="mb-3">
                                <label for="areaDescripcion" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Descripcion</label>
                                <textarea v-model.trim="newArea.areaDescripcion" class="form-control" id="areaDescripcion" placeholder="Ingrese un detalle"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="areaEstado" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Estado</label>
                                <select v-model="newArea.areaEstado" class="form-select border" id="areaEstado">
                                    <option value="1">ACTIVO</option>
                                    <option value="0">INACTIVO</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-primary" :disabled="loadingSave" @click="saveUpdateArea()">
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
    var listaDepartamentos = <?= json_encode($listaDepartamentos ?? []) ?>;

    if (window.appAreas) {
        window.appAreas.unmount();
    }

    window.appAreas = Vue.createApp({
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
                newArea: this.emptyArea(),
                listaAreas: [],
                listaDepartamentos: listaDepartamentos,
                formValidacion: [],
            };
        },
        created() {
            this.getAreas();
        },
        methods: {
            emptyArea() {
                return {
                    fkDepartamento: '',
                    areaCodigo: '',
                    areaNombre: '',
                    areaDescripcion: '',
                    areaEstado: '1',
                };
            },
            async getAreas() {
                this.loadingList = true;
                try {
                    let response = await axios.get(this.url + '/biocomedor/areas/getAreas');
                    if ($.fn.DataTable.isDataTable('#tblAreas')) {
                        $('#tblAreas').DataTable().destroy();
                    }

                    this.listaAreas = response.data ? response.data : [];

                    this.$nextTick(() => {
                        if (this.admin) {
                            dataTableModalBtn('#tblAreas', 'Lista de areas', '#modalArea', 'CREAR AREA');
                        } else {
                            dataTable('#tblAreas', 'Lista de areas');
                        }
                    });
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                } finally {
                    this.loadingList = false;
                }
            },
            loadArea(area) {
                this.newArea = {
                    fkDepartamento: area.fk_departamento ? area.fk_departamento : '',
                    areaCodigo: area.area_codigo,
                    areaNombre: area.area_nombre,
                    areaDescripcion: area.area_descripcion,
                    areaEstado: area.area_estado,
                };
                this.idEdit = area.id;
                this.formValidacion = [];
            },
            async saveUpdateArea() {
                if (this.loadingSave) {
                    return;
                }

                this.loadingSave = true;
                let datos = this.formData(this.newArea);
                let url = this.url + '/biocomedor/areas/saveArea';

                if (this.idEdit !== '') {
                    datos.append('idArea', this.idEdit);
                    url = this.url + '/biocomedor/areas/updateArea';
                }

                try {
                    let response = await axios.post(url, datos);

                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getAreas();
                        $('#modalArea').modal('hide');
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
                this.newArea = this.emptyArea();
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

    window.appAreas.mount('#app');
</script>
