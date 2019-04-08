<template>
    <td @click="go" class="data-table__value text-left cursor-pointer hover:bg-grey-lighter">
        <i class="far fa-folder mr-2"></i>
        {{ field.value }}
    </td>
</template>

<script>
    export default {
        props: ['field', 'resourceId'],

        methods: {
            go() {
                App.$emit('parentSelect', this.resourceId)
            }
        },
    }
</script>
