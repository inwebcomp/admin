<template>
    <div class="active-panel active-panel--edit">
        <router-link v-if="! popupMode"
                     :to="backRoute"
                     class="active-panel__button active-panel__button--back active-panel__button--icon">
            <!--<i class="fas fa-chevron-left"></i>-->
            <i class="fas fa-times"></i>
        </router-link>

        <div v-if="popupMode"
             class="active-panel__button active-panel__button--back active-panel__button--icon"
             @click="closePopup"
             :to="backRoute">
            <i class="fas fa-times"></i>
        </div>

        <h1 class="active-panel__caption">{{ title }} <b v-if="accent">{{ accent }}</b></h1>
    </div>
</template>

<script>
    export default {
        name: "ActivePanel",

        props: {
            accent: {
                type: String,
                default: null
            },
            title: {
                type: String,
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
            }
        },

        methods: {
            closePopup() {
                this.$closePopup()
            }
        }
    }
</script>