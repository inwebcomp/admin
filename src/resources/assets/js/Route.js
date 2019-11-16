export default {
    install(Vue, options) {
        Vue.prototype.$makeRoute = {
            url(url) {
                return '/' + url
            },

            create(resource, bg = null) {
                return this.url('resource/' + (bg || resource) + '/#create/' + resource)
            },

            edit(resource, id, bg = null) {
                return this.url('resource/' + (bg || resource) + '/#edit/' + resource + '/' + id)
            },
        }
    }
}