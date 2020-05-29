<template>
    <div>
        <div @click="opened = !opened" class="flex items-center cursor-pointer">
            <h3 class="text-sm uppercase tracking-wide text-80 bg-30 p-3">{{ filter.name }}</h3>
            <i class="fas text-grey" :class="opened ? 'fa-angle-down' : 'fa-angle-up'"></i>
        </div>

        <div class="p-2 pt-0" v-show="opened">
            <field :field="field" @input="handleChange"/>
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
        field: {},
    }),

    created() {
        if (this.filter && this.filter.opened !== undefined)
            this.opened = this.filter.opened

        this.field = Object.assign({}, this.filter.customField, this.value ? {
            extraAttributes: {
                immediate: true,
                immediateSearchWord: this.value,
            }
        } : {})

        this.field.value = this.value
    },

    watch: {
        value() {
            this.field.value = this.value
        }
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
