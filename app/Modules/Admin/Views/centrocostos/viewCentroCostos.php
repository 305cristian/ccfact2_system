<!DOCTYPE html>
<!--
/**
 * Description of viewCentroCostos
 *
/**
 * @author CRISTIAN PAZ
 * @date 24 ene. 2024
 * @time 12:07:13
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-clipboard-list"></i> Centros de costos</h5>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto">
                <table id="tblCentroCostos" class="table table-striped nowrap display" style="width: 100%">
                    <thead class="bg-system text-white">
                        <tr>
                            <td>ID</td>
                            <td>NOMBRE</td>
                            <td>DESCRIPCIÓN</td>
                            <td>ESTADO</td>
                            <td>ACCIONES</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="lcc of listaCentroCostos">
                            <td>{{zfill(lcc.id)}}</td>
                            <td>{{lcc.cc_nombre}}</td>
                            <td>{{lcc.cc_descripcion}}</td>

                            <td v-if="lcc.cc_estado == 1 "><span class="badge bg-success"><i class="fas fa-check-double"></i>  Activo</span></td>
                            <td v-else><span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span></td>


                            <td>
                                <template v-if="admin">
                                    <button @click="loadCentroCosto(lcc), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalCentroCostos"><i class="fas fa-edit"></i> </button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!--MODAL CREATE CC-->
            <div id="modalCentroCostos" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 v-if="estadoSave" class=""><i class="fas fa-file-alt"></i> Crear Centro de Costos</h5>
                            <h5 v-else class=""><i class="fas fa-file-alt"></i> Actualizar Centro de Costos</h5>
                            <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
                        </div>
                        <div class="modal-body">
                            <div class="text-left">
                                <input type="hidden" v-model="idEdit">
                                <div class="mb-3">
                                    <label for="ccNombre" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Nombre</label>
                                    <input  v-model="newCC.ccNombre" type="text" class="form-control" id="ccNombre" placeholder="Ingrese un nombre" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.ccNombre" class="text-danger"></div>
                                </div>                          

                                <div class="mb-3">
                                    <label for="ccDescripcion" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Descripción</label>
                                    <textarea  v-model="newCC.ccDescripcion" type="text" class="form-control" id="ccDescripcion" placeholder="Ingrese una descripción"></textarea>
                                    <!--validaciones-->
                                    <div v-html="formValidacion.ccDescripcion" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="ccEstado" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Estado</label>
                                    <select v-model="newCC.ccEstado" class="form-select border" id="ccEstado">
                                        <option value="1">ACTIVO</option>
                                        <option value="0"> INACTIVO</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button  class="btn btn-primary" @click="saveUpdateCentroCostos()">
                                <span v-if="estadoSave"><i class="fas fa-save"></i> Crear</span>
                                <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                            </button>
                            <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-stop"></i> Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--CLOSE MODAL CREATE CC-->
        </div>
    </div>
</div>

<script type="text/javascript">
<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';

    if (window.appCc) {
        window.appCc.unmount();
    }
    window.appCc = Vue.createApp({

        data() {
            return {
                url: siteUrl,
                //TODO: PERMISOS
                admin: admin,

                //TODO: VARIABLES
                estadoSave: true,

                //TODO: V-MODELS
                idEdit: '',
                newCC: {
                    ccNombre: '',
                    ccDescripcion: '',
                    ccEstado: '1'
                },

                //TODO: LISTAS
                listaCentroCostos: [],
                formValidacion: []
            }

        },
        created() {
            this.getCentrosCostos();
        },
        methods: {
            async getCentrosCostos() {
                try {
                    let response = await axios.get(this.url + '/admin/cc/getCentrosCostos');
                    if (response.data) {
                        this.listaCentroCostos = response.data;
                    } else {
                        sweet_msg_dialog('warning', 'No se encontraron centros de costos registradas');
                    }
                    if (this.admin) {
                        dataTableModalBtn('#tblCentroCostos', 'Lista de Centros de Costos', '#modalCentroCostos', 'CREAR CENTRO DE COSTOS');
                    } else {
                        dataTable('#tblCentroCostos', 'Lista de Centros de Costos');
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                }
            },
            loadCentroCosto(cc) {
                this.newCC = {
                    ccNombre: cc.cc_nombre,
                    ccDescripcion: cc.cc_descripcion,
                    ccEstado: cc.cc_estado
                };
                this.idEdit = cc.id;
                this.nameAux = cc.cc_nombre;

            },
            async saveUpdateCentroCostos() {
                let datos = this.formData(this.newCC);
                let url = this.url + '/admin/cc/saveCentroCosto';

                if (this.idEdit !== '') {
                    datos.append('idCC', this.idEdit);
                    datos.append('nameAux', this.nameAux);//TODO: ESTA VARIABLE SE LA USA PARA VALIDAR QUE NO AYA OTRA REGISTRO CON EL MISMO NOMBRE
                    url = this.url + '/admin/cc/updateCentroCosto';
                }

                try {
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getCentrosCostos();
                        $('#modalCentroCostos').modal('hide');
                        $('.modal-backdrop').remove();

                    } else if (response.data.status === 'existe') {

                        sweet_msg_dialog('warning', response.data.msg);

                    } else if (response.data.status === 'vacio') {

                        this.formValidacion = response.data.msg;

                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                }
            },
            clear() {
                this.newCC = {
                    ccNombre: '',
                    ccDescripcion: '',
                    ccEstado: '1'
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
            }
        }
    });
    window.appCc.mount('#app');

</script>