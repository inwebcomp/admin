<template>
    <td :class="classes">
        <slot/>
    </td>
</template>

<script>
    export default {
        name: "table-heading",

        props: [
            'field',
        ],

        computed: {
            classes() {
                return [
                    'data-table__title',
                    'text-' + this.field.textAlign,
                    ...this.field.classes
                ]
            }
        },
    }
</script>
