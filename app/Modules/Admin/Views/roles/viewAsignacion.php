<!DOCTYPE html>
<!--
/**
 * Description of viewAsignacion
 *
/**
 * @author CRISTIAN PAZ
 * @date 27 dic. 2023
 * @time 17:00:46
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->
<table class="table">
    <tbody>
        <tr>
            <td colspan="2">
                <div style="display:flex; align-items:center; gap:8px; font-weight:600; color:red;">
                    <input @click="selectAllPermisos()" type="checkbox" v-model="selectAll">
                    <i class="fas fa-book-alt"></i> Todos Los Permisos
                </div>
            </td>
        </tr>

    <template v-for="lm of listaAllModulos" :key="lm.id">

        <!-- Fila del módulo (colapsable) -->
        <tr :class="lm.id"
            :style="checkedModulos.includes(lm.id) ? 'background:#EAF2F8' : ''"
            style="cursor:pointer">
            <td  @click="seletedRowModSubMod(lm.id)">
                <input :id="'checkmod'+lm.id"
                       :checked="checkedModulos.includes(lm.id)"
                       type="checkbox"
                       @click.stop="seletedRowModSubMod(lm.id)">

                <strong style=" margin-left:8px;">
                    <i :class="lm.md_icon"></i>
                    {{ lm.md_nombre }}
                </strong>

            </td>
            <td style="display:flex; align-items:center; gap:8px;">
                <span class="badge badge-info" style="font-size:11px; margin-left:8px; color:white;">
                    {{ accionesPorModulo(lm.id).length }} acciones
                </span>
                <span @click='toggleModulo(lm.id)' style="font-size:12px;">
                    <i :class="modulosAbiertos.includes(lm.id) ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"></i>
                </span>

            </td>
        </tr>

        <!-- Submódulos y acciones (solo si el módulo está abierto) -->
        <template v-if="modulosAbiertos.includes(lm.id)">

            <template v-for="lsm of listaAllSubModulos" :key="lsm.id">
                <template v-if="lm.id == lsm.md_padre">

                    <!-- Fila del submódulo -->
                    <tr :class="lsm.id"
                        :style="checkedModulos.includes(lsm.id) ? 'background:#EAF2F8' : ''"
                        style="cursor:pointer;">
                        <td @click="seletedRowModSubMod(lsm.id)" 
                             style="padding-left:24px;">
                            <input :id="'checkmod'+lsm.id"
                                   type="checkbox"
                                   :checked="checkedModulos.includes(lsm.id)"
                                   @click.stop="seletedRowModSubMod(lsm.id)">
                            <span  style=" margin-left:8px;"><i :class="lsm.md_icon"></i> {{ lsm.md_nombre }}</span>
                        </td>
                        <td style="display:flex; align-items:center; gap:8px;">
                            <span @click='toggleSubmodulo(lsm.id)' style="font-size:10px;">
                                <i :class="submodulosAbiertos.includes(lsm.id) ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"></i>
                            </span>


                        </td>
                    </tr>

                    <!-- Acciones del submódulo -->
                    <template v-if="submodulosAbiertos.includes(lsm.id)">
                        <tr v-for="la of listaAllAcciones.filter(a => a.fk_submodulo == lsm.id)"
                            :key="la.id" :class="'_'+la.id"
                            @click="seletedRowAcc(la.id)" 
                            :style="checkedAcciones.includes(la.id) ? 'background:#EAF2F8' : ''"
                            style="cursor:pointer;">
                            <td style="padding-left:48px; font-size:13px; color:gray;">
                                <input :id="'checkacc'+la.id"
                                       :checked="checkedAcciones.includes(la.id)"
                                       type="checkbox"
                                       @click.stop="seletedRowAcc(la.id)" >
                                <span style="margin-left: 8px"> {{ la.ac_nombre }}</span>
                            </td>
                            <td>

                            </td>
                        </tr>
                    </template>

                </template>
            </template>

            <!-- Acciones directas del módulo (sin submódulo) -->
            <tr v-for="la of listaAllAcciones.filter(a => a.fk_submodulo == lm.id)"
                :key="la.id" :class="'_'+la.id"
                @click="seletedRowAcc(la.id)"
                :style="checkedAcciones.includes(la.id) ? 'background:#EAF2F8' : ''"
                style="cursor:pointer;">
                <td style="padding-left:24px; font-size:13px; color:gray;">
                    <input :id="'checkacc'+la.id"
                           type="checkbox"
                           :checked="checkedAcciones.includes(la.id)"
                           @click.stop="seletedRowAcc(la.id)">
                    <span style="margin-left: 8px"> {{ la.ac_nombre }}</span>
                </td>
                <td>

                </td>
            </tr>

        </template>
    </template>
</tbody>
</table>

<!--<table class="table">
    <tbody>

        <tr>
            <td><i class="fas fa-book-alt"></i><strong style="color:red"> ALL PERMISOS</strong></td>
            <td><input @click="selectAllPermisos()" type="checkbox" v-model='selectAll'></td>
        </tr>

    <template v-for="lm of listaAllModulos" :key="lm.id">

        <tr @click="seletedRowModSubMod(lm.id)" :class="lm.id" :id=" 'check'+lm.id " >
            <td><i :class="lm.md_icon"></i><strong> {{' '+lm.md_nombre}}</strong></td>
            <td><input  :id="'checkmod'+lm.id " type="checkbox"></td>
        </tr>

        <template v-for="lsm of listaAllSubModulos" :key="lsm.id">

            <tr  v-if="lm.id == lsm.md_padre" @click="seletedRowModSubMod(lsm.id)" :class="lsm.id" :id=" 'check'+lsm.id " >
                <td>       <i :class="lsm.md_icon"></i> {{' '+lsm.md_nombre}}</td>
                <td><input :id="'checkmod'+lsm.id " type="checkbox"></td>
            </tr>


            <template v-for="la of listaAllAcciones" :key="la.id">
                <tr  v-if=" lm.id == lsm.md_padre && lsm.id == la.fk_submodulo" @click="seletedRowAcc(la.id)" :class=" '_'+la.id" :id=" 'check2'+la.id " >
                    <td>             <i class="far fa-circle"></i> {{la.ac_nombre}}</td>
                    <td><input :id="'checkacc'+la.id "  type="checkbox"></td>
                </tr>
            </template>


        </template>

        <template v-for="la of listaAllAcciones" >
            <tr v-if="lm.id == la.fk_submodulo" @click="seletedRowAcc(la.id)" :class=" '_'+la.id" :id=" 'check2'+la.id ">
                <td>             <i class="far fa-circle"></i> {{la.ac_nombre}}</td>
                <td><input :id="'checkacc'+la.id " type="checkbox"></td>
            </tr>
        </template>


    </template>
</tbody>

</table>-->

