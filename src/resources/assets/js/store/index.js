import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

import resource from './modules/resource.js'
import sidePopup from './modules/sidePopup.js'
import popup from './modules/popup.js'
import user from './modules/user.js'

export default new Vuex.Store({
    modules: {
        resource,
        sidePopup,
        popup,
        user,
    },
    strict: debug
})
