<template>
    <default-field :field="field" :errors="errors" :inline="inline" v-bind="other">
        <template slot="field" v-if="! field.sections">
            <div :class="field.translatable ? 'form__group__translatable mb-4' : ''">
                <froala :tag="'textarea'"
                        :config="config"
                        :id="field.attribute"
                        :value="value"
                        @input="$emit('input', $event)"
                        v-bind="extraAttributes"
                        :class="errorClasses()"/>

                <div v-if="field.translatable" class="form__group__translatable__locale" :class="errorClasses()">
                    {{ this.field.currentLocale }}
                </div>
            </div>
            <template v-if="field.translatable">
                <div class="form__group__translatable mb-4" v-for="(translatableValue, locale) in field.translatableValues">
                    <froala :tag="'textarea'"
                            :config="config"
                            :id="field.attribute + ':' + locale"
                            v-model="field.translatableValues[locale]"
                            v-bind="extraAttributes"
                            :class="errorClasses(translationAttribute(locale))"/>
                    <div class="form__group__translatable__locale" :class="errorClasses(translationAttribute(locale))">{{ locale }}</div>
                </div>
            </template>
        </template>
        <template slot="field" v-if="field.sections">
            <div class="field__editor__section mb-4" v-for="(section, $i) in value">
                <input
                        class="w-full form__group__input mb-2"
                        v-model="section.title"/>

                <froala
                        :tag="'textarea'"
                        :config="config"
                        :id="field.attribute"
                        v-model="section.text"
                        v-bind="extraAttributes"/>

                <div class="py-4 px-6 text-center cursor-pointer hover:bg-grey-lighter" @click="remove($i)">
                    <i class="far fa-trash-alt mr-2"></i> {{ __('Удалить') }}
                </div>
            </div>

            <div class="py-4 px-6 text-center cursor-pointer hover:bg-grey-lighter" @click="add">
                <i class="fal fa-plus mr-2"></i> {{ __('Добавить') }}
            </div>
        </template>
    </default-field>
</template>

<script>
    import HandlesValidationErrors from "~mixins/HandlesValidationErrors"
    import FormField from "~mixins/FormField"

    export default {
        mixins: [HandlesValidationErrors, FormField],

        data() {
            return {
                castArray: !! this.field.sections,

                config: {
                    language: 'ru',
                    toolbarButtons: [
                        'bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', '|',
                        'fontSize', 'color', 'paragraphStyle', '|',
                        'paragraphFormat', 'align', 'formatOL', 'formatUL', 'outdent', 'indent',
                        '-',
                        'insertLink', 'insertImage', 'insertFile', 'insertVideo', 'embedly', 'insertTable', '|',
                        'specialCharacters', 'insertHR', 'clearFormatting', '|',
                        'print', 'spellChecker', 'help', 'html', '|',
                        'undo', 'redo'
                    ],
                    toolbarSticky: false,
                    videoUpload: false,
                    pastePlain: true,
                    imageUploadURL: App.api.url('field/editor-field/image/' + this.resourceName + '/' + this.resourceId),
                    requestHeaders: {
                        'X-CSRF-TOKEN': Laravel.csrfToken
                    },
                    imageAllowedTypes: ['jpeg', 'jpg', 'png', 'svg', 'gif'],
                    imageMaxSize: 1024 * 1024 * 2,
                    imageInsertButtons: ['imageBack', '|', 'imageUpload', 'imageByURL'],
                    entities: '&quot;&#39;&iexcl;&cent;&pound;&curren;&yen;&brvbar;',

                    fileUpload: true,

                    // Set the file upload parameter.
                    fileUploadParam: 'file',

                    // Set the file upload URL.
                    fileUploadURL: App.api.url('field/editor-field/file/' + this.resourceName + '/' + this.resourceId),

                    // Set request type.
                    fileUploadMethod: 'POST',

                    // Set max file size to 20MB.
                    fileMaxSize: 20 * 1024 * 1024,

                    // Allow to upload any file.
                    fileAllowedTypes: ['*'],
                },
            }
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

        methods: {
            add() {
                let newValue = this.value ? this.value.slice() : []

                newValue.push({
                    title: '',
                    text: '',
                })

                this.$emit('input', newValue)
            },

            remove(index) {
                if (! confirm(this.__('Are you sure to delete text section?')))
                    return

                let newValue = this.value.filter((item, i) => index !== i)

                this.$emit('input', newValue)
            }
        },
    }
</script>

