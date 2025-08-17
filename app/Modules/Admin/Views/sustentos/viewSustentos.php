<!DOCTYPE html>
<!--
/**
 * Description of viewSustentos
 *
/**
 * @author CRISTIAN PAZ
 * @date 24 ene. 2024
 * @time 12:08:04
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-clipboard-medical"></i> Sustentos</h5>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto">
                <table id="tblSustentos" class="table table-striped nowrap display" style="width: 100%">
                    <thead class="bg-system text-white">
                        <tr>
                            <td>ID</td>
                            <td>COD</td>
                            <td>NOMBRE</td>
                            <td>TIPOS DE COMPROBANTES</td>
                            <td>ESTADO</td>
                            <td>ACCIONES</td>
                        </tr>
                    </thead>
                    <tbody>

                        <tr v-for="lst of listaSustentos">
                            <td>{{zfill(lst.id)}}</td>
                            <td>{{lst.sus_codigo}}</td>
                            <td>{{lst.sus_nombre}}</td>
                            <td>{{lst.sus_tipo_comprobante}}</td>

                            <td v-if="lst.sus_estado == 1 "><span class="badge bg-success"><i class="fas fa-check-double"></i>  Activo</span></td>
                            <td v-else><span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span></td>


                            <td>
                                <template v-if="admin">
                                    <button @click="loadSustento(lst), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalSustentos"><i class="fas fa-edit"></i> </button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!--MODAL CREATE SUSTENTO-->
            <div id="modalSustentos" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 v-if="estadoSave" class=""><i class="fas fa-file-alt"></i> Crear Sustento</h5>
                            <h5 v-else class=""><i class="fas fa-file-alt"></i> Actualizar Sustento</h5>
                            <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
                        </div>
                        <div class="modal-body">
                            <div class="text-left">
                                <input type="hidden" v-model="idEdit">
                                <div class="mb-3">
                                    <label for="sustentoCodigo" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Código</label>
                                    <input  v-model="newSustento.sustentoCodigo" type="text" class="form-control" id="sustentoCodigo" placeholder="Ingrese un codigo" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.sustentoCodigo" class="text-danger"></div>
                                </div>                          
                                <div class="mb-3">
                                    <label for="sustentoNombre" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Nombre</label>
                                    <input  v-model="newSustento.sustentoNombre" type="text" class="form-control" id="sustentoNombre" placeholder="Ingrese un nombre" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.sustentoNombre" class="text-danger"></div>
                                </div>                          
                                <div class="mb-3">
                                    <label for="sustentoTipoComprobante" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Tipos Comprobante</label>
                                    <input  v-model="newSustento.sustentoTipoComprobante" type="text" class="form-control" id="sustentoTipoComprobante" placeholder="Ingrese un tipo de comprobante" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.sustentoTipoComprobante" class="text-danger"></div>
                                </div>                          


                                <div class="mb-3">
                                    <label for="sustentoEstado" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Estado</label>
                                    <select v-model="newSustento.sustentoEstado" class="form-select border" id="sustentoEstado">
                                        <option value="1">ACTIVO</option>
                                        <option value="0"> INACTIVO</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button  class="btn btn-primary" @click="saveUpdateSustento()">
                                <span v-if="estadoSave"><i class="fas fa-save"></i> Crear</span>
                                <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                            </button>
                            <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-stop"></i> Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--CLOSE MODAL CREATE SUSTENTO-->
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
            newSustento: {
                sustentoCodigo: '',
                sustentoNombre: '',
                sustentoTipoComprobante: '',
                sustentoEstado: '1'
            },

            //TODO: LISTAS
            listaSustentos: [],
            formValidacion: []

        },
        created() {
            this.getSustentos();
        },
        methods: {
            async getSustentos() {
                try {
                    let response = await axios.get(this.url + '/admin/sustento/getSustentos');
                    if (response.data) {
                        v.listaSustentos = response.data;
                    } else {
                        sweet_msg_dialog('warning', 'No se encontraron sustentos registradas');
                    }
                    if (v.admin) {
                        dataTableModalBtn('#tblSustentos', 'Lista de Sustentos', '#modalSustentos', 'CREAR SUSTENTO');
                    } else {
                        dataTable('#tblSustentos', 'Lista de Sustentos');
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                }
            },
            loadSustento(sus) {
                v.newSustento = {
                    sustentoCodigo: sus.sus_codigo,
                    sustentoNombre: sus.sus_nombre,
                    sustentoTipoComprobante: sus.sus_tipo_comprobante,
                    sustentoEstado: sus.sus_estado
                };
                v.idEdit = sus.id;
                v.nameAux = sus.sus_nombre;

            },
            async saveUpdateSustento() {
                let datos = v.formData(v.newSustento);
                let url = this.url + '/admin/sustento/saveSustento';

                if (v.idEdit != '') {
                    datos.append('idSus', v.idEdit);
                    datos.append('nameAux', v.nameAux);//TODO: ESTA VARIABLE SE LA USA PARA VALIDAR QUE NO AYA OTRA REGISTRO CON EL MISMO NOMBRE
                    url = this.url + '/admin/sustento/updateSustento';
                }

                try {
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        v.clear();
                        v.getSustentos();
                        $('#modalSustentos').modal('hide');
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
                v.newSustento = {
                    sustentoCodigo: '',
                    sustentoNombre: '',
                    sustentoTipoComprobante: '',
                    sustentoEstado: '1'
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
