import defaults from 'lodash/defaults'

export default {
    methods: {
        /**
         * Update the given query string values.
         */
        updateQueryString(value) {
            console.log(value)
            this.$router.push({ query: defaults(value, this.$route.query) })
        },
    },
}