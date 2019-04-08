let state = {
    info: {}
}

let mutations = {
    set(state, resource) {
        state.info = resource
    }
}

export default {
    namespaced: true,
    state,
    mutations
}