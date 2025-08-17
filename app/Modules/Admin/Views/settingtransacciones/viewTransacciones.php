<!DOCTYPE html>
<!--
/**
 * Description of viewTransacciones
 *
/**
 * @author CRISTIAN PAZ
 * @date 21 mar. 2024
 * @time 15:32:42
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-user-cog"></i> Administrar Tipos de Transacciones</h5>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto">
                <table id="tblTransacciones" class="table table-striped nowrap display" style="width: 100%">
                    <thead class="bg-system text-white">
                        <tr>
                            <td>ID</td>
                            <td>CÓDIGO</td>
                            <td>NOMBRE</td>
                            <td>DESCRIPCIÓN</td>
                            <td>ACCIONES</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="lt of listaTransacciones">
                            <td>{{zfill(lt.id)}}</td>
                            <td>{{lt.tr_codigo}}</td>
                            <td>{{lt.tr_nombre}}</td>
                            <td>{{lt.tr_descripcion}}</td>


                            <td>
                                <template v-if="admin">
                                    <button @click="loadTransaccion(lt), estadoSave = false " class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalTransacciones"><i class="fas fa-edit"></i> </button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div> 

            <!--MODAL CREATE TRANSACCION-->
            <div id="modalTransacciones" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 v-if="estadoSave" class=""><i class="fas fa-file-alt"></i> Crear Tipo de Transacción</h5>
                            <h5 v-else class=""><i class="fas fa-file-alt"></i> Actualizar Tipo de Transacción</h5>
                            <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
                        </div>
                        <div class="modal-body">
                            <div class="text-left">
                                <input type="hidden" v-model="idEdit">
                                <div class="mb-3">
                                    <label for="nombreTrans" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Nombre</label>
                                    <input  v-model="newTrans.nombreTrans" type="text" class="form-control" id="nombreTrans" placeholder="Ingrese un nombre" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.nombreTrans" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="codigoTrans" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Código</label>
                                    <input  v-model="newTrans.codigoTrans" type="text" class="form-control" id="codigoTrans" placeholder="Ingrese un codigo" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.codigoTrans" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="descripcionTrans" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Descripción</label>
                                    <textarea  v-model="newTrans.descripcionTrans" type="text" class="form-control" id="descripcionTrans" placeholder="Ingrese una descripción"></textarea>
                                    <!--validaciones-->
                                    <div v-html="formValidacion.descripcionTrans" class="text-danger"></div>
                                </div>



                            </div>
                        </div>
                        <div class="modal-footer">
                            <button  class="btn btn-primary" @click="saveUpdateTransaccion()">
                                <span v-if="estadoSave"><i class="fas fa-save"></i> Crear</span>
                                <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                            </button>
                            <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-stop"></i> Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--CLOSE MODAL CREATE TRANSACCION-->

        </div>
    </div>
</div>

<script type="text/javascript">
<?php $admin = $user->validatePermisos('admin', $user->id) ?>

    var admin = '<?= $admin ?>';

    var v = new Vue({
        el: '#app',

        data: {
            url: siteUrl,
            //PERMISOS
            admin: admin,

            //TODO: VARIABLES
            estadoSave: true,

            //TODO: V-MODELS
            idEdit: '',
            newTrans: {
                nombreTrans: '',
                codigoTrans: '',
                descripcionTrans: ''
            },

            //TODO: LISTAS
            listaTransacciones: [],
            formValidacion: []
        },
        created() {
            this.getTransacciones();
        },
        methods: {
            async getTransacciones() {
                try {
                    let response = await axios.get(this.url + '/admin/trans/getTransacciones');
                    if (response.data) {
                        v.listaTransacciones = response.data;
                    } else {
                        sweet_msg_dialog('warning', 'No se encontraron tipos de transacciones registradas');
                    }
                    if (v.admin) {
                        dataTableModalBtn('#tblTransacciones', 'Lista de Transacciones', '#modalTransacciones', 'CREAR TRANSACCIÓN');
                    }else{
                        dataTable('#tblTransacciones', 'Lista de Transacciones');
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                }

            },
            loadTransaccion(trans) {
                v.newTrans = {
                    nombreTrans: trans.tr_nombre,
                    codigoTrans: trans.tr_codigo,
                    descripcionTrans: trans.tr_descripcion
                };
                v.idEdit = trans.id;
                v.nameAux = trans.tr_nombre;

            },
            async saveUpdateTransaccion() {
                let datos = v.formData(v.newTrans);
                let url = this.url + '/admin/trans/saveTransaccion';

                if (v.idEdit != '') {
                    datos.append('idTrans', v.idEdit);
                    datos.append('nameAux', v.nameAux);//TODO: ESTA VARIABLE SE LA USA PARA VALIDAR QUE NO AYA OTRA TRANSACCION CON EL MISMO NOMBRE
                    url = this.url + '/admin/trans/updateTransaccion';
                }

                try {
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        v.clear();
                        v.getTransacciones();
                        $('#modalTransacciones').modal('hide');
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
                v.newTrans = {
                    nombreTrans: '',
                    codigoTrans: '',
                    descripcionTrans: ''
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
                return  zFill(num, 3);
            }
        }
    });

</script>