<template>
    <default-field :field="field" :errors="errors" :inline="inline" v-bind="other">
        <template slot="field">
            <div class="field__editor__section mb-4" v-for="(section, $i) in value">
                <div class="flex -mx-4">
                    <div class="w-full ml-4" :class="{'flex': field.inlineTranslationFields}">
                        <div class="w-full form__group__translatable mb-2" :class="{'mr-2': field.inlineTranslationFields}">
                            <input
                                    class="form__group__input"
                                    :id="field.attribute + ':' + field.currentLocale"
                                    v-model="value[$i]"
                                    v-bind="extraAttributes"
                                    :class="errorClasses(translationAttribute(field.currentLocale))"
                            />

                            <div class="form__group__translatable__locale">{{ field.currentLocale }}</div>
                        </div>

                        <div class="w-full form__group__translatable mb-2"
                             v-for="(translatableValue, locale) in field.translatableValues">
                            <input
                                    class="form__group__input"
                                    :id="field.attribute + ':' + locale"
                                    v-model="translatableValue[$i]"
                                    v-bind="extraAttributes"
                                    :class="errorClasses(translationAttribute(locale))"
                            />

                            <div class="form__group__translatable__locale">{{ locale }}</div>
                        </div>
                    </div>

                    <div class="mr-4 ml-2 mb-2 py-4 px-4 flex items-center text-center cursor-pointer hover:bg-grey-lighter" @click="remove($i)">
                        <i class="far fa-trash-alt"></i>
                    </div>
                </div>
            </div>

            <div class="py-4 px-6 text-center cursor-pointer hover:bg-grey-lighter" @click="add">
                <i class="fal fa-plus mr-2"></i> {{ __('Добавить') }}
            </div>
        </template>
    </default-field>
</template>

<script>
    import FormField from "~mixins/FormField"
    import HandlesValidationErrors from "~mixins/HandlesValidationErrors"

    export default {
        mixins: [FormField, HandlesValidationErrors],

        data() {
            return {
                castArray: true,
            }
        },

        created() {
            this.initTranslations()
        },

        watch: {
            field() {
                this.initTranslations()
            }
        },

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

        methods: {
            initTranslations() {
                let value = this.field.value
                let translatableValues = this.field.translatableValues

                if (! value)
                    value = []

                Object.keys(translatableValues).forEach((lang) => {
                    if (! translatableValues[lang]) {
                        translatableValues[lang] = []
                    }

                    Object.keys(value).forEach((index) => {
                        if (! translatableValues[lang][index])
                            translatableValues[lang][index] = null
                    })
                })
            },

            hasTranslation(lang) {
                if (! this.value)
                    return false

                return !! Object.keys(this.value).find((index) => this.field.translatableValues[lang] && this.field.translatableValues[lang][index] && this.field.translatableValues[lang][index].title)
            },

            add() {
                let newValue = this.field.value ? this.field.value.slice() : []

                newValue.push(null)

                this.$emit('input', newValue)

                this.initTranslations()
            },

            remove(index) {
                if (!confirm(this.__('Are you sure to delete this section?')))
                    return

                let newValue = this.value.filter((item, i) => index !== i)

                this.$emit('input', newValue)
            }
        },
    }
</script>

