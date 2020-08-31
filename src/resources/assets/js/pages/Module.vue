<template>
    <component :is="component" :resourceName="resourceName" :resourceId="resourceId"></component>
</template>

<script>
    import NotFound from '~pages/NotFound'

    export default {
        name: "module",

        components: {
            NotFound,
        },

        props: {
            resourceName: {
                default: 'home',
            },
            resourceId: {
                default: null,
            },
            action: {
                default: 'index',
            },
        },

        computed: {
            component() {
                return 'index'
            },
        },

        watch: {
            '$route.hash': {
                immediate: true,
                handler(newValue, oldValue) {
                    const params = newValue.substr(1).split('/')
                    const action = params[0]

                    if (action) {
                        this.showPopup(...params)
                    } else {
                        if (oldValue != undefined)
                            this.$closeSidePopup()
                    }
                },
            },
        },

        created() {
            App.$on('sidePopupMaskClick', () => {
                this.$router.push(this.$route.fullPath.substr(0, this.$route.fullPath.indexOf('#')))
            })
        },

        methods: {
            showPopup(action, resourceName = null, resourceId = null) {
                let data = {
                    closeOnOverlayClick: false,
                }

                resourceName = resourceName || this.resourceName
                resourceId = resourceId || this.resourceId

                let sidePopupConfig = App.config.sidePopup
                if (sidePopupConfig && sidePopupConfig[resourceName])
                    Object.assign(data, sidePopupConfig[resourceName])

                this.$showSidePopup(action, {
                    resourceName: resourceName,
                    resourceId: resourceId,
                }, data)
            },
        },
    }
</script>
