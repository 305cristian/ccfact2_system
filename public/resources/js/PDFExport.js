/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */


/**
 * Librería para exportar datos a PDF con texto seleccionable
 * Usa jsPDF + jsPDF-AutoTable
 * Autor: Cristian R. Paz
 * Fecha: 2025-10-26
 */

class PDFExporter {
    
    constructor() {
        // Verificar si jsPDF está cargado
        if (typeof window.jspdf === 'undefined') {
            throw new Error('jsPDF no está cargado. Incluye: <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>');
        }
        if (typeof window.jspdf.jsPDF === 'undefined') {
            throw new Error('jsPDF no está correctamente inicializado');
        }
    }

    /**
     * Exportar datos a PDF
     * @param {Object} config - Configuración de exportación
     */
    exportar(config) {
        try {
            // Validar configuración
            this._validarConfig(config);

            // Crear instancia de jsPDF
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({
                orientation: config.orientacion || 'portrait',
                unit: 'mm',
                format: config.formato || 'a4'
            });

            // Variables de posición
            let y = 20; // Posición Y inicial

            // 1. ENCABEZADO DE LA EMPRESA
            if (config.empresa) {
                y = this._agregarEncabezadoEmpresa(doc, config.empresa, y);
            }

            // 2. TÍTULO DEL DOCUMENTO
            if (config.titulo) {
                y = this._agregarTitulo(doc, config.titulo, y);
            }

            // 3. INFORMACIÓN GENERAL (Datos del documento)
            if (config.informacion) {
                y = this._agregarInformacion(doc, config.informacion, y);
            }

            // 4. TABLA DE DATOS
            if (config.columnas && config.datos) {
                y = this._agregarTabla(doc, config, y);
            }

            // 5. TOTALES
            if (config.totales) {
                y = this._agregarTotales(doc, config.totales, y);
            }

            // 6. PIE DE PÁGINA
            if (config.piePagina) {
                this._agregarPiePagina(doc, config.piePagina);
            }

            // Numerar páginas
            this._numerarPaginas(doc);

            // Guardar PDF
            const nombreArchivo = `${config.nombreArchivo || 'documento'}.pdf`;
            doc.save(nombreArchivo);

            return { success: true, message: 'PDF generado exitosamente' };

        } catch (error) {
            console.error('Error al exportar PDF:', error);
            return { success: false, message: error.message };
        }
    }

    /**
     * Validar configuración
     */
    _validarConfig(config) {
        if (!config.nombreArchivo) {
            throw new Error('El nombre del archivo es requerido');
        }
    }

    /**
     * Agregar encabezado de la empresa
     */
    _agregarEncabezadoEmpresa(doc, empresa, y) {
        const pageWidth = doc.internal.pageSize.getWidth();
        
        // Logo o icono (simulado con texto)
        doc.setFontSize(10);
        doc.setTextColor(100, 100, 100);
        
        if (empresa.logo) {
            // Si tienes logo en base64, puedes agregarlo así:
            // doc.addImage(empresa.logo, 'PNG', 15, y, 30, 30);
            doc.rect(15, y, 30, 30); // Rectángulo simulando logo
            doc.text('LOGO', 25, y + 18);
        }

        // Datos de la empresa
        doc.setFontSize(14);
        doc.setFont(undefined, 'bold');
        doc.setTextColor(13, 110, 253); // Color primary Bootstrap
        doc.text(empresa.nombre || 'TU EMPRESA S.A.', 50, y + 8);
        
        doc.setFontSize(9);
        doc.setFont(undefined, 'normal');
        doc.setTextColor(100, 100, 100);
        
        if (empresa.ruc) doc.text(`RUC: ${empresa.ruc}`, 50, y + 14);
        if (empresa.direccion) doc.text(empresa.direccion, 50, y + 19);
        if (empresa.telefono) doc.text(`Tel: ${empresa.telefono}`, 50, y + 24);
        if (empresa.email) doc.text(`Email: ${empresa.email}`, 50, y + 29);

        // Línea separadora
        doc.setDrawColor(13, 110, 253);
        doc.setLineWidth(0.5);
        doc.line(15, y + 35, pageWidth - 15, y + 35);

        return y + 40;
    }

    /**
     * Agregar título del documento
     */
    _agregarTitulo(doc, titulo, y) {
        const pageWidth = doc.internal.pageSize.getWidth();
        
        doc.setFontSize(16);
        doc.setFont(undefined, 'bold');
        doc.setTextColor(13, 110, 253);
        
        const textoWidth = doc.getTextWidth(titulo);
        const x = (pageWidth - textoWidth) / 2;
        
        doc.text(titulo, x, y);
        
        return y + 10;
    }

    /**
     * Agregar información general del documento
     */
    _agregarInformacion(doc, informacion, y) {
        const pageWidth = doc.internal.pageSize.getWidth();
        const colWidth = (pageWidth - 30) / 2;
        
        doc.setFontSize(9);
        doc.setFont(undefined, 'normal');
        
        // Fondo gris claro para la sección de información
        doc.setFillColor(248, 249, 250);
        doc.rect(15, y - 5, pageWidth - 30, Object.keys(informacion).length * 6 + 5, 'F');
        
        let col = 0;
        let row = 0;
        
        Object.entries(informacion).forEach(([key, value]) => {
            const x = 20 + (col * colWidth);
            const yPos = y + (row * 6);
            
            // Clave en negrita
            doc.setFont(undefined, 'bold');
            doc.setTextColor(60, 60, 60);
            doc.text(`${key}`, x, yPos);
            
            // Valor normal
            doc.setFont(undefined, 'normal');
            doc.setTextColor(100, 100, 100);
            const valorX = x + doc.getTextWidth(`${key} `) + 2;
            doc.text(String(value), valorX, yPos);
            
            // Alternar entre columnas
            col++;
            if (col >= 2) {
                col = 0;
                row++;
            }
        });
        
        return y + (Math.ceil(Object.keys(informacion).length / 2) * 6) + 10;
    }

