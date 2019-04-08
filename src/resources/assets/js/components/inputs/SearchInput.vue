<template>
    <div class="dropdown search-input" :class="{ 'search-input--opened': opened }" v-click-outside="close">
        <text-input ref="input"
                    :small="small"
                    :value="value"
                    @input="input"
                    @focus="focus"
                    @blur="$emit('blur', $event)"
                    @enter="$emit('enter', $event)"
                    class="search-input__input"/>

        <div class="dropdown__container" v-show="opened">
            <ul class="dropdown__values search-input__values" :class="{ 'dropdown--top': atTop }">
                <li v-for="(option, $i) in options" :key="$i" class="dropdown__option" @mousedown="select($i)">
                    <a>
                        <span v-if="option.image" class="dropdown__option__image"
                              :style="{ 'background-image': 'url(' + option.image + ')' }"></span>
                        <span class="dropdown__option__text">{{ option.title }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    export default {
        name: "search-input",

        props: {
            options: {
                default() {
                    return []
                },
            },
            value: {},
            small: {
                type: Boolean,
                default: false
            },
            immediate: {
                type: Boolean,
                default: false
            },
        },

        data() {
            return {
                opened: false,
                atTop: false
            }
        },

        methods: {
            input(event) {
                this.$emit('input', event)
                this.$emit('search', event)
            },

            focus() {
                if (this.immediate)
                    this.$emit('search', this.value)

                this.opened = true
            },

            select(index) {
                let selected = this.options[index]
                this.close()

                this.$emit('input', selected.value, selected.title)
            },

            toggle() {
                this.opened ? this.close() : this.open()
            },

            close() {
                if (!this.opened)
                    return

                this.opened = false
            },

            open() {
                let offset = window.innerHeight - this.$refs.input.$el.getBoundingClientRect().bottom

                this.atTop = (offset < (Math.min(this.options.length, 8) + 1) * 36 + 16) ? true : 0

                this.opened = true
            },
        },
    }
</script>