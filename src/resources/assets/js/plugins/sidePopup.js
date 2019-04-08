export default {
    install (Vue, options) {
        Vue.prototype.$showSidePopup = function (component, payload, options = {}) {
            this.$store.dispatch('sidePopup/setComponent', {
                component,
                payload,
                options
            })

            this.$store.dispatch('sidePopup/show')
        }

        Vue.prototype.$closeSidePopup = function () {
            this.$store.dispatch('sidePopup/hide')
        }
    }
}