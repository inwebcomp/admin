<script>
    export default {
        name: "table-value",

        props: [
            'field',
            'resourceId',
            'resourceName',
            'fastEdit',
        ],

        render(createElement) {
            if (!this.field.fullCell) {
                return createElement(this.isFastEdit ? 'fast-edit-field' : 'td', {
                    class: ['data-table__value', ...this.classes],
                    props: {
                        resourceName: this.resourceName,
                        resourceId: this.resourceId,
                        field: this.field
                    },
                    attrs: {
                        colspan: this.field.colspan,
                    }
                }, this.$slots.default);
            } else {
                return this.$slots.default[0]
            }
        },

        computed: {
            classes() {
                let classes = this.field.classes || []

                return [
                    'text-' + this.field.textAlign,
                    ...classes
                ]
            },

            isFastEdit() {
                return this.fastEdit && this.field.fastEdit
            }
        },
    }
</script>
