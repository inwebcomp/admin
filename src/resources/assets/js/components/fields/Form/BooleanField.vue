<template>
    <default-field :field="field" :errors="errors" :inline="inline" v-bind="other">
        <template slot="field">
            <div class="flex items-center h-full">
                <component
                        :is="this.switch ? 'switch-input' : 'checkbox'"
                        :value="value"
                        @input="$emit('input', $event)"
                        v-bind="extraAttributes" />
            </div>
        </template>
    </default-field>
</template>

<script>
    import HandlesValidationErrors from "~mixins/HandlesValidationErrors"
    import FormField from "~mixins/FormField"

    export default {
        mixins: [HandlesValidationErrors, FormField],

        props: {
            switch: {
                type: Boolean,
                default: true
            },
        },

        computed: {
            defaultAttributes() {
                return {
                    class: this.errorClasses(),
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

