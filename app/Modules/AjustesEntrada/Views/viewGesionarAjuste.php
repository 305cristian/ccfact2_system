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
                                        <li><a class="dropdown-item" href="#" @click.prevent="verDetalle(laj.id)"><span><i class="fas fa-clipboard-list"></i> Ver Detalle</span></a> </li>
                                        <li><a class="dropdown-item" href="#"> <span><i class="fas fa-edit"></i> Modificar Ajuste</span></a></li>
                                        <li><a class="dropdown-item" href="#"><span><i class="fas fa-stop-circle"></i>  Anular Ajuste</span></a></li>
                                        <li><a class="dropdown-item" href="#"><span><i class="fas fa-clone"></i>  Clonar Ajuste</span> </a></li>
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
        <?php echo view('\Modules\AjustesEntrada\Views\reportes\viewDetalleAjuste') ?>
        <!--CLOSE MODAL DETALLE-->
    </div>
</div>
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

                listaAjustes: [],
                ajusteActual: {
                    ajen_secuencial: '',
                    ajen_fecha: '',
                    ajen_estado: '',
                    bod_nombre: '',
                    cc_nombre: '',
                    user_create: '',
                    prov_razon_social: '',
                    ajen_observacion: '',
                    detalle: []
                },
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
            // Ver detalle del ajuste
            async verDetalle(idAjuste) {
                this.cargandoDetalle = true;
                this.modalInstance.show();

                try {
                    const {data} = await axios.get(this.url + '/ajustesentrada/getDataDetalle/' + idAjuste);
                    this.ajusteActual = data;
                } catch (error) {
                    sweet_msg_dialog('error', '', '', 'Error al cargar el detalle del ajuste, ' + error.message);
                } finally {
                    this.cargandoDetalle = false;
                }
            },
            // Formateo de fecha
            formatearFecha(fecha) {
                return formatearFecha(fecha);
            },

            // Formateo de fecha y hora actual
            formatearFechaHoraActual() {
                return formatearFechaHoraActual();
            },

            // Formateo de moneda
            formatToUSD(valor) {
                return formatToUSD(valor);
            },

            // Calcular total
            calcularTotal() {
                if (!this.ajusteActual.detalle)
                    return 0;
                return this.ajusteActual.detalle.reduce((sum, item) =>
                    sum + parseFloat(item.ajend_itemcostoxcantidad || 0), 0
                        );
            },

            // Calcular unidades
            calcularTotalUnidades() {
                return  calcularTotalUnidades(this.ajusteActual.detalle);
            },

            // Clase del badge según estado
            getBadgeClass(estado) {
                return estado === 'ARCHIVADO' ? 'badge bg-success' : 'badge bg-warning';
            },

            // ==========================================
            // EXPORTAR A EXCEL
            // ==========================================
            exportarExcel() {
                const exporter = new ExcelExport();

                const config = {
                    nombreArchivo: `Ajuste_${this.ajusteActual.ajen_secuencial}_${Date.now()}`,
                    titulo: 'AJUSTE DE ENTRADA',

                    // Información del encabezado
                    encabezado: {
                        'Secuencial:': this.ajusteActual.ajen_secuencial,
                        'Fecha Emisión:': this.formatearFecha(this.ajusteActual.ajen_fecha),
                        'Estado:': this.ajusteActual.ajen_estado,
                        'Bodega:': this.ajusteActual.bod_nombre,
                        'Centro de Costo:': this.ajusteActual.cc_nombre,
                        'Proveedor:': this.ajusteActual.prov_razon_social,
                        'Usuario:': this.ajusteActual.user_create,
                        'Observaciones:': this.ajusteActual.ajen_observacion || 'Sin observaciones'
                    },

                    // Definición de columnas
                    columnas: [
                        {campo: 'index', titulo: '#', ancho: 5},
                        {campo: 'prod_codigo', titulo: 'Código', ancho: 12},
                        {campo: 'prod_nombre', titulo: 'Producto', ancho: 35},
                        {campo: 'lot_lote', titulo: 'Lote', ancho: 15},
                        {campo: 'lot_fecha_elaboracion', titulo: 'F. Elaboración', ancho: 15, formato: 'fecha'},
                        {campo: 'ajend_itemcantidad', titulo: 'Cantidad', ancho: 10, formato: 'numero'},
                        {campo: 'ajend_itemcosto', titulo: 'Costo Unitario', ancho: 15, formato: 'moneda', formatoConfig: {decimales: 2}},
                        {campo: 'ajend_itemcostoxcantidad', titulo: 'Subtotal', ancho: 15, formato: 'moneda', formatoConfig: {decimales: 2}},
                    ],

                    // Datos (agregar índice)
                    datos: this.ajusteActual.detalle.map((item, index) => ({
                            index: index + 1,
                            ...item
                        })),

                    // Totales
                    totales: {
                        prod_nombre: 'TOTAL GENERAL:',
                        ajend_itemcantidad: this.calcularTotalUnidades(),
                        ajend_itemcostoxcantidad: this.calcularTotal()
                    },

                    // Pie de página
                    piePagina: `Generado el ${this.formatearFechaHoraActual()}`
                };

                const resultado = exporter.exportar(config);

                if (resultado.success) {
                    sweet_msg_toast('success', resultado.message);
                } else {
                    sweet_msg_toast('error', resultado.message);
                }
            },

            // ==========================================
            // EXPORTAR A PDF
            // ==========================================
            exportarPDF() {
                const exporter = new PDFExporter();

                const config = {
                    nombreArchivo: `Ajuste_${this.ajusteActual.ajen_secuencial}`,
                    orientacion: 'portrait', // 'portrait' o 'landscape'
                    formato: 'a4',

                    // Datos de la empresa
                    empresa: {
                        nombre: 'TU EMPRESA S.A.',
                        ruc: '1234567890001',
                        direccion: 'Av. Principal 123, Loja - Ecuador',
                        telefono: '(07) 123-4567',
                        email: 'info@tuempresa.com'
                                // logo: 'data:image/png;base64,...' // Si tienes logo
                    },

                    // Título del documento
                    titulo: 'AJUSTE DE ENTRADA',

                    // Información general del documento
                    informacion: {
                        'Código:': this.ajusteActual.ajen_secuencial,
                        'Fecha:': this.formatearFecha(this.ajusteActual.ajen_fecha),
                        'Estado:': this.ajusteActual.ajen_estado,
                        'Bodega:': this.ajusteActual.bod_nombre,
                        'Centro de Costo:': this.ajusteActual.cc_nombre,
                        'Proveedor:': this.ajusteActual.prov_razon_social,
                        'Usuario:': this.ajusteActual.user_create,
                        'Observaciones:': this.ajusteActual.ajen_observacion || 'Sin observaciones'
                    },

                    // Definición de columnas
                    columnas: [
                        {campo: 'index', titulo: '#', ancho: 10, alineacion: 'center'},
                        {campo: 'prod_codigo', titulo: 'Código', ancho: 25},
                        {campo: 'prod_nombre', titulo: 'Producto', ancho: 60},
                        {campo: 'lot_lote', titulo: 'Lote', ancho: 25},
                        {campo: 'lot_fecha_elaboracion', titulo: 'F. Elaboración', ancho: 15, formato: 'fecha'},
                        {campo: 'ajend_itemcantidad', titulo: 'Cant.', ancho: 15, alineacion: 'center'},
                        {campo: 'ajend_itemcosto', titulo: 'Costo Unit.', ancho: 25, formato: 'moneda', alineacion: 'right'},
                        {campo: 'ajend_itemcostoxcantidad', titulo: 'Subtotal', ancho: 25, formato: 'moneda', alineacion: 'right'},
                    ],

                    // Datos
                    datos: this.ajusteActual.detalle.map((item, index) => ({
                            index: index + 1,
                            ...item
                        })),

                    // Totales
                    totales: {
                        'Total Items:': this.ajusteActual.detalle.length,
                        'Total Unidades:': this.calcularTotalUnidades(),
                        'TOTAL GENERAL:': this.formatToUSD(this.calcularTotal())
                    },

                    // Pie de página
                    piePagina: `Generado el ${this.formatearFechaHoraActual()}`
                };

                const resultado = exporter.exportar(config);

                if (resultado.success) {
                    sweet_msg_toast('success', resultado.message);
                } else {
                    sweet_msg_toast('error', resultado.message);
                }
            },
            exportarPDF_() {
                const pdfExporter = new PDFCapture();

                const config = {
                    elementoId: 'detalleAjusteModal', // ← ID del contenedor
                    nombreArchivo: `Ajuste_${this.ajusteActual.ajen_secuencial}`,
                    mostrarCargando: true, // ← Mostrar spinner
                    orientacion: 'portrait',
                    formato: 'a4',
                    margenes: [5, 5, 5, 5]
                };

                // Opción 1: Con html2canvas + jsPDF (Mejor calidad)
                pdfExporter.exportar(config).then(resultado => {
                    if (resultado.success) {
                        this.mostrarNotificacion(resultado.message, 'success');
                    } else {
                        this.mostrarNotificacion(resultado.message, 'error');
                    }
                });

                // Opción 2: Con html2pdf.js (Más simple)
                // pdfExporter.exportarConHtml2PDF(config);
            },

            zFill(value, size) {
                return zFill(value, size);
            }

        }
    })
    window.appGestionAje.mount('#app');
</script>

