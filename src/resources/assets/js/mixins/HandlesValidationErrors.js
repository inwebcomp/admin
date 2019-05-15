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
        hasErrorComputed() {
            return this.errors.has(this.fieldAttribute)
        },
    },

    methods: {
        errorClasses(attribute = null) {
            return this.hasError(attribute) ? [this.errorClass] : []
        },

        hasError(attribute = null) {
            return this.firstError(attribute)
        },

        firstError(attribute = null) {
            if (this.field.translatable) {
                let error = this.errors.first(attribute ? attribute : this.fieldAttribute)

                if (! error) {
                    Object.keys(this.field.translatableValues).forEach(locale => {
                        let value = this.errors.first(this.translationAttribute(locale))

                        if (value) {
                            error = value
                        }
                    })
                }

                return error
            } else {
                return this.errors.first(attribute ? attribute : this.fieldAttribute)
            }
        },

        translationAttribute(locale) {
            return this.field.attribute + (this.field.currentLocale == locale ? '' : ':' + locale)
        },
    },
}