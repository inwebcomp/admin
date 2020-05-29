<template>
    <div>
        <div @click="opened = !opened" class="flex items-center cursor-pointer">
            <h3 class="text-sm uppercase tracking-wide text-80 bg-30 p-3">{{ filter.name }}</h3>
            <i class="fas text-grey" :class="opened ? 'fa-angle-down' : 'fa-angle-up'"></i>
        </div>

        <boolean-option v-show="opened"
                        :resource-name="resourceName"
                        :key="option.value"
                        v-for="option in options"
                        :filter="filter"
                        :option="option"
                        @change="handleChange"
        />
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

        methods: {
            handleChange() {
                this.$emit('change')
            },
        },

        mounted() {
            if (this.filter && this.filter.opened !== undefined)
                this.opened = this.filter.opened
        },

        computed: {
            filter() {
                return this.$store.getters[`${this.resourceName}/getFilter`](this.filterKey)
            },

            options() {
                return this.$store.getters[`${this.resourceName}/getOptionsForFilter`](this.filterKey)
            },
        },
    }
</script>
