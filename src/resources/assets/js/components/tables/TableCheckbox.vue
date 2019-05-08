<template>
    <td class="data-table__checkbox" @click.stop="change">
        <checkbox :value="value" @input="change"></checkbox>
    </td>
</template>

<script>
    import Checkbox from "~components/Checkbox"

    export default {
        components: {
            Checkbox
        },

        name: "table-checkbox",

        props: {
            value: {
                default: false
            },

            all: {
                default: false
            }
        },

        methods: {
            change() {
                this.$emit('change', ! this.value)
            }
        }

        // watch: {
        //     state(newValue) {
        //         this.$emit('change', newValue)
        //     }
        // }
    }
</script>
