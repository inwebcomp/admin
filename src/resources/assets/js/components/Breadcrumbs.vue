<template>
    <div class="breadcrumbs">
        <template v-for="(item, $i) in items">
            <i class="fal fa-angle-right breadcrumbs__separator" v-if="$i > 0"></i>
            <div class="breadcrumbs__item link" :class="{'breadcrumbs__item--home': item.root}" @click="go(item.id)">
                <i v-if="item.root" class="icon fas fa-home"></i>
                <template v-if="!item.root">
                    {{ item.title }}
                </template>
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        name: "Breadcrumbs",

        props: {
            items: {
                type: Array,
                default: null
            },
        },

        methods: {
            go(id) {
                App.$emit('parentSelect', id)
            }
        },
    }
</script>