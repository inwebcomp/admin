<template>
    <div class="resource-count mr-auto ml-4" v-if="pagination && pagination.total">
        {{ min }} - {{ max }} {{ __('из') }} {{ pagination.total }}
    </div>
</template>

<script>
    export default {
        name: "ResourceCount",

        props: {
            pagination: {
                type: Object,
            },
        },

        computed: {
            min() {
                return (this.pagination.currentPage - 1) * this.pagination.perPage + 1
            },

            max() {
                let max = this.pagination.currentPage * this.pagination.perPage

                if (max > this.pagination.total)
                    return this.pagination.total

                return max
            },
        },
    }
</script>