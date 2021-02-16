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

        <div v-if="shouldShowCards" class="mt-4">
            <cards
                    v-if="smallCards.length > 0"
                    :cards="smallCards"
                    :resource-name="resourceName"
            />

            <cards
                    v-if="largeCards.length > 0"
                    :cards="largeCards"
                    size="large"
                    :resource-name="resourceName"
            />
        </div>

        <data-table v-if="info"
                    :grouped="grouped"
                    :resources="resources"
                    :resourceName="resourceName"
                    :loading="loading"
                    @input="setResources"
                    :sortable="sortable"
                    @sort="savePositions"></data-table>

        <floating-panel>
            <pagination v-if="pagination" :pagination="pagination" @changePage="changePage"></pagination>
            <resource-count :pagination="pagination"></resource-count>
        </floating-panel>
    </div>
</template>

<script>
    import Filterable from "~mixins/Filterable"
    import Orderable from "~mixins/Orderable"
    import Searchable from "~mixins/Searchable"
    import InteractsWithQueryString from "~mixins/InteractsWithQueryString"
    import HasCards from "~mixins/HasCards"

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
            HasCards,
            InteractsWithQueryString,
        ],

        data() {
            return {
                resources: null,
                info: null,
                positions: [],
                pagination: null,
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
            grouped() {
                return this.info && this.info.grouped
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

            /**
             * Determine if the resource should show any cards
             */
            shouldShowCards() {
                // Don't show cards if this resource is beings shown via a relations
                return (
                    this.cards.length > 0 &&
                    this.resourceName == this.$route.params.resourceName
                )
            },

            /**
             * Get the endpoint for this resource's metrics.
             */
            cardsEndpoint() {
                return `${this.resourceName}/cards`
            },
        },

        watch: {
            resourceName() {
                this.initializeSearchFromQueryString()
                this.initializeOrderings()
            },
        },

        async created() {
            this.initializeSearchFromQueryString()

            await this.initializeFilters()
            await this.initializeOrderings()
            await this.fetch()

            App.$on('resourceUpdate', this.fetchDefault)

            App.$on('resourceDestroyed', this.fetchDefault)

            App.$on('resourceStore', this.fetchDefault)

            App.$on('parentSelect', (parent) => {
                this.fetch(parent)
            })

            App.$on('indexRefresh', this.fetchDefault)

            App.$on('actionExecuted', this.fetchDefault)

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
                },
            )
        },

        destroyed() {
            App.$off('resourceUpdate', this.fetchDefault)
            App.$off('resourceDestroyed', this.fetchDefault)
            App.$off('resourceStore', this.fetchDefault)
            App.$off('parentSelect')
            App.$off('indexRefresh', this.fetchDefault)
            App.$off('actionExecuted', this.fetchDefault)
            App.$off('back')
        },

        methods: {
            setResources(resources) {
                this.resources = resources
            },

            changePage(page) {
                this.updateQueryString({
                    [this.pageParameter]: page,
                })

                this.fetch()

                window.scrollTo(0, 0)
            },

            fetchDefault() {
                this.fetch()
            },

            fetch(parent = null) {
                this.loading = true

                App.api.resource({
                    resourceName: this.resourceName, params: this.resourceRequestQueryString(parent),
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

                    if (this.sortable) {
                        this.resources.forEach(item => {
                            this.positions.push(item.id.position)
                        })
                    }
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
                        positions: this.positions,
                    },
                }).then(() => {
                    App.$emit('positionsUpdated')

                    this.$toasted.show(
                        this.__('Positions was updated!'),
                        {type: 'success'},
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
                        resources: this.selected,
                    },
                }).then(() => {
                    App.$emit('resourcesDestroyed', this.selected)

                    this.fetch()
                    this.$store.dispatch('resource/clearSelected')

                    this.$toasted.show(
                        this.__('Records were deleted!', {
                            resource: this.resourceName,
                        }),
                        {type: 'success'},
                    )
                })
            },
        },
    }
</script>
