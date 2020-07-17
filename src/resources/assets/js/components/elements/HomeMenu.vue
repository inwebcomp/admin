<template>
    <aside id="home-menu">
        <div class="home-menu">
            <div v-for="(group, $i) of groups" :key="$i" class="home-menu__item flex border-b"
                 :class="{ 'home-menu__item--active': isSelected(group) }">
                <router-link v-if="grouped" :to="{
                            name: group.resources[0].route || 'index',
                            params: {
                                resourceName: group.resources[0].uriKey
                            }
                        }" class="home-menu__item__icon text-center text-3xl h-32 text-grey items-center flex justify-center flex-wrap relative hover:text-accent" :title="group.label">
                    <i class="icon fas" :class="'fa-' + (group.icon ? group.icon : 'circle')"></i>
                </router-link>

                <div class="home-menu__children">
                    <router-link :to="{
                            name: resource.route || 'index',
                            params: {
                                resourceName: resource.uriKey
                            }
                        }"
                                 class="home-menu__children__item text-black bg-white h-32 px-8 text-center text-2xl items-center flex justify-center flex-wrap relative shadow hover:text-accent"
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
    export default {
        name: "home-menu",

        data() {
            return {
                groups: [],
                grouped: App.config.groupedMenu,
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
                immediate: true,
            },
        },

        methods: {
            fetch() {
                App.api.request({resourceName: 'admin-menu', action: 'menu'}).then(({menu}) => {
                    this.groups = menu
                })
            },

            isSelected(group, index) {
                if (index == 0 && this.$route.name == 'home') {
                    return true;
                }

                return !!group.resources.find(resource => {
                    if (this.$route.name == 'index' && this.$route.params.resourceName == resource.uriKey)
                        return true

                    if (this.$route.name != 'index' && this.$route.name == resource.uriKey)
                        return true
                })
            },

            isSelectedItem(resource) {
                if (this.$route.name == 'index' && this.$route.params.resourceName == resource.uriKey)
                    return true

                if (this.$route.name != 'index' && this.$route.name == resource.uriKey)
                    return true

                return false
            },
        },
    }
</script>

<style lang="scss">
    .home-menu__item {
        margin-bottom: 16px;
        padding-bottom: 16px;
    }

    .home-menu__children {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        grid-gap: 16px;
        flex: 1;

        .sidebar__children__item__notification {
            position: absolute;
            top: 16px;
            right: 16px;
        }
    }

    .home-menu__item__icon {
        width: 200px;
        margin-right: 16px;
    }
</style>