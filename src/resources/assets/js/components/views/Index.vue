<template>
    <div>
        <table-params :title="title"
                      :resourceName="resourceName"
                      :navigate="! isNested"
                      :create="authorizedToCreate"
                      :remove="authorizedToDelete"
                      @destroy="destroy"
                      @clear-selected-filters="clearSelectedFilters"
                      @filter-changed="filterChanged"
                      @clear-selected-orderings="clearSelectedOrderings"
                      @ordering-changed="orderingChanged"
                      :search="search" @search="performSearch"/>

        <breadcrumbs v-if="isNested" :items="breadcrumbs.path" :options="breadcrumbs.options" :value="selected"/>

        <data-table :resources="resources"
                    :resourceName="resourceName"
                    :loading="loading"
                    @input="resources = $event"
                    :sortable="sortable"
                    @sort="savePositions"></data-table>

        <floating-panel>
            <pagination :pagination="pagination" @changePage="changePage"></pagination>
            <resource-count :pagination="pagination"></resource-count>
        </floating-panel>
    </div>
</template>

<script>
    import Api from "~js/api"
    import Filterable from "~mixins/Filterable"
    import Orderable from "~mixins/Orderable"
    import Searchable from "~mixins/Searchable"
    import InteractsWithQueryString from "~mixins/InteractsWithQueryString"

    export default {
        name: "index",

        props: [
            'resourceName',
            'resourceId',
        ],

        mixins: [
            Filterable,
            Orderable,
            Searchable,
            InteractsWithQueryString,
        ],

        data() {
            return {
                resources: [],
                info: null,
                positions: [],
                pagination: {},
                breadcrumbs: [],
                authorizedToCreate: false,
                authorizedToDelete: false,
                loading: false,
            }
        },

        computed: {
            isNested() {
                return this.breadcrumbs && this.breadcrumbs.path && this.breadcrumbs.path.length
            },
            sortable() {
                return this.info && this.info.positionable
            },
            selected() {
                return this.$store.state.resource.selected
            },

            title() {
                return this.info ? this.info.label : null
            },

            /**
             * Get the name of the page query string variable.
             */
            pageParameter() {
                return 'page'
            },

            /**
             * Return the currently encoded filter string from the store
             */
            encodedFilters() {
                return this.$store.getters[`${this.resourceName}/currentEncodedFilters`]
            },

            /**
             * Return the initial encoded filters from the query string
             */
            initialEncodedFilters() {
                return this.$route.query[this.filterParameter] || ''
            },
        },

        watch: {
            resourceName() {
                this.initializeSearchFromQueryString()
                this.initializeOrderings()
            }
        },

        async created() {
            this.initializeSearchFromQueryString()

            await this.initializeFilters()
            await this.initializeOrderings()
            await this.fetch()

            App.$on('resourceUpdate', () => {
                this.fetch()
            })

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

            App.$on('actionExecuted', () => {
                this.fetch()
            })

            App.$on('back', () => {
                if (this.isNested && this.breadcrumbs.path.length >= 2)
                    App.$emit('parentSelect', this.breadcrumbs.path[this.breadcrumbs.path.length - 2].id)
            })

            this.$watch(
                () => {
                    return (
                        this.resourceName +
                        this.currentOrderBy +
                        this.currentOrderByDirection +
                        this.currentSearch +
                        this.initialEncodedFilters
                    )
                },
                async () => {
                    await this.initializeFilters()
                    await this.fetch()
                }
            )
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
                this.updateQueryString({
                    [this.pageParameter]: page,
                })

                this.fetch()

                window.scrollTo(0, 0)
            },

            fetch(parent = null) {
                this.loading = true

                App.api.resource({
                    resourceName: this.resourceName, params: this.resourceRequestQueryString(parent)
                }).then(({
                     resources,
                     info,
                     pagination,
                     breadcrumbs,
                     authorizedToCreate,
                     authorizedToDelete,
                 }) => {
                    this.resources = resources
                    this.info = info
                    this.pagination = pagination
                    this.breadcrumbs = breadcrumbs
                    this.authorizedToCreate = authorizedToCreate
                    this.authorizedToDelete = authorizedToDelete
                    this.loading = false

                    this.positions = []
                    this.resources.forEach(item => {
                        this.positions.push(item.id.position)
                    })
                })
            },

            /**
             * Build the resource request query string.
             */
            resourceRequestQueryString(parent) {
                return {
                    filters: this.encodedFilters,
                    orderBy: this.currentOrderBy,
                    orderByDirection: this.currentOrderByDirection,
                    search: this.currentSearch,
                    parent,
                    page: parent ? null : this.$route.query.page || null,
                }
            },

            savePositions() {
                App.api.request({
                    method: 'PUT',
                    resourceName: this.resourceName,
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
                if (!confirm(this.__('Are you sure to delete these records?')))
                    return

                App.api.request({
                    method: 'DELETE',
                    url: this.resourceName + '/destroy',
                    data: {
                        resources: this.selected
                    }
                }).then(() => {
                    App.$emit('resourcesDestroyed', this.selected)

                    this.fetch()
                    this.$store.dispatch('resource/clearSelected')

                    this.$toasted.show(
                        this.__('Records were deleted!', {
                            resource: this.resourceName,
                        }),
                        {type: 'success'}
                    )
                })
            }
        }
    }
</script>
