<template>
    <div class="fixed-content">
        <form @submit.prevent="save">
            <div class="scrollable-content">
                <active-panel :title="title" :id="resourceId" :accent="accent" :href="href" class="active-panel--static"
                              :backRoute="$route.fullPath.substr(0, $route.fullPath.indexOf('#'))">
                    <custom-actions :resourceName="resourceName" :resourceId="resourceId"/>
                </active-panel>

                <div class="px-4">
                    <div class="tabs">
                        <div v-for="(tab, $i) in visiblePanels" :key="$i" class="tab"
                             :class="{'tab--active': activeTab == tab.id}"
                             @click="changeTab(tab)" v-text="tab.name"></div>

                        <div class="tabs__more" v-if="hiddenPanels.length">
                            <div class="tab" @click.stop="toggleTabsMenu">
                                <i class="far fa-ellipsis-v"></i>
                            </div>
                            <div class="dropdown">
                                <transition name="dropdown">
                                    <div v-show="showTabsMenu" v-click-outside="hideTabsMenu"
                                         class="dropdown__container rounded overflow-hidden dropdown__container--right">
                                        <div class="dropdown__option font-bold"
                                             :class="{'bg-accent text-white hover:bg-accent hover:text-white': activeTab == tab.id}"
                                             @click="changeTab(tab)" v-for="(tab, $i) in hiddenPanels" :key="$i">{{
                                            tab.name }}
                                        </div>
                                    </div>
                                </transition>
                            </div>
                        </div>
                    </div>

                    <component
                            class="card--with-tabs"
                            v-for="(panel, $i) in availablePanels"
                            :key="$i"
                            v-show="activeTab == panel.id"
                            :withHeader="false"
                            :is="panel.component"
                            :resource-name="resourceName"
                            :resource-id="resourceId"
                            :resource="resource"
                            :panel="panel"
                            :errors="validationErrors"
                    ></component>
                </div>
            </div>

            <floating-panel
                    v-if="resource && (resource.authorizedToUpdate || resource.authorizedToCreate || resource.authorizedToDelete)"
                    class="floating-panel--action-buttons">
                <app-button v-if="resource.authorizedToUpdate" class="mr-4" submit type="save" :loading="loading">{{
                    __('Сохранить') }}
                </app-button>

                <router-link v-if="resource.authorizedToCreate"
                             :to="$makeRoute.create(resourceName)"
                             class="mr-auto">
                    <app-button type="link">{{ __('Добавить') }}</app-button>
                </router-link>

                <app-button v-if="resource.authorizedToDelete" type="destroy" @click.native="destroy"></app-button>
            </floating-panel>
        </form>
    </div>
</template>

