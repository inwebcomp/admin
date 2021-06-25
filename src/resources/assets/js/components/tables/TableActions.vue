<template>
    <div class="flex">
        <custom-actions :resourceName="resourceName" :query-string="queryString" />

        <div v-if="selected.length && remove" class="active-panel__button" @click="$emit('action', 'destroy')">
            <i class="fas fa-trash-alt mr-2 text-grey-light"></i>
            {{ __(' Удалить') }}
        </div>
    </div>
</template>

<script>
    export default {
        name: "table-actions",

        props: {
            remove: {
                type: Boolean,
                default: true,
            },
            resourceName: {},

            queryString: {
                type: Object,
                default: () => ({
                    currentSearch: '',
                    encodedFilters: '',
                }),
            },
        },

        computed: {
            selected() {
                return this.$store.state.resource.selected
            },
        },
    }
</script>