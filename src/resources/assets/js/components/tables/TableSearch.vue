<template>
    <div class="active-panel__search" v-click-outside="close">
        <text-input v-show="visible"
                    small
                    erase-icon
                    :value="query"
                    class="active-panel__search__input"
                    ref="input"
                    :placeholder="__('Поиск')"
                    @esc="close"
                    @input="handleInput"
                    @enter="handleInput($event.target.value)"/>

        <div v-show="! visible && query" class="active-panel__search__query" @click="open">
            «{{ query }}»
        </div>

        <div class="active-panel__search__icon" @click="open">
            <i class="far fa-search text-grey-light text-2xl"></i>
        </div>
    </div>
</template>

<script>
    import TextInput from "~components/inputs/TextInput"

    export default {
        name: "table-search",
        components: {TextInput},

        props: {
            query: {},
        },

        data() {
            return {
                visible: false,
            }
        },

        methods: {
            open() {
                if (this.visible) {
                    this.search(this.query)
                }

                this.visible = true

                this.$nextTick(() => {
                    this.$refs.input.$refs.input.focus()
                })
            },

            close() {
                this.visible = false
            },

            search(query) {
                this.$emit('search', query)
            },

            handleInput(value) {
                this.debouncer(() => {
                    this.search(value)
                })
            },

            debouncer: _.debounce(callback => callback(), 150),
        },
    }
</script>