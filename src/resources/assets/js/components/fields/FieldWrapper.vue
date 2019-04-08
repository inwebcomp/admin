<template>
    <div class="form__group" :class="classes">
        <slot/>
    </div>
</template>

<script>
    export default {
        props: {
            inline: {
                type: Boolean,
                default: false
            },
            size: {},
        },

        computed: {
            classes() {
                let classes = []

                if (this.inline) {
                    classes.push('form__group--inline')
                } else {
                    // classes.push('px-2')
                }

                if (! this.inline && this.size)
                    classes.push(this.size)

                return classes
            }
        },
    }
</script>
