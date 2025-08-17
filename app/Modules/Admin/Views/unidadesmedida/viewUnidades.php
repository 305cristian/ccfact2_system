<!DOCTYPE html>
<!--
/**
 * Description of viewUnidades
 *
/**
 * @author CRISTIAN PAZ
 * @date 15 abr. 2024
 * @time 11:25:40
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-box-open"></i> Administrar Unidades de medida</h5>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto">
                <table id="tblUnidadMedida" class="table table-striped nowrap display" style="width: 100%">
                    <thead class="bg-system text-white">
                        <tr>
                            <td>ID</td>
                            <td>NOMBRE</td>
                            <td>SIGLAS</td>
                            <td>ESTADO</td>
                            <td>ACCIONES</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="lum of listaUnidadesMedida">
                            <td>{{zfill(lum.id)}}</td>
                            <td>{{lum.um_nombre}}</td>
                            <td>{{lum.um_nombre_corto}}</td>

                            <td v-if="lum.um_estado == 1 "><span class="badge bg-success"><i class="fas fa-check-double"></i>  Activo</span></td>
                            <td v-else><span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span></td>

                            <td>
                                <template v-if="admin">
                                    <button @click="loadUnidadMedida(lum), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalUM"><i class="fas fa-edit"></i> </button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!--MODAL UNIDADES DE MEDIDA-->
            <div id="modalUM" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 v-if="estadoSave" class=""><i class="fas fa-user-cog"></i> Crear Unidad de medida</h5>
                            <h5 v-else class=""><i class="fas fa-user-cog"></i> Actualizar Unidad de medida</h5>
                            <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
                        </div>
                        <div class="modal-body">
                            <div class="text-left">
                                <input type="hidden" v-model="idEdit">
                                <div class="mb-3">
                                    <label for="unNombre" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Nombre</label>
                                    <input  v-model="newUM.unNombre" type="text" class="form-control" id="unNombre" placeholder="Ingrese un nombre" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.unNombre" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="unNombreCorto" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Abreviatura</label>
                                    <input  v-model="newUM.unNombreCorto" type="text" class="form-control" id="unNombreCorto" placeholder="Ingrese una abreviatura" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.unNombreCorto" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="umEstado" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Estado</label>
                                    <select v-model="newUM.umEstado" class="form-control border" id="umEstado">
                                        <option value="1">ACTIVO</option>
                                        <option value="0"> INACTIVO</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button  class="btn btn-primary" @click="saveUpdateUnidadMedida()">
                                <span v-if="estadoSave"><i class="fas fa-save"></i> Crear</span>
                                <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                            </button>
                            <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-stop"></i> Cancelar</button>
                        </div>
                    </div>
                </div>
                <!--CLOSE MODAL UNIDADES DE MEDIDA-->
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

                //TODO: PERMISOS
                admin: admin,

                //TODO: VARIABLES
                estadoSave: true,

                //TODO: V-MODELS
                idEdit: '',
                newUM: {
                    unNombre: '',
                    unNombreCorto: '',
                    umEstado: '1'
                },

                //TODO: LISTAS
                listaUnidadesMedida: [],
                formValidacion: []
            },
            created() {
                this.getUnidadesMedida();
            },
            methods: {
                async getUnidadesMedida() {
                    try {
                        let response = await axios.get(this.url + '/admin/medida/getUnidadesMedida');
                        if (response.data) {
                            v.listaUnidadesMedida = response.data;
                        } else {
                            sweet_msg_dialog('warning', 'No se han encontrado resultados de unidades de medida');
                        }
                        if (v.admin) {
                            dataTableModalBtn('#tblUnidadMedida', 'Lista unidades de medida', '#modalUM', 'CREAR UNIDAD DE MEDIDA');
                        } else {
                            dataTable('#tblUnidadMedida', 'Lista unidades de medida');
                        }
                    } catch (e) {
                        sweet_msg_dialog('error', '', '', e.response.data.message);
                    }

                },
                loadUnidadMedida(um) {
                    v.newUM = {
                        unNombre: um.um_nombre,
                        unNombreCorto: um.um_nombre_corto,
                        umEstado: um.um_estado
                    };
                    v.idEdit = um.id;
                    v.nameAux = um.um_nombre;

                },
                async saveUpdateUnidadMedida() {
                    let datos = v.formData(v.newUM);
                    let url = this.url + '/admin/medida/saveUnidadMedida';

                    if (v.idEdit != '') {
                        datos.append('idUM', v.idEdit);
                        datos.append('nameAux', v.nameAux);//TODO: ESTA VARIABLE SE LA USA PARA VALIDAR QUE NO AYA OTRA REGISTRO CON EL MISMO NOMBRE
                        url = this.url + '/admin/medida/updateUnidadMedida';
                    }

                    try {
                        let response = await axios.post(url, datos);
                        if (response.data.status === 'success') {

                            sweet_msg_dialog('success', response.data.msg);
                            v.clear();
                            v.getUnidadesMedida();
                            $('#modalUM').modal('hide');
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
                    v.newUM = {
                        unNombre: '',
                        unNombreCorto: '',
                        umEstado: '1'
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