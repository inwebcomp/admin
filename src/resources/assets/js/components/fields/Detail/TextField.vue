<template>
    <default-field :field="field" :errors="errors" :inline="inline" v-bind="other">
        <template slot="field">
            <div :class="field.translatable ? 'form__group__translatable mb-2' : ''">
                <span v-if="! field.link && !field.asHtml" v-text="value"></span>
                <span v-if="! field.link && field.asHtml" v-html="value"></span>
                <router-link v-if="field.link" :to="field.link" class="data-table__value__link">
                    {{ value }}
                </router-link>
                <div v-if="field.translatable" class="form__group__translatable__locale" :class="errorClasses()">
                    {{ this.field.currentLocale }}
                </div>
            </div>

            <template v-if="field.translatable">
                <div class="form__group__translatable mb-2" v-for="(translatableValue, locale) in field.translatableValues">
                    <span v-if="! field.link && !field.asHtml" v-text="field.translatableValues[locale]"></span>
                    <span v-if="! field.link && field.asHtml" v-html="field.translatableValues[locale]"></span>
                    <router-link v-if="field.link" :to="{ path: field.link, query: $route.query }" class="data-table__value__link">
                        {{ field.translatableValues[locale] }}
                    </router-link>

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

