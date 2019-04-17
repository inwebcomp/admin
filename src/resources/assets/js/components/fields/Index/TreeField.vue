<template>
    <td @click="go" class="data-table__value text-left" :class="{'cursor-pointer hover:bg-grey-lighter': !field.leaf}">
        <i class="far fa-folder mr-2" :class="{'hidden': field.leaf}"></i>
        {{ field.value }}
    </td>
</template>

<script>
    export default {
        props: ['field', 'resourceId'],

        methods: {
            go() {
                if (! this.field.leaf)
                App.$emit('parentSelect', this.resourceId)
            }
        },
    }
</script>
