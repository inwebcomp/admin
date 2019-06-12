<template>
    <div class="flex">
        <custom-actions />

        <div v-if="selected.length" class="active-panel__button" @click="$emit('action', 'destroy')">
            <i class="fas fa-trash-alt mr-2 text-grey-light"></i>
            {{ __(' Удалить') }}
        </div>
    </div>
</template>

<script>
    export default {
        name: "table-actions",

        computed: {
            selected() {
                return this.$store.state.resource.selected
            },
        },
    }
</script>