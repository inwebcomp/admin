export default {
    data() {
        return {
            orderBy: '',
            orderByDirection: '',
        }
    },

    computed: {
        /**
         * Get the name of the order by query string variable.
         */
        orderByParameter() {
            return this.resourceName + '_order'
        },

        /**
         * Get the name of the order by direction query string variable.
         */
        orderByDirectionParameter() {
            return this.resourceName + '_direction'
        },

        /**
         * Get the current order by value from the query string.
         */
        currentOrderBy() {
            return this.$route.query[this.orderByParameter] || ''
        },

        /**
         * Get the current order by direction from the query string.
         */
        currentOrderByDirection() {
            return this.$route.query[this.orderByDirectionParameter] || ''
        },
    },

    methods: {
        /**
         * Clear orderings and reset the resource table
         */
        async clearSelectedOrderings() {
            await this.$store.dispatch(`${this.resourceName}/resetOrderingState`, {
                resourceName: this.resourceName,
            })

            this.updateQueryString({
                [this.pageParameter]: 1,
                [this.orderByParameter]: '',
                [this.orderByDirectionParameter]: '',
            })
        },

        /**
         * Handle a ordering state change.
         */
        orderingChanged() {
            let ordering = this.$store.getters[`${this.resourceName}/currentOrdering`]

            this.updateQueryString({
                [this.pageParameter]: 1,
                [this.orderByParameter]: ordering.field,
                [this.orderByDirectionParameter]: ordering.direction,
            })
        },

        /**
         * Set up orderings for the current view
         */
        async initializeOrderings() {
            await this.initializeOrderingFromQueryString()

            // Clear out the orderings from the store first
            await this.$store.commit(`${this.resourceName}/clearOrderings`)

            await this.$store.dispatch(`${this.resourceName}/fetchOrderings`, {
                resourceName: this.resourceName,
            })
        },

        /**
         * Sync the current order by values from the query string.
         */
        initializeOrderingFromQueryString() {
            this.orderBy = this.currentOrderBy
            this.orderByDirection = this.currentOrderByDirection

            if (this.orderBy && this.orderByDirection) {
                this.$store.commit(`${this.resourceName}/setOrdering`, {
                    field: this.orderBy,
                    direction: this.orderByDirection,
                })
            }
        },
    },
}