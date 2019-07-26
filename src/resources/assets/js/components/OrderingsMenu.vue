<template>
    <dropdown v-if="orderings && orderings.length > 0">
        <dropdown-trigger
            slot-scope="{ toggle }"
            :handle-click="toggle"
            :active="orderingsAreApplied"
        >
            <slot :currentOrdering="currentOrdering"/>
        </dropdown-trigger>

        <dropdown-menu class="text-black" slot="menu" width="290" direction="rtl"
                       slot-scope="{ toggle }">
            <div v-if="orderingsAreApplied" class="bg-30 border-b border-60">
                <button
                    @click="$emit('clear-selected-orderings') && toggle()"
                    class="py-2 w-full block text-xs uppercase tracking-wide text-center text-80 dim font-bold focus:outline-none"
                >
                    {{ __('Сбросить сортировку') }}
                </button>
            </div>

            <div v-for="ordering in orderings">
                <div class="tracking-wide p-1 cursor-pointer flex items-center" @click="handleChange(ordering) && toggle()">
                    <span class="mr-auto ml-2" :class="{'font-bold': currentOrdering.field == ordering.field}">
                        {{ ordering.title }}
                    </span>

                    <div class="direction hover:bg-grey-lighter" @click.stop="handleChange(ordering, 'asc') && toggle()" :title="__('По возрастанию')">
                        <i class="fas fa-long-arrow-alt-down" :class="{'text-grey': ! (currentOrdering.field == ordering.field && currentOrdering.direction == 'asc') }"></i>
                    </div>
                    <div class="direction hover:bg-grey-lighter" @click.stop="handleChange(ordering, 'desc') && toggle()" :title="__('По убыванию')">
                        <i class="fas fa-long-arrow-alt-up" :class="{'text-grey': ! (currentOrdering.field == ordering.field && currentOrdering.direction == 'desc') }"></i>
                    </div>
                </div>
            </div>
        </dropdown-menu>
    </dropdown>
</template>

<script>
export default {
    props: {
        resourceName: String,
    },

    data() {
        return {
            active: false
        }
    },

    computed: {
        /**
         * Return the orderings from state
         */
        orderings() {
            return this.$store.getters[`${this.resourceName}/orderings`]
        },

        /**
         * Return the current ordering from state
         */
        currentOrdering() {
            return this.$store.getters[`${this.resourceName}/currentOrdering`]
        },

        /**
         * Determine via state whether orderings are applied
         */
        orderingsAreApplied() {
            return this.$store.getters[`${this.resourceName}/orderingsAreApplied`]
        },
    },

    methods: {
        handleChange(ordering, direction) {
            this.$store.commit(`${this.resourceName}/setOrdering`, {
                field: ordering.field,
                direction: direction || ordering.direction,
            })

            this.$emit('ordering-changed')
        },
    }
}
</script>

<style lang="scss" scoped>
    .direction {
        width: 30px;
        height: 30px;
        line-height: 30px;
        border-radius: 2px;
        text-align: center;
    }
</style>
