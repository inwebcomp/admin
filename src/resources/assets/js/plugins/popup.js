export default {
    install(Vue, options) {
        Vue.prototype.$showPopup = function (component, payload, options = {}) {
            this.$store.dispatch('popup/setComponent', {
                component,
                payload,
                options
            })

            this.$store.dispatch('popup/show')
        }

        Vue.prototype.$closePopup = function () {
            this.$store.dispatch('popup/hide')
        }
    }
}