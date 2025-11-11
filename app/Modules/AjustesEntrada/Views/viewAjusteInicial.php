<!DOCTYPE html>
<!--
/**
 * Description of viewAjusteInicial
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 22 oct 2025
 * @time 4:45:09 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-file-upload"></i> Cargar Ajuste Inicial</h5>
        </div>
        <div class="card-body">
            <fieldset>
                <legend>Ajuste Inicial de Inventario</legend>
                <div class="row col-md-12">
                    <div class="col-md-2 form-group-custom">
                        <div class="input-group">
                            <span class="input-group-text bg-cris-system"><i class="fas fa-calendar"></i> Fecha  </span>
                            <input type="date" v-model="ajusteInicial.ajenFecha" class="form-control">
                        </div>
                    </div>

                    <!-- Bodega -->
                    <div class="col-md-2 form-group-custom">
                        <div class="d-flex justify-content-between align-items-center border">
                            <span class="input-group-text bg-cris-system"><i class="fas fa-warehouse me-2"></i> Bodega  </span>
                            <vue-select 
                                class="flex-grow-1" 
                                :options="listaBodegas" 
                                label="bod_nombre" 
                                v-model="ajusteInicial.ajenBodega" 
                                placeholder="Seleccione una bodega"/>
                        </div>
                    </div>
                    <!-- Motivo de Ajuste -->
                    <div class="col-md-3 form-group-custom">
                        <div class="d-flex justify-content-between align-items-center border">
                            <span class="input-group-text bg-cris-system"><i class="fas fa-tag me-2"></i>Motivo</span>
                            <vue-select 
                                class="flex-grow-1"
                                :options="listaMotivos"
                                label="mot_nombre"
                                v-model="ajusteInicial.ajenMotivo"
                                placeholder="Seleccione un motivo"/>
                        </div>
                    </div>

                    <!-- Centro de Costo -->
                    <div class="col-md-2 form-group-custom">
                        <div class="d-flex justify-content-between align-items-center border">
                            <span class="input-group-text bg-cris-system"><i class="fas fa-project-diagram me-2"></i>Centro de Costo</span>
                            <vue-select 
                                class="flex-grow-1"
                                :options="listaCentroCostos"
                                label="cc_nombre"
                                v-model="ajusteInicial.ajenCentrocosto"
                                placeholder="Seleccione un centro de costos"/>
                        </div>
                    </div>

                    <!-- Subacuenta -->
                    <div class="col-md-3 form-group-custom">
                        <div class="d-flex justify-content-between align-items-center border">                  
                            <span class="input-group-text bg-cris-system" id="basic-addon1"><i class="fas fa-receipt me-2"></i> Sustento</span>
                            <vue-select  
                                class="flex-grow-1"
                                :options="listaSustentos"
                                label="sus_nombre"
                                v-model="ajusteInicial.ajenSustento"
                                placeholder="Seleccione un sustento"/>
                        </div>
                    </div>

                    <div class="col-md-2 form-group-custom">
                        <div class="d-flex justify-content-between align-items-center border">                  
                            <span class="input-group-text bg-cris-system"><i class="fas fa-receipt me-2"></i> Tipo de producto</span>
                            <vue-select
                                class="flex-grow-1"
                                :options="listaTipoProducto"
                                label="tp_nombre"
                                v-model="ajusteInicial.ajenTipoProducto"
                                placeholder="Seleccione un tipo de producto"/>
                        </div>
                    </div>

                    <div class="col-md-2 form-group-custom">
                        <div class="d-flex justify-content-between align-items-center border">                  
                            <span class="input-group-text bg-cris-system"><i class="fas fa-receipt me-2"></i> Impuesto IVA</span>
                            <vue-select
                                class="flex-grow-1"
                                :options="listaImpuestosIva"
                                label="impt_detalle"
                                v-model="ajusteInicial.ajenImpuestoIva"
                                placeholder="Seleccione un Impuesto IVA"/>
                        </div>
                    </div>

                    <div class="col-md-3 form-group-custom">
                        <div class="input-group">      
                            <span class="input-group-text bg-cris-system"><i class="fas fa-receipt me-2"></i> Observación</span>
                            <input type="text" v-model="ajusteInicial.ajenObservacion" class="form-control">
                        </div>
                    </div>

                    <!--proveedor-->
                    <div class="col-md-4 form-group-custom ">
                        <div class="d-flex justify-content-between align-items-center ">  
                            <vue-multiselect
                                v-model="ajusteInicial.ajenProveedor" 
                                tag-placeholder="Proveedor no Encontrado"
                                placeholder="Buscar Proveedores"
                                label="proveedor"
                                track-by="prov_ruc"
                                :multiple="false"
                                :searchable="true"
                                :options-limit="10"
                                :show-no-results="true"
                                :options="listaSearchProveedores"
                                @remove="onRemove($event)"
                                @search-change="searchProveedor($event)">

                                <template slot="option" slot-scope="{ option }">
                                    <span style="font-size: 12px"><strong>{{ option.prov_ruc+': '}} </strong> {{  option.prov_razon_social}}</span>
                                </template>
                            </vue-multiselect>
                            <span class="input-group-text" style="border-radius: 0px 5px 5px 0px"><i class="fas fa-user-tie"></i></span>
                        </div>
                    </div>


                    <div class="col-md-6 mt-3">
                        <label class="form-label fw-semibold text-muted">Archivo Excel Ajuste Inicial</label>
                        <p class="text-muted">
                            Plantilla: Código, Nombre, Precio Sin IVA, Stock, Grupo, Subgrupo, Marca, Unidad, Cód. barras, etc.
                        </p>
                        <input type="file" class="form-control" ref="fileAjusteInicial"
                               @change="onFileAjusteInicialChange" accept=".xlsx,.xls">
                        <div v-if="excelFilename" class="mt-2 small">
                            <i class="fas fa-paperclip me-1"></i> {{ excelFilename }}
                        </div>

                    </div>

                    <div class="col-md-3 mt-4 d-flex align-items-end">
                        <button class="btn btn-success w-100"
                                :disabled="!ajusteInicial.ajenFile || loadingProcess"
                                @click="enviarAjusteInicialExcel">
                            <span v-if="loadingProcess">
                                <i class="loading-spin me-2"></i> Procesando...
                            </span>
                            <span v-else>
                                <i class="fas fa-upload me-2"></i> Importar Ajuste Inicial
                            </span>
                        </button>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <a :href="url + '/comun/descargar/downloadPlantillaExcelAjusteInicial'" class="btn btn-outline-primary w-100">
                            <i class="fas fa-download me-2"></i> Descargar Plantilla
                        </a>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</div>
<script type="text/javascript">

    var fechaActual = DateTime.now().toFormat('yyyy-MM-dd');
    var listaSustentos = <?= json_encode($listaSustentos); ?>;
    var listaBodegas = <?= json_encode($listaBodegas); ?>;
    var listaMotivos = <?= json_encode($listaMotivos); ?>;
    var listaCentroCostos = <?= json_encode($listaCentroCostos); ?>;
    var listaTipoProducto = <?= json_encode($listaTipoProducto); ?>;
    var listaImpuestosIva = <?= json_encode($listaImpuestosIva); ?>;

    var searchTimeout = null;

    if (window.appAjeIni) {
        window.appAjeIni.unmount();
    }

    window.appAjeIni = Vue.createApp({

        components: {
            "vue-multiselect": window['vue-multiselect'].Multiselect,
            "vue-select": window['vue-select']
        },

        data() {
            return {
                url: siteUrl,

                loadingProcess: false,
                excelFilename: '',

                ajusteInicial: {
                    ajenFecha: fechaActual,
                    ajenBodega: null,
                    ajenMotivo: null,
                    ajenCentrocosto: null,
                    ajenSustento: null,
                    ajenTipoProducto: null,
                    ajenImpuestoIva: null,
                    ajenObservacion: '',
                    ajenProveedor: null,
                    ajenFile: null
                },

                //LISTAS
                listaSustentos: listaSustentos,
                listaBodegas: listaBodegas,
                listaMotivos: listaMotivos,
                listaCentroCostos: listaCentroCostos,
                listaTipoProducto: listaTipoProducto,
                listaImpuestosIva: listaImpuestosIva,

                //VUE-MULTISELECT PROVEEDOR
                listaSearchProveedores: [],

            };
        },
        mounted() {

        },
        methods: {
            onFileAjusteInicialChange(e) {
                const file = e.target.files[0] || null;
                this.ajusteInicial.ajenFile = file;
                this.excelFilename = file ? file.name : '';
            },
            async enviarAjusteInicialExcel() {
                if (!this.ajusteInicial.ajenFile) {
                    sweet_msg_toast('warning', 'Seleccione un archivo Excel.');
                    return false;
                }
                const statusValidation = this.validarCampos();
                if (statusValidation.status) {
                    sweet_msg_toast('warning', statusValidation.msg);
                    return false;
                }

                const datos = this.formData(this.ajusteInicial);

                try {
                    this.loadingProcess = true;
                    const {data} = await axios.post(this.url + '/ajustesentrada/loadAjusteInicial', datos);
                    sweet_msg_dialog(data.status, data.msg);
                    if (data.status === 'success') {
                        // Podrías redirigir a la vista del ajuste o limpiar formulario
                    }

                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response?.data?.message || e.message);
                } finally {
                    this.loadingProcess = false;
                }
            },
            //SEARCH PROVEEDORES
            searchProveedor(dataSerach) {
                clearTimeout(searchTimeout);
                const datos = {dataSerach};
                searchTimeout = setTimeout(async () => {
                    try {
                        const {data} = await axios.post(this.url + '/comun/proveedores/searchProveedor', datos);
                        if (data !== false) {
                            this.listaSearchProveedores = data;
                        } else {
                            this.listaSearchProveedores = [];
                        }
                    } catch (e) {
                        sweet_msg_dialog('error', '', '', e.data?.message || e.message);
                        this.listaSearchProveedores = [];
                    }
                }, 500);

            },
            validarCampos() {
                const campos = [
                    {key: 'ajenFecha', msg: 'Debe seleccionar una fecha'},
                    {key: 'ajenSustento', msg: 'Debe seleccionar un sustento'},
                    {key: 'ajenBodega', msg: 'Debe seleccionar una bodega'},
                    {key: 'ajenCentrocosto', msg: 'Debe seleccionar un centro de costos'},
                    {key: 'ajenMotivo', msg: 'Debe seleccionar un motivo de ajuste'},
                    {key: 'ajenProveedor', msg: 'Debe seleccionar un proveedor'},
                ];

                for (const campo of campos) {
                    if (!this.ajusteInicial[campo.key]) {
                        return {status: true, msg: campo.msg};
                    }
                }

                return {status: false};
            },
            formData(obj) {
                var formData = new FormData();
                for (var key in obj) {
                    let value = obj[key];

                    // Si es null o undefined, agregar como está
                    if (value === null || value === undefined) {
                        formData.append(key, '');
                        continue;
                    }
                    if (typeof value === 'object') {
                        value = value.id || value.sus_codigo || value.codigo || value.value || JSON.stringify(value);
                    }
                    formData.append(key, value);
                }
                return formData;
            },

        }

    });

    window.appAjeIni.mount('#app');
</script>