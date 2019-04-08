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
                <app-button submit type="save" :loading="loading">{{ __('Добавить') }}</app-button>
            </floating-panel>
        </form>
    </div>
</template>

<script>
    import {Errors} from 'form-backend-validation'

    export default {
        name: "create",

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

        created() {
            this.fetch()
        },

        methods: {
            fetch() {
                App.api.resource({
                    controller: this.controller,
                    action: 'create',
                }).then(({resource, panels}) => {
                    this.resource = resource
                    this.panels = panels
                    this.loading = false
                })
            },

            createPanelForField(field) {
                return _.tap(_.find(this.panels, panel => panel.name == field.panel), panel => {
                    panel.fields = [field]
                })
            },

            save() {
                this.loading = true

                App.api.action({
                    controller: this.controller,
                    action: 'store',
                    object: this.object,
                    data: this.storeResourceFormData
                }).then(({redirect}) => {
                    this.loading = false

                    App.$emit('resourceStore', this.object)

                    this.$toasted.show(
                        this.__('The :resource was created!', {
                            resource: this.controller,
                        }),
                        {type: 'success'}
                    )

                    this.validationErrors = new Errors()

                    if (redirect) {
                        this.$router.push(redirect)
                    }
                }).catch(({data, status}) => {
                    this.loading = false

                    if (status == 422) {
                        this.validationErrors = new Errors(data.errors)
                    }
                })
            },
        },

        computed: {
            title() {
                return this.__('Добавить в')
            },

            accent() {
                return this.resource ? this.$store.state.resource.info.label : null
            },

            availablePanels() {
                if (this.resource) {
                    let panels = {}

                    let fields = _.toArray(JSON.parse(JSON.stringify(this.resource.fields)))

                    fields.forEach(field => {
                        if (panels[field.panel]) {
                            return panels[field.panel].fields.push(field)
                        }

                        panels[field.panel] = this.createPanelForField(field)
                    })

                    return _.toArray(panels)
                }
            },

            storeResourceFormData() {
                return _.tap(new FormData(), formData => {
                    _(this.panels).each(panel => {
                        _(panel.fields).each(field => {
                            field.fill(formData)
                        })
                    })

                    formData.append('_method', 'POST')
                    formData.append('_retrieved_at', this.lastRetrievedAt)
                })
            },
        },
    }
</script>
