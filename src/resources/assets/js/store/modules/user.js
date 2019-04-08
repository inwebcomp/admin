let state = {
    info: {}
}

let mutations = {
    set(state, user) {
        state.info = user
    }
}

export default {
    namespaced: true,
    state,
    mutations
}