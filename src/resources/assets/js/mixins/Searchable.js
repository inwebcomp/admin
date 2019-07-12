export default {
    data() {
        return {
            search: '',
        }
    },

    computed: {
        /**
         * Get the name of the search query string variable.
         */
        searchParameter() {
            return this.resourceName + '_search'
        },

        /**
         * Get the current search value from the query string.
         */
        currentSearch() {
            return this.$route.query[this.searchParameter] || ''
        },
    },

    methods: {
        /**
         * Execute a search against the resource.
         */
        performSearch(query) {
            this.search = query

            this.updateQueryString({
                [this.pageParameter]: 1,
                [this.searchParameter]: query,
            })
        },

        /**
         * Sync the current search value from the query string.
         */
        initializeSearchFromQueryString() {
            this.search = this.currentSearch
        },
    },
}