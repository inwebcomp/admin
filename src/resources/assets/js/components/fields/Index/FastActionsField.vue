<template>
    <td class="data-table__value cursor-pointer link w-1 p-0" :class="classes">
        <router-link :to="{ path: field.edit, query: $route.query }" class="h-full flex items-center block p-4">
            <i class="fas fa-edit"></i>
        </router-link>
    </td>
</template>

<script>
    import Vue from 'vue'

    export default {
        props: ['field'],

        computed: {
            classes() {
                let arr = this.field.classes

                arr.push('text-' + this.field.textAlign)

                return arr
            }
        },

        methods: {
            fastEdit() {
                this.$router.push()
            },
        }
    }
</script>
