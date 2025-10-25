<!DOCTYPE html>
<!--
/**
 * Description of viewMarcas
 *
/**
 * @author CRISTIAN PAZ
 * @date 15 abr. 2024
 * @time 11:29:18
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-file-archive"></i> Administrar Marcas</h5>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto">
                <table id="tblMarcas" class="table table-striped nowrap display" style="width: 100%">
                    <thead class="bg-system text-white">
                        <tr>
                            <td>ID</td>
                            <td>NOMBRE</td>
                            <td>FECHA CREACIÓN</td>
                            <td>ESTADO</td>
                            <td>ACCIONES</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="lm of listaMarcas">
                            <td>{{zfill(lm.id)}}</td>
                            <td>{{lm.mrc_nombre}}</td>
                            <td>{{lm.mrc_fecha_creacion}}</td>

                            <td v-if="lm.mrc_estado == 1 "><span class="badge bg-success"><i class="fas fa-check-double"></i>  Activo</span></td>
                            <td v-else><span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span></td>

                            <td>
                                <template v-if="admin">
                                    <button @click="loadMarca(lm), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalMarca"><i class="fas fa-edit"></i> </button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!--MODAL MARCAS-->
            <div id="modalMarca" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 v-if="estadoSave" class=""><i class="fas fa-file-alt"></i> Crear Marca</h5>
                            <h5 v-else class=""><i class="fas fa-file-alt"></i> Actualizar Marca</h5>
                            <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
                        </div>
                        <div class="modal-body">
                            <div class="text-left">
                                <input type="hidden" v-model="idEdit">
                                <div class="mb-3">
                                    <label for="mrcNombre" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Nombre</label>
                                    <input  v-model="newMarca.mrcNombre" type="text" class="form-control" id="mrcNombre" placeholder="Ingrese un nombre" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.mrcNombre" class="text-danger"></div>
                                </div>                       

                                <div class="mb-3">
                                    <label for="mrcEstado" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Estado</label>
                                    <select v-model="newMarca.mrcEstado" class="form-select border" id="mrcEstado">
                                        <option value="1">ACTIVO</option>
                                        <option value="0"> INACTIVO</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button  class="btn btn-primary" @click="saveUpdateMarca()">
                                <span v-if="estadoSave"><i class="fas fa-save"></i> Crear</span>
                                <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                            </button>
                            <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-stop"></i> Cancelar</button>
                        </div>
                    </div>
                </div>
                <!--CLOSE MODAL MARCAS-->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';

    if (window.appMarcas) {
        window.appMarcas.unmount();
    }


    window.appMarcas = Vue.createApp({

        data() {
            return {
                url: siteUrl,

                //TODO: PERMISOS
                admin: admin,

                //TODO: VARIABLES
                estadoSave: true,
                //TODO: V-MODELS
                idEdit: '',
                newMarca: {
                    mrcNombre: '',
                    mrcEstado: '1'
                },

                //TODO: LISTAS
                listaMarcas: [],
                formValidacion: []
            }

        },
        created() {
            this.getMarcas();
        },
        methods: {
            async getMarcas() {
                try {
                    let response = await axios.get(this.url + '/admin/marcas/getMarcas');
                    if (response.data) {
                        this.listaMarcas = response.data;
                    } else {
                        sweet_msg_dialog('warning', 'No se encontraron marcas registradas');
                    }
                    if (this.admin) {
                        dataTableModalBtn('#tblMarcas', 'Lista de Marcas', '#modalMarca', 'CREAR MARCA');
                    } else {
                        dataTable('#tblMarcas', 'Lista de Marcas');
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                }
            },
            loadMarca(marca) {
                this.newMarca = {
                    mrcNombre: marca.mrc_nombre,
                    mrcEstado: marca.mrc_estado
                };
                this.idEdit = marca.id;
                this.nameAux = marca.mrc_nombre;
            },
            async saveUpdateMarca() {
                let datos = this.formData(this.newMarca);
                let url = this.url + '/admin/marcas/saveMarca';

                if (this.idEdit != '') {
                    datos.append('idMarca', this.idEdit);
                    datos.append('nameAux', this.nameAux);//TODO: ESTA VARIABLE SE LA USA PARA VALIDAR QUE NO EXISTA OTRA REGISTRO CON EL MISMO NOMBRE
                    url = this.url + '/admin/marcas/updateMarca';
                }

                try {
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getMarcas();
                        $('#modalMarca').modal('hide');
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
                this.newMarca = {
                    mrcNombre: '',
                    mrcEstado: '1'
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
    window.appMarcas.mount('#app');
</script>
