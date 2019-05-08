let state = {
    info: {},
    selected: [],
}

let mutations = {
    set(state, resource) {
        state.info = resource
    },

    setSelected(state, items) {
        state.selected = items
    },

    deleteSelected(state, id) {
        state.selected = state.selected.filter(item => item != id)
    },

    addSelected(state, id) {
        state.selected.push(id)
    },
}

let actions = {
    clearSelected({ commit }) {
        commit('setSelected', [])
    },
}

export default {
    namespaced: true,
    state,
    mutations,
    actions,
}