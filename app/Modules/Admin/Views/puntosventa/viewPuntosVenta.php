<!DOCTYPE html>
<!--
/**
 * Description of viewPuntoVenta
 *
/**
 * @author CRISTIAN PAZ
 * @date 24 ene. 2024
 * @time 12:08:35
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-shop"></i> Puntos de Venta</h5>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto">
                <table id="tblPuntoVenta" class="table table-striped nowrap display" style="width: 100%">
                    <thead class="bg-system text-white">
                        <tr>
                            <td>ID</td>
                            <td>COMPROBANTE</td>
                            <td>P. ESTABLECIMIENTO</td>
                            <td>P. EMISIÓN</td>
                            <td>AUTH. SRI</td>
                            <td>FECHA VENCE AUTH.</td>
                            <td>SEC. INICIAL</td>
                            <td>SEC. FINAL</td>
                            <td>ELECTRONICO</td>
                            <td>BODEGA</td>
                            <td>EMPLEADOS</td>
                            <td>ESTADO</td>
                            <td>ACCIONES</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="lpv of listaPuntoVenta">
                            <td>{{zfill(lpv.id)}}</td>
                            <td>{{lpv.comp_nombre}}</td>
                            <td>{{lpv.pv_establecimiento}}</td>
                            <td>{{lpv.pv_emision}}</td>
                            <td>{{lpv.pv_auth_sri}}</td>
                            <td>{{lpv.pv_fecha_vence_auth}}</td>
                            <td>{{lpv.pv_sec_inicial}}</td>
                            <td>{{lpv.pv_sec_final}}</td>

                            <td v-if="lpv.pv_is_electronica == 1 "><span class="badge bg-success"><i class="fas fa-check-double"></i>  SI</span></td>
                            <td v-else><span class="badge bg-danger"><i class="fas fa-stop-circle"></i> NO</span></td>   

                            <td>{{lpv.bod_nombre}}</td>

                            <td class="text-center"><button @click="showEmpleados(lpv.id)" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalEmpleados"><span><i class="fas fa-user"></i></span></button></td>                           

                            <td v-if="lpv.pv_estado == 1 "><span class="badge bg-success"><i class="fas fa-check-double"></i>  Activo</span></td>
                            <td v-else><span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span></td>


                            <td>
                                <template v-if="admin">
                                    <button @click="loadPuntoVenta(lpv), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalPuntoVenta"><i class="fas fa-edit"></i> </button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!--MODAL CREATE PUNTOS DE VENTA-->
            <div id="modalPuntoVenta" class="modal fade" data-bs-backdrop="static" dat-bs-keyboard="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 v-if="estadoSave" class=""><i class="fas fa-file-alt"></i> Crear Punto de Venta</h5>
                            <h5 v-else class=""><i class="fas fa-file-alt"></i> Actualizar Punto de Venta</h5>
                            <button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>
                        </div>
                        <div class="modal-body">
                            <div class="row col-md-12">
                                <input type="hidden" v-model="idEdit">
                                <div class="mb-3">
                                    <label for="pvComprobante" class="col-form-label col-form-label-sm"><i class="fal fa-clipboard-list"></i> Comprobante</label>
                                    <select title="Seleccione un comprobante" class="form-control selectpicker show-tick border" data-live-search="true" v-model="newPV.pvComprobante" id="pvComprobante">
                                        <option v-for="lc of listaComprobantes" v-bind:value="lc.comp_codigo">{{lc.comp_nombre}}</option>
                                    </select>
                                    <!--validaciones-->
                                    <div v-html="formValidacion.pvComprobante" class="text-danger"></div>
                                </div>    

                                <div class="mb-3 col-md-6">
                                    <label for="pvEstablecimiento" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> P. Establecimiento</label>
                                    <input  v-model="newPV.pvEstablecimiento" type="number" class="form-control" id="pvEstablecimiento" placeholder="Ingrese un punto de establecimiento" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.pvEstablecimiento" class="text-danger"></div>
                                </div>       

                                <div class="mb-3 col-md-6">
                                    <label for="pvEmision" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> P. Emisión</label>
                                    <input  v-model="newPV.pvEmision" type="number" class="form-control" id="pvEmision" placeholder="Ingrese un punto de emisión" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.pvEmision" class="text-danger"></div>
                                </div>                          

                                <div class="mb-3 col-md-6">
                                    <label for="pvAuthSri" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Autorización SRI</label>
                                    <input  v-model="newPV.pvAuthSri" type="number" class="form-control" id="pvAuthSri" placeholder="Ingrese el # de autorización del SRI" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.pvAuthSri" class="text-danger"></div>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="pvFechaVenceAuthSri" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Fecha Vence Autorización</label>
                                    <input  v-model="newPV.pvFechaVenceAuthSri" type="date" class="form-control" id="pvFechaVenceAuthSri" placeholder="Seleccione la fecha de vencimiento" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.pvFechaVenceAuthSri" class="text-danger"></div>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="pvSecInicial" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Secuencia Inicial</label>
                                    <input  v-model="newPV.pvSecInicial" type="number" class="form-control" id="pvSecInicial" placeholder="Ingrese la secuencia inicial" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.pvSecInicial" class="text-danger"></div>
                                </div>       

                                <div class="mb-3 col-md-6">
                                    <label for="pvSecFinal" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Secuencia Final</label>
                                    <input  v-model="newPV.pvSecFinal" type="number" class="form-control" id="pvSecFinal" placeholder="Ingrese la secuencia final" />
                                    <!--validaciones-->
                                    <div v-html="formValidacion.pvSecFinal" class="text-danger"></div>
                                </div> 

                                <div class="mb-3">
                                    <label for="pvBodega" class="col-form-label col-form-label-sm"><i class="fal fa-clipboard-list"></i> Bodega</label>
                                    <select title="Seleccione una bodega" class="form-control selectpicker show-tick border" data-live-search="true" v-model="newPV.pvBodega" id="pvBodega">
                                        <option v-for="lb of listaBodegas" v-bind:value="lb.id">{{lb.bod_nombre}}</option>
                                    </select>
                                    <!--validaciones-->
                                    <div v-html="formValidacion.pvBodega" class="text-danger"></div>
                                </div>    

                                <div class="mb-3">
                                    <label for="pvEmpleado" class="col-form-label col-form-label-sm"><i class="fal fa-clipboard-list"></i> Empleados</label>
                                    <vue-multiselect 
                                        v-model="pvEmpleados"
                                        tag-placeholder="Empleado no encontrado"
                                        placeholder="Buscar y agregar empleados"
                                        label="empleado"
                                        track-by="id"
                                        :multiple="true"
                                        :searchable="true"
                                        :options-limit="10"
                                        :show-no-results="true"
                                        :options="listaEmpleados"                                    
                                        >
                                    </vue-multiselect>
                                    <!--validaciones-->
                                    <!--NA-->
                                </div>    

                                <div class="mb-3 col-md-6">
                                    <label for="pvIsElectronica" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Es Electronico</label>
                                    <select v-model="newPV.pvIsElectronica" class="form-select border" id="pvIsElectronica">
                                        <option value="1">SI</option>
                                        <option value="0"> NO</option>
                                    </select>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="pvEstado" class="col-form-label col-form-label-sm"><i class="fal fa-file-check"></i> Estado</label>
                                    <select v-model="newPV.pvEstado" class="form-select border" id="pvEstado">
                                        <option value="1">ACTIVO</option>
                                        <option value="0"> INACTIVO</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button  class="btn btn-primary" @click="saveUpdatePuntoVenta()">
                                <span v-if="estadoSave"><i class="fas fa-save"></i> Crear</span>
                                <span v-else><i class="fas fa-refresh"></i> Actualizar</span>
                            </button>
                            <button @click="clear()" class="btn btn-danger" data-bs-dismiss="modal"><i class="fas fa-stop"></i> Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--CLOSE MODAL CREATE PUNTOS DE VENTA-->

            <!--MODAL EMPLEADOS PUNTPO VENTA-->
            <div id="modalEmpleados" class="modal fade">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-file-alt"></i> Empleados</h5>
                            <!--<button @click="clear()" class="btn btn-danger btn-sm" data-bs-dismiss="modal">X</button>-->
                        </div>
                        <div class="modal-body">
                            <table class="table" style="width: 100%">
                                <tr v-for="lse of pvEmpleados">
                                    <td><span><i class="fas fa-check"></i></span> {{lse.empleado}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--CIERRA MODAL EMPLEADOS PUNTPO VENTA-->
        </div>
    </div>
</div>

<script type="text/javascript">


<?php $admin = $user->validatePermisos('admin', $user->id) ?>
    var admin = '<?= $admin ?>';

    var listaComprobantes =<?php echo json_encode($listaComprobantes); ?>;
    var listaBodegas =<?php echo json_encode($listaBodegas); ?>;
    var listaEmpleados =<?php echo json_encode($listaEmpleados); ?>;

    if (window.appPuntoVenta) {
        window.appPuntoVenta.unmount();
    }

    window.appPuntoVenta = Vue.createApp({

        components: {
            'vue-multiselect': window['vue-multiselect'].Multiselect
        },
        data() {
            return {
                url: siteUrl,

                //TODO: PERMISOS
                admin: admin,

                //TODO: VARIABLES
                estadoSave: true,

                //TODO: V-MODELS
                idEdit: '',
                newPV: {
                    pvEstablecimiento: '',
                    pvEmision: '',
                    pvComprobante: '',
                    pvAuthSri: '',
                    pvFechaVenceAuthSri: '',
                    pvSecInicial: '',
                    pvSecFinal: '',
                    pvIsElectronica: '0',
                    pvBodega: '',
                    pvEstado: '1',
                },
                pvEmpleados: '',

                //TODO: LISTAS
                listaPuntoVenta: [],
                listaComprobantes: listaComprobantes,
                listaBodegas: listaBodegas,
                listaEmpleados: listaEmpleados,
                formValidacion: []
            };
        },
        created() {
            this.getPuntosVenta();
        },
        methods: {
            async showEmpleados(idPv) {
                try {
                    let response = await axios.post(this.url + '/admin/pventa/showEmpleados/' + idPv);
                    if (response.data) {
                        this.pvEmpleados = response.data;
                    } else {
                        this.pvEmpleados = '';
                        sweet_msg_toast('warning', 'El punto de venta no tiene empleados registrados.');
                    }
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                }

            },
            async getPuntosVenta() {
                try {
                    let response = await axios.get(this.url + '/admin/pventa/getPuntosVenta');
                    if (response.data) {
                        this.listaPuntoVenta = response.data;
                    } else {
                        sweet_msg_dialog('warning', 'No se encontraron puntos de venta registradas');
                    }
                    if (this.admin) {
                        dataTableModalBtn('#tblPuntoVenta', 'Lista de puntos de venta', '#modalPuntoVenta', 'CREAR PUNTO DE VENTA');
                    } else {
                        dataTable('#tblPuntoVenta', 'Lista de puntos de venta');
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data.message);
                }
            },
            loadPuntoVenta(pv) {
                this.newPV = {
                    pvComprobante: pv.fk_comprobante,
                    pvEstablecimiento: pv.pv_establecimiento,
                    pvEmision: pv.pv_emision,
                    pvAuthSri: pv.pv_auth_sri,
                    pvFechaVenceAuthSri: pv.pv_fecha_vence_auth,
                    pvSecInicial: pv.pv_sec_inicial,
                    pvSecFinal: pv.pv_sec_final,
                    pvIsElectronica: pv.pv_is_electronica,
                    pvBodega: pv.pv_fk_bodega,
                    pvEstado: pv.pv_estado
                };
                $('#pvComprobante').selectpicker('val', pv.fk_comprobante);
                $('#pvBodega').selectpicker('val', pv.pv_fk_bodega);

                this.idEdit = pv.id;
                this.nameAux = pv.fk_comprobante;

                this.showEmpleados(pv.id);

            },
            async saveUpdatePuntoVenta() {
                let datos = this.formData(this.newPV);

                if (this.pvEmpleados) {
                    let empleadoId = this.pvEmpleados.map(data => data.id);
                    datos.append('pvEmpleado', empleadoId);
                } else {
                    datos.append('pvEmpleado', "");
                }

                let url = this.url + '/admin/pventa/savePuntoVenta';

                if (this.idEdit != '') {
                    datos.append('idPV', this.idEdit);
                    datos.append('nameAux', this.nameAux);//TODO: ESTA VARIABLE SE LA USA PARA VALIDAR QUE NO AYA OTRA REGISTRO CON EL MISMO NOMBRE
                    url = this.url + '/admin/pventa/updatePuntoVenta';
                }

                try {
                    let response = await axios.post(url, datos);
                    if (response.data.status === 'success') {

                        sweet_msg_dialog('success', response.data.msg);
                        this.clear();
                        this.getPuntosVenta();
                        $('#modalPuntoVenta').modal('hide');
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
                this.newPV = {
                    pvEstablecimiento: '',
                    pvEmision: '',
                    pvComprobante: '',
                    pvAuthSri: '',
                    pvFechaVenceAuthSri: '',
                    pvSecInicial: '',
                    pvSecFinal: '',
                    pvIsElectronica: '0',
                    pvBodega: '',
                    pvEstado: '1',
                };
                $('#pvComprobante').selectpicker('val', '');
                $('#pvBodega').selectpicker('val', '');
                this.pvEmpleados = '';

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
    window.appPuntoVenta.mount('#app');

</script>
