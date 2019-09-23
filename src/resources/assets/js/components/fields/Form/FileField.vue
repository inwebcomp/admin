<template>
    <default-field :field="field" :errors="errors" :inline="inline" v-bind="other">
        <template slot="field">
            <div class="file-field__load-area"
                 @dragover.prevent="over = true"
                 @dragleave.prevent="over = false"
                 ref="area">

                <label class="file-field__load-area__label w-full flex flex-col items-center px-4 py-6 bg-white text-blue rounded-sm tracking-wide uppercase border border-grey border-dashed cursor-pointer hover:bg-grey-lighter" :class="{'file-field__load-area--over': over}">
                    <svg class="file-field__load-area__icon w-8 h-8" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 20 20">
                        <path d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z"/>
                    </svg>

                    <span class="file-field__load-area__text mt-2 text-base leading-normal">
            <slot>{{ __('Выберите файл') }}</slot>
        </span>

                    <input type='file' class="file-field__load-area__input hidden"
                           :accept="extensions"
                           :multiple="multiple"
                           @change="initLoad($event.target.files)"/>
                </label>
            </div>

            <div class="file-field__loaded-files flex flex-wrap -mx-2" v-if="field.value.length">
                <hr class="file-field__hr m-2">

                <file-element-for-load class="mx-2 mb-4" v-for="(file, $i) in field.value" :key="$i" :loading="file.loading" :errors="file.errors" @remove="removeLoaded($i)">
                    <div class="p-3">
                        {{ file.name }}
                    </div>
                </file-element-for-load>
            </div>
        </template>
    </default-field>
</template>

<script>
    import HandlesValidationErrors from "~mixins/HandlesValidationErrors"
    import FormField from "~mixins/FormField"

    export default {
        mixins: [HandlesValidationErrors, FormField],

        props: {
            accept: {
                type: Array,
                default: () => []
            },
            maxSize: {
                type: Number,
                default: 2
            },
            multiple: {
                type: Boolean,
                default: false,
            }
        },

        data() {
            return {
                over: false,
            }
        },

        computed: {
            defaultAttributes() {
                return {
                    class: this.errorClasses(),
                }
            },

            extraAttributes() {
                const attrs = this.field.extraAttributes

                return {
                    ...this.defaultAttributes,
                    ...attrs,
                }
            },

            extensions() {
                if (! this.accept.length)
                    return false

                return this.accept.map(ext => '.' + ext).join(', ')
            }
        },

        methods: {
            checkSize(file) {
                if (! file.errors)
                    file.errors = [];

                if (file.size > this.maxSize * 1000000) {
                    file.errors.push(this.__('Max file size is :sizeMB', {size: this.maxSize}))
                    return false
                }

                return true
            },

            drop(event) {
                event.preventDefault()
                this.over = false

                let files = event.dataTransfer.files;

                this.load(files)

                return false
            },

            initLoad(files) {
                [...files].forEach(file => this.checkSize(file))

                this.load(files)
            },

            load(files) {
                Array.from(files).forEach(file => {
                    let errors = file.errors
                    file = new File([file], file.name, {type: file.type})
                    let reader = new FileReader()
                    reader.readAsDataURL(file)
                    reader.onload = () => {
                        const fileData = {
                            file: file,
                            full_urls: {
                                default: reader.result,
                            },
                            name: file.name,
                            file_name: file.name,
                            errors: errors,
                            loading: false
                        }
                        if (this.multiple) {
                            this.field.value.push(fileData)
                        } else {
                            this.handleChange([fileData])
                        }

                        this.$emit('input', this.field.value)
                    }
                })
            },

            removeLoaded(index) {
                this.field.value = this.field.value.filter((value, i) => i !== index)

                this.$emit('input', this.field.value)
            },
        },
    }
</script>

