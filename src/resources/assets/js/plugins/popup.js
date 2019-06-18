export default {
    install(Vue, options) {
        Vue.prototype.$showPopup = function (component, payload, options = {}) {
            let finalOptions = {
                closeOnOverlayClick: true,
                closeButton: true,
            }

            Object.assign(finalOptions, options)

            this.$store.dispatch('popup/setComponent', {
                component,
                payload,
                options: finalOptions
            })

            this.$store.dispatch('popup/show')
        }

        Vue.prototype.$closePopup = function () {
            this.$store.dispatch('popup/hide')
        }
    }
}