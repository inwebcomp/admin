<template>
    <a
        @click="handleClick"
        class="dropdown__trigger" :class="{'dropdown__trigger': active}"
    >
        <slot />
    </a>
</template>

<script>
export default {
    props: {
        handleClick: Function,
        active: {
            type: Boolean,
            default: false,
        },
    },
}
</script>
