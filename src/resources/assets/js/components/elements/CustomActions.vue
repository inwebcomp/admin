<template>
    <div class="custom-actions flex">
        <div class="active-panel__button" v-for="action in availableActions" :key="action.urikey"
             @click.prevent="determineActionStrategy(action)">
            <i v-if="action.icon" class="mr-2 text-grey-light" :class="action.icon"></i>
            {{ action.name }}
        </div>
    </div>
</template>

<script>
    import _ from 'lodash'
    import {Errors} from 'form-backend-validation'

    export default {
        name: 'CustomActions',

        props: {
            endpoint: {
                type: String,
                default: null,
            },
        },

        data: () => ({
            actions: [],
            working: false,
            errors: new Errors(),
            selectedActionKey: '',
            confirmActionModalOpened: false,
        }),

        watch: {
            resourceName() {
               this.fetch()
            },

            /**
             * Watch the actions property for changes.
             */
            actions() {
                this.selectedActionKey = ''
            },
        },

        created() {
            App.$on('executeAction', (action) => {
                this.selectedActionKey = action.uriKey

                this.$nextTick(() => {
                    this.executeAction()
                })
            });
        },

        methods: {
            /**
             * Get the actions available for the current resource.
             */
            fetch() {
                return App.api.request({
                    url: `${this.resourceName}/actions`,
                }).then(({actions}) => {
                    this.actions = _.filter(actions, action => {
                        return !action.onlyOnDetail
                    })
                })
            },

            /**
             * Determine whether the action should redirect or open a confirmation modal
             */
            determineActionStrategy(action) {
                this.selectedActionKey = action.uriKey

                if (action.withoutConfirmation) {
                    this.executeAction()
                } else {
                    this.openConfirmationModal()
                }
            },

            /**
             * Confirm with the user that they actually want to run the selected action.
             */
            openConfirmationModal() {
                this.$showPopup(this.selectedAction.component, {
                    working: this.working,
                    selectedResources: this.selectedResources,
                    resourceName: this.resourceName,
                    action: this.selectedAction,
                    errors: this.errors,
                })
            },

            /**
             * Close the action confirmation modal.
             */
            closeConfirmationModal() {
                this.$closePopup()
            },

            /**
             * Initialize all of the action fields to empty strings.
             */
            initializeActionFields() {
                _(this.allActions).each(action => {
                    _(action.fields).each(field => {
                        field.fill = () => ''
                    })
                })
            },

            /**
             * Execute the selected action.
             */
            executeAction() {
                if (this.selectedResources.length == 0) {
                    alert(this.__('Please select a resource to perform this action on.'))
                    return
                }

                if (this.working)
                    return

                this.working = true

                App.api.request({
                    method: 'post',
                    url: this.endpoint || `${this.resourceName}/action`,
                    params: this.actionRequestQueryString,
                    data: this.actionFormData(),
                })
                    .then(response => {
                        this.confirmActionModalOpened = false
                        this.handleActionResponse(response)
                        this.working = false

                        this.$store.dispatch('resource/clearSelected')
                    })
                    .catch(({status, data}) => {
                        this.working = false

                        if (status == 422) {
                            this.errors = new Errors(data.errors)
                        }
                    })
            },

            /**
             * Gather the action FormData for the given action.
             */
            actionFormData() {
                return _.tap(new FormData(), formData => {
                    formData.append('resources', this.selectedResources)

                    _.each(this.selectedAction.fields, field => {
                        field.fill(formData)
                    })
                })
            },

            /**
             * Handle the action response. Typically either a message, download or a redirect.
             */
            handleActionResponse(response) {
                if (response.message) {
                    App.$emit('actionExecuted')
                    this.$toasted.show(response.message, {type: 'success'})
                } else if (response.deleted) {
                    App.$emit('actionExecuted')
                } else if (response.danger) {
                    App.$emit('actionExecuted')
                    this.$toasted.show(response.danger, {type: 'error'})
                } else if (response.download) {
                    let link = document.createElement('a')
                    link.href = response.download
                    link.download = response.name
                    document.body.appendChild(link)
                    link.click()
                    document.body.removeChild(link)
                } else if (response.redirect) {
                    window.location = response.redirect
                } else if (response.openInNewTab) {
                    window.open(response.openInNewTab, '_blank')
                } else {
                    App.$emit('actionExecuted')
                    this.$toasted.show(this.__('The action ran successfully!'), {type: 'success'})
                }
            },
        },

        computed: {
            resourceName() {
                return this.$store.state.resource.info.uriKey
            },

            selectedResources() {
                return this.$store.state.resource.selected
            },

            selectedAction() {
                if (this.selectedActionKey) {
                    return _.find(this.allActions, a => a.uriKey == this.selectedActionKey)
                }
            },

            /**
             * Get the query string for an action request.
             */
            actionRequestQueryString() {
                return {
                    action: this.selectedActionKey,
                    // search: this.queryString.currentSearch,
                    // filters: this.queryString.encodedFilters,
                    // viaResource: this.queryString.viaResource,
                    // viaResourceId: this.queryString.viaResourceId,
                    // viaRelationship: this.queryString.viaRelationship,
                }
            },

            /**
             * Get all of the available actions.
             */
            allActions() {
                return this.actions
            },

            /**
             * Get all of the available non-pivot actions for the resource.
             */
            availableActions() {
                return _(this.actions)
                    .filter(action => {
                        if (this.selectedResources.length) {
                            return true
                        }

                        return action.availableForEntireResource
                    })
                    .value()
            },
        },
    }
</script>