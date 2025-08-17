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

    let msg_txt = '<strong style="font-size: 13px; color: black">' + msg + '</strong>';

    let colorBackground, colorIcon = '#fff', position = "bottom-center";

    if (status == "success")
        colorIcon = '#11b682';
    colorBackground = 'black';

    if (status == "error")
        colorIcon = '#f27474';
    colorBackground = 'black';

    if (status == "info")
        colorIcon = '#5fb6fd';
    colorBackground = 'black';

    if (status == "warning")
        colorIcon = '#FFCC00';
    colorBackground = 'black';

    const Toast = Swal.mixin({
        toast: true,
        animation: true,
        position: position,
        showConfirmButton: false,
        timer: 4000,
        iconColor: '#000',
        background: colorIcon,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    })
    Toast.fire({
        icon: status,
        title: msg_txt
    })
}
function sweet_msg_dialog(status, msg, ruta = '', e = '') {

    var msg_txt = '<h5>' + msg + '</h5>';
    var title = '! Atención';
    var colorIcon = '#fff';
    var size = '40%';

    if (status == "success") {
        colorIcon = '#11b682';
        title = '! OK';
    }
    if (status == "error") {
        colorIcon = '#f27474';
        msg_txt = '<h5>Se ha detectado un fallo al procesar con la base de datos Comuniquese con el administrador del sistema ' + ' ' + e + ' <h5>';
        size = '30%';
    }
    if (status == "info") {
        colorIcon = '#5fb6fd';
    }
    if (status == "warning") {
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
            window.location.href = baseUrl + ruta;
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
