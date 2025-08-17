<!DOCTYPE html>
<!--
/**
 * Description of viewRoles
 *
/**
 * @author CRISTIAN PAZ
 * @date 27 dic. 2023
 * @time 13:19:03
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-user-cog"></i> Administrar Roles</h5>
        </div>
        <div class="card-body">
            <table id="tblRoles" class="table table-striped nowrap display" style="width: 100%">
                <thead class="bg-system text-white">
                    <tr>
                        <td>ID</td>
                        <td>ROL</td>
                        <td>FECHA CREACIÓN</td>
                        <td>ESTADO</td>
                        <td>ACCIONES</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="lr of listaRoles">
                        <td>{{zfill(lr.id)}}</td>
                        <td>{{lr.rol_nombre}}</td>
                        <td>{{lr.rol_fecha_creacion}}</td>

                        <td v-if="lr.rol_estado || admin "><span class="badge bg-success"><i class="fas fa-check-double"></i>  Activo</span></td>
                        <td v-else><span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span></td>

                        <td>
                            <template v-if="editRol">
                                <button @click="loadRol(lr), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalRol"><i class="fas fa-edit"></i> </button>
                                <button @click="loadPermisosRol(lr.id)" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalAsignacion"><i class="fas fa-cogs"></i> </button>
                            </template>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!--MODAL CREATE ROL-->
            <div id="modalRol" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 v-if="estadoSave" class=""><i class="fas fa-user-cog"></i> Crear Rol</h5>
                            <h5 v-else class=""><i class="fas fa-user-cog"></i> Actualizar Rol</h5>
                            <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
                        </div>
                        <div class="modal-body">
                            <div class="text-left">
                                <input type="hidden" v-model="idEdit">
                                <div class="mb-3">
                                    <label for="rol" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Nombre del Rol</label>
                                    <input  v-model="newRol.nombreRol" type="text" class="form-control" id="rol" placeholder="Ingrese un nombre" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.nombreRol" class="text-danger"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="rol" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Estado</label>
                                    <select v-model="newRol.estado" class="form-control border" id="rol">
                                        <option value="1">ACTIVO</option>
                                        <option value="0"> INACTIVO</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button v-if="estadoSave" class="btn btn-primary" @click="saveRol()"><i class="fas fa-save"></i> Crear</button>
                            <button v-else class="btn btn-primary" @click="updateRol()"><i class="fas fa-refresh"></i> Actualizar</button>
                            <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-stop"></i> Cancelar</button>                        </div>
                    </div>
                </div>
            </div>
            <!--CLOSE MODAL CREATE ROL-->

            <!--MODAL ASIGNAR PERMISOS-->
            <div id="modalAsignacion" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
                <div class="modal-dialog" style="max-width: 60%">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class=""><i class="fas fa-clipboard-list-check"></i> Asignar Permisos</h5>
                            <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
                        </div>
                        <div class="modal-body">
                            <?php echo view('\Modules\Admin\Views\roles\viewAsignacion') ?>
                        </div>
                        <div class="modal-footer">
                            <button  class="btn btn-primary" @click="ASIGNARPERMISOS()"><i class="fas fa-file-check"></i> Asignar</button>
                            <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-stop"></i> Cancelar</button>                        </div>
                    </div>
                </div>
            </div>
            <!--CLOSE MODAL ASIGNAR PERMISOS-->

        </div>
    </div>
</div>
<!--<div id="loading-screen-upd" style="display: none">
    <img src="<?php // echo base_url() ?>/uploads/img/system/spinning.svg" alt="loading"/>
</div>-->

<script type="text/javascript">

<?php $editRol = $user->validatePermisos('edit_rol', $user->id) ?>
    var editRol = '<?= $editRol ?>';
<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';

    var listaAllModulos =<?php echo json_encode($listaAllModulos) ?>;
    var listaAllSubModulos =<?php echo json_encode($listaAllSubModulos) ?>;
    var listaAllAcciones =<?php echo json_encode($listaAllAcciones) ?>;

    var v = new Vue({
        el: '#app',
        data: {
            //TODO: PERMISOS
            editRol: editRol,
            admin: admin,

            //TODO: VARIABLES
            url: siteUrl,
            estadoSave: true,
            nameAux: '',
            idRol: '',

            //TODO: LISTAS
            listaRoles: [],
            listaAllModulos: listaAllModulos,
            listaAllSubModulos: listaAllSubModulos,
            listaAllAcciones: listaAllAcciones,
            checkedModulos: [],
            checkedAcciones: [],

            //TODO: V-MODELS
            newRol: {
                nombreRol: '',
                estado: '1'
            },
            idEdit: '',
            selectAll: false,

            //TODO: VALIDACIONES
            formValidacion: [],

        },
        created() {
            this.getRoles();
        },
        methods: {
            async   getRoles() {
                try {
                    let response = await axios.get(this.url + '/admin/roles/getRoles');
                    if (response.data) {
                        v.listaRoles = response.data;
                    } else {
                        sweet_msg_dialog('warning', 'no se han ncontrado roles registrados');
                    }
                    if (v.admin) {
                        dataTableModalBtn('#tblRoles', 'Roles', '#modalRol', 'NUEVO ROL', 'fas fa-plus-circle');
                    } else {
                        dataTable('#tblRoles', 'Roles');
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                }

            },
            loadRol(rol) {
                v.newRol = {
                    nombreRol: rol.rol_nombre,
                    estado: rol.rol_estado
                };
                v.nameAux = rol.rol_nombre;
                v.idEdit = rol.id;
            },
            async saveRol() {

                let datos = v.formData(v.newRol);

                try {
                    let response = await axios.post(this.url + '/admin/roles/saveRol', datos);
                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        v.clear_();
                        v.getRoles();

                    } else if (response.data.status === 'existe') {

                        sweet_msg_dialog('warning', response.data.msg);

                    } else if (response.data.status === 'vacio') {

                        v.formValidacion = response.data.msg;

                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                }

            },
            async updateRol() {
                try {
                    let datos = v.formData(v.newRol);
                    datos.append('idRol', v.idEdit);
                    datos.append('nameAux', v.nameAux);

                    let response = await axios.post(this.url + '/admin/roles/updateRol', datos);

                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        v.clear_();
                        $('#modalRol').modal('hide');
                        $('.modal-backdrop').remove();
                        v.getRoles();

                    } else if (response.data.status === 'existe') {

                        sweet_msg_dialog('warning', response.data.msg);

                    } else if (response.data.status === 'vacio') {

                        v.formValidacion = response.data.msg;

                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                }

            },
            async loadPermisosRol(idRol) {
                v.idRol = idRol;
                let datos = {idRol: idRol};
                let response = await axios.post(this.url + '/admin/roles/loadPermisosRol', datos);
                if (response.data.listaModulos) {
                    response.data.listaModulos.map((data) => {
                        v.checkedModulos.push(data.fk_modulo);
                        document.getElementsByClassName(data.fk_modulo)[0].style.background = "#EAF2F8";
                        document.getElementById('checkmod' + data.fk_modulo).checked = true;

                    });
                }
                if (response.data.listaAcciones) {
                    response.data.listaAcciones.map((data) => {
                        v.checkedAcciones.push(data.fk_accion);
                        document.getElementsByClassName('_' + data.fk_accion)[0].style.background = "#EAF2F8";
                        document.getElementById('checkacc' + data.fk_accion).checked = true;
                    });
                }
            },
            selectAllPermisos() {
                v.selectAllPermisosMod();
                v.selectAllPermisosAcc();
            },

            selectAllPermisosMod() {
                v.checkedModulos = [];
                if (!v.selectAll) {//Preguntamos si el check de la tabla arranque desselecionado                 
                    for (let i in v.listaAllModulos) {
                        v.checkedModulos.push(v.listaAllModulos[i].id);
                        document.getElementsByClassName(v.listaAllModulos[i].id)[0].style.background = "#EAF2F8";
                        document.getElementById('checkmod' + v.listaAllModulos[i].id).checked = true;
                    }
                } else {
                    for (let i in v.listaAllModulos) {
                        v.checkedModulos = [];
                        document.getElementsByClassName(v.listaAllModulos[i].id)[0].style.background = "transparent";
                        document.getElementById('checkmod' + v.listaAllModulos[i].id).checked = false;

                    }
                }
                v.selectAllPermisosSubMod();
            },
            selectAllPermisosSubMod() {
                if (!v.selectAll) {//Preguntamos si el check de la tabla arranque desselecionado                 
                    for (let i in v.listaAllSubModulos) {
                        v.checkedModulos.push(v.listaAllSubModulos[i].id);
                        document.getElementsByClassName(v.listaAllSubModulos[i].id)[0].style.background = "#EAF2F8";
                        document.getElementById('checkmod' + v.listaAllSubModulos[i].id).checked = true;
                    }
                } else {
                    for (let i in v.listaAllSubModulos) {
                        v.checkedModulos = [];
                        document.getElementsByClassName(v.listaAllSubModulos[i].id)[0].style.background = "transparent";
                        document.getElementById('checkmod' + v.listaAllSubModulos[i].id).checked = false;

                    }
                }
            },
            selectAllPermisosAcc() {
                v.checkedAcciones = [];
                if (!v.selectAll) {//Preguntamos si el check de la tabla arranque desselecionado                 
                    for (let i in v.listaAllAcciones) {
                        v.checkedAcciones.push(v.listaAllAcciones[i].id);
                        document.getElementsByClassName('_' + v.listaAllAcciones[i].id)[0].style.background = "#EAF2F8";
                        document.getElementById('checkacc' + v.listaAllAcciones[i].id).checked = true;
                    }
                } else {
                    for (let i in v.listaAllAcciones) {
                        v.checkedAcciones = [];
                        document.getElementsByClassName('_' + v.listaAllAcciones[i].id)[0].style.background = "transparent";
                        document.getElementById('checkacc' + v.listaAllAcciones[i].id).checked = false;

                    }
                }
            },

            seletedRowModSubMod(codigo) {

                let existeModulo = v.checkedModulos.includes(codigo);//Pregunto si el modulo esta en el arreglo si se cumple devuelvo TRUE caso contrario si no esta debuelvo falso

                var newArray;

                if (existeModulo) {//Si la condicion es TRUE entro

                    newArray = v.checkedModulos.filter((item) => item !== codigo);
                    document.getElementsByClassName(codigo)[0].style.background = "transparent";
                    v.checkedModulos = newArray; //Quito el modulo al arreglo checkedModulos
                    document.getElementById('checkmod' + codigo).checked = false;
                } else {
                    document.getElementsByClassName(codigo)[0].style.background = "#EAF2F8";
                    document.getElementById('checkmod' + codigo).checked = true;
                    v.checkedModulos.push(codigo); //Agrego el modulo al arreglo checkedModulos
                }

            },
            seletedRowAcc(codigo) {
                let existeAccion = v.checkedAcciones.includes(codigo);//Pregunto si la accion esta en el arreglo si se cumple devuelvo TRUE caso contrario si no esta debuelvo falso

                var newArray;

                if (existeAccion) {//Si la condicion es TRUE entro

                    newArray = v.checkedAcciones.filter((item) => item !== codigo);
                    document.getElementsByClassName('_' + codigo)[0].style.background = "transparent";
                    v.checkedAcciones = newArray; //Quito la accion del arreglo acciones
                    document.getElementById('checkacc' + codigo).checked = false;
                } else {
                    document.getElementsByClassName('_' + codigo)[0].style.background = "#EAF2F8";
                    document.getElementById('checkacc' + codigo).checked = true;
                    v.checkedAcciones.push(codigo); //Agrego el modulo al arreglo checkedModulos
                }

            },

            async ASIGNARPERMISOS() {
                try {
                    let datos = {
                        rolId: v.idRol,
                        listaModulos: v.checkedModulos,
                        listaAcciones: v.checkedAcciones
                    }
                    let response = await axios.post(this.url + '/admin/roles/aplicarPermisos', datos);
                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                    } else {
                        sweet_msg_dialog('warning', response.data.msg);
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                }

            },
            clear() {
                v.newRol = {
                    nombreRol: '',
                    estado: '1'
                };
                v.formValidacion = [];
                v.estadoSave = true;
                v.idEdit = '';
                v.checkedModulos = [];
                v.checkedAcciones = [];

                for (let i in v.listaAllModulos) {
                    v.checkedModulos = [];
                    document.getElementsByClassName(v.listaAllModulos[i].id)[0].style.background = "transparent";
                    document.getElementById('checkmod' + v.listaAllModulos[i].id).checked = false;

                }
                for (let i in v.listaAllSubModulos) {
                    v.checkedModulos = [];
                    document.getElementsByClassName(v.listaAllSubModulos[i].id)[0].style.background = "transparent";
                    document.getElementById('checkmod' + v.listaAllSubModulos[i].id).checked = false;

                }
                for (let i in v.listaAllAcciones) {
                    v.checkedAcciones = [];
                    document.getElementsByClassName('_' + v.listaAllAcciones[i].id)[0].style.background = "transparent";
                    document.getElementById('checkacc' + v.listaAllAcciones[i].id).checked = false;

                }
            },
            clear_() {
                v.newRol = {
                    nombreRol: '',
                    estado: '1'
                };
                v.formValidacion = [];
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
            zfill(num) {

                return  zFill(num, 3);
            }
        }
    });

</script>