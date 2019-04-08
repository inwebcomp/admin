<template>
    <label :for="labelFor" class="form__group__label pr-4" :class="{'w-1/5': inline}">
        <slot/>
    </label>
</template>

<script>
    export default {
        props: {
            labelFor: {
                type: String,
            },
            inline: {
                type: Boolean,
                default: false
            },
        },
    }
</script>
