/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */

/**
 * Description of viewEmployee
 * @author Cristian R. Paz
 * @Date 28 sep. 2023
 * @Time 12:45:10
 */



function zFill(number, width) {
    var numberOutput = Math.abs(number);
    var length = number.toString().length;
    var zero = "0";

    if (width <= length) {
        if (number < 0) {
            return ("-" + numberOutput.toString());
        } else {
            return numberOutput.toString();
        }
    } else {
        if (number < 0) {
            return ("-" + (zero.repeat(width - length)) + numberOutput.toString());
        } else {
            return ((zero.repeat(width - length)) + numberOutput.toString());
        }
    }
}
function sweet_msg_toast(status, msg) {

    let msg_txt = '<strong style="font-size: 13px">' + msg + '</strong>';

    let colorBackground, colorIcon = '#fff', position = "bottom", borderColor = "#fff", colorText = "#fff";

    switch (status) {
        case 'success':
            colorIcon = '#00C851';      // verde éxito
            colorBackground = '#ecfdf5'; // verde oscuro fondo
            borderColor = '#10b981';
            colorText = '#00C851';
            position = "bottom-end";
            break;

        case 'error':
            colorIcon = '#8B0836';      // rojo error
            colorBackground = '#FF6467'; // rojo oscuro fondo
            borderColor = '#FB2C36';
            colorText = '#8B0836';
            position = "bottom";
            break;

        case 'info':
            colorIcon = '#33b5e5';      // azul informativo
            colorBackground = '#B8E6FE'; // azul oscuro fondo
            borderColor = '#33b5e5';
            colorText = '#2C92B8';
            position = "bottom";
            break;

        case 'warning':
            colorIcon = '#ffbb33';      // amarillo advertencia
            colorBackground = '#fffbeb'; // amarillo oscuro fondo
            borderColor = '#ffbb33';
            colorText = '#FF8904';
            position = "bottom";
            break;

        default:
            colorIcon = '#cccccc';
            colorBackground = '#1e1e1e';
    }
    const Toast = Swal.mixin({
        toast: true,
        showClass: {
            popup: 'swal2-show'
        },
        hideClass: {
            popup: 'swal2-hide'
        },
        position: position,
        showConfirmButton: false,
        timer: 3000,
        iconColor: colorIcon,
        background: colorBackground,
        timerProgressBar: true,
        color: colorText,
        didOpen: (toast) => {
            toast.style.border = '3px solid ' + borderColor;   // 👈 borde personalizado
            toast.style.borderRadius = '12px';          // esquinas redondeadas
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    Toast.fire({
        icon: status,
        title: msg_txt
    });
}
function sweet_msg_dialog(status, msg, ruta = '', e = '') {

    var msg_txt = '<h5>' + msg + '</h5>';
    var title = '! Atención';
    var colorIcon = '#fff';
    var size = '40%';

    colorIcon = '#f27474';

    if (status === "success") {
        colorIcon = '#11b682';
        title = '! OK';
    }

    if (status === "error" && msg === '') {
        colorIcon = '#f27474';
        msg_txt = '<h5>Se ha detectado un fallo al procesar con la base de datos Comuniquese con el administrador del sistema ' + ' ' + e + ' <h5>';
        size = '30%';
    }
    if (status === "info") {
        colorIcon = '#5fb6fd';
    }
    if (status === "warning") {
        colorIcon = '#FFCC00';
    }
    Swal.fire({
        iconColor: colorIcon,
        title: title,
        html: msg_txt,
        icon: status,
        width: size,
        confirmButtonColor: '#3085d6',
        confirmButtonText: '<i class="fas fa-check-double"></i> Aceptar!',
        allowOutsideClick: false
    }).then((result) => {
        if (status === "success" && ruta !== '') {
            window.location.href = siteUrl + ruta;
        }
    });

}
function _loading() {
    window.axios.interceptors.request.use((config) => {
        document.body.classList.add('loading-indicator');
        return config;

    }, (error) => {
        return Promise.reject(error);
    })

    window.axios.interceptors.response.use((response) => {
        document.body.classList.remove('loading-indicator');
        return response;
    }, function (error) {
        return Promise.reject(error);
    })
}

function  _loading_upd() {

    var content = document.getElementById('loading-screen-upd');

    window.axios.interceptors.request.use((config) => {
        content.style.display = 'block';
        return config;
    }, (error) => {
        return Promise.reject(error);
    });

    window.axios.interceptors.response.use((response) => {
        content.style.display = 'none';
        return response;
    }, function (error) {
        return Promise.reject(error);
    });
}

function swalLoading(title, text) {

    var titulo = "Procesando...";
    var texto = "<h5>Procesando su solicitado, espere por favor...</h5>";

    if (title) {
        titulo = title;
    }
    if (text) {
        texto = text;
    }

    Swal.fire({
        title: titulo,
        html: texto,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

function formatearFechaHoraActual() {
    const ahora = new Date();
    return ahora.toLocaleString('es-EC', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatearFecha(fecha) {
    if (!fecha)
        return '-';
    const date = new Date(fecha);
    return date.toLocaleDateString('es-EC', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
}

function formatToUSD(value) {
    if (isNaN(value))
        return '$0.00';
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2
    }).format(value);
}

function calcularTotalUnidades(dataDetalle) {
    if (!dataDetalle)
        return 0;
    return dataDetalle.reduce((sum, item) =>
        sum + parseInt(item.ajend_itemcantidad || 0), 0
            );
}

function generarExcel(constentExport, title, ruta) {
    try {

        if (!constentExport) {
            sweet_msg_toast('error', 'No se encontró el contenido del reporte');
            return;
        }

        // Clonar el contenido
        const clone = constentExport.cloneNode(true);

        // Obtener HTML limpio
        let htmlExport = clone.innerHTML;

//        // Agregar estilos inline para Excel (opcional pero recomendado)
//        htmlExport = `
//            <html xmlns:o="urn:schemas-microsoft-com:office:office" 
//                  xmlns:x="urn:schemas-microsoft-com:office:excel" 
//                  xmlns="http://www.w3.org/TR/REC-html40">
//            <head>
//                <meta charset="UTF-8">
//                <style>
//                    table { border-collapse: collapse; width: 100%; }
//                    th, td { border: 1px solid #000; padding: 8px; text-align: left; }
//                    th { background-color: #e9ecef; font-weight: bold; }
//                    .text-center { text-align: center; }
//                    .text-end { text-align: right; }
//                    .fw-bold { font-weight: bold; }
//                    .bg-light { background-color: #f8f9fa; }
//                    .bg-warning { background-color: #ffc107; }
//                    .bg-success { background-color: #198754; color: white; }
//                    .bg-danger { background-color: #dc3545; color: white; }
//                    .badge { padding: 4px 8px; border-radius: 4px; }
//                </style>
//            </head>
//            <body>
//                ${htmlExport}
//            </body>
//            </html>
//        `;

        // Crear formulario para enviar los datos
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = ruta;
        form.target = '_blank'; // Opcional: abrir en nueva pestaña

        // Campo: título del documento
        const inputTitle = document.createElement('input');
        inputTitle.type = 'hidden';
        inputTitle.name = 'titleDoc';
        inputTitle.value = title;
        form.appendChild(inputTitle);

        // Campo: datos HTML
        const inputData = document.createElement('input');
        inputData.type = 'hidden';
        inputData.name = 'dataHtml';
        inputData.value = htmlExport;
        form.appendChild(inputData);

        // Agregar formulario al DOM y enviarlo
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);


    } catch (error) {
        sweet_msg_toast('error', 'Error al generar Excel: ' + error.message);
    }

}





