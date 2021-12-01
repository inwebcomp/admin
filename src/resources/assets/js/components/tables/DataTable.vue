<template>
    <div class="relative overflow-auto">
        <table class="data-table bg-white" v-if="resources.length">
            <thead>
            <tr class="data-table__header">
                <td v-if="sortable"></td>
                <table-checkbox :all="true" :value="selectedCheckbox"
                                @change="selectAll(selectedCheckbox = $event)"></table-checkbox>
                <table-heading v-for="(field, $i) of fields" :key="$i" :field="field">{{ field.indexName }}
                </table-heading>
            </tr>
            </thead>

            <template v-for="(group, $g) in resourcesToDisplay">
                <tr class="data-table__line" v-if="group.groupInfo">
                    <table-checkbox class="bg-grey-lightest pt-4"
                                    :value="group.groupInfo.selected"
                                    @change="selectMany(groupIds(group), group.groupInfo.selected = $event)"/>

                    <table-group-info :info="group.groupInfo"
                                      :length="group.groupInfo.fields.length ? 1 : fields.length"/>

                    <template v-for="field in group.groupInfo.fields">
                        <table-value :field="field" class="bg-grey-lightest">
                            <component v-if="field.component" :is="'index-' + field.component"
                                       :field="field"></component>
                        </table-value>
                    </template>

                    <td v-if="group.groupInfo.fields.length"
                        :colspan="fields.length - 1 - groupInfoColspan(group.groupInfo)"
                        class="bg-grey-lightest"></td>
                </tr>

                <draggable class="data-table__body"
                           :value="group.resources"
                           @input="savePositions"
                           tag="tbody"
                           v-bind="dragOptions"
                           @start="drag = true"
                           @end="dragEnd">

                    <tr class="data-table__line" :class="resource.classes" v-for="(resource, $n) in group.resources"
                        :key="resource.id.value">
                        <table-sort-handle v-if="sortable"/>

                        <table-checkbox @change="select(resource.id.value)"
                                        :value="selected.includes(resource.id.value)"/>

                        <template v-for="(field, $i) of resource.fields">
                            <table-value :fastEdit="resource.authorizedToFastUpdate" :field="field"
                                         :resourceName="resourceName" :resourceId="resource.id.value">
                                <component :is="'index-' + field.component" :field="field" :resourceName="resourceName"
                                           :resourceId="resource.id.value"></component>
                            </table-value>
                        </template>
                    </tr>
                </draggable>
            </template>
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
    import Draggable from "vuedraggable"

    export default {
        components: {
            Draggable,
        },

        name: "data-table",

        props: {
            resources: {
                default() {
                    return {}
                },
            },
            resourceName: {},
            loading: {
                type: Boolean,
                default: true,
            },
            sortable: {
                type: Boolean,
                default: false,
            },
            grouped: {
                type: Boolean,
                default: false,
            },
        },

        watch: {
            resourceName() {
                this.selectedCheckbox = false
                this.selectAll(false)
            },
        },

        data() {
            return {
                drag: false,
                selectedCheckbox: false,
            }
        },

        computed: {
            selected() {
                return this.$store.state.resource.selected
            },

            fields() {
                return this.resourcesToDisplay[0].resources[0].fields
            },

            resourcesToDisplay() {
                if (this.grouped)
                    return this.resources

                return [
                    {resources: this.resources},
                ]
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
            },
        },

        methods: {
            savePositions(event) {
                this.$emit('input', event)
            },

            select(id) {
                if (this.selected.includes(id))
                    this.$store.commit('resource/deleteSelected', id)
                else
                    this.$store.commit('resource/addSelected', id)
            },

            selectAll(value) {
                if (!value)
                    this.$store.commit('resource/setSelected', [])
                else
                    this.$store.commit('resource/setSelected', this.allVisibleIds())
            },

            groupIds(group) {
                return group.resources.map(i => i.id.value)
            },

            allVisibleIds() {
                let ids = []

                this.resourcesToDisplay.forEach(group => {
                    Array.prototype.push.apply(ids, this.groupIds(group))
                })

                return ids
            },

            selectMany(ids, value) {
                if (!value)
                    this.$store.commit('resource/deleteSelected', ids)
                else
                    this.$store.commit('resource/addSelected', ids)
            },

            link(...args) {
                args.unshift(this.controller)
                return '/' + args.join('/')
            },

            dragEnd() {
                this.drag = false
                this.$emit('sort')
            },

            groupInfoColspan(group) {
                if (!group.fields || !group.fields.length)
                    return 0

                return group.fields.reduce(function (previousValue, currentValue) {
                    return previousValue + (currentValue.colspan || 1)
                }, 0)
            },
        },
    }
</script>
