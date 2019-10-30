<template>
    <aside id="home-menu">
        <div class="home-menu">
            <div v-for="(group, $i) of groups" :key="$i" class="home-menu__item"
                 :class="{ 'home-menu__item--active': isSelected(group) }">
                <div class="home-menu__children border-b">
                    <router-link :to="{
                            name: resource.route || 'index',
                            params: {
                                resourceName: resource.uriKey
                            }
                        }"
                                 class="home-menu__children__item text-black bg-white py-16 px-8 text-center text-2xl items-center flex justify-center flex-wrap relative shadow hover:text-accent"
                                 v-for="(resource, $i2) of group.resources" :key="$i2">

                        <div v-if="resource.color" class="w-full h-2 mr-4 absolute" style="bottom: 0;" :class="'bg-' + resource.color"></div>

                        {{ resource.label }}
                        <div class="sidebar__children__item__notification text-white ml-0" v-if="resource.notification">{{
                            resource.notification }}
                        </div>
                    </router-link>
                </div>
            </div>
        </div>
    </aside>
</template>

<script>
    import Api from "~js/api"

    export default {
        name: "home-menu",

        data() {
            return {
                groups: []
            }
        },

        props: [
            'resourceName',
            'action',
            'param',
        ],

        watch: {
            resourceName: {
                handler: 'fetch',
                immediate: true
            }
        },

        methods: {
            fetch() {
                App.api.request({resourceName: 'admin-menu', action: 'menu'}).then((data) => this.groups = data)
            },

            isSelected(group) {
                return true;
            }
        }
    }
</script>

<style lang="scss">
    .home-menu__children {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        grid-gap: 16px;
        padding-bottom: 16px;
        margin-bottom: 16px;

        .sidebar__children__item__notification {
            position: absolute;
            top: 16px;
            right: 16px;
        }
    }
</style>