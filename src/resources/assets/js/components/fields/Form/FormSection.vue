<template>
    <div class="form__section text-center my-4 mx-2">
        <div class="form__section__text text-sm font-semibold text-grey-darker p-2">{{ field.name }}</div>
        <hr class="form__section__hr border-b">
    </div>
</template>

<style lang="scss">
    .form__section {
        flex-basis: 100%;
        height: 18px;
    }

    .form__section__text {
        display: inline-block;
        background-color: #FFF;
        padding: 0 1rem;
        z-index: 1;
    }

    .form__section__hr {
        margin-top: -7px;
    }
</style>

<script>
    export default {
        props: {
            field: {
                type: Object,
                required: true,
            },
        },

        computed: {
            extraAttributes() {
                const attrs = this.field.extraAttributes

                return {
                    ...attrs,
                }
            },
        },
    }
</script>