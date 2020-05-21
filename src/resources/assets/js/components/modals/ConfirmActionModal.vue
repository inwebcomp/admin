<template>
    <form
            @keydown="handleKeydown"
            @submit.prevent.stop="handleConfirm"
    >
        <div>
            <heading :level="2" class="text-center border-b border-40 pb-8 mb-8">{{ action.name }}</heading>

            <p v-if="action.fields.length == 0 && action.confirmText" class="px-8 my-8 text-center"
               v-html="action.confirmText"></p>

            <div v-else>
                <!-- Validation Errors -->
                <validation-errors :errors="errors"/>

                <!-- Action Fields -->
                <div class="action">
                    <inline-fields class="action mb-4">
                        <component v-for="field in action.fields" :key="field.attribute"
                                   :is="'form-' + field.component"
                                   :errors="errors"
                                   :resource-name="resourceName"
                                   :field="field"
                                   v-model="field.value"/>
                    </inline-fields>
                </div>
            </div>
        </div>

        <div class="flex justify-between">
            <app-button @click.native="handleClose">{{ action.cancelButtonText }}</app-button>
            <app-button ref="runButton" :disabled="working" submit type="add">{{ action.confirmButtonText }}
            </app-button>
        </div>
    </form>
</template>

<script>
    export default {
        props: {
            working: Boolean,
            resourceName: {type: String, required: true},
            resourceId: {},
            uid: {},
            action: {type: Object, required: true},
            selectedResources: {type: [Array, String], required: true},
            errors: {type: Object, required: true},
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

        computed: {
            resourceKey() {
                return this.resourceName + this.resourceId
            },
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
                App.$emit('executeAction-' + this.resourceId + '-' + this.uid, this.action)
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
