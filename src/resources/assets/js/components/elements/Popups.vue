<template>
    <div class="popup-wrapper">
        <div class="side-popup-mask" v-show="sidePopup.active" @click="closeSidePopup"></div>
        <div class="popup-mask" v-show="popup.active" @click="closePopup"></div>

        <side-popup>
            <component :is="sidePopup.component" v-bind="sidePopup.payload"></component>
        </side-popup>
        <popup>
            <component :is="popup.component" v-bind="popup.payload"></component>
        </popup>
    </div>
</template>

<script>
    export default {
        name: "Popups",

        computed: {
            sidePopup() {
                return this.$store.state.sidePopup
            },
            popup() {
                return this.$store.state.popup
            },
        },

        methods: {
            closeSidePopup() {
                App.$emit('sidePopupMaskClick');

                if (this.sidePopup.options.closeOnOverlayClick !== false)
                    this.$closeSidePopup()
            },
            closePopup() {
                App.$emit('popupMaskClick');

                if (this.popup.options.closeOnOverlayClick !== false)
                    this.$closePopup()
            }
        }
    }
</script>