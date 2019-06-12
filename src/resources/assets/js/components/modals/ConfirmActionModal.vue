<template>
    <form
        @keydown="handleKeydown"
        @submit.prevent.stop="handleConfirm"
    >
        <div>
            <heading :level="2" class="text-center border-b border-40 pb-8 mb-8">{{ action.name }}</heading>

            <p v-if="action.fields.length == 0" class="px-8 my-8 text-center">
                {{ __('Are you sure you want to run this action?') }}
            </p>

            <div v-else>
                <!-- Validation Errors -->
                <validation-errors :errors="errors" />

                <!-- Action Fields -->
                <div class="action" v-for="field in action.fields" :key="field.attribute">
                    <component
                        :is="'form-' + field.component"
                        :errors="errors"
                        :resource-name="resourceName"
                        :field="field"
                        v-model="field.value"
                    />
                </div>
            </div>
        </div>

        <div class="flex justify-between">
            <app-button @click.native="handleClose">{{ __('Отмена') }}</app-button>
            <app-button ref="runButton" :disabled="working" submit type="add">{{ __('Выполнить') }}</app-button>
        </div>
    </form>
</template>

<script>
export default {
    props: {
        working: Boolean,
        resourceName: { type: String, required: true },
        action: { type: Object, required: true },
        selectedResources: { type: [Array, String], required: true },
        errors: { type: Object, required: true },
    },

    /**
     * Mount the component.
     */
    mounted() {
        // If the modal has inputs, let's highlight the first one, otherwise
        // let's highlight the submit button
        if (document.querySelectorAll('.popup input').length) {
            document.querySelectorAll('.popup input')[0].focus()
        } else {
            this.$refs.runButton.focus()
        }
    },

    methods: {
        /**
         * Stop propogation of input events unless it's for an escape or enter keypress
         */
        handleKeydown(e) {
            if (['Escape', 'Enter'].indexOf(e.key) !== -1) {
                return
            }

            e.stopPropagation()
        },

        /**
         * Execute the selected action.
         */
        handleConfirm() {
            App.$emit('executeAction', this.action)
            this.$closePopup()
        },

        /**
         * Close the modal.
         */
        handleClose() {
            this.$closePopup()
        },
    },
}
</script>
