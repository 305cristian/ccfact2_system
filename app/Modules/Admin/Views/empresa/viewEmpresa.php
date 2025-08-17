<!DOCTYPE html>
<!--
/**
 * Description of viewEmpresa
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
            <h5 class="card-title text-system"><i class="fas fa-building"></i> Empresa</h5>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto">
                <table id="tblEmpresa" class="table table-striped nowrap display" style="width: 100%">
                    <thead class="bg-system text-white">
                        <tr>
                            <th>RUC</th>
                            <th>RAZON SOCIAL</th>
                            <th>REPRESENTANTE LEGAL</th>
                            <th>NOMBRE COMERCIAL</th>
                            <th>DIRECCIÓN</th>                          
                            <th>TELÉFONO</th>
                            <th>CELULAR</th>
                            <th>EMAIL</th>
                            <th>FECHA DE CREACIÓN</th>
                            <th>MISIÓN</th>
                            <th>VISION</th>
                            <th class="text-center">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="lemp of listaEmpresa">

                            <td>{{lemp.epr_ruc}}</td>
                            <td>{{lemp.epr_razon_social}}</td>
                            <td>{{lemp.epr_rep_legal}}</td>
                            <td>{{lemp.epr_nombre_comercial}}</td>
                            <td>{{lemp.epr_direccion}}</td>
                            <td>{{lemp.epr_telefono}}</td>
                            <td>{{lemp.epr_celular}}</td>
                            <td>{{lemp.epr_email}}</td>
                            <td>{{lemp.epr_fecha_creacion}}</td>
                            <td>{{lemp.epr_mision}}</td>
                            <td>{{lemp.epr_vision}}</td>

                            <td>
                                <template v-if="admin">
                                    <button @click="loadEmpresa(lemp), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalmpresa"><i class="fas fa-edit"></i> </button>
                                </template>
                            </td>

                        </tr>
                    </tbody>
                </table>

            </div>
            <!--MODAL CREATE EMPRESA-->
            <div id="modalmpresa" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 v-if="estadoSave" class=""><i class="fas fa-file-alt"></i> Crear Empresa</h5>
                            <h5 v-else class=""><i class="fas fa-file-alt"></i> Actualizar Empresa</h5>
                            <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
                        </div>
                        <div class="modal-body">
                            <div class="text-left">
                                <input type="hidden" v-model="idEdit">
                                <div class="row col-md-12">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="empRuc" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> RUC</label>
                                            <input  v-model="newEmpresa.empRuc" type="text" class="form-control" id="empRuc" placeholder="Ingrese el RUC empresarial" />
                                            <!--validaciones-->
                                            <div v-html="formValidacion.empRuc" class="text-danger"></div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="empRasonSocial" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Razón Social</label>
                                            <input  v-model="newEmpresa.empRasonSocial" type="text" class="form-control" id="empRasonSocial" placeholder="Ingrese la razon social" />
                                            <!--validaciones-->
                                            <div v-html="formValidacion.empRasonSocial" class="text-danger"></div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="empRepLegal" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Representante Legal</label>
                                            <input  v-model="newEmpresa.empRepLegal" type="text" class="form-control" id="empRepLegal" placeholder="Ingrese la razon social" />
                                            <!--validaciones-->
                                            <div v-html="formValidacion.empRepLegal" class="text-danger"></div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="empNombreComercial" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Nombre Comercial</label>
                                            <input  v-model="newEmpresa.empNombreComercial" type="text" class="form-control" id="empNombreComercial" placeholder="Ingrese El nombre comercial" />
                                            <!--validaciones-->
                                            <div v-html="formValidacion.empNombreComercial" class="text-danger"></div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="empCiudad" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Ciudad / Provincia</label>
                                            <input  v-model="newEmpresa.empCiudad" type="text" class="form-control" id="empCiudad" placeholder="Ingrese una ciudad" />
                                            <!--validaciones-->
                                            <div v-html="formValidacion.empCiudad" class="text-danger"></div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="empDireccion" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Dirección</label>
                                            <textarea  v-model="newEmpresa.empDireccion" type="text" class="form-control" id="empDireccion" placeholder="Ingrese una dirección"></textarea>
                                            <!--validaciones-->
                                            <div v-html="formValidacion.empDireccion" class="text-danger"></div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="empMision" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Misión</label>
                                            <textarea  v-model="newEmpresa.empMision" type="text" class="form-control" id="empMision" placeholder="Ingrese la misión"></textarea>
                                            <!--validaciones-->
                                            <div v-html="formValidacion.empMision" class="text-danger"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="empVision" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Visión</label>
                                            <textarea  v-model="newEmpresa.empVision" type="text" class="form-control" id="empVision" placeholder="Ingrese la visión"></textarea>
                                            <!--validaciones-->
                                            <div v-html="formValidacion.empVision" class="text-danger"></div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="empObjetivos" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Objetivos</label>
                                            <textarea  v-model="newEmpresa.empObjetivos" type="text" class="form-control" id="empObjetivos" placeholder="Ingrese los objetivos"></textarea>
                                            <!--validaciones-->
                                            <div v-html="formValidacion.empObjetivos" class="text-danger"></div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="empTelefono" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Teléfono</label>
                                            <input  v-model="newEmpresa.empTelefono" type="number" class="form-control" id="empTelefono" placeholder="Ingrese el # de teléfono" />
                                            <!--validaciones-->
                                            <div v-html="formValidacion.empTelefono" class="text-danger"></div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="empCelular" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Celular</label>
                                            <input  v-model="newEmpresa.empCelular" type="number" class="form-control" id="empCelular" placeholder="Ingrese el # de celular" />
                                            <!--validaciones-->
                                            <div v-html="formValidacion.empCelular" class="text-danger"></div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="empEmail" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Email</label>
                                            <input  v-model="newEmpresa.empEmail" type="email" class="form-control" id="empEmail" placeholder="Ingrese el email" />
                                            <!--validaciones-->
                                            <div v-html="formValidacion.empEmail" class="text-danger"></div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="empFechaCreacion" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Fecha de Creación</label>
                                            <input  v-model="newEmpresa.empFechaCreacion" type="date" class="form-control" id="empFechaCreacion" placeholder="Ingrese la fecha de creación" />
                                            <!--validaciones-->
                                            <div v-html="formValidacion.empFechaCreacion" class="text-danger"></div>
                                        </div>


                                    </div>
                                </div>




                            </div>
                        </div>
                        <div class="modal-footer">
                            <button  class="btn btn-primary" @click="saveUpdateEmpresa()">
                                <span v-if="estadoSave"><i class="fas fa-save"></i> Crear</span>
                                <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                            </button>
                            <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-stop"></i> Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--CLOSE MODAL CREATE EMPRESA-->

        </div>
    </div>
</div>

<script type="text/javascript" >

<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';

    var v = new Vue({
        el: '#app',
        data: {

            url: siteUrl,
            //PERMISOS
            admin: admin,

            //TODO: VARIABLES
            idEdit: '',
            estadoSave: true,

            //TODO: V-MODELS
            newEmpresa: {
                empRuc: '',
                empRasonSocial: '',
                empRepLegal: '',
                empNombreComercial: '',
                empCiudad: '',
                empDireccion: '',
                empMision: '',
                empVision: '',
                empObjetivos: '',
                empTelefono: '',
                empCelular: '',
                empEmail: '',
                empFechaCreacion: ''

            },

            //TODO: LISTAS
            listaEmpresa: [],
            formValidacion: []
        },
        created() {
            this.getEmpresa();
        },
        methods: {

            async getEmpresa() {
                try {
                    let response = await axios.get(this.url + '/admin/enterprice/getEmpresa');

                    if (response.data) {
                        v.listaEmpresa = response.data;
                    } else {
                        sweet_msg_dialog('warning', 'No se ha encontrado ninguna empresa registrada');
                    }
                    dataTable('#tblEmpresa', 'Datos de la Empresa');

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                }

            },

            loadEmpresa(emp) {
                v.newEmpresa = {
                    empRuc: emp.epr_ruc,
                    empRasonSocial: emp.epr_razon_social,
                    empRepLegal: emp.epr_rep_legal,
                    empNombreComercial: emp.epr_nombre_comercial,
                    empCiudad: emp.epr_ciudad,
                    empDireccion: emp.epr_direccion,
                    empMision: emp.epr_mision,
                    empVision: emp.epr_vision,
                    empObjetivos: emp.epr_objetivos,
                    empTelefono: emp.epr_telefono,
                    empCelular: emp.epr_celular,
                    empEmail: emp.epr_email,
                    empFechaCreacion: emp.epr_fecha_creacion

                };
                v.idEdit = emp.id;
                v.nameAux = emp.epr_ruc;

            },
            async saveUpdateEmpresa() {
                let datos = v.formData(v.newEmpresa);
                let url = this.url + '/admin/enterprice/saveEmpresa';

                if (v.idEdit != '') {
                    datos.append('idEmpresa', v.idEdit);
                    datos.append('nameAux', v.nameAux);//TODO: ESTA VARIABLE SE LA USA PARA VALIDAR QUE NO AYA OTRA REGISTRO CON EL MISMO NOMBRE
                    url = this.url + '/admin/enterprice/updateEmpresa';
                }

                try {
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        v.clear();
                        v.getEmpresa();
                        $('#modalmpresa').modal('hide');
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
                v.newEmpresa = {
                    empRuc: '',
                    empRasonSocial: '',
                    empRepLegal: '',
                    empNombreComercial: '',
                    empCiudad: '',
                    empDireccion: '',
                    empMision: '',
                    empVision: '',
                    empObjetivos: '',
                    empTelefono: '',
                    empCelular: '',
                    empEmail: '',
                    empFechaCreacion: ''
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
            }

        }
    });

</script>
