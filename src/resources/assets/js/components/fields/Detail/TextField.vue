<template>
    <default-field :field="field" :errors="errors" :inline="inline" v-bind="other">
        <template slot="field">
            <div :class="field.translatable ? 'form__group__translatable mb-2' : ''">
                {{ value }}
                <div v-if="field.translatable" class="form__group__translatable__locale" :class="errorClasses()">
                    {{ this.field.currentLocale }}
                </div>
            </div>

            <template v-if="field.translatable">
                <div class="form__group__translatable mb-2" v-for="(translatableValue, locale) in field.translatableValues">
                    {{ field.translatableValues[locale] }}
                    <div class="form__group__translatable__locale" :class="errorClasses(translationAttribute(locale))">{{ locale }}</div>
                </div>
            </template>
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
                return {
                    type: this.field.type || 'text',
                    min: this.field.min,
                    max: this.field.max,
                    step: this.field.step,
                    pattern: this.field.pattern,
                    placeholder: this.field.placeholder,
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

