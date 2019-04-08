import Vue from 'vue'
import VueRouter from 'vue-router'
import routes from './routes'

Vue.use(VueRouter)

const router = createRouter({ base: window.config.baseUrl })
/**
 * The router factory
 */
function createRouter({ base }) {
    return new VueRouter({
        mode: 'history',
        base: base,
        routes
    })
}

export default router
