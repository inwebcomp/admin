<template>
    <div class="status-field">
        <span class="status-field__icon mr-2" v-if="field.value.color" :class="'bg-' + field.value.color"></span>
        <span class="status-field__text">{{ field.value.title }}</span>
    </div>
</template>

<script>
    export default {
        props: ['field'],
    }
</script>

<style lang="scss" scoped>
    .status-field {
        display: flex;
        align-items: center;

        &__icon {
            display: inline-block;
            color: #222;
            height: 16px;
            line-height: 16px;
            width: 16px;
            border-radius: 8px;
        }

        &__text {
            line-height: 1;
        }
    }
</style>
