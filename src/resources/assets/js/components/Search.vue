<template>
    <div class="search" v-click-outside="close">
        <text-input ref="input" @focus="focus" v-model="query" small :placeholder="__('Поиск')" class="search__input" />

        <transition name="dropdown">
            <div class="dropdown__container" ref="container" v-show="opened">
                <ul class="dropdown__values search__values">
                    <div v-for="(group, $i) in groups" :key="$i" class="search__option__group">
                        <div class="dropdown__option__text search__option__group__title">{{ group.title }}</div>

                        <router-link v-for="(option, $i) in group.items" :key="$i" tag="li" :to="option.url" class="dropdown__option" @click="select(option.value)">
                            <span v-if="option.image" class="dropdown__option__image"
                                  :style="{ 'background-image': 'url(' + option.image + ')' }"></span>

                            <div class="dropdown__option__text search__option__text">
                                <div class="search__option__title">{{ option.title }}</div>
                                <div class="search__option__subtitle">{{ option.subTitle }}</div>
                                <div class="search__option__id">ID: {{ option.resourceId }}<i v-if="! option.visibility" class="fas fa-eye-slash  text-grey ml-2"></i></div>
                            </div>
                        </router-link>
                    </div>
                </ul>
            </div>
        </transition>
    </div>
</template>

<script>
    export default {
        name: "Search",

        data() {
            return {
                query: '',
                opened: false,
                groups: [],
                timer: null,
            }
        },

        computed: {
            resource() {
                return this.$store.state.resource.info
            },
        },

        watch: {
            query() {
                if (this.query == '') {
                    this.groups = []
                    return null
                }

                clearTimeout(this.timer)

                this.timer = setTimeout(() => {
                    App.api.request({
                        controller: this.resource.uriKey,
                        action: 'search',
                        params: {
                            search: this.query
                        }
                    }).then(data => {
                        this.groups = data
                    })
                }, 300)
            }
        },

        methods: {
            search() {
                if (this.query == '')
                    return false


            },

            focus() {
                this.opened = true
            },

            select(value) {
                // this.$emit('input', value)

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
            },
        },
    }
</script>