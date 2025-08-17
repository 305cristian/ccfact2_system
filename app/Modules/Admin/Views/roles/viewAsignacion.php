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
    <template>
        <tr  >
            <td><i class="fas fa-book-alt"></i><strong style="color:red">ALL PERMISOS</strong></td>
            <td><input @click="selectAllPermisos()" type="checkbox" v-model='selectAll'></td>
        </tr>
    </template>

    <template v-for="lm of listaAllModulos">

        <tr @click="seletedRowModSubMod(lm.id)" :class="lm.id" :id=" 'check'+lm.id ">
            <td><i :class="lm.md_icon"></i><strong>{{lm.md_nombre}}</strong></td>
            <td><input  :id="'checkmod'+lm.id " type="checkbox"></td>
        </tr>

        <template v-for="lsm of listaAllSubModulos" v-if="lm.id == lsm.md_padre">

            <tr @click="seletedRowModSubMod(lsm.id)" :class="lsm.id" :id=" 'check'+lsm.id ">
                <td>       <i :class="lsm.md_icon"></i>{{lsm.md_nombre}}</td>
                <td><input :id="'checkmod'+lsm.id " type="checkbox"></td>
            </tr>


            <template v-for="la of listaAllAcciones" v-if="lsm.id == la.fk_modulo">
                <tr @click="seletedRowAcc(la.id)" :class=" '_'+la.id" :id=" 'check2'+la.id ">
                    <td>             <i class="far fa-circle"></i>{{la.ac_nombre}}</td>
                    <td><input :id="'checkacc'+la.id "  type="checkbox"></td>
                </tr>
            </template>


        </template>

        <template v-for="la of listaAllAcciones" v-if="lm.id == la.fk_modulo">
            <tr @click="seletedRowAcc(la.id)" :class=" '_'+la.id" :id=" 'check2'+la.id ">
                <td>             <i class="far fa-circle"></i>{{la.ac_nombre}}</td>
                <td><input :id="'checkacc'+la.id " type="checkbox"></td>
            </tr>
        </template>


    </template>

</table>

