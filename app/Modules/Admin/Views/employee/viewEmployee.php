<?php
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of viewEmployee
 * @author Cristian R. Paz
 * @Date 28 sep. 2023
 * @Time 12:45:10
 */
?>

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-user-cog"></i> Empleados</h5>
        </div>
        <div class="card-body">
            <div class="col-md-12" style="height: 700px ;overflow-x: auto">
                <table id="tblEmpleados" class="table table-striped nowrap display" style="width: 100%">
                    <thead class="bg-system text-white">
                        <tr>
                            <th>ID</th>
                            <th>Foto</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Cedula</th>
                            <th>usuario</th>
                            <th>Correo</th>
                            <th>Telefono</th>
                            <th>Cargo</th>
                            <th>Departamento</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="lemp of listaEmpleados">
                            <td>{{zfill(lemp.id)}}</td>
                            <td>
                                <button @click='setFoto(lemp.emp_foto)' class="btn btn-info btn-sm" data-bs-target="#foto" data-bs-toggle="modal"><i class="fas fa-image"></i></button>
                            </td>
                            <td>{{lemp.emp_nombre}}</td>
                            <td>{{lemp.emp_apellido}}</td>
                            <td>{{lemp.emp_dni}}</td>
                            <td>{{lemp.emp_username}}</td>
                            <td>{{lemp.emp_email}}</td>
                            <td>{{lemp.emp_celular}}</td>
                            <td>{{lemp.carg_nombre}}</td>
                            <td>{{lemp.dep_nombre}}</td>

                            <td v-if="lemp.is_root == 1">SUPERADMIN</td>
                            <td v-else>{{lemp.rol_nombre}}</td>

                            <td v-if="lemp.emp_estado == 1 "><span class="badge bg-success"><i class="fas fa-check-double"></i>  Activo</span></td>
                            <td v-else><span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span></td>
                            <td  class="text-center" style="width: 5px">
                                <template v-if="editEmployee || admin">
                                    <button @click="loadEmpleado(lemp), estadoSave=false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEmpleado" ><i class="fas fa-edit"></i></button>
                                    <button @click="setIdPaswordReset(lemp.id)" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalPasswordReset" ><i class="fas fa-lock-alt"></i></button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!--MODAL FOTO-->
        <div id="foto" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class=""><i class="fad fa-image"></i> Foto</h5>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <img :src="this.url+'/uploads/img/employee/'+fotoPerfil " alt='foto empleado'>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!--CIERRA MODAL FOTO-->

        <!--MODAL PASSWORD RESET-->
        <div id="modalPasswordReset" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class=""><i class="fas fa-key-skeleton"></i> Restablecer Contraseña</h5>
                        <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
                    </div>
                    <div class="modal-body">
                        <div class="text-left">
                            <div class="mb-3">
                                <label for="newPassword" class="col-form-label col-form-label-sm"><i class="fal fa-key-skeleton"></i> Nueva Contraseña</label>
                                <input  v-model="newPassword" type="password" class="form-control" id="newPassword" placeholder="Ingrese una contraseña" />
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="col-form-label col-form-label-sm"><i class="fal fa-key"></i> Confirme la nueva contraseña</label>
                                <input  v-model="confirmPassword" type="password" class="form-control" id="confirmPassword" placeholder="Confirme la contraseña" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" @click="restablecerContraseña()"><i class="fas fa-lock-open"></i> Restablecer</button>
                    </div>
                </div>
            </div>
        </div>
        <!--CIERRA MODAL PASSWORD RESET-->

        <!--MODAL EMPLEADO-->
        <div id="modalEmpleado" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-if="estadoSave" class=""><i class="fas fa-user-plus"></i> Crear Empleado</h5>
                        <h5 v-else class=""><i class="fas fa-user-plus"></i> Actualizar Empleado</h5>
                        <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
                    </div>
                    <div class="modal-body">
                        <div class="col-md-12 d-flex">
                            <div class="col-md-6">
                                <input type="hidden" v-model="idEdit">
                                <div class="mb-3">
                                    <label for="dni" class="col-form-label col-form-label-sm"><i class="fal fa-dna"></i> CI/RUC</label>
                                    <input v-model="newEmpleado.dni"  type="number" class="form-control" id="dni" placeholder="Ingrese el DNI" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.dni" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="nombres" class="col-form-label col-form-label-sm"><i class="fal fa-user"></i> Nombres</label>
                                    <input  v-model="newEmpleado.nombres" type="text" class="form-control" id="nombres" placeholder="Ingrese los nombres" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.nombres" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="apellidos" class="col-form-label col-form-label-sm"><i class="fal fa-user"></i> Apellidos</label>
                                    <input  v-model="newEmpleado.apellidos" vtype="text" class="form-control" id="apellidos" placeholder="Ingrese los apellidos" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.apellidos" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="col-form-label col-form-label-sm"><i class="fal fa-mailbox"></i> Email</label>
                                    <input  v-model="newEmpleado.email" type="email" class="form-control" id="email" placeholder="Ingrese el email" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.email" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="telefono" class="col-form-label col-form-label-sm"><i class="fal fa-phone"></i> Teléfono</label>
                                    <input  v-model="newEmpleado.telefono" type="number" class="form-control" id="telefono" placeholder="Ingrese en # telefono" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.telefono" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="celular" class="col-form-label col-form-label-sm"><i class="fal fa-phone-alt"></i> Celular</label>
                                    <input  v-model="newEmpleado.celular" type="number" class="form-control" id="celular" placeholder="Ingrese el # celular" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.celular" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="cargo" class="col-form-label col-form-label-sm"><i class="fal fa-caravan"></i> Cargo</label>
                                    <select id="selectCargo" v-model="newEmpleado.cargo" title="Seleccione un Cargo" class="form-control border selectpicker show-tick" data-live-search="true" id="cargo">
                                        <option v-for="lc of listaCargos" v-bind:value="lc.id">{{lc.carg_nombre}}</option>
                                    </select>
                                    <!--validaciones-->
                                    <div v-html="formValidacion.cargo" class="text-danger"></div>
                                </div>


                            </div>

                            <div class="col-md-6">


                                <div class="mb-3">
                                    <label for="departamento" class="col-form-label col-form-label-sm"><i class="fal fa-caravan-alt"></i> Departamento</label>
                                    <select id="selectDep" v-model="newEmpleado.departamento" title="Seleccione un Departamento" class="form-control border selectpicker show-tick" data-live-search="true" id="departamento">
                                        <option v-for="ld of listaDepartamentos" v-bind:value="ld.id">{{ld.dep_nombre}}</option>
                                    </select>
                                    <!--validaciones-->
                                    <div v-html="formValidacion.departamento" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="user" class="col-form-label col-form-label-sm"><i class="fal fa-user"></i> Usuario</label>
                                    <input v-model="newEmpleado.usuario" type="text" class="form-control " id="user" placeholder="Ingrese el nombre de usuario" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.usuario" class="text-danger"></div>
                                </div>
                                <div class="mb-3" v-if="estadoSave">
                                    <label for="password" class="col-form-label col-form-label-sm"><i class="fal fa-key-skeleton"></i> Contraseña</label>
                                    <input  v-model="newEmpleado.password" type="password" class="form-control" id="password" placeholder="Ingrese la contraseña" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.password" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="bodega_main" class="col-form-label col-form-label-sm"><i class="fal fa-building"></i> Bodega Principal</label>
                                    <select id="selectBodMain" v-model="newEmpleado.bodegaMain" title="Seleccione una bodega principal" class="form-control border selectpicker show-tick" data-live-search="true" id="bodega_main">
                                        <option v-for="lb of listaBodegas" v-bind:value="lb.id">{{lb.bod_nombre}}</option>
                                    </select>
                                    <!--validaciones-->
                                    <div v-html="formValidacion.bodegaMain" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label class="col-form-label col-form-label-sm"><i class="fal fa-buildings"></i> Bodegas</label>
                                    <vue-multiselect 
                                        v-model="bodegas"
                                        tag-placeholder="Bodega no encontrado"
                                        placeholder="Buscar y agregar bodega"
                                        label="bod_nombre"
                                        track-by="id"
                                        :multiple="true"
                                        :searchable="true"
                                        :options-limit="10"
                                        :show-no-results="true"
                                        :options="listaBodegas"                                    
                                        >
                                    </vue-multiselect>
                                    <!--validaciones-->
                                    <div v-html="formValidacion.bodegas" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="rol" class="col-form-label col-form-label-sm"><i class="fal fa-user-tie"></i> Rol</label>
                                    <select id="selectRol" v-model="newEmpleado.rol" title="Seleccione un rol" class="form-control border selectpicker show-tick" data-live-search="true" id="rol">
                                        <option v-for="lr of listaRoles" v-bind:value="lr.id">{{lr.rol_nombre}}</option>
                                    </select>
                                    <!--validaciones-->
                                    <div v-html="formValidacion.rol" class="text-danger"></div>
                                </div>

                                <div class="mb-3">                                 
                                    <label for="rol" class="col-form-label col-form-label-sm"><i class="fal fa-user-alt-slash"></i> Estado</label>
                                    <select v-model="newEmpleado.estado" class="form-control border" id="rol">
                                        <option value="1">ACTIVO</option>
                                        <option value="0"> INACTIVO</option>
                                    </select>

                                </div>



                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button v-if="estadoSave" class="btn btn-primary" @click="saveEmpleado()"><i class="fas fa-save"></i> Crear</button>
                        <button v-else class="btn btn-primary" @click="updateEmpleado()"><i class="fas fa-refresh"></i> Actualizar</button>
                        <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-stop"></i> Cancelar</button>
                    </div>

                </div>
            </div>
        </div>
        <!--CIERRA MODAL EMPLEADO-->
    </div>
