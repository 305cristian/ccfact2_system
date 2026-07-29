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
                            <button v-if="estadoSave" class="btn btn-primary" @click="saveRol()" :disabled="loadingRol">
                                <span v-if="loadingRol"><i class="loading-spin"></i> Creando...</span>
                                <span v-else><i class="fas fa-save"></i> Crear</span>
                            </button>
                            <button v-else class="btn btn-primary" @click="updateRol()" :disabled="loadingRol">
                                <span v-if="loadingRol"><i class="loading-spin"></i> Actualizando...</span>
                                <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                            </button>
                            <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal" :disabled="loadingRol"><i class="fas fa-stop"></i> Cancelar</button>                        </div>
                    </div>
                </div>
            </div>
            <!--CLOSE MODAL CREATE ROL-->

            <!--MODAL ASIGNAR PERMISOS-->
            <div id="modalAsignacion" ref="modalAsignacion" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-scrollable" style="max-width: 40%">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class=""><i class="fas fa-clipboard-list-check"></i> Asignar Permisos</h5>
                            <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
                        </div>
                        <div class="modal-body">
                            <?php echo view('\Modules\Admin\Views\roles\viewAsignacion') ?>
                        </div>
                        <div class="modal-footer">
                            <button  class="btn btn-primary" @click="ASIGNARPERMISOS()" :disabled="loading">
                                <span v-if="loading"><i class="fas fa-spinner fa-spin"></i> Asignando...</span>
                                <span v-else><i class="fas fa-file-check"></i> Asignar</span>
                            </button>
                            <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal" :disabled="loading"><i class="fas fa-stop"></i> Cancelar</button>                        </div>
                    </div>
                </div>
            </div>
            <!--CLOSE MODAL ASIGNAR PERMISOS-->

        </div>
    </div>
</div>

<script type="text/javascript">

<?php $editRol = $user->validatePermisos('edit_rol', $user->id) ?>
    var editRol = '<?= $editRol ?>';
