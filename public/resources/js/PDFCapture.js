/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */


/**
 * Librería para capturar HTML y exportar a PDF de alta calidad
 * Mantiene el diseño exacto del reporte
 * Autor: Cristian R. Paz
 * Fecha: 2025-10-2025
 */

class PDFCapture {
    
    constructor() {
        if (typeof html2canvas === 'undefined') {
            throw new Error('html2canvas no está cargado');
        }
        if (typeof window.jspdf === 'undefined') {
            throw new Error('jsPDF no está cargado');
        }
    }

    /**
     * Capturar elemento HTML y exportar a PDF de alta calidad
     * @param {Object} config - Configuración
     */
    async exportar(config) {
        try {
            // Validar configuración
            if (!config.elementoId) {
                throw new Error('El ID del elemento a capturar es requerido');
            }

            const elemento = document.getElementById(config.elementoId);
            if (!elemento) {
                throw new Error(`No se encontró el elemento con ID: ${config.elementoId}`);
            }

            // Mostrar indicador de carga
            if (config.mostrarCargando) {
                this._mostrarCargando();
            }

            // Preparar elemento para captura
            this._prepararElemento(elemento);

            // Configuración de html2canvas (MEJORADA)
            const canvas = await html2canvas(elemento, {
                scale: 3, // ← Mayor escala = mejor calidad
                useCORS: true,
                logging: false,
                backgroundColor: '#ffffff',
                allowTaint: true,
                letterRendering: true, // ← Mejora renderizado de texto
                imageTimeout: 0,
                removeContainer: true,
                scrollY: -window.scrollY,
                scrollX: -window.scrollX,
                windowWidth: elemento.scrollWidth,
                windowHeight: elemento.scrollHeight
            });

            // Restaurar elemento
            this._restaurarElemento(elemento);

            // Crear PDF
            const { jsPDF } = window.jspdf;
            
            // Calcular dimensiones
            const imgWidth = 210; // A4 en mm
            const pageHeight = 297; // A4 en mm
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            
            let heightLeft = imgHeight;
            let position = 0;

            // Crear documento
            const pdf = new jsPDF({
                orientation: imgHeight > imgWidth ? 'portrait' : 'landscape',
                unit: 'mm',
                format: 'a4',
                compress: true
            });

            // Convertir canvas a imagen
            const imgData = canvas.toDataURL('image/jpeg', 0.95);

            // Agregar imagen al PDF (con paginación si es necesario)
            pdf.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight, undefined, 'FAST');
            heightLeft -= pageHeight;

            // Si el contenido es más largo, agregar páginas
            while (heightLeft >= 0) {
                position = heightLeft - imgHeight;
                pdf.addPage();
                pdf.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight, undefined, 'FAST');
                heightLeft -= pageHeight;
            }

            // Guardar PDF
            const nombreArchivo = `${config.nombreArchivo || 'documento'}.pdf`;
            pdf.save(nombreArchivo);

            // Ocultar indicador de carga
            if (config.mostrarCargando) {
                this._ocultarCargando();
            }

            return { success: true, message: 'PDF generado exitosamente' };

        } catch (error) {
            console.error('Error al exportar PDF:', error);
            this._ocultarCargando();
            return { success: false, message: error.message };
        }
    }

    /**
     * Preparar elemento para mejor captura
     */
    _prepararElemento(elemento) {
        // Guardar estilos originales
        elemento._originalStyles = {
            transform: elemento.style.transform,
            position: elemento.style.position,
            left: elemento.style.left,
            top: elemento.style.top
        };

        // Aplicar estilos para mejor captura
        elemento.style.transform = 'scale(1)';
        elemento.style.position = 'relative';
        elemento.style.left = '0';
        elemento.style.top = '0';
    }

    /**
     * Restaurar elemento
     */
    _restaurarElemento(elemento) {
        if (elemento._originalStyles) {
            Object.assign(elemento.style, elemento._originalStyles);
            delete elemento._originalStyles;
        }
    }

    /**
     * Mostrar indicador de carga
     */
    _mostrarCargando() {
        const loading = document.createElement('div');
        loading.id = 'pdf-loading-overlay';
        loading.innerHTML = `
            <div style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.7);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                color: white;
                font-family: Arial, sans-serif;
            ">
                <div style="text-align: center;">
                    <div style="
                        border: 4px solid #f3f3f3;
                        border-top: 4px solid #3498db;
                        border-radius: 50%;
                        width: 50px;
                        height: 50px;
                        animation: spin 1s linear infinite;
                        margin: 0 auto 20px;
                    "></div>
                    <p style="font-size: 18px; margin: 0;">Generando PDF...</p>
                    <p style="font-size: 14px; margin: 5px 0 0; opacity: 0.8;">Por favor espere</p>
                </div>
            </div>
            <style>
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>
        `;
        document.body.appendChild(loading);
    }

    /**
     * Ocultar indicador de carga
     */
    _ocultarCargando() {
        const loading = document.getElementById('pdf-loading-overlay');
        if (loading) {
            loading.remove();
        }
    }

    /**
     * Exportar con mejor calidad (alternativa usando html2pdf.js)
     */
    async exportarConHtml2PDF(config) {
        try {
            if (typeof html2pdf === 'undefined') {
                throw new Error('html2pdf.js no está cargado');
            }

            const elemento = document.getElementById(config.elementoId);
            if (!elemento) {
                throw new Error(`No se encontró el elemento: ${config.elementoId}`);
            }

            const opciones = {
                margin: config.margenes || [5, 5, 5, 5],
                filename: `${config.nombreArchivo || 'documento'}.pdf`,
                image: { 
                    type: 'jpeg', 
                    quality: 0.98 
                },
                html2canvas: { 
                    scale: 3, // ← Máxima calidad
                    useCORS: true,
                    logging: false,
                    letterRendering: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff'
                },
                jsPDF: { 
                    unit: 'mm', 
                    format: config.formato || 'a4', 
                    orientation: config.orientacion || 'portrait',
                    compress: true
                },
                pagebreak: { 
                    mode: ['avoid-all', 'css', 'legacy'],
                    before: '.page-break-before',
                    after: '.page-break-after',
                    avoid: ['tr', '.no-break']
                }
            };

            if (config.mostrarCargando) {
                this._mostrarCargando();
            }

            await html2pdf()
                .set(opciones)
                .from(elemento)
                .save();

            if (config.mostrarCargando) {
                this._ocultarCargando();
            }

            return { success: true, message: 'PDF generado exitosamente' };

        } catch (error) {
            console.error('Error:', error);
            this._ocultarCargando();
            return { success: false, message: error.message };
        }
    }
}

// Exportar para uso global
if (typeof window !== 'undefined') {
    window.PDFCapture = PDFCapture;
}