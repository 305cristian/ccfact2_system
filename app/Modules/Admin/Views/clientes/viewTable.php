<!DOCTYPE html>
<!--
/**
 * Description of viewTable
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 29 ago 2024
 * @time 4:09:15 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<div class="table-responsive">
    <table id="tblClientes" class="table table-striped nowrap display" style="width: 100%">
        <thead class="bg-system text-white">
            <tr>
                <td>ID</td>
                <td>CI/RUC</td>
                <td>DOCUMENTO</td>
                <td>NOMBRES</td>
                <td>APELLIDOS</td>
                <td>RAZON SOCIAL</td>
                <td>SEXO</td>
                <td>TELÉFONO</td>
                <td>CELULAR</td>
                <td>EMAIL</td>
                <td>DIRECCIÓN</td>                     
                <td>PROVINCIA</td>
                <td>CANTÓN</td>
                <td>PARROQUIA</td>
                <td>ESTADO</td>           
                <td>ACCIONES</td>
            </tr>
        </thead>

        <tbody>
            <tr v-for="lc of listaClientes">
                <td>{{zFill(lc.id)}}</td>
                <td>{{lc.clie_dni}}</td>
                <td>{{lc.doc_nombre}}</td>
                <td>{{lc.clie_nombres}}</td>
                <td>{{lc.clie_apellidos}}</td>                     
                <td>{{lc.clie_razon_social}}</td>
                <td>{{lc.clie_sexo}}</td>
                <td>{{lc.clie_telefono}}</td>
                <td>{{lc.clie_celular}}</td>
                <td>{{lc.clie_email}}</td>
                <td>{{lc.clie_direccion}}</td>
                <td>{{lc.prv_nombre}}</td>
                <td>{{lc.ctn_nombre}}</td>
                <td>{{lc.prr_nombre}}</td>

                <td v-if="lc.clie_estado == 1"><span class="badge bg-success"><i class="fas fa-check-double"></i>  Activo</span></td>
                <td v-else><span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span></td>

                <td>
                    <template v-if="admin">
                        <button @click="loadCliente(lc), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalClientes"><i class="fas fa-edit"></i> </button>
                    </template>
                </td>
            </tr>
        </tbody>
    </table>
</div>