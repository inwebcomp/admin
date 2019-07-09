<template>
    <div>
        <label class="w-full cursor-pointer">
            <checkbox class="m-2"
                      :value="isChecked"
                      @input="updateCheckedState(option.value, $event)"/>
            {{ option.title }}
        </label>
    </div>
</template>

<script>
    export default {
        props: {
            resourceName: {
                type: String,
                required: true,
            },
            filter: Object,
            option: Object,
        },

        methods: {
            updateCheckedState(optionKey, checked) {
                let oldValue = this.filter.currentValue
                let newValue = {...oldValue, [optionKey]: checked}

                this.$store.commit(`${this.resourceName}/updateFilterState`, {
                    filterClass: this.filter.class,
                    value: newValue,
                })

                this.$emit('change')
            },
        },

        computed: {
            isChecked() {
                return (
                    this.$store.getters[`${this.resourceName}/filterOptionValue`](
                        this.filter.class,
                        this.option.value
                    ) == true
                )
            },
        },
    }
</script>
