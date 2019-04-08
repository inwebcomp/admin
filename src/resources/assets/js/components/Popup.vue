<template>
    <transition :name="transition">
        <div class="popup-wrapper" v-show="show" @click="closePopup">
            <div class="popup-container" @click.stop role="dialog">
                <div class="popup-content" role="document">
                    <slot></slot>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    export default {
        name: 'popup',

        props: {
            transition: {
                default: 'popup'
            }
        },

        computed: {
            show() {
                return this.$store.state.popup.active
            },

            options() {
                return this.$store.state.popup.options
            }
        },

        watch: {
            show: {
                immediate: true,
                handler(visible) {
                    if (visible)
                        window.addEventListener('keyup', this.closeOnEsc, {once: true, capture: true})
                    else
                        window.removeEventListener('keyup', this.closeOnEsc)
                }
            }
        },

        created() {
            let self = this

            self.$on('close', function () {
                self.$store.dispatch('popup/hide')
            })
        },

        methods: {
            closePopup() {
                App.$emit('popupMaskClick')

                if (this.options.closeOnOverlayClick !== false)
                    this.$closePopup()
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

