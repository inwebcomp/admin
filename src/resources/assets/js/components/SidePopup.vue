<template>
    <transition :name="transition">
        <div class="side-popup-wrapper" v-show="show">
            <div class="side-popup-container">
                <div class="side-popup-content">
                    <slot></slot>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    export default {
        name: 'side-popup',

        props: {
            transition: {
                default: 'side-popup'
            }
        },

        computed: {
            show() {
                return this.$store.state.sidePopup.active
            },

            options() {
                return this.$store.state.sidePopup.options
            }
        },

        watch: {
            show: {
                immediate: true,
                handler(visible) {
                    if (visible)
                        window.addEventListener('keyup', this.closeOnEsc, {once: true})
                    else
                        window.removeEventListener('keyup', this.closeOnEsc)
                }
            }
        },

        created() {
            let self = this

            self.$on('close', function () {
                self.$store.dispatch('sidePopup/hide')
            })
        },

        methods: {
            closePopup() {
                App.$emit('sidePopupMaskClick')

                if (this.options.closeOnOverlayClick !== false)
                    this.$closeSidePopup()
            },

            closeOnEsc(e) {
                e.preventDefault()
                e.stopPropagation()

                if (e.keyCode === 27)
                    this.closePopup()
            }
        },
    }
</script>