<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';

    var listaAllModulos =<?php echo json_encode($listaAllModulos) ?>;
    var listaAllSubModulos =<?php echo json_encode($listaAllSubModulos) ?>;
    var listaAllAcciones =<?php echo json_encode($listaAllAcciones) ?>;

    if (window.appRoles) {
        window.appRoles.unmount();
    }

    window.appRoles = Vue.createApp({

        data() {
            return {
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
                modulosAbiertos: [],
                submodulosAbiertos: [],

                //TODO: V-MODELS
                newRol: {
                    nombreRol: '',
                    estado: '1'
                },
                idEdit: '',
                selectAll: false,

                //TODO: VALIDACIONES
                formValidacion: [],

                modalAsignacion: null,
                loadingRol:false,
                loading:false

            };
        },

        mounted() {
            this.getRoles();
            this.modalAsignacion = new bootstrap.Modal(this.$refs.modalAsignacion);
        },
        methods: {
            toggleModulo(id) {
                const idx = this.modulosAbiertos.indexOf(id);
                if (idx === -1)
                    this.modulosAbiertos.push(id);
                else
                    this.modulosAbiertos.splice(idx, 1);
            },
            toggleSubmodulo(id) {
                const idx = this.submodulosAbiertos.indexOf(id);
                if (idx === -1)
                    this.submodulosAbiertos.push(id);
                else
                    this.submodulosAbiertos.splice(idx, 1);
            },
            accionesPorModulo(idMod) {
                const subIds = this.listaAllSubModulos
                        .filter(s => s.md_padre == idMod)
                        .map(s => s.id);
                return this.listaAllAcciones.filter(
                        a => a.fk_submodulo == idMod || subIds.includes(a.fk_submodulo)
                );
            },
            async   getRoles() {
                try {
                    let response = await axios.get(this.url + '/admin/roles/getRoles');
                    if (response.data) {
                        this.listaRoles = response.data;
                    } else {
                        sweet_msg_dialog('warning', 'no se han ncontrado roles registrados');
                    }
                    if (this.admin) {
                        dataTableModalBtn('#tblRoles', 'Roles', '#modalRol', 'NUEVO ROL', 'fas fa-plus-circle');
                    } else {
                        dataTable('#tblRoles', 'Roles');
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                }

            },
            loadRol(rol) {
                this.newRol = {
                    nombreRol: rol.rol_nombre,
                    estado: rol.rol_estado
                };
                this.nameAux = rol.rol_nombre;
                this.idEdit = rol.id;
            },
            async saveRol() {

                let datos = this.formData(this.newRol);

                try {
                    this.loadingRol = true;
                    let response = await axios.post(this.url + '/admin/roles/saveRol', datos);
                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        this.clear_();
                        this.getRoles();

                    } else if (response.data.status === 'existe') {

                        sweet_msg_dialog('warning', response.data.msg);

                    } else if (response.data.status === 'vacio') {

                        this.formValidacion = response.data.msg;

                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                } finally {
                    this.loadingRol = false;
                }

            },
            async updateRol() {
                try {
                    this.loadingRol = true;
                    let datos = this.formData(this.newRol);
                    datos.append('idRol', this.idEdit);
                    datos.append('nameAux', this.nameAux);

                    let response = await axios.post(this.url + '/admin/roles/updateRol', datos);

                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        this.clear_();
                        $('#modalRol').modal('hide');
                        $('.modal-backdrop').remove();
                        this.getRoles();

                    } else if (response.data.status === 'existe') {

                        sweet_msg_dialog('warning', response.data.msg);

                    } else if (response.data.status === 'vacio') {

                        this.formValidacion = response.data.msg;

                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                } finally {
                    this.loadingRol = false;
                }

            },
            async loadPermisosRol(idRol) {
                this.idRol = idRol;
                let datos = {idRol: idRol};
                swalLoading('Cargando...', '');
                let response = await axios.post(this.url + '/admin/roles/loadPermisosRol', datos);
                if (response.data.listaModulos) {
                    response.data.listaModulos.map((data) => {
                        this.checkedModulos.push(data.fk_modulo);
                        if (!this.modulosAbiertos.includes(data.fk_modulo)) {
                            this.modulosAbiertos.push(data.fk_modulo);
                        }
                        if (!this.submodulosAbiertos.includes(data.fk_modulo)) {
                            this.submodulosAbiertos.push(data.fk_modulo);
                        }
//                        document.getElementsByClassName(data.fk_modulo)[0].style.background = "#EAF2F8";
//                        document.getElementById('checkmod' + data.fk_modulo).checked = true;

                    });
                }
                if (response.data.listaAcciones) {
                    response.data.listaAcciones.map((data) => {
                        this.checkedAcciones.push(data.fk_accion);
//                        document.getElementsByClassName('_' + data.fk_accion)[0].style.background = "#EAF2F8";
//                        document.getElementById('checkacc' + data.fk_accion).checked = true;
                    });
                }
                Swal.close();
            },
            selectAllPermisos() {
                this.selectAllPermisosMod();
                this.selectAllPermisosAcc();
            },

            selectAllPermisosMod() {
                this.checkedModulos = [];
                if (!this.selectAll) {
                    this.listaAllModulos.forEach(m => this.checkedModulos.push(m.id));
                    this.listaAllSubModulos.forEach(s => this.checkedModulos.push(s.id));
                }
//                this.checkedModulos = [];
//                if (!this.selectAll) {//Preguntamos si el check de la tabla arranque desselecionado                 
//                    for (let i in this.listaAllModulos) {
//                        this.checkedModulos.push(this.listaAllModulos[i].id);
//                        document.getElementsByClassName(this.listaAllModulos[i].id)[0].style.background = "#EAF2F8";
//                        document.getElementById('checkmod' + this.listaAllModulos[i].id).checked = true;
//                    }
//                } else {
//                    for (let i in this.listaAllModulos) {
//                        this.checkedModulos = [];
//                        document.getElementsByClassName(this.listaAllModulos[i].id)[0].style.background = "transparent";
//                        document.getElementById('checkmod' + this.listaAllModulos[i].id).checked = false;
//
//                    }
//                }
//                this.selectAllPermisosSubMod();
            },
            selectAllPermisosSubMod() {
                if (!this.selectAll) {
                    this.listaAllSubModulos.forEach(s => {
                        if (!this.checkedModulos.includes(s.id)) {
                            this.checkedModulos.push(s.id);
                        }
                    });
                } else {
                    this.checkedModulos = [];
                }
//                if (!this.selectAll) {//Preguntamos si el check de la tabla arranque desselecionado                 
//                    for (let i in this.listaAllSubModulos) {
//                        this.checkedModulos.push(this.listaAllSubModulos[i].id);
//                        document.getElementsByClassName(this.listaAllSubModulos[i].id)[0].style.background = "#EAF2F8";
//                        document.getElementById('checkmod' + this.listaAllSubModulos[i].id).checked = true;
//                    }
//                } else {
//                    for (let i in this.listaAllSubModulos) {
//                        this.checkedModulos = [];
//                        document.getElementsByClassName(this.listaAllSubModulos[i].id)[0].style.background = "transparent";
//                        document.getElementById('checkmod' + this.listaAllSubModulos[i].id).checked = false;
//
//                    }
//                }
            },
            selectAllPermisosAcc() {
                this.checkedAcciones = [];
                if (!this.selectAll) {
                    this.listaAllAcciones.forEach(a => this.checkedAcciones.push(a.id));
                }
//                this.checkedAcciones = [];
//                if (!this.selectAll) {//Preguntamos si el check de la tabla arranque desselecionado                 
//                    for (let i in this.listaAllAcciones) {
//                        this.checkedAcciones.push(this.listaAllAcciones[i].id);
//                        document.getElementsByClassName('_' + this.listaAllAcciones[i].id)[0].style.background = "#EAF2F8";
//                        document.getElementById('checkacc' + this.listaAllAcciones[i].id).checked = true;
//                    }
//                } else {
//                    for (let i in this.listaAllAcciones) {
//                        this.checkedAcciones = [];
//                        document.getElementsByClassName('_' + this.listaAllAcciones[i].id)[0].style.background = "transparent";
//                        document.getElementById('checkacc' + this.listaAllAcciones[i].id).checked = false;
//
//                    }
//                }
            },

            seletedRowModSubMod(codigo) {

                let existeModulo = this.checkedModulos.includes(codigo);//Pregunto si el modulo esta en el arreglo si se cumple devuelvo TRUE caso contrario si no esta debuelvo falso

                var newArray;

                if (existeModulo) {//Si la condicion es TRUE entro

                    newArray = this.checkedModulos.filter((item) => item !== codigo);
//                    document.getElementsByClassName(codigo)[0].style.background = "transparent";
                    this.checkedModulos = newArray; //Quito el modulo al arreglo checkedModulos
//                    document.getElementById('checkmod' + codigo).checked = false;
                } else {
//                    document.getElementsByClassName(codigo)[0].style.background = "#EAF2F8";
//                    document.getElementById('checkmod' + codigo).checked = true;
                    this.checkedModulos.push(codigo); //Agrego el modulo al arreglo checkedModulos
                }

            },
            seletedRowAcc(codigo) {
                let existeAccion = this.checkedAcciones.includes(codigo);//Pregunto si la accion esta en el arreglo si se cumple devuelvo TRUE caso contrario si no esta debuelvo falso

                var newArray;

                if (existeAccion) {//Si la condicion es TRUE entro

                    newArray = this.checkedAcciones.filter((item) => item !== codigo);
//                    document.getElementsByClassName('_' + codigo)[0].style.background = "transparent";
                    this.checkedAcciones = newArray; //Quito la accion del arreglo acciones
//                    document.getElementById('checkacc' + codigo).checked = false;
                } else {
//                    document.getElementsByClassName('_' + codigo)[0].style.background = "#EAF2F8";
//                    document.getElementById('checkacc' + codigo).checked = true;
                    this.checkedAcciones.push(codigo); //Agrego el modulo al arreglo checkedModulos
                }

            },

            async ASIGNARPERMISOS() {
                try {
                    let datos = {
                        rolId: this.idRol,
                        listaModulos: this.checkedModulos,
                        listaAcciones: this.checkedAcciones
                    };
                     this.loading=true;
                    let response = await axios.post(this.url + '/admin/roles/aplicarPermisos', datos);
                    if (response.data.status === 'success') {
                        sweet_msg_dialog('success', response.data.msg);
                        this.modalAsignacion.hide();
                    } else {
                        sweet_msg_dialog('warning', response.data.msg);
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e);
                }finally {
                    this.loading=false;
                }

            },
            clear() {
                this.newRol = {
                    nombreRol: '',
                    estado: '1'
                };
                this.formValidacion = [];
                this.estadoSave = true;
                this.idEdit = '';
                this.checkedModulos = [];
                this.checkedAcciones = [];
                this.modulosAbiertos = [];
                this.submodulosAbiertos = [];

//                for (let i in this.listaAllModulos) {
//                    this.checkedModulos = [];
//                    document.getElementsByClassName(this.listaAllModulos[i].id)[0].style.background = "transparent";
//                    document.getElementById('checkmod' + this.listaAllModulos[i].id).checked = false;
//
//                }
//                for (let i in this.listaAllSubModulos) {
//                    this.checkedModulos = [];
//                    document.getElementsByClassName(this.listaAllSubModulos[i].id)[0].style.background = "transparent";
//                    document.getElementById('checkmod' + this.listaAllSubModulos[i].id).checked = false;
//
//                }
//                for (let i in this.listaAllAcciones) {
//                    this.checkedAcciones = [];
//                    document.getElementsByClassName('_' + this.listaAllAcciones[i].id)[0].style.background = "transparent";
//                    document.getElementById('checkacc' + this.listaAllAcciones[i].id).checked = false;
//
//                }
            },
            clear_() {
                this.newRol = {
                    nombreRol: '',
                    estado: '1'
                };
                this.formValidacion = [];
                this.estadoSave = true;
                this.idEdit = '';
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
    window.appRoles.mount('#app');
</script>
