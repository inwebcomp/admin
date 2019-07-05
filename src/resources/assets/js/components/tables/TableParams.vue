<template>
    <div class="active-panel active-panel--edit">
        <div @click="go" class="active-panel__button active-panel__button--back active-panel__button--icon"><i
                class="fas fa-chevron-left"></i></div>

        <h1 class="active-panel__caption mr-4">{{ title }}</h1>

        <router-link v-if="this.resourceName"
                     :to="{ name: 'action', params: { resourceName: this.resourceName, action: 'create' }}"
                     class="active-panel__button mr-auto">
            <i class="fas fa-plus mr-2 text-grey-light"></i>
            {{ __('Добавить') }}
        </router-link>

        <table-actions @action="$emit($event)"/>

        <div class="active-panel__button">
            <i class="fas fa-sort-amount-down mr-2 text-grey-light"></i>
            {{ __('По дате добавления') }}
        </div>

        <!-- Filters -->
        <filter-menu :resourceName="resourceName"
                     @clear-selected-filters="$emit('clear-selected-filters')"
                     @filter-changed="$emit('filter-changed')">
            <template v-slot="{ filtersAreApplied, activeFilterCount }">
                <div class="active-panel__button">
                    <i class="fas fa-filter mr-2 text-grey-light"></i>
                    {{ __('Фильтрация') }}

                    <span v-if="filtersAreApplied" class="active-panel__button__count">
                        {{ activeFilterCount }}
                    </span>
                </div>
            </template>
        </filter-menu>

        <div class="active-panel__button">
            <i class="fas fa-cog mr-2 text-grey-light"></i>
            {{ __('Cтолбцы') }}
        </div>
    </div>
</template>

<script>
    export default {
        name: "table-params",

        computed: {
            title() {
                if (this.$store.state.resource.info)
                    return this.$store.state.resource.info.label;
                else
                    return ''
            },

            resourceName() {
                return this.$store.state.resource.info.uriKey
            }
        },

        methods: {
            go() {
                if (this.navigate)
                    this.$router.push({name: 'home'})
                else
                    App.$emit('back')
            },
        },
    }
</script>
