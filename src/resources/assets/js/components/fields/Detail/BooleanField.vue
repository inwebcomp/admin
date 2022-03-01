<template>
    <default-field :field="field" :errors="errors" :inline="inline" v-bind="other">
        <template slot="field">
            <div class="flex items-center h-full">
                <i v-if="value" class="far fa-check"></i>
                <i v-if="!value" class="far fa-times"></i>
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

