<!DOCTYPE html>
<!--
/**
 * Description of viewTipoProducto
 *
/**
 * @author CRISTIAN PAZ
 * @date 15 abr. 2024
 * @time 11:33:39
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-box-archive"></i> Tipos de Productos</h5>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto">
                <table id="tblTipoProducto" class="table table-striped nowrap display" style="width: 100%">
                    <thead class="bg-system text-white">
                        <tr>
                            <td>ID</td>
                            <td>NOMBRE</td>
                            <td>DETALLE</td>
                            <td>ESTADO</td>
                            <td>ACCIONES</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="ltp of listaTipoProducto">
                            <td>{{zfill(ltp.id)}}</td>
                            <td>{{ltp.tp_nombre}}</td>
                            <td>{{shortText(ltp.tp_descripcion)}}</td>

                            <td v-if="ltp.tp_estado == 1 "><span class="badge bg-success"><i class="fas fa-check-double"></i>  Activo</span></td>
                            <td v-else><span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span></td>


                            <td>
                                <template v-if="admin">
                                    <button @click="loadTipoProducto(ltp), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalTP"><i class="fas fa-edit"></i> </button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!--MODAL TIPO DE PRODUCTO-->
            <div id="modalTP" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 v-if="estadoSave" class=""><i class="fas fa-file-alt"></i> Crear Tipo de producto</h5>
                            <h5 v-else class=""><i class="fas fa-file-alt"></i> Actualizar Tipo de producto</h5>
                            <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
                        </div>
                        <div class="modal-body">
                            <div class="text-left">
                                <input type="hidden" v-model="idEdit">
                                <div class="mb-3">
                                    <label for="tpNombre" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Nombre</label>
                                    <input  v-model="newTP.tpNombre" type="text" class="form-control" id="tpNombre" placeholder="Ingrese un nombre" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.tpNombre" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="tpDescripcion" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Detalle</label>
                                    <textarea  v-model="newTP.tpDescripcion" type="text" class="form-control" id="tpDescripcion" placeholder="Ingrese un detalle" ></textarea>
                                    <!--validaciones-->
                                    <div v-html="formValidacion.tpDescripcion" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="tpEstado" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Estado</label>
                                    <select v-model="newTP.tpEstado" class="form-control border" id="tpEstado">
                                        <option value="1">ACTIVO</option>
                                        <option value="0"> INACTIVO</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button  class="btn btn-primary" @click="saveUpdateTipoProducto()">
                                <span v-if="estadoSave"><i class="fas fa-save"></i> Crear</span>
                                <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                            </button>
                            <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-stop"></i> Cancelar</button>
                        </div>
                    </div>
                </div>
                <!--CLOSE MODAL TIPO DE PRODUCTO-->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';

    if (window.appTipoProducto) {
        window.appTipoProducto.unmount();
    }

    window.appTipoProducto = Vue.createApp({

        data() {
            return {
                url: siteUrl,

                //TODO: PERMISOS
                admin: admin,

                //TODO: VARIABLES
                estadoSave: true,

                //TODO: V-MODELS
                idEdit: '',
                newTP: {
                    tpNombre: '',
                    tpDescripcion: '',
                    tpEstado: '1'
                },

                //TODO: LISTAS
                listaTipoProducto: [],
                formValidacion: []
            }

        },
        created() {
            this.getTiposProducto();
        },
        methods: {
            async getTiposProducto() {
                try {
                    let response = await axios.get(this.url + '/admin/tiposprod/getTiposProducto');
                    if (response.data) {
                        this.listaTipoProducto = response.data;
                    } else {
                        sweet_msg_dialog('warning', 'No se encontraron tipos de producto registradas');
                    }
                    if (this.admin) {
                        dataTableModalBtn('#tblTipoProducto', 'Lista de tipos de producto', '#modalTP', 'CREAR TIPO DE PRODUCTYO');
                    } else {
                        dataTable('#tblTipoProducto', 'Lista de tipos de producto');
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                }
            },
            loadTipoProducto(tp) {
                this.newTP = {
                    tpNombre: tp.tp_nombre,
                    tpDescripcion: tp.tp_descripcion,
                    tpEstado: tp.tp_estado
                };
                this.idEdit = tp.id;
                this.nameAux = tp.tp_nombre;

            },
            async saveUpdateTipoProducto() {
                let datos = this.formData(this.newTP);
                let url = this.url + '/admin/tiposprod/saveTipoProducto';

                if (this.idEdit != '') {
                    datos.append('idTP', this.idEdit);
                    datos.append('nameAux', this.nameAux);//TODO: ESTA VARIABLE SE LA USA PARA VALIDAR QUE NO EXISTA OTRA REGISTRO CON EL MISMO NOMBRE
                    url = this.url + '/admin/tiposprod/updateTipoProducto';
                }

                try {
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getTiposProducto();
                        $('#modalTP').modal('hide');
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
            shortText(texto) {
                let txt = "";
                if (texto.length > 50) {
                    txt = texto.substring(0, 50);
                } else {
                    txt = texto;
                }
                return txt;
            },
            clear() {
                this.newTP = {
                    tpNombre: '',
                    tpDescripcion: '',
                    tpEstado: '1'
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
            }
            ,
            zfill(num) {
                return zFill(num, 3);
            }
        }
    });
    window.appTipoProducto.mount('#app');

</script>