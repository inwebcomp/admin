<template>
    <div class="dropdown__container select-none" :class="menuClasses" :style="styles">
        <slot />
    </div>
</template>

<script>
export default {
    props: {
        direction: {
            type: String,
            default: 'ltr',
            validator: value => ['ltr', 'rtl'].indexOf(value) != -1,
        },
        width: {
            default: 120,
        },
    },
    computed: {
        menuClasses() {
            return [this.direction == 'ltr' ? 'dropdown__container--left' : 'dropdown__container--right']
        },
        styles() {
            return {
                width: `${this.width}px`,
            }
        },
    },
}
</script>
