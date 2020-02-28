<template>
    <div class="active-panel active-panel--edit">
        <div v-if="! popupMode"
             @click="go"
             class="active-panel__button active-panel__button--back active-panel__button--icon">
            <i class="fas fa-times"></i>
        </div>

        <div v-if="popupMode"
             class="active-panel__button active-panel__button--back active-panel__button--icon"
             @click="closePopup"
             :to="backRoute">
            <i class="fas fa-times"></i>
        </div>

        <h1 class="active-panel__caption">
            {{ title }}
            <b v-if="id">#{{ id }}</b>
            <template v-if="accent && (! id || accent != id)"> -
                <b v-if="!href">{{ accent }}</b>
                <b v-if="href">
                    <a target="_blank" class="text-white" :href="href">
                        {{ accent }}
                        <i class="fas fa-external-link-alt text-sm ml-1 align-middle"></i>
                    </a>
                </b>
            </template>
        </h1>

        <slot />
    </div>
</template>

<script>
    export default {
        name: "ActivePanel",

        props: {
            accent: {
                default: null
            },
            title: {
                default: null
            },
            id: {
                default: null
            },
            href: {
                default: null
            },
            backRoute: {
                default() {
                    return {name: 'home'}
                }
            },
            popupMode: {
                type: Boolean,
                default: false
            },
            navigate: {
                type: Boolean,
                default: true
            }
        },

        methods: {
            closePopup() {
                this.$closePopup()
            },

            go() {
                if (this.navigate)
                    this.$router.push(this.backRoute)
                else
                    App.$emit('back')
            }
        }
    }
</script>