<template>
    <BasePartitionMetric
            :title="card.name"
            :help-text="card.helpText"
            :help-width="card.helpWidth"
            :chart-data="chartData"
            :loading="loading"
    />
</template>

<script>
    import Minimum from '~util/Minimum'
    import BasePartitionMetric from './Base/PartitionMetric'
    import MetricBehavior from './MetricBehavior'

    export default {
        mixins: [MetricBehavior],

        components: {
            BasePartitionMetric,
        },

        props: {
            card: {
                type: Object,
                required: true,
            },

            resourceName: {
                type: String,
                default: '',
            },

            resourceId: {
                type: [Number, String],
                default: '',
            },

            lens: {
                type: String,
                default: '',
            },
        },

        data: () => ({
            loading: true,
            chartData: [],
        }),

        watch: {
            resourceId() {
                this.fetch()
            },
        },

        created() {
            this.fetch()
        },

        methods: {
            fetch() {
                this.loading = true

                Minimum(App.api.request({
                    url: this.metricEndpoint,
                })).then(({
                              value: {value},
                          }) => {
                        this.chartData = value
                        this.loading = false
                    },
                )
            },
        },
        computed: {
            metricEndpoint() {
                const lens = this.lens !== '' ? `/lens/${this.lens}` : ''
                if (this.resourceName && this.resourceId) {
                    return `${this.resourceName}${lens}/${this.resourceId}/metrics/${this.card.uriKey}`
                } else if (this.resourceName) {
                    return `${this.resourceName}${lens}/metrics/${this.card.uriKey}`
                } else {
                    return `metrics/${this.card.uriKey}`
                }
            },
        },
    }
</script>
