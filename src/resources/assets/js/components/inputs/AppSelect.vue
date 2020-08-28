<template>
    <div class="dropdown select"
         :class="{ 'dropdown--opened': opened, 'dropdown--top': atTop, 'dropdown--small': small }"
         v-click-outside="close">
        <button type="button" class="dropdown__value form__group__input" :class="{'form__group__input--h-small': small}"
                ref="value"
                @click="toggle"
                @keydown="moveFocus"
                @keydown.enter="selectFocused">

            <template v-if="selected">
                <div v-if="selected.image" class="dropdown__option__image"
                     :style="{ 'background-image': 'url(' + selected.image + ')' }"></div>
                <div v-if="selected.color" class="dropdown__option__color" :class="'bg-' + selected.color" :style="selected.color.charAt(0) == '#' ? 'background-color: ' + selected.color : ''"></div>
            </template>

            <span class="dropdown__value__text">{{ selected ? selected.title : this.emptyTitleText }}</span>
        </button>

        <transition name="dropdown">
            <div class="dropdown__container" ref="container" v-show="opened">
                <div class="dropdown__search" v-if="search">
                    <text-input ref="search"
                                :placeholder="__('Поиск')"
                                :tabindex="tabindex"
                                small
                                v-model="searchWord"
                                @input="$emit('search', searchWord)"
                                class="select__input"/>
                </div>

                <slot :options="filteredOptions">
                    <ul class="dropdown__values select__values" ref="container">
                        <li v-for="(option, $i) in filteredOptions" :key="$i"
                            class="dropdown__option"
                            :class="{'dropdown__option--focused': focused == $i + 1}"
                            @click="select(option.value)">
                            <div v-if="option.image" class="dropdown__option__image"
                                 :style="{ 'background-image': 'url(' + option.image + ')' }"></div>
                            <div v-if="option.color" class="dropdown__option__color"
                                 :class="'bg-' + option.color" :style="option.color.charAt(0) == '#' ? 'background-color: ' + option.color : ''"></div>
                            <div class="dropdown__option__text">{{ option.title }}</div>
                        </li>
                    </ul>
                </slot>
            </div>
        </transition>
    </div>
</template>

<script>
    export default {
        name: "app-select",

        props: {
            options: {
                default() {
                    return []
                },
            },
            value: {},
            tabindex: {},
            immediate: {
                type: Boolean,
                default: false
            },
            search: {
                type: Boolean,
                default: true,
            },
            simpleSearch: {
                type: Boolean,
                default: false,
            },
            small: {
                type: Boolean,
                default: false,
            },
            emptyTitle: {
                type: String,
                default: null,
            },
            withEmpty: {
                type: Boolean,
                default: false,
            },
            immediateSearchWord: {
                type: String,
                default: null,
            }
        },

        data() {
            return {
                searchWord: '',
                opened: false,
                atTop: false,
                focused: null,
            }
        },

        created() {
            if (this.immediate)
                this.$emit('search', this.immediateSearchWord ? this.immediateSearchWord : this.searchWord)
        },

        methods: {
            focus() {
                this.open()
            },

            select(value) {
                this.$emit('input', value)
                this.$emit('select', value)
                this.$emit('change', value)
                this.close()
            },

            selectByIndex(index) {
                if (this.options[index])
                    this.select(this.options[index].value)
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
                this.opened = true

                if (this.search) {
                    this.$nextTick(() => {
                        this.$refs.search.$refs.input.focus()
                    })
                }
            },

            moveFocus(event) {
                if (event.keyCode == 38) { // Up
                    this.focused = (this.focused <= 1) ? this.options.length : this.focused - 1
                    event.preventDefault()

                    if (this.focused == this.options.length) {
                        this.$refs.container.scrollTop = this.options.length * 34;
                    } else {
                        let topPos = this.focused * 34;
                        let dif = topPos - this.$refs.container.scrollTop
                        if (dif <= 0)
                            this.$refs.container.scrollTop += dif - 34;
                    }
                } else if (event.keyCode == 40) { // Down
                    this.focused = (this.focused >= this.options.length) ? 1 : this.focused + 1
                    event.preventDefault()

                    if (this.focused == 1) {
                        this.$refs.container.scrollTop = 0;
                    } else {
                        let topPos = this.focused * 34;
                        let dif = topPos - this.$refs.container.scrollTop - Math.min(this.options.length, 8) * 34
                        if (dif > 0)
                            this.$refs.container.scrollTop += dif;
                    }
                }
                if (!this.opened)
                    this.selectByIndex(this.focused - 1)
            },

            selectFocused(event) {
                this.selectByIndex(this.focused - 1)
                event.preventDefault()
            },
        },

        computed: {
            selected() {
                return this.options.find(item => item.value === this.value);
            },

            emptyTitleText() {
                return this.emptyTitle ? this.emptyTitle : '-- ' + this.__('Выберите значение')
            },

            filteredOptions() {
                if (this.search && this.simpleSearch && this.searchWord) {
                    return this.options.filter(option => option.title.toLowerCase().indexOf(this.searchWord.toLowerCase()) === 0)
                }

                if (this.withEmpty)
                    return [
                        {
                            value: null,
                            title: this.emptyTitleText
                        },
                        ...this.options
                    ]

                return this.options
            }
        },

        watch: {
            opened(opened) {
                if (!opened)
                    return

                this.$nextTick(() => {
                    let height = this.$refs.container.getBoundingClientRect().height
                    let offset = window.innerHeight - this.$refs.value.getBoundingClientRect().bottom - height

                    this.atTop = offset < 0
                })
            },

            options() {
                this.focused = false

                this.$nextTick(() => {
                    let height = this.$refs.container.getBoundingClientRect().height
                    let offset = window.innerHeight - this.$refs.value.getBoundingClientRect().bottom - height

                    this.atTop = offset < 0
                })
            }
        },
    }
</script>