<template>
    <div class="flex">
        <template v-if="typeof field.value == 'object'">
            <div class="mr-2" v-for="(image, $i) in images">
                <img v-if="image" class="m-auto block h-12" :src="img(image)" />
                <div style="width: 42px" v-if="! image"></div>
            </div>
            <div v-if="hasMore" class="more-images text-xl text-grey">
                <i class="fal fa-plus"></i>
            </div>
        </template>
        <template v-if="typeof field.value != 'object'">
            <img v-if="field.value" class="m-auto block h-12" :src="img(field.value)" />
            <div style="width: 42px" v-if="! field.value"></div>
        </template>
    </div>
</template>

<script>
    export default {
        props: ['field'],

        computed: {
            hasMore() {
                if (! this.field.limit)
                    return false

                return this.field.value.length > this.field.limit
            },

            images() {
                if (! this.field.limit)
                    return this.field.value

                return this.field.value.slice(0, this.field.limit)
            }
        }
    }
</script>

<style scoped lang="scss">
    .more-images {
        line-height: 42px;
        text-align: center;
    }
</style>