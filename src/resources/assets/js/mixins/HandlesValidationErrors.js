import { Errors } from 'form-backend-validation'

export default {
    props: {
        errors: {
            default: () => new Errors(),
        },
    },

    data: () => ({
        errorClass: 'border-danger',
    }),

    computed: {
        fieldAttribute() {
            return this.field.attribute
        },
    },

    methods: {
        errorClasses(attribute = null) {
            return this.hasError(attribute) ? [this.errorClass] : []
        },

        hasError(attribute = null) {
            return this.errors.has(attribute ? attribute : this.fieldAttribute)
        },

        firstError(attribute = null) {
            if (this.field.translatable) {
                let error = null

                Object.keys(this.field.translatableValues).forEach(locale => {
                    let value = this.errors.first(this.translationAttribute(locale))

                    if (value) {
                        error = value
                    }
                })

                return error
            } else {
                if (this.hasError(attribute)) {
                    return this.errors.first(attribute ? attribute : this.fieldAttribute)
                }
            }
        },

        translationAttribute(locale) {
            return this.field.attribute + (this.field.currentLocale == locale ? '' : ':' + locale)
        },
    },
}