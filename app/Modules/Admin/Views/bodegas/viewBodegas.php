<!DOCTYPE html>
<!--
/**
 * Description of viewBodegas
 *
/**
 * @author CRISTIAN PAZ
 * @date 24 ene. 2024
 * @time 12:03:16
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-buildings"></i> Bodegas</h5>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto">
                <table id="tblBodegas" class="table table-striped nowrap display" style="width: 100%">
                    <thead class="bg-system text-white">
                        <tr>
                            <td>ID</td>
                            <td>NOMBRE</td>
                            <td>DETALLE</td>
                            <td>CTA CONTABLE SIN IMPUESTO</td>
                            <td>CTA CONTABLE IVA</td>
                            <td>ESTADO</td>
                            <td>ACCIONES</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="lb of listaBodegas">
                            <td>{{zfill(lb.id)}}</td>
                            <td>{{lb.bod_nombre}}</td>
                            <td>{{lb.bod_descripcion}}</td>
                            <td>{{lb.bod_ctacont0? lb.bod_ctacont0:'-'}}</td>
                            <td>{{lb.bod_ctacont_iva?lb.bod_ctacont_iva:'-'}}</td>

                            <td v-if="lb.bod_estado == 1 "><span class="badge bg-success"><i class="fas fa-check-double"></i>  Activo</span></td>
                            <td v-else><span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span></td>


                            <td>
                                <template v-if="admin">
                                    <button @click="loadBodega(lb), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalBodega"><i class="fas fa-edit"></i> </button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!--MODAL CREATE BODEGA-->
            <div id="modalBodega" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 v-if="estadoSave" class=""><i class="fas fa-file-alt"></i> Crear Bodega</h5>
                            <h5 v-else class=""><i class="fas fa-file-alt"></i> Actualizar Bodega</h5>
                            <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
                        </div>
                        <div class="modal-body">
                            <div class="text-left">
                                <input type="hidden" v-model="idEdit">
                                <div class="mb-3">
                                    <label for="bodNombre" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Nombre</label>
                                    <input  v-model="newBodega.bodNombre" type="text" class="form-control" id="bodNombre" placeholder="Ingrese un nombre" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.bodNombre" class="text-danger"></div>
                                </div>                          

                                <div class="mb-3">
                                    <label for="bodDescripcion" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Descripción</label>
                                    <textarea  v-model="newBodega.bodDescripcion" type="text" class="form-control" id="bodDescripcion" placeholder="Ingrese un detalle"></textarea>
                                    <!--validaciones-->
                                    <div v-html="formValidacion.bodDescripcion" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="bodEstado" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Estado</label>
                                    <select v-model="newBodega.bodEstado" class="form-select border" id="bodEstado">
                                        <option value="1">ACTIVO</option>
                                        <option value="0"> INACTIVO</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button  class="btn btn-primary" @click="saveUpdateBodega()">
                                <span v-if="estadoSave"><i class="fas fa-save"></i> Crear</span>
                                <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                            </button>
                            <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-stop"></i> Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--CLOSE MODAL CREATE BODEGA-->
        </div>
    </div>
</div>

<script type="text/javascript">

<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';

    if (window.appBodegas) {
        window.appBodegas.unmount();
    }

    window.appBodegas = Vue.createApp({

        data() {
            return {
                url: siteUrl,

                //TODO: PERMISOS
                admin: admin,

                //TODO: VARIABLES
                estadoSave: true,
                //TODO: V-MODELS
                idEdit: '',
                newBodega: {
                    bodNombre: '',
                    bodDescripcion: '',
                    bodIva0: '',
                    bodIva: '',
                    bodEstado: '1'
                },

                //TODO: LISTAS
                listaBodegas: [],
                formValidacion: []

            };
        },
        created() {
            this.getBodegas();
        },
        methods: {
            async getBodegas() {
                try {
                    let response = await axios.get(this.url + '/admin/bodegas/getBodegas');
                    if (response.data) {
                        this.listaBodegas = response.data;
                    } else {
                        sweet_msg_dialog('warning', 'No se encontraron bodgas registradas');
                    }
                    if (this.admin) {
                        dataTableModalBtn('#tblBodegas', 'Lista de bodegas', '#modalBodega', 'CREAR BODEGA');
                    } else {
                        dataTable('#tblBodegas', 'Lista de bodegas');
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                }
            },
            loadBodega(bod) {
                this.newBodega = {
                    bodNombre: bod.bod_nombre,
                    bodDescripcion: bod.bod_descripcion,
                    bodEstado: bod.bod_estado
                };
                this.idEdit = bod.id;
                this.nameAux = bod.bod_nombre;

            },
            async saveUpdateBodega() {
                let datos = this.formData(this.newBodega);
                let url = this.url + '/admin/bodegas/saveBodegas';

                if (this.idEdit != '') {
                    datos.append('idBod', this.idEdit);
                    datos.append('nameAux', this.nameAux);//TODO: ESTA VARIABLE SE LA USA PARA VALIDAR QUE NO AYA OTRA REGISTRO CON EL MISMO NOMBRE
                    url = this.url + '/admin/bodegas/updateBodegas';
                }

                try {
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getBodegas();
                        $('#modalBodega').modal('hide');
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
                this.newBodega = {
                    bodNombre: '',
                    bodDescripcion: '',
                    bodIva0: '',
                    bodIva: '',
                    bodEstado: '1'
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
    window.appBodegas.mount('#app');
</script>
