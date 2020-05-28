<template>
    <div class="active-panel active-panel--sticky">
        <div @click="go" class="active-panel__button active-panel__button--back active-panel__button--icon"><i
                class="fas fa-chevron-left"></i></div>

        <h1 class="active-panel__caption mr-4">{{ title }}</h1>

        <router-link v-if="this.resourceName && this.create"
                     :to="$makeRoute.create(this.resourceName)"
                     class="active-panel__button mr-auto">
            <i class="fas fa-plus mr-2 text-grey-light"></i>
            {{ __('Добавить') }}
        </router-link>

        <table-actions :resourceName="resourceName" :remove="remove" class="ml-auto" @action="$emit($event)"/>

        <!-- Search -->
        <table-search class="mr-4" @search="$emit('search', $event)" :query="search" />

        <!-- Orderings -->
        <orderings-menu :resourceName="resourceName"
                     @clear-selected-orderings="$emit('clear-selected-orderings')"
                     @ordering-changed="$emit('ordering-changed')">
            <template v-slot="{ currentOrdering }">
                <div class="active-panel__button" v-if="currentOrdering">
                    <i class="fas mr-2 text-grey-light" :class="'fa-long-arrow-alt-' + (currentOrdering.direction == 'desc' ? 'up' : 'down')"></i>
                    {{ currentOrdering.title }}
                </div>
            </template>
        </orderings-menu>

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

        <!--<div class="active-panel__button">-->
            <!--<i class="fas fa-cog mr-2 text-grey-light"></i>-->
            <!--{{ __('Cтолбцы') }}-->
        <!--</div>-->
    </div>
</template>

<script>
    export default {
        name: "table-params",

        props: {
            search: {},
            title: {},
            resourceName: {},
            create: {
                type: Boolean,
                default: false,
            },
            remove: {
                type: Boolean,
                default: false,
            },
            navigate: {
                type: Boolean,
                default: true
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
