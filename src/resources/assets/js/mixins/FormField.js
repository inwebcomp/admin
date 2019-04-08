import FormDataHelper from "~js/services/FormDataHelper"

export default {
    props: {
        resourceName: {},
        resourceId: {},
        field: {},
        inline: {},
        other: {},
        value: {},
    },

    watch: {
        field: {
            immediate: true,
            handler() {
                this.field.fill = this.fill
                // this.handleChange(this.field.value || '')
            }
        }
    },

    data() {
        return {
            castArray: false
        }
    },

    mounted() {
        // this.setInitialValue()

        // Register a global event for setting the field's value
        App.$on(this.field.attribute + '-value', value => {
            this.value = value
        })
    },

    destroyed() {
        App.$off(this.field.attribute + '-value')
    },

    methods: {
        /*
         * Set the initial value for the field
         */
        // setInitialValue() {
        //     this.value = !(this.field.value === undefined || this.field.value === null)
        //         ? this.field.value
        //         : ''
        // },

        /**
         * Provide a function that fills a passed FormData object with the
         * field's internal value attribute
         */
        fill(formData) {
            let value = !(this.value === undefined || this.value === null)
                    ? this.value
                    : ''

            if (this.castArray) {
                FormDataHelper.append(value, formData, this.field.attribute)
            } else {
                formData.append(this.field.attribute, value)
            }
        },

        /**
         * Update the field's internal value
         */
        handleChange(value) {
            this.field.value = value
        },
    },
}