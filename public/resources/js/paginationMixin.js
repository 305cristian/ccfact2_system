/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */


window.paginationMixin = {
    computed: {
        totalPages() {
            if (!this.pagination)
                return 0;
            return Math.ceil(
                    this.pagination.filteredRecords / this.pagination.pageSize
                    );
        },

        visiblePages() {
            if (!this.pagination)
                return [];

            const pages = [];
            const total = this.totalPages;
            const current = this.pagination.currentPage;

            if (total <= 7) {
                for (let i = 1; i <= total; i++) {
                    pages.push(i);
                }
            } else {
                pages.push(1);

                if (current > 4)
                    pages.push('...');

                const start = Math.max(2, current - 2);
                const end = Math.min(total - 1, current + 2);

                for (let i = start; i <= end; i++) {
                    pages.push(i);
                }

                if (current < total - 3)
                    pages.push('...');

                if (total > 1)
                    pages.push(total);
            }

            return pages;
        }
    },

    methods: {
        goToPage(page) {
            if (
                    page >= 1 &&
                    page <= this.totalPages &&
                    page !== this.pagination.currentPage
                    ) {
                this.pagination.currentPage = page;
                this.searchDataReport(true);
            }
        },

        onSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.pagination.currentPage = 1;
                this.searchDataReport(true);
            }, 500);
        },

        onPageSizeChange() {
            this.pagination.currentPage = 1;
            this.searchDataReport(true);
        },

        sort(column) {
            if (this.pagination.sortColumn === column) {
                this.pagination.sortDirection = this.pagination.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.pagination.sortColumn = column;
                this.pagination.sortDirection = 'asc';
            }

            this.pagination.currentPage = 1;
            this.searchDataReport(true);
        },

        getSortClass(column) {
            if (this.pagination.sortColumn !== column) {
                return 'fa-solid fa-sort';
            }

            return this.pagination.sortDirection === 'asc'
                    ? 'fa-solid fa-sort-up'
                    : 'fa-solid fa-sort-down';
        }
    }
};
