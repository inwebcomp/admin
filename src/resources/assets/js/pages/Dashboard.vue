<template>
    <aside class="m-4">
        <div>
            <heading class="mb-6 mt-6">{{ label }}</heading>

            <div v-if="shouldShowCards" class="-mx-3">
                <cards v-if="smallCards.length > 0" :cards="smallCards" class="mb-3"/>
                <cards v-if="largeCards.length > 0" :cards="largeCards" size="large"/>
            </div>
        </div>
    </aside>
</template>

<script>
    import CardSizes from '~util/cardSizes'

    export default {
        name: "dashboard",

        metaInfo() {
            return {
                title: `${this.label}`,
            }
        },

        data: () => ({label: '', cards: ''}),

        props: {
            name: {
                type: String,
                required: false,
                default: 'main',
            },
        },

        watch: {
            name() {
                this.fetchDashboard()
            },
        },

        created() {
            this.fetchDashboard()
        },

        methods: {
            async fetchDashboard() {
                const {label, cards} = await App.api.request({
                    url: this.dashboardEndpoint,
                })
                    .catch(e => {
                        this.$router.push({name: '404'})
                    })

                this.label = label
                this.cards = cards
            },
        },

        computed: {
            /**
             * Get the endpoint for this dashboard.
             */
            dashboardEndpoint() {
                return `dashboards/${this.name}`
            },

            /**
             * Determine whether we have cards to show on the Dashboard
             */
            shouldShowCards() {
                return this.cards.length > 0
            },

            /**
             * Return the small cards used for the Dashboard
             */
            smallCards() {
                return _.filter(this.cards, c => CardSizes.indexOf(c.width) !== -1)
            },

            /**
             * Return the full-width cards used for the Dashboard
             */
            largeCards() {
                return _.filter(this.cards, c => c.width == 'full')
            },

            /**
             * Get the extra card params to pass to the endpoint.
             */
            extraCardParams() {
                return null
            },
        },
    }
</script>
