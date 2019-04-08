<template>
    <td class="data-table__checkbox" @click.stop="state = ! state">
        <checkbox v-model="state"></checkbox>
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
            all: {
                default: false
            }
        },

        data() {
            return {
                state: false
            }
        },

        watch: {
            state(newValue) {
                if (this.all) {
                    this.$emit('changeAll', newValue)
                }
            }
        }
    }
</script>
