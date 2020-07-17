<template>
    <aside id="sidebar" :class="{'sidebar--grouped': grouped}">
        <div class="sidebar" :class="{'styled-scrollbar': !grouped}">
            <div v-for="(group, $i) of groups" :key="$i" class="sidebar__item"
                 :class="{ 'sidebar__item--active': isSelected(group, $i) }">
                <router-link v-if="grouped" :to="{
                            name: group.resources[0].route || 'index',
                            params: {
                                resourceName: group.resources[0].uriKey
                            }
                        }" class="sidebar__item__link" :title="group.label">
                    <i class="icon fas" :class="'fa-' + (group.icon ? group.icon : 'circle')"></i>
                </router-link>

                <div class="sidebar__children" :class="{'styled-scrollbar': grouped}">
                    <router-link :to="{
                            name: resource.route || 'index',
                            params: {
                                resourceName: resource.uriKey
                            }
                        }" class="sidebar__children__item"
                                 :title="resource.label"
                                 :class="{ 'sidebar__children__item--active': isSelectedItem(resource) }"
                                 v-for="(resource, $i2) of group.resources" :key="$i2">
                        {{ resource.label }}
                        <div class="sidebar__children__item__notification" v-if="resource.notification">{{
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
        name: "app-menu",

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
