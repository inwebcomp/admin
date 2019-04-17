<template>
    <div class="relative overflow-auto" style="height: calc(100vh - 198px)">
        <table class="data-table bg-white" v-if="resources.length">
            <thead>
            <tr class="data-table__header">
                <table-checkbox :all="true"></table-checkbox>
                <table-heading v-for="(field, $i) of fields" :key="$i" :field="field">{{ field.indexName }}</table-heading>
            </tr>
            </thead>
            <tbody class="data-table__body">
            <tr class="data-table__line" v-for="(resource, $n) of resources" :key="$n">
                <table-checkbox></table-checkbox>

                <template v-for="(field, $i) of resource.fields">
                    <table-value :field="field" :resourceId="resource.id.value">
                        <component :is="'index-' + field.component" :field="field" :resourceId="resource.id.value"></component>
                    </table-value>
                </template>
            </tr>
            </tbody>
        </table>

        <div v-if="! resources.length && ! loading">
            <no-data-found></no-data-found>
        </div>

        <div v-if="loading">
            <data-table-loading></data-table-loading>
        </div>
    </div>
</template>

<script>
    import TableCheckbox from "~components/tables/TableCheckbox"
    import TableValue from "~components/tables/TableValue"
    import NoDataFound from "~components/tables/NoDataFound"
    import DataTableLoading from "./DataTableLoading"
    import TableHeading from "~js/components/tables/TableHeading"

    export default {
        components: {
            TableHeading,
            DataTableLoading,
            NoDataFound,
            TableValue,
            TableCheckbox
        },

        name: "data-table",

        props: {
            resources: {
                default() {
                    return {}
                }
            },
            loading: {
                type: Boolean,
                default: true
            }
        },

        computed: {
            fields() {
                return this.resources[0].fields
            }
        },

        methods: {
            link(...args) {
                args.unshift(this.controller)
                return '/' + args.join('/')
            },
        }
    }
</script>
