<template>
    <default-field :field="field" :errors="errors" :inline="inline" v-bind="other">
        <template slot="field">
            <app-select :id="field.attribute"
                        :options="options"
                        :value="value"
                        @input="$emit('input', $event)"
                        @search="searchWord = $event"
                        :search="search"
                        v-bind="extraAttributes"
                        :class="errorClasses()"/>
        </template>
    </default-field>
</template>

<script>
    import HandlesValidationErrors from "~mixins/HandlesValidationErrors"
    import FormField from "~mixins/FormField"

    export default {
        mixins: [FormField, HandlesValidationErrors],

        data() {
            return {
                searchWord: ''
            }
        },

        computed: {
            defaultAttributes() {
                return {}
            },

            extraAttributes() {
                const attrs = this.field.extraAttributes

                return {
                    ...this.defaultAttributes,
                    ...attrs,
                }
            },

            search() {
                return! ! this.field.search
            },

            options() {
                if (this.search && this.searchWord) {
                    return this.field.options.filter(option => option.title.indexOf(this.searchWord) === 0)
                }

                return this.field.options
            }
        },
    }
</script>

