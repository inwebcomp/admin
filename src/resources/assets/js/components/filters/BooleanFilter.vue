<template>
    <div>
        <h3 class="text-sm uppercase tracking-wide text-80 bg-30 p-3">{{ filter.name }}</h3>

        <boolean-option
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

    methods: {
        handleChange() {
            this.$emit('change')
        },
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
