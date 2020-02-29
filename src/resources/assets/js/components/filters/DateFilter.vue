<template>
    <div>
        <h3 class="text-sm uppercase tracking-wide text-80 bg-30 p-3">{{ filter.name }}</h3>

        <div class="p-2 pt-0">
            <date-time-picker
                class="w-full form-control form-input form-input-bordered"
                dusk="date-filter"
                name="date-filter"
                :value="value"
                dateFormat="Y-m-d"
                :placeholder="placeholder"
                :enable-time="false"
                :enable-seconds="false"
                :first-day-of-week="firstDayOfWeek"
                @input.prevent=""
                @change="handleChange"
                :range="filter.range"
            />
        </div>
    </div>
</template>

<script>
import DateTimePicker from '../DateTimePicker'

export default {
    components: { DateTimePicker },

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
        handleChange(value) {
            this.$store.commit(`${this.resourceName}/updateFilterState`, {
                filterClass: this.filterKey,
                value,
            })
            this.$emit('change')
        },
    },

    computed: {
        placeholder() {
            return this.filter.placeholder || this.__('Выберите дату')
        },

        value() {
            return this.filter.currentValue
        },

        valueSince() {
            return this.filter.currentValue.since
        },

        valueTill() {
            return this.filter.currentValue.till
        },

        filter() {
            return this.$store.getters[`${this.resourceName}/getFilter`](this.filterKey)
        },

        options() {
            return this.$store.getters[`${this.resourceName}/getOptionsForFilter`](this.filterKey)
        },

        firstDayOfWeek() {
            return this.filter.firstDayOfWeek || 0
        },
    },
}
</script>
