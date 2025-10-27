/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */


/**
 * Librería para exportar datos a Excel de forma reutilizable
 * Autor: Cristian R.Paz
 * Fecha: 2025-10-26
 */

class ExcelExport {

    constructor() {
        // Verificar si SheetJS está cargado
        if (typeof XLSX === 'undefined') {
            throw new Error('SheetJS no está cargado. Incluye: <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>');
        }
    }

    /**
     * Exportar datos a Excel en un solo libro
     * @param {Object} config - Configuración de exportación
     * @param {String} config.nombreArchivo - Nombre del archivo (sin extensión)
     * @param {String} config.titulo - Título del documento
     * @param {Object} config.encabezado - Datos del encabezado
     * @param {Array} config.columnas - Definición de columnas
     * @param {Array} config.datos - Array de datos a exportar
     * @param {Object} config.totales - Objeto con totales (opcional)
     * @param {Object} config.estilos - Configuración de estilos (opcional)
     */
    exportar(config) {
        try {
            // Validar configuración
            this._validarConfig(config);

            // Construir el contenido
            const contenido = this._construirContenido(config);

            // Crear workbook
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(contenido);

            // Aplicar estilos y anchos de columna
            this._aplicarEstilos(ws, config);

            // Agregar hoja al workbook
            const nombreHoja = config.nombreHoja || 'Datos';
            XLSX.utils.book_append_sheet(wb, ws, nombreHoja);

            // Descargar archivo
            const nombreCompleto = `${config.nombreArchivo}.xlsx`;
            XLSX.writeFile(wb, nombreCompleto);

            return {success: true, message: 'Archivo Excel generado exitosamente'};

        } catch (error) {
            console.error('Error al exportar Excel:', error);
            return {success: false, message: error.message};
        }
    }

    /**
     * Validar configuración
     */
    _validarConfig(config) {
        if (!config.nombreArchivo) {
            throw new Error('El nombre del archivo es requerido');
        }
        if (!config.columnas || !Array.isArray(config.columnas)) {
            throw new Error('Las columnas son requeridas y deben ser un array');
        }
        if (!config.datos || !Array.isArray(config.datos)) {
            throw new Error('Los datos son requeridos y deben ser un array');
        }
    }

    /**
     * Construir el contenido del Excel
     */
    _construirContenido(config) {
        const contenido = [];
        let filaActual = 0;

        // 1. TÍTULO PRINCIPAL
        if (config.titulo) {
            contenido.push([config.titulo]);
            contenido.push([]); // Fila vacía
            filaActual += 2;
        }

        // 2. ENCABEZADO (información general)
        if (config.encabezado) {
            Object.entries(config.encabezado).forEach(([key, value]) => {
                contenido.push([key, value]);
            });
            contenido.push([]); // Fila vacía
        }

        // 3. ENCABEZADOS DE COLUMNAS
        const nombresColumnas = config.columnas.map(col => col.titulo || col.campo);
        contenido.push(nombresColumnas);

        // 4. DATOS
        config.datos.forEach((fila) => {
            const filaData = config.columnas.map(col => {
                let valor = this._obtenerValorAnidado(fila, col.campo);

                // Aplicar formato si existe
                if (col.formato && valor !== null && valor !== undefined) {
                    valor = this._aplicarFormato(valor, col.formato, col.formatoConfig);
                }

                return valor !== null && valor !== undefined ? valor : '-';
            });
            contenido.push(filaData);
        });

        // 5. TOTALES (opcional)
        if (config.totales) {
            contenido.push([]); // Fila vacía
            const filaTotales = this._construirFilaTotales(config.columnas, config.totales);
            contenido.push(filaTotales);
        }

        // 6. PIE DE PÁGINA (opcional)
        if (config.piePagina) {
            contenido.push([]);
            contenido.push([config.piePagina]);
        }

        return contenido;
    }

    /**
     * Obtener valor anidado de un objeto (ej: "producto.nombre")
     */
    _obtenerValorAnidado(obj, path) {
        return path.split('.').reduce((acc, part) => acc && acc[part], obj);
    }

    /**
     * Aplicar formato a un valor
     */
    _aplicarFormato(valor, formato, config = {}) {
        switch (formato) {
            case 'moneda':
                return this._formatearMoneda(valor, config);
            case 'fecha':
                return this._formatearFecha(valor, config);
            case 'numero':
                return this._formatearNumero(valor, config);
            case 'porcentaje':
                return this._formatearPorcentaje(valor, config);
            default:
                return valor;
    }
    }

    /**
     * Formatear moneda
     */
    _formatearMoneda(valor, config = {}) {
        const simbolo = config.simbolo || '$';
        const decimales = config.decimales !== undefined ? config.decimales : 2;
        return parseFloat(valor).toFixed(decimales);
    }

    /**
     * Formatear fecha
     */
    _formatearFecha(valor, config = {}) {
        if (!valor)
            return '-';
        const fecha = new Date(valor);
        const locale = config.locale || 'es-EC';
        const opciones = config.opciones || {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        };
        return fecha.toLocaleDateString(locale, opciones);
    }

    /**
     * Formatear número
     */
    _formatearNumero(valor, config = {}) {
        const decimales = config.decimales !== undefined ? config.decimales : 0;
        return parseFloat(valor).toFixed(decimales);
    }

    /**
     * Formatear porcentaje
     */
    _formatearPorcentaje(valor, config = {}) {
        const decimales = config.decimales !== undefined ? config.decimales : 2;
        return `${(parseFloat(valor) * 100).toFixed(decimales)}%`;
    }

    /**
     * Construir fila de totales
     */
    _construirFilaTotales(columnas, totales) {
        return columnas.map(col => {
            if (totales[col.campo] !== undefined) {
                return totales[col.campo];
            }
            if (col.esTotal) {
                return 'TOTAL:';
            }
            return '';
        });
    }

    /**
     * Aplicar estilos al worksheet
     */
    _aplicarEstilos(ws, config) {
        // Configurar anchos de columna
        if (config.columnas) {
            ws['!cols'] = config.columnas.map(col => ({
                    wch: col.ancho || 15
                }));
        }

        // Configurar altura de filas (opcional)
        if (config.alturaFilas) {
            ws['!rows'] = config.alturaFilas.map(altura => ({
                    hpt: altura
                }));
        }
    }

    /**
     * Exportar múltiples hojas en un solo libro
     */
    exportarMultipleHojas(config) {
        try {
            if (!config.hojas || !Array.isArray(config.hojas)) {
                throw new Error('Se requiere un array de hojas');
            }

            const wb = XLSX.utils.book_new();

            config.hojas.forEach((hoja) => {
                const contenido = this._construirContenido(hoja);
                const ws = XLSX.utils.aoa_to_sheet(contenido);
                this._aplicarEstilos(ws, hoja);

                const nombreHoja = hoja.nombreHoja || 'Hoja';
                XLSX.utils.book_append_sheet(wb, ws, nombreHoja);
            });

            const nombreCompleto = `${config.nombreArchivo}.xlsx`;
            XLSX.writeFile(wb, nombreCompleto);

            return {success: true, message: 'Archivo Excel generado exitosamente'};

        } catch (error) {
            console.error('Error al exportar Excel:', error);
            return {success: false, message: error.message};
        }
    }
}

// Exportar la clase para uso global
if (typeof window !== 'undefined') {
    window.ExcelExport = ExcelExport;
}