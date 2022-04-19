<template>
    <div class="breadcrumbs-tree__container">
        <div class="breadcrumbs-tree">
            <template v-for="(item, $i) in items">
                <div class="flex items-center">
                    <i class="fal fa-angle-right breadcrumbs-tree__separator" v-if="$i > 0"></i>
                    <div class="breadcrumbs-tree__item link py-2" :class="{'breadcrumbs__item--home': api.root, 'font-bold': ($i == items.length - 1)}" @click="go(item.id)">
                        <i v-if="api.root" class="icon fas fa-home"></i>
                        <template v-if="!api.root">
                            {{ item.title }}
                        </template>
                    </div>
                </div>
            </template>

            <div>
                <ul>
                    <li v-for="(item, $i) in options" v-if="item.value" :key="$i" class="list-unstyled">
                        <div class="breadcrumbs__item link py-2" :class="{'font-bold': item.value == value}" @click="go(item.value)">
                            {{ item.title }}
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "BreadcrumbsTree",

        props: {
            items: {
                type: Array,
                default: null
            },
            options: {
                type: Array,
                default: null
            },
            value: {},
        },

        methods: {
            go(id) {
                console.log(this.value, this.options, this.items)
                App.$emit('parentSelect', id)
            }
        },

        computed: {
            api() {
                return App.api;
            }
        },
    }
</script>