    /**
     * Agregar tabla de datos con autoTable
     */
    _agregarTabla(doc, config, y) {
        // Preparar encabezados
        const headers = config.columnas.map(col => col.titulo || col.campo);
        
        // Preparar datos
        const body = config.datos.map(fila => {
            return config.columnas.map(col => {
                let valor = this._obtenerValorAnidado(fila, col.campo);
                
                // Aplicar formato si existe
                if (col.formato && valor !== null && valor !== undefined) {
                    valor = this._aplicarFormato(valor, col.formato, col.formatoConfig);
                }
                
                return valor !== null && valor !== undefined ? valor : '-';
            });
        });

        // Configurar estilos de la tabla
        const columnStyles = {};
        config.columnas.forEach((col, index) => {
            columnStyles[index] = {
                cellWidth: col.ancho || 'auto',
                halign: col.alineacion || (col.formato === 'moneda' || col.formato === 'numero' ? 'right' : 'left')
            };
        });

        // Generar tabla con autoTable
        doc.autoTable({
            startY: y,
            head: [headers],
            body: body,
            theme: 'grid',
            styles: {
                fontSize: 8,
                cellPadding: 3,
                lineColor: [220, 220, 220],
                lineWidth: 0.1
            },
            headStyles: {
                fillColor: [13, 110, 253],
                textColor: [255, 255, 255],
                fontStyle: 'bold',
                halign: 'center'
            },
            alternateRowStyles: {
                fillColor: [248, 249, 250]
            },
            columnStyles: columnStyles,
            margin: { left: 15, right: 15 }
        });

        return doc.lastAutoTable.finalY + 10;
    }

    /**
     * Agregar totales
     */
    _agregarTotales(doc, totales, y) {
        const pageWidth = doc.internal.pageSize.getWidth();
        
        // Fondo para totales
        doc.setFillColor(13, 110, 253);
        doc.setDrawColor(13, 110, 253);
        doc.roundedRect(pageWidth - 80, y - 3, 65, 25, 2, 2, 'FD');
        
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(9);
        doc.setFont(undefined, 'normal');
        
        let yPos = y + 3;
        Object.entries(totales).forEach(([key, value]) => {
            doc.text(`${key}`, pageWidth - 75, yPos);
            
            doc.setFont(undefined, 'bold');
            doc.setFontSize(key.includes('TOTAL') ? 12 : 10);
            doc.text(String(value), pageWidth - 20, yPos, { align: 'right' });
            
            doc.setFont(undefined, 'normal');
            doc.setFontSize(9);
            yPos += 6;
        });
        
        return yPos + 5;
    }

    /**
     * Agregar pie de página
     */
    _agregarPiePagina(doc, piePagina) {
        const pageHeight = doc.internal.pageSize.getHeight();
        const pageWidth = doc.internal.pageSize.getWidth();
        
        doc.setFontSize(8);
        doc.setFont(undefined, 'italic');
        doc.setTextColor(150, 150, 150);
        
        const texto = typeof piePagina === 'string' ? piePagina : `Generado el ${new Date().toLocaleString('es-EC')}`;
        const textoWidth = doc.getTextWidth(texto);
        
        doc.text(texto, (pageWidth - textoWidth) / 2, pageHeight - 10);
    }

    /**
     * Numerar páginas
     */
    _numerarPaginas(doc) {
        const pageCount = doc.internal.getNumberOfPages();
        const pageHeight = doc.internal.pageSize.getHeight();
        const pageWidth = doc.internal.pageSize.getWidth();
        
        doc.setFontSize(8);
        doc.setTextColor(150, 150, 150);
        
        for (let i = 1; i <= pageCount; i++) {
            doc.setPage(i);
            const texto = `Página ${i} de ${pageCount}`;
            doc.text(texto, pageWidth - 30, pageHeight - 10);
        }
    }

    /**
     * Obtener valor anidado
     */
    _obtenerValorAnidado(obj, path) {
        return path.split('.').reduce((acc, part) => acc && acc[part], obj);
    }

    /**
     * Aplicar formato
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

    _formatearMoneda(valor, config = {}) {
        const simbolo = config.simbolo || '$';
        const decimales = config.decimales !== undefined ? config.decimales : 2;
        return `${simbolo}${parseFloat(valor).toFixed(decimales)}`;
    }

    _formatearFecha(valor, config = {}) {
        if (!valor) return '-';
        const fecha = new Date(valor);
        const locale = config.locale || 'es-EC';
        return fecha.toLocaleDateString(locale);
    }

    _formatearNumero(valor, config = {}) {
        const decimales = config.decimales !== undefined ? config.decimales : 0;
        return parseFloat(valor).toFixed(decimales);
    }

    _formatearPorcentaje(valor, config = {}) {
        const decimales = config.decimales !== undefined ? config.decimales : 2;
        return `${(parseFloat(valor) * 100).toFixed(decimales)}%`;
    }
}

// Exportar para uso global
if (typeof window !== 'undefined') {
    window.PDFExporter = PDFExporter;
}