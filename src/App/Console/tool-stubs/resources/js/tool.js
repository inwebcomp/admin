import Tool from './components/Tool'

App.booting((Vue, router, store) => {
    router.addRoutes([
        {
            name: '{{ component }}',
            path: '/{{ component }}',
            components: {
                default: Tool,
                header: Vue.component('app-header'),
                sidebar: Vue.component('app-sidebar')
            },
            props: {
                default: true
            }
        },
    ])
})
