<template>
    <default-field :field="field" :errors="errors" :inline="inline" v-bind="other" :full-width-content="true">
        <template slot="field">
            <div :class="field.translatable ? 'form__group__translatable mb-2' : ''">
                <textarea
                        class="form__group__input form__group__input--textarea w-full h-auto"
                        :id="field.attribute"
                        :value="value"
                        @input="$emit('input', $event.target.value)"
                        v-bind="extraAttributes"
                        :class="errorClasses()">
                </textarea>

                <div v-if="field.translatable" class="form__group__translatable__locale" :class="errorClasses()">
                    {{ this.field.currentLocale }}
                </div>
            </div>

            <template v-if="field.translatable">
                <div class="form__group__translatable mb-2" v-for="(translatableValue, locale) in field.translatableValues">
                    <textarea
                            class="form__group__input form__group__input--textarea w-full h-auto"
                            :id="field.attribute + ':' + locale"
                            v-model="field.translatableValues[locale]"
                            v-bind="extraAttributes"
                            :class="errorClasses(translationAttribute(locale))">
                    </textarea>
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