import _ from 'lodash'

export default {
    namespaced: true,

    state: {
        orderings: [],
        ordering: {
            field: '',
            direction: ''
        },
        originalOrdering: {
            field: '',
            direction: '',
        },
        defaultOrdering: {
            field: '',
            direction: '',
        },
    },

    getters: {
        orderings: state => state.orderings,

        originalOrdering: state => state.originalOrdering,

        hasOrderings: state => Boolean(state.orderings.length > 0),

        currentOrdering: (state) => {
            let obj = state.orderings.find(ordering => ordering.field == state.ordering.field)
            let ordering = Object.assign({}, obj)

            if (!obj)
                return state.defaultOrdering

            ordering.direction = state.ordering.direction
            return ordering
        },

        orderingsAreApplied: (state) => {
            return !_.isEqual(state.ordering, state.originalOrdering)
        },
    },

    actions: {
        /**
         * Fetch the current orderings for the given resource name.
         */
        async fetchOrderings({commit, state}, {resourceName}) {
            const {orderings, defaultOrdering} = await App.api.request({
                url: resourceName + '/orderings'
            })

            commit('storeOrderings', orderings)
            commit('setDefaultOrdering', defaultOrdering)
        },

        /**
         * Reset the default ordering state to the original ordering settings.
         */
        async resetOrderingState({commit, getters}) {
            commit('setOrdering', {
                field: getters.originalOrdering.field,
                direction: getters.originalOrdering.direction,
            })
        },
    },

    mutations: {
        setOrdering(state, {field, direction}) {
            state.ordering = {
                field,
                direction
            }
        },

        /**
         * Store the mutable ordering settings
         */
        storeOrderings(state, data) {
            state.orderings = data
        },

        /**
         * Store the mutable ordering settings
         */
        setDefaultOrdering(state, data) {
            if (! state.ordering.field) {
                state.ordering = {
                    field: data.field,
                    direction: data.direction,
                }
            }

            state.originalOrdering = {
                field: data.field,
                direction: data.direction,
            }
        },

        /**
         * Clear the orderings for this resource
         */
        clearOrderings(state) {
            state.orderings = []
            state.originalOrdering = {
                field: '',
                direction: '',
            }
        },
    },
}
