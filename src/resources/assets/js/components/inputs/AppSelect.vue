<template>
    <div class="dropdown select" :class="{ 'dropdown--opened': opened, 'dropdown--top': atTop, 'dropdown--small': small }" v-click-outside="close">
        <div class="dropdown__value form__group__input" :class="{'form__group__input--h-small': small}" ref="value" @click="toggle">
            <template v-if="selected">
                <div v-if="selected.image" class="dropdown__option__image"
                     :style="{ 'background-image': 'url(' + selected.image + ')' }"></div>
                <div v-if="selected.color" class="dropdown__option__color" :class="'bg-' + selected.color"></div>
            </template>

            {{ selected ? selected.title : '-- ' + __('Выберите значение') }}
        </div>

        <transition name="dropdown">
            <div class="dropdown__container" ref="container" v-show="opened">
                <div class="dropdown__search" v-if="search">
                    <text-input ref="search"
                                :placeholder="__('Поиск')"
                                small
                                v-model="searchWord"
                                @input="$emit('search', searchWord)"
                                class="select__input"/>
                </div>

                <ul class="dropdown__values select__values">
                    <li v-for="(option, $i) in options" :key="$i" class="dropdown__option" @click="select(option.value)">
                        <div v-if="option.image" class="dropdown__option__image"
                              :style="{ 'background-image': 'url(' + option.image + ')' }"></div>
                        <div v-if="option.color" class="dropdown__option__color" :class="'bg-' + option.color"></div>
                        <div class="dropdown__option__text">{{ option.title }}</div>
                    </li>
                </ul>
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
            immediate: {
                type: Boolean,
                default: false
            },
            search: {
                type: Boolean,
                default: true,
            },
            small: {
                type: Boolean,
                default: false,
            },
        },

        data() {
            return {
                searchWord: '',
                opened: false,
                atTop: false,
            }
        },

        created() {
            if (this.immediate)
                this.$emit('search', this.searchWord)
        },

        methods: {
            focus() {
                this.opened = true
            },

            select(value) {
                this.$emit('input', value)
                this.$emit('select', value)
                this.$emit('change', value)
                this.close()
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
        },

        computed: {
            selected() {
                return this.options.find(item => item.value === this.value);
            }
        },

        watch: {
            opened(opened) {
                if (! opened)
                    return

                this.$nextTick(() => {
                    let height = this.$refs.container.getBoundingClientRect().height
                    let offset = window.innerHeight - this.$refs.value.getBoundingClientRect().bottom - height

                    this.atTop = offset < 0
                })
            },
            options() {
                this.$nextTick(() => {
                    let height = this.$refs.container.getBoundingClientRect().height
                    let offset = window.innerHeight - this.$refs.value.getBoundingClientRect().bottom - height

                    this.atTop = offset < 0
                })
            }
        },
    }
</script>