<script>
    import {Errors} from 'form-backend-validation'

    export default {
        name: "edit",

        props: [
            'resourceName',
            'resourceId',
        ],

        data: () => ({
            resource: null,
            info: null,
            panels: [],
            loading: true,
            validationErrors: new Errors(),
            lastRetrievedAt: null,
            activeTab: 0,
            showTabsMenu: false,
        }),

        watch: {
            resourceId: {
                handler() {
                    this.fetch()
                },
                immediate: true
            }
        },

        created() {
            App.$on('actionExecuted', this.fetch)

            let tabs = sessionStorage.getItem('openedTabs')

            if (tabs)
                tabs = JSON.parse(tabs)

            if (tabs && tabs[this.resourceName]) {
                this.activeTab = tabs[this.resourceName]

                this.$nextTick(() => {
                    App.$emit('editFormTabChange', this.activeTab)
                })
            }
        },

        destroyed() {
            App.$off('actionExecuted')
        },

        methods: {
            fetch() {
                App.api.resource({
                    resourceName: this.resourceName,
                    action: 'edit',
                    resourceId: this.resourceId
                }).then(({resource, panels, info}) => {
                    this.resource = resource
                    this.info = info
                    this.panels = panels
                    this.loading = false

                    this.updateLastRetrievedAtTimestamp()
                }).catch(({status}) => {
                    if (status == 404) {
                        this.$toasted.show(
                            this.__("The :resource can't be found!", {
                                resource: this.resourceName,
                            }),
                            {type: 'error'}
                        )
                    }
                })
            },

            toggleTabsMenu() {
                this.showTabsMenu = !this.showTabsMenu
            },

            hideTabsMenu(event) {
                if (this.showTabsMenu)
                    this.showTabsMenu = false
            },

            /**
             * Update the last retrieved at timestamp to the current UNIX timestamp.
             */
            updateLastRetrievedAtTimestamp() {
                this.lastRetrievedAt = Math.floor(new Date().getTime() / 1000)
            },

            createPanelForField(field) {
                return _.tap(_.find(this.panels, panel => panel.name == field.panel), panel => {
                    panel.fields = [field]
                })
            },

            save() {
                App.$emit('saveResource', this.resource)

                this.loading = true

                App.api.action({
                    resourceName: this.resourceName,
                    action: 'update',
                    resourceId: this.resourceId,
                    data: this.updateResourceFormData()
                }).then(() => {
                    this.loading = false

                    App.$emit('resourceUpdate', this.resourceId)

                    this.$toasted.show(
                        this.__('The :resource was updated!', {
                            resource: this.resourceName,
                        }),
                        {type: 'success'}
                    )

                    this.validationErrors = new Errors()

                    this.fetch()
                }).catch(({data, status}) => {
                    this.loading = false

                    if (status == 422) {
                        this.validationErrors = new Errors(data.errors)
                    }

                    if (status == 409) {
                        this.$toasted.show(
                            this.__('Another user has updated this resource since this page was loaded. Please refresh the page and try again.'),
                            {type: 'error'}
                        )
                    }
                })
            },

            destroy() {
                if (!confirm(this.__('Are you sure to delete this record?')))
                    return

                App.api.request({
                    method: 'DELETE',
                    url: this.resourceName + '/destroy',
                    data: {
                        resources: [this.resourceId]
                    }
                }).then(() => {
                    App.$emit('resourceDestroyed', this.resourceId)

                    this.$router.push({name: 'index', params: {resourceName: this.resourceName}})

                    this.$toasted.show(
                        this.__('The :resource was deleted!', {
                            resource: this.resourceName,
                        }),
                        {type: 'success'}
                    )
                })
            },

            updateResourceFormData() {
                return _.tap(new FormData(), formData => {
                    _(this.panels).each(panel => {
                        _(panel.fields).filter(field => !field.disabled).each(field => {
                            if (field.fill)
                                field.fill(formData)
                        })
                    })

                    formData.append('_method', 'PUT')
                    formData.append('_retrieved_at', this.lastRetrievedAt)
                })
            },

            changeTab(tab) {
                let index = tab.id
                this.activeTab = index

                App.$emit('editFormTabChange', tab)

                if (!sessionStorage.getItem('openedTabs')) {
                    sessionStorage.setItem('openedTabs', JSON.stringify({}))
                }

                let tabs = sessionStorage.getItem('openedTabs')

                if (tabs)
                    tabs = JSON.parse(tabs)

                tabs[this.resourceName] = index

                sessionStorage.setItem('openedTabs', JSON.stringify(tabs))

                this.hideTabsMenu()
            }
        },

        computed: {
            title() {
                return this.info ? this.info.singularLabel : null
            },

            accent() {
                return this.info ? this.info.title : null
            },

            href() {
                return this.info ? this.info.href : null
            },

            // accent() {
            //     return (this.availablePanels && this.availablePanels[0].fields[0]) ? this.availablePanels[0].fields[0].value : null
            // },

            availablePanels() {
                if (this.resource) {
                    let panels = {}

                    let fields = _.toArray(JSON.parse(JSON.stringify(this.resource.fields)))

                    fields.forEach(field => {
                        if (!field.panel)
                            return

                        if (panels[field.panel]) {
                            return panels[field.panel].fields.push(field)
                        }

                        panels[field.panel] = this.createPanelForField(field)
                    })

                    return _.toArray(panels).map((panel, index) => {
                        panel.id = index
                        return panel
                    })
                }
            },

            visiblePanels() {
                return this.availablePanels ? this.availablePanels.filter(panel => ! panel.hidden) : []
            },

            hiddenPanels() {
                return this.availablePanels ? this.availablePanels.filter(panel => panel.hidden) : []
            },

            actionPermissions() {
                return []
            }
        },
    }
</script>
