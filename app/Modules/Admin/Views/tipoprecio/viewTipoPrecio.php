<!DOCTYPE html>
<!--
/**
 * Description of viewTipoPrecio
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 3 jul 2024
 * @time 12:24:55 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-dollar-circle"></i> Tipos de Precio</h5>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto">
                <table id="tblTipoprecio" class="table table-striped nowrap display" style="width: 100%">
                    <thead class="bg-system text-white">
                        <tr>
                            <td>ID</td>
                            <td>TIPO DE PRECIO</td>
                            <td>DESCRIPCIÓN</td>
                            <td>ESTADO</td>
                            <td>ACCIONES</td>
                        </tr>
                    </thead>
                    <tbody>

                        <tr v-for="ltp of listaTiposPrecio">
                            <td>{{zfill(ltp.id)}}</td>
                            <td>{{ltp.tpc_nombre}}</td>
                            <td>{{ltp.tpc_descripcion}}</td>

                            <td v-if="ltp.tpc_estado == 1 "><span class="badge bg-success"><i class="fas fa-check-double"></i>  Activo</span></td>
                            <td v-else><span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span></td>


                            <td>
                                <template v-if="admin">
                                    <button @click="loadTipoPrecio(ltp), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalTipoPrecio"><i class="fas fa-edit"></i> </button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!--MODAL CREATE TIPO PRECIO-->
            <div id="modalTipoPrecio" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 v-if="estadoSave" class=""><i class="fas fa-dollar-circle"></i> Crear Tipo de Precio</h5>
                            <h5 v-else class=""><i class="fas fa-dollar-circle"></i> Actualizar Tipo de Precio</h5>
                            <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
                        </div>
                        <div class="modal-body">
                            <div class="text-left">
                                <input type="hidden" v-model="idEdit">

                                <div class="mb-3">
                                    <label for="tpNombre" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Nombre</label>
                                    <input  v-model="newTipoPrecio.tpNombre" type="text" class="form-control" id="tpNombre" placeholder="Ingrese un nombre" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.tpNombre" class="text-danger"></div>
                                </div>                          
                                <div class="mb-3">
                                    <label for="tpDescripcion" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Descripción</label>
                                    <textarea rows="3"  v-model="newTipoPrecio.tpDescripcion" type="text" class="form-control" id="tpDescripcion" placeholder="Ingrese una Descripción" ></textarea>
                                    <!--validaciones-->
                                    <div v-html="formValidacion.tpDescripcion" class="text-danger"></div>
                                </div>                          


                                <div class="mb-3">
                                    <label for="tpEstado" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Estado</label>
                                    <select v-model="newTipoPrecio.tpEstado" class="form-select border" id="tpEstado">
                                        <option value="1">ACTIVO</option>
                                        <option value="0"> INACTIVO</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button  class="btn btn-primary" @click="saveUpdateTipoPrecio()">
                                <span v-if="estadoSave"><i class="fas fa-save"></i> Crear</span>
                                <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                            </button>
                            <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-stop"></i> Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--CLOSE MODAL CREATE TIPO PRECIO-->
        </div>
    </div>
</div>

<script type="text/javascript">

<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';

    var v = new Vue({
        el: "#app",
        data: {
            url: siteUrl,
            //TODO: PERMISOS
            admin: admin,

            //TODO: VARIABLES
            estadoSave: true,

            //TODO: V-MODELS
            idEdit: '',
            newTipoPrecio: {
                tpNombre: '',
                tpDescripcion: '',
                tpEstado: '1'
            },

            //TODO: LISTAS
            listaTiposPrecio: [],
            formValidacion: []

        },
        created() {
            this.getTiposPrecios();
        },
        methods: {
            async getTiposPrecios() {

                try {
                    let response = await axios.get(this.url + '/admin/tiposprice/getTipoPrecio');
                    if (response.data) {
                        v.listaTiposPrecio = response.data;
                    } else {
                        sweet_msg_dialog('warning', 'No se encontraron tipos de precios registradas');
                    }
                    if (v.admin) {
                        dataTableModalBtn('#tblTipoprecio', 'Lista de Tipos de PrecioS', '#modalTipoPrecio', 'CREAR TIPO DE PRECIO');
                    } else {
                        dataTable('#tblTipoprecio', 'Lista de Tipos de Precios');
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                }

            },
            loadTipoPrecio(tpc) {
                v.newTipoPrecio = {
                    tpNombre: tpc.tpc_nombre,
                    tpDescripcion: tpc.tpc_descripcion,
                    tpEstado: tpc.tpc_estado
                };
                v.idEdit = tpc.id;
                v.nameAux = tpc.tpc_nombre;

            },
            async saveUpdateTipoPrecio() {
                let datos = v.formData(v.newTipoPrecio);
                let url = this.url + '/admin/tiposprice/saveTipoPrecio';

                if (v.idEdit != '') {
                    datos.append('idTp', v.idEdit);
                    datos.append('nameAux', v.nameAux);//TODO: ESTA VARIABLE SE LA USA PARA VALIDAR QUE NO AYA OTRA REGISTRO CON EL MISMO NOMBRE
                    url = this.url + '/admin/tiposprice/updateTipoPrecio';
                }

                try {
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        v.clear();
                        v.getTiposPrecios();
                        $('#modalTipoPrecio').modal('hide');
                        $('.modal-backdrop').remove();

                    } else if (response.data.status === 'existe') {

                        sweet_msg_dialog('warning', response.data.msg);

                    } else if (response.data.status === 'vacio') {

                        v.formValidacion = response.data.msg;

                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                }
            },
            clear() {
                v.newTipoPrecio = {
                    tpNombre: "",
                    tpDescripcion: "",
                    tpEstado: "1"
                };
                v.estadoSave = true;
                v.idEdit = '';
                v.formValidacion = [];
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

</script>