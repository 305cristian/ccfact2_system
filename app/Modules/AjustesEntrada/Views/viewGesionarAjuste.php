<!DOCTYPE html>
<!--
/**
 * Description of viewGesionarAjuste
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 22 oct 2025
 * @time 2:49:18 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div id="app" class="container-fluid">
    <div class="card card-system card-outline">
        <div class="card-header">
            <h5 class="card-title text-system"><i class="fas fa-clipboard-list-check"></i> Gestion de Ajustes de Entrada</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tblAjustes" class="table table-striped nowrap w-100" >
                    <thead class="bg-system text-white">
                        <tr>
                            <th style="width: 5px">ACIONES</th>
                            <th style="width: 5px">CÓDIGO</th>
                            <th>FECHA</th>
                            <th>TOTAL</th>
                            <th>OBSERVACIONES</th>
                            <th>BODEGA</th>
                            <th>PROVEEDOR</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for='laj of listaAjustes'>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span><i class="fas fa-ellipsis-v"></i></span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                        <li><a class="dropdown-item" href="#" @click="verDetalle(laj.id)">Ver Detalle</a></li>
                                        <li><a class="dropdown-item" href="#">Modificar Ajuste</a></li>
                                        <li><a class="dropdown-item" href="#">Anular Ajuste</a></li>
                                        <li><a class="dropdown-item" href="#">Clonar Ajuste</a></li>
                                    </ul>
                                </div>
                                <!--<button @click="loadAjuste(laj), estadoSave = false" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalAnillos"><i class="fas fa-edit"></i> </button>-->
                            </td>
                            <td>{{zfill(laj.ajen_secuencial)}}</td>
                            <td>{{laj.ajen_fecha}}</td>
                            <td>{{laj.ajen_total}}</td>
                            <td>{{laj.ajen_observaciones}}</td>
                            <td>{{laj.bod_nombre}}</td>
                            <td>{{laj.prov_razon_social}}</td>

                            <td>
                                <span v-if="laj.ajen_estado == 2" class="badge bg-success"><i class="fas fa-check-double"></i>  ARCHIVADO</span>
                                <span v-else-if="laj.ajen_estado == 1" class="badge bg-warning"><i class="fas fa-warning"></i>  BORRADOR</span>
                                <span v-else-if="laj.ajen_estado == 1" class="badge bg-danger"><i class="fas fa-danger"></i>  ANULADO</span>
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div> 

        </div>
    </div>
</div>
<script type="text/javascript">
    
    const {createApp} =Vue;

  createApp({
      
        data() {
            return {
                url: siteUrl,

                listaAjustes: [],
            }
        },
        created() {
            this.getAjustes();
        },
        methods: {

            async getAjustes() {
                try {
                    let {data} = await axios.post(this.url + '/ajustesentrada/getAjustes');
                    if (data) {
                        this.listaAjustes = data;
                    } else {
                        sweet_msg_dialog('warning', 'No se han encontrado ajustes registrados en los parametros especificados');
                    }
                    dataTable('#tblAjustes', 'Listado de ajustes de entrada');
                } catch (e) {
                    sweet_msg_dialog('error', '', '', e.response.data?.message || e.message);
                }

            },
            verDetalle(idAjuste){
                alert(idAjuste);
            },
            zfill(value) {
                return zFill(value, 4);
            }

        }
    }).mount('#app');
</script>
