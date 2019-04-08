<template>
    <div>
        <active-panel popupMode :title="title" :accent="accent" :backRoute="{ name: 'index', params: { controller: this.controller } }"></active-panel>

        <form @submit.prevent="save" class="px-4">
            <card class="card--form" :caption="__('Базовая информация')">
                <div class="p-8">
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


                    <div class="form__group__label"></div>
                    <app-button type="save">{{ __('Сохранить') }}</app-button>
                </div>
            </card>
        </form>
    </div>
</template>

<script>
    export default {
        name: "fast-edit",

        props: [
            'controller',
            'object',
        ],

        data: () => ({
            resource: null,
            panels: [],
            loading: {
                type: Boolean,
                default: true
            }
        }),

        watch: {
            object: {
                handler: 'fetch',
                immediate: true
            }
        },

        methods: {
            fetch() {
                this.loading = true
                App.api.resource(this.controller, 'edit', this.object).then(({resource, panels}) => {
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
        },

        computed: {
            title() {
                return (this.resource) ? this.$store.state.resource.info.singularLabel : null;
            },

            accent() {
                return (this.panels[0] && this.panels[0].fields[0]) ? this.panels[0].fields[0].value : null;
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
        },
    }
</script>
