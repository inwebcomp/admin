let state = {
    active: null,
    component: null,
    payload: null,
    options: {}
}

let mutations = {
    show(state) {
        state.active = true
    },

    hide(state) {
        state.active = false
    },

    reset(state) {
        state.component = null
        state.payload = null
        state.options = {}
    },

    setComponent(state, { component, payload, options }) {
        state.component = component
        state.payload = payload
        state.options = options
    }
}

let actions = {
    show({ commit }) {
        commit('show')
    },

    hide({ commit }) {
        commit('hide')
        setTimeout(() => {
            commit('reset')
        }, 100)
    },

    setComponent({ commit }, params) {
        commit('setComponent', params)
    },
}

export default {
    namespaced: true,
    state,
    mutations,
    actions
}