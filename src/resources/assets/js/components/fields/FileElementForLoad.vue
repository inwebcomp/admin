<template>
    <div class="file-field__element file-field__element--for-load file-field__image" :class="{'file-field__element--error': errors.length}">
        <slot></slot>

        <div class="file-field__element__overlay" @click="act">
            <svg v-if="! loading && ! errors.length" class="file-field__load-area__icon w-8 h-8" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z" />
            </svg>

            <i class="fas fa-spinner-third file-field__element__loading" v-if="loading"></i>

            <div class="file-field__element__error" v-for="(error) in errors">{{ error }}</div>

            <div class="file-field__element__remove" @click.stop="$emit('remove')">
                <i class="far fa-times"></i>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'FileElementForLoad',

        props: {
            loading: {
                type: Boolean,
                default: false
            },
            errors: {
                type: Array,
                default: () => {}
            },
        },

        methods: {
            act() {
               if (this.errors.length) {
                   this.$emit('remove')
                   return
               }

               this.$emit('upload')
            }
        },
    }
</script>
