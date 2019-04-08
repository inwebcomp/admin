<template>
    <aside id="sidebar">
        <div class="sidebar">
            <div v-for="(group, $i) of groups" :key="$i" class="sidebar__item"
                 :class="{ 'sidebar__item--active': isSelected(group) }">
                <router-link :to="{
                            name: group.resources[0].route || 'index',
                            params: {
                                controller: group.resources[0].uriKey
                            }
                        }" class="sidebar__item__link" :title="group.label">
                    <i class="icon fas" :class="'fa-' + (group.icon ? group.icon : 'circle')"></i>
                </router-link>

                <div class="sidebar__children">
                    <router-link :to="{
                            name: resource.route || 'index',
                            params: {
                                controller: resource.uriKey
                            }
                        }" class="sidebar__children__item" :title="resource.label"
                                 v-for="(resource, $i2) of group.resources" :key="$i2">
                        {{ resource.label }}
                    </router-link>
                </div>
            </div>
        </div>
    </aside>
</template>

<script>
    import Api from "~js/api"

    export default {
        name: "app-menu",

        data() {
            return {
                groups: []
            }
        },

        props: [
            'controller',
            'action',
            'param',
        ],

        watch: {
            controller: {
                handler: 'fetch',
                immediate: true
            }
        },

        methods: {
            fetch() {
                Api.request({controller: 'admin-menu', action: 'menu'}).then((data) => this.groups = data)
            },

            isSelected(group) {
                return true;
            }
        }
    }
</script>
