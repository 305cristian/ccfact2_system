<!DOCTYPE html>
<!--
/**
 * Description of viewPaginationHead
 *
/**
 * @author CRISTIAN R. PAZ
 * @date 27 ene 2026
 * @time 2:17:21 p.m.
 */       
 
Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHPWebPage.php to edit this template
-->

<hr>
<div class="row g-3 align-items-center mb-2">
    <div class="col-12 col-md-1 d-flex justify-content-between">
        Mostrar 
        <select style="min-width: 50%"
                v-model="pagination.pageSize" 
                @change="onPageSizeChange" 
                class="form-select"
                >
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
         Registros
    </div>

    <div class="col-12 col-md-2 ms-auto d-flex gap-1 justify-content-center">
        <button class="btn btn-success btn-sm" @click.prevent="exportExcel()" :disabled="downloadingexcel">
            <span v-if="downloadingexcel"><i class="loading-spin"></i> Exportando...</span>
            <span v-else><i class="fas fa-file-excel"></i> Expportar a Excel</span>
        </button>
        <button class="btn btn-danger btn-sm" @click.prevent="exportPdf()" :disabled="downloadingpdf">
            <span v-if="downloadingpdf"><i class="loading-spin"></i> Exportando...</span>
            <span v-else ><i class="fas fa-file-pdf"></i> Exportar a PDF</span>
        </button>
    </div>

    <div class="col-12 col-md-2 ms-auto ">
        <input 
            v-model="pagination.searchTerm" 
            @input="onSearch"
            class="form-control" 
            placeholder="Buscar productos..."
            type="text"
            >
    </div>
</div>