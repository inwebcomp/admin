<template>
    <td class="data-table__value cursor-pointer link w-1 p-0" :class="classes">
        <router-link :to="this.field.edit" class="h-full block p-4">
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
