<template>
    <div>
        <table-params :navigate="! isNested" @destroy="destroy" />

        <breadcrumbs v-if="isNested" :items="breadcrumbs.path" :options="breadcrumbs.options" :value="selected" />

        <data-table class="floating-panel__padding"
                    :resources="resources"
                    :loading="loading"
                    @input="resources = $event"
                    :sortable="sortable"
                    @sort="savePositions"></data-table>

        <floating-panel>
            <pagination :pagination="pagination" @changePage="changePage"></pagination>
        </floating-panel>
    </div>
</template>

<script>
    import Api from "~js/api"

    export default {
        name: "index",
        props: [
            'controller',
            'object',
        ],

        data() {
            return {
                resources: [],
                positions: [],
                pagination: {},
                breadcrumbs: [],
                loading: {
                    type: Boolean,
                    default: true
                }
            }
        },

        watch: {
            controller: {
                handler() {
                    this.fetch()
                },
                immediate: true
            },
        },

        computed: {
            isNested() {
                return this.breadcrumbs && this.breadcrumbs.path && this.breadcrumbs.path.length
            },
            sortable() {
                return this.$store.state.resource.info.positionable
            },
            selected() {
                return this.$store.state.resource.selected
            },
        },

        created() {
            // App.$on('resourceUpdate', () => {
            //     this.fetch()
            // })

            App.$on('resourceDestroyed', () => {
                this.fetch()
            })

            App.$on('resourceStore', () => {
                this.fetch()
            })

            App.$on('parentSelect', (parent) => {
                this.fetch(parent)
            })

            App.$on('indexRefresh', () => {
                this.fetch()
            })


            App.$on('back', () => {
                if (this.isNested && this.breadcrumbs.path.length >= 2)
                    App.$emit('parentSelect', this.breadcrumbs.path[this.breadcrumbs.path.length - 2].id)
            })
        },

        destroyed() {
            App.$off('resourceUpdate')
            App.$off('resourceDestroyed')
            App.$off('resourceStore')
            App.$off('parentSelect')
            App.$off('indexRefresh')
        },

        methods: {
            changePage(page) {
                this.$router.replace({
                    query: {page}
                })

                this.fetch()

                window.scrollTo(0, 0)
            },

            fetch(parent = null) {
                this.loading = true

                Api.resource({
                    controller: this.controller, params: {
                        page: parent ? null : this.$route.query.page || null,
                        parent
                    }
                }).then(({resources, pagination, breadcrumbs}) => {
                    this.resources = resources
                    this.pagination = pagination
                    this.breadcrumbs = breadcrumbs
                    this.loading = false

                    this.positions = []
                    this.resources.forEach(item => {
                        this.positions.push(item.id.position)
                    })
                })
            },

            savePositions() {
                Api.request({
                    method: 'PUT',
                    controller: this.controller,
                    action: 'positions',
                    data: {
                        items: this.resources.map(item => item.id.value),
                        positions: this.positions
                    }
                }).then(() => {
                    App.$emit('positionsUpdated')

                    this.$toasted.show(
                        this.__('Positions was updated!'),
                        {type: 'success'}
                    )
                })
            },

            destroy() {
                if (! confirm(this.__('Are you sure to delete these records?')))
                    return

                App.api.request({
                    method: 'DELETE',
                    url: this.controller + '/destroy',
                    data: {
                        resources: this.selected
                    }
                }).then(() => {
                    App.$emit('resourcesDestroyed', this.selected)

                    this.fetch()
                    this.$store.dispatch('resource/clearSelected')

                    this.$toasted.show(
                        this.__('Records were deleted!', {
                            resource: this.controller,
                        }),
                        {type: 'success'}
                    )
                })
            }
        }
    }
</script>
