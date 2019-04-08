<template>
    <div>
        <component :is="component" :controller="controller" :object="object"></component>
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
            controller: {
                default: 'home'
            },
            object: {
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
                    params: {controller: this.controller}
                })
            })
        },

        methods: {
            showPopup() {
                this.$showSidePopup(this.action, {
                    controller: this.controller,
                    object: this.object
                }, {
                    closeOnOverlayClick: false
                })
            }
        },
    }
</script>
