<template>
    <div>
        <div @click="opened = !opened" class="flex items-center cursor-pointer">
            <h3 class="text-sm uppercase tracking-wide text-80 bg-30 p-3">{{ filter.name }}</h3>
            <i class="fas text-grey" :class="opened ? 'fa-angle-down' : 'fa-angle-up'"></i>
        </div>

        <div class="p-2 pt-0" v-show="opened">
            <app-select
                small
                :search="filter.search"
                :simpleSearch="true"
                :value="value"
                :options="filter.options"
                @change="handleChange" />
        </div>
    </div>
</template>

<script>
export default {
    props: {
        resourceName: {
            type: String,
            required: true,
        },
        filterKey: {
            type: String,
            required: true,
        },
    },

    data: () => ({
        opened: true,
    }),

    mounted() {
        if (this.filter && this.filter.opened !== undefined)
            this.opened = this.filter.opened
    },

    methods: {
        handleChange(value) {
            if (value === '')
                value = null

            this.$store.commit(`${this.resourceName}/updateFilterState`, {
                filterClass: this.filterKey,
                value: value,
            })

            this.$emit('change')
        },
    },

    computed: {
        filter() {
            return this.$store.getters[`${this.resourceName}/getFilter`](this.filterKey)
        },

        value() {
            return this.filter.currentValue
        },
    },
}
</script>
