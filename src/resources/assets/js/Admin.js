import Vue from "vue";
import axios from 'axios'
import VueAxios from 'vue-axios'
import VueRouter from 'vue-router'
import Translator from './services/Translator'
import VueFroala from 'vue-froala-wysiwyg'
import store from './store'
import router from './router'
import Api from "~js/api"
import './components'
import SidePopup from './plugins/sidePopup'
import Popup from './plugins/popup'
import Toasted from 'vue-toasted'

import ClickOutside from "~directives/ClickOutside";

Vue.directive('click-outside', ClickOutside);


Vue.use(VueAxios, axios)
Vue.use(VueRouter)
Vue.use(VueFroala)

Vue.use(SidePopup)
Vue.use(Popup)

Vue.use(Toasted, {
    position: 'bottom-right',
    duration: 4000,
})

export default class Admin {
    constructor(config) {
        this.bus = new Vue()
        this.bootingCallbacks = []
        this.config = config
        this.api = new Api(config)
    }

    /**
     * Register a callback to be called before Admin starts. This is used to bootstrap
     * addons, tools, custom fields, or anything else Admin needs
     */
    booting(callback) {
        this.bootingCallbacks.push(callback)
    }

    /**
     * Execute all of the booting callbacks.
     */
    boot() {
        this.bootingCallbacks.forEach(callback => callback(Vue, router, store))

        this.bootingCallbacks = []

        this.loadTranslator()
        this.loadMixins()
    }

    /**
     * Start the Admin app by calling each of the tool's callbacks and then creating
     * the underlying Vue instance.
     */
    start() {
        let self = this

        self.boot()

        self.app = new Vue({
            el: "#app",
            router: router,
            store,
            created() {
                this.$store.commit('user/set', self.config.user)
            },
            mounted() {
                self.$on('error', message => {
                    this.$toasted.show(message, { type: 'error' })
                })
            },
        });
    }

    /**
     * Loads thanslator and mix it with vue
     */
    loadTranslator() {
        let Lang = new Translator(this.config.translations);

        Vue.mixin({
            methods: {
                __: function(...args) {
                    return Lang.get(...args);
                }
            }
        });
    }

    /**
     * Loads thanslator and mix it with vue
     */
    loadMixins() {
        let self = this

        Vue.mixin({
            methods: {
                img: function(path) {
                    return path ? path : 'https://via.placeholder.com/42x42?text=Missing'
                },
            }
        });
    }

    /**
     * Register a listener on Admin's built-in event bus
     */
    $on(...args) {
        this.bus.$on(...args)
    }

    /**
     * Register a one-time listener on the event bus
     */
    $once(...args) {
        this.bus.$once(...args)
    }

    /**
     * Unregister an listener on the event bus
     */
    $off(...args) {
        this.bus.$off(...args)
    }

    /**
     * Emit an event on the event bus
     */
    $emit(...args) {
        this.bus.$emit(...args)
    }
}
