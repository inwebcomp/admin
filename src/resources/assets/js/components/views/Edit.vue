<template>
    <div class="fixed-content">
        <form @submit.prevent="save">
            <div class="scrollable-content">
                <active-panel :title="title" :accent="accent"
                              :backRoute="{ name: 'index', params: { controller: this.controller } }"></active-panel>

                <div class="px-4">
                    <component
                            v-for="(panel, $i) in availablePanels"
                            :key="$i"
                            :is="panel.component"
                            :resource-name="controller"
                            :resource-id="object"
                            :resource="resource"
                            :panel="panel"
                            :errors="validationErrors"
                    ></component>
                </div>
            </div>

            <floating-panel class="floating-panel--action-buttons">
                <app-button submit type="save" :loading="loading">{{ __('Сохранить') }}</app-button>
                <app-button type="destroy" @click.native="destroy"></app-button>
            </floating-panel>
        </form>
    </div>
</template>

<script>
    import {Errors} from 'form-backend-validation'

    export default {
        name: "edit",

        props: [
            'controller',
            'object',
        ],

        data: () => ({
            resource: null,
            panels: [],
            loading: true,
            validationErrors: new Errors(),
            lastRetrievedAt: null,
        }),

        watch: {
            object: {
                handler() {
                    this.fetch()
                },
                immediate: true
            }
        },

        methods: {
            fetch() {
                App.api.resource({
                    controller: this.controller,
                    action: 'edit',
                    object: this.object
                }).then(({resource, panels}) => {
                    this.resource = resource
                    this.panels = panels
                    this.loading = false
                }).catch(({status}) => {
                    if (status == 404) {
                        this.$toasted.show(
                            this.__("The :resource can't be found!", {
                                resource: this.controller,
                            }),
                            {type: 'error'}
                        )
                    }
                })

                this.updateLastRetrievedAtTimestamp()
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
                    controller: this.controller,
                    action: 'update',
                    object: this.object,
                    data: this.updateResourceFormData
                }).then(() => {
                    this.loading = false

                    App.$emit('resourceUpdate', this.object)

                    this.$toasted.show(
                        this.__('The :resource was updated!', {
                            resource: this.controller,
                        }),
                        {type: 'success'}
                    )

                    this.validationErrors = new Errors()

                    this.updateLastRetrievedAtTimestamp()
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
                if (! confirm(this.__('Are you sure to delete this record?')))
                    return

                App.api.request({
                    method: 'DELETE',
                    url: this.controller + '/destroy',
                    data: {
                        resources: [this.object]
                    }
                }).then(() => {
                    App.$emit('resourceDestroyed', this.object)

                    this.$router.push({name: 'index', params: {controller: this.controller}})

                    this.$toasted.show(
                        this.__('The :resource was deleted!', {
                            resource: this.controller,
                        }),
                        {type: 'success'}
                    )
                })
            }
        },

        computed: {
            title() {
                return (this.resource) ? this.$store.state.resource.info.singularLabel : null
            },

            accent() {
                return (this.availablePanels && this.availablePanels[0].fields[0]) ? this.availablePanels[0].fields[0].value : null
            },

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

                    return _.toArray(panels)
                }
            },

            updateResourceFormData() {
                return _.tap(new FormData(), formData => {
                    _(this.panels).each(panel => {
                        _(panel.fields).each(field => {
                            if (field.fill)
                                field.fill(formData)
                        })
                    })

                    formData.append('_method', 'PUT')
                    formData.append('_retrieved_at', this.lastRetrievedAt)
                })
            },
        },
    }
</script>
