<!--<template>-->
    <!--<div>-->
        <!--<td v-if="! field.fullCell" class="data-table__value" :class="classes">-->
            <!--<slot></slot>-->
        <!--</td>-->
        <!--<slot v-if="field.fullCell" class="data-table__value" :class="classes"></slot>-->
    <!--</div>-->
<!--</template>-->

<script>
    export default {
        name: "table-value",

        props: [
            'field',
            'resourceId',
        ],

        render(createElement) {
            if (! this.field.fullCell) {
                return createElement('td', {
                    class: ['data-table__value', ...this.classes],
                }, this.$slots.default);
            } else {
                return this.$slots.default[0]
            }
        },

        computed: {
            classes() {
                return [
                    'text-' + this.field.textAlign,
                    ...this.field.classes
                ]
            }
        },
    }
</script>
