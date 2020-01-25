<template>
    <div class="custom-actions flex flex-wrap">
        <div class="active-panel__button"
             :class="{'active-panel__button--disabled': action.disabled}"
             v-for="action in availableActions" :key="action.urikey"
             @click.prevent="! action.disabled && determineActionStrategy(action)">
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
            resourceName: {},
            resourceId: {},
        },

        data: () => ({
            actions: [],
            working: false,
            errors: new Errors(),
            selectedActionKey: '',
            confirmActionModalOpened: false,
        }),

        watch: {
            /**
             * Watch the actions property for changes.
             */
            actions() {
                this.selectedActionKey = ''
            },
        },

        created() {
            if (this.resourceName) {
                this.fetch()
            }

            App.$on('executeAction' + this.resourceId, (action) => {
                this.selectedActionKey = action.uriKey

                this.$nextTick(() => {
                    this.executeAction()
                })
            });

            if (this.resourceName) {
                App.$on('resourceUpdate', this.fetch);
            }

            this.$watch(
                () => {
                    return (
                        this.resourceKey
                    )
                },
                () => {
                    this.fetch()
                }
            )
        },

        destroyed() {
            App.$off('executeAction' + this.resourceKey)
        },

        methods: {
            /**
             * Get the actions available for the current resource.
             */
            fetch() {
                return App.api.request({
                    url: `${this.resourceName}/actions/${this.resourceId}`,
                }).then(({actions}) => {
                    this.actions = _.filter(actions, action => {
                        if (this.resourceId)
                            return ! action.onlyOnIndex
                        else
                            return ! action.onlyOnDetail
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
                    resourceId: this.resourceId,
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
                if (this.selectedResources.length == 0 && ! this.selectedAction.availableForEntireResource) {
                    alert(this.__('Please select a resource to perform this action on.'))
                    return
                }

                if (this.working)
                    return

                this.working = true

                if (! this.selectedAction.queueable) {
                    this.$toasted.show(this.__('Загрузка ...'), {
                        iconPack: 'fontawesome',
                        icon: 'fa-hourglass-half',
                        duration: 1000 * 1000,
                    })
                }

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
                    this.$toasted.clear()
                } else if (response.danger) {
                    App.$emit('actionExecuted')
                    this.$toasted.show(response.danger, {type: 'error'})
                } else if (response.download) {
                    App.$emit('actionExecuted')
                    let link = document.createElement('a')
                    link.href = response.download
                    link.download = response.name
                    document.body.appendChild(link)
                    link.click()
                    document.body.removeChild(link)
                    this.$toasted.show(this.__('Началось скачивание'), {type: 'success'})
                } else if (response.redirect) {
                    App.$emit('actionExecuted')
                    window.location = response.redirect
                    this.$toasted.clear()
                } else if (response.openInNewTab) {
                    App.$emit('actionExecuted')
                    window.open(response.openInNewTab, '_blank')
                    this.$toasted.clear()
                } else {
                    App.$emit('actionExecuted')
                    this.$toasted.show(this.__('Действие запущено'), {type: 'success'})
                }
            },
        },

        computed: {
            resourceKey() {
                return this.resourceName + this.resourceId
            },

            selectedResources() {
                if (this.resourceId)
                    return [this.resourceId * 1];

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