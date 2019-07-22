<template>
    <default-field :field="field" :errors="errors" :inline="inline" v-bind="other">
        <template slot="field">
            <app-select :id="field.attribute"
                        :value="value"
                        :options="options"
                        @input="$emit('input', $event)"
                        v-bind="extraAttributes"
                        @search="search"
                        :class="errorClasses()">
            </app-select>
        </template>
    </default-field>
</template>

<script>
    import HandlesValidationErrors from "~mixins/HandlesValidationErrors"
    import FormField from "~mixins/FormField"

    export default {
        mixins: [FormField, HandlesValidationErrors],

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
        },

        data() {
          return {
              options: []
          }
        },

        methods: {
            search(search) {
                App.api.request({
                    url: 'field/model-field/' + this.field.resource + '/search',
                    params: {
                        search
                    }
                }).then((data) => {
                    this.options = data
                })
            },

            select(value) {
                this.handleChange(value)
            }
        },
    }
</script>

