<template>
    <div class="status-field" v-if="field.value !== null" :class="{'status-field--small': field.small}">
        <span class="status-field__icon mr-2" v-if="field.value.color" :class="'bg-' + field.value.color" :style="field.value.color.charAt(0) == '#' ? 'background-color: ' + field.value.color : ''"></span>
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
            min-width: 16px;
            border-radius: 8px;
        }

        &__text {
            line-height: 1;
        }

        &--small {
            flex-direction: column;

            .status-field__text {
                font-size: 12px;
            }
            .status-field__icon {
                margin-right: 0 !important;
            }
        }
    }
</style>