</div>
<script type="text/javascript">

    //TODO: SECCION PERMISOS
<?php $editEmployee = $user->validatePermisos('edit_employee', $user->id) ?>
    var editEmployee = '<?= $editEmployee ?>';
<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';

    var listaBodegas =<?php echo json_encode($listaBodegas) ?>;
    var listaCargos =<?php echo json_encode($listaCargos) ?>;
    var listaDepartamentos =<?php echo json_encode($listaDepartamentos) ?>;
    var listaRoles =<?php echo json_encode($listaRoles) ?>;

    var v = new Vue({
        el: '#app',
        components: {
            'vue-multiselect': window.VueMultiselect.default
        },
        data: {
            //TODO: PERMISOS
            editEmployee: editEmployee,
            admin: admin,
            //TODO: variables
            fotoPerfil: 'user.png',
            url: siteUrl,
            estadoSave: true,
            dniAux: '',

            //TODO: V-MODELS
            bodegas: '',
            newEmpleado: {
                dni: '',
                nombres: '',
                apellidos: '',
                email: '',
                telefono: '',
                celular: '',
                cargo: '',
                departamento: '',
                usuario: '',
                password: '',
                bodegaMain: '',
                rol: '',
                estado: '1'
            },
            idEdit: '',

            //TODO: LISTAS
            listaEmpleados: [],
            listaBodegas: listaBodegas,
            listaCargos: listaCargos,
            listaDepartamentos: listaDepartamentos,
            listaRoles: listaRoles,

            //TODO: VALIDACIONES
            formValidacion: [],

            //TODO: RESET PASSWORD
            confirmPassword: '',
            newPassword: '',
            idEmpPR: ''

        },
        created() {
            this.getEmpleados();
        },
        methods: {

            setIdPaswordReset(idEmp) {
                v.idEmpPR = idEmp;
            },

            async restablecerContraseña() {
                if (!v.newPassword) {
                    sweet_msg_toast('warning', 'El campo contraseña no puede estar vacío');
                    return false;
                }
                if (!v.confirmPassword) {
                    sweet_msg_toast('warning', 'El campo confirmar contraseña no puede estar vacío');
                    return false;
                }
                if (v.newPassword != v.confirmPassword) {
                    sweet_msg_toast('warning', 'Las contraseñas no coinciden');
                    return false;
                }
                let datos = {
                    idEmpPR: v.idEmpPR,
                    newPassword: v.newPassword,
                    confirmPassword: v.confirmPassword,
                }
                try {
                    let response = await axios.post(this.url + '/admin/employee/resetPassword', datos);
                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                    } else {
                        sweet_msg_dialog('error', 'Ha ocurrido un error al restablecer la contraseña');
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                }

            },

            async getEmpleados() {
                try {
                    let response = await axios.get(this.url + '/admin/employee/getEmpleados');
                    if (response.data) {
                        v.listaEmpleados = response.data;
                    } else {
                        sweet_msg_dialog('warning', 'No se han encontrado empleados registrados');
                    }
                    if (v.admin) {
                        dataTableModalBtn('#tblEmpleados', 'Reporte de empleados', '#modalEmpleado', 'CREAR EMPLEADO', 'fas fa-user-plus');

                    } else {
                        dataTable('#tblEmpleados', 'Reporte de empleados');
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                }

            },
            async saveEmpleado() {

                try {
                    let datos = v.formData(v.newEmpleado);

                    if (v.bodegas) {
                        let bodegas_id = v.bodegas.map(data => data.id);
                        datos.append('bodegas', bodegas_id);
                    } else {
                        datos.append('bodegas', "");
                    }

                    let response = await axios.post(this.url + '/admin/employee/saveEmpleado', datos);

                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        v.clear();
                        v.getEmpleados();

                    } else if (response.data.status === 'existe') {

                        sweet_msg_dialog('warning', response.data.msg);

                    } else if (response.data.status === 'vacio') {

                        v.formValidacion = response.data.msg;

                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                }

            },
            async updateEmpleado() {

                try {
                    let datos = v.formData(v.newEmpleado);
                    datos.append('idEmp', v.idEdit);
                    datos.append('dniAux', v.dniAux);
                    if (v.bodegas) {
                        let bodegas_id = v.bodegas.map(data => data.id);
                        datos.append('bodegas', bodegas_id);
                    } else {
                        datos.append('bodegas', "");
                    }


                    let response = await axios.post(this.url + '/admin/employee/updateEmpleado', datos);

                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        v.clear();
                        $('#modalEmpleado').modal('hide');
                        $('.modal-backdrop').remove();
                        v.getEmpleados();

                    } else if (response.data.status === 'existe') {

                        sweet_msg_dialog('warning', response.data.msg);

                    } else if (response.data.status === 'vacio') {

                        v.formValidacion = response.data.msg;

                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                }

            },
            async loadEmpleado(emp) {
                v.newEmpleado = {
                    dni: emp.emp_dni,
                    nombres: emp.emp_nombre,
                    apellidos: emp.emp_apellido,
                    email: emp.emp_email,
                    telefono: emp.emp_telefono,
                    celular: emp.emp_celular,
                    cargo: emp.fk_cargo,
                    departamento: emp.fk_departamento,
                    usuario: emp.emp_username,
                    bodegaMain: emp.fk_bodega_main,
                    rol: emp.fk_rol,
                    estado: emp.emp_estado
                };
                v.dniAux = emp.emp_dni;
                v.idEdit = emp.id;
                $('#selectBodMain').selectpicker('val', emp.fk_bodega_main);
                $('#selectCargo').selectpicker('val', emp.fk_cargo);
                $('#selectDep').selectpicker('val', emp.fk_departamento);
                $('#selectRol').selectpicker('val', emp.fk_rol);

                let datos = {idEmp: emp.id}
                let response = await axios.post(this.url + '/admin/employee/getBodegas', datos);
                if (response.data) {
                    v.bodegas = response.data;
                }
            },
            clear() {
                v.newEmpleado = {
                    dni: '',
                    nombres: '',
                    apellidos: '',
                    email: '',
                    telefono: '',
                    celular: '',
                    cargo: '',
                    departamento: '',
                    usuario: '',
                    password: '',
                    bodegaMain: '',
                    rol: '',
                    estado: '1'
                };
                v.bodegas = '';
                v.formValidacion = [];
                $('#selectBodMain').selectpicker('val', '');
                $('#selectCargo').selectpicker('val', '');
                $('#selectDep').selectpicker('val', '');
                $('#selectRol').selectpicker('val', '');
                //TODO: PARTE CLAVE PARA IDENTIFICAR SI INSERTA O ACTUALIZA
                v.estadoSave = true;
                v.idEdit = '';
            },

            formData(obj) {
                var formData = new FormData();
                for (var key in obj) {
                    formData.append(key, obj[key]);
                }
                return formData;
            },
            setFoto(foto) {
                if (foto) {
                    v.fotoPerfil = foto;
                } else {
                    v.fotoPerfil = 'user.png';
                }

            },
            zfill(val) {
                return zFill(val, 3);
            }

        }
    });

</script>