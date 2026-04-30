<!DOCTYPE html>
<!--
/**
 * Description of viewPaginationFoot
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 26 ene 2026
 * @time 2:35:26 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<div class="jd-flex justify-content-between align-items-center mt-2">
    <div class="pagination-info">
        Mostrando registros del {{ (pagination.currentPage - 1) * pagination.pageSize + 1 }} al {{ Math.min(pagination.currentPage * pagination.pageSize, pagination.filteredRecords) }} 
        de un total de {{ pagination.totalRecords }} registros 
        <span v-if="pagination.filteredRecords !== pagination.totalRecords">
            (filtrados de {{ pagination.totalRecords }} total)
        </span>
    </div>

    <ul class="pagination justify-content-end">
        <li class="page-item" :class="{ disabled: pagination.currentPage === 1 }">
            <a class="page-link" href="#"  @click.prevent="goToPage(1)">
                <i class="fa-solid fa-angles-left"></i>
            </a>
        </li>
        <li class="page-item" :class="{ disabled: pagination.currentPage === 1 }">
            <a  class="page-link" href="#" @click.prevent="goToPage(pagination.currentPage - 1)">
               Previous
            </a>
        </li>

        <template v-for="(page, index) in visiblePages"  :key="index">
            <li class="page-item"  :class="{ active: page === pagination.currentPage, disabled: page === '...' }">
                <a 
                    v-if="page !== '...'" 
                    class="page-link"
                    @click.prevent="goToPage(page)" 
                    href="#"
                    >
                    {{ page }}
                </a>
                <span v-else class="ellipsis">...</span></li>
        </template>

        <li class="page-item" :class="{ disabled: pagination.currentPage === pagination.totalPages }">
            <a class="page-link" href="#" @click.prevent="goToPage(pagination.currentPage + 1)" >
               Next
            </a>
        </li>
        <li class="page-item" :class="{ disabled: pagination.currentPage === pagination.totalPages }">
            <a class="page-link" href="#" @click.prevent="goToPage(pagination.totalPages)" >
              <i class="fa-solid fa-angles-right"></i>
            </a>
        </li>
    </ul>
</div>