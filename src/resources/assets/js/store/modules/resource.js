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
        if (typeof id == 'object')
            state.selected = state.selected.filter(item => ! id.includes(item))
        else
            state.selected = state.selected.filter(item => item != id)
    },

    addSelected(state, id) {
        if (typeof id == 'object')
            Array.prototype.push.apply(state.selected, id)
        else
            state.selected.push(id)
    },
}

let actions = {
    clearSelected({commit}) {
        commit('setSelected', [])
    },
}

export default {
    namespaced: true,
    state,
    mutations,
    actions,
}