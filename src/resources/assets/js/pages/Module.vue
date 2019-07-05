<template>
    <div>
        <component :is="component" :resourceName="resourceName" :resourceId="resourceId"></component>
    </div>
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
                default: 'home'
            },
            resourceId: {
                default: null
            },
            action: {
                default: 'index'
            }
        },

        computed: {
            component() {
                return 'index'
            }
        },

        watch: {
            '$route.path': {
                immediate: true,
                handler(newValue, oldValue) {
                    if (this.action == 'edit' || this.action == 'create' || this.action == 'view') {
                        this.showPopup()
                    } else {
                        if (oldValue != undefined)
                            this.$closeSidePopup()
                    }
                }
            },
        },

        created() {
            App.$on('sidePopupMaskClick', () => {
                this.$router.push({
                    name: 'index',
                    params: {resourceName: this.resourceName}
                })
            })
        },

        methods: {
            showPopup() {
                this.$showSidePopup(this.action, {
                    resourceName: this.resourceName,
                    resourceId: this.resourceId
                }, {
                    closeOnOverlayClick: false
                })
            }
        },
    }
</script>
