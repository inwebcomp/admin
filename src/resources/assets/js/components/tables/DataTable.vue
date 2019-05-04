<template>
    <div class="relative overflow-auto" style="height: calc(100vh - 198px)">
        <table class="data-table bg-white" v-if="resources.length">
            <thead>
            <tr class="data-table__header">
                <td v-if="sortable"></td>
                <table-checkbox :all="true"></table-checkbox>
                <table-heading v-for="(field, $i) of fields" :key="$i" :field="field">{{ field.indexName }}</table-heading>
            </tr>
            </thead>
            <draggable class="data-table__body"
                       :value="resources"
                       @input="$emit('input', $event)"
                       tag="tbody"
                       v-bind="dragOptions"
                       @start="drag = true"
                       @end="dragEnd">

                <tr class="data-table__line" v-for="(resource, $n) of resources" :key="$n">
                    <table-sort-handle v-if="sortable"/>

                    <table-checkbox/>

                    <template v-for="(field, $i) of resource.fields">
                        <table-value :field="field" :resourceId="resource.id.value">
                            <component :is="'index-' + field.component" :field="field" :resourceId="resource.id.value"></component>
                        </table-value>
                    </template>
                </tr>
            </draggable>
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
    import Draggable from "vuedraggable"

    export default {
        components: {
            TableHeading,
            DataTableLoading,
            NoDataFound,
            TableValue,
            TableCheckbox,
            Draggable
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
            },
            sortable: {
                type: Boolean,
                default: false
            }
        },

        data() {
            return {
                drag: false,
            }
        },

        computed: {
            fields() {
                return this.resources[0].fields
            },

            dragOptions() {
                return {
                    disabled: !this.sortable,
                    delay: 0,
                    touchStartThreshold: 0,
                    forceFallback: true,
                    animation: 150,
                    ghostClass: "ghost",
                    handle: ".handle",
                    dragClass: "sortable-drag",
                }
            }
        },

        methods: {
            link(...args) {
                args.unshift(this.controller)
                return '/' + args.join('/')
            },

            dragEnd() {
                this.drag = false
                this.$emit('sort')
            }
        }
    }
</script>
