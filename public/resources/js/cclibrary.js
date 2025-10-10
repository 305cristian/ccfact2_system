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

    if (status === "success") {
        colorIcon = '#11b682';
        title = '! OK';
    }
    if (status === "error") {
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

