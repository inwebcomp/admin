<template>
    <div class="pagination">
        <template v-if="pages.length">
            <div @click="goTo(pagination.currentPage - 1)" class="pagination__page pagination__page--prev" v-if="! isFirstPage"><i class="far fa-angle-left"></i></div>

            <div @click="goTo(page)" class="pagination__page"
                 :class="{ 'pagination__page--current': page == pagination.currentPage }"
                 v-for="({title, page}, $i) in pages" :key="$i">{{ title }}
            </div>

            <div @click="goTo(pagination.currentPage + 1)" class="pagination__page pagination__page--next" v-if="! isLastPage"><i class="far fa-angle-right"></i></div>
        </template>
    </div>
</template>

<script>
    export default {
        name: "Pagination",

        props: {
            pagination: {
                type: Object,
            },
        },

        computed: {
            isFirstPage() {
                return this.pagination.currentPage == 1
            },

            isLastPage() {
                return this.pagination.currentPage == this.pagination.lastPage
            },

            pages() {
                if (this.pagination.lastPage <= 1)
                    return []

                let pages = []

                pages.push({
                    title: 1,
                    page: 1
                })

                if (this.pagination.currentPage - 4 > 1) {
                    pages.push({
                        title: '...',
                        page: Math.max(2, this.pagination.currentPage - 6)
                    })
                }

                for (let i = this.pagination.currentPage - 3; i <= this.pagination.currentPage + 3; i++) {
                    if (i < this.pagination.lastPage && i > 1) {
                        pages.push({
                            title: i,
                            page: i
                        })
                    }
                }

                if (this.pagination.currentPage + 4 < this.pagination.lastPage) {
                    pages.push({
                        title: '...',
                        page: Math.min(this.pagination.lastPage - 1, this.pagination.currentPage + 6)
                    })
                }

                pages.push({
                    title: this.pagination.lastPage,
                    page: this.pagination.lastPage
                })

                return pages
            }
        },

        methods: {
            goTo(page) {
                if (page == this.pagination.currentPage)
                    return

                this.$emit('changePage', page)
            }
        },
    }
</script>