<!DOCTYPE html>
<!--
/**
 * Description of viewTable
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 1 ago 2024
 * @time 12:43:59 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<div v-if="loading" class="loading-data">
    <h5 style="font-family: ">Cargando Productos...</h5>
</div>
<div class="table-responsive">

    <table id="tblProductos" class="table table-striped nowrap display" style="width: 100%">
        <thead class="bg-system text-white">
            <tr>
                <td>ACCIONES</td>

                <td>ID</td>
                <td>CÓDIGO</td>
                <td>NOMBRE</td>
                <td>PRES.</td>
                <td>STOCK.</td>                     
                <td>MARCA</td>
                <td>GRUPO</td>
                <td>SUBGRUPO</td>
                <td>CST. PROMEDIO</td>
                <td>CST. ULTIMO</td>
                <td>CST. ALTO</td>
                <td>PVP pA</td>
                <td>TIPO PRODUCTO</td>
                <td>IMPUESTO</td>
                <td>PUEDE COMPRARSE</td>
                <td>PUEDE VENDERSE</td>
                <td>ES SERVICIO</td>
                <td>CTRL. LOTE</td>
                <td>ICE</td>
                <td>CTA. CONT. COMPRAS</td>
                <td>CTA. CONT. VENTAS</td>
                <td>ESTADO</td>
            </tr>
        </thead>

        <tbody>
            <tr v-for="lp of listaProductos">
                <td>
                    <template v-if="admin">
                        <button @click="loadProducto(lp), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalProductos"><i class="fas fa-edit"></i> </button>
                    </template>
                </td>
                <td>{{zfill(lp.id)}}</td>
                <td>{{lp.prod_codigo}}</td>
                <td>{{lp.prod_nombre}}</td>
                <td>{{lp.um_nombre_corto}}</td>
                <td>{{lp.prod_stockactual}}</td>                     
                <td>{{lp.mrc_nombre? lp.mrc_nombre: "-"}}</td>
                <td>{{lp.gr_nombre}}</td>
                <td>{{lp.sgr_nombre}}</td>
                <td>{{lp.prod_costopromedio}}</td>
                <td>{{lp.prod_costoultimo}}</td>
                <td>{{lp.prod_costoalto}}</td>
                <td>{{parseFloat(lp.pp_valor).toFixed(2)}}</td>
                <td>{{lp.tp_nombre}}</td>
                <td> IVA {{parseFloat(lp.prod_ivaporcentage).toFixed(0)}}%</td>
                <td> <span v-if="lp.prod_compra == 1" class="badge badge-primary">SI</span><span v-else class="badge badge-secondary">NO</span></td>
                <td> <span v-if="lp.prod_venta == 1" class="badge badge-primary">SI</span><span v-else class="badge badge-secondary">NO</span></td>
                <td> <span v-if="lp.prod_isservicio == 1" class="badge badge-primary">SI</span><span v-else class="badge badge-secondary">NO</span></td>
                <td> <span v-if="lp.prod_ctrllote == 1" class="badge badge-primary">SI</span><span v-else class="badge badge-secondary">NO</span></td>
                <td> <span v-if="lp.prod_tiene_ice == 1" class="badge badge-primary">SI</span><span v-else class="badge badge-secondary">NO</span></td>
                <td>{{lp.prod_cuentacontablecompras}}</td>
                <td>{{lp.prod_cuentacontableventas}}</td>

                <td v-if="lp.prod_estado == 1"><span class="badge bg-success"><i class="fas fa-check-double"></i>  Activo</span></td>
                <td v-else><span class="badge bg-danger"><i class="fas fa-stop-circle"></i> Inactivo</span></td>


            </tr>
        </tbody>
    </table>
</div>
