<template>
    <dropdown v-if="filters && filters.length > 0">
        <dropdown-trigger
            slot-scope="{ toggle }"
            :handle-click="toggle"
            :active="filtersAreApplied"
        >
            <slot :activeFilterCount="activeFilterCount" :filtersAreApplied="filtersAreApplied"></slot>
        </dropdown-trigger>

        <dropdown-menu class="text-black" slot="menu" width="290" direction="rtl">
            <div v-if="filtersAreApplied" class="bg-30 border-b border-60">
                <button
                    @click="$emit('clear-selected-filters')"
                    class="py-2 w-full block text-xs uppercase tracking-wide text-center text-80 dim font-bold focus:outline-none"
                >
                    {{ __('Сбросить фильтра') }}
                </button>
            </div>

            <!-- Custom Filters -->
            <component
                v-for="filter in filters"
                :resourceName="resourceName"
                :key="filter.name"
                :filter-key="filter.class"
                :is="filter.component"
                @input="$emit('filter-changed')"
                @change="$emit('filter-changed')"
            />
        </dropdown-menu>
    </dropdown>
</template>

<script>
export default {
    props: {
        resourceName: String,
    },

    computed: {
        /**
         * Return the filters from state
         */
        filters() {
            return this.$store.getters[`${this.resourceName}/filters`]
        },

        /**
         * Determine via state whether filters are applied
         */
        filtersAreApplied() {
            return this.$store.getters[`${this.resourceName}/filtersAreApplied`]
        },

        /**
         * Return the number of active filters
         */
        activeFilterCount() {
            return this.$store.getters[`${this.resourceName}/activeFilterCount`]
        },
    },
}
</script>
