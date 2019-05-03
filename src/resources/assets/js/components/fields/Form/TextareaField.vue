<template>
    <default-field :field="field" :errors="errors" :inline="inline" v-bind="other" :full-width-content="true">
        <template slot="field">
            <textarea
                    class="form__group__input form__group__input--textarea w-full h-auto"
                    :id="field.attribute"
                    :value="value"
                    @input="$emit('input', $event.target.value)"
                    v-bind="extraAttributes"></textarea>
        </template>
    </default-field>
</template>

<script>
    import HandlesValidationErrors from "~mixins/HandlesValidationErrors"
    import FormField from "~mixins/FormField"

    export default {
        mixins: [FormField, HandlesValidationErrors],

        props: {
            resourceName: { type: String },
            field: {
                type: Object,
                required: true,
            },
        },

        computed: {
            defaultAttributes() {
                return {
                    rows: this.field.rows,
                    class: this.errorClasses(),
                    placeholder: this.field.name,
                }
            },

            extraAttributes() {
                const attrs = this.field.extraAttributes

                return {
                    ...this.defaultAttributes,
                    ...attrs,
                }
            },
        },
    }
</script>