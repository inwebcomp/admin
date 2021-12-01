<template>
    <div class="status-field" v-if="field.value !== null" :class="{'status-field--small': field.small, 'status-field--only-icon': field.onlyIcon}">
        <span class="status-field__icon mr-2" :title="field.value.title" v-if="field.value.color" :class="'bg-' + field.value.color + ' ' + (field.onlyIcon ? 'ml-2' : '')" :style="field.value.color.charAt(0) == '#' ? 'background-color: ' + field.value.color : ''"></span>
        <span class="status-field__text" v-if="!field.onlyIcon">{{ field.value.title }}</span>
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

        &--only-icon {
            display: block;
            text-align: center;
        }
    }
</style>
