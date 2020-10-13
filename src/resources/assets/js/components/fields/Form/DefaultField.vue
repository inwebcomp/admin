<template>
    <field-wrapper :inline="inline" :size="fieldSize">
        <slot>
            <form-label v-if="fieldLabel" :label-for="field.attribute" :inline="inline">
                {{ fieldLabel }}
            </form-label>
        </slot>
        <div class="form__group__input-group" :class="fieldClasses">
            <slot name="field" />

            <help-text class="form__group__error mt-2 text-danger" v-if="hasError() && showErrors">
                {{ firstError() }}
            </help-text>

            <help-text class="mt-2" v-if="showHelpText && field.helpText">{{ field.helpText }}</help-text>
        </div>
    </field-wrapper>
</template>

<script>
    import HandlesValidationErrors from '~mixins/HandlesValidationErrors'

    export default {
        mixins: [HandlesValidationErrors],

        props: {
            field: { type: Object, required: true },
            fieldName: { type: String },
            showHelpText: { type: Boolean, default: true },
            showErrors: { type: Boolean, default: true },
            fullWidthContent: { type: Boolean, default: false },
            inline: { type: Boolean, default: false },
            size: { type: Boolean, default: false },
        },

        data() {
            return {
                fieldSize: null
            }
        },

        created() {
            this.setSize()
        },

        methods: {
            setSize() {
                if (this.size)
                    this.fieldSize = this.size

                if (this.field.size)
                    this.fieldSize = this.field.size

                if (this.fullWidthContent)
                    this.fieldSize = this.inline ? 'w-4/5' : 'w-full'
            }
        },

        computed: {
            fieldLabel() {
                // If the field name is purposefully an empty string, then
                // let's show it as such
                if (this.fieldName === '') {
                    return ''
                }

                return this.fieldName || this.field.singularLabel || this.field.name
            },

            fieldClasses() {
                let classes = this.field.classes || []

                if (this.inline) {
                    classes.push(this.fieldSize)
                } else {
                    classes.push('w-full')
                }

                return classes
            },
        },
    }
</script>
