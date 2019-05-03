<template>
    <button :type="getType" class="button" :class="classes">
        <slot>
            <i v-if="type == 'destroy'" class="far fa-trash-alt"></i>
        </slot>
    </button>
</template>

<script>
    export default {
        name: "app-button",

        props: {
            type: {
                type: String,
                default: null
            },

            submit: {
                type: Boolean,
                default: false
            },

            loading: {
                type: Boolean,
                default: false
            },
        },

        watch: {
            loading(newValue) {
                if (newValue) {
                    this.$el.style.width = this.$el.offsetWidth + 'px'
                } else {
                    this.$el.style.width = null
                }
            }
        },

        computed: {
            classes() {
                let classes = []

                if (this.type == 'save' || this.type == 'add')
                    classes.push('button--success')
                else if (this.type == 'destroy')
                    classes.push('button--danger button--icon')
                else if (this.type == 'accent')
                    classes.push('button--accent')

                if (this.loading)
                    classes.push('button--loading')

                return classes
            },

            getType() {
                return this.submit ? 'submit' : 'button'
            }
        }
    }
</script>