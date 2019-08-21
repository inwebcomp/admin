<template>
    <div class="dropdown search-input" :class="{ 'search-input--opened': opened }" v-click-outside="close">
        <text-input ref="input"
                    :small="small"
                    :value="value"
                    :tabindex="tabindex"
                    @input="input"
                    @focus="focus"
                    @blur="blur"
                    @enter="$emit('enter', $event)"
                    @keydown.native="moveFocus"
                    class="search-input__input"/>

        <div class="dropdown__container" v-show="opened">
            <ul class="dropdown__values search-input__values" :class="{ 'dropdown--top': atTop }">
                <li v-for="(option, $i) in options" :key="$i" class="dropdown__option" :class="{'dropdown__option--focused': focused == $i + 1}" @mousedown="select($i)">
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
            tabindex: {},
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
                atTop: false,
                focused: null,
            }
        },

        watch: {
            options() {
                this.focused = false
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

            blur(event) {
                this.close()
                this.$emit('blur', event)
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

            moveFocus(event) {
                if (event.keyCode == 38) { // Up
                    this.focused = (this.focused <= 1) ? this.options.length : this.focused - 1
                } else if (event.keyCode == 40) { // Down
                    this.focused = (this.focused >= this.options.length) ? 1 : this.focused + 1
                } else if (event.keyCode == 13) { // Enter
                    this.select(this.focused - 1)
                }
            }
        },
    }
</script>