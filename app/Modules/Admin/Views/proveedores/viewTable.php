<!DOCTYPE html>
<!--
/**
 * Description of viewTable
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 17 ago 2025
 * @time 10:21:53 a.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<div v-if="loading" class="loading-data">
    <h5 style="font-family: ">Cargando Proveedores...</h5>
</div>
<div class="table-responsive">
    <table id="tblProveedores" class="table table-striped nowrap display w-100">
        <thead class="bg-system text-white">
            <tr>
                <td>ACCIONES</td>
                <td>ID</td>
                <td>CI/RUC</td>
                <td>DOCUMENTO</td>
                <td>NOMBRES</td>
                <td>APELLIDOS</td>
                <td>RAZON SOCIAL</td>
                <td>TIPO SUJETO</td>
                <td>TELÉFONO</td>
                <td>CELULAR</td>
                <td>EMAIL</td>
                <td>DIRECCIÓN</td>                     
                <td>PROVINCIA</td>
                <td>CANTÓN</td>
                <td>PARROQUIA</td>
                <td>SECTOR</td>
                <td>ANILLO</td>
                <td>CTA. CONTABLE</td>           
                <td>ESTADO</td>           
            </tr>
        </thead>

        <tbody>
            <tr v-for="lp of listaProveedores">
                <td>
                    <template v-if="admin">
                        <button @click="loadProveedor(lp), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalProveedores"><i class="fas fa-edit"></i> </button>
                    </template>
                </td>
                <td>{{zFill(lp.id)}}</td>
                <td>{{lp.prov_ruc}}</td>
                <td>{{lp.doc_nombre}}</td>
                <td>{{lp.prov_nombres}}</td>
                <td>{{lp.prov_apellidos}}</td>                     
                <td>{{lp.prov_razon_social}}</td>
                <td>{{lp.tps_descripcion}}</td>
                <td>{{lp.prov_telefono}}</td>
                <td>{{lp.prov_celular}}</td>
                <td>{{lp.prov_email}}</td>
                <td>{{lp.prov_direccion}}</td>
                <td>{{lp.prv_nombre}}</td>
                <td>{{lp.ctn_nombre}}</td>
                <td>{{lp.prr_nombre}}</td>
                <td>{{lp.sec_nombre}}</td>
                <td>{{lp.an_nombre}}</td>
                <td>{{lp.cta_contable}}</td>

                <td v-if="lp.prov_estado == 1"><span class="badge bg-success"><i class="fas fa-check-double"></i>  Activo</span></td>
                <td v-else><span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span></td>


            </tr>
        </tbody>
    </table>
</div>
