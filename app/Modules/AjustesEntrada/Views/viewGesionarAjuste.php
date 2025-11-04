<!DOCTYPE html>
<!--
/**
 * Description of viewGesionarAjuste
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 22 oct 2025
 * @time 2:49:18 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-clipboard-list-check"></i> Gestion de Ajustes de Entrada</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tblAjustes" class="table table-striped nowrap w-100" >
                    <thead class="bg-system text-white">
                        <tr>
                            <th style="width: 5px">ACIONES</th>
                            <th style="width: 5px">CÓDIGO</th>
                            <th>FECHA</th>
                            <th>TOTAL</th>
                            <th>OBSERVACIONES</th>
                            <th>BODEGA</th>
                            <th>C. COSTO</th>
                            <th>PROVEEDOR</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for='laj of listaAjustes'>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span><i class="fas fa-ellipsis-v"></i></span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                        <li><button class="dropdown-item" href="#" @click.prevent="verDetalle(laj)"><span><i class="fas fa-clipboard-list"></i> Ver Detalle</span></button> </li>
                                        <li><button :disabled="laj.ajen_estado == 2 ? true : false " class="dropdown-item" href="#"  @click.prevent="loadAjusteEdit(laj.id)"> <span><i class="fas fa-edit"></i> Modificar Ajuste</span></button></li>
                                        <li><button class="dropdown-item" href="#"><span><i class="fas fa-stop-circle"></i>  Anular Ajuste</span></button></li>
                                        <li><button class="dropdown-item" href="#"><span><i class="fas fa-clone"></i>  Clonar Ajuste</span> </button></li>
                                    </ul>
                                </div>
                                <!--<button @click="loadAjuste(laj), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalAnillos"><i class="fas fa-edit"></i> </button>-->
                            </td>
                            <td>{{zFill(laj.ajen_secuencial,4)}}</td>
                            <td>{{laj.ajen_fecha}}</td>
                            <td>{{laj.ajen_total}}</td>
                            <td>{{laj.ajen_observaciones}}</td>
                            <td>{{laj.bod_nombre}}</td>
                            <td>{{laj.cc_nombre}}</td>
                            <td>{{laj.prov_razon_social}}</td>

                            <td>
                                <span v-if="laj.ajen_estado == 2" class="badge bg-success"><i class="fas fa-check-double"></i>  ARCHIVADO</span>
                                <span v-else-if="laj.ajen_estado == 1" class="badge bg-warning"><i class="fas fa-warning"></i>  BORRADOR</span>
                                <span v-else-if="laj.ajen_estado == -1" class="badge bg-danger"><i class="fas fa-stop-circle"></i>  ANULADO</span>
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div> 

        </div>

        <!--MODAL DETALLE-->
        <?php echo view('\Modules\AjustesEntrada\Views\reportes\viewModalReport') ?>
        <!--CLOSE MODAL DETALLE-->
    </div>
</div>
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>

<script type="text/javascript">

if (window.appGestionAje) {
    window.appGestionAje.unmount();
}

window.appGestionAje = Vue.createApp({

    data() {
        return {
            url: siteUrl,
            pathUrl: baseUrl,
            empresa: window.empresa,
            idAjuste: '',
            secuencialAjuste: '',
            listaAjustes: [],
            cargandoDetalle: false,
            modalInstance: null
        };
    },
    created() {

    },
    computed: {
        estadoDocumento() {
            if (this.ajusteActual.ajen_estado === '1')
                return '<span class="badge bg-warning"><i class="fas fa-edit"></i> BORRADOR </span>';
            if (this.ajusteActual.ajen_estado === '2')
                return '<span class="badge bg-success"><i class="fas fa-check-double "></i> ARCHIVADO </span>';
            if (this.ajusteActual.ajen_estado === '-1')
                return '<span class="badge bg-danger"><i class="fas fa-stop-circle "></i> ANULADO </span>';
            return 'Desconocido';
        }

    },
    mounted() {
        //Cragamos los ajustes
        this.getAjustes();
        // Inicializar modal de Bootstrap
        this.modalInstance = new bootstrap.Modal(this.$refs.modalReport);
    },
    methods: {

        async getAjustes() {
            try {

                const {data} = await axios.post(this.url + '/ajustesentrada/getAjustes');
                if (data) {
                    this.listaAjustes = data;
                } else {
                    sweet_msg_dialog('warning', 'No se han encontrado ajustes registrados en los parametros especificados');
                }
                dataTable('#tblAjustes', 'Listado de ajustes de entrada');
            } catch (e) {
                sweet_msg_dialog('error', '', '', e.response.data?.message || e.message);
            }

        },

        async loadAjusteEdit(idAjuste) {

            try {
                swalLoading('Cargando documento');
                const {data} = await axios.get(this.url + '/ajustesentrada/loadAjusteEdit/' + idAjuste);
                if (data.status === 'success') {
                    window.location.href = data.redirect;
                } else {
                    sweet_msg_dialog('error', data.msg);
                }
                Swal.close();
            } catch (e) {
                sweet_msg_dialog('error', '', '', 'Error al cargar el detalle del ajuste, ' + e.message);
            }

        },

        // Ver detalle del ajuste
        async verDetalle(ajuste) {
            this.idAjuste = ajuste.id;
            this.secuencialAjuste = ajuste.ajen_secuencial;
            this.cargandoDetalle = true;
            this.modalInstance.show();
            try {

                const {data} = await axios.get(this.url + '/ajustesentrada/getDataDetalle/' + ajuste.id);
                this.cargandoDetalle = false;
                await Vue.nextTick();
                const modal = document.getElementById('detalleAjusteModal');
                modal.innerHTML = data;

            } catch (error) {
                sweet_msg_dialog('error', '', '', 'Error al cargar el detalle del ajuste, ' + error.message);
            } finally {
                this.cargandoDetalle = false;
            }
        },

        // ==========================================
        // EXPORTAR A EXCEL
        // ==========================================

        generarExcel() {
            const contenido = document.getElementById('contentExport');
            const ruta = `${this.url}/comun/exportar/generarExcel`;
            const titulo = `Ajuste_Entrada_${this.zFill(this.secuencialAjuste, 5)}`;
            return generarExcel(contenido, titulo, ruta);
        },
        // ==========================================
        // EXPORTAR A PDF
        // ==========================================
        generarPDF() {
            try {
                window.open(`${this.url}/ajustesentrada/generarPDF/${this.idAjuste}?download=1`, '_blank');
            } catch (e) {
                sweet_msg_dialog('error', '', '', 'Error al generar el documento, ' + e.message);
            }
        },

        async enviarPorEmail() {
            try {
                swalLoading('Enviando email...');
                const {data} = await axios.post(`${this.url}/ajustesentrada/enviarPorEmail/${this.idAjuste}`);
                Swal.close();
                if (data.success) {
                    sweet_msg_toast('success', 'Email enviado exitosamente');
                } else {
                    sweet_msg_toast('error', data.message);
                }
            } catch (error) {
                sweet_msg_toast('error', 'Error al enviar email');
            }
        },
//                exportarExcel() {
//            const exporter = new ExcelExport();
//
//            const config = {
//                nombreArchivo: `Ajuste_${this.ajusteActual.ajen_secuencial}_${Date.now()}`,
//                titulo: 'AJUSTE DE ENTRADA',
//
//                // Información del encabezado
//                encabezado: {
//                    'Secuencial:': this.ajusteActual.ajen_secuencial,
//                    'Fecha Emisión:': this.formatearFecha(this.ajusteActual.ajen_fecha),
//                    'Estado:': this.ajusteActual.ajen_estado,
//                    'Bodega:': this.ajusteActual.bod_nombre,
//                    'Centro de Costo:': this.ajusteActual.cc_nombre,
//                    'Proveedor:': this.ajusteActual.prov_razon_social,
//                    'Usuario:': this.ajusteActual.user_create,
//                    'Observaciones:': this.ajusteActual.ajen_observacion || 'Sin observaciones'
//                },
//
//                // Definición de columnas
//                columnas: [
//                    {campo: 'index', titulo: '#', ancho: 5},
//                    {campo: 'prod_codigo', titulo: 'Código', ancho: 12},
//                    {campo: 'prod_nombre', titulo: 'Producto', ancho: 35},
//                    {campo: 'lot_lote', titulo: 'Lote', ancho: 15},
//                    {campo: 'lot_fecha_elaboracion', titulo: 'F. Elaboración', ancho: 15, formato: 'fecha'},
//                    {campo: 'ajend_itemcantidad', titulo: 'Cantidad', ancho: 10, formato: 'numero'},
//                    {campo: 'ajend_itemcosto', titulo: 'Costo Unitario', ancho: 15, formato: 'moneda', formatoConfig: {decimales: 2}},
//                    {campo: 'ajend_itemcostoxcantidad', titulo: 'Subtotal', ancho: 15, formato: 'moneda', formatoConfig: {decimales: 2}},
//                ],
//
//                // Datos (agregar índice)
//                datos: this.ajusteActual.detalle.map((item, index) => ({
//                        index: index + 1,
//                        ...item
//                    })),
//
//                // Totales
//                totales: {
//                    prod_nombre: 'TOTAL GENERAL:',
//                    ajend_itemcantidad: this.calcularTotalUnidades(),
//                    ajend_itemcostoxcantidad: this.calcularTotal()
//                },
//
//                // Pie de página
//                piePagina: `Generado el ${this.formatearFechaHoraActual()}`
//            };
//
//            const resultado = exporter.exportar(config);
//
//            if (resultado.success) {
//                sweet_msg_toast('success', resultado.message);
//            } else {
//                sweet_msg_toast('error', resultado.message);
//            }
//        },

        //            // Formateo de fecha
//            formatearFecha(fecha) {
//                return formatearFecha(fecha);
//            },
//
//            // Formateo de fecha y hora actual
//            formatearFechaHoraActual() {
//                return formatearFechaHoraActual();
//            },
//
//            // Formateo de moneda
//            formatToUSD(valor) {
//                return formatToUSD(valor);
//            },
//
//            // Calcular total
//            calcularTotal() {
//                if (!this.ajusteActual.detalle)
//                    return 0;
//                return this.ajusteActual.detalle.reduce((sum, item) =>
//                    sum + parseFloat(item.ajend_itemcostoxcantidad || 0), 0
//                        );
//            },
//
//            // Calcular unidades
//            calcularTotalUnidades() {
//                return  calcularTotalUnidades(this.ajusteActual.detalle);
//            },


        zFill(value, size) {
            return zFill(value, size);
        }

    }
});
window.appGestionAje.mount('#app');
</script>

