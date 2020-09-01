<template>
    <td class="data-table__group-title p-4 pt-8 bg-grey-lightest font-bold text-grey-darker bg-grey-lightest" :colspan="length">
        {{ info.title }}
    </td>
</template>

<script>
    export default {
        name: "table-group-info",

        props: [
            'info',
            'length',
        ],
    }
</script>
