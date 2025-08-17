<?php
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of viewPerfil
 * @author Cristian R. Paz
 * @Date 31 ago. 2023
 * @Time 15:21:44
 */
?>

<div class="modal-header">
    <h5 class="modal-title" ><i class="fal fa-user-tie fa-2x"></i> Datos del empleado</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form>
        <div class="col-lg-12 d-flex">

            <div class="col-lg-6">
                <div class="mb-3">
                    <label for="nombre" class="col-form-label col-form-label-sm"><i class="fal fa-user-tie"></i> Nombres</label>
                    <input v-model="dataEmpleado.nombre" type="text" class="form-control" id="nombre" placeholder="Ingrese sus nombres" />
                </div>

                <div class="mb-3">
                    <label for="apellido" class="col-form-label col-form-label-sm"><i class="fal fa-user-tie"></i> Apellidos</label>
                    <input  v-model="dataEmpleado.apellido" type="text" class="form-control" id="apellido" placeholder="Ingrese sus apellidos" />
                </div>

                <div class="mb-3">
                    <label for="correo" class="col-form-label col-form-label-sm"><i class="fal fa-inbox"></i> Correo</label>
                    <input v-model="dataEmpleado.email" type="email" class="form-control" id="correo" placeholder="Ingrese su correo electrónico" />
                </div>

                <div class="mb-3">
                    <label for="celular" class="col-form-label col-form-label-sm"><i class="fal fa-phone"></i> Teléfono Celular</label>
                    <input  v-model="dataEmpleado.celular" type="number" class="form-control" id="celular" placeholder="Ingrese su número de celular" />
                </div>

                <div class="mb-3">
                    <label for="usuario" class="col-form-label col-form-label-sm"><i class="fal fa-user"></i> Usuario</label>
                    <input  v-model="dataEmpleado.usuario" type="text" class="form-control" id="usuario" placeholder="Ingrese su nombre de usuario" autocomplete="current-password"/>
                </div>

                <button @click="updateEmployee()" type="button" class="btn btn-system" ><i class="fas fa-refresh"></i> Actualizar</button>

            </div>
            <div class="col-lg-6">

                <div class="mb-3">
                    <label for="contrasenia" class="col-form-label col-form-label-sm"><i class="fal fa-key"></i> Contraseña actual</label>
                    <input v-model="passActual" type="password" class="form-control" id="contrasenia" placeholder="Ingrese su contraseña actual" autocomplete="current-password"/>
                </div>
                <div class="mb-3">
                    <label for="newContrasenia" class="col-form-label col-form-label-sm"><i class="fal fa-key-skeleton"></i> Nueva Contraseña</label>
                    <input v-model="passNew" type="password" class="form-control" id="newContrasenia" placeholder="Ingrese su nueva contraseña" autocomplete="current-password"/>
                </div>
                <div class="mb-3">
                    <label for="newContrasenia2" class="col-form-label col-form-label-sm"><i class="fal fa-key-skeleton"></i> Confirme nueva Contraseña</label>
                    <input v-model="passConfNew" type="password" class="form-control" id="newContrasenia2" placeholder="Confirme su nueva contraseña" autocomplete="current-password"/>
                </div>

                <button @click="resetPassword()" type="button" class="btn btn-system" ><i class="fas fa-refresh"></i> Resetear</button>
            </div>

        </div>
    </form>
</div>
<div class="modal-footer">
    <!--<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>-->
</div>

