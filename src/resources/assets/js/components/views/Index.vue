<template>
    <div>
        <table-params :navigate="! isNested"></table-params>
        <breadcrumbs v-if="isNested && breadcrumbs.length > 1" :items="breadcrumbs" />
        <data-table class="floating-panel__padding" :resources="resources" :loading="loading"></data-table>

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
                return this.breadcrumbs && this.breadcrumbs.length
            }
        },

        created() {
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


            App.$on('back', () => {
                if (this.isNested && this.breadcrumbs.length >= 2)
                    App.$emit('parentSelect', this.breadcrumbs[this.breadcrumbs.length - 2].id)
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
                        page: this.$route.query.page || null,
                        parent
                    }
                }).then(({resources, pagination, breadcrumbs}) => {
                    this.resources = resources
                    this.pagination = pagination
                    this.breadcrumbs = breadcrumbs
                    this.loading = false
                })
            }
        }
    }
</script>
